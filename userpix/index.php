<?php
      // This simple script displays all the users with pictures on one page.
      // By default it is not linked anywhere on the site.  If you want to
      // make it available you should link it in yourself from somewhere.
      // Remember also to comment or delete the lines restricting access
      // to administrators only (see below)


    include('../config.php');

    $PAGE->set_url(new moodle_url($CFG->wwwroot.'/userpix/index.php'));

    require_login();

/// Remove the following three lines if you want everyone to access it
    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

    if (!$users = $DB->get_records("user", array("picture"=>"1"), "lastaccess DESC", "id,firstname,lastname")) {
        print_error("nousers");
    }

    $title = get_string("users");

    $PAGE->navbar->add($title);
    $PAGE->set_title($title);
    $PAGE->set_heading($title);
    echo $OUTPUT->header();

    foreach ($users as $user) {
        $fullname = fullname($user);
        echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=1\" ".
             "title=\"$fullname\">";
        require_once($CFG->libdir.'/filelib.php');
        $userpictureurl = get_file_url($user->id.'/f1.jpg', null, 'user');
        echo '<img src="'. $userpictureurl .'"'.
            ' style="border:0px; width:100px; height:100px" alt="'.$fullname.'" />';
        echo "</a> \n";
    }

    echo $OUTPUT->footer();
