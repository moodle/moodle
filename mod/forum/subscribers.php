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

    if (! $cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        $cm->id = 0;
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("This page is for teachers only");
    }

    unset($SESSION->fromdiscussion);

    add_to_log($course->id, "forum", "view subscribers", "subscribers.php?id=$forum->id", "", $cm->id);

    $strsubscribers = get_string("subscribers", "forum");
    $strforums      = get_string("forums", "forum");

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                       <a href=\"index.php?id=$course->id\">$strforums</a> -> 
                       <a href=\"view.php?f=$forum->id\">$forum->name</a> -> $strsubscribers";
    } else {
        $navigation = "<a href=\"index.php?id=$course->id\">$strforums</a> -> 
                       <a href=\"view.php?f=$forum->id\">$forum->name</a> -> $strsubscribers";
    }

    print_header("$course->shortname: $strsubscribers", "$course->fullname", "$navigation");

    if (! $users = forum_subscribed_users($course, $forum) ) {
        print_heading(get_string("nosubscribers", "forum"));

    } else {
        print_heading(get_string("subscribersto","forum", "'$forum->name'"));
        echo '<table align="center" cellpadding="5" cellspacing="5">';
        foreach ($users as $user) {
            echo "<tr><td>";
            print_user_picture($user->id, $course->id, $user->picture);
            echo "</td><td bgcolor=\"$THEME->cellcontent\">";
            echo "$user->firstname $user->lastname";
            echo "</td><td bgcolor=\"$THEME->cellcontent\">";
            echo "$user->email";
            echo "</td><td>";
            echo "<font size=1><a href=\"subscribe.php?id=$forum->id&user=$user->id\">unsubscribe</a></font>";
            echo "</td></tr>";
        }
        echo "</table>";
    }

    print_footer($course);

?>
