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

    $subscribers = get_records_sql("SELECT u.* FROM user u, user_students us, user_teachers ut, 
                                          forum_subscriptions fs
                                    WHERE fs.forum = '$forum->id' AND fs.user = u.id AND 
                                          (fs.user = us.user OR fs.user = ut.user) 
                                    GROUP BY u.id 
                                    ORDER BY u.firstname");

    if (! $subscribers) {
        print_heading("No subscribers yet");
    } else {
        print_heading("Subscribers to '$forum->name'");
        echo "<TABLE ALIGN=CENTER>";
        foreach ($subscribers as $subscriber) {
            echo "<TR><TD>";
            print_user_picture($subscriber->id, $course->id, $subscriber->picture);
            echo "</TD><TD>";
            echo "$subscriber->firstname $subscriber->lastname";
            echo "</TD></TR>";
        }
        echo "</TABLE>";
    }

    print_footer($course);

?>
