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
 * Testable content plugin class.
 *
 * @package    core_contentbank
 * @category   test
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace contenttype_testable;

use file_exception;
use stored_file;

/**
 * Testable content plugin class.
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content extends \core_contentbank\content {

    /**
     * Import a file as a valid content.
     *
     * This method will thow an error if the filename is "error.*"
     *
     * @param stored_file $file File to store in the content file area.
     * @return stored_file|null the stored content file or null if the file is discarted.
     * @throws file_exception if the filename contains the word "error"
     */
    public function import_file(stored_file $file): ?stored_file {
        $filename = $file->get_filename();
        if (strrpos($filename, 'error') !== false) {
            throw new file_exception('yourerrorthanks', 'contenttype_test');
        }
        return parent::import_file($file);
    }
}
