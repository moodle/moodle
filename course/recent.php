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
 * Display all recent activity in a flexible way
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once('../config.php');
require_once('lib.php');
require_once('recent_form.php');

$id = required_param('id', PARAM_INT);

$PAGE->set_url('/course/recent.php', array('id'=>$id));

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error("That's an invalid course id");
}

require_login($course);

add_to_log($course->id, "course", "recent", "recent.php?id=$course->id", $course->id);

$context = context_course::instance($course->id);

$lastlogin = time() - COURSE_MAX_RECENT_PERIOD;
if (!isguestuser() and !empty($USER->lastcourseaccess[$COURSE->id])) {
    if ($USER->lastcourseaccess[$COURSE->id] > $lastlogin) {
        $lastlogin = $USER->lastcourseaccess[$COURSE->id];
    }
}

$param = new stdClass();
$param->user   = 0;
$param->modid  = 'all';
$param->group  = 0;
$param->sortby = 'default';
$param->date   = $lastlogin;
$param->id     = $COURSE->id;

$mform = new recent_form();
$mform->set_data($param);
if ($formdata = $mform->get_data()) {
    $param = $formdata;
}

$userinfo = get_string('allparticipants');
$dateinfo = get_string('alldays');

if (!empty($param->user)) {
    if (!$u = $DB->get_record('user', array('id'=>$param->user))) {
        print_error("That's an invalid user!");
    }
    $userinfo = fullname($u);
}

$strrecentactivity = get_string('recentactivity');
$PAGE->navbar->add($strrecentactivity, new moodle_url('/course/recent.php', array('id'=>$course->id)));
$PAGE->navbar->add($userinfo);
$PAGE->set_title("$course->shortname: $strrecentactivity");
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($course->fullname) . ": $userinfo", 2);

$mform->display();

$modinfo = get_fast_modinfo($course);
$modnames = get_module_types_names();

if (has_capability('moodle/course:viewhiddensections', $context)) {
    $hiddenfilter = "";
} else {
    $hiddenfilter = "AND cs.visible = 1";
}
$sections = array();
foreach ($modinfo->get_section_info_all() as $i => $section) {
    if (!empty($section->uservisible)) {
        $sections[$i] = $section;
    }
}

if ($param->modid === 'all') {
    // ok

} else if (strpos($param->modid, 'mod/') === 0) {
    $modname = substr($param->modid, strlen('mod/'));
    if (array_key_exists($modname, $modnames) and file_exists("$CFG->dirroot/mod/$modname/lib.php")) {
        $filter = $modname;
    }

} else if (strpos($param->modid, 'section/') === 0) {
    $sectionid = substr($param->modid, strlen('section/'));
    if (isset($sections[$sectionid])) {
        $sections = array($sectionid=>$sections[$sectionid]);
    }

} else if (is_numeric($param->modid)) {
    $sectionnum = $modinfo->cms[$param->modid]->sectionnum;
    $filter_modid = $param->modid;
    $sections = array($sectionnum => $sections[$sectionnum]);
}


$modinfo->get_groups(); // load all my groups and cache it in modinfo

$activities = array();
$index = 0;

foreach ($sections as $sectionnum => $section) {

    $activity = new stdClass();
    $activity->type = 'section';
    if ($section->section > 0) {
        $activity->name = get_section_name($course, $section);
    } else {
        $activity->name = '';
    }

    $activity->visible = $section->visible;
    $activities[$index++] = $activity;

    if (empty($modinfo->sections[$sectionnum])) {
        continue;
    }

    foreach ($modinfo->sections[$sectionnum] as $cmid) {
        $cm = $modinfo->cms[$cmid];

        if (!$cm->uservisible) {
            continue;
        }

        if (!empty($filter) and $cm->modname != $filter) {
            continue;
        }

        if (!empty($filter_modid) and $cmid != $filter_modid) {
            continue;
        }

        $libfile = "$CFG->dirroot/mod/$cm->modname/lib.php";

        if (file_exists($libfile)) {
            require_once($libfile);
            $get_recent_mod_activity = $cm->modname."_get_recent_mod_activity";

            if (function_exists($get_recent_mod_activity)) {
                $activity = new stdClass();
                $activity->type    = 'activity';
                $activity->cmid    = $cmid;
                $activities[$index++] = $activity;
                $get_recent_mod_activity($activities, $index, $param->date, $course->id, $cmid, $param->user, $param->group);
            }
        }
    }
}

