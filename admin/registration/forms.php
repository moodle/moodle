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


require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/lib/hublib.php');

/**
 * This form display a hub selector.
 * The hub list is retrieved from Moodle.org hub directory.
 * Also displayed, a text field to enter private hub url + its password
 */
class hub_selector_form extends moodleform {

    public function definition() {
        global $CFG;
        $mform =& $this->_form;
        $mform->addElement('header', 'site', get_string('selecthub', 'hub'));

        //retrieve the hub list on the hub directory by web service
        $function = 'hubdirectory_get_hubs';
        $params = array();
        $serverurl = HUBDIRECTORYURL."/local/hubdirectory/webservice/webservices.php";
        require_once($CFG->dirroot."/webservice/xmlrpc/lib.php");
        $xmlrpcclient = new webservice_xmlrpc_client();
        $hubs = $xmlrpcclient->call($serverurl, 'publichubdirectory', $function, $params);

        //Public hub list
        $options = array();
        foreach ($hubs as $hub) {
            //to not display a name longer than 100 character (too big)
            if (strlen($hub['name'])>100) {
                $hubname = substr($hub['name'],0, 100);
                $hubname = $hubname."...";
            } else {
                $hubname = $hub['name'];
            }
            $options[$hub['url']] = $hubname;
            $mform->addElement('hidden', clean_param($hub['url'], PARAM_ALPHANUMEXT), $hubname);
        }
        $mform->addElement('select', 'publichub', get_string('publichub','hub'),
                $options, array("size" => 15));

        $mform->addElement('static','or' , '', get_string('orenterprivatehub', 'hub'));

        //Private hub
        $mform->addElement('text','unlistedurl' , get_string('privatehuburl', 'hub'));
        $mform->addElement('text','password' , get_string('password'));

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
        $mform =& $this->_form;
        $huburl = $this->_customdata['huburl'];
        $hubname = $this->_customdata['hubname'];
        $admin = get_admin();
        $site = get_site();

        //retrieve config for this hub and set default if they don't exist
        $cleanhuburl = clean_param($huburl, PARAM_ALPHANUMEXT);
        $sitename = get_config('hub', 'site_name_'.$cleanhuburl);
        if ($sitename === false) {
            $sitename = $site->fullname;
        }
        $sitedescription = get_config('hub', 'site_description_'.$cleanhuburl);
        if ($sitedescription === false) {
            $sitedescription = $site->summary;
        }
        $contactname = get_config('hub', 'site_contactname_'.$cleanhuburl);
        if ($contactname === false) {
            $contactname = fullname($admin, true);
        }
        $contactemail = get_config('hub', 'site_contactemail_'.$cleanhuburl);
        if ($contactemail === false) {
            $contactemail = $admin->email;
        }
        $contactphone = get_config('hub', 'site_contactphone_'.$cleanhuburl);
        if ($contactphone === false) {
            $contactphone = $admin->phone1;
        }
        $imageurl = get_config('hub', 'site_imageurl_'.$cleanhuburl);
        $privacy = get_config('hub', 'site_privacy_'.$cleanhuburl);
        $address = get_config('hub', 'site_address_'.$cleanhuburl);
        $region = get_config('hub', 'site_region_'.$cleanhuburl);
        $country = get_config('hub', 'site_country_'.$cleanhuburl);
        if ($country === false) {
            $country =  $admin->country;
        }
        $geolocation = get_config('hub', 'site_geolocation_'.$cleanhuburl);
        $contactable = get_config('hub', 'site_contactable_'.$cleanhuburl);
        $emailalert = get_config('hub', 'site_emailalert_'.$cleanhuburl);
        $coursesnumber = get_config('hub', 'site_coursesnumber_'.$cleanhuburl);
        $usersnumber = get_config('hub', 'site_usersnumber_'.$cleanhuburl);
        $roleassignmentsnumber = get_config('hub', 'site_roleassignmentsnumber_'.$cleanhuburl);
        $postsnumber = get_config('hub', 'site_postsnumber_'.$cleanhuburl);
        $questionsnumber = get_config('hub', 'site_questionsnumber_'.$cleanhuburl);
        $resourcesnumber = get_config('hub', 'site_resourcesnumber_'.$cleanhuburl);
        $mediancoursesize = get_config('hub', 'site_mediancoursesize_'.$cleanhuburl);

        //hidden parameters
        $mform->addElement('hidden', 'huburl', $huburl);
        $mform->addElement('hidden', 'hubname', $hubname);

        //the input parameters
        $mform->addElement('header', 'moodle', get_string('registrationinfo', 'hub'));

        $mform->addElement('text','name' , get_string('fullsitename'));
        $mform->addRule('name', $strrequired, 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);
        $mform->setDefault('name', $sitename);

        $options = array();
        $hub = new hub();
        $options[SITENOTPUBLISHED] = $hub->get_site_privacy_string(SITENOTPUBLISHED);
        $options[SITENAMEPUBLISHED] = $hub->get_site_privacy_string(SITENAMEPUBLISHED);
        $options[SITELINKPUBLISHED] = $hub->get_site_privacy_string(SITELINKPUBLISHED);
        $mform->addElement('select', 'privacy', get_string('siteprivacy', 'hub'), $options);
        $mform->setDefault('privacy', $privacy);
        $mform->addHelpButton('privacy', 'privacy', 'hub');
        unset($options);

        $mform->addElement('textarea', 'description', get_string('description'), array('rows'=>10));
        $mform->addRule('description', $strrequired, 'required', null, 'client');
        $mform->setDefault('description', $sitedescription);
        $mform->setType('description', PARAM_TEXT);

        $mform->addElement('static', 'urlstring',get_string('siteurl', 'hub'), $CFG->wwwroot);

        $languages = get_string_manager()->get_list_of_languages();
        $mform->addElement('static', 'langstring',get_string('language'), $languages[current_language()]);
        $mform->addElement('hidden', 'language', current_language());

        $mform->addElement('static', 'versionstring',get_string('moodleversion'), $CFG->version);
        $mform->addElement('hidden', 'moodleversion', $CFG->version);

        $mform->addElement('static', 'releasestring',get_string('moodlerelease'), $CFG->release);
        $mform->addElement('hidden', 'moodlerelease', $CFG->release);

        $mform->addElement('textarea','address' , get_string('postaladdress', 'hub'));
        $mform->setType('address', PARAM_TEXT);
        $mform->setDefault('address', $address);

        //TODO: use the region array I generated
//        $mform->addElement('select', 'region', get_string('selectaregion'), array('-' => '-'));
//        $mform->setDefault('region', $region);
        $mform->addElement('hidden', 'regioncode', '-');

        $countries = get_string_manager()->get_list_of_countries();
        $mform->addElement('select', 'countrycode', get_string('selectacountry'), $countries);
        $mform->setDefault('countrycode', $country);

        $mform->addElement('text','geolocation' , get_string('geolocation'));
        $mform->setDefault('geolocation', $geolocation);
        $mform->addHelpButton('geolocation', 'geolocation', 'hub');

        $mform->addElement('text', 'contactname', get_string('administrator'));
        $mform->addRule('contactname', $strrequired, 'required', null, 'client');
        $mform->setType('contactname', PARAM_TEXT);
        $mform->setDefault('contactname', $contactname);

        $mform->addElement('text','contactphone' , get_string('phone'));
        $mform->setType('contactphone', PARAM_TEXT);
        $mform->addHelpButton('contactphone', 'contactphone', 'hub');

        $mform->addElement('text', 'contactemail', get_string('email'));
        $mform->addRule('contactemail', $strrequired, 'required', null, 'client');
        $mform->setType('contactemail', PARAM_TEXT);
        $mform->setDefault('contactemail', $contactemail);

        $options = array();
        $options[0] = get_string("registrationcontactno");
        $options[1] = get_string("registrationcontactyes");
        $mform->addElement('select', 'contactable', get_string('registrationcontact'), $options);
        $mform->setDefault('contactable', $contactable);
        $mform->addHelpButton('contactable', 'contactable', 'hub');
        unset($options);

        $options = array();
        $options[0] = get_string("registrationno");
        $options[1] = get_string("registrationyes");
        $mform->addElement('select', 'emailalert', get_string('registrationemail'), $options);
        $mform->setDefault('emailalert', $emailalert);
        $mform->addHelpButton('emailalert', 'emailalert', 'hub');
        unset($options);

//        $mform->addElement('text','imageurl' , get_string('logourl', 'hub'));
//        $mform->setType('imageurl', PARAM_URL);
//        $mform->setDefault('imageurl', $imageurl);
//        $mform->addHelpButton('imageurl', 'imageurl', 'hub');

        /// Display statistic that are going to be retrieve by the hub
        $coursecount = $DB->count_records('course')-1;
        $usercount = $DB->count_records('user', array('deleted'=>0));
        $roleassigncount = $DB->count_records('role_assignments');
        $postcount = $DB->count_records('forum_posts');
        $questioncount = $DB->count_records('question');
        $resourcecount = $DB->count_records('resource');
        require_once($CFG->dirroot."/course/lib.php");
        $participantnumberaverage= average_number_of_participants();
        $modulenumberaverage= average_number_of_courses_modules();

        if (MOODLEORGHUBURL != $huburl) {
            $mform->addElement('checkbox', 'courses',  get_string('sendfollowinginfo', 'hub'),
                    " ".get_string('coursesnumber', 'hub', $coursecount));
            $mform->setDefault('courses', true);

            $mform->addElement('checkbox', 'users', '',
                    " ".get_string('usersnumber', 'hub', $usercount));
            $mform->setDefault('users', true);

            $mform->addElement('checkbox', 'roleassignments', '',
                    " ".get_string('roleassignmentsnumber', 'hub', $roleassigncount));
            $mform->setDefault('roleassignments', true);

            $mform->addElement('checkbox', 'posts', '',
                    " ".get_string('postsnumber', 'hub', $postcount));
            $mform->setDefault('posts', true);

            $mform->addElement('checkbox', 'questions', '',
                    " ".get_string('questionsnumber', 'hub', $questioncount));
            $mform->setDefault('questions', true);

            $mform->addElement('checkbox', 'resources', '',
                    " ".get_string('resourcesnumber', 'hub', $resourcecount));
            $mform->setDefault('resources', true);

            $mform->addElement('checkbox', 'participantnumberaverage', '',
                    " ".get_string('participantnumberaverage', 'hub', $participantnumberaverage));
            $mform->setDefault('participantnumberaverage', true);

            $mform->addElement('checkbox', 'modulenumberaverage', '',
                    " ".get_string('modulenumberaverage', 'hub', $modulenumberaverage));
            $mform->setDefault('modulenumberaverage', true);
        } else {
            $mform->addElement('static', 'courseslabel',get_string('sendfollowinginfo', 'hub'),
                    " ".get_string('coursesnumber', 'hub', $coursecount));
            $mform->addElement('hidden', 'courses', true);

            $mform->addElement('static', 'userslabel', '',
                    " ".get_string('usersnumber', 'hub', $usercount));
            $mform->addElement('hidden', 'users', true);

            $mform->addElement('static', 'roleassignmentslabel', '',
                    " ".get_string('roleassignmentsnumber', 'hub', $roleassigncount));
            $mform->addElement('hidden', 'roleassignments', true);

            $mform->addElement('static', 'postslabel', '',
                    " ".get_string('postsnumber', 'hub', $postcount));
            $mform->addElement('hidden', 'posts', true);

            $mform->addElement('static', 'questionslabel', '',
                    " ".get_string('questionsnumber', 'hub', $questioncount));
            $mform->addElement('hidden', 'questions', true);

            $mform->addElement('static', 'resourceslabel', '',
                    " ".get_string('resourcesnumber', 'hub', $resourcecount));
            $mform->addElement('hidden', 'resources', true);

            $mform->addElement('static', 'participantnumberaveragelabel', '',
                    " ".get_string('participantnumberaverage', 'hub', $participantnumberaverage));
            $mform->addElement('hidden', 'participantnumberaverage', true);

            $mform->addElement('static', 'modulenumberaveragelabel', '',
                    " ".get_string('modulenumberaverage', 'hub', $modulenumberaverage));
            $mform->addElement('hidden', 'modulenumberaverage', true);
        }

        //check if it's a first registration or update
        $hubregistered = $hub->get_registeredhub($huburl);

        if (!empty($hubregistered)) {
            $buttonlabel = get_string('updatesite', 'hub',
                    !empty($hubname)?$hubname:$huburl);
            $mform->addElement('hidden', 'update', true);
        } else {
            $buttonlabel = get_string('registersite', 'hub',
                    !empty($hubname)?$hubname:$huburl);
        }

        $this->add_action_buttons(false, $buttonlabel);
    }

    /**
     * Check that the image size is correct
     */
    function validation($data, $files) {
        global $CFG;

        $errors = array();

        //check if the image (imageurl) has a correct size
        $imageurl = $this->_form->_submitValues['imageurl'];
        if (!empty($imageurl)) {
            list($imagewidth, $imageheight, $imagetype, $imageattr)  = getimagesize($imageurl); //getimagesize is a GD function
            if ($imagewidth > SITEIMAGEWIDTH or $imageheight > SITEIMAGEHEIGHT) {
                $sizestrings = new stdClass();
                $sizestrings->width = SITEIMAGEWIDTH;
                $sizestrings->height = SITEIMAGEHEIGHT;
                $errors['imageurl'] = get_string('errorbadimageheightwidth', 'hub', $sizestrings);
            }
        }

        return $errors;
    }

}

?>
