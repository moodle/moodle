<?php
// script to assign students to a meta course by selecting which courses the meta course comprises.
// this is basically a hack of student.php that uses courses instead.


    require_once("../config.php");
    require_once("lib.php");
 
    define("MAX_COURSES_PER_PAGE", 1000);

    $id = required_param('id',PARAM_INT);         // course id
    $add = optional_param('add', '', PARAM_ALPHA);
    $remove = optional_param('remove', '', PARAM_ALPHA);
    $search = optional_param('search', '', PARAM_ALPHA); // search string

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect (can't find it)");
    }

    require_login($course->id);

    if (!$course->metacourse) {
        redirect("$CFG->wwwroot/course/student.php?id=$course->id");
    }

    if (!isadmin()) {
        error("You must be an admin");
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

    print_header("$course->shortname: $strassigncourses", 
                 "$site->fullname", 
                 "<a href=\"view.php?id=$course->id\">$course->shortname</a> -> $strassigncourses", 
                 "studentform.searchtext");

/// Don't allow restricted teachers to even see this page (because it contains
/// a lot of email addresses and access to all student on the server

    check_for_restricted_user($USER->username, "$CFG->wwwroot/course/view.php?id=$course->id");

/// Print a help notice about the need to use this page

    if (!$frm = data_submitted()) {
        $note = get_string("importmetacoursenote");   
        print_simple_box($note, "center", "50%");

/// A form was submitted so process the input

    } else {
        if (!empty($frm->add) and !empty($frm->addselect) and confirm_sesskey()) {
            $timestart = $timeend = 0;
            foreach ($frm->addselect as $addcourse) {
                set_time_limit(10);
                if (!add_to_metacourse($course->id,$addcourse)) {
                    error("Could not add the selected course to this meta course!");
                }
            }
        } else if (!empty($frm->remove) and !empty($frm->removeselect) and confirm_sesskey()) {
            foreach ($frm->removeselect as $removecourse) {
                set_time_limit(10);
                if (! remove_from_metacourse($course->id,$removecourse)) {
                    error("Could not remove the selected course from this meta course!");
                }
            }
        } else if (!empty($frm->showall) and confirm_sesskey()) {
            unset($frm->searchtext);
            $frm->previoussearch = 0;
        }
    }
    

    $previoussearch = (is_object($frm) && ((!empty($frm->search) or ($frm->previoussearch == 1)))) ;

    /// Get all existing students and teachers for this course.
    if(! $alreadycourses = get_courses_in_metacourse($course->id)) {
        $alreadycourses = array();
    }

    $numcourses = 0;


/// Get search results excluding any users already in this course
    if (!empty($frm->searchtext) and $previoussearch and confirm_sesskey()) {
        $searchcourses = get_courses_search(explode(" ",$frm->searchtext),'fullname ASC',0,99999,$numcourses);
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
    
/// If no search results then get potential students for this course excluding users already in course
    if (empty($searchcourses)) {
        
        $numcourses = get_courses_notin_metacourse($course->id,true);

        $courses = array();

        if ($numcourses <= MAX_COURSES_PER_PAGE) {
            $courses = get_courses_notin_metacourse($course->id);
        }
    }


    $searchtext = (isset($frm->searchtext)) ? $frm->searchtext : "";
    $previoussearch = ($previoussearch) ? '1' : '0';

    print_simple_box_start("center");

    include('importstudents.html');

    print_simple_box_end();

    print_footer();





?>
