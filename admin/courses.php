<?php // $Id$

    require_once('../config.php');

    require_login();

    if (!$site = get_site()) {
        redirect("index.php");
    }

    $stradministration = get_string("administration");
    $strcourses        = get_string("courses");
    $context = get_context_instance(CONTEXT_SYSTEM, SITEID);

    print_header("$site->shortname: $stradministration: $strcourses", "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> $strcourses");

    print_heading($strcourses);

    $table->align = array ("right", "left");

    $table->data[] = array('<b><a href="../course/index.php?edit=on&amp;sesskey='.$USER->sesskey.'">'.get_string("managecourses")."</a></b>",
                           get_string("adminhelpcourses"));
    $table->data[] = array("<b><a href=\"enrol.php?sesskey=$USER->sesskey\">".get_string("enrolmentplugins")."</a></b>",
                           get_string("adminhelpenrolments"));
    $table->data[] = array('<b><a href="roles/assign.php?contextid='.$context->id.'">'.
            get_string('assignsiteroles').'</a></b>', get_string('adminhelpassignsiteroles'));

    print_table($table);

    print_footer($site);

?>
