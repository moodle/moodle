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
 * @package    block_pu
 * @copyright  2021 onwards LSU Online & Continuing Education
 * @copyright  2021 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

// Authentication.
require_login();

// Set the user object.
global $USER;

// Inlcude the requisite helpers functionality.
require_once('classes/helpers.php');

// Set up the page params.
$pageparams = [
    'courseid' => required_param('courseid', PARAM_INT),
    'pcmid' => required_param('pcmid', PARAM_INT)
];

// Map the params to some variables for usability.
$courseid = $pageparams['courseid'];
$pcmid    = $pageparams['pcmid'];

// Set the invalid param for later.
$pageparams['function'] = 'invalid';

// Map the userid.
$userid   = $USER->id;

// Build the url for the page.
$PAGE->set_url('/blocks/pu/replace.php', $pageparams);

// Security check.
if (!block_pu_helpers::guilduser_check($params = array('course_id' => $courseid, 'user_id' => $userid, 'pcmid'=> $pcmid))) {
    $url = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($url, get_string('nopermission', 'block_pu'), null, \core\output\notification::NOTIFY_ERROR);
}

// Get the course object.
$course = get_course($courseid);

// get the course context from Moodle.
$coursecontext = context_course::instance($course->id);

// Set the page context.
$PAGE->set_context($coursecontext);

// Throw an exception if user does not have capability to compose messages.
/*
require_capability('blocks/pu:replace',
                   $coursecontext,
                   $USER->id,
                   $doanything = true,
                   $errormessage = 'nopermissions',
                   $stringfile = 'block_pu');
*/

// Construct the page.
$PAGE->set_pagetype('block-pu');
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/blocks/pu/replace.php', $pageparams));
$PAGE->set_title(get_string('pluginname', 'block_pu'));
$PAGE->requires->js(new moodle_url('/blocks/pu/js.js'));

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_pu'));

// Set the required CSS.
$PAGE->requires->css(new moodle_url('/blocks/pu/styles.css'));

// Set the return navbar link to the coursetools.
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$qmnode = $coursenode->add(get_string('pluginname', 'block_pu'),
    new moodle_url('/course/view.php', array('id' => $courseid), $anchor = 'coursetools'));
$qmnode->make_active();

// Construct the warning.
$areyousurestr = get_string('pu_yousure', 'block_pu');

$areyousure = html_writer::span($areyousurestr, 'pu_you_sure');

// Construct the links.
$replacementlink = html_writer::link(new moodle_url('/blocks/pu/coder.php',
                                 $pageparams),
                                 get_string('pu_replace', 'block_pu'),
                                 array('onclick' => 'processClick();', 'id' => 'nodbl', 'class' => 'btn btn-outline-secondary pu_replace'));

$tryagainlink = html_writer::link(new moodle_url('/course/view.php',
                               array('id' => $courseid), $anchor = 'coursetools'),
                               get_string('pu_try_again', 'block_pu'),
                               array('class' => 'btn btn-primary pu_retry'));

// Output the page.
$out = html_writer::div($areyousure, 'pu_you_sure');
$out .= html_writer::div($tryagainlink, 'pu_retry');
$out .= html_writer::div($replacementlink, 'pu_replace');

// Echo the output.
echo $OUTPUT->header();
echo $out;
echo $OUTPUT->footer();
