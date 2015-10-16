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
 * Defines classes used for updates.
 *
 * @package    core
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\update;

use html_writer, coding_exception, core_component;

defined('MOODLE_INTERNAL') || die();

/**
 * Singleton class that handles checking for available updates
 */
class checker {

    /** @var \core\update\checker holds the singleton instance */
    protected static $singletoninstance;
    /** @var null|int the timestamp of when the most recent response was fetched */
    protected $recentfetch = null;
    /** @var null|array the recent response from the update notification provider */
    protected $recentresponse = null;
    /** @var null|string the numerical version of the local Moodle code */
    protected $currentversion = null;
    /** @var null|string the release info of the local Moodle code */
    protected $currentrelease = null;
    /** @var null|string branch of the local Moodle code */
    protected $currentbranch = null;
    /** @var array of (string)frankestyle => (string)version list of additional plugins deployed at this site */
    protected $currentplugins = array();

    /**
     * Direct initiation not allowed, use the factory method {@link self::instance()}
     */
    protected function __construct() {
    }

    /**
     * Sorry, this is singleton
     */
    protected function __clone() {
    }

    /**
     * Factory method for this class
     *
     * @return \core\update\checker the singleton instance
     */
    public static function instance() {
        if (is_null(self::$singletoninstance)) {
            self::$singletoninstance = new self();
        }
        return self::$singletoninstance;
    }

    /**
     * Reset any caches
     * @param bool $phpunitreset
     */
    public static function reset_caches($phpunitreset = false) {
        if ($phpunitreset) {
            self::$singletoninstance = null;
        }
    }

    /**
     * Is checking for available updates enabled?
     *
     * The feature is enabled unless it is prohibited via config.php.
     * If enabled, the button for manual checking for available updates is
     * displayed at admin screens. To perform scheduled checks for updates
     * automatically, the admin setting $CFG->updateautocheck has to be enabled.
     *
     * @return bool
     */
    public function enabled() {
        global $CFG;

        return empty($CFG->disableupdatenotifications);
    }

    /**
     * Returns the timestamp of the last execution of {@link fetch()}
     *
     * @return int|null null if it has never been executed or we don't known
     */
    public function get_last_timefetched() {

        $this->restore_response();

        if (!empty($this->recentfetch)) {
            return $this->recentfetch;

        } else {
            return null;
        }
    }

    /**
     * Fetches the available update status from the remote site
     *
     * @throws checker_exception
     */
    public function fetch() {

        $response = $this->get_response();
        $this->validate_response($response);
        $this->store_response($response);

        // We need to reset plugin manager's caches - the currently existing
        // singleton is not aware of eventually available updates we just fetched.
        \core_plugin_manager::reset_caches();
    }

    /**
     * Returns the available update information for the given component
     *
     * This method returns null if the most recent response does not contain any information
     * about it. The returned structure is an array of available updates for the given
     * component. Each update info is an object with at least one property called
     * 'version'. Other possible properties are 'release', 'maturity', 'url' and 'downloadurl'.
     *
     * For the 'core' component, the method returns real updates only (those with higher version).
     * For all other components, the list of all known remote updates is returned and the caller
     * (usually the {@link core_plugin_manager}) is supposed to make the actual comparison of versions.
     *
     * @param string $component frankenstyle
     * @param array $options with supported keys 'minmaturity' and/or 'notifybuilds'
     * @return null|array null or array of \core\update\info objects
     */
    public function get_update_info($component, array $options = array()) {

        if (!isset($options['minmaturity'])) {
            $options['minmaturity'] = 0;
        }

        if (!isset($options['notifybuilds'])) {
            $options['notifybuilds'] = false;
        }

        if ($component === 'core') {
            $this->load_current_environment();
        }

        $this->restore_response();

        if (empty($this->recentresponse['updates'][$component])) {
            return null;
        }

        $updates = array();
        foreach ($this->recentresponse['updates'][$component] as $info) {
            $update = new info($component, $info);
            if (isset($update->maturity) and ($update->maturity < $options['minmaturity'])) {
                continue;
            }
            if ($component === 'core') {
                if ($update->version <= $this->currentversion) {
                    continue;
                }
                if (empty($options['notifybuilds']) and $this->is_same_release($update->release)) {
                    continue;
                }
            }
            $updates[] = $update;
        }

        if (empty($updates)) {
            return null;
        }

        return $updates;
    }

