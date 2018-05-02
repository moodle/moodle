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

/*
 * @package    moodle
 * @subpackage registration
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This page displays the site registration form for Moodle.org/MOOCH or for a different hub.
 * It handles redirection to the hub to continue the registration workflow process.
 * It also handles update operation by web service.
 */


require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/registration/forms.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/registration/lib.php');

require_sesskey();

$huburl = required_param('huburl', PARAM_URL);
$huburl = rtrim($huburl, "/");

if ($huburl == HUB_MOODLEORGHUBURL) { // register to Moodle.org
    admin_externalpage_setup('registrationmoodleorg');
} else { //register to a hub
    admin_externalpage_setup('registrationhub');
}

$password = optional_param('password', '', PARAM_TEXT);
$hubname = optional_param('hubname', '', PARAM_TEXT);

$registrationmanager = new registration_manager();

$registeredhub = $registrationmanager->get_registeredhub($huburl);

$siteregistrationform = new site_registration_form('',
                array('alreadyregistered' => !empty($registeredhub->token),
                    'huburl' => $huburl, 'hubname' => $hubname,
                    'password' => $password));
$fromform = $siteregistrationform->get_data();

if (!empty($fromform) and confirm_sesskey()) {

    // Set to -1 all optional data marked as "don't send" by the admin.
    // The function get_site_info() will not calculate the optional data if config is set to -1.
    $inputnames = array('courses', 'users', 'roleassignments', 'posts', 'questions', 'resources',
        'badges', 'issuedbadges', 'modulenumberaverage', 'participantnumberaverage');
    foreach ($inputnames as $inputname) {
        if (empty($fromform->{$inputname})) {
            $fromform->{$inputname} = -1;
        }
    }

    // Save the settings.
    $cleanhuburl = clean_param($huburl, PARAM_ALPHANUMEXT);
    set_config('site_name_' . $cleanhuburl, $fromform->name, 'hub');
    set_config('site_description_' . $cleanhuburl, $fromform->description, 'hub');
    set_config('site_contactname_' . $cleanhuburl, $fromform->contactname, 'hub');
    set_config('site_contactemail_' . $cleanhuburl, $fromform->contactemail, 'hub');
    set_config('site_contactphone_' . $cleanhuburl, $fromform->contactphone, 'hub');
    set_config('site_imageurl_' . $cleanhuburl, $fromform->imageurl, 'hub');
    set_config('site_privacy_' . $cleanhuburl, $fromform->privacy, 'hub');
    set_config('site_address_' . $cleanhuburl, $fromform->address, 'hub');
    set_config('site_region_' . $cleanhuburl, $fromform->regioncode, 'hub');
    set_config('site_country_' . $cleanhuburl, $fromform->countrycode, 'hub');
    set_config('site_language_' . $cleanhuburl, $fromform->language, 'hub');
    set_config('site_geolocation_' . $cleanhuburl, $fromform->geolocation, 'hub');
    set_config('site_contactable_' . $cleanhuburl, $fromform->contactable, 'hub');
    set_config('site_emailalert_' . $cleanhuburl, $fromform->emailalert, 'hub');
    set_config('site_coursesnumber_' . $cleanhuburl, $fromform->courses, 'hub');
    set_config('site_usersnumber_' . $cleanhuburl, $fromform->users, 'hub');
    set_config('site_roleassignmentsnumber_' . $cleanhuburl, $fromform->roleassignments, 'hub');
    set_config('site_postsnumber_' . $cleanhuburl, $fromform->posts, 'hub');
    set_config('site_questionsnumber_' . $cleanhuburl, $fromform->questions, 'hub');
    set_config('site_resourcesnumber_' . $cleanhuburl, $fromform->resources, 'hub');
    set_config('site_badges_' . $cleanhuburl, $fromform->badges, 'hub');
    set_config('site_issuedbadges_' . $cleanhuburl, $fromform->issuedbadges, 'hub');
    set_config('site_modulenumberaverage_' . $cleanhuburl, $fromform->modulenumberaverage, 'hub');
    set_config('site_participantnumberaverage_' . $cleanhuburl, $fromform->participantnumberaverage, 'hub');
}

/////// UPDATE ACTION ////////

