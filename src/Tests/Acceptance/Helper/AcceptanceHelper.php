<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Tests\Acceptance\Helper;

class AcceptanceHelper
{
    public static function getExtensionsForMinimalUsableSystem(): array
    {
        return [
            'backend',
            'core',
            'extbase',
            'filelist',
            'fluid',
            'frontend',
            'install',
        ];
    }
}
