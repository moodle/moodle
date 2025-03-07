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

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Web Service to control the order of a provider instance.
 *
 * @package   core_ai
 * @category  external
 * @copyright Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_provider_order extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'plugin' => new external_value(PARAM_INT, ' The provider instance ID', VALUE_REQUIRED),
            'direction' => new external_value(PARAM_INT, 'The direction to move', VALUE_REQUIRED),
        ]);
    }

    /**
     * Set the provider instance order.
     *
     * @param int $providerid The provider instance ID
     * @param int $direction The direction to move the provider instance
     * @return array
     */
    public static function execute(
        int $providerid,
        int $direction,
    ): array {
        [
            'plugin' => $providerid,
            'direction' => $direction,
        ] = self::validate_parameters(self::execute_parameters(), [
            'plugin' => $providerid,
            'direction' => $direction,
        ]);

        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        $manager = \core\di::get(\core_ai\manager::class);
        $aiproviders = $manager->get_provider_instances(['id' => $providerid]);
        $aiprovider = reset($aiproviders);
        if ($aiprovider) {
            $manager->change_provider_order($providerid, $direction);
        }

        $directionstring = $direction === \core\plugininfo\aiprovider::MOVE_UP
                            ? \core\plugininfo\aiprovider::UP
                            : \core\plugininfo\aiprovider::DOWN;
        $message = get_string('providermoved' . $directionstring, 'ai', $aiprovider->name);
        $messagetype = \core\notification::SUCCESS;

        \core\notification::add($message, $messagetype);

        return [];
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([]);
    }
}
