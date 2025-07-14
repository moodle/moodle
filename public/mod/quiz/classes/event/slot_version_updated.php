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

declare(strict_types=1);

namespace mod_quiz\event;

use core\exception\coding_exception;
use core\url;

/**
 * The question version of a slot has changed.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int quizid: the id of the quiz.
 *      - int previousversion: the previous question version.
 *      - int newversion: the new question version.
 * }
 *
 * @package   mod_quiz
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @author    Cameron Ball <cameronball@catalyst-au.net>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class slot_version_updated extends \core\event\base {

    #[\Override]
    protected function init(): void {
        $this->data['objecttable'] = 'quiz_slots';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    #[\Override]
    public static function get_name(): string {
        return get_string('eventslotversionupdated', 'mod_quiz');
    }

    #[\Override]
    public function get_description(): string {
        $previousversion = $this->other['previousversion'] ?? 'Always latest';
        $newversion = $this->other['newversion'] ?? 'Always latest';
        return "The user with id '$this->userid' updated the slot with id '{$this->objectid}' " .
            "belonging to the quiz with course module id '$this->contextinstanceid'. " .
            "Its question version was changed from '$previousversion' to '$newversion'.";
    }

    #[\Override]
    public function get_url(): url {
        return new url('/mod/quiz/edit.php', ['cmid' => $this->contextinstanceid]);
    }

    #[\Override]
    protected function validate_data(): void {
        parent::validate_data();

        if (!isset($this->objectid)) {
            throw new coding_exception('The \'objectid\' value must be set.');
        }

        if (!isset($this->contextinstanceid)) {
            throw new coding_exception('The \'contextinstanceid\' value must be set.');
        }

        if (!isset($this->other['quizid'])) {
            throw new coding_exception('The \'quizid\' value must be set in other.');
        }

        // The value of previousversion and newversion can be null, so we check if
        // the array key exists.
        if (!array_key_exists('previousversion', $this->other)) {
            throw new coding_exception('The \'previousversion\' value must be set in other.');
        }

        if (!array_key_exists('newversion', $this->other)) {
            throw new coding_exception('The \'newversion\' value must be set in other.');
        }
    }

    #[\Override]
    public static function get_objectid_mapping(): array {
        return ['db' => 'quiz_slots', 'restore' => 'quiz_question_instance'];
    }

    #[\Override]
    public static function get_other_mapping(): array {
        $othermapped = [];
        $othermapped['quizid'] = ['db' => 'quiz', 'restore' => 'quiz'];

        return $othermapped;
    }
}
