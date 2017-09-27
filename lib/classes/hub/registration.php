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
 * Class registration
 *
 * @package    core
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\hub;
defined('MOODLE_INTERNAL') || die();

use moodle_exception;
use moodle_url;
use context_system;
use stdClass;

/**
 * Methods to use when publishing and searching courses on moodle.net
 *
 * @package    core
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class registration {

    /** @var Fields used in a site registration form */
    const FORM_FIELDS = ['name', 'description', 'contactname', 'contactemail', 'contactphone', 'imageurl', 'privacy', 'street',
        'regioncode', 'countrycode', 'geolocation', 'contactable', 'emailalert', 'language'];

    /** @var Site privacy: not displayed */
    const HUB_SITENOTPUBLISHED = 'notdisplayed';

    /** @var Site privacy: public */
    const HUB_SITENAMEPUBLISHED = 'named';

    /** @var Site privacy: public and global */
    const HUB_SITELINKPUBLISHED = 'linked';

    /** @var stdClass cached site registration information */
    protected static $registration = null;

    /**
     * Get site registration
     *
     * @param bool $confirmed
     * @return stdClass|null
     */
    protected static function get_registration($confirmed = true) {
        global $DB;

        if (self::$registration === null) {
            self::$registration = $DB->get_record('registration_hubs', ['huburl' => HUB_MOODLEORGHUBURL]);
        }

        if (self::$registration && (bool)self::$registration->confirmed == (bool)$confirmed) {
            return self::$registration;
        }

        return null;
    }

    /**
     * Same as get_registration except it throws exception if site not registered
     *
     * @return stdClass
     * @throws \moodle_exception
     */
    public static function require_registration() {
        if ($registration = self::get_registration()) {
            return $registration;
        }
        if (has_capability('moodle/site:config', context_system::instance())) {
            throw new moodle_exception('registrationwarning', 'admin', new moodle_url('/admin/registration/index.php'));
        } else {
            throw new moodle_exception('registrationwarningcontactadmin', 'admin');
        }
    }

    /**
     * Checks if site is registered
     *
     * @return bool
     */
    public static function is_registered() {
        return self::get_registration() ? true : false;
    }

    /**
     * Returns registration token
     *
     * @param int $strictness if set to MUST_EXIST and site is not registered will throw an exception
     * @return null
     * @throws moodle_exception
     */
    public static function get_token($strictness = IGNORE_MISSING) {
        if ($strictness == MUST_EXIST) {
            $registration = self::require_registration();
        } else if (!$registration = self::get_registration()) {
            return null;
        }
        return $registration->token;
    }

    /**
     * When was the registration last updated
     *
     * @return int|null timestamp or null if site is not registered
     */
    public static function get_last_updated() {
        if ($registration = self::get_registration()) {
            return $registration->timemodified;
        }
        return null;
    }

    /**
     * Calculates and prepares site information to send to moodle.net as part of registration or update
     *
     * @param array $defaults default values for inputs in the registration form (if site was never registered before)
     * @return array site info
     */
    public static function get_site_info($defaults = []) {
        global $CFG, $DB;
        require_once($CFG->libdir . '/badgeslib.php');
        require_once($CFG->dirroot . "/course/lib.php");

        $siteinfo = array();
        $cleanhuburl = clean_param(HUB_MOODLEORGHUBURL, PARAM_ALPHANUMEXT);
        foreach (self::FORM_FIELDS as $field) {
            $siteinfo[$field] = get_config('hub', 'site_'.$field.'_' . $cleanhuburl);
            if ($siteinfo[$field] === false && array_key_exists($field, $defaults)) {
                $siteinfo[$field] = $defaults[$field];
            }
        }

        // Statistical data.
        $siteinfo['courses'] = $DB->count_records('course') - 1;
        $siteinfo['users'] = $DB->count_records('user', array('deleted' => 0));
        $siteinfo['enrolments'] = $DB->count_records('role_assignments');
        $siteinfo['posts'] = $DB->count_records('forum_posts');
        $siteinfo['questions'] = $DB->count_records('question');
        $siteinfo['resources'] = $DB->count_records('resource');
        $siteinfo['badges'] = $DB->count_records_select('badge', 'status <> ' . BADGE_STATUS_ARCHIVED);
        $siteinfo['issuedbadges'] = $DB->count_records('badge_issued');
        $siteinfo['participantnumberaverage'] = average_number_of_participants();
        $siteinfo['modulenumberaverage'] = average_number_of_courses_modules();

        // Version and url.
        $siteinfo['moodleversion'] = $CFG->version;
        $siteinfo['moodlerelease'] = $CFG->release;
        $siteinfo['url'] = $CFG->wwwroot;

        // Mobile related information.
        $siteinfo['mobileservicesenabled'] = 0;
        $siteinfo['mobilenotificationsenabled'] = 0;
        $siteinfo['registereduserdevices'] = 0;
        $siteinfo['registeredactiveuserdevices'] = 0;
        if (!empty($CFG->enablewebservices) && !empty($CFG->enablemobilewebservice)) {
            $siteinfo['mobileservicesenabled'] = 1;
            $siteinfo['registereduserdevices'] = $DB->count_records('user_devices');
            $airnotifierextpath = $CFG->dirroot . '/message/output/airnotifier/externallib.php';
            if (file_exists($airnotifierextpath)) { // Maybe some one uninstalled the plugin.
                require_once($airnotifierextpath);
                $siteinfo['mobilenotificationsenabled'] = \message_airnotifier_external::is_system_configured();
                $siteinfo['registeredactiveuserdevices'] = $DB->count_records('message_airnotifier_devices', array('enable' => 1));
            }
        }

        return $siteinfo;
    }

    /**
     * Save registration info locally so it can be retrieved when registration needs to be updated
     *
     * @param stdClass $formdata data from {@link site_registration_form}
     */
    public static function save_site_info($formdata) {
        $cleanhuburl = clean_param(HUB_MOODLEORGHUBURL, PARAM_ALPHANUMEXT);
        foreach (self::FORM_FIELDS as $field) {
            set_config('site_' . $field . '_' . $cleanhuburl, $formdata->$field, 'hub');
        }
    }

    /**
     * Updates site registration when "Update reigstration" button is clicked by admin
     */
    public static function update_manual() {
        global $DB;

        if (!$registration = self::get_registration()) {
            return false;
        }

        $siteinfo = self::get_site_info();
        try {
            api::update_registration($siteinfo);
        } catch (moodle_exception $e) {
            \core\notification::add(get_string('errorregistrationupdate', 'hub', $e->getMessage()),
                \core\output\notification::NOTIFY_ERROR);
            return false;
        }
        $DB->update_record('registration_hubs', ['id' => $registration->id, 'timemodified' => time()]);
        \core\notification::add(get_string('siteregistrationupdated', 'hub'),
            \core\output\notification::NOTIFY_SUCCESS);
        self::$registration = null;
        return true;
    }

    /**
     * Updates site registration via cron
     *
     * @throws moodle_exception
     */
    public static function update_cron() {
        global $DB;

        if (!$registration = self::get_registration()) {
            mtrace(get_string('registrationwarning', 'admin'));
            return;
        }

        $siteinfo = self::get_site_info();
        api::update_registration($siteinfo);
        $DB->update_record('registration_hubs', ['id' => $registration->id, 'timemodified' => time()]);
        mtrace(get_string('siteregistrationupdated', 'hub'));
        self::$registration = null;
    }

    /**
     * Confirms registration by moodle.net
     *
     * @param string $token
     * @param string $newtoken
     * @param string $hubname
     * @throws moodle_exception
     */
    public static function confirm_registration($token, $newtoken, $hubname) {
        global $DB;

        $registration = self::get_registration(false);
        if (!$registration || $registration->token !== $token) {
            throw new moodle_exception('wrongtoken', 'hub', new moodle_url('/admin/registration/index.php'));
        }
        $record = ['id' => $registration->id];
        $record['token'] = $newtoken;
        $record['confirmed'] = 1;
        $record['hubname'] = $hubname;
        $record['timemodified'] = time();
        $DB->update_record('registration_hubs', $record);
        self::$registration = null;
    }

    /**
     * Retrieve the options for site privacy form element to use in registration form
     * @return array
     */
    public static function site_privacy_options() {
        return [
            self::HUB_SITENOTPUBLISHED => get_string('siteprivacynotpublished', 'hub'),
            self::HUB_SITENAMEPUBLISHED => get_string('siteprivacypublished', 'hub'),
            self::HUB_SITELINKPUBLISHED => get_string('siteprivacylinked', 'hub')
        ];
    }

    /**
     * Registers a site
     *
     * This method will make sure that unconfirmed registration record is created and then redirect to
     * registration script on https://moodle.net
     * Moodle.net will check that the site is accessible, register it and redirect back
     * to /admin/registration/confirmregistration.php
     *
     * @throws \coding_exception
     */
    public static function register() {
        global $DB;

        if (self::is_registered()) {
            // Caller of this method must make sure that site is not registered.
            throw new \coding_exception('Site already registered');
        }

        $hub = self::get_registration(false);
        if (empty($hub)) {
            // Create a new record in 'registration_hubs'.
            $hub = new stdClass();
            $hub->token = get_site_identifier();
            $hub->secret = $hub->token;
            $hub->huburl = HUB_MOODLEORGHUBURL;
            $hub->hubname = 'Moodle.net';
            $hub->confirmed = 0;
            $hub->timemodified = time();
            $hub->id = $DB->insert_record('registration_hubs', $hub);
            self::$registration = null;
        }

        $params = self::get_site_info();
        $params['token'] = $hub->token;

        redirect(new moodle_url(HUB_MOODLEORGHUBURL . '/local/hub/siteregistration.php', $params));
    }

    /**
     * Unregister site
     *
     * @param bool $unpublishalladvertisedcourses
     * @param bool $unpublishalluploadedcourses
     * @return bool
     */
    public static function unregister($unpublishalladvertisedcourses, $unpublishalluploadedcourses) {
        global $DB;

        if (!$hub = self::get_registration()) {
            return true;
        }

        // Unpublish the courses.
        try {
            publication::delete_all_publications($unpublishalladvertisedcourses, $unpublishalluploadedcourses);
        } catch (moodle_exception $e) {
            $errormessage = $e->getMessage();
            $errormessage .= \html_writer::empty_tag('br') .
                get_string('errorunpublishcourses', 'hub');

            \core\notification::add(get_string('unregistrationerror', 'hub', $errormessage),
                \core\output\notification::NOTIFY_ERROR);
            return false;
        }

        // Course unpublish went ok, unregister the site now.
        try {
            api::unregister_site();
        } catch (moodle_exception $e) {
            \core\notification::add(get_string('unregistrationerror', 'hub', $e->getMessage()),
                \core\output\notification::NOTIFY_ERROR);
            return false;
        }

        $DB->delete_records('registration_hubs', array('id' => $hub->id));
        self::$registration = null;
        return true;
    }

    /**
     * Generate a new token for the site that is not registered
     *
     * @param string $token
     * @throws moodle_exception
     */
    public static function reset_site_identifier($token) {
        global $DB, $CFG;

        $registration = self::get_registration(false);
        if (!$registration || $registration->token != $token) {
            throw new moodle_exception('wrongtoken', 'hub',
               new moodle_url('/admin/registration/index.php'));
        }

        $DB->delete_records('registration_hubs', array('id' => $registration->id));
        self::$registration = null;

        $CFG->siteidentifier = null;
        get_site_identifier();
    }

    /**
     * Returns information about moodle.net
     *
     * Example of the return array:
     * {
     *     "courses": 384,
     *     "description": "Moodle.net connects you with free content and courses shared by Moodle ...",
     *     "downloadablecourses": 190,
     *     "enrollablecourses": 194,
     *     "hublogo": 1,
     *     "language": "en",
     *     "name": "Moodle.net",
     *     "sites": 274175,
     *     "url": "https://moodle.net",
     *     "imgurl": moodle_url : "https://moodle.net/local/hub/webservice/download.php?filetype=hubscreenshot"
     * }
     *
     * @return array|null
     */
    public static function get_moodlenet_info() {
        try {
            return api::get_hub_info();
        } catch (moodle_exception $e) {
            // Ignore error, we only need it for displaying information about moodle.net, if this request
            // fails, it's not a big deal.
            return null;
        }
    }
}