<?PHP // $Id$

    require("../config.php");

    require_variable($id);       // course id
    require_variable($user);     // user id

    if (! $course = get_record("course", "id", $id)) {
        error("Course id is incorrect.");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Only teachers can look at this page");
    }

    if (! $user = get_record("user", "id", $user)) {
        error("User ID is incorrect");
    }

    add_to_log("View total report of $user->firstname $user->lastname", $course->id);

    print_header("$course->shortname: Report", "$course->fullname",
                 "<A HREF=\"../course/view.php?id=$course->id\">$course->shortname</A> ->
                  <A HREF=\"../user/index.php?id=$course->id\">Participants</A> ->
                  <A HREF=\"../user/view.php?id=$user->id&course=$course->id\">$user->firstname $user->lastname</A> -> 
                  Full Report", "");

    if ($mods = get_records_sql("SELECT * FROM modules ORDER BY fullname")) {
        foreach ($mods as $mod) {
            $userfile = "$CFG->dirroot/mod/$mod->name/user.php";
            if (file_exists($userfile)) {
                echo "<H2>".$mod->fullname."s</H2>";
                echo "<BLOCKQUOTE>";
                include($userfile);
                echo "</BLOCKQUOTE>";
                echo "<HR WIDTH=100%>";
            }
        }
    }

    print_footer($course);

?>

