<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckComposerCommand extends AbstractT3Command
{
    private const STEPS = [
        't3:check:composer:validate',
        't3:check:composer:normalize',
    ];

    protected function configure(): void
    {
        $this->setName('t3:check:composer')
            ->setDescription('Runs all t3:check:composer:* commands (validate, normalize).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->runSteps(self::STEPS, $input, $output);
    }
}
