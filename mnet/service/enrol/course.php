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
 * Page to enrol our users into remote courses
 *
 * @package    plugintype
 * @subpackage pluginname
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/mnet/service/enrol/locallib.php');

require_sesskey();

$hostid   = required_param('host', PARAM_INT); // remote host id in our mnet_host table
$courseid = required_param('course', PARAM_INT); // id of the course in our cache table
$usecache = optional_param('usecache', true, PARAM_BOOL); // use cached list of enrolments

admin_externalpage_setup('mnetenrol');
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
$host   = $hosts[$hostid];
$course = $DB->get_record('mnetservice_enrol_courses', array('id'=>$courseid, 'hostid'=>$host->id), '*', MUST_EXIST);

echo $OUTPUT->header();

// course name
$icon = html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/course'), 'alt' => get_string('category')));
echo $OUTPUT->heading($icon . s($course->fullname));

// collapsible course summary
if (!empty($course->summary)) {
    unset($options);
    $options->trusted = false;
    $options->para    = false;
    $options->filter  = false;
    $options->noclean = false;
    print_collapsible_region_start('remotecourse summary', 'remotecourse-summary', get_string('coursesummary'), false, true);
    echo format_text($course->summary, $course->summaryformat, $options,  $course->id);
    print_collapsible_region_end();
}

// form to enrol our students
$enrolments = $service->get_remote_course_enrolments($host->id, $course->remoteid, $usecache);

print_object($enrolments); // DONOTCOMMIT
echo $OUTPUT->footer();
