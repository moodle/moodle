<?PHP //$Id$
    //This script is used to configure and execute the backup proccess.

    //Define some globals for all the script

    //MUST CHANGE WITH FINAL BACKUP LOCATION !! WITHOUT TRAILING SLASH !!
    //ALL RELATIVE FROM THE LOCATION OF THE backup.php SCRIPT !!!

    $moodle_home = "../../..";
    $mods_home = "mod";

    //END MUST CHANGE

    //Units used
    require_once ("$moodle_home/config.php");

    //Optional variables    
    optional_variable($id);       // course id

    //Check login       
    require_login();

    //Check admin
    //Check admin
    if (!isadmin()) {
    }

    //Check site
    if (!$site = get_site()) {
        error("Site not found!");
    }

    //Get strings
    $strcoursebackup = get_string("coursebackup");
    $stradministration = get_string("administration");

    //If no course has been selected
    if (!$id) {
        print_header("$site->shortname: $strcoursebackup", $site->fullname,
                     "<A HREF=\"$moodle_home/$CFG->admin/index.php\">$stradministration</A> -> $strcoursebackup");

        if ($courses = get_courses()) {
            print_heading(get_string("choosecourse"));
            print_simple_box_start("CENTER");
            foreach ($courses as $course) {
            echo "<A HREF=\"backup.php?id=$course->id\">$course->fullname ($course->shortname)</A><BR>";
            }
            print_simple_box_end();
        } else {
            print_heading(get_string("nocoursesyet"));
            print_continue("../$CFG->admin/index.php");
        }
        print_footer();
        exit;
    }

    //Get and check course
    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect (can't find it)");
    }
    //Print header
    print_header("$site->shortname: $strcoursebackup", $site->fullname,
                 "<A HREF=\"$moodle_home/$CFG->admin/index.php\">$stradministration</A> ->
                  <A HREF=\"backup.php\">$strcoursebackup</A> -> $course->fullname ($course->shortname)");

    //Print form     
    print_heading("$strcoursebackup: $course->fullname ($course->shortname)");
    print_simple_box_start("center", "", "$THEME->cellheading");
    //Set form initial values
    //Call the form     
    include_once("backup.html");
    print_simple_box_end();

    //Print footer  
    print_footer();
?>

