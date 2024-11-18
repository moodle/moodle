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
 * Synchronise plans from template learning paths.
 *
 * @package    block_iomad_learningpath
 * @copyright  2024 Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_learningpath\task;
defined('MOODLE_INTERNAL') || die();

use core_competency\api;
use core_competency\template_learningpath;

/**
 * Synchronise plans from template learningpaths.
 *
 *
 * @package    block_iomad_learningpaths
 * @copyright  2024 Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sync_plans_from_template_learningpaths_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('syncplanslearningpaths', 'block_iomad_learningpath');
    }

    /**
     * Do the job.
     */
    public function execute() {
        if (!api::is_enabled()) {
            return;
        }

        $missingplans = template_learningpath::get_all_missing_plans(self::get_last_run_time());

        foreach ($missingplans as $missingplan) {
            foreach ($missingplan['userids'] as $userid) {
                try {
                    api::create_plan_from_template($missingplan['template'], $userid);
                } catch (\Exception $e) {
                    debugging(sprintf('Exception caught while creating plan for user %d from template %d. Message: %s',
                        $userid, $missingplan['template']->get_id(), $e->getMessage()), DEBUG_DEVELOPER);
                }
            }
        }
    }
}
