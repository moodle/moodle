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
 * Class for Moodle Mobile tools.
 *
 * @package    tool_mobile
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
namespace tool_mobile;

use core_component;
use core_plugin_manager;
use context_system;
use moodle_url;
use moodle_exception;
use lang_string;
use curl;
use core_qrcode;
use stdClass;

/**
 * API exposed by tool_mobile, to be used mostly by external functions and the plugin settings.
 *
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
class api {

    /** @var int to identify the login via app. */
    const LOGIN_VIA_APP = 1;
    /** @var int to identify the login via browser. */
    const LOGIN_VIA_BROWSER = 2;
    /** @var int to identify the login via an embedded browser. */
    const LOGIN_VIA_EMBEDDED_BROWSER = 3;
    /** @var int seconds an auto-login key will expire. */
    const LOGIN_KEY_TTL = 60;
    /** @var string URL of the Moodle Apps Portal */
    const MOODLE_APPS_PORTAL_URL = 'https://apps.moodle.com';
    /** @var int default value in seconds a QR login key will expire. */
    const LOGIN_QR_KEY_TTL = 600;
    /** @var int QR code disabled value */
    const QR_CODE_DISABLED = 0;
    /** @var int QR code type URL value */
    const QR_CODE_URL = 1;
    /** @var int QR code type login value */
    const QR_CODE_LOGIN = 2;
    /** @var string Default Android app id */
    const DEFAULT_ANDROID_APP_ID = 'com.moodle.moodlemobile';
    /** @var string Default iOS app id */
    const DEFAULT_IOS_APP_ID = '633359593';
    /** @var int AUTOLOGOUT disabled value */
    const AUTOLOGOUT_DISABLED = 0;
    /** @var int AUTOLOGOUT type inmediate value */
    const AUTOLOGOUT_INMEDIATE = 1;
    /** @var int AUTOLOGOUT type custom value */
    const AUTOLOGOUT_CUSTOM = 2;

    /**
     * Returns a list of Moodle plugins supporting the mobile app.
     *
     * @return array an array of objects containing the plugin information
     */
    public static function get_plugins_supporting_mobile() {
        global $CFG;
        require_once($CFG->libdir . '/adminlib.php');

        $cachekey = 'mobileplugins';
        if (!isloggedin()) {
            $cachekey = 'authmobileplugins';    // Use a different cache for not logged users.
        }

        // Check if we can return this from cache.
        $cache = \cache::make('tool_mobile', 'plugininfo');
        $pluginsinfo = $cache->get($cachekey);
        if ($pluginsinfo !== false) {
            return (array)$pluginsinfo;
        }

        $pluginsinfo = [];
        // For not logged users return only auth plugins.
        // This is to prevent anyone (not being a registered user) from obtaining and downloading all the site plugins.
        if (!isloggedin()) {
            $plugintypes = array('auth' => $CFG->dirroot.'/auth');
        } else {
            $plugintypes = core_component::get_plugin_types();
        }

        foreach ($plugintypes as $plugintype => $unused) {
            // We need to include files here.
            $pluginswithfile = core_component::get_plugin_list_with_file($plugintype, 'db' . DIRECTORY_SEPARATOR . 'mobile.php');
            foreach ($pluginswithfile as $plugin => $notused) {
                $path = core_component::get_plugin_directory($plugintype, $plugin);
                $component = $plugintype . '_' . $plugin;
                $version = get_component_version($component);

                require("$path/db/mobile.php");
                foreach ($addons as $addonname => $addoninfo) {

                    // Add handlers (for site add-ons).
                    $handlers = !empty($addoninfo['handlers']) ? $addoninfo['handlers'] : [];
                    $handlers = json_encode($handlers); // JSON formatted, since it is a complex structure that may vary over time.

                    // Now language strings used by the app.
                    $lang = [];
                    if (!empty($addoninfo['lang'])) {
                        $stringmanager = get_string_manager();
                        $langs = $stringmanager->get_list_of_translations(true);
                        foreach ($langs as $langid => $langname) {
                            foreach ($addoninfo['lang'] as $stringinfo) {
                                $lang[$langid][$stringinfo[0]] = $stringmanager->get_string(
                                    $stringinfo[0],
                                    $stringinfo[1] ?? '',
                                    null,
                                    $langid,
                                );
                            }
                        }
                    }
                    $lang = json_encode($lang);

                    $plugininfo = array(
                        'component' => $component,
                        'version' => $version,
                        'addon' => $addonname,
                        'dependencies' => !empty($addoninfo['dependencies']) ? $addoninfo['dependencies'] : [],
                        'fileurl' => '',
                        'filehash' => '',
                        'filesize' => 0,
                        'handlers' => $handlers,
                        'lang' => $lang,
                    );

                    // All the mobile packages must be under the plugin mobile directory.
                    $package = $path . '/mobile/' . $addonname . '.zip';
                    if (file_exists($package)) {
                        $plugininfo['fileurl'] = $CFG->wwwroot . '' . str_replace($CFG->dirroot, '', $package);
                        $plugininfo['filehash'] = sha1_file($package);
                        $plugininfo['filesize'] = filesize($package);
                    }
                    $pluginsinfo[] = $plugininfo;
                }
            }
        }

        $cache->set($cachekey, $pluginsinfo);

        return $pluginsinfo;
    }

    /**
     * Returns a list of the site public settings, those not requiring authentication.
     *
     * @return array with the settings and warnings
     */
    public static function get_public_config() {
        global $CFG, $SITE, $PAGE, $OUTPUT;
        require_once($CFG->libdir . '/authlib.php');

        $context = context_system::instance();
        // We need this to make work the format text functions.
        $PAGE->set_context($context);

        // Check if contacting site support is available to all visitors.
        $sitesupportavailable = (isset($CFG->supportavailability) && $CFG->supportavailability == CONTACT_SUPPORT_ANYONE);

        [$authinstructions] = \core_external\util::format_text($CFG->auth_instructions, FORMAT_MOODLE, $context->id);
        [$maintenancemessage] = \core_external\util::format_text($CFG->maintenance_message, FORMAT_MOODLE, $context->id);
        $settings = array(
            'wwwroot' => $CFG->wwwroot,
            'httpswwwroot' => $CFG->wwwroot,
            'sitename' => \core_external\util::format_string($SITE->fullname, $context->id, true),
            'guestlogin' => $CFG->guestloginbutton,
            'rememberusername' => $CFG->rememberusername,
            'authloginviaemail' => $CFG->authloginviaemail,
            'registerauth' => $CFG->registerauth,
            'forgottenpasswordurl' => clean_param($CFG->forgottenpasswordurl, PARAM_URL), // We may expect a mailto: here.
            'authinstructions' => $authinstructions,
            'authnoneenabled' => (int) \core\di::get(\core\authentication::class)->is_enabled('none'),
            'enablewebservices' => $CFG->enablewebservices,
            'enablemobilewebservice' => $CFG->enablemobilewebservice,
            'maintenanceenabled' => $CFG->maintenance_enabled,
            'maintenancemessage' => $maintenancemessage,
            'mobilecssurl' => !empty($CFG->mobilecssurl) ? $CFG->mobilecssurl : '',
            'tool_mobile_disabledfeatures' => get_config('tool_mobile', 'disabledfeatures'),
            'country' => clean_param($CFG->country, PARAM_NOTAGS),
            'agedigitalconsentverification' => \core_auth\digital_consent::is_age_digital_consent_verification_enabled(),
            'autolang' => $CFG->autolang,
            'lang' => clean_param($CFG->lang, PARAM_LANG),  // Avoid breaking WS because of incorrect package langs.
            'langmenu' => $CFG->langmenu,
            'langlist' => $CFG->langlist,
            'locale' => $CFG->locale,
            'tool_mobile_minimumversion' => get_config('tool_mobile', 'minimumversion'),
            'tool_mobile_iosappid' => get_config('tool_mobile', 'iosappid'),
            'tool_mobile_androidappid' => get_config('tool_mobile', 'androidappid'),
            'tool_mobile_setuplink' => clean_param(get_config('tool_mobile', 'setuplink'), PARAM_URL),
            'tool_mobile_qrcodetype' => clean_param(get_config('tool_mobile', 'qrcodetype'), PARAM_INT),
            'tool_mobile_enabledeeplinkautologin' => clean_param(
                get_config('tool_mobile', 'enabledeeplinkautologin'),
                PARAM_BOOL
            ),
            'supportpage' => $sitesupportavailable ? clean_param($CFG->supportpage, PARAM_URL) : '',
            'supportavailability' => clean_param($CFG->supportavailability, PARAM_INT),
            'showloginform' => (int) get_config('core', 'showloginform'),
            'tool_mfa_enabled' => clean_param(get_config('tool_mfa', 'enabled'), PARAM_BOOL),
            'enableloginrecaptcha' => clean_param(login_captcha_enabled(), PARAM_BOOL),
            'enableforgotpasswordrecaptcha' => clean_param(forgotpassword_captcha_enabled(), PARAM_BOOL),
        );

        $typeoflogin = get_config('tool_mobile', 'typeoflogin');
        // Not found, edge case.
        if ($typeoflogin === false) {
            $typeoflogin = self::LOGIN_VIA_APP; // Defaults to via app.
        }
        $settings['typeoflogin'] = $typeoflogin;

        // Check if the user can sign-up to return the launch URL in that case.
        $cansignup = signup_is_enabled();

        $url = new moodle_url("/$CFG->admin/tool/mobile/launch.php");
        $settings['launchurl'] = $url->out(false);

        // Check that we are receiving a moodle_url object, themes can override get_logo_url and may return incorrect values.
        if (($logourl = $OUTPUT->get_logo_url()) && $logourl instanceof moodle_url) {
            $settings['logourl'] = clean_param($logourl->out(false), PARAM_URL);
        }
        if (($compactlogourl = $OUTPUT->get_compact_logo_url()) && $compactlogourl instanceof moodle_url) {
            $settings['compactlogourl'] = clean_param($compactlogourl->out(false), PARAM_URL);
        }

        // Identity providers.
        $authsequence = \core\di::get(\core\authentication::class)->get_enabled_plugins();
        $identityproviders = \auth_plugin_base::get_identity_providers($authsequence);
        $identityprovidersdata = \auth_plugin_base::prepare_identity_providers_for_output($identityproviders, $OUTPUT);
        if (!empty($identityprovidersdata)) {
            $settings['identityproviders'] = $identityprovidersdata;
            // Clean URLs to avoid breaking Web Services.
            // We can't do it in prepare_identity_providers_for_output() because it may break the web output.
            foreach ($settings['identityproviders'] as &$ip) {
                $ip['url'] = (!empty($ip['url'])) ? clean_param($ip['url'], PARAM_URL) : '';
                $ip['iconurl'] = (!empty($ip['iconurl'])) ? clean_param($ip['iconurl'], PARAM_URL) : '';
            }
        }

        // If age is verified or support is available to all visitors, also return the admin contact details.
        if ($settings['agedigitalconsentverification'] || $sitesupportavailable) {
            $settings['supportname'] = clean_param($CFG->supportname, PARAM_NOTAGS);
            $settings['supportemail'] = clean_param($CFG->supportemail, PARAM_EMAIL);
        }

        return $settings;
    }

    /**
     * Returns a list of site configurations, filtering by section.
     *
     * @param  string $section section name
     * @return stdClass object containing the settings
     */
    public static function get_config($section) {
        global $CFG, $SITE;

        $settings = new \stdClass;
        $context = context_system::instance();
        $isadmin = has_capability('moodle/site:config', $context);

        if (empty($section) or $section == 'frontpagesettings') {
            require_once($CFG->dirroot . '/course/format/lib.php');
            // First settings that anyone can deduce.
            $settings->fullname = \core_external\util::format_string($SITE->fullname, $context->id);
            $settings->shortname = \core_external\util::format_string($SITE->shortname, $context->id);

            // Return to a var instead of directly to $settings object because of differences between
            // list() in php5 and php7. {@link http://php.net/manual/en/function.list.php}
            $formattedsummary = \core_external\util::format_text($SITE->summary, $SITE->summaryformat,
                                                                                        $context->id);
            $settings->summary = $formattedsummary[0];
            $settings->summaryformat = $formattedsummary[1];
            $settings->frontpage = $CFG->frontpage;
            $settings->frontpageloggedin = $CFG->frontpageloggedin;
            $settings->maxcategorydepth = $CFG->maxcategorydepth;
            $settings->frontpagecourselimit = $CFG->frontpagecourselimit;
            $settings->numsections = course_get_format($SITE)->get_last_section_number();
            $settings->newsitems = $SITE->newsitems;
            $settings->commentsperpage = $CFG->commentsperpage;

            // Now, admin settings.
            if ($isadmin) {
                $settings->defaultfrontpageroleid = $CFG->defaultfrontpageroleid;
            }
        }

        if (empty($section) or $section == 'sitepolicies') {
            $manager = new \core_privacy\local\sitepolicy\manager();
            $settings->sitepolicy = ($sitepolicy = $manager->get_embed_url()) ? $sitepolicy->out(false) : '';
            $settings->sitepolicyhandler = $CFG->sitepolicyhandler;
            $settings->disableuserimages = $CFG->disableuserimages;
        }

        if (empty($section) or $section == 'gradessettings') {
            require_once($CFG->dirroot . '/user/lib.php');
            $settings->mygradesurl = \core\user::mygrades_url()->out(false);
        }

        if (empty($section) or $section == 'mobileapp') {
            $settings->tool_mobile_forcelogout = get_config('tool_mobile', 'forcelogout');
            $settings->tool_mobile_customlangstrings = get_config('tool_mobile', 'customlangstrings');
            $settings->tool_mobile_disabledfeatures = get_config('tool_mobile', 'disabledfeatures');
            $settings->tool_mobile_showlogoinappheader = clean_param(get_config('tool_mobile', 'showlogoinappheader'), PARAM_BOOL);
            $settings->tool_mobile_filetypeexclusionlist = get_config('tool_mobile', 'filetypeexclusionlist');
            $custommenuitems = get_config('tool_mobile', 'custommenuitems');
            $customusermenuitems = get_config('tool_mobile', 'customusermenuitems');
            // If filtering of the primary custom menu is enabled, apply only the string filters.
            if (!empty($CFG->navfilter && !empty($CFG->stringfilters))) {
                // Apply filters that are enabled for Content and Headings.
                $filtermanager = \filter_manager::instance();
                $custommenuitems = $filtermanager->filter_string($custommenuitems, \context_system::instance());
                $customusermenuitems = $filtermanager->filter_string($customusermenuitems, \context_system::instance());
            }
            $settings->tool_mobile_custommenuitems = $custommenuitems;
            $settings->tool_mobile_customusermenuitems = $customusermenuitems;
            $settings->tool_mobile_scriptallowlist = get_config('tool_mobile', 'scriptallowlist');
            $settings->tool_mobile_apppolicy = get_config('tool_mobile', 'apppolicy');
            // This setting could be not set in some edge cases such as bad upgrade.
            $mintimereq = get_config('tool_mobile', 'autologinmintimebetweenreq');
            $mintimereq = empty($mintimereq) ? 6 * MINSECS : $mintimereq;
            $settings->tool_mobile_autologinmintimebetweenreq = $mintimereq;
            $settings->tool_mobile_autologout = get_config('tool_mobile', 'autologout');
            $settings->tool_mobile_autologouttime = get_config('tool_mobile', 'autologouttime');
        }

        if (empty($section) or $section == 'calendar') {
            $settings->calendartype = $CFG->calendartype;
            $settings->calendar_site_timeformat = $CFG->calendar_site_timeformat;
            $settings->calendar_startwday = $CFG->calendar_startwday;
            $settings->calendar_adminseesall = $CFG->calendar_adminseesall;
            $settings->calendar_lookahead = $CFG->calendar_lookahead;
            $settings->calendar_maxevents = $CFG->calendar_maxevents;
        }

        if (empty($section) or $section == 'coursecolors') {
            $colornumbers = range(1, 10);
            foreach ($colornumbers as $number) {
                $settings->{'core_admin_coursecolor' . $number} = get_config('core_admin', 'coursecolor' . $number);
            }
        }

        if (empty($section) or $section == 'supportcontact') {
            $settings->supportavailability = $CFG->supportavailability;

            if ($CFG->supportavailability == CONTACT_SUPPORT_DISABLED) {
                $settings->supportname = null;
                $settings->supportemail = null;
                $settings->supportpage = null;
            } else {
                $settings->supportname = $CFG->supportname;
                $settings->supportemail = $CFG->supportemail ?? null;
                $settings->supportpage = $CFG->supportpage;
            }
        }

        if (empty($section) || $section === 'graceperiodsettings') {
            $settings->coursegraceperiodafter = $CFG->coursegraceperiodafter;
            $settings->coursegraceperiodbefore = $CFG->coursegraceperiodbefore;
        }

        if (empty($section) || $section === 'navigation') {
            $settings->enablemyhome = $CFG->enablemyhome ?? 1;
            $settings->enabledashboard = $CFG->enabledashboard;
            $settings->enablemycourses = $CFG->enablemycourses ?? 1;
        }

        if (empty($section) || ($section === 'themesettings' || $section === 'themesettingsadvanced')) {
            $settings->customusermenuitems = $CFG->customusermenuitems;
        }

        if (empty($section) || $section === 'locationsettings') {
            $settings->timezone = $CFG->timezone;
            $settings->forcetimezone = $CFG->forcetimezone;
        }

        if (empty($section) || $section === 'manageglobalsearch') {
            $settings->searchengine = $CFG->searchengine;
            $settings->searchenablecategories = $CFG->searchenablecategories;
            $settings->searchdefaultcategory = $CFG->searchdefaultcategory;
            $settings->searchhideallcategory = $CFG->searchhideallcategory;
            $settings->searchmaxtopresults = $CFG->searchmaxtopresults;
            $settings->searchbannerenable = $CFG->searchbannerenable;
            $settings->searchbanner = \core_external\util::format_text(
                $CFG->searchbanner, FORMAT_HTML, $context)[0];
        }

        if (empty($section) || $section === 'privacysettings') {
            $settings->tool_dataprivacy_contactdataprotectionofficer = get_config('tool_dataprivacy', 'contactdataprotectionofficer');
            $settings->tool_dataprivacy_showdataretentionsummary = get_config('tool_dataprivacy', 'showdataretentionsummary');
        }

        if (empty($section) || $section === 'blog') {
            $settings->useblogassociations = $CFG->useblogassociations;
            $settings->bloglevel = $CFG->bloglevel;
            $settings->blogusecomments = $CFG->blogusecomments;
        }

        if (empty($section) || $section === 'h5psettings') {
            \core_h5p\local\library\autoloader::register();
            $customcss = \core_h5p\file_storage::get_custom_styles();
            if (!empty($customcss)) {
                $settings->h5pcustomcssurl = $customcss['cssurl']->out() . '?ver=' . $customcss['cssversion'];
            }
        }

        return $settings;
    }

    /*
     * Check if all the required conditions are met to allow the auto-login process continue.
     *
     * @param  int $userid  current user id
     * @since Moodle 3.2
     * @throws moodle_exception
     */
    public static function check_autologin_prerequisites($userid) {
        global $CFG;

        if (!$CFG->enablewebservices or !$CFG->enablemobilewebservice) {
            throw new moodle_exception('enablewsdescription', 'webservice');
        }

        if (!is_https()) {
            throw new moodle_exception('httpsrequired', 'tool_mobile');
        }

        if (has_capability('moodle/site:config', context_system::instance(), $userid) or is_siteadmin($userid)) {
            throw new moodle_exception('autologinnotallowedtoadmins', 'tool_mobile');
        }
    }

    /**
     * Creates an auto-login key for the current user, this key is restricted by time and ip address.
     * This key is used for automatically login the user in the site when the Moodle app opens the site in a mobile browser.
     *
     * @return string the key
     * @since Moodle 3.2
     */
    public static function get_autologin_key() {
        global $USER;
        // Delete previous keys.
        delete_user_key('tool_mobile', $USER->id);

        // Create a new key.
        $iprestriction = getremoteaddr();
        $validuntil = time() + self::LOGIN_KEY_TTL;
        return create_user_key('tool_mobile', $USER->id, null, $iprestriction, $validuntil);
    }

    /**
     * Creates a QR login key for the current user, this key is restricted by time and ip address.
     * This key is used for automatically login the user in the site when the user scans a QR code in the Moodle app.
     *
     * @param  stdClass $mobilesettings  mobile app plugin settings
     * @return string the key
     * @since Moodle 3.9
     */
    public static function get_qrlogin_key(stdClass $mobilesettings) {
        global $USER;
        // Delete previous keys.
        delete_user_key('tool_mobile/qrlogin', $USER->id);

        // Create a new key.
        $iprestriction = !empty($mobilesettings->qrsameipcheck) ? getremoteaddr(null) : null;
        $qrkeyttl = !empty($mobilesettings->qrkeyttl) ? $mobilesettings->qrkeyttl : self::LOGIN_QR_KEY_TTL;
        $validuntil = time() + $qrkeyttl;
        return create_user_key('tool_mobile/qrlogin', $USER->id, null, $iprestriction, $validuntil);
    }

    /**
     * Get a list of the Mobile app features.
     *
     * @return array array with the features grouped by theirs ubication in the app.
     * @since Moodle 3.3
     */
    public static function get_features_list() {
        global $CFG;
        require_once($CFG->libdir . '/authlib.php');

        $general = new lang_string('general');
        $mainmenu = new lang_string('mainmenu', 'tool_mobile');
        $course = new lang_string('course');
        $modules = new lang_string('managemodules');
        $blocks = new lang_string('blocks');
        $useraccount = new lang_string('useraccount');
        $participants = new lang_string('participants');
        $files = new lang_string('files');
        $siteplugins = new lang_string('siteplugins', 'tool_mobile');
        $identityproviders = new lang_string('oauth2identityproviders', 'tool_mobile');

        $availablemods = core_plugin_manager::instance()->get_plugins_of_type('mod');
        $coursemodules = [];
        $appsupportedmodules = [
            'assign', 'bigbluebuttonbn', 'book', 'choice', 'data', 'feedback', 'folder', 'forum', 'glossary', 'h5pactivity',
            'imscp', 'label', 'lesson', 'lti', 'page', 'quiz', 'resource', 'scorm', 'url', 'wiki', 'workshop'];

        foreach ($availablemods as $mod) {
            if (in_array($mod->name, $appsupportedmodules)) {
                $modfeaturename = ucfirst($mod->name);
                if ($mod->name === 'bigbluebuttonbn') {
                    $modfeaturename = 'BBB';
                } else if ($mod->name === 'h5pactivity') {
                    $modfeaturename = 'H5PActivity';
                }
                $coursemodules['CoreCourseModuleDelegate_AddonMod' . $modfeaturename] = $mod->displayname;
            }
        }
        asort($coursemodules);

        $sitepluginslist = [];
        $mobileplugins = self::get_plugins_supporting_mobile();
        foreach ($mobileplugins as $plugin) {
            $displayname = core_plugin_manager::instance()->plugin_name($plugin['component']) . " - " . $plugin['addon'];
            $sitepluginslist['sitePlugin_' . $plugin['component'] . '_' . $plugin['addon']] = $displayname;
        }

        // Display blocks.
        $availableblocks = core_plugin_manager::instance()->get_plugins_of_type('block');
        $courseblocks = [];
        $appsupportedblocks = [
            'activity_results' => 'CoreBlockDelegate_AddonBlockActivityResults',
            'site_main_menu' => 'CoreBlockDelegate_AddonBlockSiteMainMenu',
            'myoverview' => 'CoreBlockDelegate_AddonBlockMyOverview',
            'course_list' => 'CoreBlockDelegate_AddonBlockCourseList',
            'timeline' => 'CoreBlockDelegate_AddonBlockTimeline',
            'recentlyaccessedcourses' => 'CoreBlockDelegate_AddonBlockRecentlyAccessedCourses',
            'starredcourses' => 'CoreBlockDelegate_AddonBlockStarredCourses',
            'recentlyaccesseditems' => 'CoreBlockDelegate_AddonBlockRecentlyAccessedItems',
            'badges' => 'CoreBlockDelegate_AddonBlockBadges',
            'blog_menu' => 'CoreBlockDelegate_AddonBlockBlogMenu',
            'blog_recent' => 'CoreBlockDelegate_AddonBlockBlogRecent',
            'blog_tags' => 'CoreBlockDelegate_AddonBlockBlogTags',
            'calendar_month' => 'CoreBlockDelegate_AddonBlockCalendarMonth',
            'calendar_upcoming' => 'CoreBlockDelegate_AddonBlockCalendarUpcoming',
            'comments' => 'CoreBlockDelegate_AddonBlockComments',
            'completionstatus' => 'CoreBlockDelegate_AddonBlockCompletionStatus',
            'feedback' => 'CoreBlockDelegate_AddonBlockFeedback',
            'globalsearch' => 'CoreBlockDelegate_AddonBlockGlobalSearch',
            'glossary_random' => 'CoreBlockDelegate_AddonBlockGlossaryRandom',
            'html' => 'CoreBlockDelegate_AddonBlockHtml',
            'lp' => 'CoreBlockDelegate_AddonBlockLp',
            'news_items' => 'CoreBlockDelegate_AddonBlockNewsItems',
            'online_users' => 'CoreBlockDelegate_AddonBlockOnlineUsers',
            'private_files' => 'CoreBlockDelegate_AddonBlockPrivateFiles',
            'recent_activity' => 'CoreBlockDelegate_AddonBlockRecentActivity',
            'rss_client' => 'CoreBlockDelegate_AddonBlockRssClient',
            'search_forums' => 'CoreBlockDelegate_AddonBlockSearchForums',
            'selfcompletion' => 'CoreBlockDelegate_AddonBlockSelfCompletion',
            'tags' => 'CoreBlockDelegate_AddonBlockTags',
        ];

        foreach ($availableblocks as $block) {
            if (isset($appsupportedblocks[$block->name])) {
                $courseblocks[$appsupportedblocks[$block->name]] = $block->displayname;
            }
        }
        asort($courseblocks);

        $features = [
            "$general" => [
                'NoDelegate_CoreOffline' => new lang_string('offlineuse', 'tool_mobile'),
                'NoDelegate_SiteBlocks' => new lang_string('blocks'),
                'NoDelegate_CoreComments' => new lang_string('comments'),
                'NoDelegate_CoreRating' => new lang_string('ratings', 'rating'),
                'NoDelegate_CoreTag' => new lang_string('tags'),
                'CoreLoginEmailSignup' => new lang_string('startsignup'),
                'NoDelegate_ForgottenPassword' => new lang_string('forgotten'),
                'NoDelegate_ResponsiveMainMenuItems' => new lang_string('responsivemainmenuitems', 'tool_mobile'),
                'NoDelegate_H5POffline' => new lang_string('h5poffline', 'tool_mobile'),
                'NoDelegate_DarkMode' => new lang_string('darkmode', 'tool_mobile'),
                'CoreFilterDelegate' => new lang_string('type_filter_plural', 'plugin'),
                'CoreReportBuilderDelegate' => new lang_string('reportbuilder', 'core_reportbuilder'),
                'NoDelegate_CoreUserSupport' => new lang_string('contactsitesupport', 'admin'),
                'NoDelegate_GlobalSearch' => new lang_string('globalsearch', 'search'),
            ],
            "$mainmenu" => [
                'CoreMainMenuDelegate_CoreSiteHome' => new lang_string('sitehome'),
                'CoreMainMenuDelegate_CoreCoursesDashboard' => new lang_string('myhome'),
                'CoreMainMenuDelegate_CoreCourses' => new lang_string('mycourses'),
                'CoreMainMenuDelegate_AddonMessages' => new lang_string('messages', 'message'),
                'CoreMainMenuDelegate_AddonNotifications' => new lang_string('notifications', 'message'),
                'CoreMainMenuDelegate_AddonCalendar' => new lang_string('calendar', 'calendar'),
                'CoreMainMenuDelegate_AddonBlog' => new lang_string('blog', 'blog'),
                'CoreMainMenuDelegate_CoreTag' => new lang_string('tags'),
                'CoreMainMenuDelegate_QrReader' => new lang_string('scanqrcode', 'tool_mobile'),
            ],
            "$useraccount" => [
                'CoreUserDelegate_CoreGrades' => new lang_string('grades', 'grades'),
                'CoreUserDelegate_AddonPrivateFiles' => new lang_string('files'),
                'CoreUserDelegate_AddonBadges:account' => new lang_string('badges', 'badges'),
                'CoreUserDelegate_AddonBlog:account' => new lang_string('blog', 'blog'),
                'CoreUserDelegate_AddonCompetency' => new lang_string('myplans', 'tool_lp'),
                'CoreUserDelegate_CorePolicy' => new lang_string('policiesagreements', 'tool_policy'),
                'CoreUserDelegate_CoreDataPrivacy' => new lang_string('pluginname', 'tool_dataprivacy'),
                'NoDelegate_SwitchAccount' => new lang_string('switchaccount', 'tool_mobile'),
            ],
            "$course" => [
                'CoreCourseOptionsDelegate_CoreUserParticipants' => new lang_string('participants'),
                'CoreCourseOptionsDelegate_CoreGrades' => new lang_string('grades', 'grades'),
                'CoreCourseOptionsDelegate_AddonCompetency' => new lang_string('competencies', 'competency'),
                'CoreCourseOptionsDelegate_AddonNotes' => new lang_string('notes', 'notes'),
                'CoreCourseOptionsDelegate_AddonCourseCompletion' => new lang_string('coursecompletion', 'completion'),
                'NoDelegate_CourseBlocks' => new lang_string('blocks'),
                'CoreCourseOptionsDelegate_AddonBlog' => new lang_string('blog', 'blog'),
                'CoreCourseOptionsDelegate_search' => new lang_string('search'),
                'NoDelegate_CoreCourseDownload' => new lang_string('downloadcourse', 'tool_mobile'),
                'NoDelegate_CoreCoursesDownload' => new lang_string('downloadcourses', 'tool_mobile'),
                'CoreCourseOptionsDelegate_CoreCourseOverview' => new lang_string('activitiesoverview', 'tool_mobile'),
                'NoDelegate_CoreCourseModuleNavigation' => new lang_string('modulenavigation', 'tool_mobile'),
                'NoDelegate_CoreCourseSectionNavigation' => new lang_string('sectionnavigation', 'tool_mobile'),
            ],
            "$participants" => [
                'CoreUserDelegate_CoreGrades:viewGrades' => new lang_string('grades', 'grades'),
                'CoreUserDelegate_AddonCourseCompletion:viewCompletion' => new lang_string('coursecompletion', 'completion'),
                'CoreUserDelegate_AddonBadges' => new lang_string('badges', 'badges'),
                'CoreUserDelegate_AddonNotes:notes' => new lang_string('notes', 'notes'),
                'CoreUserDelegate_AddonBlog:blogs' => new lang_string('blog', 'blog'),
                'CoreUserDelegate_AddonCompetency:learningPlan' => new lang_string('competencies', 'competency'),
                'CoreUserDelegate_AddonMessages:sendMessage' => new lang_string('sendmessage', 'message'),
                'CoreUserDelegate_picture' => new lang_string('userpic'),
            ],
            "$files" => [
                'AddonPrivateFilesPrivateFiles' => new lang_string('privatefiles'),
                'AddonPrivateFilesSiteFiles' => new lang_string('sitefiles'),
                'AddonPrivateFilesUpload' => new lang_string('upload'),
            ],
            "$modules" => $coursemodules,
            "$blocks" => $courseblocks,
        ];

        if (!empty($sitepluginslist)) {
            $features["$siteplugins"] = $sitepluginslist;
        }

        if (!empty($availablemods['lti'])) {
            $ltidisplayname = $availablemods['lti']->displayname;
            $features["$ltidisplayname"]['CoreCourseModuleDelegate_AddonModLti:launchViaSite'] =
                new lang_string('launchviasiteinbrowser', 'tool_mobile');
        }

        // Display OAuth 2 identity providers.
        if (\core\di::get(\core\authentication::class)->is_enabled('oauth2')) {
            $identityproviderslist = [];
            $idps = \auth_plugin_base::get_identity_providers(['oauth2']);

            foreach ($idps as $idp) {
                // Only add identity providers that have an ID.
                $id = isset($idp['url']) ? $idp['url']->get_param('id') : null;
                if ($id != null) {
                    $identityproviderslist['NoDelegate_IdentityProvider_' . $id] = $idp['name'];
                }
            }

            if (!empty($identityproviderslist)) {
                $features["$identityproviders"] = [];

                if (count($identityproviderslist) > 1) {
                    // Include an option to disable them all.
                    $features["$identityproviders"]['NoDelegate_IdentityProviders'] = new lang_string('all');
                }

                $features["$identityproviders"] = array_merge($features["$identityproviders"], $identityproviderslist);
            }
        }

        return $features;
    }

    /**
     * This function check the current site for potential configuration issues that may prevent the mobile app to work.
     *
     * @return array list of potential issues
     * @since  Moodle 3.4
     */
    public static function get_potential_config_issues() {
        global $CFG;
        require_once($CFG->dirroot . "/lib/filelib.php");
        require_once($CFG->dirroot . '/message/lib.php');

        $warnings = [];

        if (is_https()) {
            $curl = new curl();
            // Return certificate information and verify the certificate.
            $curl->setopt(array('CURLOPT_CERTINFO' => 1, 'CURLOPT_SSL_VERIFYPEER' => true));
            // Check https using a page not redirecting or returning exceptions.
            $curl->head("$CFG->wwwroot/$CFG->admin/tool/mobile/mobile.webmanifest.php");
            $info = $curl->get_info();

            // Check the certificate is not self-signed or has an untrusted-root.
            // This may be weak in some scenarios (when the curl SSL verifier is outdated).
            if (empty($info['http_code']) || empty($info['certinfo'])) {
                $warnings[] = ['selfsignedoruntrustedcertificatewarning', 'tool_mobile'];
            } else {
                $timenow = time();
                $infokeys = array_keys($info['certinfo']);
                $lastkey = end($infokeys);

                if (count($info['certinfo']) == 1) {
                    // This will work in a normal browser because it will complete the chain, but not in a mobile app.
                    $warnings[] = ['invalidcertificatechainwarning', 'tool_mobile'];
                }

                foreach ($info['certinfo'] as $key => $cert) {
                    // Convert to lower case the keys, some OS/curl implementations differ.
                    $cert = array_change_key_case($cert, CASE_LOWER);

                    // Due to a bug in certain curl/openssl versions the signature algorithm isn't always correctly parsed.
                    // See https://github.com/curl/curl/issues/3706 for reference.
                    if (!array_key_exists('signature algorithm', $cert)) {
                        // The malformed field that does contain the algorithm we're looking for looks like the following:
                        // <WHITESPACE>Signature Algorithm: <ALGORITHM><CRLF><ALGORITHM>.
                        preg_match('/\s+Signature Algorithm: (?<algorithm>[^\s]+)/', $cert['public key algorithm'], $matches);

                        $signaturealgorithm = $matches['algorithm'] ?? '';
                    } else {
                        $signaturealgorithm = $cert['signature algorithm'];
                    }

                    // Check if the signature algorithm is weak (Android won't work with SHA-1).
                    if ($key != $lastkey &&
                            ($signaturealgorithm == 'sha1WithRSAEncryption' || $signaturealgorithm == 'sha1WithRSA')) {
                        $warnings['insecurealgorithmwarning'] = ['insecurealgorithmwarning', 'tool_mobile'];
                    }
                    // Check certificate start date.
                    if (strtotime($cert['start date']) > $timenow) {
                        $warnings['invalidcertificatestartdatewarning'] = ['invalidcertificatestartdatewarning', 'tool_mobile'];
                    }
                    // Check certificate end date.
                    if (strtotime($cert['expire date']) < $timenow) {
                        $warnings['invalidcertificateexpiredatewarning'] = ['invalidcertificateexpiredatewarning', 'tool_mobile'];
                    }
                }
            }
        } else {
            // Warning for non https sites.
            $warnings[] = ['nohttpsformobilewarning', 'admin'];
        }

        // Check ADOdb debug enabled.
        if (get_config('auth_db', 'debugauthdb') || get_config('enrol_database', 'debugdb')) {
            $warnings[] = ['adodbdebugwarning', 'tool_mobile'];
        }
        // Check display errors on.
        if (!empty($CFG->debugdisplay)) {
            $warnings[] = ['displayerrorswarning', 'tool_mobile'];
        }
        // Check mobile notifications.
        $processors = get_message_processors();
        $enabled = false;
        foreach ($processors as $processor => $status) {
            if ($processor == 'airnotifier' && $status->enabled) {
                $enabled = true;
            }
        }
        if (!$enabled) {
            $warnings[] = ['mobilenotificationsdisabledwarning', 'tool_mobile'];
        }

        return $warnings;
    }

    /**
     * Generates a QR code with the site URL or for automatic login from the mobile app.
     *
     * @param  stdClass $mobilesettings tool_mobile settings
     * @return string base64 data image contents, null if qr disabled
     */
    public static function generate_login_qrcode(stdClass $mobilesettings) {
        global $CFG, $USER;

        if ($mobilesettings->qrcodetype == static::QR_CODE_DISABLED) {
            return null;
        }

        $urlscheme = !empty($mobilesettings->forcedurlscheme) ? $mobilesettings->forcedurlscheme : 'moodlemobile';
        $data = $urlscheme . '://' . $CFG->wwwroot;

        if ($mobilesettings->qrcodetype == static::QR_CODE_LOGIN) {
            $qrloginkey = static::get_qrlogin_key($mobilesettings);
            $data .= '?qrlogin=' . $qrloginkey . '&userid=' . $USER->id;
        }

        $qrcode = new core_qrcode($data);
        $imagedata = 'data:image/png;base64,' . base64_encode($qrcode->getBarcodePngData(5, 5));

        return $imagedata;
    }

    /**
     * Gets Moodle app plan subscription information for the current site as it is returned by the Apps Portal.
     *
     * @param bool $forcecache If true, return only cached data. Has priority over $ignorecache.
     * @param bool $ignorecache If true, ignore cached data and request information from the Application Portal.
     * @param int $timeout Time in seconds to wait for the Apps Portal response before giving up. Defaults to 10 seconds.
     * @return array Subscription information
     */
    public static function get_subscription_information($forcecache = false, $ignorecache = false, $timeout = 10): ?array {
        global $CFG;

        require_once($CFG->libdir . '/filelib.php');

        $timeout = min(30, $timeout);
        // Manage cache of the subscription information to avoid requesting it too often to the Moodle Apps Portal.
        $cache = \cache::make('tool_mobile', 'subscriptioninfo');
        $subscriptiondata = $cache->get(0);

        // If we must force using cache, return it (or null if not present) and never contact the portal.
        if ($forcecache) {
            return $subscriptiondata !== false ? $subscriptiondata : null;
        }

        // If we are allowed to use cache and we have data, return it early.
        if (!$ignorecache && $subscriptiondata !== false) {
            return $subscriptiondata;
        }

        $mobilesettings = get_config('tool_mobile');

        // To validate that the requests come from this site we need to send some private information that only is known by the
        // Moodle Apps portal or the Sites registration database.
        $credentials = [];

        if (!empty($CFG->airnotifieraccesskey)) {
            $credentials[] = ['type' => 'airnotifieraccesskey', 'value' => $CFG->airnotifieraccesskey];
        }
        if (\core\hub\registration::is_registered()) {
            $credentials[] = ['type' => 'siteid', 'value' => \core\hub\registration::get_secret()];
        }
        // Generate a hash key for validating that the request is coming from this site via WS.
        $key = complex_random_string(32);
        $sitesubscriptionkey = json_encode(['validuntil' => time() + 10 * MINSECS, 'key' => $key]);
        set_config('sitesubscriptionkey', $sitesubscriptionkey, 'tool_mobile');
        $credentials[] = ['type' => 'sitesubscriptionkey', 'value' => $key];

        // Parameters for the WebService returning site information.
        $androidappid = empty($mobilesettings->androidappid) ? static::DEFAULT_ANDROID_APP_ID : $mobilesettings->androidappid;
        $iosappid = empty($mobilesettings->iosappid) ? static::DEFAULT_IOS_APP_ID : $mobilesettings->iosappid;
        $fnparams = (object) [
            'siteurl' => $CFG->wwwroot,
            'appids' => [$androidappid, $iosappid],
            'credentials' => $credentials,
        ];
        // Get the current language to send to the WS.
        $settingslang = current_language();
        // Prepare the arguments for a request to the AJAX nologin endpoint.
        $args = [
            (object) [
                'index' => 0,
                'methodname' => 'local_apps_get_site_info',
                'args' => $fnparams,
            ],
        ];

        // Ask the Moodle Apps Portal for the subscription information.
        $curl = new \curl();
        $curl->setopt([
            'CURLOPT_TIMEOUT' => $timeout,
            'CURLOPT_CONNECTTIMEOUT' => $timeout,
        ]);

        $serverurl = static::MOODLE_APPS_PORTAL_URL . "/lib/ajax/service-nologin.php?lang=$settingslang";
        $query = 'args=' . urlencode(json_encode($args));
        $wsresponse = @json_decode($curl->post($serverurl, $query), true);

        $info = $curl->get_info();
        if ($curlerrno = $curl->get_errno()) {
            // CURL connection error.
            debugging("Unexpected response from the Moodle Apps Portal server, CURL error number: $curlerrno");
            if (!$ignorecache && $subscriptiondata !== false) {
                return $subscriptiondata;
            }
            return null;
        } else if (!empty($curl->error)) {
            // CURL error without an error number.
            debugging('Unexpected response from the Moodle Apps Portal server, CURL error: ' . $curl->error);
            if (!$ignorecache && $subscriptiondata !== false) {
                return $subscriptiondata;
            }
            return null;
        } else if ($info['http_code'] != 200) {
            // Unexpected error from server.
            debugging('Unexpected response from the Moodle Apps Portal server, HTTP code:' . $info['http_code']);
            if (!$ignorecache && $subscriptiondata !== false) {
                return $subscriptiondata;
            }
            return null;
        } else if (!empty($wsresponse[0]['error'])) {
            // Unexpected error from Moodle Apps Portal.
            debugging('Unexpected response from the Moodle Apps Portal server:' . json_encode($wsresponse[0]));
            return null;
        } else if (empty($wsresponse[0]['data'])) {
            debugging('Unexpected response from the Moodle Apps Portal server:' . json_encode($wsresponse));
            return null;
        }

        $cache->set(0, $wsresponse[0]['data']);
        set_config('subscriptioninfoupdated', time(), 'tool_mobile');
        return $wsresponse[0]['data'];
    }
}