    /**
     * The method being run via cron.php
     */
    public function cron() {
        global $CFG;

        if (!$this->enabled() or !$this->cron_autocheck_enabled()) {
            $this->cron_mtrace('Automatic check for available updates not enabled, skipping.');
            return;
        }

        $now = $this->cron_current_timestamp();

        if ($this->cron_has_fresh_fetch($now)) {
            $this->cron_mtrace('Recently fetched info about available updates is still fresh enough, skipping.');
            return;
        }

        if ($this->cron_has_outdated_fetch($now)) {
            $this->cron_mtrace('Outdated or missing info about available updates, forced fetching ... ', '');
            $this->cron_execute();
            return;
        }

        $offset = $this->cron_execution_offset();
        $start = mktime(1, 0, 0, date('n', $now), date('j', $now), date('Y', $now)); // 01:00 AM today local time
        if ($now > $start + $offset) {
            $this->cron_mtrace('Regular daily check for available updates ... ', '');
            $this->cron_execute();
            return;
        }
    }

    /* === End of public API === */

    /**
     * Makes cURL request to get data from the remote site
     *
     * @return string raw request result
     * @throws checker_exception
     */
    protected function get_response() {
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');

        $curl = new \curl(array('proxy' => true));
        $response = $curl->post($this->prepare_request_url(), $this->prepare_request_params(), $this->prepare_request_options());
        $curlerrno = $curl->get_errno();
        if (!empty($curlerrno)) {
            throw new checker_exception('err_response_curl', 'cURL error '.$curlerrno.': '.$curl->error);
        }
        $curlinfo = $curl->get_info();
        if ($curlinfo['http_code'] != 200) {
            throw new checker_exception('err_response_http_code', $curlinfo['http_code']);
        }
        return $response;
    }

    /**
     * Makes sure the response is valid, has correct API format etc.
     *
     * @param string $response raw response as returned by the {@link self::get_response()}
     * @throws checker_exception
     */
    protected function validate_response($response) {

        $response = $this->decode_response($response);

        if (empty($response)) {
            throw new checker_exception('err_response_empty');
        }

        if (empty($response['status']) or $response['status'] !== 'OK') {
            throw new checker_exception('err_response_status', $response['status']);
        }

        if (empty($response['apiver']) or $response['apiver'] !== '1.3') {
            throw new checker_exception('err_response_format_version', $response['apiver']);
        }

        if (empty($response['forbranch']) or $response['forbranch'] !== moodle_major_version(true)) {
            throw new checker_exception('err_response_target_version', $response['forbranch']);
        }
    }

    /**
     * Decodes the raw string response from the update notifications provider
     *
     * @param string $response as returned by {@link self::get_response()}
     * @return array decoded response structure
     */
    protected function decode_response($response) {
        return json_decode($response, true);
    }

    /**
     * Stores the valid fetched response for later usage
     *
     * This implementation uses the config_plugins table as the permanent storage.
     *
     * @param string $response raw valid data returned by {@link self::get_response()}
     */
    protected function store_response($response) {

        set_config('recentfetch', time(), 'core_plugin');
        set_config('recentresponse', $response, 'core_plugin');

        if (defined('CACHE_DISABLE_ALL') and CACHE_DISABLE_ALL) {
            // Very nasty hack to work around cache coherency issues on admin/index.php?cache=0 page,
            // we definitely need to keep caches in sync when writing into DB at all times!
            \cache_helper::purge_all(true);
        }

        $this->restore_response(true);
    }

