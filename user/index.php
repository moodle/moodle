<?PHP // $Id$

//  Lists all the users within a given course

    require("../config.php");
    require("../lib/countries.php");
    require("lib.php");

    require_variable($id);   //course
    optional_variable($sort, "u.lastaccess");  //how to sort students
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

    if ( $teachers = get_course_teachers($course->id)) {
        echo "<H2 align=center>$course->teachers</H2>";
        foreach ($teachers as $teacher) {
            if ($teacher->authority > 0) {    // Don't print teachers with no authority
                print_user($teacher, $course, $string);
            }
        }
    }

    if ($students = get_course_students($course->id, "$sort $dir")) {
        $numstudents = count($students);
        echo "<H2 align=center>$numstudents $course->students</H2>";
        if ($numstudents < $USER_SMALL_CLASS) {
            foreach ($students as $student) {
                print_user($student, $course, $string);
            }
        } else {  // Print one big table with abbreviated info
            if ($sort == "u.firstname") {
                $name       = "$string->name";
                $location   = "<A HREF=\"index.php?id=$course->id&sort=u.country&dir=ASC\">$string->location</A>";
                $lastaccess = "<A HREF=\"index.php?id=$course->id&sort=u.lastaccess&dir=DESC\">$string->lastaccess</A>";
            } else if ($sort == "u.country") {
                $name       = "<A HREF=\"index.php?id=$course->id&sort=u.firstname&dir=ASC\">$string->name</A>";
                $location   = "$string->location";
                $lastaccess = "<A HREF=\"index.php?id=$course->id&sort=u.lastaccess&dir=DESC\">$string->lastaccess</A>";
            } else {
                $name       = "<A HREF=\"index.php?id=$course->id&sort=u.firstname&dir=ASC\">$string->name</A>";
                $location   = "<A HREF=\"index.php?id=$course->id&sort=u.country&dir=ASC\">$string->location</A>";
                $lastaccess = "$string->lastaccess";
            }
            $table->head = array ("&nbsp;", $name, $location, $lastaccess);
            $table->align = array ("LEFT", "LEFT", "LEFT", "LEFT");
            $table->size = array ("35", "*", "*", "*");
            
            foreach ($students as $student) {
                if ($student->lastaccess) {
                    $lastaccess = userdate($student->lastaccess);
                    $lastaccess .= "&nbsp (".format_time(time() - $student->lastaccess).")";
                } else {
                    $lastaccess = $string->never;
                }

                $table->data[] = array (print_user_picture($student->id, $course->id, $student->picture, false, true),
                    "<B><A HREF=\"$CFG->wwwroot/user/view.php?id=$student->id&course=$course->id\">$student->firstname $student->lastname</A></B>",
                    "<FONT SIZE=2>$student->city, ".$COUNTRIES["$student->country"]."</FONT>",
                    "<FONT SIZE=2>$lastaccess</FONT>");
            }
            print_table($table, 2, 0);
        }
    } 

    print_footer($course);

?>
