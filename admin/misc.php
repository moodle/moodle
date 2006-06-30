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
    $strmisc           = get_string("miscellaneous");

    print_header("$site->shortname: $stradministration: $strmisc", "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> $strmisc");

    print_heading($strmisc);

    $table->align = array ("right", "left");

    $table->data[] = array('<b><a href="../files/index.php?id='.$site->id.'">'.get_string('sitefiles').'</a></b>',
                           get_string("adminhelpsitefiles"));
    $table->data[] = array('<b><a href="stickyblocks.php">'.get_string('stickyblocks','admin')."</a></b>",
                           get_string('adminhelpstickyblocks'));
    $table->data[] = array('<b><a href="report.php">'.get_string('reports')."</a></b>",
                           get_string('adminhelpreports'));
    $table->data[] = array('<b><a href="health.php">'.get_string('healthcenter')."</a></b>",
                           get_string('adminhelphealthcenter'));
    $table->data[] = array('<b><a href="environment.php">'.get_string('environment', 'admin')."</a></b>",
                           get_string('adminhelpenvironment'));

    print_table($table);

    print_footer($site);

?>


