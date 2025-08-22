<?php

namespace Grim\Config;

use Symfony\Component\Yaml\Yaml;
use Dotenv\Dotenv;

class ConfigManager
{
    private static ?ConfigManager $instance = null;
    private array $config = [];
    private string $configPath;
    private string $envPath;

    private function __construct()
    {
        $this->configPath = dirname(__DIR__, 2) . '/config/';
        $this->envPath = dirname(__DIR__, 2) . '/';
        $this->loadConfiguration();
    }

    public static function getInstance(): ConfigManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfiguration(): void
    {
        // Load environment variables
        if (file_exists($this->envPath . '.env')) {
            $dotenv = Dotenv::createImmutable($this->envPath);
            $dotenv->load();
        }

        // Load YAML configuration
        $configFiles = [
            'scanner.yaml',
            'apis.yaml',
            'vulnerabilities.yaml'
        ];

        foreach ($configFiles as $file) {
            $filePath = $this->configPath . $file;
            if (file_exists($filePath)) {
                $this->config = array_merge($this->config, Yaml::parseFile($filePath));
            }
        }

        // Set defaults
        $this->setDefaults();
    }

    private function setDefaults(): void
    {
        $defaults = [
            'scanner' => [
                'timeout' => $_ENV['SCAN_TIMEOUT'] ?? 30,
                'max_concurrent_scans' => $_ENV['MAX_CONCURRENT_SCANS'] ?? 5,
                'user_agent' => $_ENV['USER_AGENT'] ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'save_results' => $_ENV['SAVE_RESULTS'] ?? true,
                'results_dir' => $_ENV['RESULTS_DIR'] ?? 'results/',
                'export_formats' => explode(',', $_ENV['EXPORT_FORMATS'] ?? 'json,csv,html')
            ],
            'security' => [
                'enable_rate_limiting' => $_ENV['ENABLE_RATE_LIMITING'] ?? true,
                'max_requests_per_minute' => $_ENV['MAX_REQUESTS_PER_MINUTE'] ?? 60,
                'blocked_ips' => explode(',', $_ENV['BLOCKED_IPS'] ?? '127.0.0.1,192.168.1.1')
            ],
            'logging' => [
                'level' => $_ENV['LOG_LEVEL'] ?? 'INFO',
                'file' => $_ENV['LOG_FILE'] ?? 'logs/grim.log'
            ],
            'apis' => [
                'viewdns' => [
                    'key' => $_ENV['VIEWDNS_API_KEY'] ?? '',
                    'base_url' => 'https://api.viewdns.info/'
                ],
                'moz' => [
                    'access_id' => $_ENV['MOZ_ACCESS_ID'] ?? '',
                    'secret_key' => $_ENV['MOZ_SECRET_KEY'] ?? '',
                    'base_url' => 'http://lsapi.seomoz.com/'
                ]
            ]
        ];

        $this->config = array_merge_recursive($defaults, $this->config);
    }

    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }

        return $value;
    }

    public function set(string $key, $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    public function getAll(): array
    {
        return $this->config;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }
}
