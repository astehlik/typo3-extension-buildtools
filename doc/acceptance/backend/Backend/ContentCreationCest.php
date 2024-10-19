<?php

declare(strict_types=1);

namespace Vendor\MyExt\Tests\Acceptance\Backend;

use De\SWebhosting\Buildtools\Tests\Acceptance\Helper\Backend\ModalDialog;
use De\SWebhosting\Buildtools\Tests\Acceptance\Helper\Backend\PageTree;
use Vendor\MyExt\Tests\Acceptance\Support\BackendTester;

class ContentCreationCest
{
    public function _before(BackendTester $I): void
    {
        $I->useExistingSession('admin');
    }

    public function contentCanBeCreated(BackendTester $I, PageTree $pageTree, ModalDialog $modalDialog): void
    {
        $I->click('Page');
        $pageTree->openPath(['root Page']);

        $I->wait(0.2);
        $I->switchToContentFrame();

        $I->click('Create new content element');

        $modalDialog->canSeeDialog();

        $I->click('Header Only');

        $I->switchToContentFrame();

        $headerInputSelector = 'input[data-formengine-input-name$="[header]"]';
        $I->waitForElement($headerInputSelector);
        $I->fillField($headerInputSelector, 'Testheader');

        $I->click('Save');

        $I->waitForElement($headerInputSelector);

        $I->dontSeeInSource('alert-danger');

        $I->seeInField($headerInputSelector, 'Testheader');
    }
}
