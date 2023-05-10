<?php
declare(strict_types=1);

namespace De\SWebhosting\Buildtools;

use Composer\Script\Event;

/**
 * This hook creates a vendor symlink in the Web folder because this is where
 * the testing framework is looking for an autoload.php file.
 */
class ExtensionTestEnvironment
{
    public static function prepare(Event $event): void
    {
        $extensionKey = $event->getComposer()->getPackage()->getExtra()['typo3/cms']['extension-key'] ?? '';

        if ($extensionKey === '') {
            throw new \RuntimeException(
                'Could not read Extension key from composer.json.'
                . ' Please add "typo3/cms.extension-key" in the extras section of your composer.json.'
            );
        }

        // We are located at .Build/vendor/de-swebhosting/buildtools/src
        $rootDirectory = realpath(__DIR__ . '/../../../../../');

        $vendorDir = $rootDirectory . DIRECTORY_SEPARATOR . '.Build' . DIRECTORY_SEPARATOR . 'vendor';

        $webDir = $rootDirectory . DIRECTORY_SEPARATOR . '.Build' . DIRECTORY_SEPARATOR . 'Web';

        $extDir = $webDir . DIRECTORY_SEPARATOR . 'typo3conf' . DIRECTORY_SEPARATOR . 'ext';

        $sysextDir = $webDir . DIRECTORY_SEPARATOR . 'typo3' . DIRECTORY_SEPARATOR . 'sysext';

        if (!is_dir($extDir)) {
            mkdir($extDir, 0755, true);
        }

        $extensionSymlink = $extDir . DIRECTORY_SEPARATOR . $extensionKey;
        if (!file_exists($extensionSymlink)) {
            symlink($rootDirectory, $extensionSymlink);
        }

        if (!is_dir($sysextDir)) {
            mkdir($sysextDir, 0755, true);
        }

        $packageArtifactPath = $vendorDir . DIRECTORY_SEPARATOR . 'typo3' . DIRECTORY_SEPARATOR . 'PackageArtifact.php';

        if (!file_exists($packageArtifactPath)) {
            $event->getIO()->writeError('PackageArtifact.php not found. Please run "composer install" first.');
            return;
        }

        $packages = require $packageArtifactPath;

        foreach ($packages['composerNameToPackageKeyMap'] as $composerName => $extensionName) {
            if (str_starts_with($composerName, 'typo3/cms-')) {
                $sysextSymlink = $sysextDir . DIRECTORY_SEPARATOR . $extensionName;

                if (!is_link($sysextSymlink)) {
                    symlink(
                        $vendorDir . DIRECTORY_SEPARATOR . 'typo3' . DIRECTORY_SEPARATOR . 'cms-' . $extensionName,
                        $sysextSymlink
                    );
                }
                continue;
            }
        }
    }
}
