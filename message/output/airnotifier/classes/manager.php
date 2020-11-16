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
 * Airnotifier manager class
 *
 * @package    message_airnotifier
 * @category   external
 * @copyright  2012 Jerome Mouneyrac <jerome@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.7
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Airnotifier helper manager class
 *
 * @copyright  2012 Jerome Mouneyrac <jerome@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_airnotifier_manager {

    /** @var string The Airnotifier public instance URL */
    const AIRNOTIFIER_PUBLICURL = 'https://messages.moodle.net';

    /**
     * Include the relevant javascript and language strings for the device
     * toolbox YUI module
     *
     * @return bool
     */
    public function include_device_ajax() {
        global $PAGE, $CFG;

        $config = new stdClass();
        $config->resturl = '/message/output/airnotifier/rest.php';
        $config->pageparams = array();

        // Include toolboxes.
        $PAGE->requires->yui_module('moodle-message_airnotifier-toolboxes', 'M.message.init_device_toolbox', array(array(
                'ajaxurl' => $config->resturl,
                'config' => $config,
                ))
        );

        // Required strings for the javascript.
        $PAGE->requires->strings_for_js(array('deletecheckdevicename'), 'message_airnotifier');
        $PAGE->requires->strings_for_js(array('show', 'hide'), 'moodle');

        return true;
    }

    /**
     * Return the user devices for a specific app.
     *
     * @param string $appname the app name .
     * @param int $userid if empty take the current user.
     * @return array all the devices
     */
    public function get_user_devices($appname, $userid = null) {
        global $USER, $DB;

        if (empty($userid)) {
            $userid = $USER->id;
        }

        $devices = array();

        $params = array('appid' => $appname, 'userid' => $userid);

        // First, we look all the devices registered for this user in the Moodle core.
        // We are going to allow only ios devices (since these are the ones that supports PUSH notifications).
        $userdevices = $DB->get_records('user_devices', $params);
        foreach ($userdevices as $device) {
            if (core_text::strtolower($device->platform)) {
                // Check if the device is known by airnotifier.
                if (!$airnotifierdev = $DB->get_record('message_airnotifier_devices',
                        array('userdeviceid' => $device->id))) {

                    // We have to create the device token in airnotifier.
                    if (! $this->create_token($device->pushid, $device->platform)) {
                        continue;
                    }

                    $airnotifierdev = new stdClass;
                    $airnotifierdev->userdeviceid = $device->id;
                    $airnotifierdev->enable = 1;
                    $airnotifierdev->id = $DB->insert_record('message_airnotifier_devices', $airnotifierdev);
                }
                $device->id = $airnotifierdev->id;
                $device->enable = $airnotifierdev->enable;
                $devices[] = $device;
            }
        }

        return $devices;
    }

    /**
     * Request and access key to Airnotifier
     *
     * @return mixed The access key or false in case of error
     */
    public function request_accesskey() {
        global $CFG, $USER;

        require_once($CFG->libdir . '/filelib.php');

        // Sending the request access key request to Airnotifier.
        $serverurl = $CFG->airnotifierurl . ':' . $CFG->airnotifierport . '/accesskeys/';
        // We use an APP Key "none", it can be anything.
        $header = array('Accept: application/json', 'X-AN-APP-NAME: ' . $CFG->airnotifierappname,
            'X-AN-APP-KEY: none');
        $curl = new curl();
        $curl->setHeader($header);

        // Site ids are stored as secrets in md5 in the Moodle public hub.
        $params = array(
            'url' => $CFG->wwwroot,
            'siteid' => md5($CFG->siteidentifier),
            'contact' => $USER->email,
            'description' => $CFG->wwwroot
            );
        $resp = $curl->post($serverurl, $params);

        if ($key = json_decode($resp, true)) {
            if (!empty($key['accesskey'])) {
                return $key['accesskey'];
            }
        }
        debugging("Unexpected response from the Airnotifier server: $resp");
        return false;
    }

    /**
     * Create a device token in the Airnotifier instance
     * @param string $token The token to be created
     * @param string $deviceplatform The device platform (Android, iOS, iOS-fcm, etc...)
     * @return bool True if all was right
     */
    private function create_token($token, $deviceplatform = '') {
        global $CFG;

        if (!$this->is_system_configured()) {
            return false;
        }

        require_once($CFG->libdir . '/filelib.php');

        $serverurl = $CFG->airnotifierurl . ':' . $CFG->airnotifierport . '/tokens/' . $token;
        $header = array('Accept: application/json', 'X-AN-APP-NAME: ' . $CFG->airnotifierappname,
            'X-AN-APP-KEY: ' . $CFG->airnotifieraccesskey);
        $curl = new curl;
        $curl->setHeader($header);
        $params = [];
        if (!empty($deviceplatform)) {
            $params["device"] = $deviceplatform;
        }
        $resp = $curl->post($serverurl, $params);

        if ($token = json_decode($resp, true)) {
            if (!empty($token['status'])) {
                return $token['status'] == 'ok' || $token['status'] == 'token exists';
            }
        }
        debugging("Unexpected response from the Airnotifier server: $resp");
        return false;
    }

    /**
     * Tests whether the airnotifier settings have been configured
     * @return boolean true if airnotifier is configured
     */
    public function is_system_configured() {
        global $CFG;

        return (!empty($CFG->airnotifierurl) && !empty($CFG->airnotifierport) &&
                !empty($CFG->airnotifieraccesskey)  && !empty($CFG->airnotifierappname) &&
                !empty($CFG->airnotifiermobileappname));
    }

    /**
     * Enables or disables a registered user device so it can receive Push notifications
     *
     * @param  int $deviceid the device id
     * @param  bool $enable  true to enable it, false to disable it
     * @return bool true if the device was enabled, false in case of error
     * @since  Moodle 3.2
     */
    public static function enable_device($deviceid, $enable) {
        global $DB, $USER;

        if (!$device = $DB->get_record('message_airnotifier_devices', array('id' => $deviceid), '*')) {
            return false;
        }

        // Check that the device belongs to the current user.
        if (!$userdevice = $DB->get_record('user_devices', array('id' => $device->userdeviceid, 'userid' => $USER->id), '*')) {
            return false;
        }

        $device->enable = $enable;
        return $DB->update_record('message_airnotifier_devices', $device);
    }

    /**
     * Check the system configuration to detect possible issues.
     *
     * @return array result checks
     */
    public function check_configuration(): array {
        global $CFG, $DB;

        $results = [];
        // Check Mobile services enabled.
        $summary = html_writer::link((new moodle_url('/admin/settings.php', ['section' => 'mobilesettings'])),
                get_string('enablemobilewebservice', 'admin'));
        if (empty($CFG->enablewebservices) || empty($CFG->enablemobilewebservice)) {
            $results[] = new core\check\result(core\check\result::CRITICAL, $summary, get_string('enablewsdescription', 'webservice'));
        } else {
            $results[] = new core\check\result(core\check\result::OK, $summary, get_string('enabled', 'admin'));
        }

        // Check Mobile notifications enabled.
        require_once($CFG->dirroot . '/message/lib.php');
        $processors = get_message_processors();
        $enabled = false;
        foreach ($processors as $processor => $status) {
            if ($processor == 'airnotifier' && $status->enabled) {
                $enabled = true;
            }
        }

        $summary = html_writer::link((new moodle_url('/admin/message.php')), get_string('enableprocessor', 'message_airnotifier'));
        if ($enabled) {
            $results[] = new core\check\result(core\check\result::OK, $summary, get_string('enabled', 'admin'));
        } else {
            $results[] = new core\check\result(core\check\result::CRITICAL, $summary,
                get_string('mobilenotificationsdisabledwarning', 'tool_mobile'));
        }

        // Check Mobile notifications configuration is ok.
        $summary = html_writer::link((new moodle_url('/admin/settings.php', ['section' => 'messagesettingairnotifier'])),
            get_string('notificationsserverconfiguration', 'message_airnotifier'));
        if ($this->is_system_configured()) {
            $results[] = new core\check\result(core\check\result::OK, $summary, get_string('configured', 'message_airnotifier'));
        } else {
            $results[] = new core\check\result(core\check\result::ERROR, $summary, get_string('notconfigured', 'message_airnotifier'));
        }

        // Check settings properly formatted. Only display in case of errors.
        $settingstocheck = ['airnotifierappname', 'airnotifiermobileappname'];
        if ($this->is_system_configured()) {
            foreach ($settingstocheck as $setting) {
                if ($CFG->$setting != trim($CFG->$setting)) {
                    $summary = html_writer::link((new moodle_url('/admin/settings.php', ['section' => 'messagesettingairnotifier'])),
                        get_string('notificationsserverconfiguration', 'message_airnotifier'));

                    $results[] = new core\check\result(core\check\result::ERROR, $summary,
                        get_string('airnotifierfielderror', 'message_airnotifier', get_string($setting, 'message_airnotifier')));
                }
            }
        }

        // Check connectivity with Airnotifier.
        $url = $CFG->airnotifierurl . ':' . $CFG->airnotifierport;
        $curl = new \curl();
        $curl->setopt(['CURLOPT_TIMEOUT' => 5, 'CURLOPT_CONNECTTIMEOUT' => 5]);
        $curl->get($url);
        $info = $curl->get_info();

        $summary = html_writer::link($url, get_string('airnotifierurl', 'message_airnotifier'));
        if (!empty($info['http_code']) && ($info['http_code'] == 200 || $info['http_code'] == 302)) {
            $results[] = new core\check\result(core\check\result::OK, $summary, get_string('online', 'message'));
        } else {
            $details = get_string('serverconnectivityerror', 'message_airnotifier', $url);
            $curlerrno = $curl->get_errno();
            if (!empty($curlerrno)) {
                $details .= ' CURL ERROR: ' . $curlerrno . ' - ' . $curl->error;
            }
            $results[] = new core\check\result(core\check\result::ERROR, $summary, $details);
        }

        // Check access key by trying to create an invalid token.
        $settingsurl = new moodle_url('/admin/settings.php', ['section' => 'messagesettingairnotifier']);
        $summary = html_writer::link($settingsurl, get_string('airnotifieraccesskey', 'message_airnotifier'));
        if (!empty($CFG->airnotifieraccesskey)) {
            $url = $CFG->airnotifierurl . ':' . $CFG->airnotifierport . '/tokens/testtoken';
            $header = ['Accept: application/json', 'X-AN-APP-NAME: ' . $CFG->airnotifierappname,
                'X-AN-APP-KEY: ' . $CFG->airnotifieraccesskey];
            $curl->setHeader($header);
            $response = $curl->post($url);
            $info = $curl->get_info();

            if ($curlerrno = $curl->get_errno()) {
                $details = get_string('serverconnectivityerror', 'message_airnotifier', $url);
                $details .= ' CURL ERROR: ' . $curlerrno . ' - ' . $curl->error;
                $results[] = new core\check\result(core\check\result::ERROR, $summary, $details);
            } else if (!empty($info['http_code']) && $info['http_code'] == 400 && $key = json_decode($response, true)) {
                if ($key['error'] == 'Invalid access key') {
                    $results[] = new core\check\result(core\check\result::ERROR, $summary, $key['error']);
                } else {
                    $results[] = new core\check\result(core\check\result::OK, $summary, get_string('enabled', 'admin'));
                }
            }
        } else {
            $results[] = new core\check\result(core\check\result::ERROR, $summary,
                get_string('requestaccesskey', 'message_airnotifier'));
        }

        // Check default preferences.
        $preferences = (array) get_message_output_default_preferences();
        $providerscount = 0;
        $providersconfigured = 0;
        foreach ($preferences as $prefname => $prefval) {
            if (strpos($prefname, 'message_provider') === 0) {
                $providerscount++;
                if (strpos($prefval, 'airnotifier') !== false) {
                    $providersconfigured++;
                }
            }
        }

        $summary = html_writer::link((new moodle_url('/admin/message.php')), get_string('managemessageoutputs', 'message'));
        if ($providersconfigured == 0) {
            $results[] = new core\check\result(core\check\result::ERROR, $summary,
                get_string('messageprovidersempty', 'message_airnotifier'));
        } else if ($providersconfigured / $providerscount < 0.25) {
            // Less than a 25% of the providers are enabled by default for users.
            $results[] = new core\check\result(core\check\result::WARNING, $summary,
                get_string('messageproviderslow', 'message_airnotifier'));
        } else {
            $results[] = new core\check\result(core\check\result::OK, $summary, get_string('configured', 'message_airnotifier'));
        }

        // Check user devices from last month.
        $recentdevicescount = $DB->count_records_select('user_devices', 'appid = ? AND timemodified > ?',
            [$CFG->airnotifiermobileappname, time() - (WEEKSECS * 4)]);

        $summary = get_string('userdevices', 'message_airnotifier');
        if (!empty($recentdevicescount)) {
            $results[] = new core\check\result(core\check\result::OK, $summary, get_string('configured', 'message_airnotifier'));
        } else {
            $results[] = new core\check\result(core\check\result::ERROR, $summary, get_string('nodevices', 'message_airnotifier'));
        }
        return $results;
    }

    /**
     * Send a test notification to the given user.
     *
     * @param  stdClass $user user object
     */
    public function send_test_notification(stdClass $user): void {
        global $CFG;
        require_once($CFG->dirroot . '/message/output/airnotifier/message_output_airnotifier.php');

        $data = new stdClass;
        $data->userto = clone $user;
        $data->subject = 'Push Notification Test';
        $data->fullmessage = 'This is a test message send at: ' . userdate(time());
        $data->notification = 1;

        // The send_message method always return true, so it does not make sense to return anything.
        $airnotifier = new message_output_airnotifier();
        $airnotifier->send_message($data);
    }

    /**
     * Check whether the given user has enabled devices or not for the given app.
     *
     * @param  string $appname the app to check
     * @param  int $userid the user to check the devices for (empty for current user)
     * @return bool true when the user has enabled devices, false otherwise
     */
    public function has_enabled_devices(string $appname, int $userid = null): bool {
        $enableddevices = false;
        $devices = $this->get_user_devices($appname, $userid);

        foreach ($devices as $device) {
            if (!$device->enable) {
                continue;
            }
            $enableddevices = true;
            break;
        }
        return $enableddevices;
    }
}
