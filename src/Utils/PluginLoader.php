<?php
namespace Grim\Utils;

class PluginLoader
{
    public static function loadPlugins(string $dir = __DIR__ . '/../../plugins')
    {
        $plugins = [];
        foreach (glob($dir . '/*.php') as $file) {
            require_once $file;
            $class = basename($file, '.php');
            if (class_exists($class)) {
                $plugin = new $class();
                if (method_exists($plugin, 'run')) {
                    $plugins[] = $plugin;
                }
            }
        }
        return $plugins;
    }
}
