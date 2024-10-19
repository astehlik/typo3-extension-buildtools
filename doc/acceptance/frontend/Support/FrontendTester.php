<?php

declare(strict_types=1);

namespace Vendor\MyExt\Tests\Acceptance\Support;

use Codeception\Actor;
use Vendor\MyExt\Tests\Acceptance\Support\_generated\FrontendTesterActions;

class FrontendTester extends Actor
{
    use FrontendTesterActions;
}
