<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckPhpCommand extends AbstractT3Command
{
    private const STEPS = [
        't3:check:php:lint',
        't3:check:php:cs',
        't3:check:php:cgl',
        't3:check:php:stan',
    ];

    protected function configure(): void
    {
        $this->setName('t3:check:php')
            ->setDescription('Runs all t3:check:php:* commands (lint, cs, cgl, stan).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->runSteps(self::STEPS, $input, $output);
    }
}
