<?php   /// $Id$
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2004  Martin Dougiamas  http://moodle.com               //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////


/**
* enrolment_base is the base class for enrolment plugins
*
* This class provides all the functionality for an enrolment plugin
* In fact it includes all the code for the default, "internal" method
* so that other plugins can override these as necessary.
*/

class enrolment_base {

var $errormsg;



/**
* Returns information about the courses a student has access to
*
* Set the $user->student course array
* Set the $user->timeaccess course array
*
* @param    user  referenced object, must contain $user->id already set
*/
function get_student_courses(&$user) {

    if ($students = get_records("user_students", "userid", $user->id)) {
        $currenttime = time();
        foreach ($students as $student) {

        /// Is course visible?

            if (get_field("course", "visible", "id", $student->course)) {

            /// Is the student enrolment active right now?

                if ( ( $student->timestart == 0 or ( $currenttime > $student->timestart )) and 
                     ( $student->timeend   == 0 or ( $currenttime < $student->timeend )) ) {
                    $user->student[$student->course] = true;
                    $user->timeaccess[$student->course] = $student->timeaccess;
                }
            }
        }
    }   
}



/**
* Returns information about the courses a student has access to
*
* Set the $user->teacher course array
* Set the $user->teacheredit course array
* Set the $user->timeaccess course array
*
* @param    user  referenced object, must contain $user->id already set
*/
function get_teacher_courses(&$user) {

    if ($teachers = get_records("user_teachers", "userid", $user->id)) {
        $currenttime = time();
        foreach ($teachers as $teacher) {

        /// Is teacher only teaching this course for a specific time period?

            if ( ( $teacher->timestart == 0 or ( $currenttime > $teacher->timestart )) and 
                 ( $teacher->timeend   == 0 or ( $currenttime < $teacher->timeend )) ) {

                $user->teacher[$teacher->course] = true;

                if ($teacher->editall) {
                    $user->teacheredit[$teacher->course] = true;
                }   

                $user->timeaccess[$teacher->course] = $teacher->timeaccess;
            }
        }   
    }
}




/**
* Prints the entry form/page for this enrolment
*
* This is only called from course/enrol.php
* Most plugins will probably override this to print payment 
* forms etc, or even just a notice to say that manual enrolment 
* is disabled
*
* @param    course  current course object
*/
function print_entry($course) {
    global $CFG, $USER, $SESSION, $THEME;

    $strloginto = get_string("loginto", "", $course->shortname);
    $strcourses = get_string("courses");



/// Automatically enrol into courses without password

    if ($course->password == "") {   // no password, so enrol

        if (isguest()) {
            add_to_log($course->id, "course", "guest", "view.php?id=$course->id", "$USER->id");

        } else if (empty($_GET['confirm']) && empty($_GET['cancel'])) {

            print_header($strloginto, $course->fullname, "<a href=\".\">$strcourses</a> -> $strloginto");
            echo "<br />";
            notice_yesno(get_string("enrolmentconfirmation"), "enrol.php?id=$course->id&amp;confirm=1", "enrol.php?id=$course->id&amp;cancel=1");
            print_footer();
            exit;

        } elseif (!empty($_GET['confirm'])) {
            if ($course->enrolperiod) {
                $timestart = time();
                $timeend = time() + $course->enrolperiod;
            } else {
                $timestart = $timeend = 0;
            }

            if (! enrol_student($USER->id, $course->id, $timestart, $timeend, 'manual')) {
                error("An error occurred while trying to enrol you.");
            }

            $subject = get_string("welcometocourse", "", $course->fullname);
            $a->coursename = $course->fullname;
            $a->profileurl = "$CFG->wwwroot/user/view.php?id=$USER->id&course=$course->id";
            $message = get_string("welcometocoursetext", "", $a);
            if (! $teacher = get_teacher($course->id)) {
                $teacher = get_admin();
            }
            email_to_user($USER, $teacher, $subject, $message);

            add_to_log($course->id, "course", "enrol", "view.php?id=$course->id", "$USER->id");

            $USER->student[$course->id] = true;

            if ($SESSION->wantsurl) {
                $destination = $SESSION->wantsurl;
                unset($SESSION->wantsurl);
            } else {
                $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
            }

            redirect($destination);
        } elseif (!empty($_GET['cancel'])) {
            unset($SESSION->wantsurl);
            redirect($CFG->wwwroot);
        }
    }

    $teacher = get_teacher($course->id);
    if (!isset($password)) {
        $password = "";
    }


    print_header($strloginto, $course->fullname, "<a href=\".\">$strcourses</a> -> $strloginto", "form.password");

    print_course($course, "80%");

    include("$CFG->dirroot/enrol/internal/enrol.html");

    print_footer();

}



/**
* The other half to print_entry, this checks the form data
*
* This function checks that the user has completed the task on the 
* enrolment entry page and then enrolls them.
*
* @param    form    the form data submitted, as an object
* @param    course  the current course, as an object
*/
function check_entry($form, $course) {
    global $CFG, $USER, $SESSION, $THEME;

    if (empty($form->password)) {
        $form->password = '';
    }

    $groupid = $this->check_group_entry($course->id, $form->password);
    if (($form->password == $course->password) or ($groupid !== false) ) {

        if (isguest()) {
        
            add_to_log($course->id, "course", "guest", "view.php?id=$course->id", $_SERVER['REMOTE_ADDR']);
            
        } else {  /// Update or add new enrolment

            if ($course->enrolperiod) {
                $timestart = time();
                $timeend   = $timestart + $course->enrolperiod;
            } else {
                $timestart = $timeend = 0;
            }

            if (! enrol_student($USER->id, $course->id, $timestart, $timeend, 'manual')) {
                error("An error occurred while trying to enrol you.");
            }

            if ($groupid !== false) {
                if (add_user_to_group($groupid, $USER->id)) {
                    $USER->groupmember[$course->id] = $groupid;
                } else {
                    error("An error occurred while trying to add you to a group");
                }
            }

            $subject = get_string("welcometocourse", "", $course->fullname);
            $a->coursename = $course->fullname;
            $a->profileurl = "$CFG->wwwroot/user/view.php?id=$USER->id&amp;course=$course->id";
            $message = get_string("welcometocoursetext", "", $a);
            
            if (! $teacher = get_teacher($course->id)) {
                $teacher = get_admin();
            }
            
            email_to_user($USER, $teacher, $subject, $message);
            add_to_log($course->id, "course", "enrol", "view.php?id=$course->id", "$USER->id");
        }
        
        $USER->student[$course->id] = true;
        
        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
        }
        
        redirect($destination);

    } else {
        $this->errormsg = get_string("enrolmentkeyhint", "", substr($course->password,0,1));
    }
                        
}


