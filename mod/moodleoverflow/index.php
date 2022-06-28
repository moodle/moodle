<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Moodleoverflow index.
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Require needed files.
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Fetch submitted parameters.
$id        = required_param('id', PARAM_INT);
$subscribe = optional_param('subscribe', null, PARAM_INT);

// Set an url to go back to the page.
$url = new moodle_url('/mod/moodleoverflow/index.php', array('id' => $id));

// Check whether the subscription parameter was set.
if ($subscribe !== null) {
    require_sesskey();
    $url->param('subscribe', $subscribe);
}

// The the url of this page.
$PAGE->set_url($url);

// Check if the id is related to a valid course.
if (!$course = $DB->get_record('course', array('id' => $id))) {
    throw new moodle_exception('invalidcourseid');
}

// From now on, the user must be enrolled to a course.
require_course_login($course);
$PAGE->set_pagelayout('incourse');
$coursecontext = context_course::instance($course->id);
unset($SESSION->fromdiscussion);

// Trigger the course module instace lise viewed evewnt.
$params = array(
    'context' => context_course::instance($course->id)
);
$event  = \mod_moodleoverflow\event\course_module_instance_list_viewed::create($params);
$event->add_record_snapshot('course', $course);
$event->trigger();

// Cache some strings.
$string                           = array();
$string['moodleoverflow']         = get_string('moodleoverflow', 'moodleoverflow');
$string['moodleoverflows']        = get_string('moodleoverflows', 'moodleoverflow');
$string['modulenameplural']       = get_string('modulenameplural', 'moodleoverflow');
$string['description']            = get_string('description');
$string['discussions']            = get_string('discussions', 'moodleoverflow');
$string['subscribed']             = get_string('subscribed', 'moodleoverflow');
$string['unreadposts']            = get_string('unreadposts', 'moodleoverflow');
$string['tracking']               = get_string('tracking', 'moodleoverflow');
$string['markallread']            = get_string('markallread', 'moodleoverflow');
$string['trackmoodleoverflow']    = get_string('trackmoodleoverflow', 'moodleoverflow');
$string['notrackmoodleoverflow']  = get_string('notrackmoodleoverflow', 'moodleoverflow');
$string['subscribe']              = get_string('subscribe', 'moodleoverflow');
$string['unsubscribe']            = get_string('unsubscribe', 'moodleoverflow');
$string['subscribeenrolledonly']  = get_string('subscribeenrolledonly', 'moodleoverflow');
$string['allsubscribe']           = get_string('allsubscribe', 'moodleoverflow');
$string['allunsubscribe']         = get_string('allunsubscribe', 'moodleoverflow');
$string['generalmoodleoverflows'] = get_string('generalmoodleoverflows', 'moodleoverflow');
$string['yes']                    = get_string('yes');
$string['no']                     = get_string('no');

// Begin to print a table for the general area.
$generaltable        = new html_table();
$generaltable->head  = array($string['moodleoverflow'], $string['description'], $string['discussions']);
$generaltable->align = array('left', 'left', 'center');

// Check whether moodleoverflows can be tracked.
$cantrack = \mod_moodleoverflow\readtracking::moodleoverflow_can_track_moodleoverflows();
if ($cantrack) {
    $untracked = \mod_moodleoverflow\readtracking::get_untracked_moodleoverflows($USER->id, $course->id);

    // Add information about the unread posts to the table.
    $generaltable->head[]  = $string['unreadposts'];
    $generaltable->align[] = 'center';

    // Add information about the tracking to the table.
    $generaltable->head[]  = $string['tracking'];
    $generaltable->align[] = 'center';
}

// Fill the subscription cache for this course and user combination.
\mod_moodleoverflow\subscriptions::fill_subscription_cache_for_course($course->id, $USER->id);

// Retrieve the sections of the course.
$usesections = course_format_uses_sections($course->format);

