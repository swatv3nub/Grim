<?php
namespace Grim\Plugin;

interface PluginInterface
{
    public function getName(): string;
    public function run(array $context = []): void;
}
