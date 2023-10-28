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
 * Steps definitions to open and close action menus.
 *
 * @package    core
 * @category   test
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../behat/behat_base.php');

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;

/**
 * Steps definitions to open and close action menus.
 *
 * @package    core
 * @category   test
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_action_menu extends behat_base {

    /**
     * Open the action menu in
     *
     * @Given /^I open the action menu in "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)"$/
     * @param string $element
     * @param string $selector
     * @return void
     */
    public function i_open_the_action_menu_in($element, $selectortype) {
        // Gets the node based on the requested selector type and locator.
        $node = $this->get_node_in_container(
            "css_element",
            "[role=button][aria-haspopup=true],button[aria-haspopup=true],[role=menuitem][aria-haspopup=true]",
            $selectortype,
            $element
        );

        // Check if it is not already opened.
        if ($node->getAttribute('aria-expanded') === 'true') {
            return;
        }

        $this->ensure_node_is_visible($node);
        $node->click();
    }

    /**
     * When an action menu is open, follow one of the items in it.
     *
     * @Given /^I choose "(?P<link_string>(?:[^"]|\\")*)" in the open action menu$/
     * @param string $linkstring
     * @return void
     */
    public function i_choose_in_the_open_action_menu($menuitemstring) {
        if (!$this->running_javascript()) {
            throw new DriverException('Action menu steps are not available with Javascript disabled');
        }
        // Gets the node based on the requested selector type and locator.
        $menuselector = ".moodle-actionmenu .dropdown.show .dropdown-menu";
        $node = $this->get_node_in_container("link", $menuitemstring, "css_element", $menuselector);
        $this->ensure_node_is_visible($node);
        $node->click();
    }

    /**
     * Select a specific item in an action menu.
     *
     * @When /^I choose the "(?P<item_string>(?:[^"]|\\")*)" item in the "(?P<actionmenu_string>(?:[^"]|\\")*)" action menu$/
     * @param string $item The item to choose
     * @param string $actionmenu The text used in the description of the action menu
     */
    public function i_choose_in_the_named_menu(string $item, string $actionmenu): void {
        $menu = $this->find('actionmenu', $actionmenu);
        $this->select_item_in_action_menu($item, $menu);
    }

    /**
     * Select a specific item in an action menu within a container.
     *
     * @When /^I choose the "(?P<item_string>(?:[^"]|\\")*)" item in the "(?P<actionmenu_string>(?:[^"]|\\")*)" action menu of the "(?P<locator_string>(?:[^"]|\\")*)" "(?P<type_string>(?:[^"]|\\")*)"$/
     * @param string $item The item to choose
     * @param string $actionmenu The text used in the description of the action menu
     * @param string|NodeElement $locator The identifer used for the container
     * @param string $selector The type of container to locate
     */
    public function i_choose_in_the_named_menu_in_container(string $item, string $actionmenu, $locator, $selector): void {
        $container = $this->find($selector, $locator);
        $menu = $this->find('actionmenu', $actionmenu, false, $container);
        $this->select_item_in_action_menu($item, $menu);
    }

    /**
     * Select an item in the specified menu.
     *
     * Note: This step does work both with, and without, JavaScript.
     *
     * @param string $item Item string value
     * @param NodeElement $menu The menu NodeElement to select from
     */
    protected function select_item_in_action_menu(string $item, NodeElement $menu): void {
        if ($this->running_javascript()) {
            // Open the menu by clicking on the trigger.
            $this->execute(
                'behat_general::i_click_on',
                [$menu, "NodeElement"]
            );
        }

        // Select the menu item.
        $this->execute(
            'behat_general::i_click_on_in_the',
            [$item, "link", $menu, "NodeElement"]
        );
    }

    /**
     * The action menu item should not exist.
     *
     * @Then /^the "(?P<item_string>(?:[^"]|\\")*)" item should not exist in the "(?P<actionmenu_string>(?:[^"]|\\")*)" action menu$/
     * @param string $item The item to check
     * @param string $actionmenu The text used in the description of the action menu
     */
    public function item_should_not_exist(string $item, string $actionmenu): void {
        $menu = $this->find('actionmenu', $actionmenu);
        $this->execute('behat_general::should_not_exist_in_the', [
            $item, 'link',
            $menu, 'NodeElement'
        ]);
    }

    /**
     * The action menu item should not exist within a container.
     *
     * @Then /^the "(?P<item_string>(?:[^"]|\\")*)" item should not exist in the "(?P<actionmenu_string>(?:[^"]|\\")*)" action menu of the "(?P<locator_string>(?:[^"]|\\")*)" "(?P<type_string>(?:[^"]|\\")*)"$/
     * @param string $item The item to check
     * @param string $actionmenu The text used in the description of the action menu
     * @param string|NodeElement $locator The identifer used for the container
     * @param string $selector The type of container to locate
     */
    public function item_should_not_exist_in_the(string $item, string $actionmenu, $locator, $selector): void {
        $container = $this->find($selector, $locator);
        $menu = $this->find('actionmenu', $actionmenu, false, $container);
        $this->execute('behat_general::should_not_exist_in_the', [
            $item, 'link',
            $menu, 'NodeElement'
        ]);
    }


    /**
     * The action menu item should exist.
     *
     * @Then /^the "(?P<item_string>(?:[^"]|\\")*)" item should exist in the "(?P<actionmenu_string>(?:[^"]|\\")*)" action menu$/
     * @param string $item The item to check
     * @param string $actionmenu The text used in the description of the action menu
     */
    public function item_should_exist(string $item, string $actionmenu): void {
        $menu = $this->find('actionmenu', $actionmenu);
        $this->execute('behat_general::should_exist_in_the', [
            $item, 'link',
            $menu, 'NodeElement'
        ]);
    }

    /**
     * The action menu item should exist within a container.
     *
     * @Then /^the "(?P<item_string>(?:[^"]|\\")*)" item should exist in the "(?P<actionmenu_string>(?:[^"]|\\")*)" action menu of the "(?P<locator_string>(?:[^"]|\\")*)" "(?P<type_string>(?:[^"]|\\")*)"$/
     * @param string $item The item to check
     * @param string $actionmenu The text used in the description of the action menu
     * @param string|NodeElement $locator The identifer used for the container
     * @param string $selector The type of container to locate
     */
    public function item_should_exist_in_the(string $item, string $actionmenu, $locator, $selector): void {
        $container = $this->find($selector, $locator);
        $menu = $this->find('actionmenu', $actionmenu, false, $container);
        $this->execute('behat_general::should_exist_in_the', [
            $item, 'link',
            $menu, 'NodeElement'
        ]);
    }
}
