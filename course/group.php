<?php // $Id$

/// Shows current group, and allows editing of the group 
/// icon and other settings related to that group

/// This script appears within a popup window

    require_once('../config.php');
    require_once('lib.php');

    $id    = required_param('id');          // Course id
    $group = optional_param('group', 0);    // Optionally look at other groups
    $edit  = optional_param('edit', false); // Editing can be turned on

    if (! $course = get_record('course', 'id', $id) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);

    if (!isteacheredit($course->id)) {
        close_window();
    }

    if (! $group = get_record("groups", "id", $group, "courseid", $course->id)) {
        notice('Specified group could not be found!', "#");
        close_window_button();
    }


/// Print the headers of the page

    print_header(get_string('groupinfoedit').' : '.$group->name);


/// If data submitted, then process and store.

    if ($form = data_submitted() and confirm_sesskey()) { 

        if (empty($form->name)) {
            $edit = true;
            $err['name'] = get_string("missingname");

        } else {
            require_once($CFG->dirroot.'/lib/uploadlib.php');

            $um = new upload_manager('imagefile',false,false,null,false,0,true,true);
            if ($um->preprocess_files()) {
                require_once("$CFG->libdir/gdlib.php");
                if (save_profile_image($group->id, $um, 'groups')) {
                    $group->picture = 1;
                }
            }

            // Setting a new object in order to avoid updating other columns for the record,
            // which could lead to SQL injection vulnerabilities.

            // Be VERY sure to sanitize all parameters that go into $dataobj!

            $dataobj = new stdClass;
            $dataobj->id          = $group->id;
            $dataobj->name        = clean_text($form->name);
            $dataobj->description = clean_text($form->description);
            $dataobj->hidepicture = empty($form->hidepicture) ? 0 : 1;
            $dataobj->password    = required_param('password', PARAM_ALPHANUM);

            if (!update_record('groups', $dataobj)) {
                notify("A strange error occurred while trying to save");
            } else {
                notify(get_string('changessaved'));
            }
            close_window(3);
        }
    }


/// Are we editing?  If so, handle it.

    if ($usehtmleditor = can_use_richtext_editor()) {
        $defaultformat = FORMAT_HTML;
    } else {
        $defaultformat = FORMAT_MOODLE;
    }

    $usehtmleditor = false;

    $sesskey = !empty($USER->id) ? $USER->sesskey : '';

    include('group-edit.html');

    if ($usehtmleditor) {
        use_html_editor("description");
    }

    echo "</body></html>";
?>
