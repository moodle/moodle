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

namespace tool_brickfield\task;

use tool_brickfield\accessibility;
use tool_brickfield\manager;
use tool_brickfield\scheduler;

/**
 * Task function to bulk process caches for accessibility checks.
 *
 * @package    tool_brickfield
 * @copyright  2020 Brickfield Education Labs https://www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class process_analysis_requests extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('processanalysisrequests', manager::PLUGINNAME);
    }

    /**
     * Execute the task
     */
    public function execute() {
        // If this feature has been disabled, do nothing.
        if (accessibility::is_accessibility_enabled()) {
            scheduler::process_scheduled_requests();
        }
    }
}
