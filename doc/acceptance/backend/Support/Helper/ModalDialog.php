<?php
declare(strict_types=1);

namespace Vendor\MyExt\Tests\Acceptance\Support\Helper;

use Vendor\MyExt\Tests\Acceptance\Support\BackendTester;
use TYPO3\TestingFramework\Core\Acceptance\Helper\AbstractModalDialog;

class ModalDialog extends AbstractModalDialog
{
    public function __construct(BackendTester $I)
    {
        $this->tester = $I;
    }
}
