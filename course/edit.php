<?php // $Id$
      // Edit course settings

    require_once("../config.php");
    require_once("lib.php");
    require_once("$CFG->libdir/blocklib.php");
    require_once("$CFG->dirroot/enrol/enrol.class.php");

    $id       = optional_param('id', 0, PARAM_INT); // course id
    $category = optional_param('category', 0, PARAM_INT); // possible default category

    require_login();
   
    $disable_meta = false;
    $focus = "";

    if ($id) {
        if (! $course = get_record('course', 'id', $id)) {
            error('Course ID was incorrect');
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
    } else {  // Creating a new course

        $context = get_context_instance(CONTEXT_COURSECAT, $category);
        // first check to see if user has site level course creation. 
        // This is because it is possible that coursecat = 0 when an admin is adding a course
        if (!has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
            if (!has_capability('moodle/course:create',$context)) {
                error("You do not currently have course creation privileges!");
            }
        }

        $course = NULL;
    }

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }


/// If data submitted, then process and store.

    if ($form = data_submitted() and confirm_sesskey()) {

        $form->startdate = make_timestamp($form->startyear, $form->startmonth, $form->startday);
        $form->category = clean_param($form->category, PARAM_INT);

        if (empty($form->enrolstartdisabled)) {
            $form->enrolstartdate = make_timestamp($form->enrolstartyear, $form->enrolstartmonth, $form->enrolstartday);
        } else {
            $form->enrolstartdate = 0;
        }

        if (empty($form->enrolenddisabled)) {
            $form->enrolenddate = make_timestamp($form->enrolendyear, $form->enrolendmonth, $form->enrolendday);
        } else {
            $form->enrolenddate = 0;
        }

        $form->format = optional_param('format', 'social', PARAM_ALPHA);

        $form->defaultrole = optional_param('defaultrole', 0, PARAM_INT);
        if ($form->defaultrole == -1) {   // Just leave it however it is
            unset($form->defaultrole);
        }

        $err = array();
        validate_form($course, $form, $err);

        if (count($err) == 0) {

            $allowedmods = array();
            if (!empty($form->allowedmods)) {
                $allowedmods = $form->allowedmods;
                unset($form->allowedmods);
            }
            
            $form->timemodified = time();
            
            if (!empty($course)) {
                // Test for and remove blocks which aren't appropriate anymore
                $page = page_create_object(PAGE_COURSE_VIEW, $course->id);
                blocks_remove_inappropriate($page);

                // Update with the new data
                if (update_record('course', $form)) {
                    add_to_log($course->id, "course", "update", "edit.php?id=$id", "");
                    if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
                        $course->restrictmodules = $form->restrictmodules;
                        update_restricted_mods($course,$allowedmods);
                    }
                    fix_course_sortorder();
                    // everything ok, no need to display any message in redirect
                    redirect("view.php?id=$course->id");
                } else {
                    error("Serious Error! Could not update the course record! (id = $form->id)");
                }
            } else {
                $form->timecreated = time();

                // place at beginning of category
                fix_course_sortorder();
                $form->sortorder = get_field_sql("SELECT min(sortorder)-1 FROM {$CFG->prefix}course WHERE category=$form->category");                
                if (empty($form->sortorder)) {
                    $form->sortorder = 100;
                }
                // fill in default teacher and student names to keep backwards compatibility
                $form->teacher = addslashes(get_string('defaultcourseteacher'));
                $form->teachers = addslashes(get_string('defaultcourseteachers'));
                $form->student = addslashes(get_string('defaultcoursestudent'));
                $form->students = addslashes(get_string('defaultcoursestudents'));

                if ($newcourseid = insert_record('course', $form)) {  // Set up new course
                    
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
                    add_to_log(SITEID, "course", "new", "view.php?id=$newcourseid", "$form->fullname (ID $newcourseid)")        ;
                    $context = get_context_instance(CONTEXT_COURSE, $newcourseid);

                    if ($form->metacourse and has_capability('moodle/course:managemetacourse', $context)) { // Redirect users with metacourse capability to student import
                        redirect($CFG->wwwroot."/course/importstudents.php?id=$newcourseid");

                    } else if (has_capability('moodle/role:assign', $context)) { // Redirect users with assign capability to assign users to different roles
                        redirect($CFG->wwwroot."/$CFG->admin/roles/assign.php?contextid=$context->id");

                    } else {         // Add current teacher and send to course

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
        } else {
            foreach ($err as $key => $value) {
                $focus = "form.$key";
            }

        }
    }

/// Otherwise fill and print the form.

    if (empty($form)) {
        if (!empty($course)) {
            $form = $course;
        } else {
            $form->startdate = time() + 3600 * 24;
            $form->fullname = get_string("defaultcoursefullname");
            $form->shortname = get_string("defaultcourseshortname");
            $form->summary = get_string("defaultcoursesummary");
            $form->format = "weeks";
            $form->password = "";
            $form->guest = 0;
            $form->numsections = 10;
            $form->idnumber = '';
            $form->cost = '';
            $form->currency = empty($CFG->enrol_currency) ? 'USD' : $CFG->enrol_currency;
            $form->newsitems = 5;
            $form->showgrades = 1;
            $form->groupmode = 0;
            $form->groupmodeforce = 0;
            $form->category = $category;
            $form->id = "";
            $form->visible = 1;

        }
    } else {
        $form = stripslashes_safe($form);
    }

    // !! no db access using data from $form beyond this point !!

    $form->categories = get_records_select_menu("course_categories", "", "name", "id,name");

    $courseformats = get_list_of_plugins("course/format");
    $form->courseformats = array();

    foreach ($courseformats as $courseformat) {
        $form->courseformats["$courseformat"] = get_string("format$courseformat");
    }

    if (empty($allowedmods)) {
        $allowedmods = array();
        if (!empty($course)) {
            if ($am = get_records("course_allowed_modules","course",$course->id)) {
                foreach ($am as $m) {
                    $allowedmods[] = $m->module;
                }
            } else {
                if (empty($course->restrictmodules)) {
                    $allowedmods = explode(',',$CFG->defaultallowedmodules);
                } // it'll be greyed out but we want these by default anyway.
            }
        } else {
            if ($CFG->restrictmodulesfor == 'all') {
                $allowedmods = explode(',',$CFG->defaultallowedmodules);
                if (!empty($CFG->restrictbydefault)) {
                    $form->restrictmodules = 1;
                }
            }
        }
    }


    $usehtmleditor = can_use_html_editor();

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

    $form->sesskey = !empty($USER->id) ? $USER->sesskey : '';

    print_heading($streditcoursesettings);
    print_simple_box_start("center");
    include("edit.html");
    print_simple_box_end();

    if ($usehtmleditor) {
        use_html_editor("summary");
    }

    print_footer($course);

    exit;

/// Functions /////////////////////////////////////////////////////////////////

function validate_form($course, &$form, &$err) {

    if (empty($form->enrolenddisabled) && $form->enrolenddate <= $form->enrolstartdate) {
        $err["enroldate"] = get_string("enrolenddaterror");
    }

    if (empty($form->fullname))
        $err["fullname"] = get_string("missingfullname");

    if (empty($form->shortname))
        $err["shortname"] = get_string("missingshortname");

    if ($foundcourses = get_records("course", "shortname", $form->shortname)) {
        if (!empty($course->id)) {
            unset($foundcourses[$course->id]);
        }
        if (!empty($foundcourses)) {
            foreach ($foundcourses as $foundcourse) {
                $foundcoursenames[] = $foundcourse->fullname;
            }
            $foundcoursenamestring = addslashes(implode(',', $foundcoursenames));

            $err["shortname"] = get_string("shortnametaken", "", $foundcoursenamestring);
        }
    }

    if (empty($form->summary))
        $err["summary"] = get_string("missingsummary");

    if (! $form->category)
        $err["category"] = get_string("missingcategory");

    return;
}


?>
