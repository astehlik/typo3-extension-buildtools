<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckPhpCsCommand extends AbstractT3Command
{
    protected function configure(): void
    {
        $this->setName('t3:check:php:cs')
            ->setDescription('Checks the PHP code style using PHP_CodeSniffer (phpcs).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->runCodeSniffer(false, $output);
    }
}
