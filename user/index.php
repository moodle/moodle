<?PHP // $Id$

//  Lists all the users within a given course

    require("../config.php");
    require("../lib/countries.php");
    require("lib.php");

    require_variable($id);   //course

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

    if ( $teachers = get_course_teachers($course->id)) {
        echo "<H2 align=center>$course->teachers</H2>";
        foreach ($teachers as $teacher) {
            if ($teacher->authority > 0) {    // Don't print teachers with no authority
                print_user($teacher, $course, $string);
            }
        }
    }

    if ($students = get_course_students($course->id)) {
        echo "<H2 align=center>$course->students</H2>";
        foreach ($students as $student) {
            print_user($student, $course, $string);
        }
    } 

    print_footer($course);

?>
