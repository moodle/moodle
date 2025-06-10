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
 * @package    block_simple_restore
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class semester_backup_path_setting extends admin_setting_configtext {
    public function validate($data) {
        global $CFG;

        // Must validate through original validation.
        $isvalidated = parent::validate($data);

        if (is_string($isvalidated)) {
            return $isvalidated;
        }

        $chars = str_split($data);
        if (current($chars) != '/' and end($chars) != '/') {
            return get_string('config_path_surround', 'block_simple_restore');
        }

        $realpath = "$CFG->dataroot$data";

        if (!file_exists($realpath)) {
            return get_string('config_path_not_exists', 'block_simple_restore');
        }

        if (!is_writable($realpath)) {
            return get_string('config_path_not_writable', 'block_simple_restore');
        }

        return true;
    }
}
