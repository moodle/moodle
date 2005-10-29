<?php // $Id$
      // Edit course settings

    require_once("../config.php");
    require_once("lib.php");
    require_once("$CFG->libdir/blocklib.php");

    $id = optional_param('id', 0, PARAM_INT); // course id
    $category = optional_param('category', 0, PARAM_INT); // possible default category

    require_login();
   
    $disable_meta = false;
    $focus = "";

    if ($id) {
        if (! $course = get_record("course", "id", $id)) {
            error("Course ID was incorrect");
        }

        if (!isteacheredit($course->id)) {
            error("You do not currently have editing privileges!");
        }
        
        if (course_in_meta($course)) {
            $disable_meta = get_string('metaalreadyinmeta');
        }
        else if ($course->metacourse) {
            if (count_records("course_meta","parent_course",$course->id) > 0) {
                $disable_meta = get_string('metaalreadyhascourses');
            }
        }
        else {
            if (count_records("user_students","course",$course->id) > 0) {
                $disable_meta = get_string('metaalreadyhasenrolments');
            }
        }
    } else {  // Admin is creating a new course

        if (!iscreator()) {
            error("You do not currently have course creation privileges!");
        }

        $course = NULL;
    }

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }


/// If data submitted, then process and store.

    if ($form = data_submitted() and confirm_sesskey()) {

        if (empty($course)) {
            check_for_restricted_user($USER->username, "$CFG->wwwroot");
        } else {
            check_for_restricted_user($USER->username, "$CFG->wwwroot/course/view.php?id=$course->id");
        }

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
                    if (isadmin()) {
                        $course->restrictmodules = $form->restrictmodules;
                        update_restricted_mods($course,$allowedmods);
                    }
                    fix_course_sortorder();
                    redirect($page->url_get_full(), get_string('changessaved'));
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

                if ($newcourseid = insert_record('course', $form)) {  // Set up new course
                    
                    // Setup the blocks
                    $page = page_create_object(PAGE_COURSE_VIEW, $newcourseid);
                    blocks_repopulate_page($page); // Return value not checked because you can always edit later

                    if (isadmin()) {
                        $course = get_record("course","id",$newcourseid);
                        update_restricted_mods($course,$allowedmods);
                    }

                    $section = NULL;
                    $section->course = $newcourseid;   // Create a default section.
                    $section->section = 0;
                    $section->id = insert_record("course_sections", $section);

                    fix_course_sortorder();
                    add_to_log(SITEID, "course", "new", "view.php?id=$newcourseid", "$form->fullname (ID $newcourseid)");

                    if (isadmin()) { // Redirect admin to add teachers
                        redirect("teacher.php?id=$newcourseid", get_string("changessaved"));

                    } else {         // Add current teacher and send to course

                        $newteacher = NULL;
                        $newteacher->userid = $USER->id;
                        $newteacher->course = $newcourseid;
                        $newteacher->authority = 1;   // First teacher is the main teacher
                        $newteacher->editall = 1;     // Course creator can edit their own course

                        if (!$newteacher->id = insert_record("user_teachers", $newteacher)) {
                            error("Could not add you to this new course!");
                        }

                        $USER->teacher[$newcourseid] = true;
                        $USER->teacheredit[$newcourseid] = true;

                        fix_course_sortorder();

                        redirect("view.php?id=$newcourseid", get_string("changessaved"));
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

            if (current_language() == $CFG->lang) {
                $form->teacher  = $site->teacher;
                $form->teachers = $site->teachers;
                $form->student  = $site->student;
                $form->students = $site->students;
            } else {
                $form->teacher = get_string("defaultcourseteacher");
                $form->teachers = get_string("defaultcourseteachers");
                $form->student = get_string("defaultcoursestudent");
                $form->students = get_string("defaultcoursestudents");
            }
        }
    }

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

    print_footer($course);

    if ($usehtmleditor) {
        use_html_editor("summary");
    }

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

    if (empty($form->teacher))
        $err["teacher"] = get_string("missingteacher");

    if (empty($form->student))
        $err["student"] = get_string("missingstudent");

    if (! $form->category)
        $err["category"] = get_string("missingcategory");

    return;
}


?>
