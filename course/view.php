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

    require_login($id);

    add_to_log($course->id, "course", "view", "view.php?id=$course->id", "$course->id");

    if (isset($edit)) {
        if (isteacher($course->id)) {
            if ($edit == "on") {
                $USER->editing = true;
            } else if ($edit == "off") {
                $USER->editing = false;
            }
        }
    }

    if (isset($help)) {
        if ($help == "on") {
            $USER->help = true;
        } else if ($help == "off") {
            $USER->help = false;
        } 
    }

    $SESSION->fromdiscussion = "$CFG->wwwroot/course/view.php?id=$course->id";

    if (! $course->category) {      // This course is not a real course.
        redirect("$CFG->wwwroot/");
    }


    $courseword = get_string("course");

    print_header("$courseword: $course->fullname", "$course->fullname", "$course->shortname", "search.search", "", true,
                  update_course_icon($course->id));

    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);

    if (! $sections = get_all_sections($course->id)) {
        $section->course = $course->id;   // Create a default section.
        $section->section = 0;
        $section->id = insert_record("course_sections", $section);
        if (! $sections = get_all_sections($course->id) ) {
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
