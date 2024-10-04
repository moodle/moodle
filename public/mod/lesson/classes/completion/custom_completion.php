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

namespace mod_lesson\completion;

use core_completion\activity_custom_completion;

/**
 * Activity custom completion subclass for the lesson activity.
 *
 * Contains the class for defining mod_lesson's custom completion rules
 * and fetching a lesson instance's completion statuses for a user.
 *
 * @package mod_lesson
 * @copyright Michael Hawkins <michaelh@moodle.com>
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

        switch ($rule) {
            case 'completiontimespent':
                $duration = $DB->get_field_sql(
                    "SELECT SUM(lessontime - starttime)
                       FROM {lesson_timer}
                      WHERE lessonid = :lessonid
                        AND userid = :userid",
                    ['userid' => $this->userid, 'lessonid' => $this->cm->instance]);

                $status = ($duration && $duration >= $this->cm->customdata['customcompletionrules']['completiontimespent']);
                break;
            case 'completionendreached':
                $status = $DB->record_exists('lesson_timer',
                    ['lessonid' => $this->cm->instance, 'userid' => $this->userid, 'completed' => 1]);
                break;
            default:
                $status = false;
                break;
        }

        return $status ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
    }

    /**
     * Fetch the list of custom completion rules that this module defines.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return [
            'completiontimespent',
            'completionendreached',
        ];
    }

    /**
     * Returns an associative array of the descriptions of custom completion rules.
     *
     * @return array
     */
    public function get_custom_rule_descriptions(): array {
        $timespent = format_time($this->cm->customdata['customcompletionrules']['completiontimespent'] ?? 0);

        return [
            'completiontimespent' => get_string('completiondetail:timespent', 'lesson', $timespent),
            'completionendreached' => get_string('completiondetail:reachend', 'lesson'),
        ];
    }

    /**
     * Returns an array of all completion rules, in the order they should be displayed to users.
     *
     * @return array
     */
    public function get_sort_order(): array {
        return [
            'completionview',
            'completiontimespent',
            'completionendreached',
            'completionusegrade',
            'completionpassgrade',
        ];
    }
}
