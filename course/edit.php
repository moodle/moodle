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

        if (! $site = get_record("course", "category", 0)) {
            redirect("$CFG->wwwroot/admin/");
        }
    }


/// If data submitted, then process and store.

	if (match_referer() && isset($HTTP_POST_VARS)) {

        $form = (object)$HTTP_POST_VARS;

        $form->startdate = mktime(0,0,0,(int)$form->startmonth,(int)$form->startday,(int)$form->startyear);
        $form->enddate   = mktime(0,0,0,(int)$form->endmonth,(int)$form->endday,(int)$form->endyear);

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
                if ($newid = insert_record("course", $form)) {  // Set up new course
                    $week->course = $newid;   // Create a default week.
                    $week->week = 0;
                    $week->timemodified = time();
                    $week->id = insert_record("course_weeks", $week);

                    add_to_log($newid, "course", "new", "view.php?id=$newid", "");
		            redirect("$CFG->wwwroot/admin/teacher.php?id=$newid", "Changes saved");
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
            $ts = getdate($course->startdate);
            $te = getdate($course->enddate);
        } else {
            $ts = getdate(time() + 3600 * 24);
            $te = getdate(time() + 3600 * 24 * 7 * 16);
            $form->teacher = "Facilitator";
            $form->student = "Student";
            $form->fullname = "Course Fullname 101";
            $form->shortname = "CF101";
            $form->summary = "Write a concise and interesting paragraph here that explains what this course is about.";
            $form->format = 0;
            $form->category = 1;
        }

        $form->startday = $ts[mday];
        $form->startmonth = $ts[mon];
        $form->startyear = $ts[year];

        $form->endday = $te[mday];
        $form->endmonth = $te[mon];
        $form->endyear = $te[year];
    }

    for ($i=1;$i<=31;$i++) {
        $form->days[$i] = "$i";
    }
    for ($i=1;$i<=12;$i++) {
        $form->months[$i] = date("F", mktime(0,0,0,$i,1,2000));
    }
    for ($i=2000;$i<=2005;$i++) {
        $form->years[$i] = $i;
    }

    $form->categories = get_records_sql_menu("SELECT id,name FROM course_categories");

    //$form->owners   = get_records_sql_menu("SELECT u.id, CONCAT(u.firstname, " ", u.lastname) FROM users u, teachers t WHERE t.user = u.id");

    if (isset($course)) {
	    print_header("Edit course settings", "$course->fullname", 
                     "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                      -> Edit course settings", $focus);
    } else {
        print_header("Admin: Creating a new course", "$site->shortname: Administration",
                     "<A HREF=\"$CFG->wwwroot/admin/\">Admin</A> 
                      -> Create a new course", $focus);
    }

    print_simple_box_start("center", "", "$THEME->cellheading");
    print_heading("Editing course settings");
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

    if ($form->startdate > $form->enddate)
        $err["startdate"] = "Starts after it ends!";

#    if (($form->startdate < time()) && ($course->format <> $form->format)) {
#        $err["format"] = "Can't change the format now";
#        $form->format = $course->format;
#    }

    if (! $form->category)
        $err["category"] = "You need to choose a category";

    return;
}


?>
