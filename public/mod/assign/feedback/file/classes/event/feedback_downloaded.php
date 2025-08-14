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

namespace assignfeedback_file\event;

/**
 * One or all of the feedback files have been downloaded.
 *
 * @package assignfeedback_file
 * @since Moodle 5.1
 * @copyright 2025 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback_downloaded extends \core\event\base {
    #[\Override]
    public function get_description() {
        return "The user with id '$this->userid' downloaded feeedback file '{$this->other['filename']}' (" .
            "'{$this->other['fileid']}') for the assignment with course module id '$this->contextinstanceid'.";
    }

    #[\Override]
    public static function get_name() {
        return get_string('eventfeedback_downloaded', 'assignfeedback_file');
    }

    #[\Override]
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'assign_grades';
    }

    /**
     * Creates the event.
     *
     * @param \stored_file $file File that was downloaded
     * @return feedback_downloaded Event instance
     */
    public static function create_for_file(\stored_file $file): feedback_downloaded {
        return self::create([
            'contextid' => $file->get_contextid(),
            'objectid' => $file->get_itemid(), // The file item id is the assign_grades id.
            'other' => [
                'fileid' => $file->get_id(),
                'filename' => $file->get_filename(),
            ],
        ]);
    }

    #[\Override]
    protected function validate_data() {
        if (empty($this->other['fileid'])) {
            throw new \coding_exception('other[\'fileid\'] must be set');
        }
        if (empty($this->other['filename'])) {
            throw new \coding_exception('other[\'filename\'] must be set');
        }

        parent::validate_data();
    }

    #[\Override]
    public static function get_objectid_mapping() {
        return ['db' => 'assign_grades', 'restore' => 'grade'];
    }
}
