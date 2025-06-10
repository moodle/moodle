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
 * Admin report GUI
 *
 * @package    tool_crawler
 * @copyright  2016 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('locallib.php');

require_login(null, false);

$report     = optional_param('report',  '', PARAM_ALPHANUMEXT);
$page       = optional_param('page',    0,  PARAM_INT);
$perpage    = optional_param('perpage', 50, PARAM_INT);
$courseid   = optional_param('course',  0,  PARAM_INT);
$retryid    = optional_param('retryid', 0,  PARAM_INT);
$start = $page * $perpage;

$sqlfilter = '';

$navurl = new moodle_url('/admin/tool/crawler/report.php', array(
    'report' => $report,
    'course' => $courseid
));
$baseurl = new moodle_url('/admin/tool/crawler/report.php', array(
    'perpage' => $perpage,
    'report' => $report,
    'course' => $courseid
));

if ($courseid) {
    // If course then this is an a course editor report.
    $course = get_course($courseid);
    require_login($course);

    $coursecontext = context_course::instance($courseid);
    require_capability('moodle/course:update', $coursecontext);

    $coursename = format_string($course->fullname, true, array('context' => $coursecontext));
    $PAGE->set_context($coursecontext);
    $PAGE->set_url($navurl);
    $PAGE->set_title( get_string($report, 'tool_crawler') );
    $PAGE->set_heading($coursename);
    $PAGE->set_pagelayout('incourse');
    $PAGE->add_body_class('limitedwidth');
    $sqlfilter = ' AND c.id = '.$courseid;

} else {

    // If no course then this is an admin only report.
    require_capability('moodle/site:config', context_system::instance());
    admin_externalpage_setup('tool_crawler_'.$report);
}
echo $OUTPUT->header();

require('tabs.php');
echo $tabs;

if ($retryid) {
    $persistent = new \tool_crawler\local\url();
    $persistent->reset_for_recrawl($retryid);
}

$datetimeformat = get_string('strftimerecentsecondshtml', 'tool_crawler');

