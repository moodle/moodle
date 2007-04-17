<?php // $Id$

//  Display the course home page.

    require_once('../config.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/blocklib.php');
    require_once($CFG->libdir.'/ajax/ajaxlib.php');
    require_once($CFG->dirroot.'/mod/forum/lib.php');

    $id          = optional_param('id', 0, PARAM_INT);
    $name        = optional_param('name', '', PARAM_RAW);
    $edit        = optional_param('edit', -1, PARAM_BOOL);
    $hide        = optional_param('hide', 0, PARAM_INT);
    $show        = optional_param('show', 0, PARAM_INT);
    $idnumber    = optional_param('idnumber', '', PARAM_RAW);
    $section     = optional_param('section', 0, PARAM_INT);
    $move        = optional_param('move', 0, PARAM_INT);
    $marker      = optional_param('marker',-1 , PARAM_INT);
    $switchrole  = optional_param('switchrole',-1, PARAM_INT);



    if (empty($id) && empty($name) && empty($idnumber)) {
        error("Must specify course id, short name or idnumber");
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

    if (!$context = get_context_instance(CONTEXT_COURSE, $course->id)) {
        print_error('nocontext');
    }

    if ($switchrole == 0) {  // Remove any switched roles before checking login
        role_switch($switchrole, $context);
    }

    require_login($course->id);

    if ($switchrole > 0) {
        role_switch($switchrole, $context);
        require_login($course->id);   // Double check that this role is allowed here
    }

    //If course is hosted on an external server, redirect to corresponding
    //url with appropriate authentication attached as parameter 
    if (file_exists($CFG->dirroot .'/course/externservercourse.php')) {
        include $CFG->dirroot .'/course/externservercourse.php';
        if (function_exists('extern_server_course')) {
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
    $pageblocks = blocks_setup($PAGE, BLOCKS_PINNED_BOTH);


    if (!isset($USER->editing)) {
        $USER->editing = 0;
    }
    if ($PAGE->user_allowed_editing()) {
        if (($edit == 1) and confirm_sesskey()) {
            $USER->editing = 1;
        } else if (($edit == 0) and confirm_sesskey()) {
            $USER->editing = 0;
            if(!empty($USER->activitycopy) && $USER->activitycopycourse == $course->id) {
                $USER->activitycopy       = false;
                $USER->activitycopycourse = NULL;
            }
        }

        if ($hide && confirm_sesskey()) {
            set_section_visible($course->id, $hide, '0');
        }

        if ($show && confirm_sesskey()) {
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
        $USER->editing = 0;
    }

    $SESSION->fromdiscussion = $CFG->wwwroot .'/course/view.php?id='. $course->id;


    if ($course->id == SITEID) {
        // This course is not a real course.
        redirect($CFG->wwwroot .'/');
    }


    // AJAX-capable course format?
    $CFG->useajax = false; 
    $ajaxformatfile = $CFG->dirroot.'/course/format/'.$course->format.'/ajax.php';
    $bodytags = '';

    if (file_exists($ajaxformatfile)) {      // Needs to exist otherwise no AJAX by default

        $CFG->ajaxcapable = false;           // May be overridden later by ajaxformatfile
        $CFG->ajaxtestedbrowsers = array();  // May be overridden later by ajaxformatfile

        require_once($ajaxformatfile);

        if (!empty($USER->editing) && $CFG->ajaxcapable && has_capability('moodle/course:manageactivities', $context)) {
                                                             // Course-based switches

            if (ajaxenabled($CFG->ajaxtestedbrowsers)) {     // Browser, user and site-based switches
                
                require_js(array('yui_yahoo',
                                 'yui_dom',
                                 'yui_event',
                                 'yui_dragdrop',
                                 'yui_connection',
                                 'ajaxcourse_blocks',
                                 'ajaxcourse_sections'));
                
                if (debugging('', DEBUG_DEVELOPER)) {
                    require_js(array('yui_logger'));

                    $bodytags = 'onload = "javascript:
                    show_logger = function() {
                        var logreader = new YAHOO.widget.LogReader();
                        logreader.newestOnTop = false;
                        logreader.setTitle(\'Moodle Debug: YUI Log Console\');
                    };
                    show_logger();
                    "';
                }

                // Okay, global variable alert. VERY UGLY. We need to create
                // this object here before the <blockname>_print_block()
                // function is called, since that function needs to set some
                // stuff in the javascriptportal object.
                $COURSE->javascriptportal = new jsportal();
                $CFG->useajax = true;
            }
        }
    }

    $CFG->blocksdrag = $CFG->useajax;   // this will add a new class to the header so we can style differently


    $PAGE->print_header(get_string('course').': %fullname%', NULL, '', $bodytags);
    // Course wrapper start.
    echo '<div class="course-content">';


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


    if (empty($course->modinfo)) {
        // Course cache was never made.
        rebuild_course_cache($course->id);
        if (! $course = get_record('course', 'id', $course->id) ) {
            error("That's an invalid course id");
        }
    }


    // Include the actual course format.
    require($CFG->dirroot .'/course/format/'. $course->format .'/format.php');
    // Content wrapper end.
    echo "</div>\n\n";


    // Use AJAX?
    if ($CFG->useajax && has_capability('moodle/course:manageactivities', $context)) {
        // At the bottom because we want to process sections and activities
        // after the relevant html has been generated. We're forced to do this
        // because of the way in which lib/ajax/ajaxcourse.js is written.
        echo '<script type="text/javascript" ';
        echo "src=\"{$CFG->wwwroot}/lib/ajax/ajaxcourse.js\"></script>\n";

        $COURSE->javascriptportal->print_javascript($course->id);
    }


    print_footer(NULL, $course);

?>
