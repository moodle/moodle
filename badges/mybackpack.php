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

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->dirroot . '/badges/backpack_form.php');
require_once($CFG->dirroot . '/badges/lib/backpacklib.php');

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
$PAGE->set_pagelayout('mydashboard');

$backpack = $DB->get_record('badge_backpack', array('userid' => $USER->id));
$badgescache = cache::make('core', 'externalbadges');

if ($disconnect && $backpack) {
    require_sesskey();
    $DB->delete_records('badge_external', array('backpackid' => $backpack->id));
    $DB->delete_records('badge_backpack', array('userid' => $USER->id));
    $badgescache->delete($USER->id);
    redirect(new moodle_url('/badges/mybackpack.php'));
}

if ($backpack) {
    // If backpack is connected, need to select collections.
    $bp = new OpenBadgesBackpackHandler($backpack);
    $request = $bp->get_collections();
    if (empty($request->groups)) {
        $params['nogroups'] = get_string('error:nogroups', 'badges');
    } else {
        $params['groups'] = $request->groups;
    }
    $params['email'] = $backpack->email;
    $params['selected'] = $DB->get_fieldset_select('badge_external', 'collectionid', 'backpackid = :bid', array('bid' => $backpack->id));
    $params['backpackid'] = $backpack->id;
    $form = new edit_collections_form(new moodle_url('/badges/mybackpack.php'), $params);

    if ($form->is_cancelled()) {
        redirect(new moodle_url('/badges/mybadges.php'));
    } else if ($data = $form->get_data()) {
        if (empty($data->group)) {
            redirect(new moodle_url('/badges/mybadges.php'));
        } else {
            $groups = array_filter($data->group);
        }

        // Remove all unselected collections if there are any.
        $sqlparams = array('backpack' => $backpack->id);
        $select = 'backpackid = :backpack ';
        if (!empty($groups)) {
            list($grouptest, $groupparams) = $DB->get_in_or_equal($groups, SQL_PARAMS_NAMED, 'col', false);
            $select .= ' AND collectionid ' . $grouptest;
            $sqlparams = array_merge($sqlparams, $groupparams);
        }
        $DB->delete_records_select('badge_external', $select, $sqlparams);

        // Insert selected collections if they are not in database yet.
        foreach ($groups as $group) {
            $obj = new stdClass();
            $obj->backpackid = $data->backpackid;
            $obj->collectionid = (int) $group;
            if (!$DB->record_exists('badge_external', array('backpackid' => $obj->backpackid, 'collectionid' => $obj->collectionid))) {
                $DB->insert_record('badge_external', $obj);
            }
        }
        $badgescache->delete($USER->id);
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

    $form = new edit_backpack_form(new moodle_url('/badges/mybackpack.php'), $params);
    if ($form->is_cancelled()) {
        redirect(new moodle_url('/badges/mybadges.php'));
    } else if ($data = $form->get_data()) {
        // The form may have been submitted under one of the following circumstances:
        // 1. After clicking 'Connect to backpack'. We'll have $data->email.
        // 2. After clicking 'Resend verification email'. We'll have $data->email.
        // 3. After clicking 'Connect using a different email' to cancel the verification process. We'll have $data->revertbutton.
        if (isset($data->revertbutton)) {
            unset_user_preference('badges_email_verify_secret');
            unset_user_preference('badges_email_verify_address');
            redirect(new moodle_url('/badges/mybackpack.php'));
        } else if (isset($data->email)) {
            if (send_verification_email($data->email)) {
                redirect(new moodle_url('/badges/mybackpack.php'),
                    get_string('backpackemailverifypending', 'badges', $data->email),
                    null, \core\output\notification::NOTIFY_INFO);
            } else {
                print_error ('backpackcannotsendverification', 'badges');
            }
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($title);
$form->display();
echo $OUTPUT->footer();
