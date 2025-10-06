<?php

namespace Grim\Scanner;

use Grim\Utils\Logger;
use Grim\Utils\HttpClient;
use Grim\Config\ConfigManager;

abstract class Scanner
{
    protected Logger $logger;
    protected HttpClient $httpClient;
    protected ConfigManager $config;
    protected string $targetUrl;
    protected array $results = [];
    protected bool $verbose = false;

    public function __construct(string $targetUrl, bool $verbose = false)
    {
        $this->targetUrl = $targetUrl;
        $this->verbose = $verbose;
        $this->logger = Logger::getInstance();
        $this->httpClient = new HttpClient();
        $this->config = ConfigManager::getInstance();

        $this->initialize();
    }

    abstract protected function initialize(): void;
    abstract public function scan(): array;
    abstract public function getName(): string;

    protected function logInfo(string $message, array $context = []): void
    {
        if ($this->verbose) {
            $this->logger->info($message, $context);
        }
    }

    protected function logWarning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    protected function logError(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    protected function logDebug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function setTargetUrl(string $targetUrl): void
    {
        $this->targetUrl = $targetUrl;
    }

    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }

    public function isVerbose(): bool
    {
        return $this->verbose;
    }

    public function setVerbose(bool $verbose): void
    {
        $this->verbose = $verbose;
    }

    protected function addResult(string $type, string $description, array $details = [], string $severity = 'info'): void
    {
        $this->results[] = [
            'type' => $type,
            'description' => $description,
            'details' => $details,
            'severity' => $severity,
            'timestamp' => date('Y-m-d H:i:s'),
            'target' => $this->targetUrl
        ];
    }

    protected function addVulnerability(string $type, string $description, array $details = []): void
    {
        $this->addResult($type, $description, $details, 'vulnerability');
    }

    protected function addInfo(string $type, string $description, array $details = []): void
    {
        $this->addResult($type, $description, $details, 'info');
    }

    protected function addWarning(string $type, string $description, array $details = []): void
    {
        $this->addResult($type, $description, $details, 'warning');
    }

    protected function validateUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    protected function sanitizeUrl(string $url): string
    {
        $url = trim($url);

        // Remove protocol if present
        $url = preg_replace('/^https?:\/\//', '', $url);

        // Add http protocol if none specified
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'http://' . $url;
        }

        return $url;
    }

    protected function getDomainFromUrl(string $url): string
    {
        $parsed = parse_url($url);
        return $parsed['host'] ?? $url;
    }

    protected function getBaseUrl(string $url): string
    {
        $parsed = parse_url($url);
        $scheme = $parsed['scheme'] ?? 'http';
        $host = $parsed['host'] ?? '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';

        return $scheme . '://' . $host . $port;
    }

    protected function buildUrl(string $path): string
    {
        $baseUrl = $this->getBaseUrl($this->targetUrl);
        $path = ltrim($path, '/');
        return $baseUrl . '/' . $path;
    }

    protected function delay(float $seconds): void
    {
        if ($seconds > 0) {
            usleep($seconds * 1000000);
        }
    }

    protected function randomDelay(float $min = 0.1, float $max = 2.0): void
    {
        $this->delay(rand($min * 100, $max * 100) / 100);
    }
}
