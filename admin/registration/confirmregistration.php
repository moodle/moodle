<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This file is part of Moodle - http://moodle.org/                      //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//                                                                       //
// Moodle is free software: you can redistribute it and/or modify        //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation, either version 3 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// Moodle is distributed in the hope that it will be useful,             //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details.                          //
//                                                                       //
// You should have received a copy of the GNU General Public License     //
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * @package    moodle
 * @subpackage registration
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * The administrator is redirect to this page from the hub to confirm that the
 * site has been registered. It is an administration page. The administrator
 * should be using the same browser during all the registration process.
 * This page save the token that the hub gave us, in order to call the hub
 * directory later by web service.
 */

require('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$newtoken = optional_param('newtoken', '', PARAM_ALPHANUM);
$url = optional_param('url', '', PARAM_URL);
$hubname = optional_param('hubname', '', PARAM_TEXT);
$token = optional_param('token', '', PARAM_TEXT);
$error = optional_param('error', '', PARAM_ALPHANUM);

admin_externalpage_setup('registrationmoodleorg');

if ($url !== HUB_MOODLEORGHUBURL) {
    // Allow other plugins to confirm registration on hubs other than moodle.net . Plugins implementing this
    // callback need to redirect or exit. See https://docs.moodle.org/en/Hub_registration .
    $callbacks = get_plugins_with_function('hub_registration');
    foreach ($callbacks as $plugintype => $plugins) {
        foreach ($plugins as $plugin => $callback) {
            $callback('confirm');
        }
    }
    throw new moodle_exception('errorotherhubsnotsupported', 'hub');
}

if (!empty($error) and $error == 'urlalreadyexist') {
    throw new moodle_exception('urlalreadyregistered', 'hub',
            $CFG->wwwroot . '/' . $CFG->admin . '/registration/index.php');
}

//check that we are waiting a confirmation from this hub, and check that the token is correct
core\hub\registration::confirm_registration($token, $newtoken, $hubname);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('registrationconfirmed', 'hub'), 3, 'main');

// Display notification message.
echo $OUTPUT->notification(get_string('registrationconfirmedon', 'hub'), 'notifysuccess');

// Display continue button.
$returnurl = !empty($SESSION->registrationredirect) ? clean_param($SESSION->registrationredirect, PARAM_LOCALURL) : null;
unset($SESSION->registrationredirect);
$continueurl = new moodle_url($returnurl ?: '/admin/registration/index.php');
$continuebutton = $OUTPUT->render(new single_button($continueurl, get_string('continue')));
$continuebutton = html_writer::tag('div', $continuebutton, array('class' => 'mdl-align'));
echo $continuebutton;

echo $OUTPUT->footer();


