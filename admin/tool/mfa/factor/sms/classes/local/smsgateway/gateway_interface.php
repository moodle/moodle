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
 * SMS Gateway interface
 *
 * @package     factor_sms
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_sms\local\smsgateway;

interface gateway_interface {

    /**
     * Sends an SMS message
     *
     * @param string $messagecontent the content to send in the SMS message.
     * @param string $phonenumber the destination for the message.
     * @return bool true on message send success
     */
    public function send_sms_message(string $messagecontent, string $phonenumber): bool;

    /**
     * Add gateway specific settings to the SMS factor settings page.
     *
     * @param \admin_settingpage $settings
     * @return void
     */
    public static function add_settings(\admin_settingpage $settings): void;

    /**
     * Returns whether or not the gateway is enabled
     *
     * @return  bool
     */
    public static function is_gateway_enabled(): bool;
}
