<?PHP // $Id$

//  Lists all the users within a given course

    require_once("../config.php");
    require_once("../lib/countries.php");
    require_once("lib.php");

    require_variable($id);   //course
    optional_variable($sort, "lastaccess");  //how to sort students
    optional_variable($dir,"DESC");   //how to sort students

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "user", "view all", "index.php?id=$course->id", "");

    if ($course->category) {
        print_header("$course->shortname: ".get_string("participants"), "$course->fullname",
                     "<A HREF=../course/view.php?id=$course->id>$course->shortname</A> -> ".
                      get_string("participants"), "");
    } else {
        print_header("$course->shortname: ".get_string("participants"), "$course->fullname", 
                      get_string("participants"), "");
    }

    $string->email       = get_string("email");
    $string->location    = get_string("location");
    $string->lastaccess  = get_string("lastaccess");
    $string->activity    = get_string("activity");
    $string->unenrol     = get_string("unenrol");
    $string->loginas     = get_string("loginas");
    $string->fullprofile = get_string("fullprofile");
    $string->role        = get_string("role");
    $string->never       = get_string("never");
    $string->name        = get_string("name");
    $string->day         = get_string("day");
    $string->days        = get_string("days");
    $string->hour        = get_string("hour");
    $string->hours       = get_string("hours");
    $string->min         = get_string("min");
    $string->mins        = get_string("mins");
    $string->sec         = get_string("sec");
    $string->secs        = get_string("secs");

    if ( $teachers = get_course_teachers($course->id)) {
        echo "<H2 align=center>$course->teachers</H2>";
        foreach ($teachers as $teacher) {
            if ($teacher->authority > 0) {    // Don't print teachers with no authority
                print_user($teacher, $course, $string);
            }
        }
    }

    if ($sort == "name") {
        $dsort = "u.firstname";
    } else {
        $dsort = "u.$sort";
    }

    if ($students = get_course_students($course->id, "$dsort $dir")) {
        $numstudents = count($students);
        echo "<H2 align=center>$numstudents $course->students</H2>";
        if ($numstudents < $USER_SMALL_CLASS) {
            foreach ($students as $student) {
                print_user($student, $course, $string);
            }
        } else if ($numstudents > $USER_HUGE_CLASS) {
            print_heading(get_string("toomanytoshow"));

        } else {  // Print one big table with abbreviated info
            $columns = array("name", "city", "country", "lastaccess");

            foreach ($columns as $column) {
                $colname[$column] = get_string($column);
                $columnsort = $column;
                if ($column == "lastaccess") {
                    $columndir = "DESC";
                } else {
                    $columndir = "ASC";
                }
                if ($columnsort == $sort) {
                   $$column = $colname["$column"];
                } else {
                   $$column = "<A HREF=\"index.php?id=$course->id&sort=$columnsort&dir=$columndir\">".$colname["$column"]."</A>";
                }
            }

            foreach ($students as $key => $student) {
                $students[$key]->country = $COUNTRIES[$student->country];
            }
            if ($sort == "country") {  // Need to re-sort by full country name, not code
                foreach ($students as $student) {
                    $sstudents[$student->id] = $student->country;
                }
                asort($sstudents);
                foreach ($sstudents as $key => $value) {
                    $nstudents[] = $students[$key];
                }
                $students = $nstudents;
            }

            $table->head = array ("&nbsp;", $name, $city, $country, $lastaccess);
            $table->align = array ("LEFT", "LEFT", "LEFT", "LEFT", "LEFT");
            $table->size = array ("10", "*", "*", "*", "*");
            $table->size = array ("10", "*", "*", "*", "*");
            $table->cellpadding = 2;
            $table->cellspacing = 0;
            
            foreach ($students as $student) {
                if ($student->lastaccess) {
                    $lastaccess = format_time(time() - $student->lastaccess, $string);
                } else {
                    $lastaccess = $string->never;
                }

                if ($numstudents > $USER_LARGE_CLASS) {  // Don't show pictures
                    $picture = "";
                } else {
                    $picture = print_user_picture($student->id, $course->id, $student->picture, false, true);
                }

                $table->data[] = array ($picture,
                    "<B><A HREF=\"$CFG->wwwroot/user/view.php?id=$student->id&course=$course->id\">$student->firstname $student->lastname</A></B>",
                    "<FONT SIZE=2>$student->city</FONT>", 
                    "<FONT SIZE=2>$student->country</FONT>",
                    "<FONT SIZE=2>$lastaccess</FONT>");
            }
            print_table($table);

        }
        if ($CFG->longtimenosee < 500) {
            echo "<CENTER><P><FONT SIZE=1>(";
            print_string("unusedaccounts","",$CFG->longtimenosee);
            echo ")</FONT></P></CENTER>";
        }
    } 

    print_footer($course);

?>
