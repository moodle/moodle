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
 * Contains the class for fetching the important dates in mod_quiz for a given module instance and a user.
 *
 * @package   mod_quiz
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_quiz;

use core\activity_dates;

/**
 * Class for fetching the important dates in mod_quiz for a given module instance and a user.
 *
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dates extends activity_dates {

    /** @var int|null timeclose the activity closing date */
    private ?int $timeclose;

    /**
     * Returns a list of important dates in mod_quiz
     *
     * @return array
     */
    protected function get_dates(): array {
        global $DB;

        $timeopen = $this->cm->customdata['timeopen'] ?? null;
        $timeclose = $this->cm->customdata['timeclose'] ?? null;

        $useroverride = $DB->get_record('quiz_overrides', [
            'quiz' => $this->cm->instance,
            'userid' => $this->userid,
        ]);
        $overrides = $useroverride ? [$useroverride] : [];

        $groups = groups_get_user_groups($this->cm->course, $this->userid);
        if (!empty($groups[0])) {
            [$groupidsql, $params] = $DB->get_in_or_equal(array_values($groups[0]), SQL_PARAMS_NAMED);
            $params['quizid'] = $this->cm->instance;
            $overrides = array_merge($overrides, $DB->get_records_select(
                'quiz_overrides',
                "quiz = :quizid AND groupid {$groupidsql}",
                $params,
            ));
        }

        foreach ($overrides as $override) {
            if (isset($override->timeopen)) {
                $timeopen = empty($timeopen) ? $override->timeopen : min($timeopen, $override->timeopen);
            }
            if (isset($override->timeclose)) {
                $timeclose = empty($timeclose) || empty($override->timeclose) ? $override->timeclose :
                    max($timeclose, $override->timeclose);
            }
        }

        $this->timeclose = $timeclose ? (int) $timeclose : null;

        $now = time();
        $dates = [];

        if ($timeopen) {
            $openlabelid = $timeopen > $now ? 'activitydate:opens' : 'activitydate:opened';
            $dates[] = [
                'dataid' => 'timeopen',
                'label' => get_string($openlabelid, 'core_course'),
                'timestamp' => (int) $timeopen,
            ];
        }

        if ($timeclose) {
            $closelabelid = $timeclose > $now ? 'activitydate:closes' : 'activitydate:closed';
            $dates[] = [
                'dataid' => 'timeclose',
                'label' => get_string($closelabelid, 'core_course'),
                'timestamp' => (int) $timeclose,
            ];
        }

        return $dates;
    }

    /**
     * Returns the dues date data, if any.
     * @return int|null the close timestamp or null if not set.
     */
    public function get_due_date(): ?int {
        if (!isset($this->timeclose)) {
            $this->get_dates();
        }
        return $this->timeclose;
    }
}
