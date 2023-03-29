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
 * @package   local_report_attendance
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/excellib.class.php');
require_once('select_form.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');
require_once('lib.php');

// Deal with the params.
$courseid = optional_param('courseid', 0, PARAM_INT);
$participant = optional_param('participant', 0, PARAM_INT);
$dodownload = optional_param('dodownload', 0, PARAM_INT);
$departmentid = optional_param('departmentid', 0, PARAM_INT);

// Check permissions.
require_login();
$context = context_system::instance();
iomad::require_capability('local/report_attendance:view', $context);

// Url stuff.
$url = new moodle_url('/local/report_attendance/index.php');
$dashboardurl = new moodle_url('/blocks/iomad_company_admin/index.php');

// Page stuff:.
$strcompletion = get_string('pluginname', 'local_report_attendance');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title($strcompletion);
$PAGE->requires->css("/local/report_attendance/styles.css");

// Set the page heading.
$PAGE->set_heading($strcompletion);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Get the associated department id.
$company = new company($companyid);
$parentlevel = company::get_company_parentnode($company->id);
$companydepartment = $parentlevel->id;

// Work out where the user sits in the company department tree.
$userlevel = $company->get_userlevel($USER);
$userhierarchylevel = key($userlevel);
if ($departmentid == 0 ) {
    $departmentid = $userhierarchylevel;
}

// Create data for form.
$customdata = null;
$options = array();
$options['dodownload'] = 1;
if (!empty($courseid)) {
    $options['courseid'] = $courseid;
}

// Only print the header if we are not downloading.
if (empty($dodownload)) {
    echo $OUTPUT->header();

    // Check the department is valid.
    if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
        print_error('invaliddepartment', 'block_iomad_company_admin');
    }
} else {
    // Check the department is valid.
    if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
        print_error('invaliddepartment', 'block_iomad_company_admin');
    }
}

// Get the courses which have the classroom module in them.
$courses = attendancerep::courseselectlist($companyid);
$courseselect = new single_select($url, 'courseid', $courses, $courseid);
$courseselect->label = get_string('course');
$courseselect->formid = 'choosecourse';
if (empty($courses)) {
    echo get_string('nocourses', 'local_report_attendance');
    echo $OUTPUT->footer();
    die;
}
if (empty($dodownload)) {
    echo html_writer::tag('div',
                           $OUTPUT->render($courseselect),
                           array('id' => 'iomad_course_selector'));
}

// Get the department users who are on the course.
$allowedusers = company::get_recursive_department_users($departmentid);
$allowedlist = "";
foreach ($allowedusers as $alloweduser) {
    if (empty($allowedlist)) {
        $allowedlist = $alloweduser->userid;
    } else {
        $allowedlist .= ','.$alloweduser->userid;
    }
}

if (!empty($courseid)) {
    if (empty($dodownload)) {
        // Get the events from this course and display them as a table.
        $events = $DB->get_records('trainingevent', array('course' => $courseid));
        foreach ($events as $event) {
            $eventtable = new html_table();
            $location = $DB->get_record('classroom', array('id' => $event->classroomid));
            $eventtable->align = array('left', 'left');
            $eventtable->width = '50%';
            echo "<h2>".get_string('event', 'local_report_attendance'). " " .$event->name."</h2>";
            foreach ($location as $key => $value) {
                if ($key == 'id') {
                    continue;
                } else if ($key == 'companyid') {
                    continue;
                } else if ($key == 'capacity') {
                    continue;
                } else {
                    $eventtable->data[] = array($key, $value);
                }
            }
            echo html_writer::table($eventtable);
            $attendancetable = new html_table();
            $attendancetable->width = '95%';
            $attendancetable->head = array(get_string('fullname'),
                                           get_string('department', 'block_iomad_company_admin'),
                                           get_string('email'));

            if (!empty($allowedusers) &&
                $users = $DB->get_records_sql('SELECT userid AS id FROM {trainingevent_users}
                                               WHERE trainingeventid='.$event->id.'
                                               AND userid IN ('.$allowedlist.') AND waitlisted=0' )) {
                foreach ($users as $user) {
                    $fulluserdata = $DB->get_record('user', array('id' => $user->id));
                    $fulluserdata->department = company_user::get_department_name($user->id);
                    $fullusername = $fulluserdata->firstname.' '.$fulluserdata->lastname;
                    $attendancetable->data[] = array($fullusername,
                                                     $fulluserdata->department,
                                                     $fulluserdata->email);
                }
            }
            echo "<h3>".get_string('attendance', 'local_report_attendance')."</h3>";
            echo $OUTPUT->single_button(new moodle_url('index.php',
                                            array('courseid' => $courseid,
                                                  'dodownload' => $event->id)),
                                            get_string("downloadcsv", 'local_report_attendance'));
            echo html_writer::table($attendancetable);
        }
    } else {
        if (!$event = $DB->get_record('trainingevent', array('id' => $dodownload))) {
            die;
        }
        $location = $DB->get_record('classroom', array('id' => $event->classroomid));
        // Output everything to a file.
        header("Content-Type: application/download\n");
        header("Content-Disposition: attachment; filename=\"".$event->name.".csv\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
        header("Pragma: public");
        $locationinfo = "$location->name, $location->address, $location->city,";
        $locationinfo .= " $location->country, $location->postcode";
        echo "\"$event->name, $locationinfo\"\n";
        echo "\"".get_string('fullname')."\",\"".get_string('department', 'block_iomad_company_admin')."\",\"". get_string('email')."\"\n";
        if ($users = $DB->get_records_sql('SELECT userid AS id FROM {trainingevent_users}
                                           WHERE trainingeventid='.$event->id.'
                                           AND userid IN ('.$allowedlist.') AND waitlisted=0')) {
            foreach ($users as $user) {
                $fulluserdata = $DB->get_record('user', array('id' => $user->id));
                $fulluserdata->department = company_user::get_department_name($user->id);
                $fullname = "$fulluserdata->firstname $fulluserdata->lastname";
                echo "\"$fullname\", \"$fulluserdata->department\", \"$fulluserdata->email\"\n";
            }
        }
    }
}
if (!empty($dodownload)) {
    exit;
}
echo $OUTPUT->footer();
