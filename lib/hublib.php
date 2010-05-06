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
 *
 * Hub library
 *
 * @package   hub
 * @copyright 2010 Moodle Pty Ltd (http://moodle.com)
 * @author    Jerome Mouneyrac
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


///// IMAGE SIZES /////

/**
 * SITEIMAGEHEIGHT - the maximum height size of a site logo
 */
define('SITEIMAGEHEIGHT',   150);

/**
 * SITEIMAGEWIDTH - the maximum width size of a site logo
 */
define('SITEIMAGEWIDTH',   150);



///// MOODLE.ORG URLS //////

/**
 * Hub directory url (should be moodle.org)
 */
define('HUBDIRECTORYURL', "http://hubdirectory.moodle.org");


/**
 * Moodle.org url (should be moodle.org)
 */
define('MOODLEORGHUBURL', "http://hub.moodle.org");



//// SITE PRIVACY /////

/**
 * Site privacy: private
 */
define('SITENOTPUBLISHED', 'notdisplayed');

/**
 * Site privacy: public
 */
define('SITENAMEPUBLISHED', 'named');

/**
 * Site privacy: public and global
 */
define('SITELINKPUBLISHED', 'linked');




//// AUDIENCE ////

/**
 * Audience: educators
 */
define('AUDIENCE_EDUCATORS', 'educators');

/**
 * Audience: students
 */
define('AUDIENCE_STUDENTS', 'students');

/**
 * Audience: admins
 */
define('AUDIENCE_ADMINS', 'admins');



///// EDUCATIONAL LEVEL /////

/**
 * Educational level: primary
 */
define('EDULEVEL_PRIMARY', 'primary');

/**
 * Educational level: secondary
 */
define('EDULEVEL_SECONDARY', 'secondary');

/**
 * Educational level: tertiary
 */
define('EDULEVEL_TERTIARY', 'tertiary');

/**
 * Educational level: government
 */
define('EDULEVEL_GOVERNMENT', 'government');

/**
 * Educational level: association
 */
define('EDULEVEL_ASSOCIATION', 'association');

/**
 * Educational level: corporate
 */
define('EDULEVEL_CORPORATE', 'corporate');

/**
 * Educational level: other
 */
define('EDULEVEL_OTHER', 'other');



///// FILE TYPES /////

/**
 * FILE TYPE: SCREENSHOTS
 */
define('SCREENSHOT_FILE_TYPE', 'screenshot');

/**
 * FILE TYPE: BACKUP
 */
define('BACKUP_FILE_TYPE', 'backup');



class hub {

///////////////////////////
/// DB Facade functions  //
///////////////////////////

    public function add_registeredhub($hub) {
        global $DB;
        $id = $DB->insert_record('registration_hubs', $hub);
        return $id;
    }

    public function get_registeredhub($huburl = null, $token = null) {
        global $DB;

        $params = array();
        if (!empty($huburl)) {
            $params['huburl'] = $huburl;
        }
        if (!empty($token)) {
            $params['token'] = $token;
        }
        $params['confirmed'] = 1;
        $token = $DB->get_record('registration_hubs',$params);
        return $token;
    }

    public function get_unconfirmedhub($huburl) {
        global $DB;

        $params = array();
        $params['huburl'] = $huburl;
        $params['confirmed'] = 0;
        $token = $DB->get_record('registration_hubs',$params);
        return $token;
    }

    public function update_registeredhub($communication) {
        global $DB;
        $DB->update_record('registration_hubs', $communication);
    }


///////////////////////////
/// Library functions   ///
///////////////////////////



    /**
     * Return site information for a specific hub
     * @param string $huburl
     * @return array site info
     */
    public function get_site_info($huburl) {
        global $CFG, $DB;

        $siteinfo = array();
        $cleanhuburl = clean_param($huburl, PARAM_ALPHANUMEXT);
        $siteinfo['name'] = get_config('hub', 'site_name_'.$cleanhuburl);
        $siteinfo['description'] = get_config('hub', 'site_description_'.$cleanhuburl);
        $siteinfo['contactname'] = get_config('hub', 'site_contactname_'.$cleanhuburl);
        $siteinfo['contactemail'] = get_config('hub', 'site_contactemail_'.$cleanhuburl);
        $siteinfo['contactphone'] = get_config('hub', 'site_contactphone_'.$cleanhuburl);
        $siteinfo['imageurl'] = get_config('hub', 'site_imageurl_'.$cleanhuburl);
        $siteinfo['privacy'] = get_config('hub', 'site_privacy_'.$cleanhuburl);
        $siteinfo['street'] = get_config('hub', 'site_address_'.$cleanhuburl);
        $siteinfo['regioncode'] = get_config('hub', 'site_region_'.$cleanhuburl);
        $siteinfo['countrycode'] = get_config('hub', 'site_country_'.$cleanhuburl);
        $siteinfo['geolocation'] = get_config('hub', 'site_geolocation_'.$cleanhuburl);
        $siteinfo['contactable'] = get_config('hub', 'site_contactable_'.$cleanhuburl);
        $siteinfo['emailalert'] = get_config('hub', 'site_emailalert_'.$cleanhuburl);
        if (get_config('hub', 'site_coursesnumber_'.$cleanhuburl) == -1) {
            $coursecount = -1;
        } else {
            $coursecount = $DB->count_records('course')-1;
        }
        $siteinfo['courses'] = $coursecount;
        if (get_config('hub', 'site_usersnumber_'.$cleanhuburl) == -1) {
            $usercount = -1;
        } else {
            $usercount = $DB->count_records('user', array('deleted'=>0));
        }
        $siteinfo['users'] = $usercount;

        if (get_config('hub', 'site_roleassignmentsnumber_'.$cleanhuburl) == -1) {
            $roleassigncount = -1;
        } else {
            $roleassigncount = $DB->count_records('role_assignments');
        }
        $siteinfo['enrolments'] = $roleassigncount;
        if (get_config('hub', 'site_postsnumber_'.$cleanhuburl) == -1) {
            $postcount = -1;
        } else {
            $postcount = $DB->count_records('forum_posts');
        }
        $siteinfo['posts'] = $postcount;
        if (get_config('hub', 'site_questionsnumber_'.$cleanhuburl) == -1) {
            $questioncount = -1;
        } else {
            $questioncount = $DB->count_records('question');
        }
        $siteinfo['questions'] = $questioncount;
        if (get_config('hub', 'site_resourcesnumber_'.$cleanhuburl) == -1) {
            $resourcecount = -1;
        } else {
            $resourcecount = $DB->count_records('resource');
        }
        $siteinfo['resources'] = $resourcecount;
        //TODO
        require_once($CFG->dirroot."/course/lib.php");
        if (get_config('hub', 'site_participantnumberaverage_'.$cleanhuburl) == -1) {
            $participantnumberaverage = -1;
        } else {
            $participantnumberaverage = average_number_of_participants();
        }
        $siteinfo['participantnumberaverage'] = $participantnumberaverage;
        if (get_config('hub', 'site_modulenumberaverage_'.$cleanhuburl) == -1) {
            $modulenumberaverage = -1;
        } else {
            $modulenumberaverage = average_number_of_courses_modules();
        }
        $siteinfo['modulenumberaverage'] = $modulenumberaverage;
        $siteinfo['language'] = current_language();
        $siteinfo['moodleversion'] = $CFG->version;
        $siteinfo['moodlerelease'] = $CFG->release;
        $siteinfo['url'] = $CFG->wwwroot;

        return $siteinfo;
    }


    /**
     * Retrieve the site privacy string matching the define value
     * @param string $privacy must match the define into moodlelib.php
     * @return string
     */
    public function get_site_privacy_string($privacy) {
        switch ($privacy) {
            case SITENOTPUBLISHED:
                $privacystring = get_string('publicdirectory0');
                break;
            case SITENAMEPUBLISHED:
                $privacystring = get_string('publicdirectory1');
                break;
            case SITELINKPUBLISHED:
                $privacystring = get_string('publicdirectory2');
                break;
        }
        if (empty($privacystring)) {
            throw new moodle_exception('unknownprivacy');
        }
        return $privacystring;
    }



    /**
     * Return all hubs where the site is registered on
     */
    public function get_registered_on_hubs() {
        global $DB;
        $hubs = $DB->get_records('registration_hubs', array());
        return $hubs;
    }

}