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
 * Unit tests for (parts of) the custom SQL report.
 *
 * @package report_customsql
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group report_customsql
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../locallib.php');

class report_customsql_test extends advanced_testcase {
    public function test_get_week_starts_test() {
        $this->assertEquals(array(
                strtotime('00:00 7 November 2009'), strtotime('00:00 31 October 2009')),
                report_customsql_get_week_starts(strtotime('12:36 10 November 2009')));

        $this->assertEquals(array(
                strtotime('00:00 7 November 2009'), strtotime('00:00 31 October 2009')),
                report_customsql_get_week_starts(strtotime('00:00 7 November 2009')));

        $this->assertEquals(array(
                strtotime('00:00 7 November 2009'), strtotime('00:00 31 October 2009')),
                report_customsql_get_week_starts(strtotime('23:59 13 November 2009')));
    }

    public function test_get_month_starts_test() {
        $this->assertEquals(array(
                strtotime('00:00 1 November 2009'), strtotime('00:00 1 October 2009')),
                report_customsql_get_month_starts(strtotime('12:36 10 November 2009')));

        $this->assertEquals(array(
                strtotime('00:00 1 November 2009'), strtotime('00:00 1 October 2009')),
                report_customsql_get_month_starts(strtotime('00:00 1 November 2009')));

        $this->assertEquals(array(
                strtotime('00:00 1 November 2009'), strtotime('00:00 1 October 2009')),
                report_customsql_get_month_starts(strtotime('23:59 29 November 2009')));
    }

    public function test_report_customsql_get_element_type() {
        $this->assertEquals('date_time_selector', report_customsql_get_element_type('start_date'));
        $this->assertEquals('date_time_selector', report_customsql_get_element_type('startdate'));
        $this->assertEquals('date_time_selector', report_customsql_get_element_type('date_closed'));
        $this->assertEquals('date_time_selector', report_customsql_get_element_type('dateclosed'));

        $this->assertEquals('text', report_customsql_get_element_type('anythingelse'));
        $this->assertEquals('text', report_customsql_get_element_type('not_a_date_field'));
        $this->assertEquals('text', report_customsql_get_element_type('mandated'));
    }

    public function test_report_customsql_substitute_user_token() {
        $this->assertEquals('SELECT COUNT(*) FROM oh_quiz_attempts WHERE user = 123',
                report_customsql_substitute_user_token('SELECT COUNT(*) FROM oh_quiz_attempts '.
                        'WHERE user = %%USERID%%', 123));
    }

    public function test_report_customsql_capability_options() {
        $capoptions = array(
                'report/customsql:view' => get_string('anyonewhocanveiwthisreport', 'report_customsql'),
                'moodle/site:viewreports' => get_string('userswhocanviewsitereports', 'report_customsql'),
                'moodle/site:config' => get_string('userswhocanconfig', 'report_customsql'));
        $this->assertEquals($capoptions, report_customsql_capability_options());

    }

    public function test_report_customsql_runable_options() {
        $options = array('manual'  => get_string('manual', 'report_customsql'),
                         'daily'   => get_string('automaticallydaily', 'report_customsql'),
                         'weekly'  => get_string('automaticallyweekly', 'report_customsql'),
                         'monthly' => get_string('automaticallymonthly', 'report_customsql'));

        $this->assertEquals($options, report_customsql_runable_options());
    }

    public function test_report_customsql_daily_at_options() {
        $time = array();
        for ($h = 0; $h < 24; $h++) {
            $hour = ($h < 10) ? "0$h" : $h;
            $time[$h] = "$hour:00";
        }
        $this->assertEquals($time, report_customsql_daily_at_options());
    }

    public function test_report_customsql_email_options() {
        $options = array('emailnumberofrows' => get_string('emailnumberofrows', 'report_customsql'),
                'emailresults' => get_string('emailresults', 'report_customsql'));
        $this->assertEquals($options, report_customsql_email_options());
    }

    public function test_report_customsql_bad_words_list() {
        $options = array('ALTER', 'CREATE', 'DELETE', 'DROP', 'GRANT', 'INSERT', 'INTO', 'TRUNCATE', 'UPDATE');
        $this->assertEquals($options, report_customsql_bad_words_list());
    }

    public function test_report_customsql_contains_bad_word() {
        $string = 'DELETE * FROM prefix_user u WHERE u.id  > 0';
        $this->assertEquals(1, report_customsql_contains_bad_word($string));
    }

