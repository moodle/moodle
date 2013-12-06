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
     * This will be deprecated in 2.7
     * @see behat_filepicker::i_add_file_from_repository_to_filemanager()
     *
     * @When /^I add "(?P<filename_string>(?:[^"]|\\")*)" file from recent files to "(?P<filepicker_field_string>(?:[^"]|\\")*)" filepicker$/
     * @param string $filename
     * @param string $filepickerelement
     */
    public function i_add_file_from_recent_files_to_filepicker($filename, $filepickerelement) {
        $reponame = get_string('pluginname', 'repository_recent');
        $alternative = 'I add "' . $this->escape($filename) . '" file from "' .
                $reponame . '" to "' . $this->escape($filepickerelement) . '" filemanager';
        return array(new Behat\Behat\Context\Step\Given($alternative));
    }

}
