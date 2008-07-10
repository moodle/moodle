<?php // $Id$
      // This script updates all users picturesi to remove black border.


    include('../config.php');
    include('../lib/gdlib.php');

    require_login();

    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

    if (!$users = get_records("user", "picture", "1", "lastaccess DESC", "id,firstname,lastname")) {
        error("no users!");
    }

    $title = get_string("users");

    print_header($title, $title, build_navigation(array(array('name' => $title, 'link' => null, 'type' => 'misc'))));

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

    print_footer();

?>
