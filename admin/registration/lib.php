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




//// SITE PRIVACY /////

/**
 * Site privacy: private
 */
define('HUB_SITENOTPUBLISHED', 'notdisplayed');

/**
 * Site privacy: public
 */
define('HUB_SITENAMEPUBLISHED', 'named');

/**
 * Site privacy: public and global
 */
define('HUB_SITELINKPUBLISHED', 'linked');

/**
 *
 * Site registration library
 *
 * @package   course
 * @copyright 2010 Moodle Pty Ltd (http://moodle.com)
 * @author    Jerome Mouneyrac
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class registration_manager {

    /**
     * Automatically update the registration on all hubs
     */
    public function cron() {
        global $CFG;
        if (extension_loaded('xmlrpc')) {
            //check if the last registration cron update was less than a week ago
            $lastcron = get_config('registration', 'crontime');
            if ($lastcron === false or $lastcron < strtotime("-7 day")) { //set to a week, see MDL-23704
                $function = 'hub_update_site_info';
                require_once($CFG->dirroot . "/webservice/xmlrpc/lib.php");

                //update all hub where the site is registered on
                $hubs = $this->get_registered_on_hubs();
                foreach ($hubs as $hub) {
                    //update the registration
                    $siteinfo = $this->get_site_info($hub->huburl);
                    $params = array('siteinfo' => $siteinfo);
                    $serverurl = $hub->huburl . "/local/hub/webservice/webservices.php";
                    $xmlrpcclient = new webservice_xmlrpc_client($serverurl, $hub->token);
                    try {
                        $result = $xmlrpcclient->call($function, $params);
                        mtrace(get_string('siteupdatedcron', 'hub', $hub->hubname));
                    } catch (Exception $e) {
                        $errorparam = new stdClass();
                        $errorparam->errormessage = $e->getMessage();
                        $errorparam->hubname = $hub->hubname;
                        mtrace(get_string('errorcron', 'hub', $errorparam));
                    }
                }
                set_config('crontime', time(), 'registration');
            }
        } else {
            mtrace(get_string('errorcronnoxmlrpc', 'hub'));
        }
    }

    /**
     * Return the site secret for a given hub
     * site identifier is assigned to Mooch
     * each hub has a unique and personal site secret.
     * @param string $huburl
     * @return string site secret
     */
    public function get_site_secret_for_hub($huburl) {
        global $DB;

        $existingregistration = $DB->get_record('registration_hubs',
                    array('huburl' => $huburl));

        if (!empty($existingregistration)) {
            return $existingregistration->secret;
        }

        if ($huburl == HUB_MOODLEORGHUBURL) {
            $siteidentifier =  get_site_identifier();
        } else {
            $siteidentifier = random_string(32) . $_SERVER['HTTP_HOST'];
        }

        return $siteidentifier;

    }

    /**
     * When the site register on a hub, he must call this function
     * @param object $hub where the site is registered on
     * @return integer id of the record
     */
    public function add_registeredhub($hub) {
        global $DB;
        $id = $DB->insert_record('registration_hubs', $hub);
        return $id;
    }

    /**
     * When a site unregister from a hub, he must call this function
     * @param string $huburl the huburl to delete
     */
    public function delete_registeredhub($huburl) {
        global $DB;
        $DB->delete_records('registration_hubs', array('huburl' => $huburl));
    }

    /**
     * Get a hub on which the site is registered for a given url or token
     * Mostly use to check if the site is registered on a specific hub
     * @param string $huburl
     * @param string $token
     * @return object the  hub
     */
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
        $token = $DB->get_record('registration_hubs', $params);
        return $token;
    }

    /**
     * Get the hub which has not confirmed that the site is registered on,
     * but for which a request has been sent
     * @param string $huburl
     * @return object the  hub
     */
    public function get_unconfirmedhub($huburl) {
        global $DB;

        $params = array();
        $params['huburl'] = $huburl;
        $params['confirmed'] = 0;
        $token = $DB->get_record('registration_hubs', $params);
        return $token;
    }

    /**
     * Update a registered hub (mostly use to update the confirmation status)
     * @param object $communication the hub
     */
    public function update_registeredhub($communication) {
        global $DB;
        $DB->update_record('registration_hubs', $communication);
    }

    /**
     * Return all hubs where the site is registered on
     */
    public function get_registered_on_hubs() {
        global $DB;
        $hubs = $DB->get_records('registration_hubs', array('confirmed' => 1));
        return $hubs;
    }

    /**
     * Return site information for a specific hub
     * @param string $huburl
     * @return array site info
     */
    public function get_site_info($huburl) {
        global $CFG, $DB;

        $siteinfo = array();
        $cleanhuburl = clean_param($huburl, PARAM_ALPHANUMEXT);
        $siteinfo['name'] = get_config('hub', 'site_name_' . $cleanhuburl);
        $siteinfo['description'] = get_config('hub', 'site_description_' . $cleanhuburl);
        $siteinfo['contactname'] = get_config('hub', 'site_contactname_' . $cleanhuburl);
        $siteinfo['contactemail'] = get_config('hub', 'site_contactemail_' . $cleanhuburl);
        $siteinfo['contactphone'] = get_config('hub', 'site_contactphone_' . $cleanhuburl);
        $siteinfo['imageurl'] = get_config('hub', 'site_imageurl_' . $cleanhuburl);
        $siteinfo['privacy'] = get_config('hub', 'site_privacy_' . $cleanhuburl);
        $siteinfo['street'] = get_config('hub', 'site_address_' . $cleanhuburl);
        $siteinfo['regioncode'] = get_config('hub', 'site_region_' . $cleanhuburl);
        $siteinfo['countrycode'] = get_config('hub', 'site_country_' . $cleanhuburl);
        $siteinfo['geolocation'] = get_config('hub', 'site_geolocation_' . $cleanhuburl);
        $siteinfo['contactable'] = get_config('hub', 'site_contactable_' . $cleanhuburl);
        $siteinfo['emailalert'] = get_config('hub', 'site_emailalert_' . $cleanhuburl);
        if (get_config('hub', 'site_coursesnumber_' . $cleanhuburl) == -1) {
            $coursecount = -1;
        } else {
            $coursecount = $DB->count_records('course') - 1;
        }
        $siteinfo['courses'] = $coursecount;
        if (get_config('hub', 'site_usersnumber_' . $cleanhuburl) == -1) {
            $usercount = -1;
        } else {
            $usercount = $DB->count_records('user', array('deleted' => 0));
        }
        $siteinfo['users'] = $usercount;

        if (get_config('hub', 'site_roleassignmentsnumber_' . $cleanhuburl) == -1) {
            $roleassigncount = -1;
        } else {
            $roleassigncount = $DB->count_records('role_assignments');
        }
        $siteinfo['enrolments'] = $roleassigncount;
        if (get_config('hub', 'site_postsnumber_' . $cleanhuburl) == -1) {
            $postcount = -1;
        } else {
            $postcount = $DB->count_records('forum_posts');
        }
        $siteinfo['posts'] = $postcount;
        if (get_config('hub', 'site_questionsnumber_' . $cleanhuburl) == -1) {
            $questioncount = -1;
        } else {
            $questioncount = $DB->count_records('question');
        }
        $siteinfo['questions'] = $questioncount;
        if (get_config('hub', 'site_resourcesnumber_' . $cleanhuburl) == -1) {
            $resourcecount = -1;
        } else {
            $resourcecount = $DB->count_records('resource');
        }
        $siteinfo['resources'] = $resourcecount;
        // Badge statistics.
        require_once($CFG->libdir . '/badgeslib.php');
        if (get_config('hub', 'site_badges_' . $cleanhuburl) == -1) {
            $badges = -1;
        } else {
            $badges = $DB->count_records_select('badge', 'status <> ' . BADGE_STATUS_ARCHIVED);
        }
        $siteinfo['badges'] = $badges;
        if (get_config('hub', 'site_issuedbadges_' . $cleanhuburl) == -1) {
            $issuedbadges = -1;
        } else {
            $issuedbadges = $DB->count_records('badge_issued');
        }
        $siteinfo['issuedbadges'] = $issuedbadges;
        //TODO
        require_once($CFG->dirroot . "/course/lib.php");
        if (get_config('hub', 'site_participantnumberaverage_' . $cleanhuburl) == -1) {
            $participantnumberaverage = -1;
        } else {
            $participantnumberaverage = average_number_of_participants();
        }
        $siteinfo['participantnumberaverage'] = $participantnumberaverage;
        if (get_config('hub', 'site_modulenumberaverage_' . $cleanhuburl) == -1) {
            $modulenumberaverage = -1;
        } else {
            $modulenumberaverage = average_number_of_courses_modules();
        }
        $siteinfo['modulenumberaverage'] = $modulenumberaverage;
        $siteinfo['language'] = get_config('hub', 'site_language_' . $cleanhuburl);
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
            case HUB_SITENOTPUBLISHED:
                $privacystring = get_string('siteprivacynotpublished', 'hub');
                break;
            case HUB_SITENAMEPUBLISHED:
                $privacystring = get_string('siteprivacypublished', 'hub');
                break;
            case HUB_SITELINKPUBLISHED:
                $privacystring = get_string('siteprivacylinked', 'hub');
                break;
        }
        if (empty($privacystring)) {
            throw new moodle_exception('unknownprivacy');
        }
        return $privacystring;
    }

}
?>
