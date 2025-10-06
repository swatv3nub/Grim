<?php

namespace Grim\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Grim\GrimApplication;
use Grim\Utils\Logger;

class ScanCommand extends Command
{
    protected static $defaultName = 'scan';
    protected static $defaultDescription = 'Run a full security scan on a target';

    protected function configure(): void
    {
        $this
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, 'Target URL to scan')
            ->addOption('no-vuln', null, InputOption::VALUE_NONE, 'Disable vulnerability scanning')
            ->addOption('no-info', null, InputOption::VALUE_NONE, 'Disable information gathering')
            ->addOption('no-crawl', null, InputOption::VALUE_NONE, 'Disable crawling')
            ->addOption('export', 'e', InputOption::VALUE_OPTIONAL, 'Export format (json, csv, html)', 'json')
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Output filename (without extension)')
            ->addOption('timeout', null, InputOption::VALUE_OPTIONAL, 'Request timeout in seconds', 30)
            ->addOption('delay', null, InputOption::VALUE_OPTIONAL, 'Delay between requests in seconds', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $logger = Logger::getInstance();

        // Get target URL
        $target = $input->getOption('target');
        if (!$target) {
            $target = $io->ask('Enter target URL (without http/https)', 'example.com');
        }

        // Validate target
        if (!$this->validateTarget($target)) {
            $io->error('Invalid target URL. Please provide a valid domain or URL.');
            return Command::FAILURE;
        }

        // Normalize target URL
        $target = $this->normalizeTarget($target);

        $io->title('GRIM Security Scanner - Full Scan');
        $io->text("Target: <info>{$target}</info>");
        $io->text("Starting scan at: " . date('Y-m-d H:i:s'));

        // Configure scan options
        $enableVulnScan = !$input->getOption('no-vuln');
        $enableInfoGathering = !$input->getOption('no-info');
        $enableCrawling = !$input->getOption('no-crawl');
        $verbose = $input->getOption('verbose');

        if ($verbose) {
            $logger->setLogLevel('DEBUG');
        }

        // Display scan configuration
        $io->section('Scan Configuration');
        $io->table(
            ['Component', 'Status'],
            [
                ['Vulnerability Scanning', $enableVulnScan ? '✓ Enabled' : '✗ Disabled'],
                ['Information Gathering', $enableInfoGathering ? '✓ Enabled' : '✗ Disabled'],
                ['Crawling', $enableCrawling ? '✓ Enabled' : '✗ Disabled'],
                ['Verbose Mode', $verbose ? '✓ Enabled' : '✗ Disabled']
            ]
        );

        // Confirm scan
        if (!$io->confirm('Proceed with the scan?', true)) {
            $io->text('Scan cancelled.');
            return Command::SUCCESS;
        }

        try {
            // Initialize application
            $app = new GrimApplication();

            // Run the scan
            $io->section('Running Scan');
            $progressBar = $io->createProgressBar();
            $progressBar->start();

            $results = $app->runFullScan(
                $target,
                $enableVulnScan,
                $enableInfoGathering,
                $enableCrawling
            );

            $progressBar->finish();
            $io->newLine(2);

            // Display results summary
            $this->displayResultsSummary($io, $results);

            // Export results if requested
            $exportFormat = $input->getOption('export');
            $outputFile = $input->getOption('output');

            if ($exportFormat) {
                $io->section('Exporting Results');
                try {
                    $filepath = $app->exportResults($exportFormat, $outputFile);
                    $io->success("Results exported to: {$filepath}");
                } catch (\Exception $e) {
                    $io->error("Failed to export results: " . $e->getMessage());
                }
            }

            $io->success('Scan completed successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $logger->error('Scan failed', [
                'target' => $target,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $io->error('Scan failed: ' . $e->getMessage());
            if ($verbose) {
                $io->text('Stack trace:');
                $io->text($e->getTraceAsString());
            }
            return Command::FAILURE;
        }
    }

    private function validateTarget(string $target): bool
    {
        $target = trim($target);

        // Remove protocol if present
        $target = preg_replace('/^https?:\/\//', '', $target);

        // Basic domain validation
        if (empty($target) || strpos($target, ' ') !== false) {
            return false;
        }

        // Check if it's a valid domain or IP
        if (filter_var($target, FILTER_VALIDATE_DOMAIN) || filter_var($target, FILTER_VALIDATE_IP)) {
            return true;
        }

        return false;
    }

    private function normalizeTarget(string $target): string
    {
        $target = trim($target);

        // Remove protocol if present
        $target = preg_replace('/^https?:\/\//', '', $target);

        // Add http protocol
        return 'http://' . $target;
    }

    private function displayResultsSummary(SymfonyStyle $io, array $results): void
    {
        $io->section('Scan Results Summary');

        // Basic info
        $io->table(
            ['Property', 'Value'],
            [
                ['Target', $results['target']],
                ['Start Time', $results['scan_start']],
                ['End Time', $results['scan_end']],
                ['Duration', $results['duration']]
            ]
        );

        // Scanner results
        if (isset($results['scanners'])) {
            foreach ($results['scanners'] as $scanner => $scannerResults) {
                $io->text("\n<info>" . ucfirst(str_replace('_', ' ', $scanner)) . ":</info>");

                if (is_array($scannerResults)) {
                    if (isset($scannerResults['basic'])) {
                        $io->text("  • Basic information gathered");
                    }

                    if (isset($scannerResults['technology'])) {
                        $tech = $scannerResults['technology'];
                        if ($tech['web_server']) {
                            $io->text("  • Web Server: " . $tech['web_server']);
                        }
                        if ($tech['cms']) {
                            $io->text("  • CMS: " . $tech['cms']);
                        }
                    }

                    // Count vulnerabilities if present
                    if (isset($scannerResults[0]['severity'])) {
                        $vulnCount = count(array_filter($scannerResults, fn($r) => $r['severity'] === 'vulnerability'));
                        $warningCount = count(array_filter($scannerResults, fn($r) => $r['severity'] === 'warning'));
                        $infoCount = count(array_filter($scannerResults, fn($r) => $r['severity'] === 'info'));

                        $io->text("  • Vulnerabilities found: <error>{$vulnCount}</error>");
                        $io->text("  • Warnings: <comment>{$warningCount}</comment>");
                        $io->text("  • Information items: <info>{$infoCount}</info>");
                    }
                }
            }
        }

        // Error handling
        if (isset($results['error'])) {
            $io->error('Scan completed with errors: ' . $results['error']);
        }
    }
}
