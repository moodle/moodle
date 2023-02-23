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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

/**
 * Behat grade related steps definitions.
 *
 * @package    core_grades
 * @copyright  2022 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_grades extends behat_base {

    /**
     * Return the list of partial named selectors.
     *
     * @return array
     */
    public static function get_partial_named_selectors(): array {
        return [
            new behat_component_named_selector(
                'initials bar',
                [".//*[contains(concat(' ', @class, ' '), ' initialbar ')]//span[contains(., %locator%)]/parent::div"]
            ),
        ];
    }

    /**
     * Select a given element within a specific container instance.
     *
     * @Given /^I select "(?P<input_value>(?:[^"]|\\")*)" in the "(?P<instance>(?:[^"]|\\")*)" "(?P<instance_type>(?:[^"]|\\")*)"$/
     * @param string $value The Needle
     * @param string  $element The Haystack to select within
     * @param string $selectortype What type of haystack we are looking in
     */
    public function i_select_in_the($value, $element, $selectortype) {
        // Getting the container where the text should be found.
        $container = $this->get_selected_node($selectortype, $element);
        $node = $this->find('xpath', './/input[@value="' . $value . '"]', false, $container);
        $node->click();
    }
}
