<?php // $Id$

    require_once('../config.php');

    require_login();

    require_capability('moodle/user:create', get_context_instance(CONTEXT_SYSTEM, SITEID));

    if (!$site = get_site()) {
        redirect("index.php");
    }
    $context = get_context_instance(CONTEXT_SYSTEM, SITEID);
    $stradministration = get_string("administration");
    $strusers          = get_string("users");

    print_header("$site->shortname: $stradministration: $strusers", "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> $strusers");

    print_heading($strusers);

    $table->align = array ("right", "left");

    $table->data[] = array("<b><a href=\"auth.php?sesskey=$USER->sesskey\">".get_string("authentication")."</a></b>",
                           get_string("adminhelpauthentication"));

    if (has_capability('moodle/user:update', $context)) {
        $table->data[] = array("<b><a href=\"user.php\">".get_string("edituser")."</a></b>",
                           get_string("adminhelpedituser"));
    }
    
    if (has_capability('moodle/user:create', $context)) {
        $table->data[] = array("<b><a href=\"$CFG->wwwroot/$CFG->admin/user.php?newuser=true&amp;sesskey=$USER->sesskey\">".get_string("addnewuser")."</a></b>",
                               get_string("adminhelpaddnewuser"));
    }
        
    if (has_capability('moodle/user:create', $context)) {        
        $table->data[] = array("<b><a href=\"$CFG->wwwroot/$CFG->admin/uploaduser.php?sesskey=$USER->sesskey\">".get_string("uploadusers")."</a></b>",
                               get_string("adminhelpuploadusers"));
    }
    
    $table->data[] = array('', '<hr />');
    $table->data[] = array("<b><a href=\"enrol.php?sesskey=$USER->sesskey\">".get_string("enrolmentplugins")."</a></b>",
                           get_string("adminhelpenrolments"));
    $table->data[]= array('<b><a href="roles/assign.php?contextid='.$context->id.'">'.
            get_string('assignsiteroles').'</a></b>', get_string('adminhelpassignsiteroles'));

    print_table($table);

    print_footer($site);

?>


