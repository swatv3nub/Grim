<?php

namespace Grim\Scanner;

use Grim\Utils\Logger;
use Grim\Utils\HttpClient;
use Grim\Config\ConfigManager;

class InformationGatheringScanner extends Scanner
{
    private array $gatheredInfo = [];
    private array $apis = [];

    protected function initialize(): void
    {
        $this->loadApiConfigurations();
        $this->logger->info("Information gathering scanner initialized", ['target' => $this->targetUrl]);
    }

    public function getName(): string
    {
        return 'Information Gathering Scanner';
    }

    private function loadApiConfigurations(): void
    {
        $this->apis = [
            'viewdns' => [
                'key' => $this->config->get('apis.viewdns.key'),
                'base_url' => $this->config->get('apis.viewdns.base_url')
            ],
            'moz' => [
                'access_id' => $this->config->get('apis.moz.access_id'),
                'secret_key' => $this->config->get('apis.moz.secret_key'),
                'base_url' => $this->config->get('apis.moz.base_url')
            ]
        ];
    }

    public function scan(): array
    {
        $this->logger->info("Starting information gathering scan", ['target' => $this->targetUrl]);

        $this->gatherBasicInfo();
        $this->gatherWhoisInfo();
        $this->gatherGeoIPInfo();
        $this->gatherDNSInfo();
        $this->gatherSubdomainInfo();
        $this->gatherPortInfo();
        $this->gatherTechnologyInfo();
        $this->gatherSocialMediaInfo();
        $this->gatherEmailInfo();
        $this->gatherCloudInfo();

        $this->logger->info("Information gathering scan completed", [
            'target' => $this->targetUrl,
            'info_gathered' => count($this->gatheredInfo)
        ]);

        return $this->gatheredInfo;
    }

    private function gatherBasicInfo(): void
    {
        $this->logInfo("Gathering basic information");

        $domain = $this->getDomainFromUrl($this->targetUrl);
        $ip = gethostbyname($domain);

        $this->gatheredInfo['basic'] = [
            'domain' => $domain,
            'ip_address' => $ip,
            'hostname' => gethostbyaddr($ip),
            'scan_timestamp' => date('Y-m-d H:i:s')
        ];

        $this->addInfo('basic_info', 'Basic information gathered', $this->gatheredInfo['basic']);
    }

    private function gatherWhoisInfo(): void
    {
        if (empty($this->apis['viewdns']['key'])) {
            $this->logWarning("ViewDNS API key not configured, skipping WHOIS lookup");
            return;
        }

        $this->logInfo("Gathering WHOIS information");

        $domain = $this->getDomainFromUrl($this->targetUrl);
        $url = $this->apis['viewdns']['base_url'] . "whois/v2/?domain={$domain}&apikey=" . $this->apis['viewdns']['key'];

        $response = $this->httpClient->get($url);

        if ($response) {
            $whoisData = json_decode($response, true);
            if ($whoisData) {
                $this->gatheredInfo['whois'] = $whoisData;
                $this->addInfo('whois_info', 'WHOIS information gathered', $whoisData);
            }
        }

        $this->randomDelay(1, 3);
    }

    private function gatherGeoIPInfo(): void
    {
        if (empty($this->apis['viewdns']['key'])) {
            $this->logWarning("ViewDNS API key not configured, skipping GeoIP lookup");
            return;
        }

        $this->logInfo("Gathering GeoIP information");

        $ip = $this->gatheredInfo['basic']['ip_address'] ?? gethostbyname($this->getDomainFromUrl($this->targetUrl));
        $url = $this->apis['viewdns']['base_url'] . "iplocation/?domain={$ip}&apikey=" . $this->apis['viewdns']['key'];

        $response = $this->httpClient->get($url);

        if ($response) {
            $geoData = json_decode($response, true);
            if ($geoData) {
                $this->gatheredInfo['geoip'] = $geoData;
                $this->addInfo('geoip_info', 'GeoIP information gathered', $geoData);
            }
        }

        $this->randomDelay(1, 3);
    }

    private function gatherDNSInfo(): void
    {
        if (empty($this->apis['viewdns']['key'])) {
            $this->logWarning("ViewDNS API key not configured, skipping DNS lookup");
            return;
        }

        $this->logInfo("Gathering DNS information");

        $domain = $this->getDomainFromUrl($this->targetUrl);
        $url = $this->apis['viewdns']['base_url'] . "dnsrecord/?domain={$domain}&apikey=" . $this->apis['viewdns']['key'];

        $response = $this->httpClient->get($url);

        if ($response) {
            $dnsData = json_decode($response, true);
            if ($dnsData) {
                $this->gatheredInfo['dns'] = $dnsData;
                $this->addInfo('dns_info', 'DNS information gathered', $dnsData);
            }
        }

        $this->randomDelay(1, 3);
    }

