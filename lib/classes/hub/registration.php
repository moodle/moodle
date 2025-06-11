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
use html_writer;
use core_plugin_manager;

/**
 * Methods to use when registering the site at the moodle sites directory.
 *
 * @package    core
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class registration {

    /** @var array Fields used in a site registration form.
     * IMPORTANT: any new fields with non-empty defaults have to be added to CONFIRM_NEW_FIELDS */
    const FORM_FIELDS = ['policyagreed', 'language', 'countrycode', 'privacy',
        'contactemail', 'emailalert', 'emailalertemail', 'commnews', 'commnewsemail',
        'contactname', 'name', 'description', 'imageurl', 'contactphone', 'regioncode',
        'geolocation', 'street', 'organisationtype'];

    /** @var array List of new FORM_FIELDS or siteinfo fields added indexed by the version when they were added.
     * If site was already registered, admin will be promted to confirm new registration data manually. Until registration is manually confirmed,
     * the scheduled task updating registration will be paused.
     * Keys of this array are not important as long as they increment, use current date to avoid confusions.
     */
    const CONFIRM_NEW_FIELDS = [
        2017092200 => [
            'commnews', // Receive communication news. This was added in 3.4 and is "On" by default. Admin must confirm or opt-out.
            'mobileservicesenabled', 'mobilenotificationsenabled', 'registereduserdevices', 'registeredactiveuserdevices' // Mobile stats added in 3.4.
        ],
        // Analytics stats added in Moodle 3.7.
        2019022200 => ['analyticsenabledmodels', 'analyticspredictions', 'analyticsactions', 'analyticsactionsnotuseful'],
        // Active users stats added in Moodle 3.9.
        2020022600 => ['activeusers', 'activeparticipantnumberaverage'],
        // Database type, course date info, site theme, primary auth type added in Moodle 4.2.
        2023021700 => ['dbtype', 'coursesnodates', 'sitetheme', 'primaryauthtype'],
        // Plugin usage added in Moodle 4.5.
        2023072300 => ['pluginusage'],
        // AI usage added in Moodle 4.5.
        2023081200 => ['aiusage'],
    ];

    /** @var string Site privacy: not displayed */
    const HUB_SITENOTPUBLISHED = 'notdisplayed';

    /** @var string Site privacy: public */
    const HUB_SITENAMEPUBLISHED = 'named';

    /** @var string Site privacy: public and global */
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
            self::$registration = $DB->get_record('registration_hubs', ['huburl' => HUB_MOODLEORGHUBURL]) ?: null;
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
     * Calculates and prepares site information to send to the sites directory as a part of registration.
     *
     * @param array $defaults default values for inputs in the registration form (if site was never registered before)
     * @return array site info
     */
    public static function get_site_info($defaults = []) {
        global $CFG, $DB;
        require_once($CFG->libdir . '/badgeslib.php');
        require_once($CFG->dirroot . "/course/lib.php");

        $siteinfo = array();
        foreach (self::FORM_FIELDS as $field) {
            $siteinfo[$field] = get_config('hub', 'site_'.$field);
            if ($siteinfo[$field] === false) {
                $siteinfo[$field] = array_key_exists($field, $defaults) ? $defaults[$field] : null;
            }
        }

        // Statistical data.
        $siteinfo['courses'] = $DB->count_records('course') - 1;
        $siteinfo['users'] = $DB->count_records('user', array('deleted' => 0));
        $siteinfo['activeusers'] = $DB->count_records_select('user', 'deleted = ? AND lastlogin > ?', [0, time() - DAYSECS * 30]);
        $siteinfo['enrolments'] = $DB->count_records('role_assignments');
        $siteinfo['posts'] = $DB->count_records('forum_posts');
        $siteinfo['questions'] = $DB->count_records('question');
        $siteinfo['resources'] = $DB->count_records('resource');
        $siteinfo['badges'] = $DB->count_records_select('badge', 'status <> ' . BADGE_STATUS_ARCHIVED);
        $siteinfo['issuedbadges'] = $DB->count_records('badge_issued');
        $siteinfo['participantnumberaverage'] = average_number_of_participants();
        $siteinfo['activeparticipantnumberaverage'] = average_number_of_participants(true, time() - DAYSECS * 30);
        $siteinfo['modulenumberaverage'] = average_number_of_courses_modules();
        $siteinfo['dbtype'] = $CFG->dbtype;
        $siteinfo['coursesnodates'] = $DB->count_records_select('course', 'enddate = ?', [0]) - 1;
        $siteinfo['sitetheme'] = get_config('core', 'theme');
        $siteinfo['pluginusage'] = json_encode(self::get_plugin_usage_data());

        // AI usage data.
        $aiusagedata = self::get_ai_usage_data();
        $siteinfo['aiusage'] = !empty($aiusagedata) ? json_encode($aiusagedata) : '';

        // Primary auth type.
        $primaryauthsql = 'SELECT auth, count(auth) as tc FROM {user} GROUP BY auth ORDER BY tc DESC';
        $siteinfo['primaryauthtype'] = $DB->get_field_sql($primaryauthsql, null, IGNORE_MULTIPLE);

        // Version and url.
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

        // Analytics related data follow.
        $siteinfo['analyticsenabledmodels'] = \core_analytics\stats::enabled_models();
        $siteinfo['analyticspredictions'] = \core_analytics\stats::predictions();
        $siteinfo['analyticsactions'] = \core_analytics\stats::actions();
        $siteinfo['analyticsactionsnotuseful'] = \core_analytics\stats::actions_not_useful();

        // IMPORTANT: any new fields in siteinfo have to be added to the constant CONFIRM_NEW_FIELDS.

        return $siteinfo;
    }

    /**
     * Human-readable summary of data that will be sent to the sites directory.
     *
     * @param array $siteinfo result of get_site_info()
     * @return string
     */
    public static function get_stats_summary($siteinfo) {
        $fieldsneedconfirm = self::get_new_registration_fields();
        $summary = html_writer::tag('p', get_string('sendfollowinginfo_help', 'hub')) .
            html_writer::start_tag('ul');

        $mobileservicesenabled = $siteinfo['mobileservicesenabled'] ? get_string('yes') : get_string('no');
        $mobilenotificationsenabled = $siteinfo['mobilenotificationsenabled'] ? get_string('yes') : get_string('no');
        $moodlerelease = $siteinfo['moodlerelease'];
        if (preg_match('/^(\d+\.\d.*?)[\. ]/', $moodlerelease, $matches)) {
            $moodlerelease = $matches[1];
        }
        $pluginusagelinks = [
            'overview' => new moodle_url('/admin/plugins.php'),
            'activities' => new moodle_url('/admin/modules.php'),
            'blocks' => new moodle_url('/admin/blocks.php'),
        ];
        $senddata = [
            'moodlerelease' => get_string('sitereleasenum', 'hub', $moodlerelease),
            'courses' => get_string('coursesnumber', 'hub', $siteinfo['courses']),
            'users' => get_string('usersnumber', 'hub', $siteinfo['users']),
            'activeusers' => get_string('activeusersnumber', 'hub', $siteinfo['activeusers']),
            'enrolments' => get_string('roleassignmentsnumber', 'hub', $siteinfo['enrolments']),
            'posts' => get_string('postsnumber', 'hub', $siteinfo['posts']),
            'questions' => get_string('questionsnumber', 'hub', $siteinfo['questions']),
            'resources' => get_string('resourcesnumber', 'hub', $siteinfo['resources']),
            'badges' => get_string('badgesnumber', 'hub', $siteinfo['badges']),
            'issuedbadges' => get_string('issuedbadgesnumber', 'hub', $siteinfo['issuedbadges']),
            'participantnumberaverage' => get_string('participantnumberaverage', 'hub',
                format_float($siteinfo['participantnumberaverage'], 2)),
            'activeparticipantnumberaverage' => get_string('activeparticipantnumberaverage', 'hub',
                format_float($siteinfo['activeparticipantnumberaverage'], 2)),
            'modulenumberaverage' => get_string('modulenumberaverage', 'hub',
                format_float($siteinfo['modulenumberaverage'], 2)),
            'mobileservicesenabled' => get_string('mobileservicesenabled', 'hub', $mobileservicesenabled),
            'mobilenotificationsenabled' => get_string('mobilenotificationsenabled', 'hub', $mobilenotificationsenabled),
            'registereduserdevices' => get_string('registereduserdevices', 'hub', $siteinfo['registereduserdevices']),
            'registeredactiveuserdevices' => get_string('registeredactiveuserdevices', 'hub', $siteinfo['registeredactiveuserdevices']),
            'analyticsenabledmodels' => get_string('analyticsenabledmodels', 'hub', $siteinfo['analyticsenabledmodels']),
            'analyticspredictions' => get_string('analyticspredictions', 'hub', $siteinfo['analyticspredictions']),
            'analyticsactions' => get_string('analyticsactions', 'hub', $siteinfo['analyticsactions']),
            'analyticsactionsnotuseful' => get_string('analyticsactionsnotuseful', 'hub', $siteinfo['analyticsactionsnotuseful']),
            'dbtype' => get_string('dbtype', 'hub', $siteinfo['dbtype']),
            'coursesnodates' => get_string('coursesnodates', 'hub', $siteinfo['coursesnodates']),
            'sitetheme' => get_string('sitetheme', 'hub', $siteinfo['sitetheme']),
            'primaryauthtype' => get_string('primaryauthtype', 'hub', $siteinfo['primaryauthtype']),
            'pluginusage' => get_string('pluginusagedata', 'hub', $pluginusagelinks),
            'aiusage' => get_string('aiusagestats', 'hub', self::get_ai_usage_time_range(true)),
        ];

        foreach ($senddata as $key => $str) {
            $class = in_array($key, $fieldsneedconfirm) ? ' needsconfirmation mark' : '';
            $summary .= html_writer::tag('li', $str, ['class' => 'site' . $key . $class]);
        }
        $summary .= html_writer::end_tag('ul');
        return $summary;
    }

    /**
     * Save registration info locally so it can be retrieved when registration needs to be updated
     *
     * @param stdClass $formdata data from {@link site_registration_form}
     */
    public static function save_site_info($formdata) {
        foreach (self::FORM_FIELDS as $field) {
            set_config('site_' . $field, $formdata->$field, 'hub');
        }
        // Even if the connection with the sites directory fails, admin has manually submitted the form which means they don't need
        // to be redirected to the site registration page any more.
        set_config('site_regupdateversion', max(array_keys(self::CONFIRM_NEW_FIELDS)), 'hub');
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
            if (!self::is_registered()) {
                // Token was rejected during registration update and site and locally stored token was reset,
                // proceed to site registration. This method will redirect away.
                self::register('');
            }
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

        if (self::get_new_registration_fields()) {
            mtrace(get_string('pleaserefreshregistrationnewdata', 'admin'));
            return;
        }

        $siteinfo = self::get_site_info();
        api::update_registration($siteinfo);
        $DB->update_record('registration_hubs', ['id' => $registration->id, 'timemodified' => time()]);
        mtrace(get_string('siteregistrationupdated', 'hub'));
        self::$registration = null;
    }

    /**
     * Confirms registration by the sites directory.
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

        $siteinfo = self::get_site_info();
        if (strlen(http_build_query($siteinfo)) > 1800) {
            // Update registration again because the initial request was too long and could have been truncated.
            api::update_registration($siteinfo);
            self::$registration = null;
        }

        // Finally, allow other plugins to perform actions once a site is registered for first time.
        $pluginsfunction = get_plugins_with_function('post_site_registration_confirmed');
        foreach ($pluginsfunction as $plugins) {
            foreach ($plugins as $pluginfunction) {
                $pluginfunction($registration->id);
            }
        }
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
     * Get the options for organisation type form element to use in registration form.
     *
     * Indexes reference Moodle internal ids and should not be changed.
     *
     * @return array
     */
    public static function get_site_organisation_type_options(): array {
        return [
            1 => get_string('siteorganisationtype:wholeuniversity', 'hub'),
            2 => get_string('siteorganisationtype:universitydepartment', 'hub'),
            3 => get_string('siteorganisationtype:college', 'hub'),
            4 => get_string('siteorganisationtype:collegedepartment', 'hub'),
            5 => get_string('siteorganisationtype:highschool', 'hub'),
            6 => get_string('siteorganisationtype:highschooldepartment', 'hub'),
            7 => get_string('siteorganisationtype:primaryschool', 'hub'),
            8 => get_string('siteorganisationtype:independentteacher', 'hub'),
            9 => get_string('siteorganisationtype:companyinternal', 'hub'),
            10 => get_string('siteorganisationtype:companydepartment', 'hub'),
            11 => get_string('siteorganisationtype:commercialcourseprovider', 'hub'),
            12 => get_string('siteorganisationtype:other', 'hub'),
            13 => get_string('siteorganisationtype:highschooldistrict', 'hub'),
            14 => get_string('siteorganisationtype:government', 'hub'),
            16 => get_string('siteorganisationtype:charityornotforprofit', 'hub'),
            17 => get_string('siteorganisationtype:charterschool', 'hub'),
            18 => get_string('siteorganisationtype:schooldistrict', 'hub'),
            19 => get_string('siteorganisationtype:hospital', 'hub'),
        ];
    }

    /**
     * Registers a site
     *
     * This method will make sure that unconfirmed registration record is created and then redirect to
     * registration script on the sites directory.
     * The sites directory will check that the site is accessible, register it and redirect back
     * to /admin/registration/confirmregistration.php
     *
     * @param string $returnurl
     * @throws \coding_exception
     */
    public static function register($returnurl) {
        global $DB, $SESSION;

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
            $hub->hubname = 'moodle';
            $hub->confirmed = 0;
            $hub->timemodified = time();
            $hub->id = $DB->insert_record('registration_hubs', $hub);
            self::$registration = null;
        }

        $params = self::get_site_info();

        // The most conservative limit for the redirect URL length is 2000 characters. Only pass parameters before
        // we reach this limit. The next registration update will update all fields.
        // We will also update registration after we receive confirmation from moodle.net.
        $url = new moodle_url(HUB_MOODLEORGHUBURL . '/local/hub/siteregistration.php',
            ['token' => $hub->token, 'url' => $params['url']]);
        foreach ($params as $key => $value) {
            if (strlen($url->out(false, [$key => $value])) > 2000) {
                break;
            }
            $url->param($key, $value);
        }

        $SESSION->registrationredirect = $returnurl;
        redirect($url);
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
     * Resets the registration token without changing site identifier so site can be re-registered
     *
     * @return bool
     */
    public static function reset_token() {
        global $DB;
        if (!$hub = self::get_registration()) {
            return true;
        }
        $DB->delete_records('registration_hubs', array('id' => $hub->id));
        self::$registration = null;
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
     * Returns information about the sites directory.
     *
     * Example of the return array:
     * {
     *     "courses": 384,
     *     "description": "Official moodle sites directory",
     *     "downloadablecourses": 0,
     *     "enrollablecourses": 0,
     *     "hublogo": 1,
     *     "language": "en",
     *     "name": "moodle",
     *     "sites": 274175,
     *     "url": "https://stats.moodle.org",
     *     "imgurl": "https://stats.moodle.org/local/hub/webservice/download.php?filetype=hubscreenshot"
     * }
     *
     * @return array|null
     */
    public static function get_moodlenet_info() {
        try {
            return api::get_hub_info();
        } catch (moodle_exception $e) {
            // Ignore error, we only need it for displaying information about the sites directory.
            // If this request fails, it's not a big deal.
            return null;
        }
    }

    /**
     * Does admin need to be redirected to the registration page after install?
     *
     * @param bool|null $markasviewed if set to true will mark the registration form as viewed and admin will not be redirected
     *     to the registration form again (regardless of whether the site was registered or not).
     * @return bool
     */
    public static function show_after_install($markasviewed = null) {
        global $CFG;
        if (self::is_registered()) {
            $showregistration = false;
            $markasviewed = true;
        } else {
            $showregistration = !empty($CFG->registrationpending);
            if ($showregistration && !site_is_public()) {
                // If it's not a public site, don't redirect to registration, it won't work anyway.
                $showregistration = false;
                $markasviewed = true;
            }
        }
        if ($markasviewed !== null) {
            set_config('registrationpending', !$markasviewed);
        }
        return $showregistration;
    }

    /**
     * Returns the list of the fields in the registration form that were added since registration or last manual update
     *
     * If this list is not empty the scheduled task will be paused and admin will be reminded to update registration manually.
     *
     * @return array
     */
    public static function get_new_registration_fields() {
        $fieldsneedconfirm = [];
        if (!self::is_registered()) {
            // Nothing to update if site is not registered.
            return $fieldsneedconfirm;
        }

        $lastupdated = (int)get_config('hub', 'site_regupdateversion');
        foreach (self::CONFIRM_NEW_FIELDS as $version => $fields) {
            if ($version > $lastupdated) {
                $fieldsneedconfirm = array_merge($fieldsneedconfirm, $fields);
            }
        }
        return $fieldsneedconfirm;
    }

    /**
     * Redirect to the site registration form if it's a new install, upgrade or registration needs updating.
     *
     * @param string|moodle_url $url
     */
    public static function registration_reminder($url) {
        if (defined('BEHAT_SITE_RUNNING') && BEHAT_SITE_RUNNING) {
            // No redirection during behat runs.
            return;
        }
        if (!has_capability('moodle/site:config', context_system::instance())) {
            return;
        }
        if (self::show_after_install() || self::get_new_registration_fields()) {
            $returnurl = new moodle_url($url);
            redirect(new moodle_url('/admin/registration/index.php', ['returnurl' => $returnurl->out_as_local_url(false)]));
        }
    }

    /**
     * Return a list of plugins.
     *
     * Only blocks and activities will include instance counts.
     *
     * @return array
     */
    public static function get_plugin_usage_data(): array {
        global $DB;

        $pluginman = core_plugin_manager::instance();
        $plugininfo = $pluginman->get_plugins();
        $data = [];

        foreach ($plugininfo as $plugins) {
            foreach ($plugins as $plugin) {
                // Plugins are considered enabled if $plugin->is_enabled() returns true or null.
                // Plugins that return null cannot be disabled.
                $enabled = ($plugin->is_enabled() || is_null($plugin->is_enabled()));
                $data[$plugin->type][$plugin->name]['enabled'] = $enabled ? 1 : 0;

                if ($plugin->type === 'mod') {
                    $mid = $DB->get_field('modules', 'id', ['name' => $plugin->name]);
                    $count = $DB->count_records('course_modules', ['module' => $mid]);
                    $data[$plugin->type][$plugin->name]['count'] = $count;

                } else if ($plugin->type === 'block') {
                    $count = $DB->count_records('block_instances', ['blockname' => $plugin->name]);
                    $data[$plugin->type][$plugin->name]['count'] = $count;
                }
            }
        }

        return $data;
    }

    /**
     * Get the time range to use in collected and reporting AI usage data.
     *
     * @param bool $format Use true to format timestamp.
     * @return array
     */
    private static function get_ai_usage_time_range(bool $format = false): array {
        global $DB;

        // We will try and use the last time this site was last registered for our 'from' time.
        // Otherwise, default to using one week's worth of data to roughly match the site rego scheduled task.
        $timenow = \core\di::get(\core\clock::class)->time();
        $defaultfrom = $timenow - WEEKSECS;
        $timeto = $timenow;
        $params = [
            'huburl' => HUB_MOODLEORGHUBURL,
            'confirmed' => 1,
        ];
        $lastregistered = $DB->get_field('registration_hubs', 'timemodified', $params);
        $timefrom = $lastregistered ? (int)$lastregistered : $defaultfrom;

        if ($format) {
            $timefrom = userdate($timefrom);
            $timeto = userdate($timeto);
        }

        return [
            'timefrom' => $timefrom,
            'timeto' => $timeto,
        ];
    }

    /**
     * Get AI usage data.
     *
     * @return array
     */
    public static function get_ai_usage_data(): array {
        global $DB;

        $params = self::get_ai_usage_time_range();

        $sql = "SELECT aar.*
                  FROM {ai_action_register} aar
                 WHERE aar.timecompleted >= :timefrom
                   AND aar.timecompleted <= :timeto";

        $actions = $DB->get_records_sql($sql, $params);

        // Build data for site info reporting.
        $data = [];

        foreach ($actions as $action) {
            $provider = $action->provider;
            $actionname = $action->actionname;

            // Initialise data structure.
            if (!isset($data[$provider][$actionname])) {
                $data[$provider][$actionname] = [
                    'success_count' => 0,
                    'fail_count' => 0,
                    'times' => [],
                    'errors' => [],
                ];
            }

            if ($action->success === '1') {
                $data[$provider][$actionname]['success_count'] += 1;
                // Collect AI processing times for averaging.
                $data[$provider][$actionname]['times'][] = (int)$action->timecompleted - (int)$action->timecreated;

            } else {
                $data[$provider][$actionname]['fail_count'] += 1;
                // Collect errors for determing the predominant one.
                $data[$provider][$actionname]['errors'][] = $action->errorcode;
            }
        }

        // Parse the errors and everage the times, then add them to the data.
        foreach ($data as $p => $provider) {
            foreach ($provider as $a => $actionname) {
                if (isset($data[$p][$a]['errors'])) {
                    // Create an array with the error codes counted.
                    $errors = array_count_values($data[$p][$a]['errors']);
                    if (!empty($errors)) {
                        // Sort values descending and convert to an array of error codes (most predominant will be at start).
                        arsort($errors);
                        $errors = array_keys($errors);
                        $data[$p][$a]['predominant_error'] = $errors[0];
                    }
                    unset($data[$p][$a]['errors']);
                }

                if (isset($data[$p][$a]['times'])) {
                    $count = count($data[$p][$a]['times']);
                    if ($count > 0) {
                        // Average the time to perform the action (seconds).
                        $totaltime = array_sum($data[$p][$a]['times']);
                        $data[$p][$a]['average_time'] = round($totaltime / $count);

                    }
                }
                unset($data[$p][$a]['times']);
            }
        }

        // Include the time range used to help interpret the data.
        if (!empty($data)) {
            $data['time_range'] = $params;
        }

        return $data;
    }
}
