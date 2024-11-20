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

use coding_exception;
use core\event\base;
use moodle_url;

/**
 * Event fired when a quiz attempt is reopened.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int submitterid: id of submitter (null when triggered by CLI script).
 *      - int quizid: (optional) id of the quiz.
 * }
 *
 * @package   mod_quiz
 * @since     Moodle 4.2
 * @copyright 2023 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempt_reopened extends base {

    protected function init() {
        $this->data['objecttable'] = 'quiz_attempts';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    public function get_description(): string {
        return "The user with id '$this->relateduserid' has had their attempt with id '$this->objectid'" .
            "for the quiz with course module id '$this->contextinstanceid' re-opened by the user with id '$this->userid'.";
    }

    public static function get_name(): string {
        return get_string('eventquizattemptreopened', 'mod_quiz');
    }

    public function get_url(): moodle_url {
        return new moodle_url('/mod/quiz/review.php', ['attempt' => $this->objectid]);
    }

    protected function validate_data(): void {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new coding_exception('The \'relateduserid\' must be set.');
        }

        if (!array_key_exists('submitterid', $this->other)) {
            throw new coding_exception('The \'submitterid\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping(): array {
        return ['db' => 'quiz_attempts', 'restore' => 'quiz_attempt'];
    }

    public static function get_other_mapping(): array {
        return [
            'submitterid' => ['db' => 'user', 'restore' => 'user'],
            'quizid' => ['db' => 'quiz', 'restore' => 'quiz'],
        ];
    }
}
