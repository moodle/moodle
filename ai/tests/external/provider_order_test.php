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

/**
 * Test provider order external api calls.
 *
 * @package    core_ai
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_ai\external\set_provider_order
 */
final class provider_order_test extends \advanced_testcase {
    /**
     * Test set provider order.
     */
    public function test_set_provider_order(): void {
        $this->resetAfterTest();

        // Create the provider instances.
        $manager = \core\di::get(\core_ai\manager::class);
        $openai = $manager->create_provider_instance(
            classname: '\aiprovider_openai\provider',
            name: 'openai instance',
            enabled: true, // Must be set to true to activate the sort function.
            config: ['data' => 'openai configurations'],
        );
        $azureai = $manager->create_provider_instance(
            classname: '\aiprovider_azureai\provider',
            name: 'azureai instance',
            enabled: true, // Must be set to true to activate the sort function.
            config: ['data' => 'azureai configurations'],
        );

        $this->setAdminUser();

        // Move the OpenAI instance to the bottom, and AzureAI will automatically move to the top.
        set_provider_order::execute($openai->id, \core\plugininfo\aiprovider::MOVE_DOWN);
        $providers = array_keys($manager->get_sorted_providers());
        $this->assertEquals($providers[0], $azureai->id);
        $this->assertEquals($providers[1], $openai->id);

        // Move the OpenAI instance to the top, and AzureAI will automatically move to the bottom.
        set_provider_order::execute($openai->id, \core\plugininfo\aiprovider::MOVE_UP);
        $providers = array_keys($manager->get_sorted_providers());
        $this->assertEquals($providers[0], $openai->id);
        $this->assertEquals($providers[1], $azureai->id);
    }
}
