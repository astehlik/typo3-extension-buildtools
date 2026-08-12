<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckPhpScanCommand extends AbstractT3Command
{
    protected function configure(): void
    {
        $this->setName('t3:check:php:scan')
            ->setDescription('Scans the extension for deprecated and breaking TYPO3 code using typo3scan.')
            ->addOption(
                'ignore',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma-separated list of paths to ignore',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = [$this->binPath('typo3scan'), 'scan'];

        $ignore = $input->getOption('ignore');
        if ($ignore !== null) {
            $command[] = '--ignore';
            $command[] = $ignore;
        }

        $command[] = '.';

        return $this->runProcess($command, $output);
    }
}
