<?PHP // $Id$

    require_once("../config.php");

    require_login();

    if (!isadmin) {
        error("Only admins can access this page");
    }

    if (!$site = get_site()) {
        redirect("index.php");
    }

    $stradministration = get_string("administration");
    $strusers          = get_string("users");

    print_header("$site->shortname: $stradministration", "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> $strusers");

    print_simple_box_start("center", "80%", "#FFFFFF", 20);
    print_heading($strusers);

    $table->align = array ("right", "left");

    if ($CFG->auth == "email" || $CFG->auth == "none" || $CFG->auth == "manual"){
        $table->data[] = array("<a href=\"$CFG->wwwroot/$CFG->admin/user.php?newuser=true\">".get_string("addnewuser")."</a>",
                               "<font size=-1>".get_string("adminhelpaddnewuser"));
    }
    $table->data[] = array("<a href=\"user.php\">".get_string("edituser")."</a>",
                           "<font size=-1>".get_string("adminhelpedituser"));
    $table->data[] = array("<a href=\"admin.php\">".get_string("assignadmins")."</a>",
                           "<font size=-1>".get_string("adminhelpassignadmins"));
    $table->data[] = array("<a href=\"creators.php\">".get_string("assigncreators")."</a>",
                           "<font size=-1>".get_string("adminhelpassigncreators"));
    $table->data[] = array("<a href=\"auth.php\">".get_string("authentication")."</a>",
                           "<font size=-1>".get_string("adminhelpauthentication"));

    print_table($table);
    
    print_simple_box_end();

    print_footer($site);

?>


