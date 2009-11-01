<?php
      // This script updates all users picturesi to remove black border.


    include('../config.php');
    include('../lib/gdlib.php');

    $PAGE->set_url(new moodle_url($CFG->wwwroot.'/userpix/upgrade.php'));

    require_login();

    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

    if (!$users = $DB->get_records("user", array("picture"=>"1"), "lastaccess DESC", "id,firstname,lastname")) {
        print_error('nousers');
    }

    $title = get_string("users");

    $PAGE->navbar->add($title);
    $PAGE->set_title($title);
    $PAGE->set_heading($title);
    echo $OUTPUT->header();

    foreach ($users as $user) {
        upgrade_profile_image($user->id);
        $fullname = fullname($user);
        echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=1\"".
             "title=\"$fullname\">";
        require_once($CFG->libdir.'/filelib.php');
        $userpictureurl = get_file_url($user->id.'/f1.jpg', null, 'user');
        echo '<img src="'. $userpictureurl .'"'.
            ' style="border:0px; width:100px; height:100px" alt="'.$fullname.'" />';
        echo "</a> \n";
    }

    echo $OUTPUT->footer();
