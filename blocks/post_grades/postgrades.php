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

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->dirroot . '/grade/export/lib.php');

define('EXPIRE_KEY', strtotime('7 days'));

require_login();

$courseid = required_param('courseid', PARAM_INT);
$groupid  = required_param('groupid', PARAM_INT);
$periodid = required_param('periodid', PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$group = $DB->get_record('groups', array('id' => $groupid), '*', MUST_EXIST);
$period = $DB->get_record('block_post_grades_periods', array('id' => $periodid), '*', MUST_EXIST);

$context = context_course::instance($course->id);

require_capability('block/post_grades:canpost', $context);

$sections = ues_section::from_course($course);
$validgroups = post_grades::valid_groups($course);

$section = post_grades::find_section($group, $sections);

if (empty($CFG->gradepublishing)) {
    moodle_exception('nopublishing', 'block_post_grades');
}

// Not a valid group.
if (!isset($validgroups[$groupid]) or empty($section)) {
    moodle_exception('notvalidgroup', 'block_post_grades', '', $group->name);
}

// Not a valid posting period.
if (!in_array($period, post_grades::active_periods($course))) {
    moodle_exception('notactive', 'block_post_grades');
}

$uescourse = $section->course()->fill_meta();

$params = array(
    'periodid' => $period->id,
    'sectionid' => $section->id,
    'userid' => $USER->id
);

$s = ues::gen_str('block_post_grades');

$posting = $DB->get_record('block_post_grades_postings', $params);

// Posted before... complain.
if ($posting) {
    $a = new stdClass;
    $a->fullname = $course->fullname;
    $a->name = $group->name;
    $a->post_type = $s($period->post_type);
    print_error('alreadyposted', 'block_post_grades', '', $a);
}

$posting = (object) $params;

$DB->insert_record('block_post_grades_postings', $posting);

// Data is valid, now process.
$key = get_user_key('grade/export', $USER->id, $courseid, '', EXPIRE_KEY);

$courseitem = grade_item::fetch(array('itemtype' => 'course', 'courseid' => $courseid));

$exportparams = array(
    'id' => $courseid,
    'key' => $key,
    'groupid' => $groupid,
    'itemids' => $courseitem->id,
    'export_feedback' => 0,
    'updategradesonly' => 0,
    'decimalpoints' => $courseitem->get_decimals(),
    'displaytype' => $period->export_number ?
        GRADE_DISPLAY_TYPE_REAL : GRADE_DISPLAY_TYPE_LETTER
);

$domino = get_config('block_post_grades', 'domino_application_url');

$exporturl = new moodle_url('/grade/export/xml/dump.php', $exportparams);

switch($period->post_type) {
    case 'midterm':
        $post_type = 'M';
        break;
    case 'onlinemidterm':
        $post_type = 'N';
        break;
    case 'final':
        $post_type = 'F';
        break;
    case 'onlinefinal':
        $post_type = 'O';
        break;
    case 'degree':
        $post_type = 'D';
        break;
    case 'test':
        $post_type = 'T';
}

$postparams = array(
    'postType' => $post_type,
    'DeptCode' => $uescourse->department,
    'CourseNbr' => $uescourse->cou_number,
    'SectionNbr' => $section->sec_number,
    'MoodleGradeURL' => rawurlencode(rawurlencode($exporturl->out(false)))
);

// We can't be sure about the configured url, so we are required to be safe.
$transformed = array();
foreach ($postparams as $key => $value) {
    $transformed[] = "$key=$value";
}

$forward = $domino . implode('%26', $transformed);

// Add some debugging stuff in for testing. This will be disbled in prod and can be removed.
if ($CFG->debug == 32767 && $CFG->debugdisplay > 0) {
    echo'<br>Post grades URL: ';
    print_r($forward);
    die();
}

redirect($forward);
