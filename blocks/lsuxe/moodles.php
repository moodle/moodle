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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

// Authentication.
require_login();
if (!is_siteadmin()) {
    $helpers->redirect_to_url($CFG->wwwroot);
}

$context = \context_system::instance();

$pageparams = [
    'vform' => optional_param('vform', 0, PARAM_INT),
    // TODO: Sort by field name.
    'sort' => optional_param('sort', 'sent', PARAM_TEXT),
    // TODO: Asc|desc.
    'dir' => optional_param('dir', 'desc', PARAM_TEXT),
    // TODO: need to implement pagination......maybe?
    'page' => optional_param('page', 1, PARAM_INT),
    'per_page' => 10,
    'sent_action' => optional_param('sentaction', "", PARAM_TEXT),
    'sent_data' => optional_param('sentdata', 0, PARAM_INT)
];

// Setup the page.
$title = get_string('pluginname', 'block_lsuxe') . ': ' . get_string('moodles', 'block_lsuxe');
$pagetitle = $title;
$sectiontitle = get_string('newmoodle', 'block_lsuxe');
$enablewideview = (bool)get_config('moodle', "block_lsuxe_enable_wide_view");
$url = new moodle_url($CFG->wwwroot . '/blocks/lsuxe/moodles.php', $pageparams);
$worky = null;

// Are we looking at the form to add/update or the list?
$viewform = false;
if ($pageparams['vform'] == 1) {
    // Ok then, we are looking at the FORM.
    $viewform = true;
}
// ------------------------------------------------------------------------
// If we want to push any data to javascript then we can add it here.
$initialload = array(
    "wwwroot" => $CFG->wwwroot,
    "xe_form" => "moodles",
    "xe_viewform" => $viewform
);
$initialload = json_encode($initialload, JSON_HEX_APOS | JSON_HEX_QUOT);
$xtras = "<script>window.__SERVER__=true</script>".
    "<script>window.__INITIAL_STATE__='".$initialload."'</script>";
// ------------------------------------------------------------------------

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Navbar Bread Crumbs.
$PAGE->navbar->add(get_string('xedashboard', 'block_lsuxe'), new moodle_url('lsuxe.php'));
$PAGE->navbar->add(get_string('moodles', 'block_lsuxe'), new moodle_url('moodles.php'));
$PAGE->requires->css(new moodle_url($CFG->wwwroot . '/blocks/lsuxe/style.css'));
if ($enablewideview) {
    $PAGE->requires->css(new moodle_url($CFG->wwwroot . '/blocks/lsuxe/xestyles/style.css'));
}
$PAGE->requires->js_call_amd('block_lsuxe/main', 'init');
$output = $PAGE->get_renderer('block_lsuxe');

// If the sent action is delete then the user just deleted a row, let's process it.
if ($pageparams['sent_action'] === "delete") {
    $worky = new \block_lsuxe\controllers\form_controller("moodles");
    $worky->delete_record((int)$pageparams['sent_data']);
    \core\notification::success(get_string('deletemoodle', 'block_lsuxe'));
}

// Create a Moodle Instance.
if ($viewform == true) {

    // We are viewing the form so are we updating or creating a new record?
    if ($pageparams['sent_action'] === "update") {
        // Update the section title.
        $sectiontitle = get_string('updatemoodle', 'block_lsuxe');
        // Get the course record that you want.
        $thismoodle = $DB->get_record('block_lsuxe_moodles', array('id' => (int)$pageparams['sent_data']));
        // Pass the time created value in an array.
        $mform = new \block_lsuxe\form\moodles_form(null, $thismoodle);
    } else {
        $mform = new \block_lsuxe\form\moodles_form();
    }

    // Create/Update Moodles.
    $fromform = $mform->get_data();

    if ($mform->is_cancelled()) {
        // If there is a cancel element on the form, and it was pressed,
        // then the `is_cancelled()` function will return true.
        // You can handle the cancel operation here.
        redirect($CFG->wwwroot . '/blocks/lsuxe/moodles.php');
    } else if ($fromform = $mform->get_data()) {
        // When the form is submitted, and the data is successfully validated,
        // the `get_data()` function will return the data posted in the form.
        $worky = $worky ?? new \block_lsuxe\controllers\form_controller("moodles");
        // The form_controller will process and use matching persistent.
        $worky->process_form($fromform);
    } else {
        // This branch is executed if the form is submitted but the data doesn't
        // validate and the form should be redisplayed or on the first display of the form.
        $mform->set_data($fromform);
    }
    echo $output->header();
    echo $xtras;
    echo $output->heading($sectiontitle);
    $mform->display();

} else {

    // View the Moodle Instances.
    echo $output->header();
    echo $xtras;
    $renderable = new \block_lsuxe\output\moodles_view();
    echo $output->render($renderable);
}

echo $output->footer();
