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

/**
 * General use steps definitions.
 *
 * @package core_ai
 * @copyright 2024 Matt Porritt <matt.porritt@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode;

/**
 * Steps definitions specific to the AI Subsystem.
 *
 * @package core_ai
 * @copyright 2024 Matt Porritt <matt.porritt@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_core_ai extends behat_base {
    /**
     * Change the enabled state of an AI provider plugin.
     *
     * @Then /^I "(?P<state>(?:[^"]|\\")*)" the ai provider with name "(?P<provider>(?:[^"]|\\")*)"$/
     *
     * @param string $state The state to set the plugin to.
     * @param string $providername The name of the AI provider plugin.
     */
    #[\core\attribute\example('Given I "disable" the ai provider with name "OpenAI API test"')]
    public function i_change_the_ai_provider_state_with_name(string $state, string $providername): void {
        $manager = \core\di::get(\core_ai\manager::class);
        $providers = $manager->get_provider_instances(['name' => $providername]);
        $provider = reset($providers);
        if ($state == 'disable') {
            $manager->disable_provider_instance($provider);
        } else {
            $manager->enable_provider_instance($provider);
        }
    }

    /**
     * Set action configuration for AI provider instances.
     *
     * @Given /^I set the following action configuration for ai provider with name "(?P<providername>(?:[^"]|\\")*)":$/
     *
     * @param string $providername The name of the ai provider to configure actions for.
     * @param TableNode $data
     */
    #[\core\attribute\example('I set the following action configuration for ai provider with name "OpenAI API test":
        | action         | enabled | model | endpoint                                            |
        | generate_text  | 1       | gpt-3 | https://api.openai.com/v1/engines/gpt-3/completions |
        | summarise_text | 0       | gpt-4 |                                                     |')]
    public function configure_provider_action(string $providername, TableNode $data) {
        $manager = \core\di::get(\core_ai\manager::class);
        $providers = $manager->get_provider_instances(['name' => $providername]);
        $provider = reset($providers);
        $rows = $data->getHash();
        $actiondata = [];
        foreach ($rows as $row) {
            $action = 'core_ai\\aiactions\\' . $row['action'];
            $actiondata[$action]['enabled'] = $row['enabled'];
            unset ($row['action'], $row['enabled']);
            $actiondata[$action]['settings'] = $row;
        }
        $manager->update_provider_instance(
            provider: $provider,
            actionconfig: $actiondata
        );
    }
}
