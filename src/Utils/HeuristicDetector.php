<?php
namespace Grim\Utils;

class HeuristicDetector
{
    public static function analyze($scanResult)
    {
        $anomalies = [];
        $score = 0;
        // Example: flag if more than 10 open ports
        if (isset($scanResult['scanners']['information_gathering']['open_ports']) &&
            count($scanResult['scanners']['information_gathering']['open_ports']) > 10) {
            $anomalies[] = 'High number of open ports detected.';
            $score += 2;
        }
        // Example: look for suspicious keywords in results
        $keywords = ['backdoor', 'exploit', 'shell'];
        $json = json_encode($scanResult);
        foreach ($keywords as $kw) {
            if (stripos($json, $kw) !== false) {
                $anomalies[] = "Suspicious keyword found: $kw";
                $score += 3;
            }
        }
        return [
            'anomalies' => $anomalies,
            'score' => $score
        ];
    }
}
