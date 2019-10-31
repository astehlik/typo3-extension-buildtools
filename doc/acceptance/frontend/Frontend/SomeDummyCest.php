<?php
declare(strict_types=1);

namespace Vendor\MyExt\Tests\Acceptance\Frontend;

use Vendor\MyExt\Tests\Acceptance\Support\FrontendTester;

class SomeDummyCest
{
    public function actionDay(FrontendTester $I)
    {
        $I->amOnPage('/');
        $I->canSee('Hallo acceptance world!');
    }
}
