<?php
namespace Grim\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Grim\Cloud\AwsIntegration;
use Grim\Cloud\AzureIntegration;
use Grim\Cloud\GcpIntegration;

class CloudScanCommand extends Command
{
    protected static $defaultName = 'cloud-scan';
    protected static $defaultDescription = 'Scan cloud assets for vulnerabilities.';

    protected function configure(): void
    {
        $this
            ->addArgument('provider', InputArgument::REQUIRED, 'Cloud provider (aws|azure|gcp)')
            ->addArgument('asset', InputArgument::OPTIONAL, 'Cloud asset identifier (optional, scan all if omitted)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $provider = strtolower($input->getArgument('provider'));
        $assetId = $input->getArgument('asset');
        $assets = [];
        switch ($provider) {
            case 'aws':
                $assets = AwsIntegration::discoverAssets();
                break;
            case 'azure':
                $assets = AzureIntegration::discoverAssets();
                break;
            case 'gcp':
                $assets = GcpIntegration::discoverAssets();
                break;
            default:
                $io->error('Unknown provider. Use aws, azure, or gcp.');
                return Command::FAILURE;
        }
        if (empty($assets)) {
            $io->warning('No assets discovered for provider: ' . $provider);
            return Command::SUCCESS;
        }
        $io->title('Cloud Scan: ' . strtoupper($provider));
        $io->section('Discovered Assets');
        $io->listing(array_map(function($a) { return $a['id'] . ' (' . $a['type'] . ')'; }, $assets));

        $targets = [];
        if ($assetId) {
            $targets = array_filter($assets, fn($a) => $a['id'] === $assetId);
            if (empty($targets)) {
                $io->error('Asset not found: ' . $assetId);
                return Command::FAILURE;
            }
        } else {
            $targets = $assets;
        }

        $io->section('Scanning Assets for Vulnerabilities');
        foreach ($targets as $asset) {
            // Simulate a vulnerability scan (replace with real logic)
            $io->text('Scanning ' . $asset['id'] . ' (' . $asset['type'] . ') ...');
            // Here you would call your real scanner logic
            $vulns = [
                [
                    'id' => 'CLOUD-001',
                    'description' => 'Example vulnerability',
                    'severity' => 'medium',
                    'asset' => $asset['id']
                ]
            ];
            $io->table(['ID', 'Description', 'Severity'], array_map(fn($v) => [$v['id'], $v['description'], $v['severity']], $vulns));
        }
        $io->success('Cloud scan complete.');
        return Command::SUCCESS;
    }
}
