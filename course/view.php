<?php // $Id$

//  Display the course home page.

    require_once('../config.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/blocklib.php');

    $id          = optional_param('id', 0, PARAM_INT);
    $name        = optional_param('name');
    $edit        = optional_param('edit','',PARAM_ALPHA);
    $idnumber    = optional_param('idnumber');
    $topic       = optional_param('topic',0,PARAM_INT);
    $studentview = optional_param('studentview','',PARAM_ALPHA);

    if (empty($id) && empty($name)) {
        error("Must specify course id or short name");
    }

    if (!empty($name)) {
        if (! ($course = get_record('course', 'shortname', $name)) ) {
            error('Invalid short course name');
        }
    } else if (!empty($idnumber)) {
        if (! ($course = get_record('course', 'idnumber', $idnumber)) ) {
            error('Invalid course idnumber');
        }
    } else {
        if (! ($course = get_record('course', 'id', $id)) ) {
            error('Invalid course id');
        }
    }

    require_login($course->id);

    //If course is hosted on an external server, redirect to corresponding
    //url with appropriate authentication attached as parameter 
    if (file_exists($CFG->dirroot .'/course/externservercourse.php')) {
        include $CFG->dirroot .'/course/externservercourse.php';
        if (function_exists(extern_server_course)) {
            if ($extern_url = extern_server_course($course)) {
                redirect($extern_url);
            }
        }
    }

    require_once($CFG->dirroot.'/calendar/lib.php');    /// This is after login because it needs $USER

    add_to_log($course->id, 'course', 'view', "view.php?id=$course->id", "$course->id");

    $course->format = clean_param($course->format, PARAM_ALPHA);
    if (!file_exists($CFG->dirroot.'/course/format/'.$course->format.'/format.php')) {
        $course->format = 'weeks';  // Default format is weeks
    }

    $PAGE = page_create_object(PAGE_COURSE_VIEW, $course->id);
    $pageblocks = blocks_setup($PAGE,BLOCKS_PINNED_BOTH);

    if (!isset($USER->editing)) {
        $USER->editing = false;
    }

    if (!isset($USER->studentview)) {
        $USER->studentview = false;
    }

    // need to check this here, as studentview=on disables edit allowed (where 'on' is checked)
    if (($studentview == 'off') and confirm_sesskey()) {
        $USER->studentview = false;
    }

    if ($PAGE->user_allowed_editing()) {
        if (($edit == 'on') and confirm_sesskey()) {
            $USER->editing = true;
        } else if (($edit == 'off') and confirm_sesskey()) {
            $USER->editing = false;
            if(!empty($USER->activitycopy) && $USER->activitycopycourse == $course->id) {
                $USER->activitycopy       = false;
                $USER->activitycopycourse = NULL;
            }
        }

        if (($studentview == 'on') and confirm_sesskey()) {
            $USER->studentview = true;
            $USER->editing = false;
        }

        if (isset($hide) && confirm_sesskey()) {
            set_section_visible($course->id, $hide, '0');
        }

        if (isset($show) && confirm_sesskey()) {
            set_section_visible($course->id, $show, '1');
        }

        if (!empty($section)) {
            if (!empty($move) and confirm_sesskey()) {
                if (!move_section($course, $section, $move)) {
                    notify('An error occurred while moving a section');
                }
            }
        }
    } else {
        $USER->editing = false;
    }

    $SESSION->fromdiscussion = $CFG->wwwroot .'/course/view.php?id='. $course->id;

    if ($course->id == SITEID) {      // This course is not a real course.
        redirect($CFG->wwwroot .'/');
    }

    $PAGE->print_header(get_string('course').': %fullname%');

    echo '<div class="course-content">';  // course wrapper start

    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);

    if (! $sections = get_all_sections($course->id)) {   // No sections found
        // Double-check to be extra sure
        if (! $section = get_record('course_sections', 'course', $course->id, 'section', 0)) {
            $section->course = $course->id;   // Create a default section.
            $section->section = 0;
            $section->visible = 1;
            $section->id = insert_record('course_sections', $section);
        }
        if (! $sections = get_all_sections($course->id) ) {      // Try again
            error('Error finding or creating section structures for this course');
        }
    }

    if (empty($course->modinfo)) {       // Course cache was never made
        rebuild_course_cache($course->id);
        if (! $course = get_record('course', 'id', $course->id) ) {
            error("That's an invalid course id");
        }
    }

    require($CFG->dirroot .'/course/format/'. $course->format .'/format.php');  // Include the actual course format

    echo '</div>';  // content wrapper end
    print_footer(NULL, $course);

?>
