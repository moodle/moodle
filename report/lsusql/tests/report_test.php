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

namespace report_lsusql;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/report/lsusql/locallib.php');

/**
 * Unit tests for (parts of) the custom SQL report.
 *
 * @package report_lsusql
 * @copyright 2009 The Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_test extends \advanced_testcase {

    /**
     * Data provider for test_get_week_starts
     *
     * @return array
     */
    public function get_week_starts_provider(): array {
        return [
            // Start weekday is Sunday.
            [0, '12:36 11 November 2009', '00:00 8 November 2009', '00:00 1 November 2009'],
            [0, '00:00 8 November 2009', '00:00 8 November 2009', '00:00 1 November 2009'],
            [0, '23:59 14 November 2009', '00:00 8 November 2009', '00:00 1 November 2009'],
            // Start weekday is Monday.
            [1, '12:36 6 November 2009', '00:00 2 November 2009', '00:00 26 October 2009'],
            [1, '00:00 2 November 2009', '00:00 2 November 2009', '00:00 26 October 2009'],
            [1, '23:59 8 November 2009', '00:00 2 November 2009', '00:00 26 October 2009'],
            // Start weekday is Saturday.
            [6, '12:36 10 November 2009', '00:00 7 November 2009', '00:00 31 October 2009'],
            [6, '00:00 7 November 2009', '00:00 7 November 2009', '00:00 31 October 2009'],
            [6, '23:59 13 November 2009', '00:00 7 November 2009', '00:00 31 October 2009'],
        ];
    }

    /**
     * Tests plugin get_week_starts method
     *
     * @param int $startwday
     * @param string $datestr
     * @param string $currentweek
     * @param string $lastweek
     *
     * @dataProvider get_week_starts_provider
     */
    public function test_get_week_starts(
            int $startwday, string $datestr, string $currentweek, string $lastweek): void {
        $this->resetAfterTest();

        set_config('startwday', $startwday, 'report_lsusql');

        $expected = [strtotime($currentweek), strtotime($lastweek)];
        $this->assertEquals($expected, report_lsusql_get_week_starts(strtotime($datestr)));
    }

    /**
     * Tests plugin get_week_starts method when using the calendar start of week default
     *
     * @param int $startwday
     * @param string $datestr
     * @param string $currentweek
     * @param string $lastweek
     * @return void
     *
     * @dataProvider get_week_starts_provider
     */
    public function test_get_week_starts_use_calendar_default(
            int $startwday, string $datestr, string $currentweek, string $lastweek): void {
        $this->resetAfterTest();

        // Setting this option to -1 will use the value from the site calendar.
        set_config('startwday', -1, 'report_lsusql');
        set_config('calendar_startwday', $startwday);

        $expected = [strtotime($currentweek), strtotime($lastweek)];
        $this->assertEquals($expected, report_lsusql_get_week_starts(strtotime($datestr)));
    }

    public function test_get_month_starts_test(): void {
        $this->assertEquals(array(
                strtotime('00:00 1 November 2009'), strtotime('00:00 1 October 2009')),
                report_lsusql_get_month_starts(strtotime('12:36 10 November 2009')));

        $this->assertEquals(array(
                strtotime('00:00 1 November 2009'), strtotime('00:00 1 October 2009')),
                report_lsusql_get_month_starts(strtotime('00:00 1 November 2009')));

        $this->assertEquals(array(
                strtotime('00:00 1 November 2009'), strtotime('00:00 1 October 2009')),
                report_lsusql_get_month_starts(strtotime('23:59 29 November 2009')));
    }

    public function test_report_lsusql_get_element_type(): void {
        $this->assertEquals('date_time_selector', report_lsusql_get_element_type('start_date'));
        $this->assertEquals('date_time_selector', report_lsusql_get_element_type('startdate'));
        $this->assertEquals('date_time_selector', report_lsusql_get_element_type('date_closed'));
        $this->assertEquals('date_time_selector', report_lsusql_get_element_type('dateclosed'));

        $this->assertEquals('text', report_lsusql_get_element_type('anythingelse'));
        $this->assertEquals('text', report_lsusql_get_element_type('not_a_date_field'));
        $this->assertEquals('text', report_lsusql_get_element_type('mandated'));
    }

    public function test_report_lsusql_substitute_user_token(): void {
        $this->assertEquals('SELECT COUNT(*) FROM oh_quiz_attempts WHERE user = 123',
                report_lsusql_substitute_user_token('SELECT COUNT(*) FROM oh_quiz_attempts '.
                        'WHERE user = %%USERID%%', 123));
    }

    public function test_report_lsusql_capability_options(): void {
        $capoptions = array(
                'report/lsusql:view' => get_string('anyonewhocanveiwthisreport', 'report_lsusql'),
                'moodle/site:viewreports' => get_string('userswhocanviewsitereports', 'report_lsusql'),
                'moodle/site:config' => get_string('userswhocanconfig', 'report_lsusql'));
        $this->assertEquals($capoptions, report_lsusql_capability_options());

    }

    public function test_report_lsusql_runable_options(): void {
        $options = array('manual'  => get_string('manual', 'report_lsusql'),
                         'daily'   => get_string('automaticallydaily', 'report_lsusql'),
                         'weekly'  => get_string('automaticallyweekly', 'report_lsusql'),
                         'monthly' => get_string('automaticallymonthly', 'report_lsusql'));

        $this->assertEquals($options, report_lsusql_runable_options());
    }

    public function test_report_lsusql_daily_at_options(): void {
        $time = array();
        for ($h = 0; $h < 24; $h++) {
            $hour = ($h < 10) ? "0$h" : $h;
            $time[$h] = "$hour:00";
        }
        $this->assertEquals($time, report_lsusql_daily_at_options());
    }

    public function test_report_lsusql_email_options(): void {
        $options = array('emailnumberofrows' => get_string('emailnumberofrows', 'report_lsusql'),
                'emailresults' => get_string('emailresults', 'report_lsusql'));
        $this->assertEquals($options, report_lsusql_email_options());
    }

    public function test_report_lsusql_bad_words_list(): void {
        $options = array('ALTER', 'CREATE', 'DELETE', 'DROP', 'GRANT', 'INSERT', 'INTO', 'TRUNCATE', 'UPDATE');
        $this->assertEquals($options, report_lsusql_bad_words_list());
    }

    public function test_report_lsusql_contains_bad_word(): void {
        $string = 'DELETE * FROM prefix_user u WHERE u.id  > 0';
        $this->assertEquals(1, report_lsusql_contains_bad_word($string));
    }

    public function test_report_lsusql_get_ready_to_run_daily_reports(): void {
        global $DB;
        $this->resetAfterTest(true);

        $timenow = time();
        $dateparts = getdate($timenow);
        $currenthour = $dateparts['hours'];

        [$today, $yesterday] = report_lsusql_get_daily_time_starts($timenow, $currenthour);

        // Test entry 1.
        // This report is supposed to run at the current hour (wehenver this test is run).
        // The last run time recorded in the database is acutally tomorrow(!)
        // relative to $timestamp. (Acutally timestamp is yesterday).
        $lastrun = $today;
        $timestamp = $lastrun - ($today - $yesterday);
        $id = $this->create_a_database_row('daily', $currenthour, $lastrun, null);
        $report = $DB->get_record('report_lsusql_queries', array('id' => $id));
        $this->assertFalse(report_lsusql_is_daily_report_ready($report, $timestamp));

        // Test entry 2.
        // This report is set to run at this hour, and was last run is that time
        // yesterday, and current time exactly the time the report should be run today.
        $lastrun = $yesterday;
        $timestamp = $today;
        $id = $this->create_a_database_row('daily', $currenthour - 1, $lastrun, null);
        $report = $DB->get_record('report_lsusql_queries', array('id' => $id));
        $this->assertTrue(report_lsusql_is_daily_report_ready($report, $timestamp));

        // Test entry 3.
        // This is the same as Test entry 2, except with no emails. At one point,
        // that made a difference, but it should not.
        $lastrun = $yesterday;
        $timestamp = $today;
        $id = $this->create_a_database_row('daily', $currenthour, $lastrun, null);
        $report = $DB->get_record('report_lsusql_queries', array('id' => $id));
        $this->assertTrue(report_lsusql_is_daily_report_ready($report, $timestamp));

        // Test entry 4.
        // This report is set to run next hour, and was last run at this hour
        // yesterday.
        $lastrun = $yesterday;
        $timestamp = $today;
        $id = $this->create_a_database_row('daily', $currenthour + 1, $lastrun, null);
        $report = $DB->get_record('report_lsusql_queries', array('id' => $id));
        $this->assertFalse(report_lsusql_is_daily_report_ready($report, $timestamp));

        // Verify that two reports are returned - the two assertTrues above.
        $this->assertEquals(2, count(report_lsusql_get_ready_to_run_daily_reports($timenow)));

        // Test entry 5.
        // Report should run at 1:00am. We need to make sure that it does not get
        // run late in the day, say at 11pm. (This might be the case if we
        // had a 20-hour cut-off or something.
        [$oneam] = report_lsusql_get_daily_time_starts($timenow, 1);
        [$elevenpm] = report_lsusql_get_daily_time_starts($timenow, 23);
        $timenow = $elevenpm;
        $id = $this->create_a_database_row('daily', 1, $oneam, null);
        $report = $DB->get_record('report_lsusql_queries', array('id' => $id));
        $this->assertFalse(report_lsusql_is_daily_report_ready($report, $timenow));

        // Test entry 6.
        // Suppose that yesterday, cron got delayed, so this report that should
        // run at 02:00 was acutally run at 04:00. Now today, the report should
        // run at 02:00 again, to catch up.
        [$twoam] = report_lsusql_get_daily_time_starts($timenow, 2);
        [, $fouramyesterday] = report_lsusql_get_daily_time_starts($timenow, 4);
        $timenow = $twoam;
        $id = $this->create_a_database_row('daily', 2, $fouramyesterday, null);
        $report = $DB->get_record('report_lsusql_queries', array('id' => $id));
        $this->assertTrue(report_lsusql_is_daily_report_ready($report, $timenow));
    }

    public function test_report_lsusql_is_integer(): void {
        $this->assertTrue(report_lsusql_is_integer(1));
        $this->assertTrue(report_lsusql_is_integer('1'));
        $this->assertFalse(report_lsusql_is_integer('frog'));
        $this->assertFalse(report_lsusql_is_integer('2013-10-07'));
    }

    public function test_report_lsusql_get_table_headers(): void {
        $rawheaders = [
                'String date',
                'Date date',
                'URL to link',
                'Link text',
                'Link text link url',
                'Not link',
                'Just a link url',
                'Not link link url',
                'HTML should be escaped',
        ];

        [$headers, $linkcolumns] = report_lsusql_get_table_headers($rawheaders);

        $this->assertEquals([
                'String date',
                'Date date',
                'URL to link',
                'Link text',
                'Not link',
                'Just a link url',
                'HTML should be escaped'], $headers);
        $this->assertEquals([3 => 4, 4 => -1, 5 => 7, 7 => -1], $linkcolumns);
    }

    public function test_report_lsusql_pretify_column_names(): void {
        $row = new \stdClass();
        $row->column = 1;
        $row->column_url = 2;
        $row->column_3 = 3;
        $query = "SELECT 1 AS First, 2 AS Column_URL, 3 AS column_3";
        $this->assertEquals(['column', 'Column URL', 'column 3'],
                report_lsusql_pretify_column_names($row, $query));

    }

    public function test_report_lsusql_pretify_column_names_multi_line(): void {
        $row = new \stdClass();
        $row->column = 1;
        $row->column_url = 2;
        $row->column_3 = 3;
        $query = "SELECT
                         1 AS First,
                         2 AS Column_URL,
                         3 AS column_3
                    FROM table";
        $this->assertEquals(['column', 'Column URL', 'column 3'],
                report_lsusql_pretify_column_names($row, $query));

    }

    public function test_report_lsusql_pretify_column_names_same_name_diff_capitialisation(): void {
        $row = new \stdClass();
        $row->course = 'B747-19B';
        $query = "SELECT t.course AS Course
                    FROM table";
        $this->assertEquals(['Course'],
                report_lsusql_pretify_column_names($row, $query));

    }

    public function test_report_lsusql_pretify_column_names_issue(): void {
        $row = new \stdClass();
        $row->website = 'B747-19B';
        $row->website_link_url = '%%WWWROOT%%/course/view.php%%Q%%id=123';
        $row->subpage = 'Self-referential nightmare';
        $row->subpage_link_url = '%%WWWROOT%%/mod/subpage/view.php%%Q%%id=4567';

        $query = "
                SELECT c.shortname AS Website,
                       '%%WWWROOT%%/course/view.php%%Q%%id=' || c.id AS Website_link_url,
                       s.name AS Subpage,
                       '%%WWWROOT%%/mod/subpage/view.php%%Q%%id=' || cm.id AS Subpage_link_url

                  FROM {subpage_sections} ss
                  JOIN {subpage} s ON s.id = ss.subpageid
                  JOIN {course_sections} cs ON cs.id = ss.sectionid
                  JOIN {course_modules} cm ON cm.instance = s.id
                  JOIN {modules} mod ON mod.id = cm.module
                  JOIN {course} c ON c.id = cm.course

                 WHERE mod.name = 'subpage'
                   AND ',' || cs.sequence || ',' LIKE '%,' || cm.id || ',%'

              ORDER BY website, subpage";

        $this->assertEquals(['Website', 'Website link url', 'Subpage', 'Subpage link url'],
                report_lsusql_pretify_column_names($row, $query));

    }

    public function test_report_lsusql_display_row(): void {
        $rawdata = [
                'Not a date',
                '2018-11-22 00:00:00+00',
                'https://example.com/1',
                'This is a link',
                'https://example.com/2',
                'Non-link, invalid URL',
                'https://example.com/3',
                'Not a URL',
                '<b>Raw HTML</b>',
        ];
        $linkcolumns = [3 => 4, 4 => -1, 5 => 7, 7 => -1];

        $this->assertEquals([
                'Not a date',
                '2018-11-22 00:00:00+00',
                '<a href="https://example.com/1">https://example.com/1</a>',
                '<a href="https://example.com/2">This is a link</a>',
                'Non-link, invalid URL',
                '<a href="https://example.com/3">https://example.com/3</a>',
                '&lt;b&gt;Raw HTML&lt;/b&gt;'], report_lsusql_display_row($rawdata, $linkcolumns));
    }

    /**
     * Test plugin emailing of reports
     *
     * @return void
     */
    public function test_report_lsusql_email_report(): void {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();

        $id = $this->create_a_database_row('daily', 2, 1, $user->id);
        $report = $DB->get_record('report_lsusql_queries', ['id' => $id]);

        // Give our test user the capability to view the report.
        $userrole = $DB->get_record('role', ['shortname' => 'user']);
        role_change_permission($userrole->id, \context_system::instance(), $report->capability, CAP_ALLOW);

        // Send the report, catch everything sent through message_send API.
        $sink = $this->redirectMessages();

        report_lsusql_email_report($report);

        $messages = $sink->get_messages();
        $this->assertCount(1, $messages);

        $message = reset($messages);
        $this->assertEquals(\core_user::get_support_user()->id, $message->useridfrom);
        $this->assertEquals($user->id, $message->useridto);

        $expectedsubject = get_string('emailsubjectnodata', 'report_lsusql',
            report_lsusql_plain_text_report_name($report));
        $this->assertEquals($expectedsubject, $message->subject);

        // Now check subject if the report has one row.
        $cvsfilename = $CFG->tempdir . '/res.cvs';
        file_put_contents($cvsfilename, "Col1,Col2\r\nFrog,Toad");

        report_lsusql_email_report($report, $cvsfilename);
        $messages = $sink->get_messages();
        $message = end($messages);
        $this->assertEquals('Query all users on this test [1 row]', $message->subject);

        // And more rows.
        $cvsfilename = $CFG->tempdir . '/res.cvs';
        file_put_contents($cvsfilename, "Col1,Col2\r\nFrog,Tadpole\r\nCat,Kitten\r\nDog,Puppy");

        report_lsusql_email_report($report, $cvsfilename);
        $messages = $sink->get_messages();
        $message = end($messages);
        $this->assertEquals('Query all users on this test [3 rows]', $message->subject);

        $sink->close();
    }

    /**
     * Test plugin downloading of reports.
     *
     * @return void
     */
    public function test_report_custom_sql_download_report_url(): void {
        global $DB;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();

        $id = $this->create_a_database_row('daily', 2, 1, $user->id);
        $report = $DB->get_record('report_lsusql_queries', ['id' => $id]);

        // Give our test user the capability to view the report.
        $userrole = $DB->get_record('role', ['shortname' => 'user']);
        role_change_permission($userrole->id, \context_system::instance(), $report->capability, CAP_ALLOW);

        // Test download url with the required dataformat param.
        $urlparams = [
            'dataformat' => 'csv'
        ];
        $baseurl = "https://www.example.com/moodle/pluginfile.php";
        $path = "/1/report_lsusql/download/";

        $url = report_lsusql_downloadurl($report->id, $urlparams);
        $expected = "$baseurl$path$report->id?dataformat=csv";
        $this->assertEquals($expected, $url->out(false));

        // Add some custom parameters to the params.
        $timenow = time();
        $urlparams['timestamp'] = $timenow;
        $urlparams['foo'] = 'bar';

        $url = report_lsusql_downloadurl($report->id, $urlparams);
        $expected = "$baseurl$path$report->id?dataformat=csv&timestamp=$timenow&foo=bar";
        $this->assertEquals($expected, $url->out(false));
    }

    public function test_report_lsusql_write_csv_row(): void {
        global $CFG;
        $this->resetAfterTest();

        make_temp_directory('lsusqltest');
        $path = $CFG->tempdir . '/lsusqltest/testoutput.csv';
        $handle = fopen($path, 'w');
        report_lsusql_write_csv_row($handle, ['"\\"', '","']);
        $this->assertEquals('"""\\""",""","""' . "\r\n", file_get_contents($path));
    }

    /**
     * Create an entry in 'report_lsusql_queries' table and return the id
     *
     * @param string $runable
     * @param string $at
     * @param int $lastrun
     * @param string|null $emailto
     *
     * @return int the new query id.
     */
    private function create_a_database_row(string $runable, string $at, int $lastrun, ?string $emailto): int {
        global $DB;
        $report = new \stdClass();
        $report->displayname = 'all users on this test';
        $report->description = 'test description';
        $report->querysql = 'SELECT * FROM {report_lsusql_queries} WHERE lastrun > 0';
        $report->queryparams = '';
        $report->capability = 'report/lsusql:view';
        $report->lastrun = $lastrun;
        $report->lastexecutiontime = 7;
        $report->runable = $runable;
        $report->at = $at;
        $report->emailto = $emailto;
        $report->emailwhat = 'emailnumberofrows';
        $report->categoryid = 1;

        return $DB->insert_record('report_lsusql_queries', $report);
    }
}
