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
 * Synchronise plans from template cohorts.
 *
 * @package    tool_lp
 * @copyright  2015 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp\task;
defined('MOODLE_INTERNAL') || die();

use tool_lp\api;
use tool_lp\template_cohort;

/**
 * Synchronise plans from template cohorts.
 *
 *
 * @package    tool_lp
 * @copyright  2015 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sync_plans_from_template_cohorts_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('syncplanscohorts', 'tool_lp');
    }

    /**
     * Do the job.
     */
    public function execute() {

        $missingplans = template_cohort::get_all_missing_plans();

        foreach ($missingplans as $missingplan) {
            foreach ($missingplan['userids'] as $userid) {
                api::create_plan_from_template($missingplan['template'], $userid);
            }
        }
    }
}