    public function test_report_customsql_get_ready_to_run_daily_reports() {
        global $DB;
        $this->resetAfterTest(true);

        $timenow = time();
        $dateparts = getdate($timenow);
        $currenthour = $dateparts['hours'];

        list($today, $yesterday) = report_customsql_get_daily_time_starts($timenow, $currenthour);

        // Test entry 1.
        // This report is supposed to run at the current hour (wehenver this test is run).
        // The last run time recorded in the database is acutally tomorrow(!)
        // relative to $timestamp. (Acutally timestamp is yesterday.)
        $lastrun = $today;
        $timestamp = $lastrun - ($today - $yesterday);
        $id = $this->create_a_database_row('daily', $currenthour, $lastrun, 'admin');
        $report = $DB->get_record('report_customsql_queries', array('id' => $id));
        $this->assertFalse(report_customsql_is_daily_report_ready($report, $timestamp));

        // Test entry 2.
        // This report is set to run at this hour, and was last run is that time
        // yesterday, and current time exactly the time the report should be run today.
        $lastrun = $yesterday;
        $timestamp = $today;
        $id = $this->create_a_database_row('daily', $currenthour - 1, $lastrun, 'admin, s1');
        $report = $DB->get_record('report_customsql_queries', array('id' => $id));
        $this->assertTrue(report_customsql_is_daily_report_ready($report, $timestamp));

        // Test entry 3.
        // This is the same as Test entry 2, except with no emails. At one point,
        // that made a difference, but it should not.
        $lastrun = $yesterday;
        $timestamp = $today;
        $id = $this->create_a_database_row('daily', $currenthour, $lastrun, '');
        $report = $DB->get_record('report_customsql_queries', array('id' => $id));
        $this->assertTrue(report_customsql_is_daily_report_ready($report, $timestamp));

        // Test entry 4.
        // This report is set to run next hour, and was last run at this hour
        // yesterday.
        $lastrun = $yesterday;
        $timestamp = $today;
        $id = $this->create_a_database_row('daily', $currenthour + 1, $lastrun, 's1');
        $report = $DB->get_record('report_customsql_queries', array('id' => $id));
        $this->assertFalse(report_customsql_is_daily_report_ready($report, $timestamp));

        // Verify that two reports are returned - the two assertTrues above.
        $this->assertEquals(2, count(report_customsql_get_ready_to_run_daily_reports($timenow)));

        // Test entry 5.
        // Report should run at 1:00am. We need to make sure that it does not get
        // run late in the day, say at 11pm. (This might be the case if we
        // had a 20-hour cut-off or something.
        list($oneam) = report_customsql_get_daily_time_starts($timenow, 1);
        list($elevenpm) = report_customsql_get_daily_time_starts($timenow, 23);
        $timenow = $elevenpm;
        $id = $this->create_a_database_row('daily', 1, $oneam, 's1');
        $report = $DB->get_record('report_customsql_queries', array('id' => $id));
        $this->assertFalse(report_customsql_is_daily_report_ready($report, $timenow));

        // Test entry 6.
        // Suppose that yesterday, cron got delayed, so this report that should
        // run at 02:00 was acutally run at 04:00. Now today, the report should
        // run at 02:00 again, to catch up.
        list($twoam) = report_customsql_get_daily_time_starts($timenow, 2);
        list($notused, $fouramyesterday) = report_customsql_get_daily_time_starts($timenow, 4);
        $timenow = $twoam;
        $id = $this->create_a_database_row('daily', 2, $fouramyesterday, 's1');
        $report = $DB->get_record('report_customsql_queries', array('id' => $id));
        $this->assertTrue(report_customsql_is_daily_report_ready($report, $timenow));
    }

    public function test_report_customsql_is_integer() {
        $this->assertTrue(report_customsql_is_integer(1));
        $this->assertTrue(report_customsql_is_integer('1'));
        $this->assertFalse(report_customsql_is_integer('frog'));
        $this->assertFalse(report_customsql_is_integer('2013-10-07'));
    }

    /**
     * Create an entry in 'report_customsql_queries' table and return the id
     * @param string $runable
     * @param string $at
     * @param int $lastrun
     * @param string $emailto
     */
    private function create_a_database_row($runable, $at, $lastrun, $emailto) {
        global $DB;
        $report = new stdClass();
        $report->displayname = 'all users on this test';
        $report->description = 'test description';
        $report->querysql = 'SELECT * FROM {report_customsql_queries} WHERE lastrun > 0';
        $report->queryparams = '';
        $report->capability = 'report/customsql:view';
        $report->lastrun = $lastrun;
        $report->lastexecutiontime = 7;
        $report->runable = $runable;
        $report->at = $at;
        $report->emailto = $emailto;
        $report->emailwhat = 'emailnumberofrows';
        $report->categoryid = 1;

        return $DB->insert_record('report_customsql_queries', $report);
    }
}
