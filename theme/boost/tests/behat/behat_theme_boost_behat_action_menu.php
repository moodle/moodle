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
 * Steps definitions to open and close action menus (overrides).
 *
 * @package    core
 * @category   test
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/tests/behat/behat_action_menu.php');

/**
 * Steps definitions to open and close action menus (overrides).
 *
 * @package    core
 * @category   test
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_boost_behat_action_menu extends behat_action_menu {

    public function i_open_the_action_menu_in($element, $selectortype) {
        // Gets the node based on the requested selector type and locator.
        $node = $this->get_node_in_container("css_element", "[role=button][aria-haspopup=true]", $selectortype, $element);

        // Check if it is not already opened.
        if ($node->getAttribute('aria-expanded') === 'true') {
            return;
        }

        $this->ensure_node_is_visible($node);
        $node->click();
    }

    public function i_choose_in_the_open_action_menu($menuitemstring) {
        if (!$this->running_javascript()) {
            throw new DriverException('Action menu steps are not available with Javascript disabled');
        }
        // Gets the node based on the requested selector type and locator.
        $menuselector = ".moodle-actionmenu .dropdown.open .dropdown-menu";
        $node = $this->get_node_in_container("link", $menuitemstring, "css_element", $menuselector);
        $this->ensure_node_is_visible($node);
        $node->click();
    }
}
