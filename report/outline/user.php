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
    print_error('nocapability', 'report_outline');
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
    echo $OUTPUT->context_header(
            array(
            'heading' => fullname($user),
            'user' => $user,
            'usercontext' => $personalcontext
        ), 2);
}

$modinfo = get_fast_modinfo($course, $user->id);
$sections = $modinfo->get_section_info_all();
$itemsprinted = false;

foreach ($sections as $i => $section) {

        if ($section->uservisible) { // prevent hidden sections in user activity. Thanks to Geoff Wilbert!
            // Check the section has modules/resources, if not there is nothing to display.
            if (!empty($modinfo->sections[$i])) {
                $itemsprinted = true;
                echo '<div class="section">';
                echo '<h2>';
                echo get_section_name($course, $section);
                echo "</h2>";

                echo '<div class="content">';

                if ($mode == "outline") {
                    echo "<table cellpadding=\"4\" cellspacing=\"0\">";
                }

                foreach ($modinfo->sections[$i] as $cmid) {
                    $mod = $modinfo->cms[$cmid];

                    if (empty($mod->uservisible)) {
                        continue;
                    }

                    $instance = $DB->get_record("$mod->modname", array("id"=>$mod->instance));
                    $libfile = "$CFG->dirroot/mod/$mod->modname/lib.php";

                    if (file_exists($libfile)) {
                        require_once($libfile);

                        switch ($mode) {
                            case "outline":
                                $user_outline = $mod->modname."_user_outline";
                                if (function_exists($user_outline)) {
                                    $output = $user_outline($course, $user, $mod, $instance);
                                } else {
                                    $output = report_outline_user_outline($user->id, $cmid, $mod->modname, $instance->id);
                                }
                                report_outline_print_row($mod, $instance, $output);
                                break;
                            case "complete":
                                $user_complete = $mod->modname."_user_complete";
                                $image = $OUTPUT->pix_icon('icon', $mod->modfullname, 'mod_'.$mod->modname, array('class'=>'icon'));
                                echo "<h4>$image $mod->modfullname: ".
                                     "<a href=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">".
                                     format_string($instance->name,true)."</a></h4>";

                                ob_start();

                                echo "<ul>";
                                if (function_exists($user_complete)) {
                                    $user_complete($course, $user, $mod, $instance);
                                } else {
                                    echo report_outline_user_complete($user->id, $cmid, $mod->modname, $instance->id);
                                }
                                echo "</ul>";

                                $output = ob_get_contents();
                                ob_end_clean();

                                if (str_replace(' ', '', $output) != '<ul></ul>') {
                                    echo $output;
                                }
                                break;
                            }
                        }
                    }

                if ($mode == "outline") {
                    echo "</table>";
                }
                echo '</div>';  // content
                echo '</div>';  // section
            }
        }
}

if (!$itemsprinted) {
    echo $OUTPUT->notification(get_string('nothingtodisplay'));
}

echo $OUTPUT->footer();
