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

namespace smsgateway_aws\local\service;

use core_sms\message_status;
use smsgateway_aws\helper;
use smsgateway_aws\local\aws_sms_service_provider;
use stdClass;

/**
 * AWS SNS service provider.
 *
 * @package    smsgateway_aws
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aws_sns implements aws_sms_service_provider {

    /**
     * Include the required calls.
     */
    private static function require(): void {
        global $CFG;
        require_once($CFG->libdir . '/aws-sdk/src/functions.php');
    }

    #[\Override]
    public static function send_sms_message(
        string $messagecontent,
        string $phonenumber,
        stdclass $config,
    ): message_status {
        global $SITE;
        self::require();

        // Setup client params and instantiate client.
        $params = [
            'version' => 'latest',
            'region' => $config->api_region,
            'http' => ['proxy' => helper::get_proxy_string()],
        ];
        if (!property_exists($config, 'usecredchain') || !$config->usecredchain) {
            $params['credentials'] = [
                'key' => $config->api_key,
                'secret' => $config->api_secret,
            ];
        }
        $client = new \Aws\Sns\SnsClient($params);

        // Set up the sender information.
        $senderid = $SITE->shortname;
        // Remove spaces and non-alphanumeric characters from ID.
        $senderid = preg_replace("/[^A-Za-z0-9]/", '', trim($senderid));
        // We have to truncate the senderID to 11 chars.
        $senderid = substr($senderid, 0, 11);

        try {
            // These messages need to be transactional.
            $client->SetSMSAttributes([
                'attributes' => [
                    'DefaultSMSType' => 'Transactional',
                    'DefaultSenderID' => $senderid,
                ],
            ]);

            // Actually send the message.
            $client->publish([
                'Message' => $messagecontent,
                'PhoneNumber' => $phonenumber,
            ]);
            return \core_sms\message_status::GATEWAY_SENT;
        } catch (\Aws\Exception\AwsException $e) {
            return \core_sms\message_status::GATEWAY_NOT_AVAILABLE;
        }
    }
}
