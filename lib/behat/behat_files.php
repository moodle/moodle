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
    Behat\Mink\Element\NodeElement as NodeElement;

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
     * Gets the NodeElement for filepicker of filemanager moodleform element.
     *
     * The filepicker/filemanager element label is pointing to a hidden input which is
     * not recognized as a named selector, as it is hidden...
     *
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filepickerelement The filepicker form field label
     * @return NodeElement The hidden element node.
     */
    protected function get_filepicker_node($filepickerelement) {

        // More info about the problem (in case there is a problem).
        $exception = new ExpectationException('"' . $filepickerelement . '" filepicker can not be found', $this->getSession());

        // If no file picker label is mentioned take the first file picker from the page.
        if (empty($filepickerelement)) {
            $filepickercontainer = $this->find(
                'xpath',
                "//*[@class=\"form-filemanager\"]",
                $exception
            );
        } else {
            // Gets the ffilemanager node specified by the locator which contains the filepicker container.
            $filepickerelement = $this->getSession()->getSelectorsHandler()->xpathLiteral($filepickerelement);
            $filepickercontainer = $this->find(
                'xpath',
                "//input[./@id = //label[normalize-space(.)=$filepickerelement]/@for]" .
                    "//ancestor::div[contains(concat(' ', normalize-space(@class), ' '), ' ffilemanager ') or " .
                    "contains(concat(' ', normalize-space(@class), ' '), ' ffilepicker ')]",
                $exception
            );
        }

        return $filepickercontainer;
    }

    /**
     * Performs $action on a filemanager container element (file or folder).
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
        $button = $this->find('css', '.moodle-dialogue-focused button.' . $classname, $exception);

        $this->ensure_node_is_visible($button);
        $button->click();
    }

    /**
     * Opens the contextual menu of a folder or a file.
     *
     * Works both in filemanager elements and when dealing with repository
     * elements inside filepicker modal window.
     *
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $name The name of the folder/file
     * @param string $filemanagerelement The filemanager form element locator, the repository items are in filepicker modal window if false
     * @return void
     */
    protected function open_element_contextual_menu($name, $filemanagerelement = false) {

        // If a filemanager is specified we restrict the search to the descendants of this particular filemanager form element.
        $containernode = false;
        $exceptionmsg = '"'.$name.'" element can not be found';
        if ($filemanagerelement) {
            $containernode = $this->get_filepicker_node($filemanagerelement);
            $exceptionmsg = 'The "'.$filemanagerelement.'" filemanager ' . $exceptionmsg;
            $locatorprefix = "//div[@class='fp-content']";
        } else {
            $locatorprefix = "//div[contains(concat(' ', normalize-space(@class), ' '), ' fp-repo-items ')]//descendant::div[@class='fp-content']";
        }

        $exception = new ExpectationException($exceptionmsg, $this->getSession());

        // Avoid quote-related problems.
        $name = $this->getSession()->getSelectorsHandler()->xpathLiteral($name);

        // Get a filepicker/filemanager element (folder or file).
        try {

            // First we look at the folder as we need to click on the contextual menu otherwise it would be opened.
            $node = $this->find(
                'xpath',
                $locatorprefix .
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
                $locatorprefix .
                "//descendant::*[self::div | self::a][contains(concat(' ', normalize-space(@class), ' '), ' fp-file ')]" .
                "[normalize-space(.)=$name]" .
                "//descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' fp-filename-field ')]",
                false,
                $containernode
            );
        }

        // Click opens the contextual menu when clicking on files.
        $this->ensure_node_is_visible($node);
        $node->click();
    }

    /**
     * Opens the filepicker modal window and selects the repository.
     *
     * @throws ExpectationException Thrown by behat_base::find
     * @param NodeElement $filemanagernode The filemanager or filepicker form element DOM node.
     * @param mixed $repositoryname The repo name.
     * @return void
     */
    protected function open_add_file_window($filemanagernode, $repositoryname) {

        $exception = new ExpectationException('No files can be added to the specified filemanager', $this->getSession());

        // We should deal with single-file and multiple-file filemanagers,
        // catching the exception thrown by behat_base::find() in case is not multiple
        try {
            // Looking for the add button inside the specified filemanager.
            $add = $this->find('css', 'div.fp-btn-add a', $exception, $filemanagernode);
        } catch (Exception $e) {
            // Otherwise should be a single-file filepicker form element.
            $add = $this->find('css', 'input.fp-btn-choose', $exception, $filemanagernode);
        }
        $this->ensure_node_is_visible($add);
        $add->click();

        // Wait for the default repository (if any) to load. This checks that
        // the relevant div exists and that it does not include the loading image.
        $this->ensure_element_exists(
                "//div[contains(concat(' ', normalize-space(@class), ' '), ' file-picker ')]" .
                "//div[contains(concat(' ', normalize-space(@class), ' '), ' fp-content ')]" .
                "[not(descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' fp-content-loading ')])]",
                'xpath_element');

        // Getting the repository link and opening it.
        $repoexception = new ExpectationException('The "' . $repositoryname . '" repository has not been found', $this->getSession());

        // Avoid problems with both double and single quotes in the same string.
        $repositoryname = $this->getSession()->getSelectorsHandler()->xpathLiteral($repositoryname);

        // Here we don't need to look inside the selected element because there can only be one modal window.
        $repositorylink = $this->find(
            'xpath',
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' fp-repo-area ')]" .
                "//descendant::span[contains(concat(' ', normalize-space(@class), ' '), ' fp-repo-name ')]" .
                "[normalize-space(.)=$repositoryname]",
            $repoexception
        );

        // Selecting the repo.
        $this->ensure_node_is_visible($repositorylink);
        if (!$repositorylink->getParent()->getParent()->hasClass('active')) {
            // If the repository link is active, then the repository is already loaded.
            // Clicking it while it's active causes issues, so only click it when it isn't (see MDL-51014).
            $repositorylink->click();
        }
    }

    /**
     * Waits until the file manager modal windows are closed.
     *
     * This method is not used by any of our step definitions,
     * keeping it here for users already using it.
     *
     * @throws ExpectationException
     * @return void
     */
    protected function wait_until_return_to_form() {

        $exception = new ExpectationException('The file manager is taking too much time to finish the current action', $this->getSession());

         $this->find(
             'xpath',
             "//div[contains(concat(' ', @class, ' '), ' moodle-dialogue-lightbox ')][contains(@style, 'display: none;')]",
             $exception
         );
    }

    /**
     * Checks that the file manager contents are not being updated.
     *
     * This method is not used by any of our step definitions,
     * keeping it here for users already using it.
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
    }

}
