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

namespace factor_sms;

use core_sms\hook\before_gateway_deleted;
use core_sms\hook\before_gateway_disabled;

/**
 * Hook listener for SMS factor.
 *
 * @package    factor_sms
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {

    /**
     * Hook listener before a gateway is deleted or disabled.
     *
     * This listener will check if the SMS gateway is currently in use before disabling or deleting the gateway.
     *
     * @param before_gateway_deleted|before_gateway_disabled $hook Hook instance before the gateway is deleted
     */
    public static function check_gateway_usage_in_mfa(
        before_gateway_deleted|before_gateway_disabled $hook,
    ): void {
        try {
            $smsgatewayid = (int)get_config('factor_sms', 'smsgateway');
            if ($smsgatewayid && $smsgatewayid === (int)$hook->gateway->id) {
                $hook->stop_propagation();
            }
        } catch (\dml_exception $exception) {
            $hook->stop_propagation();
        }
    }
}
