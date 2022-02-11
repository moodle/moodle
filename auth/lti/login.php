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
 * Page allowing a platform user, identified by their {iss, sub} tuple, to be bound to a new or existing Moodle account.
 *
 * This is an LTI Advantage specific login feature.
 *
 * The auth flow defined in auth_lti\auth::complete_login() redirects here when a launching user does not have an
 * account binding yet. This page prompts the user to select between:
 * a) An auto provisioned account.
 * An account with auth type 'lti' is created for the user. This account is bound to the launch credentials.
 * Or
 * b) Use an existing account
 * The standard Moodle auth flow is leveraged to get an existing user account. This account is then bound to the launch
 * credentials.
 *
 * @package    auth_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\event\user_login_failed;
use core\output\notification;

require_once(__DIR__ . '/../../config.php');

global $OUTPUT, $PAGE, $SESSION;

// Form fields dealing with the user's choice about account types (new, existing).
$newaccount = optional_param('new_account', false, PARAM_BOOL);
$existingaccount = optional_param('existing_account', false, PARAM_BOOL);

if (empty($SESSION->auth_lti) || empty($SESSION->auth_lti->launchdata)) {
    throw new coding_exception('Missing LTI launch credentials.');
}
if (empty($SESSION->auth_lti->returnurl)) {
    throw new coding_exception('Missing return URL.');
}

if ($newaccount) {
    require_sesskey();
    $launchdata = $SESSION->auth_lti->launchdata;
    $returnurl = $SESSION->auth_lti->returnurl;
    unset($SESSION->auth_lti);

    if (!empty($CFG->authpreventaccountcreation)) {
        // If 'authpreventaccountcreation' is enabled, the option to create a new account isn't presented to users in the form.
        // This just ensures no action is taken were the 'newaccount' value to be present in the submitted data.

        // Trigger login failed event.
        $failurereason = AUTH_LOGIN_UNAUTHORISED;
        $event = user_login_failed::create(['other' => ['reason' => $failurereason]]);
        $event->trigger();

        // Site settings prevent creating new accounts.
        $errormsg = get_string('cannotcreateaccounts', 'auth_lti');
        $SESSION->loginerrormsg = $errormsg;
        redirect(new moodle_url('/login/index.php'));
    } else {
        // Create a new account and link it, logging the user in.
        $auth = get_auth_plugin('lti');
        $newuser = $auth->find_or_create_user_from_launch($launchdata, true);
        complete_user_login($newuser);

        $PAGE->set_context(context_system::instance());
        $PAGE->set_url(new moodle_url('/auth/lti/login.php'));
        $PAGE->set_pagelayout('popup');
        $renderer = $PAGE->get_renderer('auth_lti');
        echo $OUTPUT->header();
        echo $renderer->render_account_binding_complete(
            new notification(get_string('accountcreatedsuccess', 'auth_lti'), notification::NOTIFY_SUCCESS, false),
            $returnurl
        );
        echo $OUTPUT->footer();
        exit();
    }
} else if ($existingaccount) {
    // Only when authenticated can an account be bound, allowing the user to continue to the original launch action.
    require_login(null, false);
    require_sesskey();
    $launchdata = $SESSION->auth_lti->launchdata;
    $returnurl = $SESSION->auth_lti->returnurl;
    unset($SESSION->auth_lti);

    global $USER;
    $auth = get_auth_plugin('lti');
    $auth->create_user_binding($launchdata['iss'], $launchdata['sub'], $USER->id);

    $PAGE->set_context(context_system::instance());
    $PAGE->set_url(new moodle_url('/auth/lti/login.php'));
    $PAGE->set_pagelayout('popup');
    $renderer = $PAGE->get_renderer('auth_lti');
    echo $OUTPUT->header();
    echo $renderer->render_account_binding_complete(
        new notification(get_string('accountlinkedsuccess', 'auth_lti'), notification::NOTIFY_SUCCESS, false),
        $returnurl
    );
    echo $OUTPUT->footer();
    exit();
}

// Render the relevant account provisioning page, based on the provisioningmode set in the calling code.
$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/auth/lti/login.php'));
$PAGE->set_pagelayout('popup');
$renderer = $PAGE->get_renderer('auth_lti');

echo $OUTPUT->header();
require_once($CFG->dirroot . '/auth/lti/auth.php');
echo $renderer->render_account_binding_options_page($SESSION->auth_lti->provisioningmode);
echo $OUTPUT->footer();
