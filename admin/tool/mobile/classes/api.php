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

/**
 * API exposed by tool_mobile, to be used mostly by external functions.
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

        // Check if we can return this from cache.
        $cache = \cache::make('tool_mobile', 'plugininfo');
        $pluginsinfo = $cache->get('mobileplugins');
        if ($pluginsinfo !== false) {
            return (array)$pluginsinfo;
        }

        $pluginsinfo = [];
        $plugintypes = core_component::get_plugin_types();

        foreach ($plugintypes as $plugintype => $unused) {
            // We need to include files here.
            $pluginswithfile = core_component::get_plugin_list_with_file($plugintype, 'db' . DIRECTORY_SEPARATOR . 'mobile.php');
            foreach ($pluginswithfile as $plugin => $notused) {
                $path = core_component::get_plugin_directory($plugintype, $plugin);
                $component = $plugintype . '_' . $plugin;
                $version = get_component_version($component);

                require("$path/db/mobile.php");
                foreach ($addons as $addonname => $addoninfo) {
                    $plugininfo = array(
                        'component' => $component,
                        'version' => $version,
                        'addon' => $addonname,
                        'dependencies' => !empty($addoninfo['dependencies']) ? $addoninfo['dependencies'] : array(),
                        'fileurl' => '',
                        'filehash' => '',
                        'filesize' => 0
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

        $cache->set('mobileplugins', $pluginsinfo);

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
            'httpswwwroot' => $CFG->httpswwwroot,
            'sitename' => external_format_string($SITE->fullname, $context->id, true),
            'guestlogin' => $CFG->guestloginbutton,
            'rememberusername' => $CFG->rememberusername,
            'authloginviaemail' => $CFG->authloginviaemail,
            'registerauth' => $CFG->registerauth,
            'forgottenpasswordurl' => $CFG->forgottenpasswordurl,
            'authinstructions' => $authinstructions,
            'authnoneenabled' => (int) is_enabled_auth('none'),
            'enablewebservices' => $CFG->enablewebservices,
            'enablemobilewebservice' => $CFG->enablemobilewebservice,
            'maintenanceenabled' => $CFG->maintenance_enabled,
            'maintenancemessage' => $maintenancemessage,
        );

        $typeoflogin = get_config('tool_mobile', 'typeoflogin');
        // Not found, edge case.
        if ($typeoflogin === false) {
            $typeoflogin = self::LOGIN_VIA_APP; // Defaults to via app.
        }
        $settings['typeoflogin'] = $typeoflogin;

        // Check if the user can sign-up to return the launch URL in that case.
        $cansignup = signup_is_enabled();

        if ($typeoflogin == self::LOGIN_VIA_BROWSER or
                $typeoflogin == self::LOGIN_VIA_EMBEDDED_BROWSER or
                $cansignup) {
            $url = new moodle_url("/$CFG->admin/tool/mobile/launch.php");
            $settings['launchurl'] = $url->out(false);
        }

        if ($logourl = $OUTPUT->get_logo_url()) {
            $settings['logourl'] = $logourl->out(false);
        }
        if ($compactlogourl = $OUTPUT->get_compact_logo_url()) {
            $settings['compactlogourl'] = $compactlogourl->out(false);
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
            $settings->numsections = course_get_format($SITE)->get_course()->numsections;
            $settings->newsitems = $SITE->newsitems;
            $settings->commentsperpage = $CFG->commentsperpage;

            // Now, admin settings.
            if ($isadmin) {
                $settings->defaultfrontpageroleid = $CFG->defaultfrontpageroleid;
            }
        }

        if (empty($section) or $section == 'sitepolicies') {
            $settings->disableuserimages = $CFG->disableuserimages;
        }

        if (empty($section) or $section == 'gradessettings') {
            require_once($CFG->dirroot . '/user/lib.php');
            $settings->mygradesurl = user_mygrades_url()->out(false);
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
}
