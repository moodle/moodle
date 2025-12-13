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
 * The mod_quiz slots created event.
 *
 * @package    mod_quiz
 * @copyright  2021 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quiz\event;

use core\exception\coding_exception;
use core\url;

/**
 * The mod_quiz slot created event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int quizid: the id of the quiz.
 *      - int slotnumber: the slot number in quiz.
 *      - int page: page number.
 * }
 *
 * @package    mod_quiz
 * @copyright  2021 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class slot_created extends \core\event\base {
    protected function init() {
        $this->data['objecttable'] = 'quiz_slots';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    public static function get_name() {
        return get_string('eventslotcreated', 'mod_quiz');
    }

    public function get_description() {

        if (isset($this->other['questionbankentryid'])) {
            $version = $this->other['version'] ?? 'Always latest';
            return "The user with id '$this->userid' created a new slot with " .
                "id '{$this->objectid}', " .
                "slot number '{$this->other['slotnumber']}', and " .
                "question bank entry id '{$this->other['questionbankentryid']}' (version '$version') " .
                "on page '{$this->other['page']}' " .
                "of the quiz with course module id '$this->contextinstanceid'.";
        }

        if (isset($this->other['questionscontextid'])) {
            return "The user with id '$this->userid' created a new slot using question references with " .
                "id '{$this->objectid}', " .
                "slot number '{$this->other['slotnumber']}', " .
                "question context '{$this->other['questionscontextid']}', and " .
                "filter condition '{$this->other['filtercondition']}' " .
                "on page '{$this->other['page']}' " .
                "of the quiz with course module id '$this->contextinstanceid'.";
        }
    }

    public function get_url() {
        return new url('/mod/quiz/edit.php', [
            'cmid' => $this->contextinstanceid
        ]);
    }

    protected function validate_data() {
        parent::validate_data();

        $errors = [];

        if (!isset($this->objectid)) {
            $errors[] = "The 'objectid' value must be set.";
        }

        if (!isset($this->contextinstanceid)) {
            $errors[] = "The 'contextinstanceid' value must be set.";
        }

        if (!isset($this->other['quizid'])) {
            $errors[] = "The 'quizid' value must be set in other.";
        }

        if (!isset($this->other['slotnumber'])) {
            $errors[] = "The 'slotnumber' value must be set in other.";
        }

        if (!isset($this->other['page'])) {
            $errors[] = "The 'page' value must be set in other.";
        }

        $questionbankfields = ['questionbankentryid', 'version'];
        $referencefields = ['questionscontextid', 'filtercondition'];
        $questionbankset = isset($this->other['questionbankentryid']) || array_key_exists('version', $this->other);
        $referenceset = isset($this->other['questionscontextid']) || isset($this->other['filtercondition']);

        if ($questionbankset && $referenceset) {
            $errors[] = "Values for exactly one of these field sets must be set in 'other': " .
                '(' . implode(', ', $questionbankfields) . '), ' .
                '(' . implode(', ', $referencefields) . ')';
        } else if (!$questionbankset && !$referenceset) {
            $errors[] = "Values for exactly one of these field sets must be set in 'other': " .
                '(' . implode(', ', $questionbankfields) . '), ' .
                '(' . implode(', ', $referencefields) . ')';
        }

        if ($questionbankset) {
            if (!isset($this->other['questionbankentryid'])) {
                $errors[] = "The 'questionbankentryid' value must be set in other.";
            }
            if (!array_key_exists('version', $this->other)) {
                $errors[] = "The 'version' value must be set in other.";
            }
        }

        if ($referenceset) {
            if (!isset($this->other['questionscontextid'])) {
                $errors[] = "The 'questionscontextid' value must be set in other.";
            }
            if (!isset($this->other['filtercondition'])) {
                $errors[] = "The 'filtercondition' value must be set in other.";
            }
        }

        if ($errors) {
            throw new coding_exception("Errors in event data:\n\n" . implode("\n", $errors));
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
