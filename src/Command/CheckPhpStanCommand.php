<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckPhpStanCommand extends AbstractT3Command
{
    protected function configure(): void
    {
        $this->setName('t3:check:php:stan')
            ->setDescription('Runs static analysis using PHPStan.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configPath = is_file('Build/phpstan/phpstan.local.neon')
            ? 'Build/phpstan/phpstan.local.neon'
            : 'Build/phpstan/phpstan.ci.neon';

        if (!is_file($configPath)) {
            $output->writeln(
                '<error>No Build/phpstan/phpstan.local.neon or Build/phpstan/phpstan.ci.neon found.</error>',
            );

            return self::INVALID;
        }

        return $this->runProcess(
            [$this->binPath('phpstan'), 'analyse', '-c', $configPath],
            $output,
            ['XDEBUG_MODE' => 'off'],
        );
    }
}
