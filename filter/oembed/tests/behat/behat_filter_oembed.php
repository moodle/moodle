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
 * Oembed filter custom behat steps.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2016 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Behat\Gherkin\Node\TableNode;

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

class behat_filter_oembed extends behat_base {

    /**
     * Get provider action xpath for specific provider and action.
     * @param string $provider
     * @param string $actionclass
     * @return string
     */
    protected function provider_action_xpath($provider, $actionclass) {
        $xpath = '//td/a[text()=\'' . $provider.'\']';
        $xpath .= '/parent::td/div/a[contains(@class,\''.$actionclass.'\')]';
        return $xpath;
    }

    /**
     * Provider action.
     * @param string $provider
     * @param string $actionclass
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    protected function provider_action($provider, $actionclass) {
        $xpath = $this->provider_action_xpath($provider, $actionclass);
        $node = $this->find('xpath', $xpath);
        $this->ensure_node_is_visible($node);
        $node->click();
    }

    /**
     * Toggle provider enable status.
     * @Given /^I toggle the provider "(?P<provider_string>[^"]*)"$/
     * @param string $provider
     */
    public function i_toggle_provider($provider) {
        $this->provider_action($provider, 'filter-oembed-visibility');
    }

    /**
     * Click edit action for provider.
     * @Given /^I edit the provider "(?P<provider_string>[^"]*)"$/
     * @param string $provider
     */
    public function i_edit_provider($provider) {
        $this->provider_action($provider, 'filter-oembed-edit');
    }

    /**
     * Ensure provider status is enabled or disabled.
     * @param string $provider
     * @param bool $enabled
     */
    protected function ensure_provider_status($provider, $enabled = true) {
        $action = $enabled ? 'disable' : 'enable';
        $xpath = $this->provider_action_xpath($provider, 'filter-oembed-visibility');
        $xpath = substr($xpath, 0, -1) . 'and contains(@href,"action='.$action.'")]';
        $this->ensure_element_exists($xpath, 'xpath_element');
    }

    /**
     * @Given /^the provider "(?P<provider_string>[^"]*)" is disabled$/
     * @param string $provider
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function the_provider_is_disabled($provider) {
        $this->ensure_provider_status($provider, false);
    }

    /**
     * @Given /^the provider "(?P<provider_string>[^"]*)" is enabled$/
     * @param string $provider
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function the_provider_is_enabled($provider) {
        $this->ensure_provider_status($provider, true);
    }

    /**
     * @Given /^I filter the provider list to "(?P<provider_string>[^"]*)"$/
     * @param $provider
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function i_filter_provider_list($provider) {
        $fieldxpath = '//input[@placeholder="Provider"]';
        $fieldnode = $this->find('xpath', $fieldxpath);
        $field = behat_field_manager::get_form_field($fieldnode, $this->getSession());
        $field->set_value($provider);
    }

    /**
     * Xpath for provider edit form.
     * @param string $provider
     * @return string
     */
    protected function edit_form_xpath($provider) {
        $xpath = '//td/a[text()=\'' . $provider.'\']';
        $xpath .= '/parent::td/div[contains(@class,\'oembed-provider-details\')]/form';
        return $xpath;
    }

    /**
     * Wait for edit form to be visible.
     * @param string $provider
     */
    protected function wait_for_edit_form($provider) {
        $xpath = $this->edit_form_xpath($provider);
        $this->ensure_element_is_visible($xpath, 'xpath_element');
    }

    /**
     * @Given /^I edit the provider "(?P<provider_string>[^"]*)" with the values:$/
     * @param string $provider
     * @param TableNode $table
     */
    public function i_edit_provider_with_values($provider, TableNode $table) {
        $this->i_edit_provider($provider);

        if (!$data = $table->getRowsHash()) {
            return;
        }

        /** @var behat_forms $formhelper */
        $formhelper = behat_context_helper::get('behat_forms');

        // Field setting code taken from behat_admin.php.
        foreach ($data as $label => $value) {

            $fieldxpath = $this->edit_form_xpath($provider);
            $fieldxpath .= '//label[contains(text(),\''.$label.'\')]';
            $fieldxpath .= '/parent::div/parent::div/div[contains(@class, \'felement\')]/*';

            $formhelper->i_set_the_field_with_xpath_to($fieldxpath, $value);

            $this->find_button(get_string('saveasnew', 'filter_oembed'))->press();
        }

    }
}
