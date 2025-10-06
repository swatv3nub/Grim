<?php
namespace Grim\Command;

use Grim\Utils\ResultLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ResultHistoryCommand extends Command
{
    protected static $defaultName = 'results';
    protected static $defaultDescription = 'List and view past scan results';

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::OPTIONAL, 'Result ID to view details');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $logger = new ResultLogger();
        $id = $input->getArgument('id');

        if ($id) {
            $result = $logger->getResult($id);
            if ($result) {
                $io->title('Scan Result #' . $id);
                $io->text('Target: ' . $result['target']);
                $io->text('Time: ' . $result['scan_time']);
                $io->writeln('<info>Result JSON:</info>');
                $io->writeln('<pre>' . json_encode(json_decode($result['result_json']), JSON_PRETTY_PRINT) . '</pre>');
            } else {
                $io->error('Result not found.');
            }
        } else {
            $results = $logger->listResults();
            if ($results) {
                $io->table(['ID', 'Target', 'Time'], $results);
            } else {
                $io->warning('No results found.');
            }
        }
        return Command::SUCCESS;
    }
}