// Initiate tables and variables.
$table                   = new html_table();
$generalmoodleoverflows  = array();
$modinfo                 = get_fast_modinfo($course);
$showsubscriptioncolumns = false;

// Parse and organize all moodleoverflows.
$sql             = "SELECT m.*
          FROM {moodleoverflow} m
         WHERE m.course = ?";
$moodleoverflows = $DB->get_records_sql($sql, array($course->id));

// Loop through allmoodleoverflows.
foreach ($modinfo->get_instances_of('moodleoverflow') as $moodleoverflowid => $cm) {

    // Check whether the user can see the instance.
    if (!$cm->uservisible OR !isset($moodleoverflows[$moodleoverflowid])) {
        continue;
    }

    // Get the current moodleoverflow instance and the context.
    $moodleoverflow = $moodleoverflows[$moodleoverflowid];
    $modulecontext  = context_module::instance($cm->id);

    // Check whether the user can see the list.
    if (!has_capability('mod/moodleoverflow:viewdiscussion', $modulecontext)) {
        continue;
    }

    // Get information about the subscription state.
    $cansubscribe                 = \mod_moodleoverflow\subscriptions::is_subscribable($moodleoverflow);
    $moodleoverflow->cansubscribe = $cansubscribe || has_capability('mod/moodleoverflow:managesubscriptions', $modulecontext);
    $moodleoverflow->issubscribed = \mod_moodleoverflow\subscriptions::is_subscribed($USER->id, $moodleoverflow, null);
    $showsubscriptioncolumns      = $showsubscriptioncolumns || $moodleoverflow->issubscribed || $moodleoverflow->cansubscribe;

    // Add the moodleoverflow to the cache.
    $generalmoodleoverflows[$moodleoverflowid] = $moodleoverflow;
}

// Check whether the subscription columns need to be displayed.
if ($showsubscriptioncolumns) {
    // The user can subscribe to at least one moodleoverflow.

    // Add the subscription state to the table.
    $generaltable->head[] = $string['subscribed'];
}

// Handle course wide subscriptions or unsubscriptions if requested.
if (!is_null($subscribe)) {

    // Catch guests and not subscribable moodleoverflows.
    if (isguestuser() OR !$showsubscriptioncolumns) {

        // Redirect the user back.
        $url          = new moodle_url('/mod/moodleoverflow/index.php', array('id' => $id));
        $notification = \core\output\notification::NOTIFY_ERROR;
        redirect($url, $string['subscribeenrolledonly'], null, $notification);
    }

    // Loop through all moodleoverflows.
    foreach ($modinfo->get_instances_of('moodleoverflow') as $moodleoverflowid => $cm) {

        // Initiate variables.
        $moodleoverflow = $moodleoverflows[$moodleoverflowid];
        $modulecontext  = context_module::instance($cm->id);
        $cansub         = false;

        // Check capabilities.
        $cap['viewdiscussion']      = has_capability('mod/moodleoverflow:viewdiscussion', $modulecontext);
        $cap['managesubscriptions'] = has_capability('mod/moodleoverflow:managesubscriptions', $modulecontext);
        $cap['manageactivities']    = has_capability('moodle/course:manageactivities', $coursecontext, $USER->id);

        // Check whether the user can view the discussions.
        if ($cap['viewdiscussion']) {
            $cansub = true;
        }

        // Check whether the user can manage subscriptions.
        if ($cansub AND $cm->visible == 0 AND !$cap['managesubscriptions']) {
            $cansub = false;
        }

        // Check the subscription state.
        $forcesubscribed = \mod_moodleoverflow\subscriptions::is_forcesubscribed($moodleoverflow);
        if (!$forcesubscribed) {

            // Check the current state.
            $subscribed   = \mod_moodleoverflow\subscriptions::is_subscribed($USER->id, $moodleoverflow, null);
            $subscribable = \mod_moodleoverflow\subscriptions::is_subscribable($moodleoverflow);

            // Check whether to subscribe or unsubscribe the user.
            if ($cap['manageactivities'] OR $subscribable AND $subscribe AND !$subscribed AND $cansub) {
                \mod_moodleoverflow\subscriptions::subscribe_user($USER->id, $moodleoverflow, $modulecontext, true);
            } else {
                \mod_moodleoverflow\subscriptions::unsubscribe_user($USER->id, $moodleoverflow, $modulecontext, true);
            }
        }
    }

    // Create an url to return the user back to.
    $url      = new moodle_url('/mod/moodleoverflow/index.php', array('id' => $id));
    $returnto = moodleoverflow_go_back_to($url);

    // Prepare the message to be displayed.
    $shortname    = format_string($course->shortname, true, array('context' => context_course::instance($course->id)));
    $notification = \core\output\notification::NOTIFY_SUCCESS;

    // Redirect the user depending on the subscription state.
    if ($subscribe) {
        redirect($returnto, get_string('nowallsubscribed', 'moodleoverflow', $shortname), null, $notification);
    } else {
        redirect($returnto, get_string('nowallunsubscribed', 'moodleoverflow', $shortname), null, $notification);
    }
}

