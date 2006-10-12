<?php // $Id$
      // Edit course settings

    require_once("../config.php");
    require_once("lib.php");
    require_once("$CFG->libdir/blocklib.php");
    require_once("$CFG->dirroot/enrol/enrol.class.php");

    include_once $CFG->libdir.'/formslib.php';

    print_header();
    notice('This file is disabled due to recent changes in formslib.php, edit3.php will be here soon ;-).');
    die;

    require_login();

    $mform =& new moodleform('edit_course', 'post', 'edit2.php');

    $mform->acceptGet('id', 'category');

    $id       = $mform->optional_param('id', 0, PARAM_INT); // course id
    $category = $mform->optional_param('category', 0, PARAM_INT); // possible default category

   
    $disable_meta = false;
    $focus = "";

    if ($id) {
        if (! $course = get_record("course", "id", $id)) {
            error("Course ID was incorrect");
        }

		$context = get_context_instance(CONTEXT_COURSE, $course->id);
		
        if (!has_capability('moodle/course:update', $context)) {
            error("You do not currently have editing privileges!");
        }
        
        if (course_in_meta($course)) {
            $disable_meta = get_string('metaalreadyinmeta');
        } else if ($course->metacourse) {
            if (count_records("course_meta","parent_course",$course->id) > 0) {
                $disable_meta = get_string('metaalreadyhascourses');
            }
        } else {
            $managers = count(get_users_by_capability($context, 'moodle/course:managemetacourse'));
            $participants = count(get_users_by_capability($context, 'moodle/course:view'));
            if ($participants > $managers) {
                $disable_meta = get_string('metaalreadyhasenrolments');
            }
        }
    } else {  // Admin is creating a new course

		$context = get_context_instance(CONTEXT_SYSTEM, SITEID);
		if (!has_capability('moodle/course:create',$context)) {
            error("You do not currently have course creation privileges!");
        }

        $course = NULL;
    }

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }



    include("edit_form.php");
            


/// If data submitted, then process and store.
// data_submitted tries to validate form data and returns false if 
// the user inputted data is invalid and the form should be redisplayed with 
// feedback.

    if ($fromform=$mform->data_submitted()) {

        
        if (!empty($fromform->enrolstartdisabled)){
            $fromform->enrolstartdate = 0;
        }

        if (!empty($fromform->enrolenddisabled)) {
            $fromform->enrolenddate = 0;
        }
        $allowedmods = array();
        if (!empty($fromform->allowedmods)) {
            $allowedmods = $fromform->allowedmods;
            unset($fromform->allowedmods);
        }
        
        $fromform->timemodified = time();

        if ($fromform->defaultrole == -1) {   // Just leave it however it is
            unset($fromform->defaultrole);
        }        
        if (!empty($course)) {
            // Test for and remove blocks which aren't appropriate anymore
            $page = page_create_object(PAGE_COURSE_VIEW, $course->id);
            blocks_remove_inappropriate($page);

            // Update with the new data
            if (update_record('course', $fromform)) {
                add_to_log($course->id, "course", "update", "edit.php?id=$id", "");
                if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
                    $course->restrictmodules = $fromform->restrictmodules;
                    update_restricted_mods($course,$allowedmods);
                }
                fix_course_sortorder();
                // everything ok, no need to display any message in redirect
                redirect("view.php?id=$course->id");
            } else {
                error("Serious Error! Could not update the course record! (id = $fromform->id)");
            }
        } else {
            $fromform->timecreated = time();

            // place at beginning of category
            fix_course_sortorder();
            $fromform->sortorder = get_field_sql("SELECT min(sortorder)-1 FROM {$CFG->prefix}course WHERE category=$fromform->category");                
            if (empty($fromform->sortorder)) {
                $fromform->sortorder = 100;
            }
            // fill in default teacher and student names to keep backwards compatibility
            $fromform->teacher = addslashes(get_string('defaultcourseteacher'));
            $fromform->teachers = addslashes(get_string('defaultcourseteachers'));
            $fromform->student = addslashes(get_string('defaultcoursestudent'));
            $fromform->students = addslashes(get_string('defaultcoursestudents'));

            if ($newcourseid = insert_record('course', $fromform)) {  // Set up new course
                
                // Setup the blocks
                $page = page_create_object(PAGE_COURSE_VIEW, $newcourseid);
                blocks_repopulate_page($page); // Return value not checked because you can always edit later

                if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
                    $course = get_record("course","id",$newcourseid);
                    update_restricted_mods($course,$allowedmods);
                }

                $section = NULL;
                $section->course = $newcourseid;   // Create a default section.
                $section->section = 0;
                $section->id = insert_record("course_sections", $section);

                fix_course_sortorder();
                add_to_log(SITEID, "course", "new", "view.php?id=$newcourseid", "$fromform->fullname (ID $newcourseid)")        ;
                $context = get_context_instance(CONTEXT_COURSE, $newcourseid);

                if ($fromform->metacourse and has_capability('moodle/course:managemetacourse', $context)) { // Redirect users with metacourse capability to student import
                        redirect($CFG->wwwroot."/course/importstudents.php?id=$newcourseid");

                } else if (has_capability('moodle/role:assign', $context)) { // Redirect users with assign capability to assign users to different roles
                    redirect($CFG->wwwroot."/$CFG->admin/roles/assign.php?contextid=$context->id");

                } else {          // Add current teacher and send to course

                    // find a role with legacy:edittingteacher
                    if ($teacherroles = get_roles_with_capability('moodle/legacy:editingteacher', CAP_ALLOW, $context)) {
                        // assign the role to this user
                        $teachereditrole = array_shift($teacherroles);
                        role_assign($teachereditrole->id, $USER->id, 0, $context->id);
                    }
                    
                    redirect("view.php?id=$newcourseid");
                }


            } else {
                error("Serious Error! Could not create the new course!");
            }
        }
        die;
    }


//print the form

    $streditcoursesettings = get_string("editcoursesettings");
    $straddnewcourse = get_string("addnewcourse");
    $stradministration = get_string("administration");
    $strcategories = get_string("categories");

    if (!empty($course)) {
        print_header($streditcoursesettings, "$course->fullname",
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a>
                      -> $streditcoursesettings", $focus);
    } else {
        print_header("$site->shortname: $straddnewcourse", "$site->fullname",
                     "<a href=\"../$CFG->admin/index.php\">$stradministration</a> -> ".
                     "<a href=\"index.php\">$strcategories</a> -> $straddnewcourse", $focus);
    }


    print_heading($streditcoursesettings);

    $mform->display();
    
    print_footer($course);

?>
