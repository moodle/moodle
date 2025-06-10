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
 * Course Hider Tool
 *
 * @package   block_course_hider
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/course_hider/lib.php');
// Authentication.
require_login();
if (!is_siteadmin()) {
    $redir = new course_hider_helpers();
    $redir->redirect_to_url($CFG->wwwroot);
}

$context = \context_system::instance();

$pageparams = [
    'vpreview' => optional_param('vpreview', 0, PARAM_INT),
    'btnpreview' => optional_param('preview', false, PARAM_BOOL),
    'btnexecute' => optional_param('execute', false, PARAM_BOOL),
];

// Setup the page.
$title = get_string('pluginname', 'block_course_hider');
$pagetitle = $title;
$sectiontitle = get_string('coursehidetitle', 'block_course_hider');
$url = new moodle_url($CFG->wwwroot . '/blocks/course_hider/course_hider.php', $pageparams);
$worky = null;

// Are we looking at the form to add/update or the list?
$viewpreview = false;
if (isset($pageparams['vpreview']) && $pageparams['vpreview'] == 1) {
    // Ok then, we are looking at the FORM.
    $viewpreview = true;
}

$results = null;

if ( isset($_REQUEST['btnexecute'])) {
    error_log("\n Yup, going through here");
    $pageparams['btnexecute'] = true;
    $results = (object) array(
        "courses" => $_REQUEST['courses'],
        "lock" => (int)$_REQUEST['lock'],
        "hide" => (int)$_REQUEST['hide']
    );
}
// ------------------------------------------------------------------------
// If we want to push any data to javascript then we can add it here.
// $initialload = array(
//     "wwwroot" => $CFG->wwwroot,
//     "course_hider_form" => "course_hider",
//     "course_hider_viewpreview" => $viewpreview,
//     "settings" => array(
//         // "sample_autocomplete" => get_config('moodle', "block_course_hider_enable_form_auto")
//     )
// );
// $initialload = json_encode($initialload, JSON_HEX_APOS | JSON_HEX_QUOT);
// $xtras = "<script>window.__SERVER__=true</script>".
//     "<script>window.__INITIAL_STATE__='".$initialload."'</script>";
// ------------------------------------------------------------------------

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($title);

// Navbar Bread Crumbs.
$PAGE->navbar->add(get_string('ch_dashboard', 'block_course_hider'), $CFG->wwwroot);
$PAGE->navbar->add(get_string('pluginname', 'block_course_hider'), new moodle_url('course_hider.php'));
$PAGE->navbar->add(get_string('pluginname', 'block_course_hider'), new moodle_url('course_hider.php'));

$PAGE->requires->css(new moodle_url($CFG->wwwroot . '/blocks/course_hider/style/main.css'));
$jsparams = array();
$PAGE->requires->js_call_amd('block_course_hider/main', 'init', $jsparams);

$output = $PAGE->get_renderer('block_course_hider');

$toform = [
    'btnpreview' => $pageparams['btnpreview']
];
$mform = new \block_course_hider\form\course_hider_form(null, $toform);

// Create/Update.
$fromform = $mform->get_data();


$worky = $worky ?? new \block_course_hider\controllers\form_controller();
if ($mform->is_cancelled()) {
    // If there is a cancel element on the form, and it was pressed,
    // then the `is_cancelled()` function will return true.
    // You can handle the cancel operation here.
    redirect($CFG->wwwroot . '/blocks/block_course_hider/course_hider.php');
} else if ($fromform = $mform->get_data()) {
    // When the form is submitted, and the data is successfully validated,
    // the `get_data()` function will return the data posted in the form.
    // The form_controller will process and use matching persistent.
    $results = $worky->process_form($fromform);
} else {
    // This branch is executed if the form is submitted but the data doesn't
    // validate and the form should be redisplayed or on the first display of the form.
    $mform->set_data($fromform);
}
echo $output->header();
// echo $xtras;
echo $output->heading($sectiontitle);
$mform->display();

if ($pageparams['btnpreview'] == 1) {
    $renderable = new \block_course_hider\output\course_hider_view($results);
    echo $output->render($renderable);
    
} else if ($pageparams['btnexecute'] == true) {
    $worky->execute_hider($results, $fromform);
}

echo $output->footer();
