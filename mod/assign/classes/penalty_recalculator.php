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

namespace mod_assign;

use core\context;
use mod_assign\task\recalculate_penalties;

/**
 * Recalculate penalties for the assignment.
 *
 * @package   mod_assign
 * @copyright 2025 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class penalty_recalculator extends \core_grades\penalty_recalculator {
    #[\Override]
    public static function recalculate_penalty(context $context, int $usermodified): void {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/mod/assign/locallib.php');

        switch ($context->contextlevel) {
            case CONTEXT_MODULE:
                $cmid = $context->instanceid;
                $cm = get_coursemodule_from_id('assign', $cmid, 0, false, MUST_EXIST);
                recalculate_penalties::queue($cm->instance, $usermodified);
                break;
            case CONTEXT_COURSE:
                $courseid = $context->instanceid;
                $assigns = $DB->get_records('assign', ['course' => $courseid]);
                foreach ($assigns as $assign) {
                    recalculate_penalties::queue($assign->id, $usermodified);
                }
                break;
        }
    }
}
