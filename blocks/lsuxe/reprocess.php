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
require_once($CFG->dirroot . '/blocks/lsuxe/lib.php');

// Disables the time limit.
set_time_limit(0);

// Set up the page params.
$pageparams = [
    'courseid' => required_param('courseid', PARAM_INT),
    'moodleid' => required_param('moodleid', PARAM_INT),
    'function' => required_param('function', PARAM_TEXT)
];

$function = $pageparams['function'];

// Authentication.
require_login();
if (!is_siteadmin()) {
    $helpers->redirect_to_url('/my');
}

$langstring = "reprocess_$function";
$backlangstring = "xebacktomoodle";

$title = get_string('pluginname', 'block_lsuxe') . ': ' . get_string('reprocess', 'block_lsuxe');
$pagetitle = $title;
$sectiontitle = get_string($langstring, 'block_lsuxe');
$url = new moodle_url('/blocks/lsuxe/lsuxe.php');
$context = \context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

// Navbar Bread Crumbs.
$PAGE->navbar->add(get_string($backlangstring, 'block_lsuxe'), new moodle_url('/blocks/lsuxe/lsuxe.php'));

echo $OUTPUT->header();
echo $OUTPUT->heading($sectiontitle);

$starttime = microtime(true);

if (lsuxe_helpers::is_ues()) {
    mtrace("Using LSU UES");
} else {
    mtrace("Normal Moodle Enrollment");
}

echo"<pre>";
lsuxe_helpers::xe_write_destcourse($pageparams);

$groups = lsuxe_helpers::xe_get_groups($pageparams);

lsuxe_helpers::xe_write_destgroups($groups);

$users = lsuxe_helpers::xe_current_enrollments($pageparams);

$count = 0;
foreach ($users as $user) {
    $count++;

    $userstarttime = microtime(true);
    $remoteuser = lsuxe_helpers::xe_remote_user_lookup($user);
    if (isset($remoteuser['id'])) {
        $usermatch = lsuxe_helpers::xe_remote_user_match($user, $remoteuser);
        if (!$usermatch) {
            $updateuser = lsuxe_helpers::xe_remote_user_update($user, $remoteuser);
        }
    } else {
        $createduser = lsuxe_helpers::xe_remote_user_create($user);
        $remoteuser = $createduser;
    }

    if ($user->status == 'enrolled') {
        $enrolluser = lsuxe_helpers::xe_enroll_user($user, $remoteuser['id']);
        $enrolgroup = lsuxe_helpers::xe_add_user_to_group($user, $remoteuser['id']);
    } else if ($user->status == 'unenrolled') {
        $enrolluser = lsuxe_helpers::xe_unenroll_user($user, $remoteuser['id']);
    }

    $userelapsedtime = round(microtime(true) - $userstarttime, 3);

    lsuxe_helpers::processed($user->xemmid);

    mtrace("User #$count ($user->username) took " . $userelapsedtime . " seconds to process.\n");
}

$elapsedtime = round(microtime(true) - $starttime, 3);
mtrace("\n\nThis entire process took " . $elapsedtime . " seconds.");
echo"</pre>";

echo $OUTPUT->footer();
