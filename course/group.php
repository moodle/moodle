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

    if ($group) {
        if (isteacheredit($course->id) or $course->groupmode == VISIBLEGROUPS) {
            if (! $group = get_record("groups", "id", $group, "courseid", $course->id)) {
                error('Specified group could not be found!', "groups.php?id=$course->id");
            }
        } else {
            error('Sorry, you don\'t have access to view this group', "view.php?id=$course->id");
        }
    } else if (! $group = get_current_group($course->id, 'full')) {
        error('You are not currently in a group!', "view.php?id=$course->id");
    }

    if (isteacheredit($course->id) or (isteacher($course->id) and ismember($group->id) ) ) {
        $edit = isset($_GET['edit']);
        $editbutton = $edit ? "" : update_group_button($course->id, $group->id);
    } else {
        $edit = false;
        $editbutton = "";
    }


/// Print the headers of the page

    $strgroup = get_string('group');
    $strgroups = get_string('groups');
    $loggedinas = "<p class=\"logininfo\">".user_login_string($course, $USER)."</p>";

    if (isteacheredit($course->id) or $course->groupmode == VISIBLEGROUPS) {
        print_header("$strgroup : $group->name", "$course->fullname", 
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> 
                      -> <a href=\"groups.php?id=$course->id\">$strgroups</a> -> $group->name", 
                      "", "", true, $editbutton, $loggedinas);
    } else {
        print_header("$strgroup : $group->name", "$course->fullname", 
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> 
                      -> $strgroup -> $group->name", "", "", true, "", $loggedinas);
    }



/// If data submitted, then process and store.

    if ($form = data_submitted()) { 

        if (empty($form->name)) {
            $edit = true;
            $err['name'] = get_string("missingname");

        } else {
            if (!empty($_FILES['imagefile'])) {
                require_once("$CFG->libdir/gdlib.php");
                if ($filename = valid_uploaded_file($_FILES['imagefile'])) { 
                    $group->picture = save_profile_image($group->id, $filename, 'groups');
                }
            }
            $group->name        = $form->name;
            $group->description = $form->description;
            $group->hidepicture = $form->hidepicture;
            if (!update_record("groups", $group)) {
                notify("A strange error occurred while trying to save ");
            } else {
                redirect("group.php?id=$course->id&group=$group->id", get_string("changessaved"));
            }
        }
    }


/// Are we editing?  If so, handle it.

    if ($edit) {          // We are editing a group's information
        if ($usehtmleditor = can_use_richtext_editor()) {
            $defaultformat = FORMAT_HTML;
        } else {
            $defaultformat = FORMAT_MOODLE;
        }

        include('group-edit.html');

        if ($usehtmleditor) {
            use_html_editor("description");
        }

        print_footer();
        exit;
    }
    
/// Just display the information 

    print_heading($group->name);
    echo '<div align="center">';
    print_group_picture($group, $course->id, true, false, false);
    echo '</div>';
    if ($group->description) {
        print_simple_box(format_text($group->description), 'center', '50%');
    }

    echo '<br />';

    if ($users = get_group_users($group->id)) {
        foreach ($users as $user) {
            print_user($user, $course);
        }
    } else {
        print_heading(get_string('nousersyet'));
    }


/// Finish off the page

    print_footer($course);

?>
