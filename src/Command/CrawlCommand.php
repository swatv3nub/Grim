<?php

namespace Grim\Command;

use Grim\Utils\Logger;
use Grim\Utils\HttpClient;
use Grim\Utils\DataExporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CrawlCommand extends Command
{
    protected static $defaultName = 'crawl';
    protected static $defaultDescription = 'Crawl a website for common files and directories';

    private Logger $logger;
    private HttpClient $httpClient;
    private DataExporter $exporter;

    public function __construct()
    {
        parent::__construct();
        $this->logger = Logger::getInstance();
        $this->httpClient = new HttpClient();
        $this->exporter = new DataExporter();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('target', null, 'Target URL to crawl')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output format (json, csv, html)', 'json')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Output filename')
            ->addOption('verbose', 'v', InputOption::VALUE_NONE, 'Verbose output')
            ->addOption('timeout', 't', InputOption::VALUE_REQUIRED, 'Request timeout in seconds', '30')
            ->addOption('user-agent', 'u', InputOption::VALUE_REQUIRED, 'Custom User-Agent string')
            ->addOption('threads', null, InputOption::VALUE_REQUIRED, 'Number of concurrent threads', '10')
            ->addOption('delay', 'd', InputOption::VALUE_REQUIRED, 'Delay between requests in milliseconds', '100')
            ->addOption('max-requests', 'm', InputOption::VALUE_REQUIRED, 'Maximum requests to make', '1000')
            ->addOption('skip-ssl-verify', null, InputOption::VALUE_NONE, 'Skip SSL certificate verification')
            ->setHelp('This command crawls a website to discover common files, directories, admin panels, and backup files.');
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

        $io->title('GRIM Web Crawler');
        $io->text("Target: <info>{$target}</info>");
        $io->text("Threads: <info>" . $input->getOption('threads') . "</info>");
        $io->text("Max Requests: <info>" . $input->getOption('max-requests') . "</info>");
        $io->text("Starting web crawl...\n");

        try {
            $this->logger->info("Web crawl started", [
                'target' => $target,
                'threads' => $input->getOption('threads'),
                'max_requests' => $input->getOption('max-requests')
            ]);

            // Set HTTP client options
            if ($input->getOption('timeout')) {
                $this->httpClient->setTimeout((int) $input->getOption('timeout'));
            }
            
            if ($input->getOption('user-agent')) {
                $this->httpClient->setUserAgent($input->getOption('user-agent'));
            }

            if ($input->getOption('skip-ssl-verify')) {
                $this->httpClient->setSkipSslVerify(true);
            }

            // Run crawl
            $io->text("Running web crawler...");
            $results = $this->runCrawler($target, $input);

            // Display results
            $this->displayResults($io, $results);

            // Export results
            if ($input->getOption('output') || $input->getOption('file')) {
                $format = $input->getOption('output');
                $filename = $input->getOption('file') ?: 'grim_crawl_' . date('Y-m-d_H-i-s');
                
                $exportPath = $this->exporter->export($results, $format, $filename);
                $io->success("Results exported to: {$exportPath}");
            }

            $this->logger->info("Web crawl completed", [
                'target' => $target,
                'findings' => $this->countFindings($results)
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error("Crawl failed: " . $e->getMessage());
            $this->logger->error("Web crawl failed", [
                'target' => $target,
                'error' => $e->getMessage()
            ]);
            return Command::FAILURE;
        }
    }

    private function runCrawler(string $target, InputInterface $input): array
    {
        $results = [
            'admin_panels' => [],
            'backup_files' => [],
            'common_files' => [],
            'directories' => [],
            'robots_txt' => null,
            'sitemap' => null,
            'crawl_stats' => [
                'total_requests' => 0,
                'successful_requests' => 0,
                'failed_requests' => 0,
                'start_time' => date('Y-m-d H:i:s'),
                'end_time' => null
            ]
        ];

        $crawlFiles = [
            'admin_panels' => 'crawl/admin.ini',
            'backup_files' => 'crawl/backup.ini',
            'common_files' => 'crawl/others.ini'
        ];

        $maxRequests = (int) $input->getOption('max-requests');
        $delay = (int) $input->getOption('delay');
        $requestCount = 0;

        // Check robots.txt and sitemap
        $io->text("Checking robots.txt and sitemap...");
        
        $robotsUrl = rtrim($target, '/') . '/robots.txt';
        $robotsResponse = $this->httpClient->get($robotsUrl);
        if ($robotsResponse) {
            $results['robots_txt'] = [
                'url' => $robotsUrl,
                'content' => $robotsResponse,
                'size' => strlen($robotsResponse)
            ];
            $requestCount++;
        }

        $sitemapUrl = rtrim($target, '/') . '/sitemap.xml';
        $sitemapResponse = $this->httpClient->get($sitemapUrl);
        if ($sitemapResponse) {
            $results['sitemap'] = [
                'url' => $sitemapUrl,
                'content' => $sitemapResponse,
                'size' => strlen($sitemapResponse)
            ];
            $requestCount++;
        }

        // Crawl common files and directories
        foreach ($crawlFiles as $type => $file) {
            if (file_exists($file)) {
                $io->text("Crawling {$type}...");
                $content = file_get_contents($file);
                $paths = explode(',', $content);
                
                foreach ($paths as $path) {
                    if ($requestCount >= $maxRequests) {
                        $io->warning("Maximum requests limit reached ({$maxRequests})");
                        break 2;
                    }

                    $path = trim($path);
                    if (!empty($path)) {
                        $testUrl = rtrim($target, '/') . '/' . ltrim($path, '/');
                        $response = $this->httpClient->get($testUrl);
                        $requestCount++;
                        
                        if ($response) {
                            $results[$type][] = [
                                'path' => $path,
                                'url' => $testUrl,
                                'status' => 'found',
                                'response_length' => strlen($response),
                                'content_type' => $this->httpClient->getLastContentType()
                            ];
                            $results['crawl_stats']['successful_requests']++;
                        } else {
                            $results['crawl_stats']['failed_requests']++;
                        }

                        // Add delay between requests
                        if ($delay > 0) {
                            usleep($delay * 1000);
                        }
                    }
                }
            }
        }

        // Add some common directory checks
        $commonDirs = ['admin', 'administrator', 'wp-admin', 'phpmyadmin', 'cpanel', 'webmail', 'mail', 'ftp', 'ssh'];
        $io->text("Checking common directories...");
        
        foreach ($commonDirs as $dir) {
            if ($requestCount >= $maxRequests) break;
            
            $testUrl = rtrim($target, '/') . '/' . $dir;
            $response = $this->httpClient->get($testUrl);
            $requestCount++;
            
            if ($response) {
                $results['directories'][] = [
                    'name' => $dir,
                    'url' => $testUrl,
                    'status' => 'found',
                    'response_length' => strlen($response),
                    'content_type' => $this->httpClient->getLastContentType()
                ];
                $results['crawl_stats']['successful_requests']++;
            } else {
                $results['crawl_stats']['failed_requests']++;
            }

            if ($delay > 0) {
                usleep($delay * 1000);
            }
        }

        $results['crawl_stats']['total_requests'] = $requestCount;
        $results['crawl_stats']['end_time'] = date('Y-m-d H:i:s');

        return $results;
    }

    private function displayResults(SymfonyStyle $io, array $results): void
    {
        $io->section('Crawl Results');

        // Display statistics
        $stats = $results['crawl_stats'];
        $io->text("Crawl Statistics:");
        $io->text("  Total Requests: {$stats['total_requests']}");
        $io->text("  Successful: {$stats['successful_requests']}");
        $io->text("  Failed: {$stats['failed_requests']}");
        $io->text("  Duration: " . $this->calculateDuration($stats['start_time'], $stats['end_time']));
        $io->newLine();

        // Display findings by category
        $totalFindings = 0;

        foreach (['admin_panels', 'backup_files', 'common_files', 'directories'] as $category) {
            if (!empty($results[$category])) {
                $count = count($results[$category]);
                $totalFindings += $count;
                $io->text("<comment>{$category} ({$count}):</comment>");
                
                foreach ($results[$category] as $item) {
                    $io->text("  ✓ {$item['url']} ({$item['response_length']} bytes)");
                }
                $io->newLine();
            }
        }

        // Display robots.txt and sitemap
        if ($results['robots_txt']) {
            $io->text("<comment>robots.txt:</comment>");
            $io->text("  ✓ {$results['robots_txt']['url']} ({$results['robots_txt']['size']} bytes)");
            $io->newLine();
            $totalFindings++;
        }

        if ($results['sitemap']) {
            $io->text("<comment>sitemap.xml:</comment>");
            $io->text("  ✓ {$results['sitemap']['url']} ({$results['sitemap']['size']} bytes)");
            $io->newLine();
            $totalFindings++;
        }

        if ($totalFindings > 0) {
            $io->success("Crawl completed. Total findings: {$totalFindings}");
        } else {
            $io->text("No files or directories found during crawl.");
        }
    }

    private function countFindings(array $results): int
    {
        $count = 0;
        foreach (['admin_panels', 'backup_files', 'common_files', 'directories'] as $category) {
            $count += count($results[$category]);
        }
        
        if ($results['robots_txt']) $count++;
        if ($results['sitemap']) $count++;
        
        return $count;
    }

    private function calculateDuration(string $start, string $end): string
    {
        $startTime = strtotime($start);
        $endTime = strtotime($end);
        $duration = $endTime - $startTime;
        
        if ($duration < 60) {
            return $duration . ' seconds';
        } elseif ($duration < 3600) {
            return round($duration / 60, 2) . ' minutes';
        } else {
            return round($duration / 3600, 2) . ' hours';
        }
    }
}
