<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Mirrors the "static checks only, no tests" scope of the tea extension's
 * check:static script - run t3:check:tests:* separately when you also want tests.
 */
final class CheckCommand extends AbstractT3Command
{
    private const STEPS = [
        't3:check:php',
    ];

    protected function configure(): void
    {
        $this->setName('t3:check')
            ->setDescription('Runs all static t3:check:* domain commands (currently just php).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (self::STEPS as $step) {
            $exitCode = $this->getApplication()->find($step)->run(new ArrayInput([]), $output);
            if ($exitCode !== self::SUCCESS) {
                return $exitCode;
            }
        }

        return self::SUCCESS;
    }
}
