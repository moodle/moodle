<?PHP // $Id$

    require("../config.php");
    require("lib.php");

    require_variable($id);     // course id
    require_variable($user);   // login as this user

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Only teachers can use this page!");
    }

    if ($course->category and !isstudent($course->id, $user)) {
        error("This student is not in this course!");
    }

    // Login as this student and return to course home page.

    $teacher_name = "$USER->firstname $USER->lastname";
    $teacher_id   = "$USER->id";

    $USER = get_user_info_from_db("id", $user);
    $USER->loggedin = true;
    $USER->realuser = $teacher_id;
    save_session("USER");

    set_moodle_cookie($USER->username);
    $student_name = "$USER->firstname $USER->lastname";

    add_to_log($course->id, "course", "loginas", "../user/view.php?id=$course->id&user=$user", "$teacher_name -> $student_name");


    $strloginas    = get_string("loginas");
    $strloggedinas = get_string("loggedinas", "", $student_name);

    print_header("$course->fullname: $strloginas $student_name", "$course->fullname", 
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> 
                  $strloginas $student_name");
    notice($strloggedinas, "$CFG->wwwroot/course/view.php?id=$course->id");


?>
