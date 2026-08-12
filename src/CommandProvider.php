<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use De\SWebhosting\Buildtools\Command\CheckCommand;
use De\SWebhosting\Buildtools\Command\CheckComposerCommand;
use De\SWebhosting\Buildtools\Command\CheckComposerNormalizeCommand;
use De\SWebhosting\Buildtools\Command\CheckComposerValidateCommand;
use De\SWebhosting\Buildtools\Command\CheckPhpCglCommand;
use De\SWebhosting\Buildtools\Command\CheckPhpCommand;
use De\SWebhosting\Buildtools\Command\CheckPhpCsCommand;
use De\SWebhosting\Buildtools\Command\CheckPhpLintCommand;
use De\SWebhosting\Buildtools\Command\CheckPhpStanCommand;
use De\SWebhosting\Buildtools\Command\CheckTestsCommand;
use De\SWebhosting\Buildtools\Command\CheckTestsFunctionalCommand;
use De\SWebhosting\Buildtools\Command\CheckTestsUnitCommand;
use De\SWebhosting\Buildtools\Command\CheckTypo3ScanCommand;
use De\SWebhosting\Buildtools\Command\FixCommand;
use De\SWebhosting\Buildtools\Command\FixComposerNormalizeCommand;
use De\SWebhosting\Buildtools\Command\FixPhpCglCommand;
use De\SWebhosting\Buildtools\Command\FixPhpCommand;
use De\SWebhosting\Buildtools\Command\FixPhpCsCommand;

final class CommandProvider implements CommandProviderCapability
{
    public function getCommands(): array
    {
        return [
            new CheckComposerValidateCommand(),
            new CheckComposerNormalizeCommand(),
            new CheckComposerCommand(),
            new FixComposerNormalizeCommand(),
            new CheckPhpLintCommand(),
            new CheckPhpCsCommand(),
            new FixPhpCsCommand(),
            new CheckPhpCglCommand(),
            new FixPhpCglCommand(),
            new CheckPhpStanCommand(),
            new CheckPhpCommand(),
            new FixPhpCommand(),
            new CheckTypo3ScanCommand(),
            new CheckTestsUnitCommand(),
            new CheckTestsFunctionalCommand(),
            new CheckTestsCommand(),
            new CheckCommand(),
            new FixCommand(),
        ];
    }
}