// Check if there are moodleoverflows.
if ($generalmoodleoverflows) {

    // Loop through all of the moodleoverflows.
    foreach ($generalmoodleoverflows as $moodleoverflow) {

        // Retrieve the contexts.
        $cm            = $modinfo->instances['moodleoverflow'][$moodleoverflow->id];
        $modulecontext = context_module::instance($cm->id);

        // Count the discussions within the moodleoverflow.
        $count = moodleoverflow_count_discussions($moodleoverflow, $course);

        // Check whether the user can track the moodleoverflow.
        if ($cantrack) {

            // Check whether the tracking is disabled.
            if ($moodleoverflow->trackingtype == MOODLEOVERFLOW_TRACKING_OFF) {
                $unreadlink  = '-';
                $trackedlink = '-';
            } else {
                // The moodleoverflow can be tracked.

                // Check if this moodleoverflow is manually untracked.
                if (isset($untracked[$moodleoverflow->id])) {
                    $unreadlink = '-';

                } else if ($unread = \mod_moodleoverflow\readtracking::moodleoverflow_count_unread_posts_moodleoverflow($cm,
                    $course)
                ) {
                    // There are unread posts in the moodleoverflow instance.

                    // Create a string to be displayed.
                    $unreadlink = '<span class="unread">';
                    $unreadlink .= '<a href="view.php?m=' . $moodleoverflow->id . '">' . $unread . '</a>';
                    $unreadlink .= '<a title="' . $string['markallread'] . '" href="markposts.php?m=' . $moodleoverflow->id .
                        '&amp;mark=read&amp;sesskey=' . sesskey() . '">';
                    $unreadlink .= '<img src="' . $OUTPUT->image_url('t/markasread') . '" alt="' .
                        $string['markallread'] . '" class="iconsmall" />';
                    $unreadlink .= '</a>';
                    $unreadlink .= '</span>';

                } else {
                    // There are no unread messages for this moodleoverflow instance.

                    // Create a string to be displayed.
                    $unreadlink = '<span class="read">0</span>';
                }

                // Check whether the moodleoverflow instance can be tracked.
                $isforced = $moodleoverflow->trackingtype == MOODLEOVERFLOW_TRACKING_FORCED;
                if ($isforced AND (get_config('moodleoverflow', 'allowforcedreadtracking'))) {
                    // Tracking is set to forced.

                    // Define the string.
                    $trackedlink = $string['yes'];

                } else if ($moodleoverflow->trackingtype === MOODLEOVERFLOW_TRACKING_OFF) {
                    // Tracking is set to off.

                    // Define the string.
                    $trackedlink = '-';

                } else {
                    // Tracking is optional.

                    // Define the url the button is linked to.
                    $trackingurlparams = array('id' => $moodleoverflow->id, 'sesskey' => sesskey());
                    $trackingurl       = new moodle_url('/mod/moodleoverflow/tracking.php', $trackingurlparams);

                    // Check whether the moodleoverflow instance is tracked.
                    if (!isset($untracked[$moodleoverflow->id])) {
                        $trackingparam = array('title' => $string['notrackmoodleoverflow']);
                        $trackedlink   = $OUTPUT->single_button($trackingurl, $string['yes'], 'post', $trackingparam);
                    } else {
                        $trackingparam = array('title' => $string['trackmoodleoverflow']);
                        $trackedlink   = $OUTPUT->single_button($trackingurl, $string['no'], 'post', $trackingparam);
                    }
                }
            }
        }

        // Get information about the moodleoverflow instance.
        $moodleoverflow->intro = shorten_text(format_module_intro('moodleoverflow', $moodleoverflow, $cm->id), 300);
        $moodleoverflowname    = format_string($moodleoverflow->name, true);

        // Check if the context module is visible.
        if ($cm->visible) {
            $style = '';
        } else {
            $style = 'class="dimmed"';
        }

        // Create links to the moodleoverflow and the discussion.
        $moodleoverflowlink = "<a href=\"view.php?m=$moodleoverflow->id\" $style>"
            . format_string($moodleoverflow->name, true) . '</a>';
        $discussionlink     = "<a href=\"view.php?m=$moodleoverflow->id\" $style>" . $count . "</a>";

        // Create rows.
        $row = array($moodleoverflowlink, $moodleoverflow->intro, $discussionlink);

        // Add the tracking information to the rows.
        if ($cantrack) {
            $row[] = $unreadlink;
            $row[] = $trackedlink;
        }

        // Add the subscription information to the rows.
        if ($showsubscriptioncolumns) {

            // Set options to create the subscription link.
            $suboptions = array(
                'subscribed'      => $string['yes'],
                'unsubscribed'    => $string['no'],
                'forcesubscribed' => $string['yes'],
                'cantsubscribe'   => '-',
            );

            // Add the subscription link to the row.
            $row[] = \mod_moodleoverflow\subscriptions::moodleoverflow_get_subscribe_link($moodleoverflow,
                $modulecontext, $suboptions);
        }

        // Add the rows to the table.
        $generaltable->data[] = $row;
    }
}

