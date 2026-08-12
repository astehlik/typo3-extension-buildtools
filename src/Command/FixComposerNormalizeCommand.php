<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FixComposerNormalizeCommand extends AbstractT3Command
{
    protected function configure(): void
    {
        $this->setName('t3:fix:composer:normalize')
            ->setDescription('Normalizes composer.json using "composer normalize".');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->getApplication()->find('normalize')->run(new ArrayInput([]), $output);
    }
}
