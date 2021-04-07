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

namespace mod_quiz\completion;

use core_completion\activity_custom_completion;

/**
 * Activity custom completion subclass for the quiz activity.
 *
 * Class for defining mod_quiz's custom completion rules and fetching the completion statuses
 * of the custom completion rules for a given quiz instance and a user.
 *
 * @package   mod_quiz
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_completion extends activity_custom_completion {

    /**
     * Fetches the completion state for a given completion rule.
     *
     * @param string $rule The completion rule.
     * @return int The completion state.
     */
    public function get_state(string $rule): int {
        global $DB;

        $this->validate_rule($rule);

        $quiz = $DB->get_record('quiz', ['id' => $this->cm->instance], '*', MUST_EXIST);

        switch ($rule) {
            case 'completionpassorattemptsexhausted':
                $status = quiz_completion_check_passing_grade_or_all_attempts(
                    $this->cm->get_course(),
                    $this->cm,
                    $this->userid,
                    $quiz
                );
                break;
            case 'completionminattempts':
                $status = quiz_completion_check_min_attempts($this->userid, $quiz);
                break;
        }

        return empty($status) ? COMPLETION_INCOMPLETE : COMPLETION_COMPLETE;
    }

    /**
     * Fetch the list of custom completion rules that this module defines.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return [
            'completionpassorattemptsexhausted',
            'completionminattempts',
        ];
    }

    /**
     * Returns an associative array of the descriptions of custom completion rules.
     *
     * @return array
     */
    public function get_custom_rule_descriptions(): array {
        $minattempts = $this->cm->customdata['customcompletionrules']['completionminattempts'];

        $completionpassorattempts = $this->cm->customdata['customcompletionrules']['completionpassorattemptsexhausted'];
        if (!empty($completionpassorattempts['completionattemptsexhausted'])) {
            $passorallattemptslabel = get_string('completiondetail:passorexhaust', 'mod_quiz');
        } else {
            $passorallattemptslabel = get_string('completiondetail:passgrade', 'mod_quiz');
        }

        return [
            'completionpassorattemptsexhausted' => $passorallattemptslabel,
            'completionminattempts' => get_string('completiondetail:minattempts', 'mod_quiz', $minattempts),
        ];
    }
}
