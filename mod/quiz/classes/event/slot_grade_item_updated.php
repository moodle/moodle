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

use core\event\base;

/**
 * The quiz sub-grade that this slot contributes to has changed.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int quizid: the id of the quiz.
 *      - int previousgradeitem: the previous max mark value.
 *      - int newgradeitem: the new max mark value.
 * }
 *
 * @package   mod_quiz
 * @copyright 2023 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class slot_grade_item_updated extends base {
    protected function init() {
        $this->data['objecttable'] = 'quiz_slots';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    public static function get_name() {
        return get_string('eventslotgradeitemupdated', 'mod_quiz');
    }

    public function get_description() {
        return "The user with id '$this->userid' updated the slot with id '{$this->objectid}' " .
            "belonging to the quiz with course module id '$this->contextinstanceid'. " .
            "The grade item this slot contributes to was changed from '{$this->other['previousgradeitem']}' " .
            "to '{$this->other['newgradeitem']}'.";
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

        if (!isset($this->other['quizid'])) {
            throw new \coding_exception('The \'quizid\' value must be set in other.');
        }

        if (!array_key_exists('previousgradeitem', $this->other)) {
            throw new \coding_exception('The \'previousgradeitem\' value must be set in other.');
        }

        if (!array_key_exists('newgradeitem', $this->other)) {
            throw new \coding_exception('The \'newgradeitem\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return ['db' => 'quiz_slots', 'restore' => 'quiz_question_instance'];
    }

    public static function get_other_mapping() {
        return [
            'quizid' => ['db' => 'quiz', 'restore' => 'quiz'],
        ];
    }
}
