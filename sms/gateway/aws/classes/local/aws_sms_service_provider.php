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

namespace smsgateway_aws\local;

use core_sms\message_status;
use stdClass;

/**
 * AWS SMS service provider interface to provide a standard interface for different aws service providers like sns, sqs etc.
 *
 * @package    smsgateway_aws
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface aws_sms_service_provider {

    /**
     * Sends an SMS message using the selected aws service provider.
     *
     * @param string $messagecontent the content to send in the SMS message.
     * @param string $phonenumber the destination for the message.
     * @return message_status Status of the message.
     */
    public static function send_sms_message(
        string $messagecontent,
        string $phonenumber,
        stdclass $config,
    ): message_status;
}
