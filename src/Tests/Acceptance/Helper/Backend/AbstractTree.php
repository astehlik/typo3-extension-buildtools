<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace De\SWebhosting\Buildtools\Tests\Acceptance\Helper\Backend;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\Exception\ElementNotInteractableException;
use Facebook\WebDriver\Exception\ElementNotVisibleException;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Sto\Mediaoembed\Tests\Acceptance\Support\BackendTester;

/**
 * Helper class to interact with the page tree.
 */
abstract class AbstractTree
{
    public static $treeItemAnchorSelector = '.node-contentlabel';

    public static $treeItemSelector = '.nodes-list > [role="treeitem"]';

    // Selectors
    public static $treeSelector = '';

    /**
     * @var BackendTester
     */
    protected $tester;

    /**
     * Check if the pagetree is visible end return the web element object.
     *
     * @return RemoteWebElement
     */
    public function getPageTreeElement()
    {
        $I = $this->tester;
        $I->switchToIFrame();
        return $I->executeInSelenium(static function (RemoteWebDriver $webdriver) {
            return $webdriver->findElement(WebDriverBy::cssSelector(static::$treeSelector));
        });
    }

    /**
     * Open the given hierarchical path in the pagetree and click the last page.
     *
     * Example to open "styleguide -> elements basic" page:
     * [
     *    'styleguide TCA demo',
     *    'elements basic',
     * ]
     *
     * @param string[] $path
     */
    public function openPath(array $path): void
    {
        $context = $this->getPageTreeElement();

        $this->waitForNodes();

        // Collapse all opened paths (might be opened due to localstorage)
        do {
            $toggled = false;

            try {
                // Collapse last opened node element, that is not the root (=first node)
                $context->findElement(
                    WebDriverBy::xpath(
                        '(.//*[position()>1 and @role="treeitem" and'
                        . ' */typo3-backend-icon/@identifier="actions-chevron-down"])[last()]/*[@class="node-toggle"]',
                    ),
                )->click();
                $toggled = true;
            } catch (NoSuchElementException $e) {
                // Element not found so it may be already opened...
            } catch (ElementNotVisibleException $e) {
                // Element not found so it may be already opened...
            } catch (ElementNotInteractableException $e) {
                // Another possible exception if the chevron isn't there ... depends on facebook driver version
            }
        } while ($toggled);

        foreach ($path as $pageName) {
            $context = $this->ensureTreeNodeIsOpen($pageName, $context);
        }
        $context->findElement(WebDriverBy::cssSelector(static::$treeItemAnchorSelector))->click();
    }

    /**
     * Waits until tree nodes are rendered.
     */
    public function waitForNodes(): void
    {
        $this->tester->waitForElement(static::$treeSelector . ' ' . static::$treeItemSelector, 5);
    }

    /**
     * Search for an element with the given link text in the provided context.
     *
     * @return RemoteWebElement
     */
    protected function ensureTreeNodeIsOpen(string $nodeText, RemoteWebElement $context)
    {
        $I = $this->tester;
        $I->wait(0.1);
        $I->see($nodeText, static::$treeItemSelector);

        /** @var RemoteWebElement $context */
        $context = $I->executeInSelenium(static function () use (
            $nodeText,
            $context
        ) {
            return $context->findElement(
                WebDriverBy::xpath('//*[@class=\'node-name\'][text()=\'' . $nodeText . '\']/../../..'),
            );
        });

        try {
            $context->findElement(
                WebDriverBy::cssSelector(
                    '.node-toggle > typo3-backend-icon[identifier=\'actions-chevron-right\']',
                ),
            )->click();
        } catch (NoSuchElementException $e) {
            // Element not found so it may be already opened...
        } catch (ElementNotVisibleException $e) {
            // Element not found so it may be already opened...
        } catch (ElementNotInteractableException $e) {
            // Another possible exception if the chevron isn't there ... depends on facebook driver version
        }

        return $context;
    }
}
