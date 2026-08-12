<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckComposerNormalizeCommand extends AbstractT3Command
{
    protected function configure(): void
    {
        $this->setName('t3:check:composer:normalize')
            ->setDescription('Checks composer.json normalization using "composer normalize" (dry-run).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->getApplication()->find('normalize')->run(
            new ArrayInput(['--dry-run' => true, '--diff' => true]),
            $output,
        );
    }
}
