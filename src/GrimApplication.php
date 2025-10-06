<?php

namespace Grim;

use Grim\Scanner\VulnerabilityScanner;
use Grim\Scanner\InformationGatheringScanner;
use Grim\Utils\Logger;
use Grim\Utils\HttpClient;
use Grim\Config\ConfigManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GrimApplication extends Application
{
    private ConfigManager $config;
    private Logger $logger;
    private HttpClient $httpClient;
    private array $scanResults = [];

    public function __construct()
    {
        parent::__construct('GRIM Security Scanner', '5.0.0');
        $this->config = ConfigManager::getInstance();
        $this->logger = Logger::getInstance();
        $this->httpClient = new HttpClient();

        $this->addCommands([
            new Command\ScanCommand(),
            new Command\InfoCommand(),
            new Command\VulnCommand(),
            new Command\CrawlCommand(),
            new Command\ConfigCommand(),
            new Command\UpdateCommand(),
            new Command\ContainerScanCommand(),
            new Command\CloudScanCommand(),
            new Command\SelfUpdateCommand(),
            new Command\ResultHistoryCommand(),
            new Command\ExportSIEMCommand()
        ]);

        // Load plugins (example)
        if (class_exists('Grim\\Utils\\PluginLoader')) {
            $plugins = \Grim\Utils\PluginLoader::loadPlugins();
            foreach ($plugins as $plugin) {
                // Optionally register plugin commands or hooks here
                $this->logger->info('Loaded plugin: ' . $plugin->getName());
            }
        }

        // Set default language for i18n
        if (class_exists('Grim\\Utils\\Translator')) {
            \Grim\Utils\Translator::setLang('en'); // Change to 'es' for Spanish, etc.
        }
    }

    public function run(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        $this->logger->info("GRIM Security Scanner started", ['version' => '5.0.0']);

        // Check system requirements
        if (!$this->checkSystemRequirements()) {
            $this->logger->error("System requirements not met");
            return 1;
        }

        // Display banner
        $this->displayBanner();

        return parent::run($input, $output);
    }

    private function checkSystemRequirements(): bool
    {
        $requirements = [
            'PHP Version' => ['required' => '8.0.0', 'current' => PHP_VERSION],
            'cURL Extension' => ['required' => true, 'current' => extension_loaded('curl')],
            'DOM Extension' => ['required' => true, 'current' => extension_loaded('dom')],
            'JSON Extension' => ['required' => true, 'current' => extension_loaded('json')],
            'MBString Extension' => ['required' => true, 'current' => extension_loaded('mbstring')]
        ];

        $allMet = true;

        foreach ($requirements as $requirement => $check) {
            if ($check['required'] === true) {
                if (!$check['current']) {
                    $this->logger->error("Missing required extension: {$requirement}");
                    $allMet = false;
                }
            } elseif (is_string($check['required'])) {
                if (version_compare($check['current'], $check['required'], '<')) {
                    $this->logger->error("PHP version requirement not met: {$requirement} - Required: {$check['required']}, Current: {$check['current']}");
                    $allMet = false;
                }
            }
        }

        return $allMet;
    }

    private function displayBanner(): void
    {
        $banner = "
\033[38;5;208m
                 _,.-------.,_
             ,;~'             '~;,
           ,;                     ;,
          ;                         ;
         ,'                         ',
        ,;                           ;,
        ; ;                         ; ;           ██████╗ ██████╗ ██╗███╗   ███╗
        | ;   ______       ______   ; |          ██╔════╝ ██╔══██╗██║████╗ ████║
        |  `/         .           \'  |          ██║  ███╗██████╔╝██║██╔████╔██║
        |  ~  ,-~~~^~, | ,~^~~~-,  ~  |          ██║   ██║██╔══██╗██║██║╚██╔╝██║
        |   |        }:{        |   |            ╚██████╔╝██║  ██║██║██║ ╚═╝ ██║
        |   l       / | \       !   |             ╚═════╝ ╚═╝  ╚═╝╚═╝╚═╝     ╚═╝
        .~  (__,.--       --.,__)  ~.                        |
        |     ---;' / | \ `;---     |                        |
         \__.       \/^\/       .__/                         |
          V| \                 / |V                          v
           | |T~\___!___!___/~T| |           
           | |`IIII_I_I_I_IIII'| |           Advanced Security Scanner v5.0.0
           |  \,III I I I III,/  |           
            \   `~~~~~~~~~~'    /            
              \   .       .   /              
                \.    ^    ./
                  ^~~~^~~~^  
\033[0m
        ";

        echo $banner;
    }

    public function runFullScan(string $targetUrl, bool $enableVulnScan = true, bool $enableInfoGathering = true, bool $enableCrawling = true): array
    {
        $this->logger->info("Starting full scan", ['target' => $targetUrl]);

        $results = [
            'target' => $targetUrl,
            'scan_start' => date('Y-m-d H:i:s'),
            'scanners' => []
        ];

        try {
            // Information Gathering
            if ($enableInfoGathering) {
                $this->logger->info("Running information gathering scanner");
                $infoScanner = new InformationGatheringScanner($targetUrl, true);
                $infoResults = $infoScanner->scan();
                $results['scanners']['information_gathering'] = $infoResults;
            }

            // Vulnerability Scanning
            if ($enableVulnScan) {
                $this->logger->info("Running vulnerability scanner");
                $vulnScanner = new VulnerabilityScanner($targetUrl, true);
                $vulnResults = $vulnScanner->scan();
                $results['scanners']['vulnerability_scan'] = $vulnResults;
            }

            // Crawling (if implemented)
            if ($enableCrawling) {
                $this->logger->info("Running crawler");
                $crawlResults = $this->runCrawler($targetUrl);
                $results['scanners']['crawling'] = $crawlResults;
            }
        } catch (\Exception $e) {
            $this->logger->error("Error during scan", [
                'target' => $targetUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $results['error'] = $e->getMessage();
        }

        $results['scan_end'] = date('Y-m-d H:i:s');
        $results['duration'] = $this->calculateScanDuration($results['scan_start'], $results['scan_end']);

        $this->scanResults = $results;
        $this->logger->info("Full scan completed", [
            'target' => $targetUrl,
            'duration' => $results['duration']
        ]);

        // Log results to SQLite history
        if (class_exists('Grim\\Utils\\ResultLogger')) {
            try {
                $logger = new \Grim\Utils\ResultLogger();
                $logger->logResult($targetUrl, $results);
            } catch (\Exception $e) {
                $this->logger->warning('Failed to log scan result: ' . $e->getMessage());
            }
        }

        return $results;
    }

    private function runCrawler(string $targetUrl): array
    {
        $crawlResults = [
            'admin_panels' => [],
            'backup_files' => [],
            'common_files' => []
        ];

        $crawlFiles = [
            'admin_panels' => 'crawl/admin.ini',
            'backup_files' => 'crawl/backup.ini',
            'common_files' => 'crawl/others.ini'
        ];

        foreach ($crawlFiles as $type => $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $paths = explode(',', $content);

                foreach ($paths as $path) {
                    $path = trim($path);
                    if (!empty($path)) {
                        $testUrl = $targetUrl . '/' . $path;
                        $response = $this->httpClient->get($testUrl);

                        if ($response) {
                            $crawlResults[$type][] = [
                                'path' => $path,
                                'url' => $testUrl,
                                'status' => 'found',
                                'response_length' => strlen($response)
                            ];
                        }
                    }
                }
            }
        }

        return $crawlResults;
    }

    private function calculateScanDuration(string $start, string $end): string
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

    public function exportResults(string $format = 'json', ?string $filename = null): string
    {
        if (empty($this->scanResults)) {
            throw new \RuntimeException("No scan results to export");
        }

        $filename = $filename ?: 'grim_scan_' . date('Y-m-d_H-i-s');
        $resultsDir = $this->config->get('scanner.results_dir', 'results/');

        if (!is_dir($resultsDir)) {
            mkdir($resultsDir, 0755, true);
        }

        switch (strtolower($format)) {
            case 'json':
                $content = json_encode($this->scanResults, JSON_PRETTY_PRINT);
                $extension = 'json';
                break;

            case 'csv':
                $content = $this->convertToCsv($this->scanResults);
                $extension = 'csv';
                break;

            case 'html':
                $content = $this->convertToHtml($this->scanResults);
                $extension = 'html';
                break;

            default:
                throw new \InvalidArgumentException("Unsupported export format: {$format}");
        }

        $filepath = $resultsDir . $filename . '.' . $extension;
        file_put_contents($filepath, $content);

        $this->logger->info("Results exported", [
            'format' => $format,
            'filepath' => $filepath
        ]);

        return $filepath;
    }

    private function convertToCsv(array $data): string
    {
        $csv = "Key,Value\n";

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $csv .= $key . "," . json_encode($value) . "\n";
            } else {
                $csv .= $key . "," . $value . "\n";
            }
        }

        return $csv;
    }

    private function convertToHtml(array $data): string
    {
        $html = "<!DOCTYPE html>\n<html>\n<head>\n";
        $html .= "<title>GRIM Scan Results</title>\n";
        $html .= "<style>\n";
        $html .= "body { font-family: Arial, sans-serif; margin: 20px; }\n";
        $html .= ".vulnerability { color: red; }\n";
        $html .= ".warning { color: orange; }\n";
        $html .= ".info { color: blue; }\n";
        $html .= ".section { margin: 20px 0; padding: 10px; border: 1px solid #ccc; }\n";
        $html .= "</style>\n</head>\n<body>\n";
        $html .= "<h1>GRIM Security Scanner Results</h1>\n";
        $html .= "<div class='section'>\n";
        $html .= "<h2>Scan Summary</h2>\n";
        $html .= "<p><strong>Target:</strong> " . htmlspecialchars($data['target']) . "</p>\n";
        $html .= "<p><strong>Start Time:</strong> " . htmlspecialchars($data['scan_start']) . "</p>\n";
        $html .= "<p><strong>End Time:</strong> " . htmlspecialchars($data['scan_end']) . "</p>\n";
        $html .= "<p><strong>Duration:</strong> " . htmlspecialchars($data['duration']) . "</p>\n";
        $html .= "</div>\n";

        foreach ($data['scanners'] as $scanner => $results) {
            $html .= "<div class='section'>\n";
            $html .= "<h2>" . ucfirst(str_replace('_', ' ', $scanner)) . "</h2>\n";
            $html .= "<pre>" . htmlspecialchars(json_encode($results, JSON_PRETTY_PRINT)) . "</pre>\n";
            $html .= "</div>\n";
        }

        $html .= "</body>\n</html>";

        return $html;
    }

    public function getScanResults(): array
    {
        return $this->scanResults;
    }

    public function clearResults(): void
    {
        $this->scanResults = [];
        $this->logger->info("Scan results cleared");
    }
}
