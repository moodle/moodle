<?php // $Id$

    require_once('../config.php');

    require_login();

    if (!isadmin()) {
        error("Only admins can access this page");
    }

    if (!$site = get_site()) {
        redirect("index.php");
    }

    $stradministration = get_string("administration");
    $strusers          = get_string("users");

    print_header("$site->shortname: $stradministration: $strusers", "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> $strusers");

    print_heading($strusers);

    $table->align = array ("right", "left");

    $table->data[] = array("<b><a href=\"auth.php?sesskey=$USER->sesskey\">".get_string("authentication")."</a></b>",
                           get_string("adminhelpauthentication"));
    $table->data[] = array("<b><a href=\"user.php\">".get_string("edituser")."</a></b>",
                           get_string("adminhelpedituser"));
    $table->data[] = array("<b><a href=\"$CFG->wwwroot/$CFG->admin/user.php?newuser=true&amp;sesskey=$USER->sesskey\">".get_string("addnewuser")."</a></b>",
                               get_string("adminhelpaddnewuser"));
    $table->data[] = array("<b><a href=\"$CFG->wwwroot/$CFG->admin/uploaduser.php?sesskey=$USER->sesskey\">".get_string("uploadusers")."</a></b>",
                               get_string("adminhelpuploadusers"));

    print_table($table);

    print_footer($site);

?>


