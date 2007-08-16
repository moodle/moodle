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
* enrolment_plugin_manual is the default enrolment plugin
*
* This class provides all the functionality for an enrolment plugin
* In fact it includes all the code for the default, "manual" method
* so that other plugins can override these as necessary.
*/

class enrolment_plugin_manual {

var $errormsg;

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

    $strloginto = get_string('loginto', '', $course->shortname);
    $strcourses = get_string('courses');

/// Automatically enrol into courses without password

    $context = get_context_instance(CONTEXT_SYSTEM, SITEID);  

    if ($course->password == '') {   // no password, so enrol

        if (has_capability('moodle/legacy:guest', $context, $USER->id, false)) {
            add_to_log($course->id, 'course', 'guest', 'view.php?id='.$course->id, getremoteaddr());

        } else if (empty($_GET['confirm']) && empty($_GET['cancel'])) {

            print_header($strloginto, $course->fullname, "<a href=\".\">$strcourses</a> -> $strloginto");
            echo '<br />';
            notice_yesno(get_string('enrolmentconfirmation'), "enrol.php?id=$course->id&amp;confirm=1", 
                                                              "enrol.php?id=$course->id&amp;cancel=1");
            print_footer();
            exit;

        } else if (!empty($_GET['confirm'])) {

            if (!enrol_into_course($course, $USER, 'manual')) {
                print_error('couldnotassignrole');
            }

            if (!empty($SESSION->wantsurl)) {
                $destination = $SESSION->wantsurl;
                unset($SESSION->wantsurl);
            } else {
                $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
            }

            redirect($destination);

        } else if (!empty($_GET['cancel'])) {
            unset($SESSION->wantsurl);
            if (!empty($SESSION->enrolcancel)) {
                $destination = $SESSION->enrolcancel;
                unset($SESSION->enrolcancel);
            } else {
                $destination = $CFG->wwwroot;
            }
            redirect($destination);
        }
    }

    // if we get here we are going to display the form asking for the enrolment key
    // and (hopefully) provide information about who to ask for it.
    if (!isset($password)) {
        $password = '';
    }

    print_header($strloginto, $course->fullname, "<a href=\".\">$strcourses</a> -> $strloginto", "form.password");

    print_course($course, "80%");

    include("$CFG->dirroot/enrol/manual/enrol.html");

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

    if (empty($course->password)) {
        // do not allow entry when no course password set
        // automatic login when manual primary, no login when secondary at all!!
        error('illegal enrolment attempted');
    }

    $groupid = $this->check_group_entry($course->id, $form->password);

    if (($form->password == $course->password) or ($groupid !== false) ) {

        if (isguestuser()) { // only real user guest, do not use this for users with guest role
            $USER->enrolkey[$course->id] = true;
            add_to_log($course->id, 'course', 'guest', 'view.php?id='.$course->id, getremoteaddr());

        } else {  /// Update or add new enrolment
            if (enrol_into_course($course, $USER, 'manual')) {
                if ($groupid !== false) {
                    if (!groups_add_member($groupid, $USER->id)) {
                        print_error('couldnotassigngroup');
                    }
                }
            } else {
                print_error('couldnotassignrole');
            }
        }

        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
        }

        redirect($destination);

    } else {
        $this->errormsg = get_string('enrolmentkeyhint', '', substr($course->password,0,1));
    }
}


/**
* Check if the given enrolment key matches a group enrolment key for the given course
*
* @param    courseid  the current course id
* @param    password  the submitted enrolment key
*/
function check_group_entry ($courseid, $password) {
    $ingroup = false;
    if (($groups = groups_get_all_groups($courseid))) {
        foreach ($groups as $group) {
            if ( !empty($group->enrolmentkey) and ($password == $group->enrolmentkey) ) {
                $ingroup = $group->id;
            }
        }
    }
    return $ingroup;
}


/**
* Prints a form for configuring the current enrolment plugin
*
* This function is called from admin/enrol.php, and outputs a 
* full page with a form for defining the current enrolment plugin.
*
* @param    frm  an object containing all the data for this page
*/
function config_form($frm) {
    global $CFG;

    if (!isset( $frm->enrol_manual_keyholderrole )) {
        $frm->enrol_manual_keyholderrole = '';
    } 
    
    include ("$CFG->dirroot/enrol/manual/config.html");
}


