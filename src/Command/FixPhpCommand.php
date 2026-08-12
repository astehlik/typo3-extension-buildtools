<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FixPhpCommand extends AbstractT3Command
{
    private const STEPS = [
        't3:fix:php:cs',
        't3:fix:php:cgl',
    ];

    protected function configure(): void
    {
        $this->setName('t3:fix:php')
            ->setDescription('Runs all t3:fix:php:* commands (cs, cgl).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->runSteps(self::STEPS, $input, $output);
    }
}
