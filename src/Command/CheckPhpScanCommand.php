<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckPhpScanCommand extends AbstractT3Command
{
    protected function configure(): void
    {
        $this->setName('t3:check:php:scan')
            ->setDescription('Scans the extension for deprecated and breaking TYPO3 code using typo3scan.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = [$this->binPath('typo3scan'), 'scan'];

        $ignore = $this->configuredIgnore();
        if ($ignore !== null) {
            $command[] = '--ignore';
            $command[] = $ignore;
        }

        $command[] = '.';

        return $this->runProcess($command, $output);
    }

    /**
     * Reads the "typo3scan-ignore" composer.json extra setting (see AbstractT3Command::buildtoolsExtra()),
     * a list of issue numbers to ignore.
     */
    private function configuredIgnore(): ?string
    {
        $ignore = $this->buildtoolsExtra()['typo3scan-ignore'] ?? null;

        return is_array($ignore) && $ignore !== [] ? implode(',', $ignore) : null;
    }
}
