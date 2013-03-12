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

        // Opening the select repository window and selecting the upload repository.
        $this->open_add_file_window($filepickernode, get_string('pluginname', 'repository_upload'));

        // Attaching specified file to the node.
        $fileabsolutepath = $CFG->dirroot . '/' . $filepath;
        $inputfilenode = $this->find_file('repo_upload_file');
        $inputfilenode->attachFile($fileabsolutepath);

        // Submit the file.
        $this->getSession()->getPage()->pressButton('Upload this file');

        // Wait a while for the file to be uploaded.
        $this->getSession()->wait(6 * 1000, false);
    }

}
