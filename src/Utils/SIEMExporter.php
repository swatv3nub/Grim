<?php
namespace Grim\Utils;

class SIEMExporter
{
    private static function getConfig($type)
    {
        $config = require __DIR__ . '/../../config/siem.php';
        return $config[$type] ?? [];
    }

    public static function exportToSplunk($result)
    {
        $conf = self::getConfig('splunk');
        $url = $conf['url'];
        $token = $conf['token'];
        $verify = $conf['verify_ssl'] ?? true;
        $payload = json_encode([
            'event' => $result
        ]);
        $headers = [
            'Authorization: Splunk ' . $token,
            'Content-Type: application/json'
        ];
        return self::post($url, $payload, $headers, $verify);
    }

    public static function exportToELK($result)
    {
        $conf = self::getConfig('elk');
        $url = $conf['url'];
        $verify = $conf['verify_ssl'] ?? true;
        $payload = json_encode($result);
        $headers = [
            'Content-Type: application/json'
        ];
        $auth = null;
        if (!empty($conf['username']) && !empty($conf['password'])) {
            $auth = $conf['username'] . ':' . $conf['password'];
        }
        return self::post($url, $payload, $headers, $verify, $auth);
    }

    public static function exportToGraylog($result)
    {
        $conf = self::getConfig('graylog');
        $url = $conf['url'];
        $verify = $conf['verify_ssl'] ?? true;
        $payload = json_encode([
            'version' => '1.1',
            'host' => gethostname(),
            'short_message' => 'GRIM scan result',
            'full_message' => json_encode($result),
            'timestamp' => time()
        ]);
        $headers = [
            'Content-Type: application/json'
        ];
        return self::post($url, $payload, $headers, $verify);
    }

    private static function post($url, $payload, $headers, $verify = true, $auth = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verify);
        if ($auth) {
            curl_setopt($ch, CURLOPT_USERPWD, $auth);
        }
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($err) {
            throw new \RuntimeException("SIEM export failed: $err");
        }
        if ($code < 200 || $code >= 300) {
            throw new \RuntimeException("SIEM export failed: HTTP $code, Response: $response");
        }
        return true;
    }
}
