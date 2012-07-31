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
 * The forms needed by registration pages.
 */


require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/registration/lib.php');

/**
 * This form display a unregistration form.
 */
class site_unregistration_form extends moodleform {

    public function definition() {
        $mform = & $this->_form;
        $mform->addElement('header', 'site', get_string('unregister', 'hub'));

        $huburl = $this->_customdata['huburl'];
        $hubname = $this->_customdata['hubname'];

        $unregisterlabel = get_string('unregister', 'hub');
        $mform->addElement('checkbox', 'unpublishalladvertisedcourses', '',
                ' ' . get_string('unpublishalladvertisedcourses', 'hub'));
        $mform->addElement('checkbox', 'unpublishalluploadedcourses', '',
                ' ' . get_string('unpublishalluploadedcourses', 'hub'));

        $mform->addElement('hidden', 'confirm', 1);
        $mform->setType('confirm', PARAM_INT);
        $mform->addElement('hidden', 'unregistration', 1);
        $mform->setType('unregistration', PARAM_INT);
        $mform->addElement('hidden', 'huburl', $huburl);
        $mform->setType('huburl', PARAM_URL);
        $mform->addElement('hidden', 'hubname', $hubname);
        $mform->setType('hubname', PARAM_TEXT);

        $this->add_action_buttons(true, $unregisterlabel);
    }

}

/**
 * This form display a clean registration data form.
 */
class site_clean_registration_data_form extends moodleform {

    public function definition() {
        $mform = & $this->_form;
        $mform->addElement('header', 'site', get_string('unregister', 'hub'));

        $huburl = $this->_customdata['huburl'];
        $hubname = $this->_customdata['hubname'];


        $unregisterlabel = get_string('forceunregister', 'hub');
        $mform->addElement('static', '', get_string('warning', 'hub'), get_string('forceunregisterconfirmation', 'hub', $hubname));


        $mform->addElement('hidden', 'confirm', 1);
        $mform->setType('confirm', PARAM_INT);
        $mform->addElement('hidden', 'unregistration', 1);
        $mform->setType('unregistration', PARAM_INT);
        $mform->addElement('hidden', 'cleanregdata', 1);
        $mform->setType('cleanregdata', PARAM_INT);
        $mform->addElement('hidden', 'huburl', $huburl);
        $mform->setType('huburl', PARAM_URL);
        $mform->addElement('hidden', 'hubname', $hubname);
        $mform->setType('hubname', PARAM_TEXT);

        $this->add_action_buttons(true, $unregisterlabel);
    }

}

/**
 * This form display a hub selector.
 * The hub list is retrieved from Moodle.org hub directory.
 * Also displayed, a text field to enter private hub url + its password
 */
class hub_selector_form extends moodleform {

    public function definition() {
        global $CFG, $OUTPUT;
        $mform = & $this->_form;
        $mform->addElement('header', 'site', get_string('selecthub', 'hub'));

        //retrieve the hub list on the hub directory by web service
        $function = 'hubdirectory_get_hubs';
        $params = array();
        $serverurl = HUB_HUBDIRECTORYURL . "/local/hubdirectory/webservice/webservices.php";
        require_once($CFG->dirroot . "/webservice/xmlrpc/lib.php");
        $xmlrpcclient = new webservice_xmlrpc_client($serverurl, 'publichubdirectory');
        try {
            $hubs = $xmlrpcclient->call($function, $params);
        } catch (Exception $e) {
            $error = $OUTPUT->notification(get_string('errorhublisting', 'hub', $e->getMessage()));
            $mform->addElement('static', 'errorhub', '', $error);
            $hubs = array();
        }

        //remove moodle.org from the hub list
        foreach ($hubs as $key => $hub) {
            if ($hub['url'] == HUB_MOODLEORGHUBURL) {
                unset($hubs[$key]);
            }
        }

        //Public hub list
        $options = array();
        foreach ($hubs as $hub) {
            //to not display a name longer than 100 character (too big)
            if (textlib::strlen($hub['name']) > 100) {
                $hubname = textlib::substr($hub['name'], 0, 100);
                $hubname = $hubname . "...";
            } else {
                $hubname = $hub['name'];
            }
            $options[$hub['url']] = $hubname;
            $mform->addElement('hidden', clean_param($hub['url'], PARAM_ALPHANUMEXT), $hubname);
            $mform->setType(clean_param($hub['url'], PARAM_ALPHANUMEXT), PARAM_ALPHANUMEXT);
        }
        if (!empty($hubs)) {
            $mform->addElement('select', 'publichub', get_string('publichub', 'hub'),
                    $options, array("size" => 15));
        }

        $mform->addElement('static', 'or', '', get_string('orenterprivatehub', 'hub'));

        //Private hub
        $mform->addElement('text', 'unlistedurl', get_string('privatehuburl', 'hub'),
                array('class' => 'registration_textfield'));
        $mform->addElement('text', 'password', get_string('password'),
                array('class' => 'registration_textfield'));

        $this->add_action_buttons(false, get_string('selecthub', 'hub'));
    }

