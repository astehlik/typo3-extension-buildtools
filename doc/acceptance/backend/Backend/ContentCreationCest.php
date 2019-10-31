<?php
declare(strict_types=1);

namespace Vendor\MyExt\Tests\Acceptance\Backend;

use Vendor\MyExt\Tests\Acceptance\Support\BackendTester;
use Vendor\MyExt\Tests\Acceptance\Support\Helper\ModalDialog;
use Vendor\MyExt\Tests\Acceptance\Support\Helper\PageTree;

class ContentCreationCest
{
    /**
     * @param BackendTester $I
     */
    public function _before(BackendTester $I)
    {
        $I->useExistingSession('admin');
    }

    public function contentCanBeCreated(BackendTester $I, PageTree $pageTree, ModalDialog $modalDialog)
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
