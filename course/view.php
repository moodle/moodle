<?php // $Id$

//  Display the course home page.

    require_once('../config.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/blocklib.php');

    optional_variable($id);
    optional_variable($name);

    optional_param('blockaction');
    optional_param('instanceid', 0, PARAM_INT);
    optional_param('blockid',    0, PARAM_INT);

    if (!$id and !$name) {
        error("Must specify course id or short name");
    }

    if (!empty($_GET['name'])) {
        if (! ($course = get_record("course", "shortname", $name)) ) {
            error("That's an invalid short course name");
        }
    } else {
        if (! ($course = get_record("course", "id", $id)) ) {
            error("That's an invalid course id");
        }
    }

    require_login($course->id);

    require_once($CFG->dirroot.'/calendar/lib.php');    /// This is after login because it needs $USER

    add_to_log($course->id, "course", "view", "view.php?id=$course->id", "$course->id");

    $course->format = clean_param($course->format, PARAM_ALPHA);
    if (!file_exists($CFG->dirroot.'/course/format/'.$course->format.'/format.php')) {
        $course->format = 'weeks';  // Default format is weeks
    }

    $PAGE = page_create_object(MOODLE_PAGE_COURSE, $course->id);
    $pageblocks = blocks_get_by_page($PAGE);

    if (!isset($USER->editing)) {
        $USER->editing = false;
    }

    $editing = false;

    if (isteacheredit($course->id)) {
       if (isset($edit)) {
            if ($edit == "on") {
                $USER->editing = true;
            } else if ($edit == "off") {
                $USER->editing = false;
            }
        }

        $editing = $USER->editing;

        if (isset($hide) && confirm_sesskey()) {
            set_section_visible($course->id, $hide, '0');
        }

        if (isset($show) && confirm_sesskey()) {
            set_section_visible($course->id, $show, '1');
        }

        if (!empty($blockaction) && confirm_sesskey()) {
            if (!empty($blockid)) {
                blocks_execute_action($PAGE, $pageblocks, strtolower($blockaction), intval($blockid));

            }
            else if (!empty($instanceid)) {
                $instance = blocks_find_instance($instanceid, $pageblocks);
                blocks_execute_action($PAGE, $pageblocks, strtolower($blockaction), $instance);
            }
            // This re-query could be eliminated by judicious programming in blocks_execute_action(),
            // but I 'm not sure if it's worth the complexity increase...
            $pageblocks = blocks_get_by_page($PAGE);
        }

        $missingblocks = blocks_get_missing($PAGE, $pageblocks);

        if (!empty($section)) {
            if (!empty($move) and confirm_sesskey()) {
                if (!move_section($course, $section, $move)) {
                    notify("An error occurred while moving a section");
                }
            }
        }
    } else {
        $USER->editing = false;
    }

    $SESSION->fromdiscussion = "$CFG->wwwroot/course/view.php?id=$course->id";

    if ($course->id == SITEID) {      // This course is not a real course.
        redirect("$CFG->wwwroot/");
    }

    $PAGE->print_header(get_string('course').': %fullname%');

    echo '<div id="course-view" class="course">';  // course wrapper start

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

    require("$CFG->dirroot/course/format/$course->format/format.php");  // Include the actual course format

    echo '</div>';  // content wrapper end
    print_footer(NULL, $course);

?>
