<?PHP //$Id$
    //This script is used to configure and execute the restore proccess.

    //Define some globals for all the script

    //MUST CHANGE WITH FINAL BACKUP LOCATION !! WITHOUT TRAILING SLASH !!
    //ALL RELATIVE FROM THE LOCATION OF THE restore.php SCRIPT !!!

    $moodle_home = "../../..";
    $mods_home = "mod";

    //END MUST CHANGE

    //Units used
    require_once ("$moodle_home/config.php");
    require_once ("$moodle_home/version.php");
    require_once ("$moodle_home/lib/xmlize.php");
    require_once ("$moodle_home/course/lib.php");
    require_once ("backup_version.php");
    require_once ("db/backup_$CFG->dbtype.php");
    require_once ("lib.php");
    require_once ("restorelib.php");

    //Optional
    optional_variable($file);

    //Check login       
    require_login();

    //Check admin
    if (!isadmin()) {
        error("You need to be an admin user to use this page.", "$CFG->wwwroot/login/index.php");
    }

    //Check site
    if (!$site = get_site()) {
        error("Site not found!");
    }
    
    //Check backup_version
    upgrade_backup_db($backup_version,$backup_release,"restore.php");

    //Get strings
    $strcourserestore = get_string("courserestore");
    $stradministration = get_string("administration");

    //If no file has been selected from the FileManager, inform and end
    if (!$file) {
        print_header("$site->shortname: $strcourserestore", $site->fullname,
                     "<A HREF=\"$moodle_home/$CFG->admin/index.php\">$stradministration</A> -> $strcourserestore");
        print_heading(get_string("nofilesselected"));
        print_continue("$moodle_home/$CFG->admin/index.php");
        print_footer();
        exit;
    }

    //If cancel has been selected, inform and end
    if ($cancel) {
        print_header("$site->shortname: $strcourserestore", $site->fullname,
                     "<A HREF=\"$moodle_home/$CFG->admin/index.php\">$stradministration</A> -> $strcourserestore");
        print_heading(get_string("restorecancelled"));
        print_continue("$moodle_home/$CFG->admin/index.php");
        print_footer();
        exit;
    }

    //We are here, so me have a file.
    //Print header
    print_header("$site->shortname: $strcourserestore", $site->fullname,
                 "<A HREF=\"$moodle_home/$CFG->admin/index.php\">$stradministration</A> ->
                  $strcourserestore -> ".basename($file));
    //Print form
    print_heading("$strcourserestore: ".basename($file));
    print_simple_box_start("center", "", "$THEME->cellheading");

    //Call the form, depending the step we are
    if (!$launch) {
        include_once("restore_precheck.html");
    } else if ($launch == "form") {
        include_once("restore_form.html");
    } else if ($launch == "check") {
        include_once("restore_check.html");
    } else if ($launch == "execute") {
        include_once("restore_execute.html");
    }
    print_simple_box_end();

    //Print footer  
    print_footer();

?>

