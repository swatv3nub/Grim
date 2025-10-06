<?php

namespace Grim\Utils;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Level;

class Logger
{
    private static ?Logger $instance = null;
    private MonologLogger $logger;
    private string $logFile;
    private string $logLevel;

    private function __construct()
    {
        $this->logFile = dirname(__DIR__, 2) . '/logs/grim.log';
        $this->logLevel = $_ENV['LOG_LEVEL'] ?? 'INFO';

        $this->initializeLogger();
    }

    public static function getInstance(): Logger
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initializeLogger(): void
    {
        // Create logs directory if it doesn't exist
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $this->logger = new MonologLogger('grim_scanner');

        // Add rotating file handler
        $fileHandler = new RotatingFileHandler($this->logFile, 30, Level::Debug);
        $fileHandler->setFormatter(new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s'
        ));
        $this->logger->pushHandler($fileHandler);

        // Add console handler for CLI
        if (php_sapi_name() === 'cli') {
            $consoleHandler = new StreamHandler('php://stdout', Level::Info);
            $consoleHandler->setFormatter(new LineFormatter(
                "%level_name%: %message% %context%\n"
            ));
            $this->logger->pushHandler($consoleHandler);
        }
    }

    public function emergency(string $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    public function log(string $level, string $message, array $context = []): void
    {
        $this->logger->log(Level::fromName($level), $message, $context);
    }

    public function getLogger(): MonologLogger
    {
        return $this->logger;
    }

    public function setLogLevel(string $level): void
    {
        $this->logLevel = strtoupper($level);
        // Update handlers with new level
        foreach ($this->logger->getHandlers() as $handler) {
            $handler->setLevel(Level::fromName($this->logLevel));
        }
    }
}