    /**
     * Loads the most recent raw response record we have fetched
     *
     * After this method is called, $this->recentresponse is set to an array. If the
     * array is empty, then either no data have been fetched yet or the fetched data
     * do not have expected format (and thence they are ignored and a debugging
     * message is displayed).
     *
     * This implementation uses the config_plugins table as the permanent storage.
     *
     * @param bool $forcereload reload even if it was already loaded
     */
    protected function restore_response($forcereload = false) {

        if (!$forcereload and !is_null($this->recentresponse)) {
            // We already have it, nothing to do.
            return;
        }

        $config = get_config('core_plugin');

        if (!empty($config->recentresponse) and !empty($config->recentfetch)) {
            try {
                $this->validate_response($config->recentresponse);
                $this->recentfetch = $config->recentfetch;
                $this->recentresponse = $this->decode_response($config->recentresponse);
            } catch (checker_exception $e) {
                // The server response is not valid. Behave as if no data were fetched yet.
                // This may happen when the most recent update info (cached locally) has been
                // fetched with the previous branch of Moodle (like during an upgrade from 2.x
                // to 2.y) or when the API of the response has changed.
                $this->recentresponse = array();
            }

        } else {
            $this->recentresponse = array();
        }
    }

    /**
     * Compares two raw {@link $recentresponse} records and returns the list of changed updates
     *
     * This method is used to populate potential update info to be sent to site admins.
     *
     * @param array $old
     * @param array $new
     * @throws checker_exception
     * @return array parts of $new['updates'] that have changed
     */
    protected function compare_responses(array $old, array $new) {

        if (empty($new)) {
            return array();
        }

        if (!array_key_exists('updates', $new)) {
            throw new checker_exception('err_response_format');
        }

        if (empty($old)) {
            return $new['updates'];
        }

        if (!array_key_exists('updates', $old)) {
            throw new checker_exception('err_response_format');
        }

        $changes = array();

        foreach ($new['updates'] as $newcomponent => $newcomponentupdates) {
            if (empty($old['updates'][$newcomponent])) {
                $changes[$newcomponent] = $newcomponentupdates;
                continue;
            }
            foreach ($newcomponentupdates as $newcomponentupdate) {
                $inold = false;
                foreach ($old['updates'][$newcomponent] as $oldcomponentupdate) {
                    if ($newcomponentupdate['version'] == $oldcomponentupdate['version']) {
                        $inold = true;
                    }
                }
                if (!$inold) {
                    if (!isset($changes[$newcomponent])) {
                        $changes[$newcomponent] = array();
                    }
                    $changes[$newcomponent][] = $newcomponentupdate;
                }
            }
        }

        return $changes;
    }

    /**
     * Returns the URL to send update requests to
     *
     * During the development or testing, you can set $CFG->alternativeupdateproviderurl
     * to a custom URL that will be used. Otherwise the standard URL will be returned.
     *
     * @return string URL
     */
    protected function prepare_request_url() {
        global $CFG;

        if (!empty($CFG->config_php_settings['alternativeupdateproviderurl'])) {
            return $CFG->config_php_settings['alternativeupdateproviderurl'];
        } else {
            return 'https://download.moodle.org/api/1.3/updates.php';
        }
    }

    /**
     * Sets the properties currentversion, currentrelease, currentbranch and currentplugins
     *
     * @param bool $forcereload
     */
    protected function load_current_environment($forcereload=false) {
        global $CFG;

        if (!is_null($this->currentversion) and !$forcereload) {
            // Nothing to do.
            return;
        }

        $version = null;
        $release = null;

        require($CFG->dirroot.'/version.php');
        $this->currentversion = $version;
        $this->currentrelease = $release;
        $this->currentbranch = moodle_major_version(true);

        $pluginman = \core_plugin_manager::instance();
        foreach ($pluginman->get_plugins() as $type => $plugins) {
            foreach ($plugins as $plugin) {
                if (!$plugin->is_standard()) {
                    $this->currentplugins[$plugin->component] = $plugin->versiondisk;
                }
            }
        }
    }

    /**
     * Returns the list of HTTP params to be sent to the updates provider URL
     *
     * @return array of (string)param => (string)value
     */
    protected function prepare_request_params() {
        global $CFG;

        $this->load_current_environment();
        $this->restore_response();

        $params = array();
        $params['format'] = 'json';

        if (isset($this->recentresponse['ticket'])) {
            $params['ticket'] = $this->recentresponse['ticket'];
        }

        if (isset($this->currentversion)) {
            $params['version'] = $this->currentversion;
        } else {
            throw new coding_exception('Main Moodle version must be already known here');
        }

        if (isset($this->currentbranch)) {
            $params['branch'] = $this->currentbranch;
        } else {
            throw new coding_exception('Moodle release must be already known here');
        }

        $plugins = array();
        foreach ($this->currentplugins as $plugin => $version) {
            $plugins[] = $plugin.'@'.$version;
        }
        if (!empty($plugins)) {
            $params['plugins'] = implode(',', $plugins);
        }

        return $params;
    }

