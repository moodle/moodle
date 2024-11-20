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
 * The mark a slot is graded out of has changed.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int quizid: the id of the quiz.
 *      - int previousmaxmark: the previous max mark value.
 *      - int newmaxmark: the new max mark value.
 * }
 *
 * @package   mod_quiz
 * @copyright 2021 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class slot_mark_updated extends \core\event\base {
    protected function init() {
        $this->data['objecttable'] = 'quiz_slots';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    public static function get_name() {
        return get_string('eventslotmarkupdated', 'mod_quiz');
    }

    public function get_description() {
        return "The user with id '$this->userid' updated the slot with id '{$this->objectid}' " .
            "belonging to the quiz with course module id '$this->contextinstanceid'. " .
            "Its max mark was changed from '{$this->other['previousmaxmark']}' to '{$this->other['newmaxmark']}'.";
    }

    public function get_url() {
        return new \moodle_url('/mod/quiz/edit.php', [
            'cmid' => $this->contextinstanceid
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

        if (!isset($this->other['previousmaxmark'])) {
            throw new \coding_exception('The \'previousmaxmark\' value must be set in other.');
        }

        if (!isset($this->other['newmaxmark'])) {
            throw new \coding_exception('The \'newmaxmark\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return ['db' => 'quiz_slots', 'restore' => 'quiz_question_instance'];
    }

    public static function get_other_mapping() {
        $othermapped = [];
        $othermapped['quizid'] = ['db' => 'quiz', 'restore' => 'quiz'];

        return $othermapped;
    }
}
