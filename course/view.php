<?PHP // $Id$

//  Display the course home page.

    require_once("../config.php");
    require_once("lib.php");
    require_once('../calendar/lib.php');

    optional_variable($id);
    optional_variable($name);

    if (!$id and !$name) {
        error("Must specify course id or short name");
    }

    if (!empty($_GET['name'])) {
        if (! $course = get_record("course", "shortname", $name) ) {
            error("That's an invalid short course name");
        }
    } else {
        if (! $course = get_record("course", "id", $id) ) {
            error("That's an invalid course id");
        }
    }

    require_login($course->id);

    add_to_log($course->id, "course", "view", "view.php?id=$course->id", "$course->id");

    if (isteacheredit($course->id)) {
        if (isset($edit)) {
            if ($edit == "on") {
                $USER->editing = true;
            } else if ($edit == "off") {
                $USER->editing = false;
            }
        }

        if (isset($hide)) {
            set_section_visible($course->id, $hide, "0");
        }

        if (isset($show)) {
            set_section_visible($course->id, $show, "1");
        }

        if (!empty($section)) {
            if (!empty($move)) {
                if (!move_section($course, $section, $move)) {
                    notify("An error occurred while moving a section");
                }
            }
        }
    } else {
        $USER->editing = false;
    }

    $SESSION->fromdiscussion = "$CFG->wwwroot/course/view.php?id=$course->id";

    if (! $course->category) {      // This course is not a real course.
        redirect("$CFG->wwwroot/");
    }

    $strcourse = get_string("course");

    $loggedinas = "<p class=\"logininfo\">".user_login_string($course, $USER)."</p>";

    print_header("$strcourse: $course->fullname", "$course->fullname", "$course->shortname", 
                 "", "", true, update_course_icon($course->id), $loggedinas);

    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);

    if (! $sections = get_all_sections($course->id)) {   // No sections found
        // Double-check to be extra sure
        if (! $section = get_record("course_sections", "course", $course->id, "section", 0)) {
            $section->course = $course->id;   // Create a default section.
            $section->section = 0;
            $section->visible = 1;
            $section->id = insert_record("course_sections", $section);
        }
        if (! $sections = get_all_sections($course->id) ) {      // Try again
            error("Error finding or creating section structures for this course");
        }
    }

    if (empty($course->modinfo)) {       // Course cache was never made
        rebuild_course_cache($course->id);
        if (! $course = get_record("course", "id", $course->id) ) {
            error("That's an invalid course id");
        }
    }

    if (!file_exists("$CFG->dirroot/course/format/$course->format/format.php")) {   // Default format is weeks
        $course->format = "weeks";
    }

    require("$CFG->dirroot/course/format/$course->format/format.php");  // Include the actual course format

    print_footer();

?>
