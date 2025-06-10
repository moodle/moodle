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
 * Activity custom completion subclass for the adaptive quiz activity.
 *
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\completion;

use core_completion\activity_custom_completion;
use mod_adaptivequiz\local\attempt;

class custom_completion extends activity_custom_completion {

    /**
     * @inheritDoc
     */
    public function get_state(string $rule): int {
        $this->validate_rule($rule);

        return attempt::user_has_completed_on_quiz($this->cm->instance, $this->userid)
            ? COMPLETION_COMPLETE
            : COMPLETION_INCOMPLETE;
    }

    /**
     * @inheritDoc
     */
    public static function get_defined_custom_rules(): array {
        return ['completionattemptcompleted'];
    }

    /**
     * @inheritDoc
     */
    public function get_custom_rule_descriptions(): array {
        return ['completionattemptcompleted' => get_string('completionattemptcompletedcminfo', 'adaptivequiz')];
    }

    /**
     * @inheritDoc
     */
    public function get_sort_order(): array {
        return ['completionview', 'completionusegrade', 'completionattemptcompleted'];
    }
}
