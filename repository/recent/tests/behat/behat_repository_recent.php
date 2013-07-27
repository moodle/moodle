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
 * Steps definitions for recent files repository type.
 *
 * @package    repository_recent
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_files.php');

/**
 * Steps definitions to deal with recent files and the filepicker.
 *
 * Extends behat_files rather than behat_base as is file-related.
 *
 * @package    repository_recent
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_repository_recent extends behat_files {

    /**
     * Adds the specified file from the 'Recent files' repository to the specified filepicker of the current page.
     *
     * @When /^I add "(?P<filename_string>(?:[^"]|\\")*)" file from recent files to "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @param string $filename
     * @param string $filepickerelement
     */
    public function i_add_file_from_recent_files_to_filepicker($filename, $filepickerelement) {

        $filepickernode = $this->get_filepicker_node($filepickerelement);

        // Opening the select repository window and selecting the recent repository.
        $this->open_add_file_window($filepickernode, get_string('pluginname', 'repository_recent'));

        // Opening the specified file contextual menu from the modal window.
        $this->open_element_contextual_menu($filename);

        $this->find_button(get_string('getfile', 'repository'))->click();

        // Ensure the file has been selected and we returned to the form page.
        $this->wait_until_return_to_form();

        // Wait until file manager contents are updated.
        $this->wait_until_contents_are_updated($filepickernode);
    }

}