// update the hub registration
$update = optional_param('update', 0, PARAM_INT);
if ($update and confirm_sesskey()) {

    //update the registration
    $function = 'hub_update_site_info';
    $siteinfo = $registrationmanager->get_site_info($huburl);
    $params = array('siteinfo' => $siteinfo);
    $serverurl = $huburl . "/local/hub/webservice/webservices.php";
    require_once($CFG->dirroot . "/webservice/xmlrpc/lib.php");
    $xmlrpcclient = new webservice_xmlrpc_client($serverurl, $registeredhub->token);
    try {
        $result = $xmlrpcclient->call($function, $params);
        $registrationmanager->update_registeredhub($registeredhub); // To update timemodified.
    } catch (Exception $e) {
        $error = $OUTPUT->notification(get_string('errorregistration', 'hub', $e->getMessage()));
    }
}

/////// FORM REGISTRATION ACTION //////

if (!empty($fromform) and empty($update) and confirm_sesskey()) {

    if (!empty($fromform) and confirm_sesskey()) { // if the register button has been clicked

        // Retrieve the optional info (specially course number, user number, module number average...).
        $siteinfo = $registrationmanager->get_site_info($huburl);
        $fromform->courses = $siteinfo['courses'];
        $fromform->users = $siteinfo['users'];
        $fromform->enrolments = $siteinfo['enrolments'];
        $fromform->posts = $siteinfo['posts'];
        $fromform->questions = $siteinfo['questions'];
        $fromform->resources = $siteinfo['resources'];
        $fromform->badges = $siteinfo['badges'];
        $fromform->issuedbadges = $siteinfo['issuedbadges'];
        $fromform->modulenumberaverage = $siteinfo['modulenumberaverage'];
        $fromform->participantnumberaverage = $siteinfo['participantnumberaverage'];
        $fromform->street = $siteinfo['street'];

        $params = (array) $fromform; //we are using the form input as the redirection parameters (token, url and name)

        $unconfirmedhub = $registrationmanager->get_unconfirmedhub($huburl);
        if (empty($unconfirmedhub)) {
            //we save the token into the communication table in order to have a reference
            $unconfirmedhub = new stdClass();
            $unconfirmedhub->token = $registrationmanager->get_site_secret_for_hub($huburl);
            $unconfirmedhub->secret = $unconfirmedhub->token;
            $unconfirmedhub->huburl = $huburl;
            $unconfirmedhub->hubname = $hubname;
            $unconfirmedhub->confirmed = 0;
            $unconfirmedhub->id = $registrationmanager->add_registeredhub($unconfirmedhub);
        }

        $params['token'] = $unconfirmedhub->token;
        $params['url'] = $CFG->wwwroot;
        redirect(new moodle_url($huburl . '/local/hub/siteregistration.php', $params));
    }
}

/////// OUTPUT SECTION /////////////

echo $OUTPUT->header();
//Display update notification result
if (!empty($registeredhub->confirmed)) {
    if (!empty($result)) {
        echo $OUTPUT->notification(get_string('siteregistrationupdated', 'hub'), 'notifysuccess');
    }
}

if (!empty($error)) {
    echo $error;
}

// Some Moodle.org registration explanation.
if ($huburl == HUB_MOODLEORGHUBURL) {
    $notificationtype = \core\output\notification::NOTIFY_ERROR;
    if (!empty($registeredhub->token)) {
        if ($registeredhub->timemodified == 0) {
            $registrationmessage = get_string('pleaserefreshregistrationunknown', 'admin');
        } else {
            $lastupdated = userdate($registeredhub->timemodified, get_string('strftimedate', 'langconfig'));
            $registrationmessage = get_string('pleaserefreshregistration', 'admin', $lastupdated);
            $notificationtype = \core\output\notification::NOTIFY_INFO;
        }
    } else {
        $registrationmessage = get_string('registrationwarning', 'admin');
    }
    echo $OUTPUT->notification($registrationmessage, $notificationtype);

    echo $OUTPUT->heading(get_string('registerwithmoodleorg', 'admin'));
    $renderer = $PAGE->get_renderer('core', 'admin');
    echo $renderer->moodleorg_registration_message();
}

$siteregistrationform->display();
echo $OUTPUT->footer();
