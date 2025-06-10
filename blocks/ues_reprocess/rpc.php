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
 *
 * @package    block_ues_reprocess
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
ues::require_daos();
require_once('lib.php');

require_login();

$id = required_param('id', PARAM_INT);
$type = required_param('type', PARAM_TEXT);

if ($type == 'user') {
    $user = $DB->get_record('user', array('id' => $id), '*', MUST_EXIST);

    $filter = function($section) {
        $section->fill_meta();
        return true;
    };

    $context = context_system::instance();
    $backurl = new moodle_url('/my');
} else {
    $course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
    $user = $USER;

    $filter = function($section) use ($course) {
        $section->fill_meta();
        return $section->idnumber == $course->idnumber;
    };

    $context = context_course::instance($course->id);
    $backurl = new moodle_url('/course/view.php', array('id' => $id));
}

$PAGE->set_context($context);

$s = ues::gen_str('block_ues_reprocess');

$uesuser = ues_user::upgrade($user);

if (has_capability('block/ues_reprocess:canreprocess', $context)) {
    $presections = ues_section::from_course($course);
} else {
    $presections = $uesuser->sections(true);
}

$ownedsections = array_filter($presections, $filter);

if ($data = data_submitted() and !empty($ownedsections)) {
    try {
        $sections = ues_reprocess::post($ownedsections, $data);
        ues_reprocess::select($sections);
    } catch (Exception $e) {
        echo $OUTPUT->notification($e->getMessage());
    }
} else {
    echo $OUTPUT->notification($s('none_found'));
}

echo $OUTPUT->continue_button($backurl);