    /**
     * Returns the list of cURL options to use when fetching available updates data
     *
     * @return array of (string)param => (string)value
     */
    protected function prepare_request_options() {
        $options = array(
            'CURLOPT_SSL_VERIFYHOST' => 2,      // This is the default in {@link curl} class but just in case.
            'CURLOPT_SSL_VERIFYPEER' => true,
        );

        return $options;
    }

    /**
     * Returns the current timestamp
     *
     * @return int the timestamp
     */
    protected function cron_current_timestamp() {
        return time();
    }

    /**
     * Output cron debugging info
     *
     * @see mtrace()
     * @param string $msg output message
     * @param string $eol end of line
     */
    protected function cron_mtrace($msg, $eol = PHP_EOL) {
        mtrace($msg, $eol);
    }

    /**
     * Decide if the autocheck feature is disabled in the server setting
     *
     * @return bool true if autocheck enabled, false if disabled
     */
    protected function cron_autocheck_enabled() {
        global $CFG;

        if (empty($CFG->updateautocheck)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Decide if the recently fetched data are still fresh enough
     *
     * @param int $now current timestamp
     * @return bool true if no need to re-fetch, false otherwise
     */
    protected function cron_has_fresh_fetch($now) {
        $recent = $this->get_last_timefetched();

        if (empty($recent)) {
            return false;
        }

        if ($now < $recent) {
            $this->cron_mtrace('The most recent fetch is reported to be in the future, this is weird!');
            return true;
        }

        if ($now - $recent > 24 * HOURSECS) {
            return false;
        }

        return true;
    }

    /**
     * Decide if the fetch is outadated or even missing
     *
     * @param int $now current timestamp
     * @return bool false if no need to re-fetch, true otherwise
     */
    protected function cron_has_outdated_fetch($now) {
        $recent = $this->get_last_timefetched();

        if (empty($recent)) {
            return true;
        }

        if ($now < $recent) {
            $this->cron_mtrace('The most recent fetch is reported to be in the future, this is weird!');
            return false;
        }

        if ($now - $recent > 48 * HOURSECS) {
            return true;
        }

        return false;
    }

    /**
     * Returns the cron execution offset for this site
     *
     * The main {@link self::cron()} is supposed to run every night in some random time
     * between 01:00 and 06:00 AM (local time). The exact moment is defined by so called
     * execution offset, that is the amount of time after 01:00 AM. The offset value is
     * initially generated randomly and then used consistently at the site. This way, the
     * regular checks against the download.moodle.org server are spread in time.
     *
     * @return int the offset number of seconds from range 1 sec to 5 hours
     */
    protected function cron_execution_offset() {
        global $CFG;

        if (empty($CFG->updatecronoffset)) {
            set_config('updatecronoffset', rand(1, 5 * HOURSECS));
        }

        return $CFG->updatecronoffset;
    }

    /**
     * Fetch available updates info and eventually send notification to site admins
     */
    protected function cron_execute() {

        try {
            $this->restore_response();
            $previous = $this->recentresponse;
            $this->fetch();
            $this->restore_response(true);
            $current = $this->recentresponse;
            $changes = $this->compare_responses($previous, $current);
            $notifications = $this->cron_notifications($changes);
            $this->cron_notify($notifications);
            $this->cron_mtrace('done');
        } catch (checker_exception $e) {
            $this->cron_mtrace('FAILED!');
        }
    }

    /**
     * Given the list of changes in available updates, pick those to send to site admins
     *
     * @param array $changes as returned by {@link self::compare_responses()}
     * @return array of \core\update\info objects to send to site admins
     */
    protected function cron_notifications(array $changes) {
        global $CFG;

        if (empty($changes)) {
            return array();
        }

        $notifications = array();
        $pluginman = \core_plugin_manager::instance();
        $plugins = $pluginman->get_plugins();

        foreach ($changes as $component => $componentchanges) {
            if (empty($componentchanges)) {
                continue;
            }
            $componentupdates = $this->get_update_info($component,
                array('minmaturity' => $CFG->updateminmaturity, 'notifybuilds' => $CFG->updatenotifybuilds));
            if (empty($componentupdates)) {
                continue;
            }
            // Notify only about those $componentchanges that are present in $componentupdates
            // to respect the preferences.
            foreach ($componentchanges as $componentchange) {
                foreach ($componentupdates as $componentupdate) {
                    if ($componentupdate->version == $componentchange['version']) {
                        if ($component == 'core') {
                            // In case of 'core', we already know that the $componentupdate
                            // is a real update with higher version ({@see self::get_update_info()}).
                            // We just perform additional check for the release property as there
                            // can be two Moodle releases having the same version (e.g. 2.4.0 and 2.5dev shortly
                            // after the release). We can do that because we have the release info
                            // always available for the core.
                            if ((string)$componentupdate->release === (string)$componentchange['release']) {
                                $notifications[] = $componentupdate;
                            }
                        } else {
                            // Use the core_plugin_manager to check if the detected $componentchange
                            // is a real update with higher version. That is, the $componentchange
                            // is present in the array of {@link \core\update\info} objects
                            // returned by the plugin's available_updates() method.
                            list($plugintype, $pluginname) = core_component::normalize_component($component);
                            if (!empty($plugins[$plugintype][$pluginname])) {
                                $availableupdates = $plugins[$plugintype][$pluginname]->available_updates();
                                if (!empty($availableupdates)) {
                                    foreach ($availableupdates as $availableupdate) {
                                        if ($availableupdate->version == $componentchange['version']) {
                                            $notifications[] = $componentupdate;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $notifications;
    }

    /**
     * Sends the given notifications to site admins via messaging API
     *
     * @param array $notifications array of \core\update\info objects to send
     */
    protected function cron_notify(array $notifications) {
        global $CFG;

        if (empty($notifications)) {
            $this->cron_mtrace('nothing to notify about. ', '');
            return;
        }

        $admins = get_admins();

        if (empty($admins)) {
            return;
        }

        $this->cron_mtrace('sending notifications ... ', '');

        $text = get_string('updatenotifications', 'core_admin') . PHP_EOL;
        $html = html_writer::tag('h1', get_string('updatenotifications', 'core_admin')) . PHP_EOL;

        $coreupdates = array();
        $pluginupdates = array();

        foreach ($notifications as $notification) {
            if ($notification->component == 'core') {
                $coreupdates[] = $notification;
            } else {
                $pluginupdates[] = $notification;
            }
        }

        if (!empty($coreupdates)) {
            $text .= PHP_EOL . get_string('updateavailable', 'core_admin') . PHP_EOL;
            $html .= html_writer::tag('h2', get_string('updateavailable', 'core_admin')) . PHP_EOL;
            $html .= html_writer::start_tag('ul') . PHP_EOL;
            foreach ($coreupdates as $coreupdate) {
                $html .= html_writer::start_tag('li');
                if (isset($coreupdate->release)) {
                    $text .= get_string('updateavailable_release', 'core_admin', $coreupdate->release);
                    $html .= html_writer::tag('strong', get_string('updateavailable_release', 'core_admin', $coreupdate->release));
                }
                if (isset($coreupdate->version)) {
                    $text .= ' '.get_string('updateavailable_version', 'core_admin', $coreupdate->version);
                    $html .= ' '.get_string('updateavailable_version', 'core_admin', $coreupdate->version);
                }
                if (isset($coreupdate->maturity)) {
                    $text .= ' ('.get_string('maturity'.$coreupdate->maturity, 'core_admin').')';
                    $html .= ' ('.get_string('maturity'.$coreupdate->maturity, 'core_admin').')';
                }
                $text .= PHP_EOL;
                $html .= html_writer::end_tag('li') . PHP_EOL;
            }
            $text .= PHP_EOL;
            $html .= html_writer::end_tag('ul') . PHP_EOL;

            $a = array('url' => $CFG->wwwroot.'/'.$CFG->admin.'/index.php');
            $text .= get_string('updateavailabledetailslink', 'core_admin', $a) . PHP_EOL;
            $a = array('url' => html_writer::link($CFG->wwwroot.'/'.$CFG->admin.'/index.php', $CFG->wwwroot.'/'.$CFG->admin.'/index.php'));
            $html .= html_writer::tag('p', get_string('updateavailabledetailslink', 'core_admin', $a)) . PHP_EOL;

            $text .= PHP_EOL . get_string('updateavailablerecommendation', 'core_admin') . PHP_EOL;
            $html .= html_writer::tag('p', get_string('updateavailablerecommendation', 'core_admin')) . PHP_EOL;
        }

        if (!empty($pluginupdates)) {
            $text .= PHP_EOL . get_string('updateavailableforplugin', 'core_admin') . PHP_EOL;
            $html .= html_writer::tag('h2', get_string('updateavailableforplugin', 'core_admin')) . PHP_EOL;

            $html .= html_writer::start_tag('ul') . PHP_EOL;
            foreach ($pluginupdates as $pluginupdate) {
                $html .= html_writer::start_tag('li');
                $text .= get_string('pluginname', $pluginupdate->component);
                $html .= html_writer::tag('strong', get_string('pluginname', $pluginupdate->component));

                $text .= ' ('.$pluginupdate->component.')';
                $html .= ' ('.$pluginupdate->component.')';

                $text .= ' '.get_string('updateavailable', 'core_plugin', $pluginupdate->version);
                $html .= ' '.get_string('updateavailable', 'core_plugin', $pluginupdate->version);

                $text .= PHP_EOL;
                $html .= html_writer::end_tag('li') . PHP_EOL;
            }
            $text .= PHP_EOL;
            $html .= html_writer::end_tag('ul') . PHP_EOL;

            $a = array('url' => $CFG->wwwroot.'/'.$CFG->admin.'/plugins.php');
            $text .= get_string('updateavailabledetailslink', 'core_admin', $a) . PHP_EOL;
            $a = array('url' => html_writer::link($CFG->wwwroot.'/'.$CFG->admin.'/plugins.php', $CFG->wwwroot.'/'.$CFG->admin.'/plugins.php'));
            $html .= html_writer::tag('p', get_string('updateavailabledetailslink', 'core_admin', $a)) . PHP_EOL;
        }

        $a = array('siteurl' => $CFG->wwwroot);
        $text .= PHP_EOL . get_string('updatenotificationfooter', 'core_admin', $a) . PHP_EOL;
        $a = array('siteurl' => html_writer::link($CFG->wwwroot, $CFG->wwwroot));
        $html .= html_writer::tag('footer', html_writer::tag('p', get_string('updatenotificationfooter', 'core_admin', $a),
            array('style' => 'font-size:smaller; color:#333;')));

        foreach ($admins as $admin) {
            $message = new \stdClass();
            $message->component         = 'moodle';
            $message->name              = 'availableupdate';
            $message->userfrom          = get_admin();
            $message->userto            = $admin;
            $message->subject           = get_string('updatenotificationsubject', 'core_admin', array('siteurl' => $CFG->wwwroot));
            $message->fullmessage       = $text;
            $message->fullmessageformat = FORMAT_PLAIN;
            $message->fullmessagehtml   = $html;
            $message->smallmessage      = get_string('updatenotifications', 'core_admin');
            $message->notification      = 1;
            message_send($message);
        }
    }

    /**
     * Compare two release labels and decide if they are the same
     *
     * @param string $remote release info of the available update
     * @param null|string $local release info of the local code, defaults to $release defined in version.php
     * @return boolean true if the releases declare the same minor+major version
     */
    protected function is_same_release($remote, $local=null) {

        if (is_null($local)) {
            $this->load_current_environment();
            $local = $this->currentrelease;
        }

        $pattern = '/^([0-9\.\+]+)([^(]*)/';

        preg_match($pattern, $remote, $remotematches);
        preg_match($pattern, $local, $localmatches);

        $remotematches[1] = str_replace('+', '', $remotematches[1]);
        $localmatches[1] = str_replace('+', '', $localmatches[1]);

        if ($remotematches[1] === $localmatches[1] and rtrim($remotematches[2]) === rtrim($localmatches[2])) {
            return true;
        } else {
            return false;
        }
    }
}
