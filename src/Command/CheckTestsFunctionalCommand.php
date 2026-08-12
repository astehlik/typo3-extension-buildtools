<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckTestsFunctionalCommand extends AbstractT3Command
{
    /**
     * Matches ddev's default db service (host "db", database "db", root/root
     * credentials to avoid grant/permission issues when tests create their own
     * schemas). Only applied when the extension hasn't already set its own
     * values, so a project-specific .ddev config or CI environment always
     * takes precedence.
     */
    private const DEFAULT_DATABASE_ENV = [
        'typo3DatabaseDriver' => 'mysqli',
        'typo3DatabaseHost' => 'db',
        'typo3DatabaseName' => 'db',
        'typo3DatabaseUsername' => 'root',
        'typo3DatabasePassword' => 'root',
    ];

    protected function configure(): void
    {
        $this->setName('t3:check:tests:functional')
            ->setDescription("Runs the PHPUnit functional test suite against ddev's database service.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $env = [];
        foreach (self::DEFAULT_DATABASE_ENV as $name => $value) {
            if (getenv($name) === false) {
                $env[$name] = $value;
            }
        }

        return $this->runProcess(
            [$this->binPath('phpunit'), '-c', $this->packageRoot() . '/phpunit/FunctionalTests.xml'],
            $output,
            $env,
        );
    }
}
