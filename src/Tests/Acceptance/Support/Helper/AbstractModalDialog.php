<?php

declare(strict_types=1);

namespace De\SWebhosting\Buildtools\Tests\Acceptance\Support\Helper;

use Codeception\Actor;
use Codeception\Module\WebDriver;

class AbstractModalDialog extends \TYPO3\TestingFramework\Core\Acceptance\Helper\AbstractModalDialog
{
    /**
     * Selector for the container in the modal where the buttons are located
     * Adapted for Boostrap 5.
     *
     * @var string
     */
    public static $openedModalButtonContainerSelector = '.t3js-modal[open] .modal-footer';

    /**
     * Selector for a visible modal window
     * Adapted for Boostrap 5.
     *
     * @var string
     */
    public static $openedModalSelector = '.t3js-modal[open]';

    /**
     * @var Actor
     */
    protected $tester;

    /**
     * Check if modal dialog is visible in top frame.
     */
    public function canSeeDialog(): void
    {
        $I = $this->tester;
        $I->switchToIFrame();
        $I->waitForElement(self::$openedModalSelector);
        // I will wait two seconds to prevent failing tests
        $I->wait(2);
    }

    /**
     * Perform a click on a link or a button, given by a locator.
     *
     * @param string $buttonLinkLocator the button title
     *
     * @see WebDriver::click()
     */
    public function clickButtonInDialog(string $buttonLinkLocator): void
    {
        $I = $this->tester;
        $this->canSeeDialog();
        $I->click($buttonLinkLocator, self::$openedModalButtonContainerSelector);
        $I->waitForElementNotVisible(self::$openedModalSelector);
    }
}
