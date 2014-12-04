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
 * Basic authentication steps definitions.
 *
 * @package    core_auth
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given;
use Behat\Behat\Context\Step\When as When;

/**
 * Log in log out steps definitions.
 *
 * @package    core_auth
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_auth extends behat_base {

    /**
     * Logs in the user. There should exist a user with the same value as username and password.
     *
     * @Given /^I log in as "(?P<username_string>(?:[^"]|\\")*)"$/
     */
    public function i_log_in_as($username) {

        // Running this step using the API rather than a chained step because
        // we need to see if the 'Log in' link is available or we need to click
        // the dropdown to expand the navigation bar before.
        $this->getSession()->visit($this->locate_path('/'));

        // Generic steps (we will prefix them later expanding the navigation dropdown if necessary).
        $steps = array(
            new Given('I click on "' . get_string('login') . '" "link" in the ".logininfo" "css_element"'),
            new Given('I set the field "' . get_string('username') . '" to "' . $this->escape($username) . '"'),
            new Given('I set the field "' . get_string('password') . '" to "'. $this->escape($username) . '"'),
            new Given('I press "' . get_string('login') . '"')
        );

        // If Javascript is disabled we have enough with these steps.
        if (!$this->running_javascript()) {
            return $steps;
        }

        // Wait for the homepage to be ready.
        $this->getSession()->wait(self::TIMEOUT * 1000, self::PAGE_READY_JS);

        // If it is needed, it expands the navigation bar with the 'Log in' link.
        if ($clicknavbar = $this->get_expand_navbar_step()) {
            array_unshift($steps, $clicknavbar);
        }

        return $steps;
    }

    /**
     * Logs out of the system.
     *
     * @Given /^I log out$/
     */
    public function i_log_out() {

        $steps = array(new When('I follow "' . get_string('logout') . '"'));

        // No need to check anything else if we run without JS.
        if (!$this->running_javascript()) {
            return $steps;
        }

        // There is no longer any need to worry about whether the navigation
        // bar needs to be expanded; user_menu now lives outside the
        // hamburger.

        // However, the user menu *always* needs to be expanded.
        $xpath = "//div[@class='usermenu']//a[contains(concat(' ', @class, ' '), ' toggle-display ')]";
        array_unshift($steps, new When('I click on "'.$xpath.'" "xpath_element"'));

        return $steps;
    }

    /**
     * Returns a step to open the navigation bar if it is needed.
     *
     * The top log in and log out links are hidden when middle or small
     * size windows (or devices) are used. This step returns a step definition
     * clicking to expand the navbar if it is hidden.
     *
     * @return Given|bool A step definition or false if there is no need to show the navbar.
     */
    protected function get_expand_navbar_step() {

        // Checking if we need to click the navbar button to show the navigation menu, it
        // is hidden by default when using clean theme and a medium or small screen size.

        // The DOM and the JS should be all ready and loaded. Running without spinning
        // as this is a widely used step and we can not spend time here trying to see
        // a DOM node that is not always there (at the moment clean is not even the
        // default theme...).
        $navbuttonjs = "return (
            Y.one('.btn-navbar') &&
            Y.one('.btn-navbar').getComputedStyle('display') !== 'none'
        )";

        // Adding an extra click we need to show the 'Log in' link.
        if (!$this->getSession()->getDriver()->evaluateScript($navbuttonjs)) {
            return false;
        }

        return new Given('I click on ".btn-navbar" "css_element"');
    }
}
