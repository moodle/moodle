<?PHP // $Id$
      // Edit course settings

	require_once("../config.php");
	require_once("lib.php");

    optional_variable($id, 0);   // course id

    if ($id) {
        if (! $course = get_record("course", "id", $id)) {
            error("Course ID was incorrect");
        }

	    require_login($course->id);

        if (!isteacher($course->id)) {
            error("Only teachers can edit the course!");
        }
    } else {  // Admin is creating a new course
        require_login();

        if (!iscreator()) {
            error("Only administrators and teachers can use this page");
        }

        $course = NULL;
    }

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }




/// If data submitted, then process and store.

	if ($form = data_submitted()) {

        $form->startdate = make_timestamp($form->startyear, $form->startmonth, $form->startday);

        validate_form($course, $form, $err);

        if (count($err) == 0) {

            $form->timemodified = time();

            if (!empty($course)) {
                if (update_record("course", $form)) {
                    add_to_log($course->id, "course", "update", "edit.php?id=$id", "");
		            redirect("view.php?id=$course->id", get_string("changessaved"));
                } else {
                    error("Serious Error! Could not update the course record! (id = $form->id)");
                }
            } else {
                $form->timecreated = time();

                if ($newcourseid = insert_record("course", $form)) {  // Set up new course
                    $section = NULL;
                    $section->course = $newcourseid;   // Create a default section.
                    $section->section = 0;
                    $section->id = insert_record("course_sections", $section);

                    add_to_log($newcourseid, "course", "new", "view.php?id=$newcourseid", "");

                    if (isadmin()) { // Redirect admin to add teachers
                        redirect("../$CFG->admin/teacher.php?id=$newcourseid", get_string("changessaved"));

                    } else {         // Add current teacher and send to course
                        
                        $newteacher = NULL;
                        $newteacher->userid = $USER->id;
                        $newteacher->course = $newcourseid;
                        $newteacher->authority = 1;   // First teacher is the main teacher

                        if (!$newteacher->id = insert_record("user_teachers", $newteacher)) {
                            error("Could not add you to this new course!");
                        }

                        $USER->teacher[$newcourseid] = true;

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
            $form->teacher = get_string("defaultcourseteacher");
            $form->teachers = get_string("defaultcourseteachers");
            $form->student = get_string("defaultcoursestudent");
            $form->students = get_string("defaultcoursestudents");
            $form->summary = get_string("defaultcoursesummary");
            $form->format = "weeks";
            $form->password = "";
            $form->guest = 0;
            $form->numsections = 10;
            $form->newsitems = 5;
            $form->showrecent = 1;
            $form->category = 1;
            $form->id = "";
        }
    }

    if (empty($focus)) {
        $focus = "";
    }

    $form->categories = get_records_select_menu("course_categories", "", "name", "id,name");
    
    $form->courseformats = array (
             "weeks"  => get_string("formatweeks"),
             "social" => get_string("formatsocial"),
             "topics" => get_string("formattopics")
    );

    $streditcoursesettings = get_string("editcoursesettings");
    $straddnewcourse = get_string("addnewcourse");
    $stradministration = get_string("administration");

    if (!empty($course)) {
	    print_header($streditcoursesettings, "$course->fullname", 
                     "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                      -> $streditcoursesettings", $focus);
    } else {
        print_header("$site->shortname: $straddnewcourse", "$site->fullname",
                     "<A HREF=\"../$CFG->admin/index.php\">$stradministration</A> 
                      -> $straddnewcourse", $focus);
    }

    print_heading($streditcoursesettings);
    print_simple_box_start("center", "", "$THEME->cellheading");
	include("edit.html");
    print_simple_box_end();

    print_footer($course);

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
