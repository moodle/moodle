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

namespace core_sms\external;

use context_system;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Webservice to enable or disable sms gateway.
 *
 * @package    core_sms
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sms_gateway_status extends external_api {

    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'plugin' => new external_value(PARAM_INT, 'Gateway ID', VALUE_REQUIRED),
            'state' => new external_value(PARAM_INT, 'Enabled or disabled', VALUE_REQUIRED),
        ]);
    }

    public static function execute(int $plugin, int $state): array {
        // Parameter validation.
        [
            'plugin' => $gatewayid,
            'state' => $state,
        ] = self::validate_parameters(self::execute_parameters(), [
            'plugin' => $plugin,
            'state' => $state,
        ]);

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        $result = [
            'result' => true,
            'message' => '',
            'messagetype' => '',
        ];
        $manager = \core\di::get(\core_sms\manager::class);
        $gatewaymanagers = $manager->get_gateway_instances(['id' => $gatewayid]);
        $gatewaymanager = reset($gatewaymanagers);

        if (!$gatewaymanager) {
            $result = [
                'result' => false,
                'message' => 'sms_gateway_not_found',
                'messagetype' => 'error'
            ];
            return $result;
        }

        if (!empty($state)) {
            $manager->enable_gateway(gateway: $gatewaymanager);
            $message = get_string('plugin_enabled', 'core_admin', $gatewaymanager->name);
            $messagetype = \core\notification::SUCCESS;
        } else {
            $gatewayresult = $manager->disable_gateway(gateway: $gatewaymanager);
            if ($gatewayresult->enabled) {
                $result = [
                    'result' => false,
                    'message' => 'sms_gateway_disable_failed',
                    'messagetype' => 'error'
                ];
                $message = get_string('sms_gateway_disable_failed', 'core_sms');
                $messagetype = \core\notification::ERROR;
            } else {
                $message = get_string('plugin_disabled', 'core_admin', $gatewaymanager->name);
                $messagetype = \core\notification::SUCCESS;
            }
        }

        \core\notification::add($message, $messagetype);

        return $result;
    }

    public static function execute_returns(): external_single_structure {
        return new external_single_structure(
            [
                'result' => new external_value(PARAM_BOOL, 'Whether the status was changed, true or false'),
                'message' => new external_value(PARAM_TEXT, 'Messages'),
                'messagetype' => new external_value(PARAM_TEXT, 'Message type'),
            ]
        );
    }

}
