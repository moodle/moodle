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

/**
 * Behat generator for SMS.
 *
 * @package    core_sms
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_core_sms_generator extends behat_generator_base {

    /**
     * Get the list of creatable entities for a core_sms.
     *
     * @return array
     */
    protected function get_creatable_entities(): array {
        return [
            'sms_gateways' => [
                'singular' => 'sms_gateway',
                'datagenerator' => 'sms_gateways',
                'required' => [
                    'classname',
                    'name',
                    'enabled',
                    'config',
                ],
            ],
        ];
    }
}
