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
 * Simple task to run the blog cron.
 */
class blog_cron_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskblogcron', 'admin');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG, $DB;

        $timenow = time();
        // Run external blog cron if needed.
        if (!empty($CFG->enableblogs) && $CFG->useexternalblogs) {
            require_once($CFG->dirroot . '/blog/lib.php');
            $sql = "timefetched < ? OR timefetched = 0";
            $externalblogs = $DB->get_records_select('blog_external', $sql, array($timenow - $CFG->externalblogcrontime));

            foreach ($externalblogs as $eb) {
                blog_sync_external_entries($eb);
            }
        }
        // Run blog associations cleanup.
        if (!empty($CFG->enableblogs) && $CFG->useblogassociations) {
            require_once($CFG->dirroot . '/blog/lib.php');
            // Delete entries whose contextids no longer exists.
            $DB->delete_records_select('blog_association', 'contextid NOT IN (SELECT id FROM {context})');
        }

    }

}
