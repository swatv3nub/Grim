<?php

namespace Grim\Command;

use Grim\Scanner\VulnerabilityScanner;
use Grim\Utils\Logger;
use Grim\Utils\DataExporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class VulnCommand extends Command
{
    protected static $defaultName = 'vuln';
    protected static $defaultDescription = 'Perform vulnerability scanning on a target';

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
            ->addArgument('target', null, 'Target URL to scan for vulnerabilities')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output format (json, csv, html)', 'json')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Output filename')
            ->addOption('verbose', 'v', InputOption::VALUE_NONE, 'Verbose output')
            ->addOption('timeout', 't', InputOption::VALUE_REQUIRED, 'Request timeout in seconds', '30')
            ->addOption('user-agent', 'u', InputOption::VALUE_REQUIRED, 'Custom User-Agent string')
            ->addOption('level', 'l', InputOption::VALUE_REQUIRED, 'Scan level (low, medium, high, critical)', 'medium')
            ->addOption('skip-ssl-verify', null, InputOption::VALUE_NONE, 'Skip SSL certificate verification')
            ->addOption('max-requests', 'm', InputOption::VALUE_REQUIRED, 'Maximum requests per second', '10')
            ->setHelp('This command performs comprehensive vulnerability scanning on a target including SQL injection, XSS, CSRF, and more.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $target = $input->getArgument('target');

        if (!$target) {
            $io->error('Target URL is required');
            return Command::FAILURE;
        }

        // Validate target format
        if (!filter_var($target, FILTER_VALIDATE_URL)) {
            $io->error('Invalid target format. Please provide a valid URL');
            return Command::FAILURE;
        }

        $io->title('GRIM Vulnerability Scanner');
        $io->text("Target: <info>{$target}</info>");
        $io->text("Scan Level: <info>" . $input->getOption('level') . "</info>");
        $io->text("Starting vulnerability scan...\n");

        try {
            $this->logger->info("Vulnerability scan started", [
                'target' => $target,
                'level' => $input->getOption('level')
            ]);

            // Initialize scanner
            $scanner = new VulnerabilityScanner($target, $input->getOption('verbose'));
            
            // Set options
            if ($input->getOption('timeout')) {
                $scanner->setTimeout((int) $input->getOption('timeout'));
            }
            
            if ($input->getOption('user-agent')) {
                $scanner->setUserAgent($input->getOption('user-agent'));
            }

            if ($input->getOption('skip-ssl-verify')) {
                $scanner->setSkipSslVerify(true);
            }

            if ($input->getOption('max-requests')) {
                $scanner->setMaxRequestsPerSecond((int) $input->getOption('max-requests'));
            }

            $scanner->setScanLevel($input->getOption('level'));

            // Run scan
            $io->text("Running vulnerability scanner...");
            $results = $scanner->scan();

            // Display results
            $this->displayResults($io, $results);

            // Export results
            if ($input->getOption('output') || $input->getOption('file')) {
                $format = $input->getOption('output');
                $filename = $input->getOption('file') ?: 'grim_vuln_' . date('Y-m-d_H-i-s');
                
                $exportPath = $this->exporter->export($results, $format, $filename);
                $io->success("Results exported to: {$exportPath}");
            }

            $this->logger->info("Vulnerability scan completed", [
                'target' => $target,
                'vulnerabilities' => $this->countVulnerabilities($results)
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error("Scan failed: " . $e->getMessage());
            $this->logger->error("Vulnerability scan failed", [
                'target' => $target,
                'error' => $e->getMessage()
            ]);
            return Command::FAILURE;
        }
    }

    private function displayResults(SymfonyStyle $io, array $results): void
    {
        $io->section('Vulnerability Scan Results');

        if (empty($results)) {
            $io->text("No vulnerabilities found on target.");
            return;
        }

        $vulnerabilities = $this->countVulnerabilities($results);
        
        // Display summary
        $io->text("Vulnerabilities found:");
        $io->text("  Critical: <fg=red>{$vulnerabilities['critical']}</>");
        $io->text("  High: <fg=red>{$vulnerabilities['high']}</>");
        $io->text("  Medium: <fg=yellow>{$vulnerabilities['medium']}</>");
        $io->text("  Low: <fg=blue>{$vulnerabilities['low']}</>");
        $io->text("  Info: <fg=cyan>{$vulnerabilities['info']}</>");
        $io->newLine();

        // Display detailed results
        foreach ($results as $category => $data) {
            if (empty($data)) continue;

            $io->text("<comment>{$category}:</comment>");
            
            if (is_array($data)) {
                foreach ($data as $vuln) {
                    if (isset($vuln['severity']) && isset($vuln['description'])) {
                        $severityColor = $this->getSeverityColor($vuln['severity']);
                        $io->text("  [<{$severityColor}>{$vuln['severity']}</>] {$vuln['description']}");
                        
                        if (isset($vuln['url'])) {
                            $io->text("    URL: {$vuln['url']}");
                        }
                        
                        if (isset($vuln['payload'])) {
                            $io->text("    Payload: <fg=gray>{$vuln['payload']}</>");
                        }
                        
                        if (isset($vuln['evidence'])) {
                            $io->text("    Evidence: <fg=gray>{$vuln['evidence']}</>");
                        }
                    } else {
                        $io->text("  " . json_encode($vuln));
                    }
                    $io->newLine();
                }
            } else {
                $io->text("  {$data}");
            }
        }

        $totalVulns = array_sum($vulnerabilities);
        if ($totalVulns > 0) {
            $io->warning("Total vulnerabilities found: {$totalVulns}");
        } else {
            $io->success("No vulnerabilities detected on target.");
        }
    }

    private function countVulnerabilities(array $results): array
    {
        $counts = [
            'critical' => 0,
            'high' => 0,
            'medium' => 0,
            'low' => 0,
            'info' => 0
        ];

        foreach ($results as $category => $data) {
            if (is_array($data)) {
                foreach ($data as $vuln) {
                    if (isset($vuln['severity'])) {
                        $severity = strtolower($vuln['severity']);
                        if (isset($counts[$severity])) {
                            $counts[$severity]++;
                        }
                    }
                }
            }
        }

        return $counts;
    }

    private function getSeverityColor(string $severity): string
    {
        return match (strtolower($severity)) {
            'critical' => 'fg=red',
            'high' => 'fg=red',
            'medium' => 'fg=yellow',
            'low' => 'fg=blue',
            'info' => 'fg=cyan',
            default => 'fg=white'
        };
    }
}
