<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

final class CheckPhpLintCommand extends AbstractT3Command
{
    protected function configure(): void
    {
        $this->setName('t3:check:php:lint')
            ->setDescription('Lints all PHP files for syntax errors using "php -l".');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $files = $this->detectExtensionFiles();
        $directories = $this->detectStandardDirectories();

        if ($directories !== []) {
            $finder = (new Finder())->files()->name('*.php')->in($directories);

            foreach ($finder as $file) {
                $files[] = $file->getPathname();
            }
        }

        if ($files === []) {
            $output->writeln('<comment>No PHP files found to lint.</comment>');

            return self::SUCCESS;
        }

        $exitCode = self::SUCCESS;

        foreach ($files as $file) {
            $process = new Process(['php', '-l', $file]);
            $process->run();

            if (!$process->isSuccessful()) {
                $output->write($process->getOutput() . $process->getErrorOutput());
                $exitCode = self::FAILURE;
            }
        }

        return $exitCode;
    }
}
