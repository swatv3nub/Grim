<?php
namespace Grim\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SelfUpdateCommand extends Command
{
    protected static $defaultName = 'self-update';
    protected static $defaultDescription = 'Check for and apply updates to GRIM.';

    protected function configure(): void
    {
        // No options for now
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Self-Update');
        $repo = 'swatv3nub/Grim';
        $apiUrl = "https://api.github.com/repos/$repo/releases/latest";
        $io->text('Checking for latest release on GitHub...');
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'GrimSelfUpdater');
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $io->error('Failed to check for updates: ' . curl_error($ch));
            return Command::FAILURE;
        }
        $release = json_decode($response, true);
        if (empty($release['tag_name'])) {
            $io->error('Could not determine latest version.');
            return Command::FAILURE;
        }
        $latestVersion = ltrim($release['tag_name'], 'v');
        $currentVersion = defined('GRIM_VERSION') ? GRIM_VERSION : '5.0.0';
        $io->text("Current version: $currentVersion");
        $io->text("Latest version: $latestVersion");
        if (version_compare($latestVersion, $currentVersion, '<=')) {
            $io->success('You are already running the latest version.');
            return Command::SUCCESS;
        }
        $io->section('Downloading update...');
        $asset = $release['assets'][0] ?? null;
        if (!$asset || empty($asset['browser_download_url'])) {
            $io->error('No downloadable asset found in the latest release.');
            return Command::FAILURE;
        }
        $downloadUrl = $asset['browser_download_url'];
        $tmpFile = tempnam(sys_get_temp_dir(), 'grim_update_');
        $fp = fopen($tmpFile, 'w');
        $ch = curl_init($downloadUrl);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'GrimSelfUpdater');
        curl_exec($ch);
        if (curl_errno($ch)) {
            fclose($fp);
            unlink($tmpFile);
            $io->error('Failed to download update: ' . curl_error($ch));
            return Command::FAILURE;
        }
        fclose($fp);
        curl_close($ch);
        $io->success('Update downloaded. Please extract and replace files manually. (Auto-extract not implemented for safety)');
        $io->text('Downloaded file: ' . $tmpFile);
        return Command::SUCCESS;
    }
}
