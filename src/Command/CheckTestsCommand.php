<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckTestsCommand extends AbstractT3Command
{
    private const STEPS = [
        't3:check:tests:unit',
        't3:check:tests:functional',
    ];

    protected function configure(): void
    {
        $this->setName('t3:check:tests')
            ->setDescription('Runs all t3:check:tests:* commands (unit, functional).');
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
