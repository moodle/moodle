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

namespace mod_questionnaire\completion;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/questionnaire/lib.php');

use coding_exception;
use core_completion\activity_custom_completion;
use moodle_exception;

/**
 * Activity custom completion subclass for the data activity.
 *
 * Class for defining mod_oucontent's custom completion rules and fetching the completion statuses
 * of the custom completion rules for a given data instance and a user.
 *
 * @package mod_questionnaire
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_completion extends activity_custom_completion {
    /**
     * Fetches the completion state for a given completion rule.
     *
     * @param string $rule
     * @return int
     */
    public function get_state(string $rule): int {
        $this->validate_rule($rule);
        $userid = $this->userid;
        $cm = $this->cm;
        $status = questionnaire_get_completion_state($cm, $userid, $rule);
        return $status ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
    }

    /**
     * Fetch the list of custom completion rules that this module defines.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return [
            'completionsubmit'
        ];
    }

    /**
     * Returns an associative array of the descriptions of custom completion rules.
     *
     * @return array
     */
    public function get_custom_rule_descriptions(): array {
        return [
            'completionsubmit' => get_string('completionsubmit', 'questionnaire')
        ];
    }

    /**
     * Returns an array of all completion rules, in the order they should be displayed to users.
     *
     * @return array
     */
    public function get_sort_order(): array {
        return [
            'completionsubmit',
        ];
    }
}
