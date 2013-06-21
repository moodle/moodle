<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Files interactions with behat.
 *
 * Note that steps definitions files can not extend other steps definitions files, so
 * steps definitions which makes use of file attachments or filepicker should
 * extend behat_files instead of behat_base.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/behat_base.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Files-related actions.
 *
 * Steps definitions related with filepicker or repositories should extend
 * this class instead of behat_base as it provides useful methods to deal
 * with the common filepicker issues.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_files extends behat_base {

    /**
     * Gets the filepicker NodeElement.
     *
     * The filepicker field label is pointing to a hidden input which is
     * not recognized as a named selector, as it is hidden...
     *
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filepickerelement The filepicker form field label
     * @return NodeElement The hidden element node.
     */
    protected function get_filepicker_node($filepickerelement) {

        // More info about the problem (in case there is a problem).
        $exception = new ExpectationException('"' . $filepickerelement . '" filepicker can not be found', $this->getSession());

        // Gets the ffilemanager node specified by the locator which contains the filepicker container.
        $filepickerelement = $this->getSession()->getSelectorsHandler()->xpathLiteral($filepickerelement);
        $filepickercontainer = $this->find(
            'xpath',
            "//input[./@id = //label[normalize-space(.)=$filepickerelement]/@for]" .
                "//ancestor::div[contains(concat(' ', normalize-space(@class), ' '), ' ffilemanager ') or " .
                "contains(concat(' ', normalize-space(@class), ' '), ' ffilepicker ')]",
            $exception
        );

        return $filepickercontainer;
    }

    /**
     * Performs $action on a filepicker container element (file or folder).
     *
     * It works together with open_element_contextual_menu
     * as this method needs the contextual menu to be opened.
     *
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $action
     * @param ExpectationException $exception
     * @return void
     */
    protected function perform_on_element($action, ExpectationException $exception) {

        // Finds the button inside the DOM, is a modal window, so should be unique.
        $classname = 'fp-file-' . $action;
        $button = $this->find('css', '.yui3-panel-focused button.' . $classname, $exception);

        $button->click();
    }

    /**
     * Opens the contextual menu of a folder or a file.
     *
     * Works both in filepicker elements and when dealing with repository
     * elements inside modal windows.
     *
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $name The name of the folder/file
     * @param string $filepickerelement The filepicker locator, the whole DOM if false
     * @return void
     */
    protected function open_element_contextual_menu($name, $filepickerelement = false) {

        // If a filepicker is specified we restrict the search to the filepicker descendants.
        $containernode = false;
        $exceptionmsg = '"'.$name.'" element can not be found';
        if ($filepickerelement) {
            $containernode = $this->get_filepicker_node($filepickerelement);
            $exceptionmsg = 'The "'.$filepickerelement.'" filepicker ' . $exceptionmsg;
        }

        $exception = new ExpectationException($exceptionmsg, $this->getSession());

        // Avoid quote-related problems.
        $name = $this->getSession()->getSelectorsHandler()->xpathLiteral($name);

        // Get a filepicker element (folder or file).
        try {

            // First we look at the folder as we need to click on the contextual menu otherwise it would be opened.
            $node = $this->find(
                'xpath',
                "//div[@class='fp-content']" .
                    "//descendant::*[self::div | self::a][contains(concat(' ', normalize-space(@class), ' '), ' fp-file ')]" .
                    "[contains(concat(' ', normalize-space(@class), ' '), ' fp-folder ')]" .
                    "[normalize-space(.)=$name]" .
                    "//descendant::a[contains(concat(' ', normalize-space(@class), ' '), ' fp-contextmenu ')]",
                $exception,
                $containernode
            );

        } catch (ExpectationException $e) {

            // Here the contextual menu is hidden, we click on the thumbnail.
            $node = $this->find(
                'xpath',
                "//div[@class='fp-content']" .
                "//descendant::*[self::div | self::a][contains(concat(' ', normalize-space(@class), ' '), ' fp-file ')]" .
                "[normalize-space(.)=$name]" .
                "//descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' fp-thumbnail ')]",
                false,
                $containernode
            );
        }

        // Click opens the contextual menu when clicking on files.
        $node->click();
    }

    /**
     * Opens the 'add file' modal window and selects the repository.
     *
     * @throws ExpectationException Thrown by behat_base::find
     * @param NodeElement $filepickernode The filepicker DOM node.
     * @param mixed $repositoryname The repo name.
     * @return void
     */
    protected function open_add_file_window($filepickernode, $repositoryname) {

        $exception = new ExpectationException('No files can be added to the specified filepicker', $this->getSession());

        // We should deal with single-file and multiple-file filepickers,
        // catching the exception thrown by behat_base::find() in case is not multiple
        try {
            // Looking for the add button inside the specified filepicker.
            $add = $this->find('css', 'div.fp-btn-add a', $exception, $filepickernode);
        } catch (Exception $e) {
            // Otherwise should be a single-file filepicker.
            $add = $this->find('css', 'input.fp-btn-choose', $exception, $filepickernode);
        }
        $add->click();

        // Getting the repository link and opening it.
        $repoexception = new ExpectationException('The "' . $repositoryname . '" repository has not been found', $this->getSession());

        // Avoid problems with both double and single quotes in the same string.
        $repositoryname = $this->getSession()->getSelectorsHandler()->xpathLiteral($repositoryname);

        // Here we don't need to look inside the selected filepicker because there can only be one modal window.
        $repositorylink = $this->find(
            'xpath',
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' fp-repo-area ')]" .
                "//descendant::span[contains(concat(' ', normalize-space(@class), ' '), ' fp-repo-name ')]" .
                "[normalize-space(.)=$repositoryname]",
            $repoexception
        );

        // Selecting the repo.
        $repositorylink->click();
    }

    /**
     * Waits until the file manager modal windows are closed.
     *
     * @throws ExpectationException
     * @return void
     */
    protected function wait_until_return_to_form() {

        $exception = new ExpectationException('The file manager is taking too much time to finish the current action', $this->getSession());

        $this->find(
            'xpath',
            "//div[@id='filesskin']" .
                "/descendant::div[contains(concat(' ', @class, ' '), ' yui3-widget-mask ')]" .
                "[contains(concat(' ', @style, ' '), ' display: none; ')]",
            $exception
        );
    }

    /**
     * Checks that the file manager contents are not being updated.
     *
     * @throws ExpectationException
     * @param NodeElement $filepickernode The file manager DOM node
     * @return void
     */
    protected function wait_until_contents_are_updated($filepickernode) {

        $exception = new ExpectationException(
            'The file manager contents are requiring too much time to be updated',
            $this->getSession()
        );

        // Looks for the loading image not being displayed. For single-file filepickers is
        // only used when accessing the filepicker, there is no filemanager-loading after selecting the file.
        $this->find(
            'xpath',
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' filemanager ')]" .
                "[not(contains(concat(' ', normalize-space(@class), ' '), ' fm-updating '))]" .
            "|" .
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' filemanager-loading ')]" .
                "[contains(@style, 'display: none;')]",
            $exception,
            $filepickernode
        );

        // After removing the class FileManagerHelper.view_files() performs other actions.
        $this->getSession()->wait(4 * 1000, false);
    }

}
