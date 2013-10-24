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

require_once(__DIR__ . '/../../../../lib/behat/behat_files.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions to deal with the filepicker.
 *
 * Extends behat_files rather than behat_base as is file-related.
 *
 * @package    repository_upload
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_repository_upload extends behat_files {

    /**
     * Uploads a file to the specified file picker. It deals both with single-file and multiple-file filepickers. The paths should be relative to moodle codebase.
     *
     * @When /^I upload "(?P<filepath_string>(?:[^"]|\\")*)" file to "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $filepath
     * @param string $filepickerelement
     */
    public function i_upload_file_to_filepicker($filepath, $filepickerelement) {
        global $CFG;

        $filepickernode = $this->get_filepicker_node($filepickerelement);

        // Wait until file manager is completely loaded.
        $this->wait_until_contents_are_updated($filepickernode);

        // Opening the select repository window and selecting the upload repository.
        $this->open_add_file_window($filepickernode, get_string('pluginname', 'repository_upload'));

        // Ensure all the form is ready.
        $this->getSession()->wait(2 * 1000, false);
        $noformexception = new ExpectationException('The upload file form is not ready', $this->getSession());
        $this->find(
            'xpath',
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' file-picker ')]" .
                "[contains(concat(' ', normalize-space(@class), ' '), ' repository_upload ')]" .
                "/descendant::div[@class='fp-content']" .
                "/descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' fp-upload-form ')]" .
                "/descendant::form",
            $noformexception
        );
        // After this we have the elements we want to interact with.

        // Form elements to interact with.
        $file = $this->find_file('repo_upload_file');
        $submit = $this->find_button(get_string('upload', 'repository'));

        // Attaching specified file to the node.
        // Replace 'admin/' if it is in start of path with $CFG->admin .
        $pos = strpos($filepath, 'admin/');
        if ($pos === 0) {
            $filepath = $CFG->admin . DIRECTORY_SEPARATOR . substr($filepath, 6);
        }
        $filepath = str_replace('/', DIRECTORY_SEPARATOR, $filepath);
        $fileabsolutepath = $CFG->dirroot . DIRECTORY_SEPARATOR . $filepath;
        $file->attachFile($fileabsolutepath);

        // Submit the file.
        $submit->press();

        // Ensure the file has been uploaded and all ajax processes finished.
        $this->wait_until_return_to_form();

        // Wait until file manager contents are updated.
        $this->wait_until_contents_are_updated($filepickernode);
    }

}
