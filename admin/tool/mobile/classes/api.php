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
        // This is to avoid anyone (not being a registered user) to obtain and download all the site remote add-ons.
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
                    $handlers = !empty($addoninfo['handlers']) ? $addoninfo['handlers'] : array();
                    $handlers = json_encode($handlers); // JSON formatted, since it is a complex structure that may vary over time.

                    // Now language strings used by the app.
                    $lang = array();
                    if (!empty($addoninfo['lang'])) {
                        $stringmanager = get_string_manager();
                        $langs = $stringmanager->get_list_of_translations(true);
                        foreach ($langs as $langid => $langname) {
                            foreach ($addoninfo['lang'] as $stringinfo) {
                                $lang[$langid][$stringinfo[0]] =
                                    $stringmanager->get_string($stringinfo[0], $stringinfo[1], null, $langid);
                            }
                        }
                    }
                    $lang = json_encode($lang);

                    $plugininfo = array(
                        'component' => $component,
                        'version' => $version,
                        'addon' => $addonname,
                        'dependencies' => !empty($addoninfo['dependencies']) ? $addoninfo['dependencies'] : array(),
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

        list($authinstructions, $notusedformat) = external_format_text($CFG->auth_instructions, FORMAT_MOODLE, $context->id);
        list($maintenancemessage, $notusedformat) = external_format_text($CFG->maintenance_message, FORMAT_MOODLE, $context->id);
        $settings = array(
            'wwwroot' => $CFG->wwwroot,
            'httpswwwroot' => $CFG->wwwroot,
            'sitename' => external_format_string($SITE->fullname, $context->id, true),
            'guestlogin' => $CFG->guestloginbutton,
            'rememberusername' => $CFG->rememberusername,
            'authloginviaemail' => $CFG->authloginviaemail,
            'registerauth' => $CFG->registerauth,
            'forgottenpasswordurl' => clean_param($CFG->forgottenpasswordurl, PARAM_URL), // We may expect a mailto: here.
            'authinstructions' => $authinstructions,
            'authnoneenabled' => (int) is_enabled_auth('none'),
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
        $authsequence = get_enabled_auth_plugins(true);
        $identityproviders = \auth_plugin_base::get_identity_providers($authsequence);
        $identityprovidersdata = \auth_plugin_base::prepare_identity_providers_for_output($identityproviders, $OUTPUT);
        if (!empty($identityprovidersdata)) {
            $settings['identityproviders'] = $identityprovidersdata;
        }

        // If age is verified, return also the admin contact details.
        if ($settings['agedigitalconsentverification']) {
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
            $settings->fullname = external_format_string($SITE->fullname, $context->id);
            $settings->shortname = external_format_string($SITE->shortname, $context->id);

            // Return to a var instead of directly to $settings object because of differences between
            // list() in php5 and php7. {@link http://php.net/manual/en/function.list.php}
            $formattedsummary = external_format_text($SITE->summary, $SITE->summaryformat,
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
            $settings->mygradesurl = user_mygrades_url();
            // The previous function may return moodle_url instances or plain string URLs.
            if ($settings->mygradesurl instanceof moodle_url) {
                $settings->mygradesurl = $settings->mygradesurl->out(false);
            }
        }

        if (empty($section) or $section == 'mobileapp') {
            $settings->tool_mobile_forcelogout = get_config('tool_mobile', 'forcelogout');
            $settings->tool_mobile_customlangstrings = get_config('tool_mobile', 'customlangstrings');
            $settings->tool_mobile_disabledfeatures = get_config('tool_mobile', 'disabledfeatures');
            $settings->tool_mobile_custommenuitems = get_config('tool_mobile', 'custommenuitems');
            $settings->tool_mobile_apppolicy = get_config('tool_mobile', 'apppolicy');
        }

        if (empty($section) or $section == 'calendar') {
            $settings->calendartype = $CFG->calendartype;
            $settings->calendar_site_timeformat = $CFG->calendar_site_timeformat;
            $settings->calendar_startwday = $CFG->calendar_startwday;
            $settings->calendar_adminseesall = $CFG->calendar_adminseesall;
            $settings->calendar_lookahead = $CFG->calendar_lookahead;
            $settings->calendar_maxevents = $CFG->calendar_maxevents;
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
     * Get a list of the Mobile app features.
     *
     * @return array array with the features grouped by theirs ubication in the app.
     * @since Moodle 3.3
     */
    public static function get_features_list() {
        global $CFG;

        $general = new lang_string('general');
        $mainmenu = new lang_string('mainmenu', 'tool_mobile');
        $course = new lang_string('course');
        $modules = new lang_string('managemodules');
        $blocks = new lang_string('blocks');
        $user = new lang_string('user');
        $files = new lang_string('files');
        $remoteaddons = new lang_string('remoteaddons', 'tool_mobile');

        $availablemods = core_plugin_manager::instance()->get_plugins_of_type('mod');
        $coursemodules = array();
        $appsupportedmodules = array('assign', 'book', 'chat', 'choice', 'data', 'feedback', 'folder', 'forum', 'glossary', 'imscp',
            'label', 'lesson', 'lti', 'page', 'quiz', 'resource', 'scorm', 'survey', 'url', 'wiki', 'workshop');

        foreach ($availablemods as $mod) {
            if (in_array($mod->name, $appsupportedmodules)) {
                $coursemodules['$mmCourseDelegate_mmaMod' . ucfirst($mod->name)] = $mod->displayname;
            }
        }
        asort($coursemodules);

        $remoteaddonslist = array();
        $mobileplugins = self::get_plugins_supporting_mobile();
        foreach ($mobileplugins as $plugin) {
            $displayname = core_plugin_manager::instance()->plugin_name($plugin['component']) . " - " . $plugin['addon'];
            $remoteaddonslist['sitePlugin_' . $plugin['component'] . '_' . $plugin['addon']] = $displayname;

        }

        // Display blocks.
        $availableblocks = core_plugin_manager::instance()->get_plugins_of_type('block');
        $courseblocks = array();
        $appsupportedblocks = array(
            'activity_modules' => 'CoreBlockDelegate_AddonBlockActivityModules',
            'site_main_menu' => 'CoreBlockDelegate_AddonBlockSiteMainMenu',
            'myoverview' => 'CoreBlockDelegate_AddonBlockMyOverview',
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
            'glossary_random' => 'CoreBlockDelegate_AddonBlockGlossaryRandom',
            'html' => 'CoreBlockDelegate_AddonBlockHtml',
            'lp' => 'CoreBlockDelegate_AddonBlockLp',
            'news_items' => 'CoreBlockDelegate_AddonBlockNewsItems',
            'online_users' => 'CoreBlockDelegate_AddonBlockOnlineUsers',
            'selfcompletion' => 'CoreBlockDelegate_AddonBlockSelfCompletion',
            'tags' => 'CoreBlockDelegate_AddonBlockTags',
        );

        foreach ($availableblocks as $block) {
            if (isset($appsupportedblocks[$block->name])) {
                $courseblocks[$appsupportedblocks[$block->name]] = $block->displayname;
            }
        }
        asort($courseblocks);

        $features = array(
            "$general" => array(
                'NoDelegate_CoreOffline' => new lang_string('offlineuse', 'tool_mobile'),
                'NoDelegate_SiteBlocks' => new lang_string('blocks'),
                'NoDelegate_CoreComments' => new lang_string('comments'),
                'NoDelegate_CoreRating' => new lang_string('ratings', 'rating'),
                'NoDelegate_CoreTag' => new lang_string('tags'),
                '$mmLoginEmailSignup' => new lang_string('startsignup'),
                'NoDelegate_ResponsiveMainMenuItems' => new lang_string('responsivemainmenuitems', 'tool_mobile'),
            ),
            "$mainmenu" => array(
                '$mmSideMenuDelegate_mmaFrontpage' => new lang_string('sitehome'),
                '$mmSideMenuDelegate_mmCourses' => new lang_string('mycourses'),
                'CoreMainMenuDelegate_CoreCoursesDashboard' => new lang_string('myhome'),
                '$mmSideMenuDelegate_mmaCalendar' => new lang_string('calendar', 'calendar'),
                '$mmSideMenuDelegate_mmaNotifications' => new lang_string('notifications', 'message'),
                '$mmSideMenuDelegate_mmaMessages' => new lang_string('messages', 'message'),
                '$mmSideMenuDelegate_mmaGrades' => new lang_string('grades', 'grades'),
                '$mmSideMenuDelegate_mmaCompetency' => new lang_string('myplans', 'tool_lp'),
                'CoreMainMenuDelegate_AddonBlog' => new lang_string('blog', 'blog'),
                '$mmSideMenuDelegate_mmaFiles' => new lang_string('files'),
                '$mmSideMenuDelegate_website' => new lang_string('webpage'),
                '$mmSideMenuDelegate_help' => new lang_string('help'),
            ),
            "$course" => array(
                'NoDelegate_CourseBlocks' => new lang_string('blocks'),
                'CoreCourseOptionsDelegate_AddonBlog' => new lang_string('blog', 'blog'),
                '$mmCoursesDelegate_search' => new lang_string('search'),
                '$mmCoursesDelegate_mmaCompetency' => new lang_string('competencies', 'competency'),
                '$mmCoursesDelegate_mmaParticipants' => new lang_string('participants'),
                '$mmCoursesDelegate_mmaGrades' => new lang_string('grades', 'grades'),
                '$mmCoursesDelegate_mmaCourseCompletion' => new lang_string('coursecompletion', 'completion'),
                '$mmCoursesDelegate_mmaNotes' => new lang_string('notes', 'notes'),
                'NoDelegate_CoreCourseDownload' => new lang_string('downloadcourse', 'tool_mobile'),
                'NoDelegate_CoreCoursesDownload' => new lang_string('downloadcourses', 'tool_mobile'),
            ),
            "$user" => array(
                'CoreUserDelegate_AddonBlog:blogs' => new lang_string('blog', 'blog'),
                '$mmUserDelegate_mmaBadges' => new lang_string('badges', 'badges'),
                '$mmUserDelegate_mmaCompetency:learningPlan' => new lang_string('competencies', 'competency'),
                '$mmUserDelegate_mmaCourseCompletion:viewCompletion' => new lang_string('coursecompletion', 'completion'),
                '$mmUserDelegate_mmaGrades:viewGrades' => new lang_string('grades', 'grades'),
                '$mmUserDelegate_mmaMessages:sendMessage' => new lang_string('sendmessage', 'message'),
                '$mmUserDelegate_mmaMessages:addContact' => new lang_string('addcontact', 'message'),
                '$mmUserDelegate_mmaMessages:blockContact' => new lang_string('blockcontact', 'message'),
                '$mmUserDelegate_mmaNotes:addNote' => new lang_string('addnewnote', 'notes'),
                '$mmUserDelegate_picture' => new lang_string('userpic'),
            ),
            "$files" => array(
                'files_privatefiles' => new lang_string('privatefiles'),
                'files_sitefiles' => new lang_string('sitefiles'),
                'files_upload' => new lang_string('upload'),
            ),
            "$modules" => $coursemodules,
            "$blocks" => $courseblocks,
        );

        if (!empty($remoteaddonslist)) {
            $features["$remoteaddons"] = $remoteaddonslist;
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

        $warnings = array();

        $curl = new curl();
        // Return certificate information and verify the certificate.
        $curl->setopt(array('CURLOPT_CERTINFO' => 1, 'CURLOPT_SSL_VERIFYPEER' => true));
        $httpswwwroot = str_replace('http:', 'https:', $CFG->wwwroot); // Force https url.
        // Check https using a page not redirecting or returning exceptions.
        $curl->head($httpswwwroot . "/$CFG->admin/tool/mobile/mobile.webmanifest.php");
        $info = $curl->get_info();

        // First of all, check the server certificate (if any).
        if (empty($info['http_code']) or ($info['http_code'] >= 400)) {
            $warnings[] = ['nohttpsformobilewarning', 'admin'];
        } else {
            // Check the certificate is not self-signed or has an untrusted-root.
            // This may be weak in some scenarios (when the curl SSL verifier is outdated).
            if (empty($info['certinfo'])) {
                $warnings[] = ['selfsignedoruntrustedcertificatewarning', 'tool_mobile'];
            } else {
                $timenow = time();
                $expectedissuer = null;
                foreach ($info['certinfo'] as $cert) {
                    // Check if the signature algorithm is weak (Android won't work with SHA-1).
                    if ($cert['Signature Algorithm'] == 'sha1WithRSAEncryption' || $cert['Signature Algorithm'] == 'sha1WithRSA') {
                        $warnings[] = ['insecurealgorithmwarning', 'tool_mobile'];
                    }
                    // Check certificate start date.
                    if (strtotime($cert['Start date']) > $timenow) {
                        $warnings[] = ['invalidcertificatestartdatewarning', 'tool_mobile'];
                    }
                    // Check certificate end date.
                    if (strtotime($cert['Expire date']) < $timenow) {
                        $warnings[] = ['invalidcertificateexpiredatewarning', 'tool_mobile'];
                    }
                    // Check the chain.
                    if ($expectedissuer !== null) {
                        if ($expectedissuer !== $cert['Subject'] || $cert['Subject'] === $cert['Issuer']) {
                            $warnings[] = ['invalidcertificatechainwarning', 'tool_mobile'];
                        }
                    }
                    $expectedissuer = $cert['Issuer'];
                }
            }
        }
        // Now check typical configuration problems.
        if ((int) $CFG->userquota === PHP_INT_MAX) {
            // In old Moodle version was a text so was possible to have numeric values > PHP_INT_MAX.
            $warnings[] = ['invaliduserquotawarning', 'tool_mobile'];
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
}
