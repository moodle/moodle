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

namespace core_ai;

/**
 * Test trait for AI.
 *
 * @package    core_ai
 * @category   test
 * @copyright  2025 Stevani Andolo <stevani@hotmail.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait ai_test_trait {
    /**
     * Creates a dummy AI provider.
     *
     * @param array $actions A set of actions to configure the provider with.
     * @param string $providerclass
     */
    private function create_ai_provider(array $actions, $providerclass): void {
        global $DB;

        $actionconfig = [];
        foreach ($actions as $action) {
            $actionclass = 'core_ai\\aiactions\\' . $action;
            $actionconfig[$actionclass] = [
                'enabled' => true,
                'settings' => [
                    'model' => 'test',
                    'endpoint' => 'test',
                    'systeminstruction' => 'test',
                ],
            ];
        }

        $config = ['apikey' => 'test'];
        $record = new \stdClass();
        $record->name = 'test';
        $record->provider = $providerclass;
        $record->enabled = 1;
        $record->config = json_encode($config);
        $record->actionconfig = json_encode($actionconfig);
        $DB->insert_record('ai_providers', $record);
    }
}
