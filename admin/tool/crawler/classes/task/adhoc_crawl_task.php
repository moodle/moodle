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
 * adhoc_crawl_task
 *
 * Crawl the queue
 *
 * @package    tool_crawler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adhoc_crawl_task extends \core\task\adhoc_task {
    /**
     * Get task name
     */
    public function get_name() {
        return get_string('adhoc_crawl_task', 'tool_crawler');
    }

    /**
     * Execute task
     */
    public function execute() {
        if (\tool_crawler\robot\crawler::get_config()->disablebot === '1') {
            return;
        }
        tool_crawler_crawl();
    }
}


