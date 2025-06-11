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
 * @copyright  2016 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_crawler\local\url;
use tool_crawler\robot\crawler;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/constants.php');
/**
 * Perform one cron 'tick' of crawl processing
 *
 * Has limits of both how many URLs to crawl
 * and a soft time limit on total crawl time (the maxcrontime in the settings.)
 * During this time grab the first item from the queue if it's not locked and crawl it.
 * Uses locking to stop other instances of this task taking the same item.
 *
 * @param boolean $verbose show verbose feedback
 */
function tool_crawler_crawl($verbose = false) {

    global $CFG, $DB;

    $robot = new crawler();
    $url = new url();
    $config = $robot::get_config();

    if ($config->debugging) {
        $verbose = $config->debugging;
    }

    $crawlstart = $config->crawlstart;
    $crawlend   = $config->crawlend;

    // If we need to start a new crawl, add new items to the queue.
    if (!$crawlstart || $crawlstart <= $crawlend) {

        $start = time();
        set_config('crawlstart', $start, 'tool_crawler');

        if ($config->uselogs == 1) {
            $recentcourses = $robot->get_recentcourses();
            foreach ($recentcourses as $courseid) {
                $robot->mark_for_crawl($CFG->wwwroot . '/', 'course/view.php?id=' . $courseid, $courseid);
            }
        } else {
            $robot->mark_for_crawl($CFG->wwwroot.'/', $config->seedurl);
        }
        // Create a new history record.
        $history = new stdClass();
        $history->startcrawl = $start;
        $history->urls = 0;
        $history->links = 0;
        $history->broken = 0;
        $history->oversize = 0;
        $history->cronticks = 0;
        $history->id = $DB->insert_record('tool_crawler_history', $history);
    } else {
        $history = $DB->get_record('tool_crawler_history', array('startcrawl' => $crawlstart));
    }

    $cronstart = time();
    $cronstop = $cronstart + $config->maxcrontime;

    $hastime = $robot->process_queue($verbose);

    // If the queue is empty then mark the crawl as ended.
    if ($hastime) {
        // Time left over, which means the queue is empty!
        // Mark the crawl as ended.
        $history->endcrawl = time();
        set_config('crawlend', time(), 'tool_crawler');
    }
    $history->urls = $url->get_processed();
    $history->links = $robot->get_num_links();
    $history->broken = $robot->get_num_broken_urls();
    $history->oversize = $robot->get_num_oversize();
    $history->cronticks++;

    $DB->update_record('tool_crawler_history', $history);
}

/**
 * Get summary stats about a URL
 *
 * @param integer $courseid a course aid
 *
 * @return array of summary data
 */
function tool_crawler_summary($courseid) {

    global $DB;

    $result = array();
    $result['large']  = array();
    $result['nearby'] = array();

    // Breakdown counts of status codes by 200, 300, 400, 500.
    $result['broken']   = $DB->get_records_sql("
         SELECT substr(b.httpcode,0,2) code,
                count(substr(b.httpcode,0,2))
           FROM {tool_crawler_url}   b
      LEFT JOIN {tool_crawler_edge}  l ON l.b  = b.id
      LEFT JOIN {tool_crawler_url}   a ON l.a  = a.id
      LEFT JOIN {course}            c ON c.id = a.courseid
          WHERE a.courseid = :course
       GROUP BY substr(b.httpcode,0,2)
    ", array('course' => $courseid) );

    $e = (object) array('count' => 0);
    if (!array_key_exists('0', $result['broken'])) {
        $result['broken']['0'] = $e;
    }
    if (!array_key_exists('2', $result['broken'])) {
        $result['broken']['2'] = $e;
    }
    if (!array_key_exists('3', $result['broken'])) {
        $result['broken']['3'] = $e;
    }
    if (!array_key_exists('4', $result['broken'])) {
        $result['broken']['4'] = $e;
    }
    if (!array_key_exists('5', $result['broken'])) {
        $result['broken']['5'] = $e;
    }

    return $result;
}

/**
 * This function extends the course navigation
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the tool
 * @param context $coursecontext The context of the course
 */
function tool_crawler_extend_navigation_course($navigation, $course, $coursecontext) {
    $coursereports = $navigation->get('coursereports');
    if (!$coursereports) {
        return; // Course reports submenu in "course administration" not available.
    }

    if ($coursereports) {
        $coursereports->add(
            get_string('pluginname', 'tool_crawler'),
            new moodle_url('/admin/tool/crawler/report.php', array('report' => 'queued', 'course' => $course->id)),
            navigation_node::TYPE_CONTAINER,
            null,
            'linkchecker',
            new pix_icon('i/report', get_string('pluginname', 'tool_crawler'))
        );
    }
}
