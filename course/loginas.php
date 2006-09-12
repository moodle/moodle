<?php // $Id$
      // Allows a teacher/admin to login as another user (in stealth mode)

    require_once("../config.php");
    require_once("lib.php");

/// Reset user back to their real self if needed
    $return   = optional_param('return', 0, PARAM_BOOL);   // return to the page we came from

    if (!empty($USER->realuser)) {
        $USER = get_complete_user_data('id', $USER->realuser);

        if (isset($SESSION->oldcurrentgroup)) {      // Restore previous "current group" cache.
            $SESSION->currentgroup = $SESSION->oldcurrentgroup;
            unset($SESSION->oldcurrentgroup);
        }
        if (isset($SESSION->oldtimeaccess)) {        // Restore previous timeaccess settings
            $USER->timeaccess = $SESSION->oldtimeaccess;
            unset($SESSION->oldtimeaccess);
        }

        if ($return and isset($_SERVER["HTTP_REFERER"])) { // That's all we wanted to do, so let's go back
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            redirect($CFG->wwwroot);
        }
    }

///-------------------------------------
/// try to login as student if allowed
    $id       = required_param('id', PARAM_INT);           // course id
    $user     = required_param('user', PARAM_INT);         // login as this user
    $password = optional_param('password', '', PARAM_RAW); // site wide password

    if (!$site = get_site()) {
        error("Site isn't defined!");
    }

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

    if ($course->category) {
        require_login($course->id);
    }

    // $user must be defined to go on

    if (!isteacher($course->id)) {
        error("Only teachers can use this page!");
    }

    // validate loginaspassword if defined in config.php

    if (empty($SESSION->loginasvalidated) && !empty($CFG->loginaspassword)) {
        if ($password == $CFG->loginaspassword && confirm_sesskey()) {
            $SESSION->loginasvalidated = true;
        } else {
            $strloginaspasswordexplain = get_string('loginaspasswordexplain');
            $strloginas = get_string('loginas');
            $strpassword = get_string('password');

            print_header("$site->fullname: $strloginas", "$site->fullname: $strloginas",
                         ' ', 'passwordform.password');
            print_simple_box_start('center', '50%', '', 5, 'noticebox');
            ?>
            <p align="center"><?php echo $strloginaspasswordexplain?></p>
            <form action="loginas.php" name="passwordform" method="post">
            <table border="0" cellpadding="3" cellspacing="3" align="center">
                <tr><td><?php echo $strpassword?>:</td>
                    <td><input type="password" name="password" size="15" value="" alt="<?php p($strpassword)?>" /></td>
                    <td><input type="submit" value="<?php p($strloginas)?>" /></td>
                </tr>
            </table>
            <input type="hidden" name="id" value="<?php p($id)?>"/>
            <input type="hidden" name="user" value="<?php p($user)?>"/>
            <input type="hidden" name="sesskey" value="<?php p($USER->sesskey)?>"/>
            </form>
            <?php
            print_simple_box_end();
            print_footer();
            die;
        }
    }

    if ($course->category and !has_capability('moodle/course:view', get_context_instance(CONTEXT_COURSE, $course->id), $user) and !isadmin()) {
        error("This student is not in this course!");
    }

    if (has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM, SITEID, $user))) {
        error("You can not login as this person!");
    }

    // Remember current timeaccess settings for later

    if (isset($USER->timeaccess)) {
        $SESSION->oldtimeaccess = $USER->timeaccess;
    }

    // Login as this student and return to course home page.

    $teacher_name = fullname($USER, true);
    $teacher_id   = "$USER->id";

    $USER = get_complete_user_data('id', $user);    // Create the new USER object with all details
    $USER->realuser = $teacher_id;

    if (isset($SESSION->currentgroup)) {    // Remember current cache setting for later
        $SESSION->oldcurrentgroup = $SESSION->currentgroup;
        unset($SESSION->currentgroup);
    }

    $student_name = fullname($USER, true);

    add_to_log($course->id, "course", "loginas", "../user/view.php?id=$course->id&amp;user=$user", "$teacher_name -> $student_name");


    $strloginas    = get_string("loginas");
    $strloggedinas = get_string("loggedinas", "", $student_name);

    print_header_simple("$strloginas $student_name", '', "$strloginas $student_name", '', '', 
                       true, '&nbsp;', navmenu($course));
    notice($strloggedinas, "$CFG->wwwroot/course/view.php?id=$course->id");


?>