// Output the page.
$PAGE->navbar->add($string['moodleoverflows']);
$PAGE->set_title($course->shortname . ': ' . $string['moodleoverflows']);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

// Show the subscribe all option only to non-guest and enrolled users.
if (!isguestuser() AND isloggedin() AND $showsubscriptioncolumns) {

    // Create a box.
    echo $OUTPUT->box_start('subscription');

    // Create the subscription link.
    $urlparams        = array('id' => $course->id, 'sesskey' => sesskey());
    $subscriptionlink = new moodle_url('/mod/moodleoverflow/index.php', $urlparams);

    // Give the option to subscribe to all.
    $subscriptionlink->param('subscribe', 1);
    $htmllink = html_writer::link($subscriptionlink, $string['allsubscribe']);
    echo html_writer::tag('div', $htmllink, ['class' => 'helplink']);

    // Give the option to unsubscribe from all.
    $subscriptionlink->param('subscribe', 0);
    $htmllink = html_writer::link($subscriptionlink, $string['allunsubscribe']);
    echo html_writer::tag('div', $htmllink, ['class' => 'helplink']);

    // Print the box.
    echo $OUTPUT->box_end();
    echo $OUTPUT->box('&nbsp;', 'clearer');
}

// Print the moodleoverflows.
if ($generalmoodleoverflows) {
    echo $OUTPUT->heading($string['generalmoodleoverflows'], 2);
    echo html_writer::table($generaltable);
}

// Print the pages footer.
echo $OUTPUT->footer();
