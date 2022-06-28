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
 * Subscribe to or unsubscribe from a moodleoverflow or manage moodleoverflow subscription mode.
 *
 * This script can be used by either individual users to subscribe to or
 * unsubscribe from a moodleoverflow (no 'mode' param provided), or by moodleoverflow managers
 * to control the subscription mode (by 'mode' param).
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/locallib.php');

// Define required and optional params.
$id           = required_param('id', PARAM_INT);             // The moodleoverflow to set subscription on.
$mode         = optional_param('mode', null, PARAM_INT);     // The moodleoverflow's subscription mode.
$user         = optional_param('user', 0, PARAM_INT);        // The userid of the user to subscribe, defaults to $USER.
$discussionid = optional_param('d', null, PARAM_INT);        // The discussionid to subscribe.
$sesskey      = optional_param('sesskey', null, PARAM_RAW);
$returnurl    = optional_param('returnurl', null, PARAM_RAW);

// Set the url to return to the same action.
$url = new moodle_url('/mod/moodleoverflow/subscribe.php', array('id' => $id));
if (!is_null($mode)) {
    $url->param('mode', $mode);
}
if ($user !== 0) {
    $url->param('user', $user);
}
if (!is_null($sesskey)) {
    $url->param('sesskey', $sesskey);
}
if (!is_null($discussionid)) {
    $url->param('d', $discussionid);
    if (!$discussion = $DB->get_record('moodleoverflow_discussions', array('id' => $discussionid, 'moodleoverflow' => $id))) {
        throw new moodle_exception('invaliddiscussionid', 'moodleoverflow');
    }
}

// Set the pages URL.
$PAGE->set_url($url);

// Get all necessary objects.
$moodleoverflow = $DB->get_record('moodleoverflow', array('id' => $id), '*', MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $moodleoverflow->course), '*', MUST_EXIST);
$cm             = get_coursemodule_from_instance('moodleoverflow', $moodleoverflow->id, $course->id, false, MUST_EXIST);
$context        = context_module::instance($cm->id);

// Define variables.
$notify                             = array();
$notify['success']                  = \core\output\notification::NOTIFY_SUCCESS;
$notify['error']                    = \core\output\notification::NOTIFY_ERROR;
$strings                            = array();
$strings['subscribeenrolledonly']   = get_string('subscribeenrolledonly', 'moodleoverflow');
$strings['everyonecannowchoose']    = get_string('everyonecannowchoose', 'moodleoverflow');
$strings['everyoneisnowsubscribed'] = get_string('everyoneisnowsubscribed', 'moodleoverflow');
$strings['noonecansubscribenow']    = get_string('noonecansubscribenow', 'moodleoverflow');
$strings['invalidforcesubscribe']   = get_string('invalidforcesubscribe', 'moodleoverflow');

// Check if the user was requesting the subscription himself.
if ($user) {
    // A manager requested the subscription.

    // Check the login.
    require_sesskey();

    // Check the users capabilities.
    if (!has_capability('mod/moodleoverflow:managesubscriptions', $context)) {
        throw new moodle_exception('nopermissiontosubscribe', 'moodleoverflow');
    }

    // Retrieve the user from the database.
    $user = $DB->get_record('user', array('id' => $user), '*', MUST_EXIST);

} else {

    // The user requested the subscription himself.
    $user = $USER;
}

// Check if the user is already subscribed.
$issubscribed = \mod_moodleoverflow\subscriptions::is_subscribed($user->id, $moodleoverflow, $discussionid, $cm);

// To subscribe to a moodleoverflow or a discussion, the user needs to be logged in.
require_login($course, false, $cm);

// Guests, visitors and not enrolled people cannot subscribe.
$isenrolled = is_enrolled($context, $USER, '', true);
if (is_null($mode) AND !$isenrolled) {

    // Prepare the output.
    $PAGE->set_title($course->shortname);
    $PAGE->set_heading($course->fullname);

    // Redirect guest users to a login page.
    if (isguestuser()) {
        echo $OUTPUT->header();
        $message = $strings['subscribeenrolledonly'] . '<br /></ br>' . get_string('liketologin');
        $url     = new moodle_url('/mod/moodleoverflow/view.php', array('m' => $id));
        echo $OUTPUT->confirm($message, get_login_url(), $url);
        echo $OUTPUT->footer;
        exit;
    } else {
        // There should not be any links leading to this place. Just redirect.
        $url = new moodle_url('/mod/moodleoverflow/view.php', array('m' => $id));
        redirect($url, $strings['subscribeenrolledonly'], null, $notify['error']);
    }
}

