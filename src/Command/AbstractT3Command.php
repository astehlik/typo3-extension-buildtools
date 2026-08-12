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
     * Buildtools-specific settings from the consuming extension's composer.json, e.g.:
     *
     *   "extra": {
     *     "typo3-extension-buildtools": {
     *       "phpcs-ruleset": "MyCodingStandard",
     *       "typo3scan-ignore": ["1234567890", "1234567891"]
     *     }
     *   }
     *
     * @return array<string, mixed>
     */
    protected function buildtoolsExtra(): array
    {
        $extra = $this->requireComposer()->getPackage()->getExtra()['typo3-extension-buildtools'] ?? [];

        return is_array($extra) ? $extra : [];
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
     * or the "phpcs-ruleset" composer.json extra setting (see buildtoolsExtra()) when given.
     */
    protected function runCodeSniffer(bool $fix, OutputInterface $output): int
    {
        $installedPaths = $this->vendorDir() . '/de-swebhosting/php-codestyle/PhpCodeSniffer';
        $standard = 'PSRDefault';

        if (is_dir('Tests/CodeSniffer')) {
            $installedPaths .= ',' . getcwd() . '/Tests/CodeSniffer';

            $configuredRuleset = $this->buildtoolsExtra()['phpcs-ruleset'] ?? null;
            $customRuleset = is_string($configuredRuleset) && $configuredRuleset !== '' ? $configuredRuleset : null;

            $standard = $customRuleset ?? 'PerCodeStyleT3Ext';
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

        $exitCode = $this->runProcess($command, $output);

        // phpcbf exits 1 to signal that it successfully applied fixes (0 = nothing to fix,
        // 2 = fixing failed for some files, 3 = general script execution failure) - that's a
        // success for the fix command, not a failure, and must not abort t3:fix(:php) chains.
        if ($fix && $exitCode === 1) {
            return self::SUCCESS;
        }

        return $exitCode;
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
