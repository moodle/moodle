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

namespace factor_sms\local\smsgateway;

use core\aws\admin_settings_aws_region;
use core\aws\aws_helper;
use factor_sms\event\sms_sent;

/**
 * AWS SNS SMS Gateway class
 *
 * @package     factor_sms
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aws_sns implements gateway_interface {

    /**
     * Create an instance of this class.
     */
    public function __construct() {
        global $CFG;
        require_once($CFG->libdir . '/aws-sdk/src/functions.php');
        require_once($CFG->libdir . '/guzzlehttp/guzzle/src/functions_include.php');
        require_once($CFG->libdir . '/guzzlehttp/promises/src/functions_include.php');
    }

    /**
     * Sends a message using the AWS SNS API
     *
     * @param string $messagecontent the content to send in the SMS message.
     * @param string $phonenumber the destination for the message.
     * @return bool true on message send success
     */
    public function send_sms_message(string $messagecontent, string $phonenumber): bool {
        global $SITE, $USER;

        $config = get_config('factor_sms');

        // Setup client params and instantiate client.
        $params = [
            'version' => 'latest',
            'region' => $config->api_region,
            'http' => ['proxy' => aws_helper::get_proxy_string()],
        ];
        if (!$config->usecredchain) {
            $params['credentials'] = [
                'key' => $config->api_key,
                'secret' => $config->api_secret,
            ];
        }
        $client = new \Aws\Sns\SnsClient($params);

        // Transform the phone number to international standard.
        $phonenumber = \factor_sms\helper::format_number($phonenumber);

        // Setup the sender information.
        $senderid = $SITE->shortname;
        // Remove spaces and non-alphanumeric characters from ID.
        $senderid = preg_replace("/[^A-Za-z0-9]/", '', trim($senderid));
        // We have to truncate the senderID to 11 chars.
        $senderid = substr($senderid, 0, 11);

        if (defined('BEHAT_SITE_RUNNING')) {
            // Fake SMS sending in behat.
            return true;
        }

        try {
            // These messages need to be transactional.
            $client->SetSMSAttributes([
                'attributes' => [
                    'DefaultSMSType' => 'Transactional',
                    'DefaultSenderID' => $senderid,
                ],
            ]);

            // Actually send the message.
            $result = $client->publish([
                'Message' => $messagecontent,
                'PhoneNumber' => $phonenumber,
            ]);

            $data = [
                'relateduserid' => null,
                'context' => \context_user::instance($USER->id),
                'other' => [
                    'userid' => $USER->id,
                    'debug' => [
                        'messageid' => $result->get('MessageId'),
                    ],
                ],
            ];
            $event = sms_sent::create($data);
            $event->trigger();

            return true;
        } catch (\Aws\Exception\AwsException $e) {
            throw new \moodle_exception('errorawsconection', 'factor_sms', '', $e->getAwsErrorMessage());
        }
    }

    /**
     * Add gateway specific settings to the SMS factor settings page.
     *
     * @param \admin_settingpage $settings
     * @return void
     */
    public static function add_settings(\admin_settingpage $settings): void {
        $settings->add(new \admin_setting_configcheckbox('factor_sms/usecredchain',
            get_string('settings:aws:usecredchain', 'factor_sms'), '', 0));

        if (!get_config('factor_sms', 'usecredchain')) {
            // AWS Settings.
            $settings->add(new \admin_setting_configtext('factor_sms/api_key',
                get_string('settings:aws:key', 'factor_sms'),
                get_string('settings:aws:key_help', 'factor_sms'), ''));

            $settings->add(new \admin_setting_configpasswordunmask('factor_sms/api_secret',
                get_string('settings:aws:secret', 'factor_sms'),
                get_string('settings:aws:secret_help', 'factor_sms'), ''));
        }

        $settings->add(new admin_settings_aws_region('factor_sms/api_region',
            get_string('settings:aws:region', 'factor_sms'),
            get_string('settings:aws:region_help', 'factor_sms'),
            'ap-southeast-2'));
    }

    /**
     * Returns whether or not the gateway is enabled
     *
     * @return  bool
     */
    public static function is_gateway_enabled(): bool {
        return true;
    }
}
