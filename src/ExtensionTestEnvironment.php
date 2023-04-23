<?php
declare(strict_types=1);

namespace De\SWebhosting\Buildtools;

use Composer\Script\Event;
use TYPO3\TestingFramework\Composer\ExtensionTestEnvironment as TYPO3ExtensionTestEnvironment;

/**
 * This hook creates a vendor symlink in the Web folder because this is where
 * the testing framework is looking for an autoload.php file.
 */
class ExtensionTestEnvironment
{
    public static function prepare(Event $event)
    {
        // We are located at .Build/vendor/de-swebhosting/buildtools/src
        $rootDirectory = realpath(__DIR__ . '/../../../../../');

        $vendorDir = $rootDirectory . DIRECTORY_SEPARATOR . '.Build' . DIRECTORY_SEPARATOR . 'vendor';

        $webDir = $rootDirectory . DIRECTORY_SEPARATOR . '.Build' . DIRECTORY_SEPARATOR . 'Web';
        $webVendorSymlink = $webDir . DIRECTORY_SEPARATOR . 'vendor';

        if (!is_link($webVendorSymlink)) {
            symlink($vendorDir, $webVendorSymlink);
        }
    }
}
