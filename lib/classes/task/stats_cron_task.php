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
 * A scheduled task.
 *
 * @package    core
 * @copyright  2013 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

/**
 * Simple task to run the stats cron.
 */
class stats_cron_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskstatscron', 'admin');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG;

        // Run stats as at the end because they are known to take very long time on large sites.
        if (!empty($CFG->enablestats) and empty($CFG->disablestatsprocessing)) {
            require_once($CFG->dirroot.'/lib/statslib.php');
            // Process configured number of days as max (defaulting to 31).
            $maxdays = empty($CFG->statsruntimedays) ? 31 : abs($CFG->statsruntimedays);
            if (stats_cron_daily($maxdays)) {
                if (stats_cron_weekly()) {
                    if (stats_cron_monthly()) {
                        stats_clean_old();
                    }
                }
            }
            \core_php_time_limit::raise();
        }
    }
}
