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
 * Behat generator for AI.
 *
 * @package    core_ai
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_core_ai_generator extends behat_generator_base {

    /**
     * Get the list of creatable entities for core_ai.
     *
     * @return array
     */
    protected function get_creatable_entities(): array {
        return [
            'ai actions' => [
                'singular' => 'ai action',
                'datagenerator' => 'ai_actions',
                'required' => [
                    'actionname',
                    'success',
                    'user',
                    'contextid',
                    'provider',
                ],
                'switchids' => [
                    'user' => 'userid',
                ],
            ],
            'ai providers' => [
                'singular' => 'ai provider',
                'datagenerator' => 'ai_provider',
                'required' => [
                    'provider',
                    'name',
                    'enabled',
                ],
            ],
        ];
    }
}
