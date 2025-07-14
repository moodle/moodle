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

namespace core_ai\external;

use context_system;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * External API to delete a provider instance.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_provider_instance extends external_api {
    /**
     * Get provider parameters.
     *
     * @since  Moodel 5.0
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'providerid' => new external_value(PARAM_INT, 'Provider ID', VALUE_REQUIRED),
        ]);
    }

    /**
     * Delete a provider instance.
     *
     * @since  Moodel 5.0
     * @param int $providerid The provider ID.
     * @return array The generated content.
     */
    public static function execute(int $providerid): array {
        [
            'providerid' => $providerid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'providerid' => $providerid,
        ]);

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        // Get AI provider instance.
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

        $providerresult = $manager->delete_provider_instance(provider: $aiprovider);
        if (!$providerresult) {
            $message = get_string('providerinstancedeletefailed', 'core_ai', $aiprovider->name);
            $messagetype = \core\notification::ERROR;
        } else {
            $message = get_string('providerinstancedeleted', 'core_ai', $aiprovider->name);
            $messagetype = \core\notification::SUCCESS;
        }

        \core\notification::add($message, $messagetype);

        // Update and return the result array in one place.
        return [
            'result' => $providerresult,
            'message' => $message,
            'messagetype' => $messagetype,
        ];
    }

    /**
     * Generate content return value.
     *
     * @since  Moodel 5.0
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
