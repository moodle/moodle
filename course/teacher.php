<?PHP // $Id$
      // Admin-only script to assign teachers to courses

	require_once("../config.php");

    define("MAX_USERS_PER_PAGE", 50);

    require_variable($id);         // course id
    optional_variable($add, "");
    optional_variable($remove, "");
    optional_variable($search, ""); // search string

    require_login();

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect (can't find it)");
    }

    if (!(isteacheredit($course->id) and iscreator()) and 
        !(isteacheredit($course->id) and !empty($CFG->teacherassignteachers) ) ) {
        error("You must be an administrator or course creator to use this page.");
    }

    $strassignteachers = get_string("assignteachers");
    $strcourses = get_string("courses");
    $strteachers = get_string("teachers");
    $stradministration = get_string("administration");
    $strexistingteachers   = get_string("existingteachers");
    $strnoexistingteachers = get_string("noexistingteachers");
    $strpotentialteachers  = get_string("potentialteachers");
    $strnopotentialteachers  = get_string("nopotentialteachers");
    $straddteacher    = get_string("addteacher");
    $strremoveteacher = get_string("removeteacher");
    $strsearch        = get_string("search");
    $strsearchresults  = get_string("searchresults");
    $strsearchagain   = get_string("searchagain");
    $strtoomanytoshow   = get_string("toomanytoshow");
    $strname   = get_string("name");
    $strorder   = get_string("order");
    $strrole   = get_string("role");
    $stredit   = get_string("edit");
    $stryes   = get_string("yes");
    $strno   = get_string("no");

    if ($search) {
        $searchstring = $strsearchagain;
    } else {
        $searchstring = $strsearch;
    }

    if ($course->teachers != $strteachers) {
        $parateachers = " ($course->teachers)";
    } else {
        $parateachers = "";
    }



/// Print headers

	print_header("$course->shortname: $strassignteachers", 
                 "$course->fullname", 
                 "<a href=\"index.php\">$strcourses</a> -> ".
                 "<a href=\"view.php?id=$course->id\">$course->shortname</a> -> ".
                 "$strassignteachers", "");


/// If data submitted, then process and store.

    if ($form = data_submitted()) {
        $rank = array();

        // Peel out all the data from variable names.
        foreach ($form as $key => $val) {
            if ($key <> "id") {
                $type = substr($key,0,1);
                $num  = substr($key,1);
                $rank[$num][$type] = $val;
            }
        }

        foreach ($rank as $num => $vals) {
            if (! $teacher = get_record("user_teachers", "course", "$course->id", "userid", "$num")) {
                error("No such teacher in course $course->shortname with user id $num");
            }
            $teacher->role = $vals['r'];
            $teacher->authority = $vals['a'];
            $teacher->editall = $vals['e'];
            if (!update_record("user_teachers", $teacher)) {
                error("Could not update teacher entry id = $teacher->id");
            }
        }
		redirect("teacher.php?id=$course->id", get_string("changessaved"));
	}

/// Add a teacher if one is specified

    if (!empty($_GET['add'])) {
        if (! add_teacher($add, $course->id)) {
            error("Could not add that teacher to this course!");
        }
    }

/// Remove a teacher if one is specified.

    if (!empty($_GET['remove'])) {
        if (! remove_teacher($remove, $course->id)) {
            error("Could not add that teacher to this course!");
        }
    }

/// Display all current teachers for this course.
    $teachers = get_course_teachers($course->id);

    print_heading_with_help("$strexistingteachers $parateachers", "teachers");

    if (empty($teachers)) {
        echo "<p align=center>$strnoexistingteachers</a>";
        $teacherlist = "";

    } else {

        $table->head  = array ("", $strname, $strorder, $strrole, $stredit, "&nbsp");
        $table->align = array ("right", "left", "center", "center", "center", "center");
        $table->size  = array ("35", "", "", "", "10", "");
    
        $ordermenu = NULL;
        $ordermenu[0] = get_string("hide");
        for ($i=1; $i<=8; $i++) {
            $ordermenu[$i] = $i;
        }

        $editmenu = NULL;
        $editmenu[0] = $strno;
        $editmenu[1] = $stryes;

        $teacherarray = array();
    
        echo "<form action=teacher.php method=post>";
        foreach ($teachers as $teacher) {
            $teacherarray[] = $teacher->id;
    
            $picture = print_user_picture($teacher->id, $course->id, $teacher->picture, false, true);
    
            $authority = choose_from_menu ($ordermenu, "a$teacher->id", $teacher->authority, "", "", "", true);

            if ($USER->id == $teacher->id) {
                $editall = "<input name=\"e$teacher->id\" type=\"hidden\" value=\"1\">$stryes";
            } else {
                $editall = choose_from_menu ($editmenu, "e$teacher->id", $teacher->editall, "", "", "", true);
            }
    
            $removelink = "<a href=\"teacher.php?id=$course->id&remove=$teacher->id\">$strremoveteacher</a>";

            if (!$teacher->role) {
                $teacher->role = $course->teacher;
            }
    
            $table->data[] = array ($picture, fullname($teacher, true), $authority,
                                    "<input type=text name=\"r$teacher->id\" value=\"$teacher->role\" size=30>",
                                    $editall, $removelink);
        }
        $teacherlist = implode(",",$teacherarray);
        unset($teacherarray);

        print_table($table);
        echo "<input type=hidden name=id value=\"$course->id\">";
        echo "<center><input type=submit value=\"".get_string("savechanges")."\"> ";
        echo "</center>";
        echo "</form>";
        echo "<br />";
    }


/// Print list of potential teachers

    print_heading("$strpotentialteachers $parateachers");

    $usercount = get_users(false, $search, true, $teacherlist);

    if ($usercount == 0) {
        echo "<p align=center>$strnopotentialteachers</p>";

    } else if ($usercount > MAX_USERS_PER_PAGE) {
        echo "<p align=center>$strtoomanytoshow ($usercount) </p>";

    } else {

        if ($search) {
            echo "<p align=center>($strsearchresults : $search)</p>";
        }

        if (!$users = get_users(true, $search, true, $teacherlist)) {
            error("Could not get users!");
        }

        unset($table);
        $table->head  = array ("", get_string("name"), get_string("email"), "");
        $table->align = array ("right", "left", "center", "center");
        $table->size  = array ("35", "", "", "");


        foreach ($users as $user) {
            $addlink = "<a href=\"teacher.php?id=$course->id&add=$user->id\">$straddteacher</a>";
            $picture = print_user_picture($user->id, $course->id, $user->picture, false, true);
            $table->data[] = array ($picture, fullname($user, true), $user->email, $addlink);
        }
        print_table($table);
    }

    if ($search or $usercount > MAX_USERS_PER_PAGE) {
        echo "<center>";
        echo "<form action=teacher.php method=get>";
        echo "<input type=hidden name=id value=\"$course->id\">";
        echo "<input type=text name=search size=20>";
        echo "<input type=submit value=\"$searchstring\">";
        echo "</form>";
        echo "</center>";
    }

    print_footer();

?>
