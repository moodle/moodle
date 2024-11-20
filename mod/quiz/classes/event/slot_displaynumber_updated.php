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
 * The mod_quiz slot display updated event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int quizid: the id of the quiz.
 *      - string displaynumber: the slot's customised question number value.
 * }
 *
 * @package    mod_quiz
 * @copyright  2022 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class slot_displaynumber_updated extends \core\event\base {
    /**
     * Initialise the quiz_slots table.
     */
    protected function init(): void {
        $this->data['objecttable'] = 'quiz_slots';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Return the name of the event.
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('eventslotdisplayedquestionnumberupdated', 'mod_quiz');
    }

    /**
     * Log describes which user customised the question number in a given slot and in which quiz.
     *
     * @return string
     */
    public function get_description(): string {
        return "The user with id '$this->userid' updated the slot with id '{$this->objectid}' " .
            "belonging to the quiz with course module id '$this->contextinstanceid'. " .
            "Its customised question number value was set to '{$this->other['displaynumber']}'.";
    }

    /**
     * Return the url object of the quiz editing page.
     *
     * @return \moodle_url
     */
    public function get_url(): \moodle_url {
        return new \moodle_url('/mod/quiz/edit.php', ['cmid' => $this->contextinstanceid]);
    }

    /**
     * validate the data being logged.
     */
    protected function validate_data(): void {
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

        if (!isset($this->other['displaynumber'])) {
            throw new \coding_exception('The \'displaynumber\' value must be set in other.');
        }
    }

    /**
     * Return the mapped array.
     *
     * @return string[]
     */
    public static function get_objectid_mapping(): array {
        return ['db' => 'quiz_slots', 'restore' => 'quiz_question_instance'];
    }

    /**
     * Return the mapped array.
     *
     * @return array
     */
    public static function get_other_mapping(): array {
        $othermapped = [];
        $othermapped['quizid'] = ['db' => 'quiz', 'restore' => 'quiz'];

        return $othermapped;
    }
}
