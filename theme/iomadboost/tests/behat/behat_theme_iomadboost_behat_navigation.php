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
// For that reason, we can't even rely on $CFG->admin being available here.

require_once(__DIR__ . '/../../../../lib/tests/behat/behat_navigation.php');

use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Step definitions related to the navigation in the IOMAD Boost theme.
 *
 * @package    theme_iomadboost
 * @category   test
 * @copyright  2021 Mihail Geshoski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_iomadboost_behat_navigation extends behat_navigation {
    /**
     * Checks whether a node is active in the navbar.
     *
     * @override i should see :name is active in navigation
     *
     * @throws ElementNotFoundException
     * @param string      $element The name of the nav elemnent to look for.
     * @return void
     */
    public function i_should_see_is_active_in_navigation($element) {
        $this->execute("behat_general::assert_element_contains_text",
            [$element, '.navbar .nav-link.active', 'css_element']);
    }
    /**
     * Checks whether a node is active in the secondary nav.
     *
     * @Given i should see :name is active in secondary navigation
     * @throws ElementNotFoundException
     * @param string      $element The name of the nav elemnent to look for.
     * @return void
     */
    public function i_should_see_is_active_in_secondary_navigation($element) {
        $this->execute("behat_general::assert_element_contains_text",
            [$element, '.secondary-navigation .nav-link.active', 'css_element']);
    }

    /**
     * Checks whether the language selector menu is present in the navbar.
     *
     * @Given language selector menu should exist in the navbar
     * @Given language selector menu should :not exist in the navbar
     *
     * @throws ElementNotFoundException
     * @param string|null $not Instructs to checks whether the element does not exist in the user menu, if defined
     * @return void
     */
    public function lang_menu_should_exist($not = null) {
        $callfunction = is_null($not) ? 'should_exist' : 'should_not_exist';
        $this->execute("behat_general::{$callfunction}", [$this->get_lang_menu_xpath(), 'xpath_element']);
    }

    /**
     * Checks whether an item exists in the language selector menu.
     *
     * @Given :itemtext :selectortype should exist in the language selector menu
     * @Given :itemtext :selectortype should :not exist in the language selector menu
     *
     * @throws ElementNotFoundException
     * @param string $itemtext The menu item to find
     * @param string $selectortype The selector type
     * @param string|null $not Instructs to checks whether the element does not exist in the user menu, if defined
     * @return void
     */
    public function should_exist_in_lang_menu($itemtext, $selectortype, $not = null) {
        $callfunction = is_null($not) ? 'should_exist_in_the' : 'should_not_exist_in_the';
        $this->execute("behat_general::{$callfunction}",
            [$itemtext, $selectortype, $this->get_lang_menu_xpath(), 'xpath_element']);
    }

    /**
     * Return the xpath for the language selector menu element.
     *
     * @return string The xpath
     */
    protected function get_lang_menu_xpath() {
        return "//nav[contains(concat(' ', @class, ' '), ' navbar ')]" .
            "//div[contains(concat(' ', @class, ' '),  ' langmenu ')]" .
            "//div[contains(concat(' ', @class, ' '), ' dropdown-menu ')]";
    }
}
