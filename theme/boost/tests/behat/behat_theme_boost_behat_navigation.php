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
 * Navigation steps overrides.
 *
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/tests/behat/behat_navigation.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException;
use Behat\Mink\Exception\DriverException as DriverException;

/**
 * Steps definitions to navigate through the navigation tree nodes (overrides).
 *
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_boost_behat_navigation extends behat_navigation {

    public function i_follow_in_the_user_menu($nodetext) {

        if ($this->running_javascript()) {
            // The user menu must be expanded when JS is enabled.
            $xpath = "//div[@class='usermenu']//a[contains(concat(' ', @class, ' '), ' dropdown-toggle ')]";
            $this->execute("behat_general::i_click_on", array($this->escape($xpath), "xpath_element"));
        }

        // Now select the link.
        // The CSS path is always present, with or without JS.
        $csspath = ".usermenu .dropdown-menu";

        $this->execute('behat_general::i_click_on_in_the',
            array($nodetext, "link", $csspath, "css_element")
        );
    }

    protected function get_top_navigation_node($nodetext) {

        // Avoid problems with quotes.
        $nodetextliteral = behat_context_helper::escape($nodetext);
        $exception = new ExpectationException('Top navigation node "' . $nodetext . ' not found in "', $this->getSession());

        // First find in navigation block.
        $xpath = "//div[contains(concat(' ', normalize-space(@class), ' '), ' card-text ')]" .
            "/ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
            "/li[contains(concat(' ', normalize-space(@class), ' '), ' contains_branch ')]" .
            "/ul/li[contains(concat(' ', normalize-space(@class), ' '), ' contains_branch ')]" .
            "[p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
            "/*[normalize-space(.)=" . $nodetextliteral ."]]" .
            "|" .
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' card-text ')]/div" .
            "/ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
            "/li[p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
            "/*[normalize-space(.)=" . $nodetextliteral ."]]";

        $node = $this->find('xpath', $xpath, $exception);

        return $node;
    }
}
