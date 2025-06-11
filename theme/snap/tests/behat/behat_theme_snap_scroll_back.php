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
 * Steps definitions for behat theme.
 *
 * @package   theme_snap
 * @category  test
 * @copyright Copyright (c) 2018 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');
use Behat\Mink\Exception\ExpectationException;

/**
 * New steps used to test the scrollback to the last activity/resource accessed.
 *
 * @package   theme_snap
 * @category  test
 * @copyright Copyright (c) 2018 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_snap_scroll_back extends behat_base {

    private $scrollid;

    /**
     * @Given /^I reset session storage$/
     */
    public function reset_session_storage() {
        $session = $this->getSession();
        $session->getDriver()->evaluateScript('sessionStorage.clear();');
    }

    /**
     * @Given /^The stored element scroll id matches the session storage id$/
     * @param string $paramid expected id
     * @throws ExpectationException
     */
    public function the_stored_id_matches() {

        $scrollid = $this->scrollid;

        $sessionid = $this->getSession()->getDriver()->evaluateScript(
            "function(){ return sessionStorage.getItem('lastMod'); }()"
        );

        if ($sessionid === $scrollid) {
            return;
        } else {
            throw new ExpectationException('The variable stored is: "'.$sessionid.'" not: "'.$scrollid.'"', $this->getSession());
        }
    }

    /**
     * @Given /^The id for element "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" is saved for scrollback$/
     * @param string $element Element we look for
     * @param string $selectortype The type of what we look for
     */
    public function id_for_element_is_saved($element, $selectortype) {
        $node = $this->get_selected_node($selectortype, $element);
        $this->scrollid = $node->getAttribute('id');
    }
}
