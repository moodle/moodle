<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);                // forum
    optional_variable($subscribe, '');    // 'all' or 'none'
    optional_variable($unsubscribe, '');  // a single user id
    optional_variable($group);            // change of group
    
    optional_variable($edit);     // Turn editing on and off

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
    
    if (isteacheredit($course->id)) {
        if (isset($_GET['edit'])) {
            if($edit == "on") {
                $USER->subscriptionsediting = true;
            } else {
                $USER->subscriptionsediting = false;
            }
        }
    } else {
        $USER->subscriptionsediting = false;
    }

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

    print_header("$course->shortname: $strsubscribers", "$course->fullname", "$navigation", 
        "", "", true, forum_update_subscriptions_button($course->id, $id));

    if (empty($USER->subscriptionsediting)) {         /// Display an overview of subscribers
    
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
        exit;
    }

/// We are in editing mode.

    if (!isteacheredit($course->id)) {
        error("You must be an editing teacher in this course, or an admin");
    }

    $strexistingsubscribers   = get_string("existingsubscribers", 'forum');
    $strpotentialsubscribers  = get_string("potentialsubscribers", 'forum');
    $straddsubscriber    = get_string("addsubscriber", 'forum');
    $strremovesubscriber = get_string("removesubscriber", 'forum');
    $strsearch        = get_string("search");
    $strsearchresults  = get_string("searchresults");
    $strshowall = get_string("showall");
    $strsubscribers = get_string("subscribers", "forum");
    $strforums      = get_string("forums", "forum");

    if ($frm = data_submitted()) {

/// A form was submitted so process the input

        if (!empty($frm->add) and !empty($frm->addselect)) {

            foreach ($frm->addselect as $addsubscriber) {
                if (! forum_subscribe($addsubscriber, $id)) {
                    error("Could not add subscriber with id $addsubscriber to this forum!");
                }
            }
        } else if (!empty($frm->remove) and !empty($frm->removeselect)) {
            foreach ($frm->removeselect as $removesubscriber) {
                if (! forum_unsubscribe($removesubscriber, $id)) {
                    error("Could not remove subscriber with id $removesubscriber from this forum!");
                }
            }
        } else if (!empty($frm->showall)) {
            unset($frm->searchtext);
            $frm->previoussearch = 0;
        }
    }

    $previoussearch = (!empty($frm->search) or ($frm->previoussearch == 1)) ;

/// Get all existing subscribers for this forum.
    if (!$subscribers = forum_subscribed_users($course, $forum)) {
        $subscribers = array();
    }
    
    $subscriberarray = array();
    foreach ($subscribers as $subscriber) {
        $subscriberarray[] = $subscriber->id;
    }
    $subscriberlist = implode(',', $subscriberarray);
    
    unset($subscriberarray);

/// Get search results excluding any users already subscribed
    switch ($CFG->dbtype) {
        case "mysql":
             $fullname = " CONCAT(u.firstname,\" \",u.lastname) ";
             $LIKE = "LIKE";
             break;
        case "postgres7":
             $fullname = " u.firstname||' '||u.lastname ";
             $LIKE = "ILIKE";
             break;
        default: 
             $fullname = " u.firstname||\" \"||u.lastname ";
             $LIKE = "ILIKE";
    }
    if (!empty($subscriberlist)) {
        $except = " AND u.id NOT IN ($subscriberlist) ";
    } else {
        $except = '';
    }
    if (!empty($frm->searchtext) and $previoussearch) {
        $searchusers = get_records_sql("SELECT u.id, u.firstname, u.lastname, u.email
                              FROM {$CFG->prefix}user u, 
                                   {$CFG->prefix}user_students s
                              WHERE s.course = '$course->id' AND s.userid = u.id AND u.deleted = '0'
                                  AND ($fullname $LIKE '%$frm->searchtext%' OR u.email $LIKE '%$frm->searchtext%')
                                  $except
                              UNION
                              SELECT u.id, u.firstname, u.lastname, u.email
                              FROM {$CFG->prefix}user u, 
                                   {$CFG->prefix}user_teachers s
                              WHERE s.course = '$course->id' AND s.userid = u.id AND u.deleted = '0'
                                  AND ($fullname $LIKE '%$frm->searchtext%' OR u.email $LIKE '%$frm->searchtext%')
                                  $except
                              ORDER BY u.firstname ASC, u.lastname ASC");

        $usercount = count_records_sql("SELECT COUNT(*)
                              FROM {$CFG->prefix}user u, 
                                   {$CFG->prefix}user_students s
                              WHERE s.course = '$course->id' AND s.userid = u.id AND u.deleted = '0'
                                  $except") +
                     count_records_sql("SELECT COUNT(*)
                              FROM {$CFG->prefix}user u, 
                                   {$CFG->prefix}user_teachers s
                              WHERE s.course = '$course->id' AND s.userid = u.id AND u.deleted = '0'
                                  $except");
    }
    
/// If no search results then get potential subscribers for this forum excluding users already subscribed
    if (empty($searchusers)) {
        if (!$users = get_records_sql("SELECT u.id, u.firstname, u.lastname, u.email
                              FROM {$CFG->prefix}user u, 
                                   {$CFG->prefix}user_students s
                              WHERE s.course = '$course->id' AND s.userid = u.id AND u.deleted = '0'
                                  $except
                              UNION
                              SELECT u.id, u.firstname, u.lastname, u.email
                              FROM {$CFG->prefix}user u, 
                                   {$CFG->prefix}user_teachers s
                              WHERE s.course = '$course->id' AND s.userid = u.id AND u.deleted = '0'
                                  $except
                              ORDER BY u.firstname ASC, u.lastname ASC")) {
            $users = array();
        }
        $usercount = count($users);
    }


    

    $searchtext = (isset($frm->searchtext)) ? $frm->searchtext : "";
    $previoussearch = ($previoussearch) ? '1' : '0';

    print_simple_box_start("center", "", "$THEME->cellheading");

    include('subscriber.html');

    print_simple_box_end();

    print_footer();

?>
