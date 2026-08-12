<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

final class CheckTypo3ScanCommand extends AbstractT3Command
{
    protected function configure(): void
    {
        $this->setName('t3:check:typo3:scan')
            ->setDescription('Scans the extension for deprecated and breaking TYPO3 code using typo3scan.')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Remove the "var" directory without asking for confirmation first',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->removeVarDirectory($input, $output)) {
            return self::FAILURE;
        }

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

    /**
     * typo3scan has no option to exclude directories from a scan, so the "var" directory
     * (runtime cache/logs, always regeneratable) is removed beforehand to avoid false
     * positives. This deletes local files, so it asks for confirmation unless --force is
     * given; in a non-interactive terminal (e.g. CI) --force must be passed explicitly.
     */
    private function removeVarDirectory(InputInterface $input, OutputInterface $output): bool
    {
        if (!is_dir('var')) {
            return true;
        }

        if (!$input->getOption('force')) {
            if (!$this->getIO()->isInteractive()) {
                $output->writeln(
                    '<error>Refusing to remove the "var" directory in a non-interactive terminal without --force.</error>',
                );

                return false;
            }

            if (
                !$this->getIO()->askConfirmation(
                    '<question>Remove the "var" directory before scanning (cannot be undone)? [y/N]</question> ',
                    false,
                )
            ) {
                $output->writeln(
                    '<comment>Aborted: scan needs "var" removed first, pass --force to skip this prompt.</comment>',
                );

                return false;
            }
        }

        (new Filesystem())->remove('var');

        return true;
    }
}
