<?php
declare(strict_types=1);

namespace Vendor\MyExt\Tests\Acceptance\Support\Extension;

use TYPO3\TestingFramework\Core\Acceptance\Extension\BackendEnvironment as TYPO3BackendEnvironment;

class BackendEnvironment extends TYPO3BackendEnvironment
{
    /**
     * Load a list of core extensions and styleguide
     *
     * @var array
     */
    protected $localConfig = [
        'coreExtensionsToLoad' => [
            'core',
            'extbase',
            'fluid',
            'backend',
            'about',
            'filelist',
            'install',
            'frontend',
            'recordlist',
            'fluid_styled_content',
        ],
        'testExtensionsToLoad' => ['typo3conf/ext/my_ext'],
        'xmlDatabaseFixtures' => [
            'PACKAGE:typo3/testing-framework/Resources/Core/Acceptance/Fixtures/be_users.xml',
            'PACKAGE:typo3/testing-framework/Resources/Core/Acceptance/Fixtures/be_sessions.xml',
            'PACKAGE:typo3/testing-framework/Resources/Core/Acceptance/Fixtures/be_groups.xml',
            'EXT:my_ext/Tests/Acceptance/Fixtures/page.xml',
        ],
    ];
}
