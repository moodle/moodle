<?php // $Id$
      // This simple script displays all the users with pictures on one page.
      // By default it is not linked anywhere on the site.  If you want to 
      // make it available you should link it in yourself from somewhere.
      // Remember also to comment or delete the lines restricting access
      // to administrators only (see below)


    include('../config.php');

    require_login();

/// Remove the following three lines if you want everyone to access it
    if (!isadmin()) {
        error("Currently only the administrator can access this page!");
    }

    
    if (!$users = get_records("user", "picture", "1", "lastaccess DESC", "id,firstname,lastname")) {
        error("no users!");
    }
    
    $title = get_string("users");
    
    print_header($title, $title, $title);
    
    foreach ($users as $user) {
       echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&course=1\"".
            "title=\"$user->firstname $user->lastname\">";
       echo "<img border=0 src=\"$CFG->wwwroot/user/pix.php/$user->id/f1.jpg\" ".
            "width=100 height=100 alt=\"$user->firstname $user->lastname\" />";
       echo "</a> \n";
    }
    
    print_footer();

?>
