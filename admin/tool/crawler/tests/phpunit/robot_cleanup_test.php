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
 * Unit tests for link crawler robot
 *
 * @package    tool_crawler
 * @copyright  2016 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_crawler\local\url;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden');

/**
 * Unit test for scheduled task robot_cleanup.
 *
 * It sets Retention Period as 1 week and then creates sample records in
 * table {tool_crawler_url} which are deliberately older then retention period.
 * Then it executes the robot_cleanup scheduled task and verifies that old records have been deleted.
 *
 * @package    tool_crawler
 * @copyright  2016 Suan Kan <suankan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_crawler_robot_cleanup_test extends advanced_testcase {

    /**
     * Prepare the config options for plugin which are used for robot_cleanup task logic
     *
     * @throws coding_exception
     */
    protected function setUp(): void {
        global $DB;

        $this->resetAfterTest(true);
        $this->robot = new \tool_crawler\robot\crawler();
        set_config('crawlend', strtotime("16-05-2016 14:51:00"), 'tool_crawler');
        set_config('retentionperiod', 600, 'tool_crawler');

        // Add 3 test records to table {tool_crawler_url}: 2 old ones and 1 item not older than configured retention period.
        $dataobjects = array(
            array(
                'url' => 'http://cqu.ubox001.com/course/index.php',
                'externalurl' => 0,
                'timecreated' => strtotime("16-05-2016 10:00:00"),
                'lastcrawled' => strtotime("16-05-2016 11:20:00"),
                'needscrawl' => strtotime("17-05-2017 10:00:00"),
                'httpcode' => 200,
                'mimetype' => 'text/html',
                'title' => 'CQU: All courses',
                'downloadduration' => 0.23,
                'filesize' => 44003,
                'filesizestatus' => TOOL_CRAWLER_FILESIZE_EXACT,
                'redirect' => null,
                'courseid' => 1,
                'contextid' => 1,
                'cmid' => null,
                'ignoreduserid' => null,
                'ignoredtime' => null,
                'httpmsg' => 'OK',
                'errormsg' => null
            ),
            array(
                'url' => 'http://moodle.org/',
                'externalurl' => 1,
                'timecreated' => strtotime("15-05-2016 10:00:00"),
                'lastcrawled' => strtotime("16-05-2016 14:49:59"),
                'needscrawl' => strtotime("17-05-2017 10:00:00"),
                'httpcode' => 200,
                'mimetype' => 'text/html',
                'title' => 'Moodle - Open-source learning platform | Moodle.org',
                'downloadduration' => 1.53,
                'filesize' => 56887,
                'filesizestatus' => TOOL_CRAWLER_FILESIZE_EXACT,
                'redirect' => 'https://moodle.org/',
                'courseid' => null,
                'contextid' => null,
                'cmid' => null,
                'ignoreduserid' => null,
                'ignoredtime' => null,
                'httpmsg' => 'Moved Permanently',
                'errormsg' => null
            ),
            array(
                'url' => 'http://cqu.ubox001.com/course/index.php?categoryid=1',
                'externalurl' => 0,
                'timecreated' => strtotime("16-05-2016 10:00:00"),
                'lastcrawled' => strtotime("16-05-2016 14:50:01"),
                'needscrawl' => strtotime("17-05-2017 10:00:00"),
                'httpcode' => 200,
                'mimetype' => 'text/html',
                'title' => 'CQU: Miscellaneous',
                'downloadduration' => 0.24,
                'filesize' => 45301,
                'filesizestatus' => TOOL_CRAWLER_FILESIZE_EXACT,
                'redirect' => null,
                'courseid' => 1,
                'contextid' => 3,
                'cmid' => null,
                'ignoreduserid' => null,
                'ignoredtime' => null,
                'httpmsg' => 'OK',
                'errormsg' => null
            )
        );

        try {
            foreach ($dataobjects as $dataobject) {
                $persistent = new url(0, (object)$dataobject);
                $persistent->create();
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    /**
     * Read plugin config params.
     * Execute robot_cleanup scheduled task.
     * Check if only 1 record (out of 3 configured above) is left in table {tool_crawler_url}.
     */
    public function test_robot_cleanup() {
        global $DB;

        // Expect the task to cleanup 2 records and leave 1.
        $cleanuptask = new \tool_crawler\task\robot_cleanup();
        // Simulate execution of robot_cleanup task at "16-05-2016 15:00:00" by passing this time as parameter.
        $cleanuptask->execute(strtotime("16-05-2016 15:00:00"));

        $count = $DB->count_records_select('tool_crawler_url', '');
        $this->assertEquals(1, $count);
    }
}