/**
* Check if the given enrolment key matches a group enrolment key for the given course
*
* Check if the given enrolment key matches a group enrolment key for the given course
*
* @param    courseid  the current course id
* @param    password  the submitted enrolment key
*/
function check_group_entry ($courseid, $password) {
    $ingroup = false;
    if ( ($groups = get_groups($courseid)) !== false ) {
        foreach ($groups as $group) 
            if ( !empty($group->password) and ($password == $group->password) )
                $ingroup = $group->id;
    }
    return $ingroup;
}


/**
* Prints a form for configuring the current enrolment plugin
*
* This function is called from admin/enrol.php, and outputs a 
* full page with a form for defining the current enrolment plugin.
*
* @param    page  an object containing all the data for this page
*/
function config_form($page) {
    
}


/**
* Processes and stored configuration data for the enrolment plugin
*
* Processes and stored configuration data for the enrolment plugin
*
* @param    config  all the configuration data as entered by the admin
*/
function process_config($config) {

    $return = true;

    foreach ($config as $name => $value) {
        if (!set_config($name, $value)) {
            $return = false;
        }
    }

    return $return;
}


/**
* This function is run by admin/cron.php every time 
*
* The cron function can perform regular checks for the current 
* enrollment plugin.  For example it can check a foreign database,
* all look for a file to pull data in from
*
*/
function cron() {
    // Delete students from all courses where their enrolment period has expired
    
    $select = "timeend > '0' AND timeend < '" . time() . "'";
    
    if ($students = get_records_select('user_students', $select)) {
        foreach ($students as $student) {
            if ($course = get_record('course', 'id', $student->course)) {
                if (empty($course->enrolperiod)) {   // This overrides student timeend
                    continue;
                }
            }
            unenrol_student($student->userid, $student->course);
        }
    }
    if ($teachers = get_records_select('user_teachers', $select)) {
        foreach ($teachers as $teacher) {
            remove_teacher($teacher->userid, $teacher->course);
        }
    }
}


/**
* Returns the relevant icons for a course
*
* Returns the relevant icons for a course
*
* @param    course  the current course, as an object
*/
function get_access_icons($course) {
    global $CFG;

    $str = '';

    if (!empty($course->guest)) {
        $strallowguests = get_string("allowguests");
        $str .= '<a title="'.$strallowguests.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
        $str .= '<img vspace="4" alt="'.$strallowguests.'" height="16" width="16" border="0" '.
                'src="'.$CFG->pixpath.'/i/guest.gif" /></a>&nbsp;&nbsp;';
    }
    if (!empty($course->password)) {
        $strrequireskey = get_string("requireskey");
        $str .= '<a title="'.$strrequireskey.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
        $str .= '<img vspace="4" alt="'.$strrequireskey.'" height="16" width="16" border="0" src="'.$CFG->pixpath.'/i/key.gif" /></a>';
    }

    return $str;
}


} /// end of class

?>
