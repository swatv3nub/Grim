<?php
namespace Grim\Utils;

class RuleLoader
{
    public static function loadCustomRules($file = __DIR__ . '/../../rules/custom_rules.json')
    {
        if (!file_exists($file)) return [];
        $json = file_get_contents($file);
        return json_decode($json, true) ?: [];
    }
}
