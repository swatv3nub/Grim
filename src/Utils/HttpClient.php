<?php

namespace Grim\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TimeoutException;
use Grim\Config\ConfigManager;

class HttpClient
{
    private Client $client;
    private ConfigManager $config;
    private Logger $logger;
    private array $rateLimit = [];
    private int $maxRequestsPerMinute;

    public function __construct()
    {
        $this->config = ConfigManager::getInstance();
        $this->logger = Logger::getInstance();
        $this->maxRequestsPerMinute = $this->config->get('security.max_requests_per_minute', 60);

        $this->initializeClient();
    }

    private function initializeClient(): void
    {
        $clientConfig = [
            'timeout' => $this->config->get('scanner.timeout', 30),
            'connect_timeout' => 10,
            'verify' => false, // For development/testing
            'headers' => [
                'User-Agent' => $this->config->get('scanner.user_agent'),
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Accept-Encoding' => 'gzip, deflate',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
            ]
        ];

        // Add proxy configuration if enabled
        if ($this->config->get('USE_PROXY', false)) {
            $clientConfig['proxy'] = sprintf(
                'http://%s:%s',
                $this->config->get('PROXY_HOST', '127.0.0.1'),
                $this->config->get('PROXY_PORT', '8080')
            );

            if ($this->config->get('PROXY_USER') && $this->config->get('PROXY_PASSWORD')) {
                $clientConfig['proxy'] = sprintf(
                    'http://%s:%s@%s:%s',
                    $this->config->get('PROXY_USER'),
                    $this->config->get('PROXY_PASSWORD'),
                    $this->config->get('PROXY_HOST', '127.0.0.1'),
                    $this->config->get('PROXY_PORT', '8080')
                );
            }
        }

        $this->client = new Client($clientConfig);
    }

    public function get(string $url, array $options = []): ?string
    {
        try {
            $this->checkRateLimit();

            $response = $this->client->get($url, array_merge([
                'timeout' => $this->config->get('scanner.timeout', 30)
            ], $options));

            $this->logger->info("GET request successful", ['url' => $url, 'status' => $response->getStatusCode()]);

            return $response->getBody()->getContents();
        } catch (RequestException $e) {
            $this->logger->error("HTTP request failed", [
                'url' => $url,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return null;
        } catch (ConnectException $e) {
            $this->logger->error("Connection failed", [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return null;
        } catch (TimeoutException $e) {
            $this->logger->error("Request timeout", [
                'url' => $url,
                'timeout' => $this->config->get('scanner.timeout', 30)
            ]);
            return null;
        } catch (\Exception $e) {
            $this->logger->error("Unexpected error during HTTP request", [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function post(string $url, array $data = [], array $options = []): ?string
    {
        try {
            $this->checkRateLimit();

            $response = $this->client->post($url, array_merge([
                'form_params' => $data,
                'timeout' => $this->config->get('scanner.timeout', 30)
            ], $options));

            $this->logger->info("POST request successful", ['url' => $url, 'status' => $response->getStatusCode()]);

            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            $this->logger->error("POST request failed", [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function head(string $url, array $options = []): ?array
    {
        try {
            $this->checkRateLimit();

            $response = $this->client->head($url, array_merge([
                'timeout' => $this->config->get('scanner.timeout', 30)
            ], $options));

            $this->logger->info("HEAD request successful", ['url' => $url, 'status' => $response->getStatusCode()]);

            return $response->getHeaders();
        } catch (\Exception $e) {
            $this->logger->error("HEAD request failed", [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function checkRateLimit(): void
    {
        if (!$this->config->get('security.enable_rate_limiting', true)) {
            return;
        }

        $currentTime = time();
        $minuteAgo = $currentTime - 60;

        // Clean old entries
        $this->rateLimit = array_filter($this->rateLimit, function ($timestamp) use ($minuteAgo) {
            return $timestamp > $minuteAgo;
        });

        // Check if we've exceeded the rate limit
        if (count($this->rateLimit) >= $this->maxRequestsPerMinute) {
            $sleepTime = 60 - ($currentTime - reset($this->rateLimit));
            if ($sleepTime > 0) {
                $this->logger->warning("Rate limit exceeded, sleeping for {$sleepTime} seconds");
                sleep($sleepTime);
            }
        }

        // Add current request timestamp
        $this->rateLimit[] = $currentTime;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->client = new Client(array_merge($this->client->getConfig(), [
            'headers' => array_merge($this->client->getConfig()['headers'], [
                'User-Agent' => $userAgent
            ])
        ]));
    }

    public function setProxy(string $proxy): void
    {
        $this->client = new Client(array_merge($this->client->getConfig(), [
            'proxy' => $proxy
        ]));
    }
}