// Create the url to redirect the user back to where he is coming from.
$urlindex = 'index.php?id=' . $course->id;
$urlview  = 'view.php?m=' . $id;
$returnto = optional_param('backtoindex', 0, PARAM_INT) ? $urlindex : $urlview;
if ($returnurl) {
    $returnto = $returnurl;
}

// Change the general subscription state.
if (!is_null($mode) AND has_capability('mod/moodleoverflow:managesubscriptions', $context)) {
    require_sesskey();

    // Set the new mode.
    switch ($mode) {

        // Everyone can choose what he wants.
        case MOODLEOVERFLOW_CHOOSESUBSCRIBE:
            \mod_moodleoverflow\subscriptions::set_subscription_mode($moodleoverflow->id, MOODLEOVERFLOW_CHOOSESUBSCRIBE);
            redirect($returnto, $strings['everyonecannowchoose'], null, $notify['success']);
            break;

        // Force users to be subscribed.
        case MOODLEOVERFLOW_FORCESUBSCRIBE:
            \mod_moodleoverflow\subscriptions::set_subscription_mode($moodleoverflow->id, MOODLEOVERFLOW_FORCESUBSCRIBE);
            redirect($strings['everyoneisnowsubscribed'], $string, null, $notify['success']);
            break;

        // Default setting.
        case MOODLEOVERFLOW_INITIALSUBSCRIBE:
            // If users are not forced, subscribe all users.
            if ($moodleoverflow->forcesubscribe <> MOODLEOVERFLOW_INITIALSUBSCRIBE) {
                $users = \mod_moodleoverflow\subscriptions::get_potential_subscribers($context, 0, 'u.id, u.email', '');
                foreach ($users as $user) {
                    \mod_moodleoverflow\subscriptions::subscribe_user($moodleoverflow->id, $moodleoverflow, $context);
                }
            }

            // Change the subscription state.
            \mod_moodleoverflow\subscriptions::set_subscription_mode($moodleoverflow->id, MOODLEOVERFLOW_INITIALSUBSCRIBE);

            // Redirect the user.
            $string = get_string('everyoneisnowsubscribed', 'moodleoverflow');
            redirect($returnto, $strings['everyoneisnowsubscribed'], null, $notify['success']);
            break;

        // Do not allow subscriptions.
        case MOODLEOVERFLOW_DISALLOWSUBSCRIBE:
            \mod_moodleoverflow\subscriptions::set_subscription_mode($moodleoverflow->id, MOODLEOVERFLOW_DISALLOWSUBSCRIBE);
            $string = get_string('noonecansubscribenow', 'moodleoverflow');
            redirect($strings['noonecansubscribenow'], $string, null, $notify['success']);
            break;

        default:
            throw new moodle_exception($strings['invalidforcesubscribe']);
    }
}

// Redirect the user back if the user is forced to be subscribed.
$isforced = \mod_moodleoverflow\subscriptions::is_forcesubscribed($moodleoverflow);
if ($isforced) {
    redirect($returnto, $strings['everyoneisnowsubscribed'], null, $notify['success']);
    exit;
}

// Create an info object.
$info                 = new stdClass();
$info->name           = fullname($user);
$info->moodleoverflow = format_string($moodleoverflow->name);

