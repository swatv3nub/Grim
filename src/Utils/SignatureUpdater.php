<?php
namespace Grim\Utils;

class SignatureUpdater
{
    public static function update($remoteUrl = 'https://example.com/rules.json', $localFile = __DIR__ . '/../../rules/custom_rules.json')
    {
        $data = @file_get_contents($remoteUrl);
        if ($data === false) {
            throw new \RuntimeException('Failed to download rules from ' . $remoteUrl);
        }
        if (json_decode($data) === null) {
            throw new \RuntimeException('Downloaded rules are not valid JSON.');
        }
        file_put_contents($localFile, $data);
        return true;
    }
}
