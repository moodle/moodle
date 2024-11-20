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

namespace mod_quiz\event;

/**
 * Event to record a quiz grade item being deleted.
 *
 * @property-read array $other {
 * }
 *
 * @package   mod_quiz
 * @copyright 2023 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_grade_item_deleted extends \core\event\base {
    protected function init() {
        $this->data['objecttable'] = 'quiz_grade_items';
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    public static function get_name() {
        return get_string('eventquizgradeitemdeleted', 'mod_quiz');
    }

    public function get_description() {
        return "The user with id '$this->userid' deleted quiz grade item with id '$this->objectid' " .
                "for the quiz with course module id '$this->contextinstanceid'.";
    }

    public function get_url() {
        return new \moodle_url('/mod/quiz/editgrading.php', [
            'cmid' => $this->contextinstanceid,
        ]);
    }

    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->objectid)) {
            throw new \coding_exception('The \'objectid\' value must be set.');
        }

        if (!isset($this->contextinstanceid)) {
            throw new \coding_exception('The \'contextinstanceid\' value must be set.');
        }
    }

    public static function get_objectid_mapping() {
        return ['db' => 'quiz_grade_items', 'restore' => 'quiz_grade_items'];
    }

    public static function get_other_mapping() {
        return [
            'quizid' => ['db' => 'quiz', 'restore' => 'quiz'],
        ];
    }
}