/**
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
    global $CFG;

    // Notify users about enrolments that are going to expire soon!

    if (empty($CFG->lastexpirynotify)) {
        $CFG->lastexpirynotify = 0;
    }
    
    if ($CFG->lastexpirynotify < date('Ymd') && 
        ($courses = get_records_select('course', 'enrolperiod > 0 AND expirynotify > 0 AND expirythreshold > 0'))) {

        $admin = get_admin();

        $strexpirynotify = get_string('expirynotify');
        foreach ($courses as $course) {
            $a = new object();
            $a->coursename = $course->shortname .'/'. $course->fullname;
            $a->threshold = $course->expirythreshold / 86400;
            $a->extendurl = $CFG->wwwroot . '/user/index.php?id=' . $course->id;
            $a->current = array();
            $a->past = array();
            $a->current = $a->past = array();
            $expiry = time() + $course->expirythreshold;

            /// Get all the role assignments for this course that have expired.

            if (!$context = get_context_instance(CONTEXT_COURSE, $course->id)) {
                continue;
            }

            if ($oldenrolments = get_records_sql('
                      SELECT u.* 
                        FROM '.$CFG->prefix.'role_assignments ra, 
                             '.$CFG->prefix.'user u
                        WHERE ra.contextid = '.$context->id.'
                          AND ra.timeend > 0 AND ra.timeend <= '.$expiry.'
                          AND ra.userid = u.id ')) {


                if (!$teacher = get_teacher($course->id)) {
                    $teacher = get_admin();
                }

                $a->teacherstr = fullname($teacher, true);

                $strexpirynotifystudentsemail = get_string('expirynotifystudentsemail', '', $a);

                foreach ($oldenrolments as $user) {       /// Email all users about to expire
                    $a->studentstr = fullname($user, true);
                    if ($user->timeend < ($expiry - 86400)) {
                        $a->past[] = fullname($user) . " <$user->email>";
                    } else {
                        $a->current[] = fullname($user) . " <$user->email>";
                        if ($course->notifystudents) {     // Send this guy notice
                            email_to_user($user, $teacher, $SITE->fullname .' '. $strexpirynotify, 
                                          $strexpirynotifystudentsemail);
                        }
                    }
                }

                $a->current = implode("\n", $a->current);
                $a->past = implode("\n", $a->past);

                $strexpirynotifyemail = get_string('expirynotifyemail', '', $a);

                if ($a->current || $a->past) {
                    if ($teachers = get_users_by_capability($context, 'moodle/course:update', 
                                                            'u.*,ra.hidden', 'r.sortorder ASC',
                                                            '', '', '', '', false)) {
                        foreach ($teachers as $teacher) {
                            email_to_user($teacher, $admin, $a->coursename .' '. $strexpirynotify, $strexpirynotifyemail);
                        }
                    }
                }
            }
            set_config('lastexpirynotify', date('Ymd'));
        }
    }
}


/**
* Returns the relevant icons for a course
*
* @param    course  the current course, as an object
*/
function get_access_icons($course) {
    global $CFG;

    global $strallowguests;
    global $strrequireskey;

    if (empty($strallowguests)) {
        $strallowguests = get_string('allowguests');
        $strrequireskey = get_string('requireskey');
    }

    $str = '';

    if (!empty($course->guest)) {
        $str .= '<a title="'.$strallowguests.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
        $str .= '<img class="accessicon" alt="'.$strallowguests.'" src="'.$CFG->pixpath.'/i/guest.gif" /></a>&nbsp;&nbsp;';
    }
    if (!empty($course->password)) {
        $str .= '<a title="'.$strrequireskey.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
        $str .= '<img class="accessicon" alt="'.$strrequireskey.'" src="'.$CFG->pixpath.'/i/key.gif" /></a>';
    }

    return $str;
}

/**
 * Prints the message telling you were to get the enrolment key
 * appropriate for the prevailing circumstances
 * A bit clunky because I didn't want to change the standard strings
 */
function print_enrolmentkeyfrom($course) {
    global $CFG;
    global $USER;

    $context = get_context_instance(CONTEXT_SYSTEM, SITEID);  
    $guest = has_capability('moodle/legacy:guest', $context, $USER->id, false);

    // if a keyholder role is defined we list teachers in that role (if any exist)
    $contactslisted = false;
    if (!empty($CFG->enrol_manual_keyholderrole)) {
        if ($contacts = get_role_users($CFG->enrol_manual_keyholderrole, get_context_instance(CONTEXT_COURSE, $course->id), true )) {
            // guest user has a slightly different message
            if ($guest) {
                print_string('enrolmentkeyfromguest', '', ':<br />' );
            }
            else {
                print_string('enrolmentkeyfrom', '', ':<br />');
            }
            foreach ($contacts as $contact) {
                $contactname = "<a href=\"../user/view.php?id=$contact->id&course=".SITEID."\">".fullname($contact)."</a>.";
                echo "$contactname<br />";
            }
            $contactslisted = true;
        } 
    }

    // if no keyholder role is defined OR nobody is in that role we do this the 'old' way
    // (show the first person with update rights)
    if (!$contactslisted) {
        if ($teachers = get_users_by_capability(get_context_instance(CONTEXT_COURSE, $course->id), 'moodle/course:update', 
            'u.*,ra.hidden', 'r.sortorder ASC', 0, 1, '', '', false, true)) {  
            $teacher = array_shift($teachers);
        }
        if (!empty($teacher)) {
            $teachername = "<a href=\"../user/view.php?id=$teacher->id&course=".SITEID."\">".fullname($teacher)."</a>.";
        } else {
            $teachername = strtolower( get_string('defaultcourseteacher') ); //get_string('yourteacher', '', $course->teacher);
        }

        // guest user has a slightly different message
        if ($guest) {
            print_string('enrolmentkeyfromguest', '', $teachername );
        }
        else {
            print_string('enrolmentkeyfrom', '', $teachername);
        }
    }
}

} /// end of class

?>
