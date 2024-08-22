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
 * Display user activity reports for a course (totals)
 *
 * @package    report
 * @subpackage outline
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use report_outline\output\hierarchicalactivities;

require('../../config.php');
require_once($CFG->dirroot.'/report/outline/locallib.php');
require_once($CFG->dirroot.'/report/outline/lib.php');

$userid   = required_param('id', PARAM_INT);
$courseid = required_param('course', PARAM_INT);
$mode     = optional_param('mode', 'outline', PARAM_ALPHA);

if ($mode !== 'complete' and $mode !== 'outline') {
    $mode = 'outline';
}

$user = $DB->get_record('user', array('id'=>$userid, 'deleted'=>0), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);

$coursecontext   = context_course::instance($course->id);
$personalcontext = context_user::instance($user->id);

if ($courseid == SITEID) {
    $PAGE->set_context($personalcontext);
}

if ($USER->id != $user->id and has_capability('moodle/user:viewuseractivitiesreport', $personalcontext)
        and !is_enrolled($coursecontext, $USER) and is_enrolled($coursecontext, $user)) {
    //TODO: do not require parents to be enrolled in courses - this is a hack!
    require_login();
    $PAGE->set_course($course);
} else {
    require_login($course);
}
$PAGE->set_url('/report/outline/user.php', array('id'=>$userid, 'course'=>$courseid, 'mode'=>$mode));

if (!report_outline_can_access_user_report($user, $course)) {
    throw new \moodle_exception('nocapability', 'report_outline');
}

$stractivityreport = get_string('activityreport');

$PAGE->set_pagelayout('report');
$PAGE->set_url('/report/outline/user.php', array('id'=>$user->id, 'course'=>$course->id, 'mode'=>$mode));
$PAGE->navigation->extend_for_user($user);
$PAGE->navigation->set_userid_for_parent_checks($user->id); // see MDL-25805 for reasons and for full commit reference for reversal when fixed.
$PAGE->set_title("$course->shortname: $stractivityreport");

// Create the appropriate breadcrumb.
$navigationnode = array(
        'url' => new moodle_url('/report/outline/user.php', array('id' => $user->id, 'course' => $course->id, 'mode' => $mode))
    );
if ($mode === 'complete') {
    $navigationnode['name'] = get_string('completereport');
} else {
    $navigationnode['name'] = get_string('outlinereport');
}
$PAGE->add_report_nodes($user->id, $navigationnode);

if ($courseid == SITEID) {
    $PAGE->set_heading(fullname($user));
} else {
    $PAGE->set_heading($course->fullname);
}

// Trigger a report viewed event.
$event = \report_outline\event\report_viewed::create(array('context' => context_course::instance($course->id),
        'relateduserid' => $userid, 'other' => array('mode' => $mode)));
$event->trigger();

echo $OUTPUT->header();
if ($courseid != SITEID) {
    $backurl = new moodle_url('/user/view.php', ['id' => $userid, 'course' => $courseid]);
    echo $OUTPUT->single_button($backurl, get_string('back'), 'get', ['class' => 'mb-3']);
    echo $OUTPUT->context_header(
            array(
            'heading' => fullname($user),
            'user' => $user,
            'usercontext' => $personalcontext
        ), 2);
    if ($mode === 'outline') {
        echo $OUTPUT->heading(get_string('outlinereport', 'moodle'), 2, 'main mt-4 mb-4');
    } else {
        echo $OUTPUT->heading(get_string('completereport', 'moodle'), 2, 'main mt-4 mb-4');
    }
}

$modinfo = get_fast_modinfo($course, $user->id);
$itemsprinted = false;

$coursestructure = new hierarchicalactivities($modinfo);
$sections = $coursestructure->export_hierarchy($OUTPUT);

foreach ($sections as $i => $section) {
    if (!$section['visible']) {
        continue;
    }
    $section = (object) $section;
    $itemsprinted = true;
    echo '<div class="section p-3 my-4">';
    echo '<h2 class="h4">';
    echo $section->text;
    echo "</h2>";

    echo '<div class="content">';

    if ($mode == "outline") {
        echo "<table>";
    }

    foreach ($section->activities as $cm) {
        if (!$cm['visible']) {
            continue;
        }
        $cm = (object) $cm;
        if ($cm->isdelegated) {
            // Print subsection box.
            if ($mode == "outline") {
                echo "</table>";
            }
            echo '<div class="section subsection p-3 my-2">';
            echo '<h3 class="font-lg">';
            echo $cm->text;
            echo "</h3>";

            echo '<div class="content">';

            if ($mode == "outline") {
                echo "<table>";
            }

            foreach ($cm->activities as $subcm) {
                if (!$subcm['visible']) {
                    continue;
                }
                $subcm = (object) $subcm;
                $mod = $modinfo->cms[$subcm->id];
                $coursestructure->print_activity($OUTPUT, $mode, $mod, $user, $course);
            }
            if ($mode == "outline") {
                echo "</table>";
            }
            echo "</div>"; // Content.
            echo "</div>"; // Subsection.
            if ($mode == "outline") {
                echo "<table>";
            }

            continue;
        }

        $mod = $modinfo->cms[$cm->id];
        $coursestructure->print_activity($OUTPUT, $mode, $mod, $user, $course);
    }

    if ($mode == "outline") {
        echo "</table>";
    }
    echo '</div>';  // Content.
    echo '</div>';  // Section.
}

if (!$itemsprinted) {
    echo $OUTPUT->notification(get_string('nothingtodisplay'), 'info', false);
}

echo $OUTPUT->footer();
