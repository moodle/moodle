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
 * User backpack settings page.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$context = context_user::instance($USER->id);
require_capability('moodle/badges:manageownbadges', $context);

$disconnect = optional_param('disconnect', false, PARAM_BOOL);

if (empty($CFG->badges_allowexternalbackpack)) {
    redirect($CFG->wwwroot);
}

$PAGE->set_url(new moodle_url('/badges/mybackpack.php'));
$PAGE->set_context($context);

$title = get_string('backpackdetails', 'badges');
$PAGE->set_title($title);
$PAGE->set_heading(fullname($USER));
$PAGE->set_pagelayout('standard');

$backpack = $DB->get_record('badge_backpack', array('userid' => $USER->id));
$badgescache = cache::make('core', 'externalbadges');

if ($disconnect && $backpack) {
    require_sesskey();
    $sitebackpack = badges_get_site_backpack($backpack->externalbackpackid);
    if ($sitebackpack->apiversion == OPEN_BADGES_V2P1) {
        $bp = new \core_badges\backpack_api2p1($sitebackpack);
        $bp->disconnect_backpack($backpack);
        redirect(new moodle_url('/badges/mybackpack.php'), get_string('backpackdisconnected', 'badges'), null,
            \core\output\notification::NOTIFY_SUCCESS);
    } else {
        // If backpack is connected, need to select collections.
        $bp = new \core_badges\backpack_api($sitebackpack, $backpack);
        $bp->disconnect_backpack($USER->id, $backpack->id);
        redirect(new moodle_url('/badges/mybackpack.php'));
    }
}
$warning = '';
if ($backpack) {

    $sitebackpack = badges_get_site_backpack($backpack->externalbackpackid);

    // If backpack is connected, need to select collections.
    $bp = new \core_badges\backpack_api($sitebackpack, $backpack);
    $request = $bp->get_collections();
    $groups = $request;
    if (isset($request->groups)) {
        $groups = $request->groups;
    }
    if (empty($groups)) {
        $err = get_string('error:nogroupssummary', 'badges');
        $err .= get_string('error:nogroupslink', 'badges', $sitebackpack->backpackweburl);
        $params['nogroups'] = $err;
    } else {
        $params['groups'] = $groups;
    }
    $params['email'] = $backpack->email;
    $params['selected'] = $bp->get_collection_record($backpack->id);
    $params['backpackweburl'] = $sitebackpack->backpackweburl;
    $form = new \core_badges\form\collections(new moodle_url('/badges/mybackpack.php'), $params);

    if ($form->is_cancelled()) {
        redirect(new moodle_url('/badges/mybadges.php'));
    } else if ($data = $form->get_data()) {
        if (empty($data->group)) {
            redirect(new moodle_url('/badges/mybadges.php'));
        } else {
            $groups = array_filter($data->group);
        }
        $bp->set_backpack_collections($backpack->id, $groups);
        redirect(new moodle_url('/badges/mybadges.php'));
    }
} else {
    // If backpack is not connected, need to connect first.
    // To create a new connection to the backpack, first we need to verify the user's email address:
    // 1. User enters email and clicks 'Connect to backpack'.
    // 2. After cross-checking the email address against the backpack provider, an email is sent to the specified address,
    // and the email and secret are stored in user preferences. These will be cleared upon successful verification.
    // 3. User clicks verification link in the email to confirm the backpack connection.
    // 4. User redirected to the mybackpack page.
    // While the verification process is pending, the edit_backpack_form form will present the user with options to resend the
    // verification email, and to cancel the current verification attempt and start over.

    // To pass through the current state of the verification attempt to the form.
    $params['email'] = get_user_preferences('badges_email_verify_address');
    $params['backpackpassword'] = get_user_preferences('badges_email_verify_password');
    $params['backpackid'] = get_user_preferences('badges_email_verify_backpackid');

    $form = new \core_badges\form\backpack(new moodle_url('/badges/mybackpack.php'), $params);
    $data = $form->get_submitted_data();
    if ($form->is_cancelled()) {
        redirect(new moodle_url('/badges/mybadges.php'));
    } else if (badges_open_badges_backpack_api($data->externalbackpackid) == OPEN_BADGES_V2P1) {
        // If backpack is version 2.1 to redirect on the backpack site to login.
        // User input username/email/password on the backpack site
        // After confirm the scopes.
        redirect(new moodle_url('/badges/backpack-connect.php', ['backpackid' => $data->externalbackpackid]));
    } else if ($data = $form->get_data()) {
        // The form may have been submitted under one of the following circumstances:
        // 1. After clicking 'Connect to backpack'. We'll have $data->email.
        // 2. After clicking 'Resend verification email'. We'll have $data->email.
        // 3. After clicking 'Connect using a different email' to cancel the verification process. We'll have $data->revertbutton.

        if (isset($data->revertbutton)) {
            badges_disconnect_user_backpack($USER->id);
            redirect(new moodle_url('/badges/mybackpack.php'));
        } else if (isset($data->backpackemail)) {
            if (badges_send_verification_email($data->backpackemail, $data->externalbackpackid, $data->password)) {
                $a = get_user_preferences('badges_email_verify_backpackid');
                redirect(new moodle_url('/badges/mybackpack.php'),
                    get_string('backpackemailverifypending', 'badges', $data->backpackemail),
                    null, \core\output\notification::NOTIFY_INFO);
            } else {
                print_error ('backpackcannotsendverification', 'badges');
            }
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($title);
echo $warning;
$form->display();
echo $OUTPUT->footer();
