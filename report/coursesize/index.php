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
 * Version information
 *
 * @package    report_coursesize
 * @copyright  2014 Catalyst IT {@link http://www.catalyst.net.nz}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/report/coursesize/locallib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');

admin_externalpage_setup('reportcoursesize');

$coursecategory = optional_param('category', 0, PARAM_INT);
$download = optional_param('download', '', PARAM_INT);
$viewtab = optional_param('view', 'coursesize', PARAM_ALPHA);
$reportconfig = get_config('report_coursesize');

// If we should show or hide empty courses.
if (!defined('REPORT_COURSESIZE_SHOWEMPTYCOURSES')) {
    define('REPORT_COURSESIZE_SHOWEMPTYCOURSES', false);
}

// Data for the tabs in the report.
$tabdata = ['coursesize' => '', 'userstopnum' => $reportconfig->numberofusers];
if (!array_key_exists($viewtab, $tabdata)) {
    // For invalid parameter value use 'coursesize'.
    $viewtab = array_keys($tabdata)[0];
}

$tabs = [];
foreach ($tabdata as $tabname => $param) {
    $tabs[] = new tabobject($tabname, new moodle_url($PAGE->url, ['view' => $tabname]),
        get_string($tabname, 'report_coursesize', $param));
}

if (empty($download)) {
    print $OUTPUT->header();
    echo $OUTPUT->tabtree($tabs, $viewtab);
}

if ($viewtab == 'userstopnum') {
    $usersizes = report_coursesize_get_usersizes();
    if (!empty($usersizes)) {
        $usertable = new html_table();
        $usertable->align = array('right', 'right');
        $usertable->head = array(get_string('user'), get_string('diskusage', 'report_coursesize'));
        $usertable->data = array();
        $usercount = 0;
        foreach ($usersizes as $userid => $size) {
            $usercount++;
            $user = $DB->get_record('user', array('id' => $userid));
            $row = array();
            $row[] = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $userid . '">' . fullname($user) . '</a>';
            $row[] = display_size($size->totalsize);
            $usertable->data[] = $row;
            if ($usercount >= $reportconfig->numberofusers) {
                break;
            }
        }
        unset($users);
        print $OUTPUT->heading(get_string('userstopnum', 'report_coursesize', $reportconfig->numberofusers));

        if (!isset($usertable)) {
            print get_string('nouserfiles', 'report_coursesize');
        } else {
            print html_writer::table($usertable);
        }

    }
} else if ($viewtab == 'coursesize') {

    if (!empty($reportconfig->filessize) && !empty($reportconfig->filessizeupdated)) {
        // Total files usage has stored by scheduled task.
        $totalusage = $reportconfig->filessize;
        $totaldate = date("Y-m-d H:i", $reportconfig->filessizeupdated);
    } else {
        $totaldate = get_string('never');
        $totalusage = 0;
    }

    $totalusagereadable = display_size($totalusage);
    $systemsize = $systembackupsize = 0;

    $coursesql = 'SELECT cx.id, c.id as courseid ' .
        'FROM {course} c ' .
        ' INNER JOIN {context} cx ON cx.instanceid=c.id AND cx.contextlevel = ' . CONTEXT_COURSE;
    $params = array();
    $courseparams = array();
    $extracoursesql = '';
    if (!empty($coursecategory)) {
        $context = context_coursecat::instance($coursecategory);
        $coursecat = core_course_category::get($coursecategory);
        $courses = $coursecat->get_courses(array('recursive' => true, 'idonly' => true));

        if (!empty($courses)) {
            list($insql, $courseparams) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
            $extracoursesql = ' WHERE c.id ' . $insql;
        } else {
            // Don't show any courses if category is selected but category has no courses.
            // This stuff really needs a rewrite!
            $extracoursesql = ' WHERE c.id is null';
        }
    }
    $coursesql .= $extracoursesql;
    $params = array_merge($params, $courseparams);
    $courselookup = $DB->get_records_sql($coursesql, $params);

    $live = false;
    $backupsizes = [];
    if (isset($reportconfig->calcmethod) && ($reportconfig->calcmethod) == 'live') {
        $live = true;
    }
    if ($live) {
        $filesql = report_coursesize_filesize_sql();
        $sql = "SELECT c.id, c.shortname, c.category, ca.name, rc.filesize
          FROM {course} c
          JOIN ($filesql) rc on rc.course = c.id ";

        // Generate table of backup filesizes too.
        $backupsql = report_coursesize_backupsize_sql();
        $backupsizes = $DB->get_records_sql($backupsql);
    } else {
        $sql = "SELECT c.id, c.shortname, c.category, ca.name, rc.filesize, rc.backupsize
          FROM {course} c
          JOIN {report_coursesize} rc on rc.course = c.id ";
    }

    $sql .= "JOIN {course_categories} ca on c.category = ca.id
         $extracoursesql
     ORDER BY rc.filesize DESC";
    $courses = $DB->get_records_sql($sql, $courseparams);

    $coursetable = new html_table();
    $coursetable->align = array('right', 'right', 'left');
    $coursetable->head = array(get_string('course'),
        get_string('category'),
        get_string('diskusage', 'report_coursesize'),
        get_string('backupsize', 'report_coursesize'));
    $coursetable->data = array();

    $totalsize = 0;
    $totalbackupsize = 0;
    $downloaddata = array();
    $downloaddata[] = array(get_string('course'),
        get_string('category'),
        get_string('diskusage', 'report_coursesize'),
        get_string('backupsize', 'report_coursesize'));;

    $coursesizes = $DB->get_records('report_coursesize');
    foreach ($courses as $courseid => $course) {
        if ($live) {
            if (isset($backupsizes[$course->id])) {
                $course->backupsize = $backupsizes[$course->id]->filesize;
            } else {
                $course->backupsize = 0;
            }
        }
        $totalsize = $totalsize + $course->filesize;
        $totalbackupsize = $totalbackupsize + $course->backupsize;
        $coursecontext = context_course::instance($course->id);
        $course->shortname = format_string($course->shortname, true, ['context' => $coursecontext]);
        $course->name = format_string($course->name, true, ['context' => $coursecontext]);
        $row = array();
        $row[] = '<a href="' . $CFG->wwwroot . '/course/view.php?id=' . $course->id . '">' . $course->shortname . '</a>';
        $row[] = '<a href="' . $CFG->wwwroot . '/course/index.php?categoryid=' . $course->category . '">' . $course->name . '</a>';

        $readablesize = display_size($course->filesize);
        $a = new stdClass;
        $a->bytes = $course->filesize;
        $a->shortname = $course->shortname;
        $a->backupbytes = $course->backupsize;
        $bytesused = get_string('coursebytes', 'report_coursesize', $a);
        $backupbytesused = get_string('coursebackupbytes', 'report_coursesize', $a);
        $summarylink = new moodle_url('/report/coursesize/course.php', array('id' => $course->id));
        $summary = html_writer::link($summarylink, ' ' . get_string('coursesummary', 'report_coursesize'));
        $row[] = "<span id=\"coursesize_" . $course->shortname . "\" title=\"$bytesused\">$readablesize</span>" . $summary;
        $row[] = "<span title=\"$backupbytesused\">" . display_size($course->backupsize) . "</span>";
        $coursetable->data[] = $row;
        $downloaddata[] = array($course->shortname, $course->name, str_replace(',', '', $readablesize),
            str_replace(',', '', display_size($course->backupsize)));
    }

    // Now add the courses that had no sitedata into the table.
    if (REPORT_COURSESIZE_SHOWEMPTYCOURSES) {
        $a = new stdClass;
        $a->bytes = 0;
        $a->backupbytes = 0;
        foreach ($courses as $cid => $course) {
            $course->shortname = format_string($course->shortname, true, context_course::instance($course->id));
            $a->shortname = $course->shortname;
            $bytesused = get_string('coursebytes', 'report_coursesize', $a);
            $bytesused = get_string('coursebackupbytes', 'report_coursesize', $a);
            $row = array();
            $row[] = '<a href="' . $CFG->wwwroot . '/course/view.php?id=' . $course->id . '">' . $course->shortname . '</a>';
            $row[] = "<span title=\"$bytesused\">0</span>";
            $row[] = "<span title=\"$bytesused\">0</span>";
            $coursetable->data[] = $row;
        }
    }
    // Now add the totals to the bottom of the table.
    $coursetable->data[] = array(); // Add empty row before total.
    $downloaddata[] = array();
    $row = array();
    $row[] = get_string('total');
    $row[] = '';
    $row[] = display_size($totalsize);
    $row[] = display_size($totalbackupsize);
    $coursetable->data[] = $row;
    $downloaddata[] = [get_string('total'), '', display_size($totalsize), display_size($totalbackupsize)];
    unset($courses);

    $systemsizereadable = display_size($systemsize);
    $systembackupreadable = display_size($systembackupsize);


    // Add in Course Cat including dropdown to filter.

    $url = '';
    $catlookup = $DB->get_records_sql('select id,name from {course_categories}');
    $options = ['0' => get_string('allcourses', 'report_coursesize')];
    foreach ($catlookup as $cat) {
        $options[$cat->id] = format_string($cat->name, true, context_system::instance());
    }

    // Add in download option. Exports CSV.

    if ($download == 1) {
        $downloadfilename = clean_filename("export_csv");
        $csvexport = new csv_export_writer ('commer');
        $csvexport->set_filename($downloadfilename);
        foreach ($downloaddata as $data) {
            $csvexport->add_data($data);
        }
        $csvexport->download_file();
        exit;
    }

    if (empty($coursecat)) {
        $updatestring = !empty($reportconfig->filessizeupdated) ? userdate($reportconfig->filessizeupdated) : get_string('never');
        print $OUTPUT->heading(get_string("sitefilesusage", 'report_coursesize'));
        print '<strong>' . get_string("totalsitedata", 'report_coursesize', $totalusagereadable) . '</strong> ';
        print get_string('lastupdate', 'report_coursesize', $updatestring) . "<br/><br/>\n";
        print get_string('catsystemuse', 'report_coursesize', $systemsizereadable) . "<br/>";
        print get_string('catsystembackupuse', 'report_coursesize', $systembackupreadable) . "<br/>";
        if (!empty($CFG->filessizelimit)) {
            print get_string("sizepermitted", 'report_coursesize', number_format($CFG->filessizelimit)) . "<br/>\n";
        }
    }
    $lastupdate = '';
    if (!$live) {
        if (empty($reportconfig->coursesizeupdated)) {
            $lastupdate = get_string('lastupdatenever', 'report_coursesize');
        } else {
            $lastupdate = get_string('lastupdate', 'report_coursesize', userdate($reportconfig->coursesizeupdated));
        }
        $lastupdate = html_writer::span($lastupdate, 'lastupdate');
    }
    $heading = get_string('coursesize', 'report_coursesize');
    if (!empty($coursecat)) {
        $heading .= " - " . $coursecat->name;
    }
    print $OUTPUT->heading($heading . ' ' . $lastupdate);

    $desc = get_string('coursesize_desc', 'report_coursesize');

    if (!REPORT_COURSESIZE_SHOWEMPTYCOURSES) {
        $desc .= ' ' . get_string('emptycourseshidden', 'report_coursesize');
    }
    print $OUTPUT->box($desc);

    $filter = $OUTPUT->single_select($url, 'category', $options, $coursecategory, []);
    $filter .= $OUTPUT->single_button(new moodle_url('index.php', array('download' => 1, 'category' => $coursecategory)),
        get_string('exportcsv', 'report_coursesize'), 'post', ['class' => 'coursesizedownload']);

    print $OUTPUT->box($filter) . "<br/>";

    print html_writer::table($coursetable);
}
print $OUTPUT->footer();
