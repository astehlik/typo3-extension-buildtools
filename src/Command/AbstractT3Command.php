<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class AbstractT3Command extends BaseCommand
{
    protected function binPath(string $name): string
    {
        return $this->vendorBinDir() . '/' . $name;
    }

    /**
     * @return list<string>
     */
    protected function detectExtensionFiles(): array
    {
        $files = glob('ext_*.php');

        return $files === false ? [] : $files;
    }

    /**
     * @return list<string>
     */
    protected function detectStandardDirectories(): array
    {
        $directories = [];

        foreach (['Classes', 'Configuration/TCA', 'Tests'] as $directory) {
            if (is_dir($directory)) {
                $directories[] = $directory;
            }
        }

        return $directories;
    }

    /**
     * Absolute path to the root of this buildtools package itself, wherever it
     * has been installed, used to locate the bundled phpunit/*.xml configs.
     */
    protected function packageRoot(): string
    {
        return dirname(__DIR__, 2);
    }

    /**
     * Ports the path- and ruleset-detection logic of bin/t3_check_codestyle.sh: uses the
     * "PSRDefault" ruleset from de-swebhosting/php-codestyle unless the extension ships its
     * own Tests/CodeSniffer ruleset, in which case its name defaults to "PerCodeStyleT3Ext"
     * (the extensions' usual ruleset directory name) but can be overridden via $customRuleset.
     */
    protected function runCodeSniffer(bool $fix, ?string $customRuleset, OutputInterface $output): int
    {
        $installedPaths = $this->vendorDir() . '/de-swebhosting/php-codestyle/PhpCodeSniffer';
        $standard = 'PSRDefault';

        if (is_dir('Tests/CodeSniffer')) {
            $installedPaths .= ',' . getcwd() . '/Tests/CodeSniffer';
            $standard = $customRuleset === null || $customRuleset === '' ? 'PerCodeStyleT3Ext' : $customRuleset;
        }

        $configExitCode = $this->runProcess(
            [$this->binPath('phpcs'), '--config-set', 'installed_paths', $installedPaths],
            $output,
        );

        if ($configExitCode !== self::SUCCESS) {
            return $configExitCode;
        }

        $command = [$this->binPath($fix ? 'phpcbf' : 'phpcs'), '--standard=' . $standard];
        array_push($command, ...$this->detectStandardDirectories(), ...$this->detectExtensionFiles());

        return $this->runProcess($command, $output);
    }

    /**
     * @param list<string> $command
     * @param array<string, string> $env additional environment variables, merged with the inherited environment
     */
    protected function runProcess(array $command, OutputInterface $output, array $env = []): int
    {
        $process = new Process($command, getcwd(), $env === [] ? null : $env);
        $process->setTimeout(null);
        $process->run(static function (string $type, string $buffer) use ($output): void {
            $output->write($buffer);
        });

        return $process->getExitCode() ?? self::FAILURE;
    }

    /**
     * Absolute path to the "bin-dir" configured in the consuming extension's
     * composer.json, e.g. "vendor/bin" or ".Build/bin".
     */
    protected function vendorBinDir(): string
    {
        return rtrim((string)$this->requireComposer()->getConfig()->get('bin-dir'), '/');
    }

    /**
     * Absolute path to the "vendor-dir" configured in the consuming extension's
     * composer.json. Not derived from vendorBinDir(), since extensions may
     * configure "bin-dir" and "vendor-dir" independently (e.g. "bin" / ".Build/vendor").
     */
    protected function vendorDir(): string
    {
        return rtrim((string)$this->requireComposer()->getConfig()->get('vendor-dir'), '/');
    }
}
