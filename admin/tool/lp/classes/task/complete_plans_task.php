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
 * Complete plans task.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp\task;
defined('MOODLE_INTERNAL') || die();

use tool_lp\api;
use tool_lp\plan;

/**
 * Complete plans task class.
 *
 * This task should run relatively often because the plans due dates can be set at
 * any time of the day in any timezone.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class complete_plans_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('completeplanstask', 'tool_lp');
    }

    /**
     * Do the job.
     */
    public function execute() {
        $records = plan::get_recordset_for_due_and_incomplete();
        foreach ($records as $record) {
            $plan = new plan(0, $record);
            api::complete_plan($plan);
        }
        $records->close();
    }

}
