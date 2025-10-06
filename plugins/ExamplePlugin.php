<?php
use Grim\Plugin\PluginInterface;

class ExamplePlugin implements PluginInterface
{
    public function getName(): string
    {
        return 'Example Plugin';
    }

    public function run(array $context = []): void
    {
        echo "[PLUGIN] ExamplePlugin executed!\n";
    }
}