    /**
     * Check the unlisted URL is a URL
     */
    function validation($data, $files) {
        global $CFG;
        $errors = parent::validation($data, $files);

        $unlistedurl = $this->_form->_submitValues['unlistedurl'];

        if (!empty($unlistedurl)) {
            $unlistedurltotest = clean_param($unlistedurl, PARAM_URL);
            if (empty($unlistedurltotest)) {
                $errors['unlistedurl'] = get_string('badurlformat', 'hub');
            }
        }

        return $errors;
    }

}

/**
 * The site registration form. Information will be sent to a given hub.
 */
class site_registration_form extends moodleform {

    public function definition() {
        global $CFG, $DB;

        $strrequired = get_string('required');
        $mform = & $this->_form;
        $huburl = $this->_customdata['huburl'];
        $hubname = $this->_customdata['hubname'];
        $password = $this->_customdata['password'];
        $admin = get_admin();
        $site = get_site();

        //retrieve config for this hub and set default if they don't exist
        $cleanhuburl = clean_param($huburl, PARAM_ALPHANUMEXT);
        $sitename = get_config('hub', 'site_name_' . $cleanhuburl);
        if ($sitename === false) {
            $sitename = format_string($site->fullname, true, array('context' => context_course::instance(SITEID)));
        }
        $sitedescription = get_config('hub', 'site_description_' . $cleanhuburl);
        if ($sitedescription === false) {
            $sitedescription = $site->summary;
        }
        $contactname = get_config('hub', 'site_contactname_' . $cleanhuburl);
        if ($contactname === false) {
            $contactname = fullname($admin, true);
        }
        $contactemail = get_config('hub', 'site_contactemail_' . $cleanhuburl);
        if ($contactemail === false) {
            $contactemail = $admin->email;
        }
        $contactphone = get_config('hub', 'site_contactphone_' . $cleanhuburl);
        if ($contactphone === false) {
            $contactphone = $admin->phone1;
        }
        $imageurl = get_config('hub', 'site_imageurl_' . $cleanhuburl);
        $privacy = get_config('hub', 'site_privacy_' . $cleanhuburl);
        $address = get_config('hub', 'site_address_' . $cleanhuburl);
        $region = get_config('hub', 'site_region_' . $cleanhuburl);
        $country = get_config('hub', 'site_country_' . $cleanhuburl);
        if ($country === false) {
            $country = $admin->country;
        }
        $language = get_config('hub', 'site_language_' . $cleanhuburl);
        if ($language === false) {
            $language = current_language();
        }
        $geolocation = get_config('hub', 'site_geolocation_' . $cleanhuburl);
        $contactable = get_config('hub', 'site_contactable_' . $cleanhuburl);
        $emailalert = get_config('hub', 'site_emailalert_' . $cleanhuburl);
        $emailalert = ($emailalert === 0) ? 0 : 1;
        $coursesnumber = get_config('hub', 'site_coursesnumber_' . $cleanhuburl);
        $usersnumber = get_config('hub', 'site_usersnumber_' . $cleanhuburl);
        $roleassignmentsnumber = get_config('hub', 'site_roleassignmentsnumber_' . $cleanhuburl);
        $postsnumber = get_config('hub', 'site_postsnumber_' . $cleanhuburl);
        $questionsnumber = get_config('hub', 'site_questionsnumber_' . $cleanhuburl);
        $resourcesnumber = get_config('hub', 'site_resourcesnumber_' . $cleanhuburl);
        $mediancoursesize = get_config('hub', 'site_mediancoursesize_' . $cleanhuburl);

        //hidden parameters
        $mform->addElement('hidden', 'huburl', $huburl);
        $mform->setType('huburl', PARAM_URL);
        $mform->addElement('hidden', 'hubname', $hubname);
        $mform->setType('hubname', PARAM_TEXT);
        $mform->addElement('hidden', 'password', $password);
        $mform->setType('password', PARAM_RAW);

        //the input parameters
        $mform->addElement('header', 'moodle', get_string('registrationinfo', 'hub'));

        $mform->addElement('text', 'name', get_string('sitename', 'hub'),
                array('class' => 'registration_textfield'));
        $mform->addRule('name', $strrequired, 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);
        $mform->setDefault('name', $sitename);
        $mform->addHelpButton('name', 'sitename', 'hub');

        $options = array();
        $registrationmanager = new registration_manager();
        $options[HUB_SITENOTPUBLISHED] = $registrationmanager->get_site_privacy_string(HUB_SITENOTPUBLISHED);
        $options[HUB_SITENAMEPUBLISHED] = $registrationmanager->get_site_privacy_string(HUB_SITENAMEPUBLISHED);
        $options[HUB_SITELINKPUBLISHED] = $registrationmanager->get_site_privacy_string(HUB_SITELINKPUBLISHED);
        $mform->addElement('select', 'privacy', get_string('siteprivacy', 'hub'), $options);
        $mform->setDefault('privacy', $privacy);
        $mform->addHelpButton('privacy', 'privacy', 'hub');
        unset($options);

        $mform->addElement('textarea', 'description', get_string('sitedesc', 'hub'),
                array('rows' => 8, 'cols' => 41));
        $mform->addRule('description', $strrequired, 'required', null, 'client');
        $mform->setDefault('description', $sitedescription);
        $mform->setType('description', PARAM_TEXT);
        $mform->addHelpButton('description', 'sitedesc', 'hub');

        $languages = get_string_manager()->get_list_of_languages();
        collatorlib::asort($languages);
        $mform->addElement('select', 'language', get_string('sitelang', 'hub'),
                $languages);
        $mform->setType('language', PARAM_ALPHANUMEXT);
        $mform->addHelpButton('language', 'sitelang', 'hub');
        $mform->setDefault('language', $language);

        $mform->addElement('textarea', 'address', get_string('postaladdress', 'hub'),
                array('rows' => 4, 'cols' => 41));
        $mform->setType('address', PARAM_TEXT);
        $mform->setDefault('address', $address);
        $mform->addHelpButton('address', 'postaladdress', 'hub');

        //TODO: use the region array I generated
//        $mform->addElement('select', 'region', get_string('selectaregion'), array('-' => '-'));
//        $mform->setDefault('region', $region);
        $mform->addElement('hidden', 'regioncode', '-');
        $mform->setType('regioncode', PARAM_ALPHANUMEXT);

        $countries = get_string_manager()->get_list_of_countries();
        $mform->addElement('select', 'countrycode', get_string('sitecountry', 'hub'), $countries);
        $mform->setDefault('countrycode', $country);
        $mform->addHelpButton('countrycode', 'sitecountry', 'hub');

        $mform->addElement('text', 'geolocation', get_string('sitegeolocation', 'hub'),
                array('class' => 'registration_textfield'));
        $mform->setDefault('geolocation', $geolocation);
        $mform->addHelpButton('geolocation', 'sitegeolocation', 'hub');

        $mform->addElement('text', 'contactname', get_string('siteadmin', 'hub'),
                array('class' => 'registration_textfield'));
        $mform->addRule('contactname', $strrequired, 'required', null, 'client');
        $mform->setType('contactname', PARAM_TEXT);
        $mform->setDefault('contactname', $contactname);
        $mform->addHelpButton('contactname', 'siteadmin', 'hub');

        $mform->addElement('text', 'contactphone', get_string('sitephone', 'hub'),
                array('class' => 'registration_textfield'));
        $mform->setType('contactphone', PARAM_TEXT);
        $mform->addHelpButton('contactphone', 'sitephone', 'hub');

        $mform->addElement('text', 'contactemail', get_string('siteemail', 'hub'),
                array('class' => 'registration_textfield'));
        $mform->addRule('contactemail', $strrequired, 'required', null, 'client');
        $mform->setType('contactemail', PARAM_TEXT);
        $mform->setDefault('contactemail', $contactemail);
        $mform->addHelpButton('contactemail', 'siteemail', 'hub');

        $options = array();
        $options[0] = get_string("registrationcontactno");
        $options[1] = get_string("registrationcontactyes");
        $mform->addElement('select', 'contactable', get_string('siteregistrationcontact', 'hub'), $options);
        $mform->setDefault('contactable', $contactable);
        $mform->addHelpButton('contactable', 'siteregistrationcontact', 'hub');
        unset($options);

        $options = array();
        $options[0] = get_string("registrationno");
        $options[1] = get_string("registrationyes");
        $mform->addElement('select', 'emailalert', get_string('siteregistrationemail', 'hub'), $options);
        $mform->setDefault('emailalert', $emailalert);
        $mform->addHelpButton('emailalert', 'siteregistrationemail', 'hub');
        unset($options);

        //TODO site logo
        $mform->addElement('hidden', 'imageurl', ''); //TODO: temporary
        $mform->setType('imageurl', PARAM_URL);

        $mform->addElement('static', 'urlstring', get_string('siteurl', 'hub'), $CFG->wwwroot);
        $mform->addHelpButton('urlstring', 'siteurl', 'hub');

        $mform->addElement('static', 'versionstring', get_string('siteversion', 'hub'), $CFG->version);
        $mform->addElement('hidden', 'moodleversion', $CFG->version);
        $mform->setType('moodleversion', PARAM_INT);
        $mform->addHelpButton('versionstring', 'siteversion', 'hub');

        $mform->addElement('static', 'releasestring', get_string('siterelease', 'hub'), $CFG->release);
        $mform->addElement('hidden', 'moodlerelease', $CFG->release);
        $mform->setType('moodlerelease', PARAM_TEXT);
        $mform->addHelpButton('releasestring', 'siterelease', 'hub');

        /// Display statistic that are going to be retrieve by the hub
        $coursecount = $DB->count_records('course') - 1;
        $usercount = $DB->count_records('user', array('deleted' => 0));
        $roleassigncount = $DB->count_records('role_assignments');
        $postcount = $DB->count_records('forum_posts');
        $questioncount = $DB->count_records('question');
        $resourcecount = $DB->count_records('resource');
        require_once($CFG->dirroot . "/course/lib.php");
        $participantnumberaverage = number_format(average_number_of_participants(), 2);
        $modulenumberaverage = number_format(average_number_of_courses_modules(), 2);

        if (HUB_MOODLEORGHUBURL != $huburl) {
            $mform->addElement('checkbox', 'courses', get_string('sendfollowinginfo', 'hub'),
                    " " . get_string('coursesnumber', 'hub', $coursecount));
            $mform->setDefault('courses', true);
            $mform->addHelpButton('courses', 'sendfollowinginfo', 'hub');

            $mform->addElement('checkbox', 'users', '',
                    " " . get_string('usersnumber', 'hub', $usercount));
            $mform->setDefault('users', true);

            $mform->addElement('checkbox', 'roleassignments', '',
                    " " . get_string('roleassignmentsnumber', 'hub', $roleassigncount));
            $mform->setDefault('roleassignments', true);

            $mform->addElement('checkbox', 'posts', '',
                    " " . get_string('postsnumber', 'hub', $postcount));
            $mform->setDefault('posts', true);

            $mform->addElement('checkbox', 'questions', '',
                    " " . get_string('questionsnumber', 'hub', $questioncount));
            $mform->setDefault('questions', true);

            $mform->addElement('checkbox', 'resources', '',
                    " " . get_string('resourcesnumber', 'hub', $resourcecount));
            $mform->setDefault('resources', true);

            $mform->addElement('checkbox', 'participantnumberaverage', '',
                    " " . get_string('participantnumberaverage', 'hub', $participantnumberaverage));
            $mform->setDefault('participantnumberaverage', true);

            $mform->addElement('checkbox', 'modulenumberaverage', '',
                    " " . get_string('modulenumberaverage', 'hub', $modulenumberaverage));
            $mform->setDefault('modulenumberaverage', true);
        } else {
            $mform->addElement('static', 'courseslabel', get_string('sendfollowinginfo', 'hub'),
                    " " . get_string('coursesnumber', 'hub', $coursecount));
            $mform->addElement('hidden', 'courses', true);
            $mform->setType('courses', PARAM_FLOAT);
            $mform->addHelpButton('courseslabel', 'sendfollowinginfo', 'hub');

            $mform->addElement('static', 'userslabel', '',
                    " " . get_string('usersnumber', 'hub', $usercount));
            $mform->addElement('hidden', 'users', true);
            $mform->setType('users', PARAM_FLOAT);

            $mform->addElement('static', 'roleassignmentslabel', '',
                    " " . get_string('roleassignmentsnumber', 'hub', $roleassigncount));
            $mform->addElement('hidden', 'roleassignments', true);
            $mform->setType('roleassignments', PARAM_FLOAT);

            $mform->addElement('static', 'postslabel', '',
                    " " . get_string('postsnumber', 'hub', $postcount));
            $mform->addElement('hidden', 'posts', true);
            $mform->setType('posts', PARAM_FLOAT);

            $mform->addElement('static', 'questionslabel', '',
                    " " . get_string('questionsnumber', 'hub', $questioncount));
            $mform->addElement('hidden', 'questions', true);
            $mform->setType('questions', PARAM_FLOAT);

            $mform->addElement('static', 'resourceslabel', '',
                    " " . get_string('resourcesnumber', 'hub', $resourcecount));
            $mform->addElement('hidden', 'resources', true);
            $mform->setType('resources', PARAM_FLOAT);

            $mform->addElement('static', 'participantnumberaveragelabel', '',
                    " " . get_string('participantnumberaverage', 'hub', $participantnumberaverage));
            $mform->addElement('hidden', 'participantnumberaverage', true);
            $mform->setType('participantnumberaverage', PARAM_FLOAT);

            $mform->addElement('static', 'modulenumberaveragelabel', '',
                    " " . get_string('modulenumberaverage', 'hub', $modulenumberaverage));
            $mform->addElement('hidden', 'modulenumberaverage', true);
            $mform->setType('modulenumberaverage', PARAM_FLOAT);
        }

        //check if it's a first registration or update
        $hubregistered = $registrationmanager->get_registeredhub($huburl);

        if (!empty($hubregistered)) {
            $buttonlabel = get_string('updatesite', 'hub',
                            !empty($hubname) ? $hubname : $huburl);
            $mform->addElement('hidden', 'update', true);
            $mform->setType('update', PARAM_BOOL);
        } else {
            $buttonlabel = get_string('registersite', 'hub',
                            !empty($hubname) ? $hubname : $huburl);
        }

        $this->add_action_buttons(false, $buttonlabel);
    }

}