$detail = true;

switch ($param->sortby) {
    case 'datedesc' : usort($activities, 'compare_activities_by_time_desc'); break;
    case 'dateasc'  : usort($activities, 'compare_activities_by_time_asc'); break;
    case 'default'  :
    default         : $detail = false; $param->sortby = 'default';

}

if (!empty($activities)) {

    $newsection   = true;
    $lastsection  = '';
    $newinstance  = true;
    $lastinstance = '';
    $inbox        = false;

    $section = 0;

    $activity_count = count($activities);
    $viewfullnames  = array();

    foreach ($activities as $key => $activity) {

        if ($activity->type == 'section') {
            if ($param->sortby != 'default') {
                continue; // no section if ordering by date
            }
            if ($activity_count == ($key + 1) or $activities[$key+1]->type == 'section') {
            // peak at next activity.  If it's another section, don't print this one!
            // this means there are no activities in the current section
                continue;
            }
        }

        if (($activity->type == 'section') && ($param->sortby == 'default')) {
            if ($inbox) {
                echo $OUTPUT->box_end();
                echo $OUTPUT->spacer(array('height'=>30, 'br'=>true)); // should be done with CSS instead
            }
            echo $OUTPUT->box_start();
            if (!empty($activity->name)) {
                echo html_writer::tag('h2', $activity->name);
            }
            $inbox = true;

        } else if ($activity->type == 'activity') {

            if ($param->sortby == 'default') {
                $cm = $modinfo->cms[$activity->cmid];

                if ($cm->visible) {
                    $class = '';
                } else {
                    $class = 'dimmed';
                }
                $name        = format_string($cm->name);
                $modfullname = $modnames[$cm->modname];

                $image = $OUTPUT->pix_icon('icon', $modfullname, $cm->modname, array('class' => 'icon smallicon'));
                $link = html_writer::link(new moodle_url("/mod/$cm->modname/view.php",
                            array("id" => $cm->id)), $name, array('class' => $class));
                echo html_writer::tag('h3', "$image $modfullname $link");
           }

        } else {

            if (!isset($viewfullnames[$activity->cmid])) {
                $cm_context = context_module::instance($activity->cmid);
                $viewfullnames[$activity->cmid] = has_capability('moodle/site:viewfullnames', $cm_context);
            }

            if (!$inbox) {
                echo $OUTPUT->box_start();
                $inbox = true;
            }

            $print_recent_mod_activity = $activity->type.'_print_recent_mod_activity';

            if (function_exists($print_recent_mod_activity)) {
                $print_recent_mod_activity($activity, $course->id, $detail, $modnames, $viewfullnames[$activity->cmid]);
            }
        }
    }

    if ($inbox) {
        echo $OUTPUT->box_end();
    }


} else {

    echo html_writer::tag('h3', get_string('norecentactivity'), array('class' => 'mdl-align'));

}

echo $OUTPUT->footer();

function compare_activities_by_time_desc($a, $b) {
    // make sure the activities actually have a timestamp property
    if ((!array_key_exists('timestamp', $a)) or (!array_key_exists('timestamp', $b))) {
      return 0;
    }
    if ($a->timestamp == $b->timestamp)
        return 0;
    return ($a->timestamp > $b->timestamp) ? -1 : 1;
}

function compare_activities_by_time_asc($a, $b) {
    // make sure the activities actually have a timestamp property
    if ((!array_key_exists('timestamp', $a)) or (!array_key_exists('timestamp', $b))) {
      return 0;
    }
    if ($a->timestamp == $b->timestamp)
        return 0;
    return ($a->timestamp < $b->timestamp) ? -1 : 1;
}

