<?PHP //$Id$
    //This script is used to configure and execute the restore proccess.

    //Define some globals for all the script

    //Units used
    require_once ("../config.php");
    require_once ("../lib/xmlize.php");
    require_once ("../course/lib.php");
    require_once ("lib.php");
    require_once ("restorelib.php");
    require_once("$CFG->libdir/blocklib.php");

    //Optional
    optional_variable($id);
    optional_variable($file);
    optional_variable($cancel);
    optional_variable($launch);

    //Check login       
    require_login();

    if (!empty($id)) {
        if (!isteacheredit($id)) {
            error("You need to be a teacher or admin user to use this page.", "$CFG->wwwroot/login/index.php");
        }
    } else {
        if (!isadmin()) {
            error("You need to be an admin user to use this page.", "$CFG->wwwroot/login/index.php");
        }
    }

    //Check site
    if (!$site = get_site()) {
        error("Site not found!");
    }

    //Check necessary functions exists. Thanks to gregb@crowncollege.edu
    backup_required_functions();
    
    //Check backup_version
    if ($file) {
        $linkto = "restore.php?id=".$id."&file=".$file;
    } else {
        $linkto = "restore.php";
    }
    upgrade_backup_db($linkto);

    //Get strings
    $strcourserestore = get_string("courserestore");
    $stradministration = get_string("administration");

    //If no file has been selected from the FileManager, inform and end
    if (!$file) {
        print_header("$site->shortname: $strcourserestore", $site->fullname,
                     "<A HREF=\"$CFG->wwwroot/$CFG->admin/index.php\">$stradministration</A> -> $strcourserestore");
        print_heading(get_string("nofilesselected"));
        print_continue("$CFG->wwwroot/$CFG->admin/index.php");
        print_footer();
        exit;
    }

    //If cancel has been selected, inform and end
    if ($cancel) {
        print_header("$site->shortname: $strcourserestore", $site->fullname,
                     "<A HREF=\"$CFG->wwwroot/$CFG->admin/index.php\">$stradministration</A> -> $strcourserestore");
        print_heading(get_string("restorecancelled"));
        print_continue("$CFG->wwwroot/$CFG->admin/index.php");
        print_footer();
        exit;
    }

    //We are here, so me have a file.

    //Get and check course
    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect (can't find it)");
    }

    check_for_restricted_user($USER->username, "$CFG->wwwroot/course/view.php?id=$course->id");

    //Print header
    if (isadmin()) {
        print_header("$site->shortname: $strcourserestore", $site->fullname,
                     "<a href=\"$CFG->wwwroot/$CFG->admin/index.php\">$stradministration</a> ->
                      $strcourserestore -> ".basename($file));
    } else {
        print_header("$course->shortname: $strcourserestore", $course->fullname,
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->
                     $strcourserestore");
    }
    //Print form
    print_heading("$strcourserestore: ".basename($file));
    print_simple_box_start("center", "", "$THEME->cellheading");
    
    //Adjust some php variables to the execution of this script
    ini_set("max_execution_time","3000");
    ini_set("memory_limit","80M");

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
