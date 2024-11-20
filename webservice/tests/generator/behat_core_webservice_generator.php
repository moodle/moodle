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
 * Behat data generator for core_webservice.
 *
 * @package     core_webservice
 * @category    test
 * @copyright   2021 Andrew Nicols <andrew@nicols.co.uk>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_core_webservice_generator extends behat_generator_base {

    /**
     * Get the list of creatable entities for a web service.
     *
     * @return  array
     */
    protected function get_creatable_entities(): array {

        return [
            'Services' => [
                'singular' => 'Service',
                'datagenerator' => 'service',
                'required' => ['name'],
            ],

            'Service functions' => [
                'singular' => 'Service function',
                'datagenerator' => 'service_functions',
                'required' => ['service', 'functions'],
            ],

            'Tokens' => [
                'singular' => 'Token',
                'datagenerator' => 'token',
                'required' => ['user'],
                'switchids' => [
                    'user' => 'userid',
                ],
            ],
        ];
    }
}
