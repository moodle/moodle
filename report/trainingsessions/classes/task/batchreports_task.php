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
 * A scheduled task for trainingsessions cron.
 *
 * @todo MDL-44734 This job will be split up properly.
 *
 * @package    report_trainingsessions
 * @category   report
 * @copyright  2014 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_trainingsessions\task;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');

class batchreports_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('batchreports_task', 'report_trainingsessions');
    }

    /**
     * Run trainingsessions cron.
     */
    public function execute() {

        // Requires community or pro version if available.
        $distrib = \report_trainingsessions_plugin_require('/report/trainingsessions/cronlib.php');

        if ($distrib == 'pro') {
            \report_trainingsessions_pro_crontask();
        } else {
            \report_trainingsessions_crontask();
        }
    }
}
