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
 * Filemanager and filepicker manipulation steps definitions.
 *
 * @package    core_filepicker
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_files.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Behat\Context\Step\Given as Given;

/**
 * Steps definitions to deal with the filemanager and filepicker.
 *
 * Extends behat_files rather than behat_base as is file-related.
 *
 * @package    core_filepicker
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_filepicker extends behat_files {

    /**
     * Creates a folder with specified name in the current folder and in the specified filepicker field.
     *
     * Will be deprecated in 2.7
     * @see behat_filepicker::i_create_folder_in_filemanager()
     *
     * @Given /^I create "(?P<foldername_string>(?:[^"]|\\")*)" folder in "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $foldername
     * @param string $filepickerelement
     */
    public function i_create_folder_in_filepicker($foldername, $filepickerelement) {
        $alternative = 'I create "' . $this->escape($foldername) .
                '" folder in "' . $this->escape($filepickerelement) . '" filemanager';
        return array(new Given($alternative));
    }

    /**
     * Creates a folder with specified name in the current folder and in the specified filemanager field.
     *
     * @Given /^I create "(?P<foldername_string>(?:[^"]|\\")*)" folder in "(?P<filemanager_field_string>(?:[^"]|\\")*)" filemanager$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $foldername
     * @param string $filemanagerelement
     */
    public function i_create_folder_in_filemanager($foldername, $filemanagerelement) {

        $fieldnode = $this->get_filepicker_node($filemanagerelement);

        // Looking for the create folder button inside the specified filemanager.
        $exception = new ExpectationException('No folders can be created in "'.$filemanagerelement.'" filemanager', $this->getSession());
        $newfolder = $this->find('css', 'div.fp-btn-mkdir a', $exception, $fieldnode);
        $newfolder->click();

        // Setting the folder name in the modal window.
        $exception = new ExpectationException('The dialog to enter the folder name does not appear', $this->getSession());
        $dialoginput = $this->find('css', '.fp-mkdir-dlg-text input', $exception);
        $dialoginput->setValue($foldername);

        $exception = new ExpectationException('The button for the create folder dialog can not be located', $this->getSession());
        $dialognode = $this->find('css', '.moodle-dialogue-focused');
        $buttonnode = $this->find('css', '.fp-dlg-butcreate', $exception, $dialognode);
        $buttonnode->click();
    }

    /**
     * Opens the contents of a filepicker folder. It looks for the folder in the current folder and in the path bar.
     *
     * Will be deprecated in 2.7
     * @see behat_filepicker::i_open_folder_from_filemanager()
     *
     * @Given /^I open "(?P<foldername_string>(?:[^"]|\\")*)" folder from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $foldername
     * @param string $filepickerelement
     */
    public function i_open_folder_from_filepicker($foldername, $filepickerelement) {
        $alternative = 'I open "' . $this->escape($foldername) . '" folder from "' .
                $this->escape($filepickerelement) . '" filemanager';
        return array(new Given($alternative));
    }

    /**
     * Opens the contents of a filemanager folder. It looks for the folder in the current folder and in the path bar.
     *
     * @Given /^I open "(?P<foldername_string>(?:[^"]|\\")*)" folder from "(?P<filemanager_field_string>(?:[^"]|\\")*)" filemanager$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $foldername
     * @param string $filemanagerelement
     */
    public function i_open_folder_from_filemanager($foldername, $filemanagerelement) {

        $fieldnode = $this->get_filepicker_node($filemanagerelement);

        $exception = new ExpectationException(
            'The "'.$foldername.'" folder can not be found in the "'.$filemanagerelement.'" filemanager',
            $this->getSession()
        );

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
    }

    /**
     * Unzips the specified file from the specified filepicker field. The zip file has to be visible in the current folder.
     *
     * Will be deprecated in 2.7
     * @see behat_filepicker::i_unzip_file_from_filemanager()
     *
     * @Given /^I unzip "(?P<filename_string>(?:[^"]|\\")*)" file from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filename
     * @param string $filepickerelement
     */
    public function i_unzip_file_from_filepicker($filename, $filepickerelement) {
        $alternative = 'I unzip "' . $this->escape($filename) . '" file from "' .
                $this->escape($filepickerelement) . '" filemanager';
        return array(new Given($alternative));
    }

    /**
     * Unzips the specified file from the specified filemanager field. The zip file has to be visible in the current folder.
     *
     * @Given /^I unzip "(?P<filename_string>(?:[^"]|\\")*)" file from "(?P<filemanager_field_string>(?:[^"]|\\")*)" filemanager$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filename
     * @param string $filemanagerelement
     */
    public function i_unzip_file_from_filemanager($filename, $filemanagerelement) {

        // Open the contextual menu of the filemanager element.
        $this->open_element_contextual_menu($filename, $filemanagerelement);

        // Execute the action.
        $exception = new ExpectationException($filename.' element can not be unzipped', $this->getSession());
        $this->perform_on_element('unzip', $exception);
    }

    /**
     * Zips the specified folder from the specified filepicker field. The folder has to be in the current folder.
     *
     * Will be deprecated in 2.7
     *
     * @Given /^I zip "(?P<filename_string>(?:[^"]|\\")*)" folder from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $foldername
     * @param string $filepickerelement
     */
    public function i_zip_folder_from_filepicker($foldername, $filepickerelement) {
        $alternative = 'I zip "' . $this->escape($foldername) . '" folder from "' .
                $this->escape($filepickerelement) . '" filemanager';
        return array(new Given($alternative));
    }

    /**
     * Zips the specified folder from the specified filemanager field. The folder has to be in the current folder.
     *
     * @Given /^I zip "(?P<filename_string>(?:[^"]|\\")*)" folder from "(?P<filemanager_field_string>(?:[^"]|\\")*)" filemanager$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $foldername
     * @param string $filemanagerelement
     */
    public function i_zip_folder_from_filemanager($foldername, $filemanagerelement) {

        // Open the contextual menu of the filemanager element.
        $this->open_element_contextual_menu($foldername, $filemanagerelement);

        // Execute the action.
        $exception = new ExpectationException($foldername.' element can not be zipped', $this->getSession());
        $this->perform_on_element('zip', $exception);
    }

    /**
     * Deletes the specified file or folder from the specified filepicker field.
     *
     * Will be deprecated in 2.7
     * @see behat_filepicker::i_delete_file_from_filemanager()
     *
     * @Given /^I delete "(?P<file_or_folder_name_string>(?:[^"]|\\")*)" from "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $name
     * @param string $filepickerelement
     */
    public function i_delete_file_from_filepicker($name, $filepickerelement) {
        $alternative = 'I delete "' . $this->escape($name) . '" from "' .
                $this->escape($filepickerelement) . '" filemanager';
        return array(new Given($alternative));
    }

    /**
     * Deletes the specified file or folder from the specified filemanager field.
     *
     * @Given /^I delete "(?P<file_or_folder_name_string>(?:[^"]|\\")*)" from "(?P<filemanager_field_string>(?:[^"]|\\")*)" filemanager$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $name
     * @param string $filemanagerelement
     */
    public function i_delete_file_from_filemanager($name, $filemanagerelement) {

        // Open the contextual menu of the filemanager element.
        $this->open_element_contextual_menu($name, $filemanagerelement);

        // Execute the action.
        $exception = new ExpectationException($name.' element can not be deleted', $this->getSession());
        $this->perform_on_element('delete', $exception);

        // Yes, we are sure.
        // Using xpath + click instead of pressButton as 'Ok' it is a common string.
        $okbutton = $this->find('css', 'div.fp-dlg button.fp-dlg-butconfirm');
        $okbutton->click();
    }


    /**
     * Makes sure user can see the exact number of elements (files in folders) in the filemanager.
     *
     * @Then /^I should see "(?P<elementscount_number>\d+)" elements in "(?P<filemanagerelement_string>(?:[^"]|\\")*)" filemanager$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param int $elementscount
     * @param string $filemanagerelement
     */
    public function i_should_see_elements_in_filemanager($elementscount, $filemanagerelement) {
        $filemanagernode = $this->get_filepicker_node($filemanagerelement);

        // We count .fp-file elements inside a filemanager not being updated.
        $xpath = "//div[contains(concat(' ', normalize-space(@class), ' '), ' filemanager ')]" .
            "[not(contains(concat(' ', normalize-space(@class), ' '), ' fm-updating '))]" .
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' fp-content ')]" .
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' fp-file ')]";

        $elements = $this->find_all('xpath', $xpath, false, $filemanagernode);
        if (count($elements) != $elementscount) {
            throw new ExpectationException('Found '.count($elements).' elements in filemanager instead of expected '.$elementscount, $this->getSession());
        }
    }

    /**
     * Picks the file from repository leaving default values in select file dialogue.
     *
     * @When /^I add "(?P<filepath_string>(?:[^"]|\\")*)" file from "(?P<repository_string>(?:[^"]|\\")*)" to "(?P<filemanagerelement_string>(?:[^"]|\\")*)" filemanager$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filepath
     * @parma string $repository
     * @param string $filemanagerelement
     */
    public function i_add_file_from_repository_to_filemanager($filepath, $repository, $filemanagerelement) {
        $this->add_file_from_repository_to_filemanager($filepath, $repository, $filemanagerelement, new TableNode(), false);
    }

    /**
     * Picks the file from repository leaving default values in select file dialogue and confirming to overwrite an existing file.
     *
     * @When /^I add and overwrite "(?P<filepath_string>(?:[^"]|\\")*)" file from "(?P<repository_string>(?:[^"]|\\")*)" to "(?P<filemanagerelement_string>(?:[^"]|\\")*)" filemanager$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filepath
     * @parma string $repository
     * @param string $filemanagerelement
     */
    public function i_add_and_overwrite_file_from_repository_to_filemanager($filepath, $repository, $filemanagerelement) {
        $this->add_file_from_repository_to_filemanager($filepath, $repository, $filemanagerelement, new TableNode(),
                get_string('overwrite', 'repository'));
    }

    /**
     * Picks the file from repository filling the form in Select file dialogue.
     *
     * @When /^I add "(?P<filepath_string>(?:[^"]|\\")*)" file from "(?P<repository_string>(?:[^"]|\\")*)" to "(?P<filemanager_field_string>(?:[^"]|\\")*)" filemanager as:$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filepath
     * @parma string $repository
     * @param string $filemanagerelement
     * @param TableNode $data Data to fill the form in Select file dialogue
     */
    public function i_add_file_from_repository_to_filemanager_as($filepath, $repository, $filemanagerelement, TableNode $data) {
        $this->add_file_from_repository_to_filemanager($filepath, $repository, $filemanagerelement, $data, false);
    }

    /**
     * Picks the file from repository confirming to overwrite an existing file
     *
     * @When /^I add and overwrite "(?P<filepath_string>(?:[^"]|\\")*)" file from "(?P<repository_string>(?:[^"]|\\")*)" to "(?P<filemanager_field_string>(?:[^"]|\\")*)" filemanager as:$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filepath
     * @parma string $repository
     * @param string $filemanagerelement
     * @param TableNode $data Data to fill the form in Select file dialogue
     */
    public function i_add_and_overwrite_file_from_repository_to_filemanager_as($filepath, $repository, $filemanagerelement, TableNode $data) {
        $this->add_file_from_repository_to_filemanager($filepath, $repository, $filemanagerelement, $data,
                get_string('overwrite', 'repository'));
    }

    /**
     * Picks the file from private files repository
     *
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filepath
     * @parma string $repository
     * @param string $filemanagerelement
     * @param TableNode $data Data to fill the form in Select file dialogue
     * @param false|string $overwriteaction false if we don't expect that file with the same name already exists,
     *     or button text in overwrite dialogue ("Overwrite", "Rename to ...", "Cancel")
     */
    protected function add_file_from_repository_to_filemanager($filepath, $repository, $filemanagerelement, TableNode $data,
            $overwriteaction = false) {
        $filemanagernode = $this->get_filepicker_node($filemanagerelement);

        // Opening the select repository window and selecting the upload repository.
        $this->open_add_file_window($filemanagernode, $repository);

        $this->open_element_contextual_menu($filepath);

        // Fill the form in Select window.
        $datahash = $data->getRowsHash();

        // The action depends on the field type.
        foreach ($datahash as $locator => $value) {
            // Getting the node element pointed by the label.
            $fieldnode = $this->find_field($locator);

            // Gets the field type from a parent node.
            $field = behat_field_manager::get_form_field($fieldnode, $this->getSession());

            // Delegates to the field class.
            $field->set_value($value);
        }

        $this->find_button(get_string('getfile', 'repository'))->click();

        // We wait for all the JS to finish as it is performing an action.
        $this->getSession()->wait(self::TIMEOUT, self::PAGE_READY_JS);

        if ($overwriteaction !== false) {
            $overwritebutton = $this->find_button($overwriteaction);
            $this->ensure_node_is_visible($overwritebutton);
            $overwritebutton->click();

            // We wait for all the JS to finish.
            $this->getSession()->wait(self::TIMEOUT, self::PAGE_READY_JS);
        }

    }

}