if ($report == 'broken') {

    $sql = " FROM {tool_crawler_url}  b
       LEFT JOIN {tool_crawler_edge} l ON l.b = b.id
       LEFT JOIN {tool_crawler_url}  a ON l.a = a.id
       LEFT JOIN {course} c ON c.id = a.courseid
           WHERE b.httpcode != ? $sqlfilter";

    $opts = array('200');
    $data  = $DB->get_records_sql("SELECT concat(b.id, '-', l.id, '-', a.id) AS id,
                                          b.url target,
                                          b.httpcode,
                                          b.httpmsg,
                                          b.errormsg,
                                          b.lastcrawled,
                                          b.priority,
                                          b.id AS toid,
                                          l.id linkid,
                                          l.text,
                                          a.url,
                                          a.title,
                                          a.redirect,
                                          a.courseid,
                                          c.shortname $sql
                                 ORDER BY httpcode DESC,
                                          c.shortname ASC",
                                          $opts,
                                          $start,
                                          $perpage);

    $count = $DB->get_field_sql  ("SELECT count(*) AS count" . $sql, $opts);

    $table = new html_table();
    $table->head = array(
        '',
        get_string('lastcrawledtime', 'tool_crawler'),
        get_string('priority', 'tool_crawler'),
        get_string('response', 'tool_crawler'),
        get_string('broken', 'tool_crawler'),
        get_string('frompage', 'tool_crawler')
    );
    if (!$courseid) {
        array_push($table->head, get_string('course', 'tool_crawler'));
    }
    $table->data = array();
    foreach ($data as $row) {
        $text = trim($row->text);
        if ($text == "") {
            $text = get_string('missing', 'tool_crawler');
            $text = htmlspecialchars($text, ENT_NOQUOTES | ENT_HTML401);
            // May add a bit of markup here so that the user can differentiate the "missing" message from an equal link text.
        } else {
            $text = htmlspecialchars($text, ENT_NOQUOTES | ENT_HTML401);
        }
        $data = array(
            html_writer::link(new moodle_url($baseurl, array('retryid' => $row->toid )),
                get_string('retry', 'tool_crawler')),
            userdate($row->lastcrawled, $datetimeformat),
            tool_crawler_priority_level($row->priority),
            tool_crawler_http_code($row),
            tool_crawler_link($row->target, $text, $row->redirect, true),
            tool_crawler_link($row->url, $row->title, $row->redirect)
        );
        if (!$courseid) {
            $escapedshortname = htmlspecialchars($row->shortname, ENT_NOQUOTES | ENT_HTML401);
            array_push($data, html_writer::link('/course/view.php?id='.$row->courseid, $escapedshortname) );
        }
        $table->data[] = $data;
    }

} else if ($report == 'queued') {

    $sql = " FROM {tool_crawler_url} a
       LEFT JOIN {course} c ON c.id = a.courseid
           WHERE (a.lastcrawled IS NULL OR a.lastcrawled < needscrawl)
                 $sqlfilter";

    $opts = array();
    $data  = $DB->get_records_sql("SELECT a.id,
                                          a.url target,
                                          a.title,
                                          a.redirect,
                                          a.lastcrawled,
                                          a.needscrawl,
                                          a.courseid,
                                          a.priority,
                                          c.shortname $sql
                                 ORDER BY a.priority DESC,
                                          a.needscrawl ASC,
                                          a.id ASC",
                                          $opts,
                                          $start,
                                          $perpage);

    $count = $DB->get_field_sql  ("SELECT count(*) AS count" . $sql, $opts);

    $table = new html_table();

    $table->head = array(
        get_string('whenqueued', 'tool_crawler'),
        get_string('url', 'tool_crawler'),
        get_string('priority', 'tool_crawler'),
    );

    if (!$courseid) {
        array_push($table->head, get_string('incourse', 'tool_crawler'));
    }
    $table->data = array();
    foreach ($data as $row) {
        $title = trim($row->title);
        if ($title == "") {
            $title = get_string('notyetknown', 'tool_crawler');
        }
        $data = array(
            userdate($row->needscrawl, $datetimeformat),
            tool_crawler_link($row->target, $title, $row->redirect),
            tool_crawler_priority_level($row->priority),
        );
        if (!$courseid) {
            $escapedshortname = htmlspecialchars($row->shortname, ENT_NOQUOTES | ENT_HTML401);
            array_push($data, html_writer::link('/course/view.php?id='.$row->courseid, $escapedshortname) );
        }
        $table->data[] = $data;
    }

} else if ($report == 'recent') {

    $sql = " FROM {tool_crawler_url}  b
       LEFT JOIN {course} c ON c.id = b.courseid
           WHERE b.lastcrawled IS NOT NULL
                 $sqlfilter";

    $opts = array();
    $data  = $DB->get_records_sql("SELECT b.id,
                                          b.url target,
                                          b.lastcrawled,
                                          b.filesize,
                                          b.filesizestatus,
                                          b.httpcode,
                                          b.httpmsg,
                                          b.errormsg,
                                          b.title,
                                          b.redirect,
                                          b.mimetype,
                                          b.courseid,
                                          b.priority,
                                          c.shortname
                                          $sql
                                 ORDER BY b.lastcrawled DESC",
                                          $opts,
                                          $start,
                                          $perpage);

    $count = $DB->get_field_sql  ("SELECT count(*) AS count" . $sql, $opts);

    $table = new html_table();
    $table->head = array(
        get_string('lastcrawledtime', 'tool_crawler'),
        get_string('response', 'tool_crawler'),
        get_string('size', 'tool_crawler'),
        get_string('url', 'tool_crawler'),
        get_string('priority', 'tool_crawler'),
        get_string('mimetype', 'tool_crawler'),
    );
    if (!$courseid) {
        array_push($table->head, get_string('incourse', 'tool_crawler'));
    }
    $table->data = array();
    foreach ($data as $row) {
        $title = trim($row->title);
        if ($title == "") {
            $title = get_string('unknown', 'tool_crawler');
        }
        $code = tool_crawler_http_code($row);
        $data = array(
            userdate($row->lastcrawled, $datetimeformat),
            $code,
            htmlspecialchars(tool_crawler_displaysize($row), ENT_NOQUOTES | ENT_HTML401),
            tool_crawler_link($row->target, $title, $row->redirect),
            tool_crawler_priority_level($row->priority),
            htmlspecialchars($row->mimetype, ENT_NOQUOTES | ENT_HTML401),
        );
        if (!$courseid) {
            $escapedshortname = htmlspecialchars($row->shortname, ENT_NOQUOTES | ENT_HTML401);
            array_push($data, html_writer::link('/course/view.php?id='.$row->courseid, $escapedshortname) );
        }
        $table->data[] = $data;
    }

} else if ($report == 'oversize') {

    $oversizesqlfilter = tool_crawler_sql_oversize_filter('b');

    $sql = " FROM {tool_crawler_url} b
       LEFT JOIN {tool_crawler_edge} l ON l.b = b.id
       LEFT JOIN {tool_crawler_url}  a ON l.a = a.id
       LEFT JOIN {course} c ON c.id = a.courseid
           WHERE {$oversizesqlfilter['wherecondition']}
                 $sqlfilter";

    $opts = $oversizesqlfilter['params'];
    $data  = $DB->get_records_sql("SELECT concat(b.id, '-', a.id, '-', l.id) id,
                                          b.url target,
                                          b.filesize,
                                          b.filesizestatus,
                                          b.lastcrawled,
                                          b.mimetype,
                                          b.priority,
                                          l.text,
                                          a.title,
                                          a.url,
                                          a.redirect,
                                          a.courseid,
                                          c.shortname
                                          $sql
                                 ORDER BY b.filesize DESC,
                                          b.filesizestatus DESC,
                                          l.text,
                                          a.id",
                                          $opts,
                                          $start,
                                          $perpage);

    $count = $DB->get_field_sql  ("SELECT count(*) AS count" . $sql, $opts);

    $table = new html_table();

    $table->head = array(
        get_string('lastcrawledtime', 'tool_crawler'),
        get_string('size', 'tool_crawler'),
        get_string('slowurl', 'tool_crawler'),
        get_string('priority', 'tool_crawler'),
        get_string('mimetype', 'tool_crawler'),
        get_string('frompage', 'tool_crawler'),
    );

    if (!$courseid) {
        array_push($table->head, get_string('course', 'tool_crawler'));
    }

    $table->data = array();
    foreach ($data as $row) {
        $text = trim($row->text);
        if ($text == "") {
            $text = get_string('missing', 'tool_crawler');
            $text = htmlspecialchars($text, ENT_NOQUOTES | ENT_HTML401);
            // May add a bit of markup here so that the user can differentiate the "missing" message from an equal link text.
        } else {
            $text = htmlspecialchars($text, ENT_NOQUOTES | ENT_HTML401);
        }
        $data = array(
            userdate($row->lastcrawled, $datetimeformat),
            htmlspecialchars(tool_crawler_displaysize($row), ENT_NOQUOTES | ENT_HTML401),
            tool_crawler_link($row->target, $text, $row->redirect, true),
            tool_crawler_priority_level($row->priority),
            htmlspecialchars($row->mimetype, ENT_NOQUOTES | ENT_HTML401),
            tool_crawler_link($row->url, $row->title, $row->redirect)
        );
        if (!$courseid) {
            $escapedshortname = htmlspecialchars($row->shortname, ENT_NOQUOTES | ENT_HTML401);
            array_push($data, html_writer::link('/course/view.php?id='.$row->courseid, $escapedshortname) );
        }
        $table->data[] = $data;
    }

}

echo $OUTPUT->heading(get_string('numberurlsfound', 'tool_crawler',
    array(
        'reports_number' => $count,
        'report_type' => $report
    )
));
echo get_string($report . '_header', 'tool_crawler');
echo html_writer::table($table);
echo $OUTPUT->paging_bar($count, $page, $perpage, $baseurl);
echo $OUTPUT->footer();

