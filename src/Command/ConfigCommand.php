<?php

namespace Grim\Command;

use Grim\Config\ConfigManager;
use Grim\Utils\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConfigCommand extends Command
{
    protected static $defaultName = 'config';
    protected static $defaultDescription = 'Manage GRIM configuration settings';

    private Logger $logger;
    private ConfigManager $config;

    public function __construct()
    {
        parent::__construct();
        $this->logger = Logger::getInstance();
        $this->config = ConfigManager::getInstance();
    }

    protected function configure(): void
    {
        $this
            ->addOption('list', 'l', InputOption::VALUE_NONE, 'List all configuration options')
            ->addOption('get', 'g', InputOption::VALUE_REQUIRED, 'Get a specific configuration value')
            ->addOption('set', 's', InputOption::VALUE_REQUIRED, 'Set a configuration value (format: key=value)')
            ->addOption('reset', 'r', InputOption::VALUE_NONE, 'Reset configuration to defaults')
            ->addOption('export', 'e', InputOption::VALUE_REQUIRED, 'Export configuration to file (json, yaml, ini)')
            ->addOption('import', 'i', InputOption::VALUE_REQUIRED, 'Import configuration from file')
            ->addOption('validate', null, InputOption::VALUE_NONE, 'Validate current configuration')
            ->setHelp('This command allows you to view, modify, and manage GRIM configuration settings.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            if ($input->getOption('list')) {
                return $this->listConfig($io);
            } elseif ($input->getOption('get')) {
                return $this->getConfig($io, $input->getOption('get'));
            } elseif ($input->getOption('set')) {
                return $this->setConfig($io, $input->getOption('set'));
            } elseif ($input->getOption('reset')) {
                return $this->resetConfig($io);
            } elseif ($input->getOption('export')) {
                return $this->exportConfig($io, $input->getOption('export'));
            } elseif ($input->getOption('import')) {
                return $this->importConfig($io, $input->getOption('import'));
            } elseif ($input->getOption('validate')) {
                return $this->validateConfig($io);
            } else {
                // Show current configuration
                return $this->showCurrentConfig($io);
            }
        } catch (\Exception $e) {
            $io->error("Configuration operation failed: " . $e->getMessage());
            $this->logger->error("Configuration command failed", ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }

    private function listConfig(SymfonyStyle $io): int
    {
        $io->title('GRIM Configuration Options');
        
        $config = $this->config->getAll();
        
        foreach ($config as $section => $options) {
            $io->section(ucfirst($section));
            
            if (is_array($options)) {
                foreach ($options as $key => $value) {
                    $displayValue = is_array($value) ? json_encode($value) : $value;
                    $io->text("  <info>{$key}</info>: {$displayValue}");
                }
            } else {
                $io->text("  {$options}");
            }
        }

        return Command::SUCCESS;
    }

    private function getConfig(SymfonyStyle $io, string $key): int
    {
        $value = $this->config->get($key);
        
        if ($value === null) {
            $io->error("Configuration key '{$key}' not found");
            return Command::FAILURE;
        }

        $io->text("Configuration value for '{$key}':");
        if (is_array($value)) {
            $io->text(json_encode($value, JSON_PRETTY_PRINT));
        } else {
            $io->text($value);
        }

        return Command::SUCCESS;
    }

    private function setConfig(SymfonyStyle $io, string $keyValue): int
    {
        if (strpos($keyValue, '=') === false) {
            $io->error("Invalid format. Use: key=value");
            return Command::FAILURE;
        }

        [$key, $value] = explode('=', $keyValue, 2);
        $key = trim($key);
        $value = trim($value);

        // Try to convert value to appropriate type
        if (strtolower($value) === 'true') {
            $value = true;
        } elseif (strtolower($value) === 'false') {
            $value = false;
        } elseif (is_numeric($value)) {
            $value = (float) $value;
            if ((int) $value == $value) {
                $value = (int) $value;
            }
        }

        $this->config->set($key, $value);
        $io->success("Configuration '{$key}' set to: " . (is_array($value) ? json_encode($value) : $value));

        return Command::SUCCESS;
    }

    private function resetConfig(SymfonyStyle $io): int
    {
        if ($io->confirm('Are you sure you want to reset all configuration to defaults?', false)) {
            $this->config->resetToDefaults();
            $io->success("Configuration reset to defaults");
            return Command::SUCCESS;
        }

        $io->text("Configuration reset cancelled");
        return Command::SUCCESS;
    }

    private function exportConfig(SymfonyStyle $io, string $format): int
    {
        $config = $this->config->getAll();
        $filename = 'grim_config_' . date('Y-m-d_H-i-s');
        
        switch (strtolower($format)) {
            case 'json':
                $content = json_encode($config, JSON_PRETTY_PRINT);
                $extension = 'json';
                break;
                
            case 'yaml':
                $content = $this->arrayToYaml($config);
                $extension = 'yaml';
                break;
                
            case 'ini':
                $content = $this->arrayToIni($config);
                $extension = 'ini';
                break;
                
            default:
                $io->error("Unsupported export format: {$format}. Supported: json, yaml, ini");
                return Command::FAILURE;
        }

        $filepath = $filename . '.' . $extension;
        file_put_contents($filepath, $content);
        
        $io->success("Configuration exported to: {$filepath}");
        return Command::SUCCESS;
    }

    private function importConfig(SymfonyStyle $io, string $filepath): int
    {
        if (!file_exists($filepath)) {
            $io->error("Configuration file not found: {$filepath}");
            return Command::FAILURE;
        }

        $extension = pathinfo($filepath, PATHINFO_EXTENSION);
        $content = file_get_contents($filepath);
        
        switch (strtolower($extension)) {
            case 'json':
                $config = json_decode($content, true);
                break;
                
            case 'yaml':
                $config = $this->yamlToArray($content);
                break;
                
            case 'ini':
                $config = parse_ini_file($filepath, true);
                break;
                
            default:
                $io->error("Unsupported file format: {$extension}");
                return Command::FAILURE;
        }

        if ($config === null) {
            $io->error("Failed to parse configuration file");
            return Command::FAILURE;
        }

        $this->config->import($config);
        $io->success("Configuration imported from: {$filepath}");
        
        return Command::SUCCESS;
    }

    private function validateConfig(SymfonyStyle $io): int
    {
        $io->title('Validating Configuration');
        
        $errors = [];
        $warnings = [];
        
        // Check required settings
        $required = ['scanner.timeout', 'scanner.max_concurrent_scans', 'scanner.user_agent'];
        foreach ($required as $key) {
            if ($this->config->get($key) === null) {
                $errors[] = "Missing required configuration: {$key}";
            }
        }
        
        // Check timeout value
        $timeout = $this->config->get('scanner.timeout');
        if ($timeout !== null && ($timeout < 1 || $timeout > 300)) {
            $warnings[] = "Timeout value ({$timeout}) is outside recommended range (1-300 seconds)";
        }
        
        // Check max concurrent scans
        $maxScans = $this->config->get('scanner.max_concurrent_scans');
        if ($maxScans !== null && ($maxScans < 1 || $maxScans > 100)) {
            $warnings[] = "Max concurrent scans ({$maxScans}) is outside recommended range (1-100)";
        }
        
        // Display results
        if (empty($errors) && empty($warnings)) {
            $io->success("Configuration is valid");
        } else {
            if (!empty($errors)) {
                $io->error("Configuration errors found:");
                foreach ($errors as $error) {
                    $io->text("  • {$error}");
                }
            }
            
            if (!empty($warnings)) {
                $io->warning("Configuration warnings:");
                foreach ($warnings as $warning) {
                    $io->text("  • {$warning}");
                }
            }
        }
        
        return empty($errors) ? Command::SUCCESS : Command::FAILURE;
    }

    private function showCurrentConfig(SymfonyStyle $io): int
    {
        $io->title('Current GRIM Configuration');
        
        $config = $this->config->getAll();
        
        foreach ($config as $section => $options) {
            $io->section(ucfirst($section));
            
            if (is_array($options)) {
                foreach ($options as $key => $value) {
                    $displayValue = is_array($value) ? json_encode($value) : $value;
                    $io->text("  <info>{$key}</info>: {$displayValue}");
                }
            } else {
                $io->text("  {$options}");
            }
        }
        
        $io->newLine();
        $io->text("Use --help to see available configuration commands");
        
        return Command::SUCCESS;
    }

    private function arrayToYaml(array $array, int $indent = 0): string
    {
        $yaml = '';
        $indentStr = str_repeat('  ', $indent);
        
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $yaml .= $indentStr . $key . ":\n";
                $yaml .= $this->arrayToYaml($value, $indent + 1);
            } else {
                $yaml .= $indentStr . $key . ': ' . $value . "\n";
            }
        }
        
        return $yaml;
    }

    private function arrayToIni(array $array): string
    {
        $ini = '';
        
        foreach ($array as $section => $options) {
            $ini .= "[{$section}]\n";
            
            if (is_array($options)) {
                foreach ($options as $key => $value) {
                    $ini .= "{$key} = {$value}\n";
                }
            } else {
                $ini .= "{$options}\n";
            }
            
            $ini .= "\n";
        }
        
        return $ini;
    }

    private function yamlToArray(string $yaml): array
    {
        // Simple YAML parser for basic configuration
        $lines = explode("\n", $yaml);
        $config = [];
        $currentSection = null;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            
            if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*):$/', $line, $matches)) {
                $currentSection = $matches[1];
                $config[$currentSection] = [];
            } elseif (strpos($line, ':') !== false && $currentSection !== null) {
                [$key, $value] = explode(':', $line, 2);
                $key = trim($key);
                $value = trim($value);
                $config[$currentSection][$key] = $value;
            }
        }
        
        return $config;
    }
}
