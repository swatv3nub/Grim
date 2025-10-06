<?php
namespace Grim\Command;

use Grim\Utils\ResultLogger;
use Grim\Utils\SIEMExporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportSIEMCommand extends Command
{
    protected static $defaultName = 'export:siem';
    protected static $defaultDescription = 'Export a scan result to SIEM (Splunk, ELK, Graylog)';

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'Result ID');
        $this->addArgument('siem', InputArgument::REQUIRED, 'SIEM type (splunk|elk|graylog)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $logger = new ResultLogger();
        $id = $input->getArgument('id');
        $siem = strtolower($input->getArgument('siem'));
        $result = $logger->getResult($id);
        if (!$result) {
            $io->error('Result not found.');
            return Command::FAILURE;
        }
        try {
            switch ($siem) {
                case 'splunk':
                    SIEMExporter::exportToSplunk($result);
                    break;
                case 'elk':
                    SIEMExporter::exportToELK($result);
                    break;
                case 'graylog':
                    SIEMExporter::exportToGraylog($result);
                    break;
                default:
                    $io->error('Unknown SIEM type.');
                    return Command::FAILURE;
            }
            $io->success('Exported to ' . $siem . ' successfully.');
            // Optionally log export attempt
            if (class_exists('Grim\\Utils\\Logger')) {
                \Grim\Utils\Logger::getInstance()->info('Exported scan result to SIEM', [
                    'id' => $id,
                    'siem' => $siem
                ]);
            }
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error('Export failed: ' . $e->getMessage());
            if (class_exists('Grim\\Utils\\Logger')) {
                \Grim\Utils\Logger::getInstance()->error('SIEM export failed', [
                    'id' => $id,
                    'siem' => $siem,
                    'error' => $e->getMessage()
                ]);
            }
            return Command::FAILURE;
        }
    }
}
