<?php

namespace Grim\Command;

use Grim\Scanner\InformationGatheringScanner;
use Grim\Utils\Logger;
use Grim\Utils\DataExporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InfoCommand extends Command
{
    protected static $defaultName = 'info';
    protected static $defaultDescription = 'Perform information gathering on a target';

    private Logger $logger;
    private DataExporter $exporter;

    public function __construct()
    {
        parent::__construct();
        $this->logger = Logger::getInstance();
        $this->exporter = new DataExporter();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('target', null, 'Target URL or domain to scan')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output format (json, csv, html)', 'json')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Output filename')
            ->addOption('timeout', 't', InputOption::VALUE_REQUIRED, 'Request timeout in seconds', '30')
            ->addOption('user-agent', 'u', InputOption::VALUE_REQUIRED, 'Custom User-Agent string')
            ->addOption('verbose', 'v', InputOption::VALUE_NONE, 'Enable verbose output')
            ->setHelp('This command performs comprehensive information gathering on a target including DNS, WHOIS, subdomain enumeration, and more.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $target = $input->getArgument('target');

        if (!$target) {
            $io->error('Target URL or domain is required');
            return Command::FAILURE;
        }

        // Validate target format
        if (!filter_var($target, FILTER_VALIDATE_URL) && !preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $target)) {
            $io->error('Invalid target format. Please provide a valid URL or domain');
            return Command::FAILURE;
        }

        $io->title('GRIM Information Gathering Scanner');
        $io->text("Target: <info>{$target}</info>");
        $io->text("Starting information gathering scan...\n");

        try {
            $this->logger->info("Information gathering started", ['target' => $target]);

            // Initialize scanner
            $scanner = new InformationGatheringScanner($target, $input->getOption('verbose'));

            // Set options
            if ($input->getOption('timeout')) {
                $scanner->setTimeout((int) $input->getOption('timeout'));
            }

            if ($input->getOption('user-agent')) {
                $scanner->setUserAgent($input->getOption('user-agent'));
            }

            // Run scan
            $io->text("Running information gathering scanner...");
            $results = $scanner->scan();

            // Display results
            $this->displayResults($io, $results);

            // Export results
            if ($input->getOption('output') || $input->getOption('file')) {
                $format = $input->getOption('output');
                $filename = $input->getOption('file') ?: 'grim_info_' . date('Y-m-d_H-i-s');

                $exportPath = $this->exporter->export($results, $format, $filename);
                $io->success("Results exported to: {$exportPath}");
            }

            $this->logger->info("Information gathering completed", [
                'target' => $target,
                'findings' => count($results)
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error("Scan failed: " . $e->getMessage());
            $this->logger->error("Information gathering failed", [
                'target' => $target,
                'error' => $e->getMessage()
            ]);
            return Command::FAILURE;
        }
    }

    private function displayResults(SymfonyStyle $io, array $results): void
    {
        $io->section('Scan Results');

        if (empty($results)) {
            $io->text("No information gathered from target.");
            return;
        }

        foreach ($results as $category => $data) {
            if (empty($data)) {
                continue;
            }

            $io->text("<comment>{$category}:</comment>");

            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        $io->text("  {$key}: " . json_encode($value));
                    } else {
                        $io->text("  {$key}: {$value}");
                    }
                }
            } else {
                $io->text("  {$data}");
            }
            $io->newLine();
        }

        // Summary
        $totalFindings = 0;
        foreach ($results as $category => $data) {
            if (is_array($data)) {
                $totalFindings += count($data);
            } else {
                $totalFindings++;
            }
        }

        $io->success("Information gathering completed. Total findings: {$totalFindings}");
    }
}
