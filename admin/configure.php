<?PHP // $Id$

    require_once("../config.php");

    require_login();

    if (!isadmin()) {
        error("Only admins can access this page");
    }

    if (!$site = get_site()) {
        redirect("index.php");
    }

    $stradministration   = get_string("administration");
    $strconfiguration  = get_string("configuration");

    print_header("$site->shortname: $stradministration: $strconfiguration", "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> $strconfiguration");

    print_heading($strconfiguration);

    $table->align = array ("right", "left");

    $table->data[] = array("<b><a href=\"config.php\">".get_string("configvariables")."</a></b>",
                           get_string("adminhelpconfigvariables"));
    $table->data[] = array("<b><a href=\"site.php\">".get_string("sitesettings")."</a></b>",
                           get_string("adminhelpsitesettings"));
    $table->data[] = array("<b><a href=\"../theme/index.php\">".get_string("themes")."</a></b>",
                           get_string("adminhelpthemes"));
    $table->data[] = array("<b><a href=\"lang.php\">".get_string("language")."</a></b>",
                           get_string("adminhelplanguage"));
    $table->data[] = array("<b><a href=\"modules.php\">".get_string("managemodules")."</a></b>",
                           get_string("adminhelpmanagemodules"));
    $table->data[] = array("<b><a href=\"blocks.php\">".get_string("manageblocks")."</a></b>",
                           get_string("adminhelpmanageblocks"));
    $table->data[] = array("<b><a href=\"filters.php\">".get_string("managefilters")."</a></b>",
                           get_string("adminhelpmanagefilters"));
    if (!isset($CFG->disablescheduledbackups)) {
        $table->data[] = array("<b><a href=\"backup.php\">".get_string("backup")."</a></b>",
                               get_string("adminhelpbackup"));
    }

    print_table($table);
    
    print_footer($site);

?>


