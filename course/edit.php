<?PHP // $Id$
      // Edit course settings

	require_once("../config.php");
	require_once("lib.php");
        require_once("$CFG->libdir/blocklib.php");

    optional_variable($id, 0);   // course id
    optional_variable($category, 0);   // category id

    require_login();

    if ($id) {
        if (! $course = get_record("course", "id", $id)) {
            error("Course ID was incorrect");
        }

        if (!isteacheredit($course->id)) {
            error("You do not currently have editing privileges!");
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

	if ($form = data_submitted()) {

        check_for_restricted_user($USER->username, "$CFG->wwwroot/course/view.php?id=$course->id");

        $form->startdate = make_timestamp($form->startyear, $form->startmonth, $form->startday);

        validate_form($course, $form, $err);

        if (count($err) == 0) {

            $form->timemodified = time();

            if (!empty($course)) {
                if (update_record("course", $form)) {
                    add_to_log($course->id, "course", "update", "edit.php?id=$id", "");
                    fix_course_sortorder($form->category);
		            redirect("view.php?id=$course->id", get_string("changessaved"));
                } else {
                    error("Serious Error! Could not update the course record! (id = $form->id)");
                }
            } else {
                $form->timecreated = time();

                //Create blockinfo default content
                if ($form->format == "social") {
                    $form->blockinfo = blocks_get_default_blocks (NULL,"participants,search_forums,calendar_month,calendar_upcoming,social_activities,recent_activity,admin,course_list");
                } else {
                    //For topics and weeks formats (default built in the function)
                    $form->blockinfo = blocks_get_default_blocks();
                }

                if ($newcourseid = insert_record("course", $form)) {  // Set up new course
                    $section = NULL;
                    $section->course = $newcourseid;   // Create a default section.
                    $section->section = 0;
                    $section->id = insert_record("course_sections", $section);

                    fix_course_sortorder($form->category);
                    add_to_log($newcourseid, "course", "new", "view.php?id=$newcourseid", "");

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

    if (empty($focus)) {
        $focus = "";
    }

    $form->categories = get_records_select_menu("course_categories", "", "name", "id,name");

    $courseformats = get_list_of_plugins("course/format");
    $form->courseformats = array();

    foreach ($courseformats as $courseformat) {
        $form->courseformats["$courseformat"] = get_string("format$courseformat");
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

    print_heading($streditcoursesettings);
    print_simple_box_start("center", "", "$THEME->cellheading");
	include("edit.html");
    print_simple_box_end();

    print_footer($course);

    if ($usehtmleditor) {
        use_html_editor("summary");
    }

    exit;

/// Functions /////////////////////////////////////////////////////////////////

function validate_form($course, &$form, &$err) {

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
