<?PHP  // $Id$

    require("../../config.php");
    require("lib.php");

    require_variable($id);          // forum

    if (! $forum = get_record("forum", "id", $id)) {
        error("Forum ID is incorrect");
    }

    if (! $course = get_record("course", "id", $forum->course)) {
        error("Could not find this course!");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("This page is for teachers only");
    }

    unset($SESSION->fromdiscuss);

    add_to_log($course->id, "forum", "view subscribers", "subscribers.php?id=$forum->id", "");

    $strsubscribers = get_string("subscribers", "forum");
    $strforums      = get_string("forums", "forum");

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->
                       <A HREF=\"index.php?id=$course->id\">$strforums</A> -> 
                       <A HREF=\"view.php?f=$forum->id\">$forum->name</A> -> $strsubscribers";
    } else {
        $navigation = "<A HREF=\"index.php?id=$course->id\">$strforums</A> -> 
                       <A HREF=\"view.php?f=$forum->id\">$forum->name</A> -> $strsubscribers";
    }

    print_header("$course->shortname: $strsubscribers", "$course->fullname", "$navigation");

    if (! $users = get_course_users($course->id)) {
        print_heading("No users yet");

    } else {
        print_heading("Subscribers to '$forum->name'");
        echo "<TABLE ALIGN=CENTER>";
        $count = 0;
        foreach ($users as $user) {
            if (is_subscribed($user->id, $forum->id)) {
                echo "<TR><TD>";
                print_user_picture($user->id, $course->id, $user->picture);
                echo "</TD><TD>";
                echo "$user->firstname $user->lastname";
                echo "</TD></TR>";
                $count++;
            }
        }
        if (!$count) {
            echo "<TR><TD>No subscribers yet</TD></TR>";
        }
        echo "</TABLE>";
    }

    print_footer($course);

?>
