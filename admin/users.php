<?PHP // $Id$

    require_once("../config.php");

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
    if (is_internal_auth()) {
        $table->data[] = array("<b><a href=\"$CFG->wwwroot/$CFG->admin/user.php?newuser=true\">".get_string("addnewuser")."</a></b>",
                               get_string("adminhelpaddnewuser"));
        $table->data[] = array("<b><a href=\"$CFG->wwwroot/$CFG->admin/uploaduser.php\">".get_string("uploadusers")."</a></b>",
                               get_string("adminhelpuploadusers"));
    }
    $table->data[] = array('', '<hr />');
    $table->data[] = array("<b><a href=\"enrol.php?sesskey=$USER->sesskey\">".get_string("enrolments")."</a></b>",
                           get_string("adminhelpenrolments"));
    $table->data[] = array("<b><a href=\"../course/index.php?edit=off\">".get_string("assignstudents")."</a></b>",
                           get_string("adminhelpassignstudents"));
    $table->data[] = array("<b><a href=\"../course/index.php?edit=on\">".get_string("assignteachers")."</a></b>",
                           get_string("adminhelpassignteachers")." <img src=\"../pix/t/user.gif\" height=\"11\" width=\"11\" alt=\"\" />");
    $table->data[] = array("<b><a href=\"creators.php?sesskey=$USER->sesskey\">".get_string("assigncreators")."</a></b>",
                           get_string("adminhelpassigncreators"));
    $table->data[] = array("<b><a href=\"admin.php?sesskey=$USER->sesskey\">".get_string("assignadmins")."</a></b>",
                           get_string("adminhelpassignadmins"));

    print_table($table);
    
    print_footer($site);

?>