    private function gatherSubdomainInfo(): void
    {
        if (empty($this->apis['viewdns']['key'])) {
            $this->logWarning("ViewDNS API key not configured, skipping subdomain lookup");
            return;
        }

        $this->logInfo("Gathering subdomain information");

        $ip = $this->gatheredInfo['basic']['ip_address'] ?? gethostbyname($this->getDomainFromUrl($this->targetUrl));
        $url = $this->apis['viewdns']['base_url'] . "reversedns/?ip={$ip}&apikey=" . $this->apis['viewdns']['key'];

        $response = $this->httpClient->get($url);

        if ($response) {
            $subdomainData = json_decode($response, true);
            if ($subdomainData) {
                $this->gatheredInfo['subdomains'] = $subdomainData;
                $this->addInfo('subdomain_info', 'Subdomain information gathered', $subdomainData);
            }
        }

        $this->randomDelay(1, 3);
    }

    private function gatherPortInfo(): void
    {
        if (empty($this->apis['viewdns']['key'])) {
            $this->logWarning("ViewDNS API key not configured, skipping port scan");
            return;
        }

        $this->logInfo("Gathering port information");

        $domain = $this->getDomainFromUrl($this->targetUrl);
        $url = $this->apis['viewdns']['base_url'] . "portscan/?host={$domain}&apikey=" . $this->apis['viewdns']['key'];

        $response = $this->httpClient->get($url);

        if ($response) {
            $portData = json_decode($response, true);
            if ($portData) {
                $this->gatheredInfo['ports'] = $portData;
                $this->addInfo('port_info', 'Port information gathered', $portData);
            }
        }

        $this->randomDelay(1, 3);
    }

    private function gatherTechnologyInfo(): void
    {
        $this->logInfo("Gathering technology information");

        $response = $this->httpClient->get($this->targetUrl);

        if ($response) {
            $techInfo = $this->extractTechnologyInfo($response);
            $this->gatheredInfo['technology'] = $techInfo;
            $this->addInfo('technology_info', 'Technology information gathered', $techInfo);
        }
    }

    private function extractTechnologyInfo(string $html): array
    {
        $techInfo = [
            'web_server' => null,
            'cms' => null,
            'programming_language' => null,
            'frameworks' => [],
            'libraries' => [],
            'analytics' => [],
            'advertising' => [],
            'hosting' => null
        ];

        // Web Server Detection
        $headers = $this->httpClient->head($this->targetUrl);
        if ($headers && isset($headers['Server'])) {
            $techInfo['web_server'] = is_array($headers['Server']) ? $headers['Server'][0] : $headers['Server'];
        }

        // CMS Detection
        if (strpos($html, '/wp-content/') !== false) {
            $techInfo['cms'] = 'WordPress';
        } elseif (strpos($html, 'Joomla') !== false) {
            $techInfo['cms'] = 'Joomla';
        } elseif (strpos($html, '/skin/frontend/') !== false) {
            $techInfo['cms'] = 'Magento';
        } elseif (strpos($html, 'Drupal') !== false) {
            $techInfo['cms'] = 'Drupal';
        }

        // Programming Language Detection
        if (strpos($html, '.php') !== false) {
            $techInfo['programming_language'] = 'PHP';
        } elseif (strpos($html, '.aspx') !== false) {
            $techInfo['programming_language'] = 'ASP.NET';
        } elseif (strpos($html, '.jsp') !== false) {
            $techInfo['programming_language'] = 'Java';
        }

        // Framework Detection
        if (strpos($html, 'jquery') !== false) {
            $techInfo['frameworks'][] = 'jQuery';
        }
        if (strpos($html, 'bootstrap') !== false) {
            $techInfo['frameworks'][] = 'Bootstrap';
        }
        if (strpos($html, 'react') !== false) {
            $techInfo['frameworks'][] = 'React';
        }
        if (strpos($html, 'angular') !== false) {
            $techInfo['frameworks'][] = 'Angular';
        }

        // Analytics Detection
        if (strpos($html, 'google-analytics') !== false || strpos($html, 'gtag') !== false) {
            $techInfo['analytics'][] = 'Google Analytics';
        }
        if (strpos($html, 'facebook.com/tr') !== false) {
            $techInfo['analytics'][] = 'Facebook Pixel';
        }

        return $techInfo;
    }

