<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FixPhpCglCommand extends AbstractT3Command
{
    protected function configure(): void
    {
        $this->setName('t3:fix:php:cgl')
            ->setDescription('Fixes the PHP coding guidelines using PHP-CS-Fixer.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->runProcess(
            [
                $this->binPath('php-cs-fixer'),
                'fix',
                '-v',
                '--config=Build/php-cs-fixer/config.php',
            ],
            $output,
            ['XDEBUG_MODE' => 'off'],
        );
    }
}
