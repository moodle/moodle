<?PHP // $Id$

//  Display the course home page.

    require_once("../config.php");
    require_once("lib.php");

    optional_variable($id);
    optional_variable($name);

    if (!$id and !$name) {
        error("Must specify course id or short name");
    }

    if ($name) {
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

    if (isteacher($course->id)) {
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
    }

    $SESSION->fromdiscussion = "$CFG->wwwroot/course/view.php?id=$course->id";

    if (! $course->category) {      // This course is not a real course.
        redirect("$CFG->wwwroot/");
    }

    if (empty($THEME->custompix)) {
        $pixpath = "../pix";
        $modpixpath = "../mod";
    } else {
        $pixpath = "../theme/$CFG->theme/pix";
        $modpixpath = "../theme/$CFG->theme/pix/mod";
    }

    $courseword = get_string("course");

    $loggedinas = "<p class=\"logininfo\">".user_login_string($course, $USER)."</p>";

    print_header("$courseword: $course->fullname", "$course->fullname", "$course->shortname", "search.search", "", true,
                  update_course_icon($course->id), $loggedinas);

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


    switch ($course->format) {
        case "weeks":
            include("weeks.php");
            break;
        case "social":
            include("social.php");
            break;
        case "topics":
            include("topics.php");
            break;
        default:
            error("Course format not defined yet!");
    }

    print_footer();

?>
