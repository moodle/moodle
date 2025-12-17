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

use core\event\base;
use core\exception\coding_exception;
use core\url;

/**
 * This event is fired when the filter condition of a slot
 * using the question set references table is updated.
 *
 * @package   mod_quiz
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @author    Cameron Ball <cameronball@catalyst-au.net>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class slot_filtercondition_updated extends base {
    #[\Override]
    protected function init() {
        $this->data['objecttable'] = 'quiz_slots';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    #[\Override]
    public static function get_name() {
        return get_string('eventslotfilterconditionupdated', 'mod_quiz');
    }

    #[\Override]
    public function get_description() {
        return "The user with id '$this->userid' updated a slot using question references with " .
                "id '{$this->objectid}' and " .
                "slot number '{$this->other['slotnumber']}' " .
                "on page '{$this->other['page']}' " .
                "of the quiz with course module id '$this->contextinstanceid' " .
                "to use question context '{$this->other['questionscontextid']}' and " .
                "filter condition '{$this->other['filtercondition']}'.";
    }

    #[\Override]
    public function get_url() {
        return new url('/mod/quiz/edit.php', ['cmid' => $this->contextinstanceid]);
    }

    #[\Override]
    protected function validate_data() {
        $missing = fn(string $field) => !isset($this->other[$field]);
        $missingmembers = array_filter(['objectid'], fn(string $member): bool => !isset($this->$member));
        $missingfields = array_filter(['quizid', 'slotnumber', 'page', 'questionscontextid', 'filtercondition'], $missing);
        parent::validate_data();

        $errors = [
            ...array_map(fn(string $member): string => "The '$member' value must be set.", $missingmembers),
            ...array_map(fn(string $field): string => "The '$field' value must be set in other.", $missingfields),
        ];

        if ($errors) {
            throw new coding_exception("Errors in event data:\n\n" . implode("\n", $errors));
        }
    }

    #[\Override]
    public static function get_objectid_mapping() {
        return ['db' => 'quiz_slots', 'restore' => 'quiz_question_instance'];
    }

    #[\Override]
    public static function get_other_mapping() {
        $othermapped = [];
        $othermapped['quizid'] = ['db' => 'quiz', 'restore' => 'quiz'];

        return $othermapped;
    }
}
