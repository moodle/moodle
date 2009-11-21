<?php

require_once("../../config.php");
require_once("lib.php");

$id    = required_param('id',PARAM_INT);           // forum
$group = optional_param('group',0,PARAM_INT);      // change of group
$edit  = optional_param('edit',-1,PARAM_BOOL);     // Turn editing on and off

$url = new moodle_url($CFG->wwwroot.'/mod/forum/subscribers.php', array('id'=>$id));
if ($group !== 0) {
    $url->param('group', $group);
}
if ($edit !== 0) {
    $url->param('edit', $edit);
}
$PAGE->set_url($url);

if (! $forum = $DB->get_record('forum', array('id'=>$id))) {
    print_error('invalidforumid', 'forum');
}

if (! $course = $DB->get_record('course', array('id'=>$forum->course))) {
    print_error('invalidcourseid');
}

if (! $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id)) {
    $cm->id = 0;
}

require_login($course->id, false, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if (!has_capability('mod/forum:viewsubscribers', $context)) {
    print_error('nopermissiontosubscribe', 'forum');
}

unset($SESSION->fromdiscussion);

add_to_log($course->id, "forum", "view subscribers", "subscribers.php?id=$forum->id", $forum->id, $cm->id);

$strsubscribeall = get_string("subscribeall", "forum");
$strsubscribenone = get_string("subscribenone", "forum");
$strsubscribers = get_string("subscribers", "forum");
$strforums = get_string("forums", "forum");

$PAGE->navbar->add($strsubscribers);
$PAGE->set_title($strsubscribers);
if (has_capability('mod/forum:managesubscriptions', $context)) {
    $PAGE->set_button(forum_update_subscriptions_button($course->id, $id));
    if ($edit != -1) {
        $USER->subscriptionsediting = $edit;
    }
} else {
    unset($USER->subscriptionsediting);
}
echo $OUTPUT->header();

/// Check to see if groups are being used in this forum
groups_print_activity_menu($cm, "subscribers.php?id=$forum->id");
$currentgroup = groups_get_activity_group($cm);
$groupmode = groups_get_activity_groupmode($cm);

if (empty($USER->subscriptionsediting)) {         /// Display an overview of subscribers

    if (! $users = forum_subscribed_users($course, $forum, $currentgroup, $context) ) {

        echo $OUTPUT->heading(get_string("nosubscribers", "forum"));

    } else {

        echo $OUTPUT->heading(get_string("subscribersto","forum", "'".format_string($forum->name)."'"));

        echo '<table align="center" cellpadding="5" cellspacing="5">';
        foreach ($users as $user) {
            echo '<tr><td>';
            echo $OUTPUT->user_picture(moodle_user_picture::make($user, $course->id));
            echo '</td><td>';
            echo fullname($user);
            echo '</td><td>';
            echo $user->email;
            echo '</td></tr>';
        }
        echo "</table>";
    }

    echo $OUTPUT->footer();
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

$searchtext = optional_param('searchtext', '', PARAM_RAW);
if ($frm = data_submitted() and confirm_sesskey()) {

/// A form was submitted so process the input
    if (!empty($frm->add) and !empty($frm->addselect)) {
        foreach ($frm->addselect as $addsubscriber) {
            if (! forum_subscribe($addsubscriber, $id)) {
                print_error('cannotaddsubscriber', 'forum', '', $addsubscriber);
            }
        }
    } else if (!empty($frm->remove) and !empty($frm->removeselect)) {
        foreach ($frm->removeselect as $removesubscriber) {
            if (! forum_unsubscribe($removesubscriber, $id)) {
                print_error('cannotremovesubscriber', 'forum', '', $removesubscriber);
            }
        }
    } else if (!empty($frm->showall)) {
        $searchtext = '';
    }
}

/// Get all existing subscribers for this forum.
if (!$subscribers = forum_subscribed_users($course, $forum, $currentgroup, $context)) {
    $subscribers = array();
}

/// Get all the potential subscribers excluding users already subscribed
$users = forum_get_potential_subscribers($context, $currentgroup, 'id,email,firstname,lastname', 'firstname ASC, lastname ASC');
if (!$users) {
    $users = array();
}
foreach ($subscribers as $subscriber) {
    unset($users[$subscriber->id]);
}

/// This is yucky, but do the search in PHP, becuase the list we are using comes from get_users_by_capability,
/// which does not allow searching in the database. Fortunately the list is only this list of users in this
/// course, which is normally OK, except on the site course of a big site. But before you can enter a search
/// term, you have already seen a page that lists everyone, since this code never does paging, so you have probably
/// already crashed your server if you are going to. This will be fixed properly for Moodle 2.0: MDL-17550.
if ($searchtext) {
    $searchusers = array();
    $lcsearchtext = moodle_strtolower($searchtext);
    foreach ($users as $userid => $user) {
        if (strpos(moodle_strtolower($user->email), $lcsearchtext) !== false ||
                strpos(moodle_strtolower($user->firstname . ' ' . $user->lastname), $lcsearchtext) !== false) {
            $searchusers[$userid] = $user;
        }
        unset($users[$userid]);
    }
}

echo $OUTPUT->box_start('generalbox boxaligncenter');

include('subscriber.html');

echo $OUTPUT->box_end();

echo $OUTPUT->footer();

