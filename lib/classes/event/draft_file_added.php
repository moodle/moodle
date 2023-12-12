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

namespace core\event;

/**
 * draft_file_added
 *
 * Event fired when a file is added to the draft area.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - string itemid: itemid of the file
 *      - string filename: Name of the file added to the draft area.
 *      - string filesize: The file size.
 *      - string filepath: The filepath.
 *      - string contenthash: The file contenthash.
 * }
 *
 * @package   core
 * @since     Moodle 4.2
 * @copyright 2023 The Open University.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class draft_file_added extends base {

    protected function init() {
        $this->data['objecttable'] = 'files';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('eventfileaddedtodraftarea', 'files');
    }

    public function get_description() {
        $humansize = display_size($this->other['filesize']);
        return "The user with id '{$this->userid}' has uploaded file '{$this->other['filepath']}{$this->other['filename']}' " .
            "to the draft file area with item id {$this->other['itemid']}. Size: {$humansize}. ".
            "Content hash: {$this->other['contenthash']}.";
    }

    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['itemid'])) {
            throw new \coding_exception('The \'itemid\' must be set in other.');
        }

        if (!isset($this->other['filename'])) {
            throw new \coding_exception('The \'filename\' value must be set in other.');
        }

        if (!isset($this->other['filesize'])) {
            throw new \coding_exception('The \'filesize\' value must be set in other.');
        }

        if (!isset($this->other['filepath'])) {
            throw new \coding_exception('The \'filepath\' value must be set in other.');
        }

        if (!isset($this->other['contenthash'])) {
            throw new \coding_exception('The \'contenthash\' value must be set in other.');
        }
    }
}
