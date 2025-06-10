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
$title = get_string('pluginname', 'block_lsuxe') . ': ' . get_string('archives', 'block_lsuxe');
$pagetitle = $title;
$sectiontitle = get_string('archives', 'block_lsuxe');
$url = new moodle_url($CFG->wwwroot . '/blocks/lsuxe/archives.php', $pageparams);
$worky = null;

// Are we looking at the form to add/update or the list?
// ------------------------------------------------------------------------
// If we want to push any data to javascript then we can add it here.
$initialload = array(
    "wwwroot" => $CFG->wwwroot,
    "xe_form" => "none",
    "xe_viewform" => 0
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
$PAGE->navbar->add(get_string('archives', 'block_lsuxe'), new moodle_url('archives.php'));
$PAGE->requires->css(new moodle_url($CFG->wwwroot . '/blocks/lsuxe/style.css'));
$PAGE->requires->js_call_amd('block_lsuxe/main', 'init');
$output = $PAGE->get_renderer('block_lsuxe');

// If the sent action is delete then the user just deleted a row, let's process it.
if ($pageparams['sent_action'] === "recovered") {
    $worky = new \block_lsuxe\controllers\form_controller("mappings");
    $worky->recover_record((int)$pageparams['sent_data']);
    \core\notification::success(get_string('recoverarchive', 'block_lsuxe'));
}


// View the Moodle Instances.
echo $output->header();
echo $xtras;
$renderable = new \block_lsuxe\output\archives_view();
echo $output->render($renderable);

echo $output->footer();
