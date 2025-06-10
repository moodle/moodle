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
 * tool_crawler
 *
 * @package    tool_crawler
 * @copyright  2019 Kristian Ringer <kristian.ringer@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_crawler\task;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/admin/tool/crawler/lib.php");

/**
 * crawl_task
 *
 * Creates a batch of crawls to be done in an ad hoc task.
 *
 * This gives us control over how parallel it is, and also when the workload is
 * processed (eg at night).
 *
 * @package    tool_crawler
 * @copyright  2016 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class crawl_task extends \core\task\scheduled_task {
    /**
     * Get task name
     */
    public function get_name() {
        return get_string('crawl_task', 'tool_crawler');
    }

    /**
     * Execute task
     */
    public function execute() {
        $config = \tool_crawler\robot\crawler::get_config();
        if ($config->disablebot === '1') {
            return;
        }
        $maxworkers = $config->max_workers;
        if (!$maxworkers) {
            return;
        }
        self::tool_crawler_add_adhoc_task($maxworkers);
    }
    /**
     * Add a batch of ad hoc crawl tasks
     * @param integer $maxworkers the limit of concurrent adhoc crawl tasks (workers)
     * @param boolean $verbose show verbose feedback
     */
    public function tool_crawler_add_adhoc_task($maxworkers, $verbose = false) {
        $crawltask = new \tool_crawler\task\adhoc_crawl_task();
        $crawltask->set_component('tool_crawler');
        if ($verbose) {
            echo "Adding $maxworkers ad hoc tasks to the queue";
        }
        // Queue the adhoc_crawl_task $maxworkers times.
        for ($i = 0; $i < $maxworkers; $i++) {
            $crawltask->set_custom_data(['worker' => $i]);
            // We set customdata so that the task API will ignore adding duplicates.
            \core\task\manager::queue_adhoc_task($crawltask, true); // Need true to check for duplicate workers.
        }
    }
}


