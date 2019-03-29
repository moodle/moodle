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
 * Steps definitions for the upload repository type.
 *
 * @package    repository_upload
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/core_behat_file_helper.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Gherkin\Node\TableNode as TableNode;

/**
 * Steps definitions to deal with the upload repository.
 *
 * @package    repository_upload
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_repository_upload extends behat_base {

    use core_behat_file_helper;

    /**
     * Uploads a file to the specified filemanager leaving other fields in upload form default. The paths should be relative to moodle codebase.
     *
     * @When /^I upload "(?P<filepath_string>(?:[^"]|\\")*)" file to "(?P<filemanager_field_string>(?:[^"]|\\")*)" filemanager$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filepath
     * @param string $filemanagerelement
     */
    public function i_upload_file_to_filemanager($filepath, $filemanagerelement) {
        $this->upload_file_to_filemanager($filepath, $filemanagerelement, new TableNode(array()), false);
    }

    /**
     * Uploads a file to the specified filemanager leaving other fields in upload form default and confirms to overwrite an existing file. The paths should be relative to moodle codebase.
     *
     * @When /^I upload and overwrite "(?P<filepath_string>(?:[^"]|\\")*)" file to "(?P<filemanager_field_string>(?:[^"]|\\")*)" filemanager$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filepath
     * @param string $filemanagerelement
     */
    public function i_upload_and_overwrite_file_to_filemanager($filepath, $filemanagerelement) {
        $this->upload_file_to_filemanager($filepath, $filemanagerelement, new TableNode(array()),
                get_string('overwrite', 'repository'));
    }

    /**
     * Uploads a file to the specified filemanager and confirms to overwrite an existing file. The paths should be relative to moodle codebase.
     *
     * @When /^I upload "(?P<filepath_string>(?:[^"]|\\")*)" file to "(?P<filemanager_field_string>(?:[^"]|\\")*)" filemanager as:$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filepath
     * @param string $filemanagerelement
     * @param TableNode $data Data to fill in upload form
     */
    public function i_upload_file_to_filemanager_as($filepath, $filemanagerelement, TableNode $data) {
        $this->upload_file_to_filemanager($filepath, $filemanagerelement, $data, false);
    }

    /**
     * Uploads a file to the specified filemanager. The paths should be relative to moodle codebase.
     *
     * @When /^I upload and overwrite "(?P<filepath_string>(?:[^"]|\\")*)" file to "(?P<filemanager_field_string>(?:[^"]|\\")*)" filemanager as:$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filepath
     * @param string $filemanagerelement
     * @param TableNode $data Data to fill in upload form
     */
    public function i_upload_and_overwrite_file_to_filemanager_as($filepath, $filemanagerelement, TableNode $data) {
        $this->upload_file_to_filemanager($filepath, $filemanagerelement, $data,
                get_string('overwrite', 'repository'));
    }

    /**
     * Uploads a file to filemanager
     *
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filepath Normally a path relative to $CFG->dirroot, but can be an absolute path too.
     * @param string $filemanagerelement
     * @param TableNode $data Data to fill in upload form
     * @param false|string $overwriteaction false if we don't expect that file with the same name already exists,
     *     or button text in overwrite dialogue ("Overwrite", "Rename to ...", "Cancel")
     */
    protected function upload_file_to_filemanager($filepath, $filemanagerelement, TableNode $data, $overwriteaction = false) {
        global $CFG;

        $filemanagernode = $this->get_filepicker_node($filemanagerelement);

        // Opening the select repository window and selecting the upload repository.
        $this->open_add_file_window($filemanagernode, get_string('pluginname', 'repository_upload'));

        // Ensure all the form is ready.
        $noformexception = new ExpectationException('The upload file form is not ready', $this->getSession());
        $this->find(
                'xpath',
                "//div[contains(concat(' ', normalize-space(@class), ' '), ' container ')]" .
                "[contains(concat(' ', normalize-space(@class), ' '), ' repository_upload ')]" .
                "/descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' file-picker ')]" .
                "/descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' fp-content ')]" .
                "/descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' fp-upload-form ')]" .
                "/descendant::form",
                $noformexception
        );
        // After this we have the elements we want to interact with.

        // Form elements to interact with.
        $file = $this->find_file('repo_upload_file');

        // Attaching specified file to the node.
        // Replace 'admin/' if it is in start of path with $CFG->admin .
        if (substr($filepath, 0, 6) === 'admin/') {
            $filepath = $CFG->dirroot . DIRECTORY_SEPARATOR . $CFG->admin .
                    DIRECTORY_SEPARATOR . substr($filepath, 6);
        }
        $filepath = str_replace('/', DIRECTORY_SEPARATOR, $filepath);
        if (!is_readable($filepath)) {
            $filepath = $CFG->dirroot . DIRECTORY_SEPARATOR . $filepath;
            if (!is_readable($filepath)) {
                throw new ExpectationException('The file to be uploaded does not exist.', $this->getSession());
            }
        }
        $file->attachFile($filepath);

        // Fill the form in Upload window.
        $datahash = $data->getRowsHash();

        // The action depends on the field type.
        foreach ($datahash as $locator => $value) {

            $field = behat_field_manager::get_form_field_from_label($locator, $this);

            // Delegates to the field class.
            $field->set_value($value);
        }

        // Submit the file.
        $submit = $this->find_button(get_string('upload', 'repository'));
        $submit->press();

        // We wait for all the JS to finish as it is performing an action.
        $this->getSession()->wait(self::get_timeout(), self::PAGE_READY_JS);

        if ($overwriteaction !== false) {
            $overwritebutton = $this->find_button($overwriteaction);
            $this->ensure_node_is_visible($overwritebutton);
            $overwritebutton->click();

            // We wait for all the JS to finish.
            $this->getSession()->wait(self::get_timeout(), self::PAGE_READY_JS);
        }

    }

    /**
     * Try to get the filemanager node specified by the element
     *
     * @param string $filepickerelement
     * @return \Behat\Mink\Element\NodeElement
     * @throws ExpectationException
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
            // Gets the filemanager node specified by the locator which contains the filepicker container.
            $filepickerelement = behat_context_helper::escape($filepickerelement);
            $filepickercontainer = $this->find(
                    'xpath',
                    "//input[./@id = //label[normalize-space(.)=$filepickerelement]/@for]" .
                    "//ancestor::div[contains(concat(' ', normalize-space(@class), ' '), ' felement ')]",
                    $exception
            );
        }

        return $filepickercontainer;
    }

}
