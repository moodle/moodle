<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);                // forum
    optional_variable($subscribe, '');    // 'all' or 'none'
    optional_variable($unsubscribe, '');  // a single user id
    optional_variable($group);            // change of group

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

    add_to_log($course->id, "forum", "view subscribers", "subscribers.php?id=$forum->id", $forum->id, $cm->id);

    $strunsubscribeshort = get_string("unsubscribeshort", "forum");
    $strsubscribeall = get_string("subscribeall", "forum");
    $strsubscribenone = get_string("subscribenone", "forum");
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


/// Check to see if groups are being used in this forum
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, "subscribers.php?id=$forum->id");
    } else {
        $currentgroup = false;
    }

    if ($subscribe == 'all') {
        if ($forum->type == 'teacher') {
            $users = get_course_teachers($course->id);
        } elseif ($currentgroup) {
            $users = get_group_users($currentgroup);
        } else {
            $users = get_course_users($course->id);
        }
        if ($users) {
            foreach ($users as $user) {
                forum_subscribe($user->id, $forum->id);
            }
        }
    } else if ($subscribe == 'none') {
        if ($currentgroup) {
            if ($users = get_group_users($currentgroup)) {
                foreach ($users as $user) {
	forum_unsubscribe($user->id, $forum->id);
                }
            }
        } else {
            delete_records("forum_subscriptions", "forum", $forum->id);
        }
    }

    if ($unsubscribe) {
        if ($user = get_record('user', 'id', $unsubscribe)) {
            forum_unsubscribe($user->id, $forum->id);
            $info->name  = fullname($user);
            $info->forum = $forum->name;
            notify(get_string("nownotsubscribed", "forum", $info));
        }
    }

    if (! $users = forum_subscribed_users($course, $forum, $currentgroup) ) {

        if (!$forum->forcesubscribe) {
            echo '<center>';
            $options['id'] = $forum->id;
            $options['subscribe'] = 'all';
            print_single_button('subscribers.php', $options, $strsubscribeall);
            echo '</center>';
        }

        print_heading(get_string("nosubscribers", "forum"));

    } else {

        if (!$forum->forcesubscribe) {
            echo '<table align="center"><tr>';
            echo '<td>';
            $options['id'] = $forum->id;
            $options['subscribe'] = 'all';
            print_single_button('subscribers.php', $options, $strsubscribeall);
            echo '</td>';
            echo '<td>';
            $options['subscribe'] = 'none';
            print_single_button('subscribers.php', $options, $strsubscribenone);
            echo '</td>';
            echo '</tr></table>';
        }

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
            echo "<font size=1><a href=\"subscribers.php?id=$forum->id&unsubscribe=$user->id\">$strunsubscribeshort</a></font>";
            echo "</td></tr>";
        }
        echo "</table>";
    }

    print_footer($course);

?>
