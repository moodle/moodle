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
    $strcourses        = get_string("courses");

    print_header("$site->shortname: $stradministration: $strcourses", "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> $strcourses");

    print_heading($strcourses);

    $table->align = array ("right", "left");

    $table->data[] = array('<b><a href="../course/index.php?edit=on&amp;sesskey='.$USER->sesskey.'">'.get_string("managecourses")."</a></b>",
                           get_string("adminhelpcourses"));
    $table->data[] = array("<b><a href=\"enrol.php?sesskey=$USER->sesskey\">".get_string("enrolmentplugins")."</a></b>",
                           get_string("adminhelpenrolments"));
    $table->data[] = array("<b><a href=\"../course/index.php?edit=off&amp;sesskey=$USER->sesskey\">".get_string("assignstudents")."</a></b>",
                           get_string("adminhelpassignstudents"));
    $table->data[] = array("<b><a href=\"../course/index.php?edit=on&amp;sesskey=$USER->sesskey\">".get_string("assignteachers")."</a></b>",
                           get_string("adminhelpassignteachers")." <img src=\"../pix/t/user.gif\" height=\"11\" width=\"11\" alt=\"\" />");
    $table->data[] = array("<b><a href=\"creators.php?sesskey=$USER->sesskey\">".get_string("assigncreators")."</a></b>",
                           get_string("adminhelpassigncreators"));
    $table->data[] = array("<b><a href=\"admin.php?sesskey=$USER->sesskey\">".get_string("assignadmins")."</a></b>",
                           get_string("adminhelpassignadmins"));

    print_table($table);

    print_footer($site);

?>