// Check if the user is subscribed to the moodleoverflow.
// The action is to unsubscribe the user.
if ($issubscribed) {

    // Check if there is a sesskey.
    if (is_null($sesskey)) {

        // Perpare the output.
        $PAGE->set_title($course->shortname);
        $PAGE->set_heading($course->fullname);
        echo $OUTPUT->header();

        // Create an url to get back to the view.
        $viewurl = new moodle_url('/mod/moodleoverflow/view.php', array('m' => $id));

        // Was a discussion id submitted?
        if ($discussionid) {

            // Create a new info object.
            $info2                 = new stdClass();
            $info2->moodleoverflow = format_string($moodleoverflow->name);
            $info2->discussion     = format_string($discussion->name);

            // Create a confirm statement.
            $string = get_string('confirmunsubscribediscussion', 'moodleoverflow', $info2);
            echo $OUTPUT->confirm($string, $PAGE->url, $viewurl);

        } else {
            // The discussion is not involved.

            // Create a confirm statement.
            $string = get_string('confirmunsubscribe', 'moodleoverflow', format_string($moodleoverflow->name));
            echo $OUTPUT->confirm($string, $PAGE->url, $viewurl);
        }

        // Print the rest of the page.
        echo $OUTPUT->footer();
        exit;
    }

    // From now on, a valid session key needs to be set.
    require_sesskey();

    // Check if a discussion id is submitted.
    if ($discussionid === null) {

        // Unsubscribe the user and redirect him back to where he is coming from.
        if (\mod_moodleoverflow\subscriptions::unsubscribe_user($user->id, $moodleoverflow, $context, true)) {
            redirect($returnto, get_string('nownotsubscribed', 'moodleoverflow', $info), null, $notify['success']);
        } else {
            throw new moodle_exception('cannotunsubscribe', 'moodleoverflow', get_local_referer(false));
        }

    } else {

        // Unsubscribe the user from the discussion.
        if (\mod_moodleoverflow\subscriptions::unsubscribe_user_from_discussion($user->id, $discussion, $context)) {
            $info->discussion = $discussion->name;
            redirect($returnto, get_string('discussionnownotsubscribed', 'moodleoverflow', $info), null, $notify['success']);
        } else {
            throw new moodle_exception('cannotunsubscribe', 'moodleoverflow', get_local_referer(false));
        }
    }

} else {
    // The user needs to be subscribed.

    // Check the capabilities.
    $capabilities                        = array();
    $capabilities['managesubscriptions'] = has_capability('mod/moodleoverflow:managesubscriptions', $context);
    $capabilities['viewdiscussion']      = has_capability('mod/moodleoverflow:viewdiscussion', $context);
    require_sesskey();

    // Check if subscriptionsare allowed.
    $disabled = \mod_moodleoverflow\subscriptions::subscription_disabled($moodleoverflow);
    if ($disabled AND !$capabilities['managesubscriptions']) {
        throw new moodle_exception('disallowsubscribe', 'moodleoverflow', get_local_referer(false));
    }

    // Check if the user can view discussions.
    if (!$capabilities['viewdiscussion']) {
        throw new moodle_exception('noviewdiscussionspermission', 'moodleoverflow', get_local_referer(false));
    }

    // Check the session key.
    if (is_null($sesskey)) {

        // Prepare the output.
        $PAGE->set_title($course->shortname);
        $PAGE->set_heading($course->fullname);
        echo $OUTPUT->header();

        // Create the url to redirect the user back to.
        $viewurl = new moodle_url('/mod/moodleoverflow/view.php', array('m' => $id));

        // Check whether a discussion is referenced.
        if ($discussionid) {

            // Create a new info object.
            $info2                 = new stdClass();
            $info2->moodleoverflow = format_string($moodleoverflow->name);
            $info2->discussion     = format_string($discussion->name);

            // Create a confirm dialog.
            $string = get_string('confirmsubscribediscussion', 'moodleoverflow', $info2);
            echo $OUTPUT->confirm($string, $PAGE->url, $viewurl);

        } else {
            // No discussion is referenced.

            // Create a confirm dialog.
            $string = get_string('confirmsubscribe', 'moodleoverflow', format_string($moodleoverflow->name));
            echo $OUTPUT->confirm($string, $PAGE->url, $viewurl);
        }

        // Print the missing part of the page.
        echo $OUTPUT->footer();
        exit;
    }

    // From now on, there needs to be a valid session key.
    require_sesskey();

    // Check if the subscription is refered to a discussion.
    if ($discussionid == null) {

        // Subscribe the user to the moodleoverflow instance.
        \mod_moodleoverflow\subscriptions::subscribe_user($user->id, $moodleoverflow, $context, true);
        redirect($returnto, get_string('nowsubscribed', 'moodleoverflow', $info), null, $notify['success']);
        exit;

    } else {
        $info->discussion = $discussion->name;

        // Subscribe the user to the discussion.
        \mod_moodleoverflow\subscriptions::subscribe_user_to_discussion($user->id, $discussion, $context);
        redirect($returnto, get_string('discussionnowsubscribed', 'moodleoverflow', $info), null, $notify['success']);
        exit;
    }
}
