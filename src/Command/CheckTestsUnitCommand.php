<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckTestsUnitCommand extends AbstractT3Command
{
    protected function configure(): void
    {
        $this->setName('t3:check:tests:unit')
            ->setDescription('Runs the PHPUnit unit test suite.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->runProcess(
            [$this->binPath('phpunit'), '-c', $this->packageRoot() . '/phpunit/UnitTests.xml'],
            $output,
        );
    }
}
