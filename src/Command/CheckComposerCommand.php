<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\ArrayInput;
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
        foreach (self::STEPS as $step) {
            $output->writeln(sprintf('<info>Running %s</info>', $step));

            $exitCode = $this->getApplication()->find($step)->run(new ArrayInput([]), $output);
            if ($exitCode !== self::SUCCESS) {
                return $exitCode;
            }
        }

        return self::SUCCESS;
    }
}
