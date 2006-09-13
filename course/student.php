<?php // $Id$
      // Script to assign students to courses
    //deprecated, should use admin/roles/assign.php now
    require_once("../config.php");

    define("MAX_USERS_PER_PAGE", 5000);

    $id             = required_param('id',PARAM_INT); // course id
    $add            = optional_param('add', 0, PARAM_BOOL);
    $remove         = optional_param('remove', 0, PARAM_BOOL);
    $showall        = optional_param('showall', 0, PARAM_BOOL);
    $searchtext     = optional_param('searchtext', '', PARAM_RAW); // search string
    $previoussearch = optional_param('previoussearch', 0, PARAM_BOOL);
    $previoussearch = ($searchtext != '') or ($previoussearch) ? 1:0;

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect (can't find it)");
    }

    if ($course->metacourse) {
        redirect("$CFG->wwwroot/course/importstudents.php?id=$course->id");
    }

    require_login($course->id);

    if (!isteacheredit($course->id)) {
        error("You must be an editing teacher in this course, or an admin");
    }

    $strassignstudents = get_string("assignstudents");
    $strexistingstudents   = get_string("existingstudents");
    $strnoexistingstudents = get_string("noexistingstudents");
    $strpotentialstudents  = get_string("potentialstudents");
    $strnopotentialstudents  = get_string("nopotentialstudents");
    $straddstudent    = get_string("addstudent");
    $strremovestudent = get_string("removestudent");
    $strsearch        = get_string("search");
    $strsearchresults  = get_string("searchresults");
    $strstudents   = get_string("students");
    $strshowall = get_string("showall");


    if ($course->students != $strstudents) {
        $strassignstudents .= " ($course->students)";
        $strpotentialstudents .= " ($course->students)";
        $strexistingstudents .= " ($course->students)";
    }

    print_header("$course->shortname: $strassignstudents",
                 "$site->fullname",
                 "<a href=\"view.php?id=$course->id\">$course->shortname</a> -> $strassignstudents",
                 "studentform.searchtext");


/// Print a help notice about the need to use this page

    if (!$frm = data_submitted()) {
        $note = get_string("assignstudentsnote");

        if ($course->password) {
            $note .= "<p>".get_string("assignstudentspass", "",
                                      "<a href=\"edit.php?id=$course->id\">$course->password</a>");
        }
        print_simple_box($note, "center", "50%");

/// A form was submitted so process the input

    } else {
        if ($add and !empty($frm->addselect) and confirm_sesskey()) {
            if ($course->enrolperiod) {
                $timestart = time();
                $timeend   = $timestart + $course->enrolperiod;
            } else {
                $timestart = $timeend = 0;
            }
            foreach ($frm->addselect as $addstudent) {
                $addstudent = clean_param($addstudent, PARAM_INT);
                if (! enrol_student($addstudent, $course->id, $timestart, $timeend)) {
                    error("Could not add student with id $addstudent to this course!");
                }
            }
        } else if ($remove and !empty($frm->removeselect) and confirm_sesskey()) {
            foreach ($frm->removeselect as $removestudent) {
                $removestudent = clean_param($removestudent, PARAM_INT);
                if (! unenrol_student($removestudent, $course->id)) {
                    error("Could not remove student with id $removestudent from this course!");
                }
            }
        } else if ($showall) {
            $searchtext = '';
            $previoussearch = 0;
        }
    }


/// Get all existing students and teachers for this course.
    if (!$students = get_course_students($course->id, "u.firstname ASC, u.lastname ASC", "", 0, 99999,
                                         '', '', NULL, '', 'u.id,u.firstname,u.lastname,u.email')) {
        $students = array();
    }
    if (!$teachers = get_course_teachers($course->id)) {
        $teachers = array();
    }
    $existinguserarray = array();
    foreach ($students as $student) {
        $existinguserarray[] = $student->id;
    }
    foreach ($teachers as $teacher) {
        $existinguserarray[] = $teacher->id;
    }
    $existinguserlist = implode(',', $existinguserarray);

    unset($existinguserarray);


/// Get search results excluding any users already in this course
    if (($searchtext != '') and $previoussearch) {
        $searchusers = get_users(true, $searchtext, true, $existinguserlist, 'firstname ASC, lastname ASC',
                                      '', '', 0, 99999, 'id, firstname, lastname, email');
        $usercount = get_users(false, '', true, $existinguserlist);
    }

/// If no search results then get potential students for this course excluding users already in course
    if (empty($searchusers)) {

        $usercount = get_users(false, '', true, $existinguserlist, 'firstname ASC, lastname ASC', '', '',
                              0, 99999, 'id, firstname, lastname, email') ;
        $users = array();

        if ($usercount <= MAX_USERS_PER_PAGE) {
            $users = get_users(true, '', true, $existinguserlist, 'firstname ASC, lastname ASC', '', '',
                               0, 99999, 'id, firstname, lastname, email');
        }

    }


    print_simple_box_start("center");

    include('student.html');

    print_simple_box_end();

    print_footer($course);

?>
