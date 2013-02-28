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

use Behat\Mink\Exception\ExpectationException as ExpectationException;

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
     * @param string $filepickerelement
     * @return NodeElement The hidden element node.
     */
    protected function get_filepicker_node($filepickerelement) {

        // More info about the problem (in case there is a problem).
        $exception = new ExpectationException('"' . $filepickerelement . '" filepicker can not be found', $this->getSession());

        // Gets the ffilemanager node specified by the locator which contains the filepicker container.
        $filepickercontainer = $this->find(
            'xpath',
            "//input[./@id = //label[contains(normalize-space(string(.)), '" . $filepickerelement . "')]/@for]/ancestor::div[contains(concat(' ', normalize-space(@class), ' '), ' ffilemanager ') or contains(concat(' ', normalize-space(@class), ' '), ' ffilepicker ')]",
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
        $button = $this->find('css', 'button.' . $classname, $exception);

        $button->click();
    }

    /**
     * Opens the contextual menu of a folder or a file.
     *
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $name The name of the folder/file
     * @param string $filepickerelement The filepicker locator, usually the form element label
     * @return void
     */
    protected function open_element_contextual_menu($name, $filepickerelement) {

        $filepickernode = $this->get_filepicker_node($filepickerelement);

        $exception = new ExpectationException('The "'.$filepickerelement.'" filepicker "'.$name.'" element can not be found', $this->getSession());

        // Get a filepicker element (folder or file).
        try {

            // First we look at the folder as we need the contextual menu otherwise it would be opened.
            $node = $this->find(
                'xpath',
                "//div[@class='fp-content']
//descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' fp-file ')]
//descendant::a[contains(concat(' ', normalize-space(@class), ' '), ' fp-contextmenu ')]
[contains(concat(' ', normalize-space(.), ' '), '" . $name . "')]",
                $exception,
                $filepickernode
            );

        } catch (Exception $e) {

            $node = $this->find(
                'xpath',
                "//div[@class='fp-content']
//descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' fp-file ')]
//descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' fp-filename ')]
[contains(concat(' ', normalize-space(.), ' '), '" . $name . "')]",
                $exception,
                $filepickernode
            );
        }

        // Right click opens the contextual menu in both folder and file.
        $node->rightClick();
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

        // Here we don't need to look inside the selected filepicker because there can only be one modal window.
        $repositorylink = $this->find(
            'xpath',
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' fp-repo-area ')]
/descendant::span[contains(concat(' ', normalize-space(@class), ' '), ' fp-repo-name ')]
[contains(concat(' ', normalize-space(.), ' '), ' " . $repositoryname . "')]",
            $repoexception
        );

        // Selecting the repo.
        $repositorylink->click();
    }
}
