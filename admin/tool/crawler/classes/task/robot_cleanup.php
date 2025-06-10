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
 * @copyright  2016 Suan Kan <suankan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_crawler\task;

defined('MOODLE_INTERNAL') || die();

/**
 * robot_cleanup
 *
 * @package    tool_crawler
 * @copyright  2016 Suan Kan <suankan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class robot_cleanup extends \core\task\scheduled_task {

    /**
     * Get task name
     */
    public function get_name() {
        return get_string('robotcleanup', 'tool_crawler');
    }

    /**
     * Finds and deletes all bad URLs which belong to previous scrape session, but not the current one.
     *
     * @param null $currenttime For unit tests purpose: we need to simulate execution of this task at any arbitrary time.
     */
    public function execute($currenttime = null) {
        global $DB;

        if (\tool_crawler\robot\crawler::get_config()->disablebot === '1') {
            return;
        }

        if (!$currenttime) {
            $currenttime = time();
        }

        // Throw and log event that robot_cleanup task was started.
        $event = \tool_crawler\event\robot_cleanup_started::create();
        $event->trigger();

        // The logic to remove old URLs from crawling history is:
        // Remove record if it's crawling had finished before the current crawling completed.
        // AND if it's crawling had finished before falling into the configured retention period.
        // In other words: by the time of execution of robot_cleanup task there might be current crawling process going.
        // Hence, we don't want to remove URLs belonging to the current crawling queue.
        $retentionperiod = \tool_crawler\robot\crawler::get_config()->retentionperiod;
        $lastcrawlend = \tool_crawler\robot\crawler::get_config()->crawlend;
        if ($retentionperiod) {
            $param = array(
                'currenttime' => $currenttime,
                'lastcrawlfinished' => $lastcrawlend,
                'expiredate' => $currenttime - $retentionperiod
            );
            $where = 'lastcrawled <= :currenttime
                  AND lastcrawled <= :lastcrawlfinished
                  AND lastcrawled <= :expiredate';
            $numrecsdeleted = $DB->count_records_select('tool_crawler_url', $where, $param);
            $DB->delete_records_select('tool_crawler_url', $where, $param);
        }

        // Throw and log event that robot_cleanup task was finished and pass number of deleted records.
        $eventdata = array(
            'other' => array(
                'numrecsdeleted' => $numrecsdeleted
            )
        );
        $event = \tool_crawler\event\robot_cleanup_completed::create($eventdata);
        $event->trigger();
    }
}
