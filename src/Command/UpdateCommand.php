<?php

namespace Grim\Command;

use Grim\Utils\Logger;
use Grim\Utils\HttpClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateCommand extends Command
{
    protected static $defaultName = 'update';
    protected static $defaultDescription = 'Check for updates and upgrade GRIM Security Scanner';

    private Logger $logger;
    private HttpClient $httpClient;
    private string $currentVersion;
    private string $updateUrl = 'https://api.github.com/repos/swatv3nub/grim/releases/latest';

    public function __construct()
    {
        parent::__construct();
        $this->logger = Logger::getInstance();
        $this->httpClient = new HttpClient();
        $this->currentVersion = $this->getCurrentVersion();
    }

    protected function configure(): void
    {
        $this
            ->addOption('check', 'c', InputOption::VALUE_NONE, 'Check for updates only')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force update even if no new version available')
            ->addOption('backup', 'b', InputOption::VALUE_NONE, 'Create backup before updating')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Show what would be updated without making changes')
            ->addOption('source', 's', InputOption::VALUE_REQUIRED, 'Custom update source URL')
            ->setHelp('This command checks for updates and can upgrade GRIM Security Scanner to the latest version.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            if ($input->getOption('check')) {
                return $this->checkForUpdates($io);
            } else {
                return $this->performUpdate($io, $input);
            }
        } catch (\Exception $e) {
            $io->error("Update operation failed: " . $e->getMessage());
            $this->logger->error("Update command failed", ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }

    private function checkForUpdates(SymfonyStyle $io): int
    {
        $io->title('Checking for GRIM Updates');
        $io->text("Current version: <info>{$this->currentVersion}</info>");
        $io->text("Checking for updates...\n");

        try {
            $latestVersion = $this->getLatestVersion();

            if ($latestVersion === null) {
                $io->error("Failed to check for updates. Please check your internet connection.");
                return Command::FAILURE;
            }

            $io->text("Latest available version: <info>{$latestVersion}</info>");

            if (version_compare($this->currentVersion, $latestVersion, '<')) {
                $io->success("Update available!");
                $io->text("You can update by running: <comment>grim update</comment>");
                return Command::SUCCESS;
            } else {
                $io->success("You are running the latest version of GRIM Security Scanner!");
                return Command::SUCCESS;
            }
        } catch (\Exception $e) {
            $io->error("Update check failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function performUpdate(SymfonyStyle $io, InputInterface $input): int
    {
        $io->title('GRIM Security Scanner Update');
        $io->text("Current version: <info>{$this->currentVersion}</info>");

        // Check for updates first
        $latestVersion = $this->getLatestVersion();
        if ($latestVersion === null) {
            $io->error("Failed to check for updates. Please check your internet connection.");
            return Command::FAILURE;
        }

        $io->text("Latest available version: <info>{$latestVersion}</info>");

        if (version_compare($this->currentVersion, $latestVersion, '>=') && !$input->getOption('force')) {
            $io->success("You are already running the latest version!");
            $io->text("Use --force to update anyway.");
            return Command::SUCCESS;
        }

        // Confirm update
        if (!$io->confirm("Do you want to update from {$this->currentVersion} to {$latestVersion}?", false)) {
            $io->text("Update cancelled.");
            return Command::SUCCESS;
        }

        // Create backup if requested
        if ($input->getOption('backup')) {
            $io->text("Creating backup...");
            if (!$this->createBackup($io)) {
                $io->error("Failed to create backup. Update cancelled.");
                return Command::FAILURE;
            }
            $io->success("Backup created successfully.");
        }

        // Perform update
        if ($input->getOption('dry-run')) {
            $io->text("DRY RUN - No changes will be made");
            $io->text("Would download: {$latestVersion}");
            $io->text("Would update files in: " . __DIR__ . '/../../');
            return Command::SUCCESS;
        }

        $io->text("Starting update process...");

        try {
            $this->downloadUpdate($latestVersion, $io);
            $this->installUpdate($latestVersion, $io);

            $io->success("Update completed successfully!");
            $io->text("GRIM Security Scanner has been updated to version {$latestVersion}");
            $io->text("Please restart the application to use the new version.");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error("Update failed: " . $e->getMessage());

            // Try to restore from backup if available
            if ($input->getOption('backup')) {
                $io->text("Attempting to restore from backup...");
                if ($this->restoreBackup($io)) {
                    $io->success("Successfully restored from backup.");
                } else {
                    $io->error("Failed to restore from backup. Manual intervention may be required.");
                }
            }

            return Command::FAILURE;
        }
    }

    private function getCurrentVersion(): string
    {
        $versionFile = __DIR__ . '/../../version.txt';
        if (file_exists($versionFile)) {
            return trim(file_get_contents($versionFile));
        }

        // Fallback to hardcoded version
        return '5.0.0';
    }

    private function getLatestVersion(): ?string
    {
        try {
            $response = $this->httpClient->get($this->updateUrl);
            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['tag_name'])) {
                    return ltrim($data['tag_name'], 'v');
                }
            }
        } catch (\Exception $e) {
            $this->logger->error("Failed to fetch latest version", ['error' => $e->getMessage()]);
        }

        return null;
    }

    private function createBackup(SymfonyStyle $io): bool
    {
        try {
            $backupDir = __DIR__ . '/../../backups';
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $backupName = 'grim_backup_' . date('Y-m-d_H-i-s');
            $backupPath = $backupDir . '/' . $backupName;

            // Create backup archive
            $zip = new \ZipArchive();
            if ($zip->open($backupPath . '.zip', \ZipArchive::CREATE) === true) {
                $this->addDirectoryToZip($zip, __DIR__ . '/../../', 'grim');
                $zip->close();

                $io->text("Backup saved to: {$backupPath}.zip");
                return true;
            }

            return false;
        } catch (\Exception $e) {
            $io->text("Backup failed: " . $e->getMessage());
            return false;
        }
    }

    private function addDirectoryToZip(\ZipArchive $zip, string $dir, string $relativePath): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativeFilePath = $relativePath . '/' . substr($filePath, strlen($dir) + 1);

                // Skip certain files and directories
                if (
                    strpos($relativeFilePath, 'backups/') === 0 ||
                    strpos($relativeFilePath, 'vendor/') === 0 ||
                    strpos($relativeFilePath, '.git/') === 0
                ) {
                    continue;
                }

                $zip->addFile($filePath, $relativeFilePath);
            }
        }
    }

    private function downloadUpdate(string $version, SymfonyStyle $io): void
    {
        $io->text("Downloading update package...");

        $downloadUrl = "https://github.com/swatv3nub/grim/releases/download/v{$version}/grim-{$version}.zip";
        $tempFile = sys_get_temp_dir() . '/grim-update-' . $version . '.zip';

        $response = $this->httpClient->get($downloadUrl);
        if (!$response) {
            throw new \RuntimeException("Failed to download update package");
        }

        if (file_put_contents($tempFile, $response) === false) {
            throw new \RuntimeException("Failed to save update package");
        }

        $io->text("Update package downloaded successfully.");
    }

    private function installUpdate(string $version, SymfonyStyle $io): void
    {
        $io->text("Installing update...");

        $tempFile = sys_get_temp_dir() . '/grim-update-' . $version . '.zip';
        $extractDir = sys_get_temp_dir() . '/grim-update-' . $version;

        // Extract update package
        $zip = new \ZipArchive();
        if ($zip->open($tempFile) !== true) {
            throw new \RuntimeException("Failed to open update package");
        }

        if (!$zip->extractTo($extractDir)) {
            throw new \RuntimeException("Failed to extract update package");
        }
        $zip->close();

        // Install files
        $this->copyDirectory($extractDir . '/grim', __DIR__ . '/../../');

        // Clean up
        unlink($tempFile);
        $this->removeDirectory($extractDir);

        $io->text("Update installed successfully.");
    }

    private function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $target = $destination . '/' . $files->getSubPathName();

            if ($file->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                copy($file, $target);
            }
        }
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($dir);
    }

    private function restoreBackup(SymfonyStyle $io): bool
    {
        try {
            $backupDir = __DIR__ . '/../../backups';
            $backups = glob($backupDir . '/grim_backup_*.zip');

            if (empty($backups)) {
                $io->text("No backup files found.");
                return false;
            }

            // Use the most recent backup
            $latestBackup = end($backups);
            $io->text("Restoring from: " . basename($latestBackup));

            $zip = new \ZipArchive();
            if ($zip->open($latestBackup) === true) {
                $zip->extractTo(__DIR__ . '/../../');
                $zip->close();

                $io->text("Backup restored successfully.");
                return true;
            }

            return false;
        } catch (\Exception $e) {
            $io->text("Restore failed: " . $e->getMessage());
            return false;
        }
    }
}
