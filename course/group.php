<?php // $Id$

/// Shows current group, and allows editing of the group 
/// icon and other settings related to that group

	require_once('../config.php');
	require_once('lib.php');

    require_variable($id);        // Course id
    optional_variable($group);    // Optionally look at other groups
    optional_variable($edit);     // Turn editing on and off

    if (! $course = get_record('course', 'id', $id) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);


    if ($group and (isteacheredit($course->id) or $course->groupmode == VISIBLEGROUPS)) {
        if (! $group = get_record("groups", "id", $group)) {
            error('Specified group could not be found!', "groups.php?id=$course->id");
        }
    } else if (! $group = get_current_group($course->id, 'full')) {
        error('You are not currently in a group!', "view.php?id=$course->id");
    }

    if (isteacheredit($course->id) or (isteacher($course->id) and ismember($group->id) ) ) {
        if (isset($edit)) {
            if ($edit == "on") {
                $USER->groupediting = true;
            } else if ($edit == "off") {
                $USER->groupediting = false;
            }
        }
    } else {
        $USER->groupediting = false;
    }


/// Print the headers of the page

    $strgroup = get_string('group');
    $strgroups = get_string('groups');
    $loggedinas = "<p class=\"logininfo\">".user_login_string($course, $USER)."</p>";

    if (isteacheredit($course->id) or $course->groupmode == VISIBLEGROUPS) {
        print_header("$strgroup : $group->name", "$course->fullname", 
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> 
                      -> <a href=\"groups.php?id=$course->id\">$strgroups</a> -> $group->name", 
                      "", "", true, update_group_button($course->id), $loggedinas);
    } else {
        print_header("$strgroup : $group->name", "$course->fullname", 
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> 
                      -> $strgroup -> $group->name", "", "", true, "", $loggedinas);
    }


/// Display the current group information

    if ($USER->groupediting) {          // Make an editing form for group information
        print_heading($group->name);
        echo '<div align="center">';
        print_group_picture($group, $course->id, true, false, false);
        echo '</div>';
        print_simple_box($group->description, 'center', '50%');

    } else {                            // Just display the information 
        print_heading($group->name);
        echo '<div align="center">';
        print_group_picture($group, $course->id, true, false, false);
        echo '</div>';
        print_simple_box($group->description, 'center', '50%');
    }

    echo '<br />';

    if ($users = get_users_in_group($group->id)) {
        foreach ($users as $user) {
            print_user($user, $course);
        }
    } else {
        print_heading(get_string('nousersyet'));
    }


/// Finish off the page

    print_footer($course);

?>
