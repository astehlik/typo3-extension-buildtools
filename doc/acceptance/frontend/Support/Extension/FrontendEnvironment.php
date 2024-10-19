<?php

declare(strict_types=1);

namespace Vendor\MyExt\Tests\Acceptance\Support\Extension;

use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\TestingFramework\Core\Acceptance\Extension\BackendEnvironment;

class FrontendEnvironment extends BackendEnvironment
{
    public const SITE_CONFIG_PATH = 'typo3conf/ext/my_ext/Tests/Acceptance/Fixtures/sites';

    /**
     * Load a list of core extensions and styleguide.
     *
     * @var array
     */
    protected $localConfig = [
        'pathsToLinkInTestInstance' => [self::SITE_CONFIG_PATH => 'typo3conf/sites'],
        'configurationToUseInTestInstance' => [
            'FE' => ['debug' => true],
            'SYS' => [
                'devIPmask' => '*',
                'displayErrors' => 1,
                'systemLogLevel' => 0,
                // E_WARNING | E_RECOVERABLE_ERROR | E_DEPRECATED
                'exceptionalErrors' => 12290,

                'caching' => [
                    'cacheConfigurations' => [
                        'cache_hash' => [
                            'backend' => NullBackend::class,
                        ],
                        'cache_pages' => [
                            'backend' => NullBackend::class,
                        ],
                        'cache_pagesection' => [
                            'backend' => NullBackend::class,
                        ],
                        'cache_rootline' => [
                            'backend' => NullBackend::class,
                        ],
                        'cache_imagesizes' => [
                            'backend' => NullBackend::class,
                        ],
                        'assets' => [
                            'backend' => NullBackend::class,
                        ],
                        'l10n' => [
                            'backend' => NullBackend::class,
                        ],
                        'fluid_template' => [
                            'backend' => NullBackend::class,
                        ],
                        'extbase_reflection' => [
                            'backend' => NullBackend::class,
                        ],
                        'extbase_datamapfactory_datamap' => [
                            'backend' => NullBackend::class,
                        ],
                    ],
                ],
            ],
        ],
        'coreExtensionsToLoad' => [
            'core',
            'extbase',
            'fluid',
            'frontend',
            'fluid_styled_content',
        ],
        'testExtensionsToLoad' => ['typo3conf/ext/my_ext'],
        'xmlDatabaseFixtures' => ['EXT:my_ext/Tests/Acceptance/Fixtures/Database/page.xml'],
    ];
}
