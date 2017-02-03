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
 * Displays a list of remote courses offered by a given host for our students
 *
 * By default the courses information is cached in our local DB table. Parameter
 * $usecache can be used to force re-fetching up to date state from remote
 * hosts (session key required in such case).
 *
 * @package    mnetservice
 * @subpackage enrol
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/mnet/service/enrol/locallib.php');

$hostid   = required_param('id', PARAM_INT); // remote host id
$usecache = optional_param('usecache', true, PARAM_BOOL); // use cached list of courses

admin_externalpage_setup('mnetenrol', '', array('id'=>$hostid, 'usecache'=>1),
        new moodle_url('/mnet/service/enrol/host.php'));
$service = mnetservice_enrol::get_instance();

if (!$service->is_available()) {
    echo $OUTPUT->box(get_string('mnetdisabled','mnet'), 'noticebox');
    echo $OUTPUT->footer();
    die();
}

// remote hosts that may publish remote enrolment service and we are subscribed to it
$hosts = $service->get_remote_publishers();

if (empty($hosts[$hostid])) {
    print_error('wearenotsubscribedtothishost', 'mnetservice_enrol');
}
$host = $hosts[$hostid];

if (!$usecache) {
    // our local database will be changed
    require_sesskey();
}
$courses = $service->get_remote_courses($host->id, $usecache);
if (is_string($courses)) {
    print_error('fetchingcourses', 'mnetservice_enrol', '', null, $service->format_error_message($courses));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('availablecourseson', 'mnetservice_enrol', s($host->hostname)));
if (empty($courses)) {
    $a = (object)array('hostname' => s($host->hostname), 'hosturl' => s($host->hosturl));
    echo $OUTPUT->box(get_string('availablecoursesonnone','mnetservice_enrol', $a), 'noticebox');
    if ($usecache) {
        echo $OUTPUT->single_button(new moodle_url($PAGE->url, array('usecache'=>0, 'sesskey'=>sesskey())),
                                    get_string('refetch', 'mnetservice_enrol'), 'get');
    }
    echo $OUTPUT->footer();
    die();
}

$table = new html_table();
$table->head = array(
    get_string('shortnamecourse'),
    get_string('fullnamecourse'),
    get_string('role'),
    get_string('action')
);
$table->attributes['class'] = 'generaltable remotecourses';
$icon = html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/course'), 'alt' => get_string('category')));
$prevcat = null;
foreach ($courses as $course) {
    $course = (object)$course;
    if ($prevcat !== $course->categoryid) {
        $row = new html_table_row();
        $cell = new html_table_cell($icon . s($course->categoryname));
        $cell->header = true;
        $cell->attributes['class'] = 'categoryname';
        $cell->colspan = 4;
        $row->cells = array($cell);
        $table->data[] = $row;
        $prevcat = $course->categoryid;
    }
    $editbtn = $OUTPUT->single_button(new moodle_url('/mnet/service/enrol/course.php',
                                      array('host'=>$host->id, 'course'=>$course->id, 'usecache'=>0, 'sesskey'=>sesskey())),
                                      get_string('editenrolments', 'mnetservice_enrol'), 'get');
    $row = new html_table_row();
    $row->cells = array(
        s($course->shortname),
        s($course->fullname),
        s($course->rolename),
        $editbtn
    );
    $table->data[] = $row;
}
echo html_writer::table($table);

if ($usecache) {
    echo $OUTPUT->single_button(new moodle_url($PAGE->url, array('usecache'=>0, 'sesskey'=>sesskey())),
                                get_string('refetch', 'mnetservice_enrol'), 'get');
}

echo $OUTPUT->footer();
