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

namespace mod_feedback;

use cm_info;
use stdClass;

/**
 * Class manager for feedback
 *
 * @package    mod_feedback
 * @copyright  2024 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * Get the template record from the template id
     *
     * @param int $templateid
     * @return stdClass
     */
    public static function get_template_record(int $templateid): stdClass {
        global $DB;

        return $DB->get_record('feedback_template', ['id' => $templateid], '*', MUST_EXIST);
    }

    /**
     * Check if the current user can see other users if in groups
     *
     * @param cm_info $cm
     * @return bool
     */
    public static function can_see_others_in_groups(cm_info $cm): bool {
        $canaccessallgroups = has_capability('moodle/site:accessallgroups', $cm->context);
        if ($canaccessallgroups) {
            return true;
        }
        $course = $cm->get_course();
        $groupmode = groups_get_activity_groupmode($cm, $course);
        $usergroups = groups_get_user_groups($course->id);
        return ($groupmode != SEPARATEGROUPS || !empty($usergroups['0']));
    }
}
