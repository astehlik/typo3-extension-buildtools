<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FixPhpCsCommand extends AbstractT3Command
{
    protected function configure(): void
    {
        $this->setName('t3:fix:php:cs')
            ->setDescription('Fixes the PHP code style using PHP_CodeSniffer (phpcbf).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->runCodeSniffer(true, $output);
    }
}
