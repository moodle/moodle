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

    if (!isstudent($course->id, $user)) {
        error("This student is not in this course!");
    }

    // Login as this student and return to course home page.

    $teacher_name = "$USER->firstname $USER->lastname";

    $USER = get_user_info_from_db("id", $user);
    $USER->loggedin = true;
    $USER->realuser = $teacher_name;

    set_moodle_cookie($USER->username);

    $student_name = "$USER->firstname $USER->lastname";
    
    add_to_log($course->id, "course", "loginas", "../user/view.php?id=$course->id&user=$user", "$teacher_name");

    notice("You are now logged in as $student_name", "$CFG->wwwroot/course/view.php?id=$course->id");

?>
