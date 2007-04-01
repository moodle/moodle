<?php // $Id$

/// Displays external information about a course

    require_once("../config.php");
    require_once("lib.php");

    $id   = optional_param('id', false, PARAM_INT); // Course id
    $name = optional_param('name', false, PARAM_RAW); // Course short name

    if (!$id and !$name) {
        error("Must specify course id or short name");
    }

    if ($name) {
        if (! $course = get_record("course", "shortname", $name) ) {
            error("That's an invalid short course name");
        }
    } else {
        if (! $course = get_record("course", "id", $id) ) {
            error("That's an invalid course id");
        }
    }

    $site = get_site();

    if ($CFG->forcelogin) {
        require_login();
    }

    print_header(get_string("summaryof", "", $course->fullname));

    echo "<h3 align=\"center\">$course->fullname<br />($course->shortname)</h3>";

    echo "<center>";
    if ($course->guest) {
        $strallowguests = get_string("allowguests");
        echo "<p><font size=\"1\"><img align=\"middle\" alt=\"\" height=\"16\" width=\"16\" border=\"0\" src=\"$CFG->pixpath/i/guest.gif\" /></a>&nbsp;$strallowguests</font></p>";
    }
    if ($course->password) {
        $strrequireskey = get_string("requireskey");
        echo "<p><font size=\"1\"><img align=\"middle\" alt=\"\" height=\"16\" width=\"16\" border=\"0\" src=\"$CFG->pixpath/i/key.gif\" /></a>&nbsp;$strrequireskey</font></p>";
    }


    /// first find all roles that are supposed to be displayed
    if (!empty($CFG->coursemanager)) {
        $coursemanagerroles = split(',', $CFG->coursemanager);
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        foreach ($coursemanagerroles as $roleid) {
            $role = get_record('role','id',$roleid);
            if ($users = get_role_users($roleid, $context, true, '', 'u.lastname ASC', true)) {
                foreach ($users as $teacher) {
                    $fullname = fullname($teacher, has_capability('moodle/site:viewfullnames', $context)); 
                    $namesarray[] = format_string($role->name).': <a href="'.$CFG->wwwroot.'/user/view.php?id='.
                                    $teacher->id.'&amp;course='.SITEID.'">'.$fullname.'</a>';
                }
            }          
        }
        
        if (!empty($namesarray)) {
            echo "<div align=\"center\">";
            echo implode('<br />', $namesarray);
            echo "</div>";
        }
    }

    echo "<br />";

    print_simple_box_start("center", "100%");
    echo filter_text(text_to_html($course->summary),$course->id);

    require_once("$CFG->dirroot/enrol/enrol.class.php");
    $enrol = enrolment_factory::factory($course->enrol);
    echo $enrol->get_access_icons($course);

    print_simple_box_end();

    echo "<br />";

    close_window_button();

?>

