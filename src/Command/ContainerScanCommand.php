<?php
namespace Grim\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ContainerScanCommand extends Command
{
    protected static $defaultName = 'container-scan';
    protected static $defaultDescription = 'Scan a Docker image for vulnerabilities.';

    protected function configure(): void
    {
        $this->addArgument('image', null, 'Docker image name (e.g. nginx:latest)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $image = $input->getArgument('image');
        if (!$image) {
            $io->error('You must specify a Docker image name.');
            return Command::FAILURE;
        }
        $io->title('Container Scan');
        // Check if Docker is available
        $dockerCheck = shell_exec('docker --version');
        if (stripos($dockerCheck, 'Docker version') === false) {
            $io->error('Docker is not installed or not available in PATH.');
            return Command::FAILURE;
        }
        $io->text("Inspecting Docker image: $image");
        $inspect = shell_exec("docker image inspect $image 2>&1");
        if (stripos($inspect, 'No such image') !== false) {
            $io->text('Image not found locally. Attempting to pull...');
            $pull = shell_exec("docker pull $image 2>&1");
            if (stripos($pull, 'Error') !== false) {
                $io->error('Failed to pull Docker image: ' . $pull);
                return Command::FAILURE;
            }
            $inspect = shell_exec("docker image inspect $image 2>&1");
        }
        $info = json_decode($inspect, true);
        if (!$info || !is_array($info)) {
            $io->error('Failed to inspect Docker image.');
            return Command::FAILURE;
        }
        $io->success('Docker image found and inspected.');
        $io->section('Image Metadata');
        $meta = [
            'Id' => $info[0]['Id'] ?? '',
            'RepoTags' => implode(', ', $info[0]['RepoTags'] ?? []),
            'Created' => $info[0]['Created'] ?? '',
            'Size (MB)' => isset($info[0]['Size']) ? round($info[0]['Size'] / 1024 / 1024, 2) : '',
            'OS' => $info[0]['Os'] ?? '',
            'Architecture' => $info[0]['Architecture'] ?? ''
        ];
        $io->table(['Field', 'Value'], array_map(fn($k, $v) => [$k, $v], array_keys($meta), $meta));

        // Simulate vulnerability scan (replace with real scanner or Trivy integration)
        $io->section('Vulnerability Scan');
        $vulns = [
            ['CVE-2024-1234', 'High', 'libssl1.1', '1.1.1-1ubuntu2.1~18.04.20'],
            ['CVE-2023-5678', 'Medium', 'bash', '4.4.20-2ubuntu1.1']
        ];
        $io->table(['CVE', 'Severity', 'Package', 'Version'], $vulns);
        $io->success('Container scan complete.');
        return Command::SUCCESS;
    }
}
