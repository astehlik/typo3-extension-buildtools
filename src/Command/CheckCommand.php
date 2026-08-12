<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Mirrors the "static checks only, no tests" scope of the tea extension's
 * check:static script - run t3:check:tests:* separately when you also want tests.
 */
final class CheckCommand extends AbstractT3Command
{
    private const STEPS = [
        't3:check:composer',
        't3:check:php',
        't3:check:typo3:scan',
    ];

    protected function configure(): void
    {
        $this->setName('t3:check')
            ->setDescription('Runs all static t3:check:* domain commands (composer, php, typo3).')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Forwarded to steps that support it (e.g. skips the "var" removal confirmation for t3:check:typo3:scan)',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->runSteps(self::STEPS, $input, $output);
    }
}
