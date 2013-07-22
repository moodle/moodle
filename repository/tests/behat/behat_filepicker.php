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
 * Files and filepicker manipulation steps definitions.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_files.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions to deal with the filepicker.
 *
 * Extends behat_files rather than behat_base as is file-related.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_filepicker extends behat_files {

    /**
     * Creates a folder with specified name in the current folder and in the specified filepicker field.
     *
     * @Given /^I create "(?P<foldername_string>(?:[^"]|\\")*)" folder in "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $foldername
     * @param string $filepickerelement
     */
    public function i_create_folder_in_filepicker($foldername, $filepickerelement) {

        $fieldnode = $this->get_filepicker_node($filepickerelement);

        // Looking for the create folder button inside the specified filepicker.
        $exception = new ExpectationException('No folders can be created in "'.$filepickerelement.'" filepicker', $this->getSession());
        $newfolder = $this->find('css', 'div.fp-btn-mkdir a', $exception, $fieldnode);
        $newfolder->click();

        // Setting the folder name in the modal window.
        $exception = new ExpectationException('The dialog to enter the folder name does not appear', $this->getSession());
        $dialoginput = $this->find('css', '.fp-mkdir-dlg-text input');
        $dialoginput->setValue($foldername);

        $this->getSession()->getPage()->pressButton(get_string('makeafolder'));

        // Wait until the process finished and modal windows are hidden.
        $this->wait_until_return_to_form();

        // Wait until the current folder contents are updated
        $this->wait_until_contents_are_updated($fieldnode);
    }

    /**
     * Opens the contents of a filepicker folder. It looks for the folder in the current folder and in the path bar.
     *
     * @Given /^I open "(?P<foldername_string>(?:[^"]|\\")*)" folder from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $foldername
     * @param string $filepickerelement
     */
    public function i_open_folder_from_filepicker($foldername, $filepickerelement) {

        $fieldnode = $this->get_filepicker_node($filepickerelement);

        $exception = new ExpectationException(
            'The "'.$foldername.'" folder can not be found in the "'.$filepickerelement.'" filepicker',
            $this->getSession()
        );

        // Just in case there is any contents refresh in progress.
        $this->wait_until_contents_are_updated($fieldnode);

        $folderliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($foldername);

        // We look both in the pathbar and in the contents.
        try {

            // In the current folder workspace.
            $folder = $this->find(
                'xpath',
                "//div[contains(concat(' ', normalize-space(@class), ' '), ' fp-folder ')]" .
                    "/descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' fp-filename ')]" .
                    "[normalize-space(.)=$folderliteral]",
                $exception,
                $fieldnode
            );
        } catch (ExpectationException $e) {

            // And in the pathbar.
            $folder = $this->find(
                'xpath',
                "//a[contains(concat(' ', normalize-space(@class), ' '), ' fp-path-folder-name ')]" .
                    "[normalize-space(.)=$folderliteral]",
                $exception,
                $fieldnode
            );
        }

        // It should be a NodeElement, otherwise an exception would have been thrown.
        $folder->click();

        // Wait until the current folder contents are updated
        $this->wait_until_contents_are_updated($fieldnode);
    }

    /**
     * Unzips the specified file from the specified filepicker field. The zip file has to be visible in the current folder.
     *
     * @Given /^I unzip "(?P<filename_string>(?:[^"]|\\")*)" file from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filename
     * @param string $filepickerelement
     */
    public function i_unzip_file_from_filepicker($filename, $filepickerelement) {

        // Open the contextual menu of the filepicker element.
        $this->open_element_contextual_menu($filename, $filepickerelement);

        // Execute the action.
        $exception = new ExpectationException($filename.' element can not be unzipped', $this->getSession());
        $this->perform_on_element('unzip', $exception);

        // Wait until the process finished and modal windows are hidden.
        $this->wait_until_return_to_form();

        // Wait until the current folder contents are updated
        $containernode = $this->get_filepicker_node($filepickerelement);
        $this->wait_until_contents_are_updated($containernode);
    }

    /**
     * Zips the specified folder from the specified filepicker field. The folder has to be in the current folder.
     *
     * @Given /^I zip "(?P<filename_string>(?:[^"]|\\")*)" folder from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $foldername
     * @param string $filepickerelement
     */
    public function i_zip_folder_from_filepicker($foldername, $filepickerelement) {

        // Open the contextual menu of the filepicker element.
        $this->open_element_contextual_menu($foldername, $filepickerelement);

        // Execute the action.
        $exception = new ExpectationException($foldername.' element can not be zipped', $this->getSession());
        $this->perform_on_element('zip', $exception);

        // Wait until the process finished and modal windows are hidden.
        $this->wait_until_return_to_form();

        // Wait until the current folder contents are updated
        $containernode = $this->get_filepicker_node($filepickerelement);
        $this->wait_until_contents_are_updated($containernode);
    }

    /**
     * Deletes the specified file or folder from the specified filepicker field.
     *
     * @Given /^I delete "(?P<file_or_folder_name_string>(?:[^"]|\\")*)" from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $foldername
     * @param string $filepickerelement
     */
    public function i_delete_file_from_filepicker($name, $filepickerelement) {

        // Open the contextual menu of the filepicker element.
        $this->open_element_contextual_menu($name, $filepickerelement);

        // Execute the action.
        $exception = new ExpectationException($name.' element can not be deleted', $this->getSession());
        $this->perform_on_element('delete', $exception);

        // Yes, we are sure.
        // Using xpath + click instead of pressButton as 'Ok' it is a common string.
        $okbutton = $this->find('css', 'div.fp-dlg button.fp-dlg-butconfirm');
        $okbutton->click();

        // Wait until the process finished and modal windows are hidden.
        $this->wait_until_return_to_form();

        // Wait until file manager contents are updated.
        $containernode = $this->get_filepicker_node($filepickerelement);
        $this->wait_until_contents_are_updated($containernode);
    }

}
