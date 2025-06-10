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
       'pcmid' => required_param('pcmid', PARAM_INT),
    'function' => required_param('function', PARAM_TEXT)
];

// Map the params to some variables for usability.
$courseid = $pageparams['courseid'];
$pcmid    = isset($pageparams['pcmid']) ? $pageparams['pcmid'] : null;
$function = $pageparams['function'];

// Map the userid.
$userid   = $USER->id;

// Set this up so we know if we're marking thse as used or invalid.
$usedorinvalid = $function == 'used' ? get_string('markused', 'block_pu') : get_string('markinvalid', 'block_pu');

// Make sure the person is a GUILD user in the course in question.
$validguilduser = block_pu_helpers::guilduser_check($params = array('course_id' => $courseid, 'user_id' => $userid));

// Grab the number of total codes assigned to the requester in the course.
$tused  = block_pu_helpers::pu_uvcount($params = array('course_id' => $courseid, 'user_id' => $userid, 'uv' => 'total'));

if ($pcmid === 0 && $validguilduser) {
    block_pu_helpers::pu_assign($params = array('course_id' => $courseid, 'user_id' => $userid));
    $url = new moodle_url('/course/view.php', array('id' => $courseid), $anchor = 'coursetools');
    redirect($url, get_string('assigned', 'block_pu'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Security check making sure you have access to the PCMID in question.
if ($pcmid > 0 && !block_pu_helpers::guilduser_check($params = array('course_id' => $courseid, 'user_id' => $userid, 'pcmid'=> $pcmid))) {
    $url = new moodle_url('/course/view.php', array('id' => $courseid), $anchor = 'coursetools');
    redirect($url, get_string('nopermission', 'block_pu'), null, \core\output\notification::NOTIFY_ERROR);
}

// Precheck if it's already used and owned by the person requesting the action.
$precheck = block_pu_helpers::pu_pcmexists($params = array('course_id' => $courseid, 'user_id' => $userid, 'pcm_id'=> $pcmid));

// If you are who you claim to be and have an associated PCMid for the course in question, mark it used / invalid.
if ($pcmid > 0 && $precheck) {

    // Run this here instead of par of the if statement.
    $marked = block_pu_helpers::pu_mark($params = array('course_id' => $courseid, 'user_id' => $userid, 'pcmid'=> $pcmid, 'function' => $function));

    // Grab the count of used codes for this course / user.
    $used  = block_pu_helpers::pu_uvcount($params = array('course_id' => $courseid, 'user_id' => $userid, 'uv' => 'used'));

    // Grab the total number of codes allocated for this course.
    $total = block_pu_helpers::pu_codetotals($params = array('course_id' => $courseid));

    // Override the lang if we've hit the max number of used codes.
    if ($used >= $total->codecount) {
        $usedorinvalid = $function == 'used' ? get_string('lastused', 'block_pu') : "I do not know what to do here!"; 
    }

    // Only assign a new code if we have codes left.
    if ($used < $total->codecount) {
        // We've marked the old code as either invalid or used, now assign a new one.
        block_pu_helpers::pu_assign($params = array('course_id' => $courseid, 'user_id' => $userid)); 
    }

    // Redirect them to their cooursetools.
    $url = new moodle_url('/course/view.php', array('id' => $courseid), $anchor = 'coursetools');
    redirect($url, $usedorinvalid, null, \core\output\notification::NOTIFY_SUCCESS);

// If something goes horribly wrong.
} else {
    $url = new moodle_url('/course/view.php', array('id' => $courseid), $anchor = 'coursetools');
    redirect($url, get_string('nothingtodo', 'block_pu'), null, \core\output\notification::NOTIFY_WARNING);
}
