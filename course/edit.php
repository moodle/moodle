<?PHP // $Id$

	require("../config.php");
	require("lib.php");

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

        if (!isadmin()) {
            error("Only administrators can use this page");
        }

        if (! $site = get_site()) {
            redirect("$CFG->wwwroot/admin/");
        }
    }


/// If data submitted, then process and store.

	if (match_referer() && isset($HTTP_POST_VARS)) {

        $form = (object)$HTTP_POST_VARS;

        $form->startdate = make_timestamp($form->startyear, $form->startmonth, $form->startday);

        validate_form($course, $form, $err);


        if (count($err) == 0) {

            $form->timemodified = time();

            if ($course) {
                if (update_record("course", $form)) {
                    add_to_log($course->id, "course", "update", "edit.php?id=$id", "");
		            redirect("view.php?id=$course->id", "Changes saved");
                } else {
                    error("Serious Error! Could not update the course record! (id = $form->id)");
                }
            } else {
                $form->timecreated = time();

                if ($newid = insert_record("course", $form)) {  // Set up new course
                    $section->course = $newid;   // Create a default section.
                    $section->section = 0;
                    $section->timemodified = time();
                    $section->id = insert_record("course_sections", $section);

                    add_to_log($newid, "course", "new", "view.php?id=$newid", "");
		            redirect("$CFG->wwwroot/admin/teacher.php?id=$newid", get_string("changessaved"));
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

    if (!$form) {
        if ($course) {
            $form = $course;
        } else {
            $form->startdate = time() + 3600 * 24;
            $form->teacher = "Facilitator";
            $form->student = "Student";
            $form->fullname = "Course Fullname 101";
            $form->shortname = "CF101";
            $form->summary = "Write a concise and interesting paragraph here that explains what this course is about.";
            $form->format = "weeks";
            $form->numsections = 10;
            $form->newsitems = 5;
            $form->category = 1;
        }
    }

    $form->categories = get_records_sql_menu("SELECT id,name FROM course_categories");

    $editcoursesettings = get_string("editcoursesettings");

    if (isset($course)) {
	    print_header($editcoursesettings, "$course->fullname", 
                     "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                      -> $editcoursesettings", $focus);
    } else {
        print_header("Admin: Creating a new course", "$site->shortname: Administration",
                     "<A HREF=\"$CFG->wwwroot/admin/\">Admin</A> 
                      -> Create a new course", $focus);
    }

    print_simple_box_start("center", "", "$THEME->cellheading");
    print_heading($editcoursesettings);
	include("edit.html");
    print_simple_box_end();

    print_footer($course);

    exit;

/// Functions /////////////////////////////////////////////////////////////////

function validate_form($course, &$form, &$err) {

    if (empty($form->fullname))
        $err["fullname"] = "Missing full name";

    if (empty($form->shortname))
        $err["shortname"] = "Missing short name";

    if (empty($form->summary))
        $err["summary"] = "Missing summary";

    if (empty($form->teacher))
        $err["teacher"] = "Must choose something";

    if (empty($form->student))
        $err["student"] = "Must choose something";

    if (! $form->category)
        $err["category"] = "You need to choose a category";

    return;
}


?>
