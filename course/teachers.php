<?PHP // $Id$
      // Allows a teacher to edit teacher order and roles for a course

	require("../config.php");
	require("lib.php");

    require_variable($id);   // course id

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

	require_login($course->id);

    if (!isteacher($course->id)) {
        error("Only teachers can edit the course!");
    }


/// If data submitted, then process and store.

	if (match_referer() && isset($HTTP_POST_VARS)) {

        $rank = array();

        // Peel out all the data from variable names.
        foreach ($HTTP_POST_VARS as $key => $val) {
            if ($key <> "id") {
                $type = substr($key,0,1);
                $num  = substr($key,1);
                $rank[$num][$type] = $val;
            }
        }

        foreach ($rank as $num => $vals) {
            if (! $teacher = get_record_sql("SELECT * FROM user_teachers WHERE course='$course->id' and user='$num'")) {
                error("No such teacher in course $course->shortname with user id $num");
            }
            $teacher->role = $vals[r];
            $teacher->authority = $vals[a];
            if (!update_record("user_teachers", $teacher)) {
                error("Could not update teacher entry id = $teacher->id");
            }
        }
		redirect("teachers.php?id=$course->id", get_string("changessaved"));
	}

/// Otherwise fill and print the form.

	print_header("$course->shortname: $course->teachers", "$course->fullname", 
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                  -> $course->teachers");

    if (!$teachers = get_course_teachers($course->id)) {
        error("No teachers found in this course!");
    }

    print_heading($course->teachers);

    $table->head  = array ("", get_string("name"), get_string("order"), get_string("role"));
    $table->align = array ("RIGHT", "LEFT", "CENTER", "CENTER");
    $table->size  = array ("35", "", "", "");

    echo "<FORM ACTION=teachers.php METHOD=post>";
    foreach ($teachers as $teacher) {

        $picture = print_user_picture($teacher->id, $course->id, $teacher->picture, false, true);

        if (!$teacher->role) {
            $teacher->role = $course->teacher;
        }

        $table->data[] = array ($picture, "$teacher->firstname $teacher->lastname",
                                "<INPUT TYPE=text NAME=\"a$teacher->id\" VALUE=\"$teacher->authority\" SIZE=2>",
                                "<INPUT TYPE=text NAME=\"r$teacher->id\" VALUE=\"$teacher->role\" SIZE=30>");
    }
    print_table($table);
    echo "<INPUT TYPE=hidden NAME=id VALUE=\"$course->id\">";
    echo "<CENTER><BR><INPUT TYPE=submit VALUE=\"".get_string("savechanges")."\"> ";
    helpbutton("teachers", $course->teachers);
    echo "</CENTER>";
    echo "</FORM>";

    print_footer($course);

?>
