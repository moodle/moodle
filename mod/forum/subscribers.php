<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

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

    unset($SESSION->fromdiscussion);
    save_session("SESSION");

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

    if (! $users = forum_subscribed_users($course, $forum) ) {
        print_heading(get_string("nosubscribers", "forum"));

    } else {
        print_heading(get_string("subscribersto","forum", "'$forum->name'"));
        echo "<TABLE ALIGN=CENTER cellpadding=5 cellspacing=5>";
        foreach ($users as $user) {
            echo "<TR><TD>";
            print_user_picture($user->id, $course->id, $user->picture);
            echo "</TD><TD BGCOLOR=\"$THEME->cellcontent\">";
            echo "$user->firstname $user->lastname";
            echo "</TD><TD BGCOLOR=\"$THEME->cellcontent\">";
            echo "$user->email";
            echo "</TD><TD>";
            echo "<FONT SIZE=1><A HREF=\"subscribe.php?id=$forum->id&user=$user->id\">unsubscribe</A></FONT>";
            echo "</TD></TR>";
        }
        echo "</TABLE>";
    }

    print_footer($course);

?>
