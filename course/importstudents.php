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
 * Script to assign students to a meta course by selecting which courses the meta
 * course comprises. This is basically a hack of student.php that uses courses instead.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

    require_once("../config.php");
    require_once("lib.php");

    define("MAX_COURSES_PER_PAGE", 1000);

    $id             = required_param('id',PARAM_INT); // course id
    $add            = optional_param('add', 0, PARAM_BOOL);
    $remove         = optional_param('remove', 0, PARAM_BOOL);
    $showall        = optional_param('showall', 0, PARAM_BOOL);
    $searchtext     = optional_param('searchtext', '', PARAM_RAW); // search string
    $previoussearch = optional_param('previoussearch', 0, PARAM_BOOL);
    $previoussearch = ($searchtext != '') or ($previoussearch) ? 1:0;

    $url = new moodle_url($CFG->wwwroot.'/course/importstudents.php', array('id'=>$id));
    if ($add !== 0) {
        $url->param('add', $add);
    }
    if ($remove !== 0) {
        $url->param('remove', $remove);
    }
    if ($showall !== 0) {
        $url->param('showall', $showall);
    }
    if ($searchtext !== '') {
        $url->param('searchtext', $searchtext);
    }
    if ($previoussearch !== 0) {
        $url->param('previoussearch', $previoussearch);
    }
    $PAGE->set_url($url);

    $site = get_site();

    if (!$course = $DB->get_record('course', array('id'=>$id))) {
        print_error("invalidcourseid");
    }

    require_login($course->id);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/course:managemetacourse', $context);

    if (!$course->metacourse) {
        redirect("$CFG->wwwroot/course/view.php?id=$course->id");
    }

    $strassigncourses = get_string('metaassigncourses');
    $stralreadycourses = get_string('metaalreadycourses');
    $strnoalreadycourses = get_string('metanoalreadycourses');
    $strpotentialcourses = get_string('metapotentialcourses');
    $strnopotentialcourses = get_string('metanopotentialcourses');
    $straddcourses = get_string('metaaddcourse');
    $strremovecourse = get_string('metaremovecourse');
    $strsearch        = get_string("search");
    $strsearchresults  = get_string("searchresults");
    $strcourses   = get_string("courses");
    $strshowall = get_string("showall");

    $PAGE->navbar->add($strassigncourses);
    $PAGE->set_title("$course->shortname: $strassigncourses");
    $PAGE->set_heading($site->fullname);
    $PAGE->set_focuscontrol("searchtext");
    echo $OUTPUT->header();

/// Print a help notice about the need to use this page

    echo $OUTPUT->heading(get_string('childcourses'));

    if (!$frm = data_submitted()) {
        $note = get_string("importmetacoursenote");
        echo $OUTPUT->box($note);

/// A form was submitted so process the input

    } else {
        if ($add and !empty($frm->addselect) and confirm_sesskey()) {
            $timestart = $timeend = 0;
            foreach ($frm->addselect as $addcourse) {
                $addcourse = clean_param($addcourse, PARAM_INT);
                set_time_limit(180);
                if (!add_to_metacourse($course->id,$addcourse)) {
                    print_error("cannotmetacourse");
                }
            }
        } else if ($remove and !empty($frm->removeselect) and confirm_sesskey()) {
            foreach ($frm->removeselect as $removecourse) {
                set_time_limit(180);
                $removecourse = clean_param($removecourse, PARAM_INT);
                if (! remove_from_metacourse($course->id,$removecourse)) {
                    print_error("cannotremovefrommeta");
                }
            }
        } else if ($showall and confirm_sesskey()) {
            $searchtext = '';
            $previoussearch = 0;
        }
    }


/// Get all existing students and teachers for this course.
    if(! $alreadycourses = get_courses_in_metacourse($course->id)) {
        $alreadycourses = array();
    }

    $numcourses = 0;


/// Get search results excluding any users already in this course
    if (($searchtext != '') and $previoussearch and confirm_sesskey()) {
        if ($searchcourses = get_courses_search(explode(" ",$searchtext),'fullname ASC',0,99999,$numcourses)) {
            foreach ($searchcourses as $tmp) {
                if (array_key_exists($tmp->id,$alreadycourses)) {
                    unset($searchcourses[$tmp->id]);
                }
                if (!empty($tmp->metacourse)) {
                    unset($searchcourses[$tmp->id]);
                }
            }
            if (array_key_exists($course->id,$searchcourses)) {
                unset($searchcourses[$course->id]);
            }
            $numcourses = count($searchcourses);
        }
    }

/// If no search results then get potential students for this course excluding users already in course
    if (empty($searchcourses)) {
        $numcourses = count_courses_notin_metacourse($course->id);

        if ($numcourses > 0 and $numcourses <= MAX_COURSES_PER_PAGE) {
            $courses = get_courses_notin_metacourse($course->id);
        } else {
            $courses = array();
        }
    }

    echo $OUTPUT->box_start();

    include('importstudents.html');

    echo $OUTPUT->box_end();

    echo $OUTPUT->footer();


