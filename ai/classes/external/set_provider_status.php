<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_ai\external;

use context_system;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Webservice to enable or disable AI provider.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_provider_status extends external_api {
    /**
     * Set provider status parameters.
     *
     * @since  Moodle 4.5
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'plugin' => new external_value(PARAM_INT, 'Provider ID', VALUE_REQUIRED),
            'state' => new external_value(PARAM_INT, 'Enabled or disabled', VALUE_REQUIRED),
        ]);
    }

    /**
     * Set a provider status.
     *
     * @since  Moodle 4.5
     * @param int $plugin The provider ID.
     * @param int $state The state of the provider.
     * @return array The generated content.
     */
    public static function execute(int $plugin, int $state): array {
        // Parameter validation.
        [
            'plugin' => $providerid,
            'state' => $state,
        ] = self::validate_parameters(self::execute_parameters(), [
            'plugin' => $plugin,
            'state' => $state,
        ]);

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        $manager = \core\di::get(\core_ai\manager::class);
        $aiproviders = $manager->get_provider_instances(['id' => $providerid]);
        $aiprovider = reset($aiproviders);

        if (!$aiprovider) {
            return [
                'result' => false,
                'message' => get_string('notfound', 'error'),
                'messagetype' => \core\notification::ERROR,
            ];
        }

        if (!empty($state)) {
            $manager->enable_provider_instance(provider: $aiprovider);
            $message = get_string('plugin_enabled', 'core_admin', $aiprovider->name);
            $messagetype = \core\notification::SUCCESS;
        } else {
            $providerresult = $manager->disable_provider_instance(provider: $aiprovider);
            if ($providerresult->enabled) {
                $message = get_string('providerinstancedisablefailed', 'core_ai');
                $messagetype = \core\notification::ERROR;
            } else {
                $message = get_string('plugin_disabled', 'core_admin', $aiprovider->name);
                $messagetype = \core\notification::SUCCESS;
            }
        }

        \core\notification::add($message, $messagetype);

        return [
            'result' => $messagetype === \core\notification::SUCCESS ? true : false,
            'message' => $message,
            'messagetype' => $messagetype,
        ];
    }

    /**
     * Generate content return value.
     *
     * @since  Moodle 4.5
     * @return external_single_structure
     */
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
