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
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
require_once('reprocess_form.php');
require_once('lib.php');

require_login();

ues::require_daos();

$type = required_param('type', PARAM_TEXT);

$validtypes = array('user', 'course');

if (!in_array($type, $validtypes)) {
    print_error('not_supported', 'block_ues_reprocess', '', $type);
}

$id = required_param('id', PARAM_INT);

$s = ues::gen_str('block_ues_reprocess');

$blockname = $s('pluginname');

if ($type == 'user') {
    $user = $DB->get_record('user', array('id' => $id), '*', MUST_EXIST);

    $filter = function ($section) {
        $section->fill_meta();
        return true;
    };

    $header = $s('reprocess');
    $context = context_system::instance();

    $backurl = new moodle_url('/my');

    $custompage = function ($page) use ($blockname, $context, $header) {
        global $USER;
        $page->set_context($context);
        $page->set_heading($blockname);
        $page->navbar->add($blockname);
        $page->navbar->add($header);
        $page->set_title($header);
        $page->set_url(new moodle_url('/blocks/ues_reprocess/reprocess.php', array(
            'id' => $USER->id, 'type' => 'user'
        )));
    };
} else {
    $course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
    $context = context_course::instance($course->id);

    $user = $USER;

    $filter = function($section) use ($course) {
        $section->fill_meta();
        return $section->idnumber == $course->idnumber;
    };

    $header = $s('reprocess_course');

    $backurl = new moodle_url('/course/view.php', array('id' => $id));
    $custompage = function ($page) use ($course, $context, $header, $blockname) {
        global $USER;

        $base = '/blocks/ues_reprocess/reprocess.php';

        $url = new moodle_url($base, array('id' => $USER->id, 'type' => 'user'));

        $page->set_context($context);
        $page->set_heading($blockname . ': ' . $header);
        $page->navbar->add($blockname, $url);
        $page->navbar->add($header);
        $page->set_title($header);
        $page->set_course($course);
        $page->set_url(new moodle_url($base, array(
            'id' => $course->id, 'type' => 'course'
        )));
    };
}

$uesuser = ues_user::upgrade($user);

if (has_capability('block/ues_reprocess:canreprocess', $context)) {
    $presections = ues_section::from_course($course);
} else {
    $presections = $uesuser->sections(true);
}

$ownedsections = array_filter($presections, $filter);

$custompage($PAGE);

$form = new reprocess_form(null, array(
    'id' => $id, 'sections' => $ownedsections, 'type' => $type
));

if ($form->is_cancelled()) {
    redirect($backurl);
} else if ($data = $form->get_data()) {
    $module = array(
        'name' => 'blocks_ues_reprocess',
        'fullpath' => '/blocks/ues_reprocess/js/reprocess.js',
        'requires' => array('base', 'io', 'node')
    );

    $PAGE->requires->js_init_call('M.block_ues_reprocess.init', null, false, $module);

    $basic = array('id' => $id, 'type' => $type);

    $params = get_object_vars($data);

    $sections = ues_reprocess::post($ownedsections, $params);

    $confirmurl = new moodle_url('rpc.php', $params);
    $cancelurl = new moodle_url('reprocess.php', $basic);
    $posted = true;
}

echo $OUTPUT->header();
echo $OUTPUT->heading($header);

if (!empty($posted) and empty($sections)) {
    echo $OUTPUT->notification($s('select'));
    $form->display();
} else if (!empty($posted)) {
    $tonumber = function ($in, $section) {
        $section->semester();
        $section->course();
        return $in . "<li><strong>$section</strong></li>";
    };

    $numbers = array_reduce($sections, $tonumber, '');

    echo $OUTPUT->confirm($s('are_you_sure', $numbers), $confirmurl, $cancelurl);

    echo html_writer::start_tag('div', array('id' => 'loading', 'style' => 'display: none'));
    echo $OUTPUT->notification($s('patience'));
    echo '<br/>';
    echo $OUTPUT->pix_icon('i/loading', 'Loading');
    echo html_writer::end_tag('div');

} else if (empty($ownedsections)) {
    echo $OUTPUT->notification($s('none_found'));
    echo $OUTPUT->continue_button($backurl);
} else {
    $form->display();
}

echo $OUTPUT->footer();
