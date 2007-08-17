<?php // $Id$
      // This simple script displays all the users with pictures on one page.
      // By default it is not linked anywhere on the site.  If you want to
      // make it available you should link it in yourself from somewhere.
      // Remember also to comment or delete the lines restricting access
      // to administrators only (see below)


    include('../config.php');

    require_login();

/// Remove the following three lines if you want everyone to access it
    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM, SITEID));

    if (!$users = get_records("user", "picture", "1", "lastaccess DESC", "id,firstname,lastname")) {
        error("no users!");
    }

    $title = get_string("users");

    print_header($title, $title, build_navigation(array(array('name' => $title, 'link' => null, 'type' => 'misc'))));

    foreach ($users as $user) {
        $fullname = fullname($user);
        echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=1\"".
             "title=\"$fullname\">";
        if ($CFG->slasharguments) {        // Use this method if possible for better caching
            echo '<img src="'. $CFG->wwwroot .'/user/pix.php/'.$user->id.'/f1.jpg"'.
                 ' style="border:0px; width:100px; height:100px" alt="'.$fullname.'" />';
        } else {
            echo '<img src="'. $CFG->wwwroot .'/user/pix.php?file=/'. $user->id .'/f1.jpg"'.
                 ' style="border:0px; width:100px; height:100px" alt="'.$fullname.'" />';
        }
        echo "</a> \n";
    }

    print_footer();

?>
