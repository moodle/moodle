<?PHP // $Id$

//  Lists all the users within a given course

    require_once("../config.php");
    require_once("lib.php");

    require_variable($id);   //course
    optional_variable($sort, "lastaccess");  //how to sort students
    optional_variable($dir,"desc");          //how to sort students
    optional_variable($page, "0");           // which page to show
    optional_variable($perpage, "20");       // how many per page


    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "user", "view all", "index.php?id=$course->id", "");

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

    $countries = get_list_of_countries();

    $loggedinas = "<p class=\"logininfo\">".user_login_string($course, $USER)."</p>";

    $showteachers = ($page == 0 and $sort == "lastaccess" and $dir == "desc");

    if ($showteachers) {
        $participantslink = get_string("participants");
    } else {
        $participantslink = "<a href=\"index.php?id=$course->id\">".get_string("participants")."</a>";
    }

    if ($course->category) {
        print_header("$course->shortname: ".get_string("participants"), "$course->fullname",
                     "<A HREF=../course/view.php?id=$course->id>$course->shortname</A> -> ".
                     "$participantslink", "", "", true, "&nbsp;", $loggedinas);
    } else {
        print_header("$course->shortname: ".get_string("participants"), "$course->fullname", 
                     "$participantslink", "", "", true, "&nbsp;", $loggedinas);
    }


    if ($showteachers) {
        if ( $teachers = get_course_teachers($course->id)) {
            echo "<h2 align=center>$course->teachers</h2>";
            foreach ($teachers as $teacher) {
                if ($teacher->authority > 0) {    // Don't print teachers with no authority
                    print_user($teacher, $course, $string, $countries);
                }
            }
        }
    }

    $dsort = "u.$sort";

    $totalcount = count_records("user_students", "course", $course->id);

    echo "<h2 align=center>$totalcount $course->students</h2>";

    if ($CFG->longtimenosee < 500) {
        echo "<center><p><font size=1>(";
        print_string("unusedaccounts","",$CFG->longtimenosee);
        echo ")</font></p></center>";
    }

    if (0 < $totalcount and $totalcount < USER_SMALL_CLASS) {    // Print simple listing

        if ($students = get_course_students($course->id, $dsort, $dir)) {
            foreach ($students as $student) {
                print_user($student, $course, $string, $countries);
            }
        }

    } else if ($students = get_course_students($course->id, $dsort, $dir, $page*$perpage, $perpage)) {

        print_paging_bar($totalcount, $page, $perpage, 
                         "index.php?id=$course->id&sort=$sort&dir=$dir&perpage=$perpage&");

        // Print one big table with abbreviated info
        $columns = array("firstname", "lastname", "city", "country", "lastaccess");

        foreach ($columns as $column) {
            $colname[$column] = get_string($column);
            if ($sort != $column) {
                $columnicon = "";
                if ($column == "lastaccess") {
                    $columndir = "desc";
                } else {
                    $columndir = "asc";
                }
            } else {
                $columndir = $dir == "asc" ? "desc":"asc";
                if ($column == "lastaccess") {
                    $columnicon = $dir == "asc" ? "up":"down";
                } else {
                    $columnicon = $dir == "asc" ? "down":"up";
                }
                $columnicon = " <img src=\"$CFG->pixpath/t/$columnicon.gif\" />";
            }
            $$column = "<a href=\"index.php?id=$course->id&sort=$column&dir=$columndir\">".$colname["$column"]."</a>$columnicon";
        }

        foreach ($students as $key => $student) {
            $students[$key]->country = $countries[$student->country];
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

        $table->head = array ("&nbsp;", "$firstname / $lastname", $city, $country, $lastaccess);
        $table->align = array ("LEFT", "LEFT", "LEFT", "LEFT", "LEFT");
        $table->size = array ("10",  "*", "*", "*", "*");
        $table->size = array ("10",  "*", "*", "*", "*");
        $table->cellpadding = 4;
        $table->cellspacing = 0;
        
        foreach ($students as $student) {

            if ($student->lastaccess) {
                $lastaccess = format_time(time() - $student->lastaccess, $string);
            } else {
                $lastaccess = $string->never;
            }

            if ($showall and $numstudents > USER_LARGE_CLASS) {  // Don't show pictures
                $picture = "";
            } else {
                $picture = print_user_picture($student->id, $course->id, $student->picture, false, true);
            }

            $table->data[] = array ($picture,
                "<b><a href=\"$CFG->wwwroot/user/view.php?id=$student->id&course=$course->id\">$student->firstname $student->lastname</a></b>",
                "<font size=2>$student->city</font>", 
                "<font size=2>$student->country</font>",
                "<font size=2>$lastaccess</font>");
        }
        print_table($table);

        print_paging_bar($totalcount, $page, $perpage, 
                         "index.php?id=$course->id&sort=$sort&dir=$dir&perpage=$perpage&");

        if ($perpage != 99999) {
            echo "<center><p>";
            echo "<a href=\"index.php?id=$course->id&sort=$sort&dir=$dir&perpage=99999\">".get_string("showall", "", $totalcount)."</a>";
            echo "</p></center>";
        }

    } 

    print_footer($course);

?>
