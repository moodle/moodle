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

use context_module;
use core_completion\activity_custom_completion;
use mod_quiz\quiz_settings;
use mod_quiz\access_manager;

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
     * Check passing grade (or no attempts left) requirement for completion.
     *
     * @return bool True if the passing grade (or no attempts left) requirement is disabled or met.
     */
    protected function check_passing_grade_or_all_attempts(): bool {
        global $CFG;
        require_once($CFG->libdir . '/gradelib.php');

        $completionpassorattempts = $this->cm->customdata['customcompletionrules']['completionpassorattemptsexhausted'];

        if (empty($completionpassorattempts['completionpassgrade'])) {
            return true;
        }

        if ($this->completionstate &&
                isset($this->completionstate['passgrade']) &&
                $this->completionstate['passgrade'] == COMPLETION_COMPLETE_PASS) {
            return true;
        }

        // If a passing grade is required and exhausting all available attempts is not accepted for completion,
        // then this quiz is not complete.
        if (empty($completionpassorattempts['completionattemptsexhausted'])) {
            return false;
        }

        // Check if all attempts are used up.
        $attempts = quiz_get_user_attempts($this->cm->instance, $this->userid, 'finished', true);
        if (!$attempts) {
            return false;
        }
        $lastfinishedattempt = end($attempts);
        $context = context_module::instance($this->cm->id);
        $quizobj = quiz_settings::create((int) $this->cm->instance, $this->userid);
        $accessmanager = new access_manager(
            $quizobj,
            time(),
            has_capability('mod/quiz:ignoretimelimits', $context, $this->userid, false)
        );

        return $accessmanager->is_finished(count($attempts), $lastfinishedattempt);
    }

    /**
     * Check minimum attempts requirement for completion.
     *
     * @return bool True if minimum attempts requirement is disabled or met.
     */
    protected function check_min_attempts() {
        $minattempts = $this->cm->customdata['customcompletionrules']['completionminattempts'];
        if (!$minattempts) {
            return true;
        }

        // Check if the user has done enough attempts.
        $attempts = quiz_get_user_attempts($this->cm->instance, $this->userid, 'finished', true);
        return $minattempts <= count($attempts);
    }

    /**
     * Fetches the completion state for a given completion rule.
     *
     * @param string $rule The completion rule.
     * @return int The completion state.
     */
    public function get_state(string $rule): int {
        $this->validate_rule($rule);

        switch ($rule) {
            case 'completionpassorattemptsexhausted':
                $status = static::check_passing_grade_or_all_attempts();
                break;
            case 'completionminattempts':
                $status = static::check_min_attempts();
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
        $minattempts = $this->cm->customdata['customcompletionrules']['completionminattempts'] ?? 0;
        $description['completionminattempts'] = get_string('completiondetail:minattempts', 'mod_quiz', $minattempts);

        // Completion pass grade is now part of core. Only show the following if it's combined with min attempts.
        $completionpassorattempts = $this->cm->customdata['customcompletionrules']['completionpassorattemptsexhausted'] ?? [];
        if (!empty($completionpassorattempts['completionattemptsexhausted'])) {
            $description['completionpassorattemptsexhausted'] = get_string('completiondetail:passorexhaust', 'mod_quiz');
        }

        return $description;
    }

    /**
     * Returns an array of all completion rules, in the order they should be displayed to users.
     *
     * @return array
     */
    public function get_sort_order(): array {
        return [
            'completionview',
            'completionminattempts',
            'completionusegrade',
            'completionpassgrade',
            'completionpassorattemptsexhausted',
        ];
    }
}