    private function gatherSocialMediaInfo(): void
    {
        $this->logInfo("Gathering social media information");

        $response = $this->httpClient->get($this->targetUrl);

        if ($response) {
            $socialInfo = $this->extractSocialMediaInfo($response);
            $this->gatheredInfo['social_media'] = $socialInfo;
            $this->addInfo('social_media_info', 'Social media information gathered', $socialInfo);
        }
    }

    private function extractSocialMediaInfo(string $html): array
    {
        $socialInfo = [
            'facebook' => [],
            'twitter' => [],
            'instagram' => [],
            'youtube' => [],
            'linkedin' => [],
            'github' => [],
            'pinterest' => []
        ];

        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $links = $dom->getElementsByTagName('a');

        foreach ($links as $link) {
            $href = $link->getAttribute('href');

            if (strpos($href, 'facebook.com/') !== false) {
                $socialInfo['facebook'][] = $href;
            } elseif (strpos($href, 'twitter.com/') !== false) {
                $socialInfo['twitter'][] = $href;
            } elseif (strpos($href, 'instagram.com/') !== false) {
                $socialInfo['instagram'][] = $href;
            } elseif (strpos($href, 'youtube.com/') !== false) {
                $socialInfo['youtube'][] = $href;
            } elseif (strpos($href, 'linkedin.com/') !== false) {
                $socialInfo['linkedin'][] = $href;
            } elseif (strpos($href, 'github.com/') !== false) {
                $socialInfo['github'][] = $href;
            } elseif (strpos($href, 'pinterest.com/') !== false) {
                $socialInfo['pinterest'][] = $href;
            }
        }

        // Remove duplicates
        foreach ($socialInfo as $platform => $links) {
            $socialInfo[$platform] = array_unique($links);
        }

        return $socialInfo;
    }

    private function gatherEmailInfo(): void
    {
        $this->logInfo("Gathering email information");

        $domain = $this->getDomainFromUrl($this->targetUrl);

        // MX Record Lookup
        $mxRecords = dns_get_record($domain, DNS_MX);
        $emailInfo = [
            'mx_records' => $mxRecords,
            'email_addresses' => []
        ];

        if ($mxRecords) {
            foreach ($mxRecords as $mx) {
                $emailInfo['email_addresses'][] = 'admin@' . $mx['target'];
                $emailInfo['email_addresses'][] = 'info@' . $mx['target'];
                $emailInfo['email_addresses'][] = 'contact@' . $mx['target'];
            }
        }

        $this->gatheredInfo['email'] = $emailInfo;
        $this->addInfo('email_info', 'Email information gathered', $emailInfo);
    }

    private function gatherCloudInfo(): void
    {
        $this->logInfo("Gathering cloud infrastructure information");

        $response = $this->httpClient->get($this->targetUrl);
        $headers = $this->httpClient->head($this->targetUrl);

        $cloudInfo = [
            'cloudflare' => false,
            'aws' => false,
            'azure' => false,
            'gcp' => false,
            'cdn' => false
        ];

        // Cloudflare Detection
        if ($response && strpos($response, 'cloudflare') !== false) {
            $cloudInfo['cloudflare'] = true;
        }

        // AWS Detection
        if ($response && (strpos($response, 'amazonaws.com') !== false || strpos($response, 'aws') !== false)) {
            $cloudInfo['aws'] = true;
        }

        // Azure Detection
        if ($response && strpos($response, 'azure') !== false) {
            $cloudInfo['azure'] = true;
        }

        // GCP Detection
        if ($response && strpos($response, 'googleapis.com') !== false) {
            $cloudInfo['gcp'] = true;
        }

        // CDN Detection
        if ($headers) {
            $cdnHeaders = ['X-CDN', 'X-Cache', 'CF-RAY', 'X-Akamai-Transformed'];
            foreach ($cdnHeaders as $header) {
                if (isset($headers[$header])) {
                    $cloudInfo['cdn'] = true;
                    break;
                }
            }
        }

        $this->gatheredInfo['cloud'] = $cloudInfo;
        $this->addInfo('cloud_info', 'Cloud infrastructure information gathered', $cloudInfo);
    }

    public function getGatheredInfo(): array
    {
        return $this->gatheredInfo;
    }

    public function exportToJson(): string
    {
        return json_encode($this->gatheredInfo, JSON_PRETTY_PRINT);
    }

    public function exportToCsv(): string
    {
        $csv = "Type,Description,Details\n";

        foreach ($this->gatheredInfo as $type => $data) {
            $csv .= $type . "," . json_encode($data) . "\n";
        }

        return $csv;
    }
}
