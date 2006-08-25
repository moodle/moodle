<?php // $Id$

    require_once('../config.php');

    require_login();

    if (!$site = get_site()) {
        redirect("index.php");
    }
    
    $context = get_context_instance(CONTEXT_SYSTEM, SITEID);

    $stradministration = get_string("administration");
    $strmisc           = get_string("miscellaneous");

    print_header("$site->shortname: $stradministration: $strmisc", "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> $strmisc");

    print_heading($strmisc);

    $table->align = array ("right", "left");

    $table->data[] = array('<b><a href="../files/index.php?id='.$site->id.'">'.get_string('sitefiles').'</a></b>',
                           get_string("adminhelpsitefiles"));
    $table->data[] = array('<b><a href="stickyblocks.php">'.get_string('stickyblocks','admin')."</a></b>",
                           get_string('adminhelpstickyblocks'));
    
    if (has_capability('moodle/site:viewreports', $context)) {
        $table->data[] = array('<b><a href="report.php">'.get_string('reports')."</a></b>",
                            get_string('adminhelpreports'));
    }
// to be enabled later
/*    $table->data[] = array('<b><a href="health.php">'.get_string('healthcenter')."</a></b>",
                           get_string('adminhelphealthcenter'));*/
    
    if (has_capability('moodle/site:config', $context)) {
        $table->data[] = array('<b><a href="environment.php">'.get_string('environment', 'admin')."</a></b>",
                            get_string('adminhelpenvironment'));
    }
    
    if (file_exists("$CFG->dirroot/$CFG->admin/$CFG->dbtype") && has_capability('moodle/site:accessdb', $context)) {
        $table->data[] = array('<b><a href="'.$CFG->dbtype.'/frame.php">'.get_string('managedatabase')."</a></b>",
                           get_string('adminhelpmanagedatabase'));
    }
/// Hack to show the XMLDB editor
    if (file_exists("$CFG->dirroot/$CFG->admin/xmldb")) {
        $table->data[] = array('<b><a href="xmldb/index.php">'.get_string('xmldbeditor')."</a></b>",
                           get_string('adminhelpxmldbeditor'));
    }

    print_table($table);

    print_footer($site);

?>


