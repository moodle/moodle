<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    $id    = required_param('id',PARAM_INT);           // forum
    $group = optional_param('group',0,PARAM_INT);      // change of group
    $edit  = optional_param('edit',-1,PARAM_BOOL);     // Turn editing on and off

    if (! $forum = get_record("forum", "id", $id)) {
        error("Forum ID is incorrect");
    }

    if (! $course = get_record("course", "id", $forum->course)) {
        error("Could not find this course!");
    }

    if (! $cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        $cm->id = 0;
    }

    require_login($course->id, false, $cm);

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if (!has_capability('mod/forum:viewsubscribers', $context)) {
        error('You do not have the permission to view forum subscribers');
    }

    unset($SESSION->fromdiscussion);

    add_to_log($course->id, "forum", "view subscribers", "subscribers.php?id=$forum->id", $forum->id, $cm->id);

    $strsubscribeall = get_string("subscribeall", "forum");
    $strsubscribenone = get_string("subscribenone", "forum");
    $strsubscribers = get_string("subscribers", "forum");
    $strforums      = get_string("forums", "forum");

    $navlinks = array();
    $navlinks[] = array('name' => $strforums, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $navlinks[] = array('name' => format_string($forum->name), 'link' => "view.php?f=$forum->id", 'type' => 'activityinstance');
    $navlinks[] = array('name' => $strsubscribers, 'link' => '', 'type' => 'title');

    $navigation = build_navigation($navlinks);

    if (has_capability('mod/forum:managesubscriptions', $context)) {
        print_header_simple("$strsubscribers", "", $navigation,
            "", "", true, forum_update_subscriptions_button($course->id, $id));
        if ($edit != -1) {
            $USER->subscriptionsediting = $edit;
        }
    } else {
        print_header_simple("$strsubscribers", "", $navigation, "", "", true, '');
        unset($USER->subscriptionsediting);
    }

/// Check to see if groups are being used in this forum
    groups_print_activity_menu($cm, "subscribers.php?id=$forum->id");
    $currentgroup = groups_get_activity_group($cm);
    $groupmode = groups_get_activity_groupmode($cm);

    if (empty($USER->subscriptionsediting)) {         /// Display an overview of subscribers

        if (! $users = forum_subscribed_users($course, $forum, $currentgroup) ) {

            print_heading(get_string("nosubscribers", "forum"));

        } else {

            print_heading(get_string("subscribersto","forum", "'".format_string($forum->name)."'"));

            echo '<table align="center" cellpadding="5" cellspacing="5">';
            foreach ($users as $user) {
                echo '<tr><td>';
                print_user_picture($user->id, $course->id, $user->picture);
                echo '</td><td>';
                echo fullname($user);
                echo '</td><td>';
                echo $user->email;
                echo '</td></tr>';
            }
            echo "</table>";
        }

        print_footer($course);
        exit;
    }

/// We are in editing mode.

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

    $previoussearch = (!empty($frm->search) or (!empty($frm->previoussearch) && $frm->previoussearch == 1)) ;

/// Get all existing subscribers for this forum.
    if (!$subscribers = forum_subscribed_users($course, $forum, $currentgroup)) {
        $subscribers = array();
    }

    $subscriberarray = array();
    foreach ($subscribers as $subscriber) {
        $subscriberarray[] = $subscriber->id;
    }
    $subscriberlist = implode(',', $subscriberarray);

    unset($subscriberarray);

/// Get search results excluding any users already subscribed

    if (!empty($frm->searchtext) and $previoussearch) {
        $searchusers = search_users($course->id, $currentgroup, $frm->searchtext, 'firstname ASC, lastname ASC', $subscriberlist);
    }

/// If no search results then get potential subscribers for this forum excluding users already subscribed
    if (empty($searchusers)) {
        if ($currentgroup) {
            $users = get_group_users($currentgroup, 'firstname ASC, lastname ASC', $subscriberlist);
        } else {
             $users = get_course_users($course->id, 'firstname ASC, lastname ASC', $subscriberlist);
        }
        if (!$users) {
            $users = array();
        }

    }

    $searchtext = (isset($frm->searchtext)) ? $frm->searchtext : "";
    $previoussearch = ($previoussearch) ? '1' : '0';

    print_simple_box_start('center');

    include('subscriber.html');

    print_simple_box_end();

    print_footer($course);

?>
