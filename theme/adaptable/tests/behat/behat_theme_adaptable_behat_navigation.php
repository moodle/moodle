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
 * Overrides for behat navigation.
 *
 * @package   theme_adaptable
 * @author    Marcus Green derived from code by Guy Thomas
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Mink\Exception\ExpectationException;

require_once(__DIR__ . '/../../../../lib/tests/behat/behat_navigation.php');

/**
 * Overrides to make behat navigation work with adapt.
 *
 * @package   theme_adaptable
 * @author    Marcus Green derived from Snap theme
 * @copyright Titus Learning 2020
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class behat_theme_adaptable_behat_navigation extends behat_navigation {
    /**
     * Open an assignment or resource based on title.
     *
     * @param string $assettitle
     * @throws ExpectationException
     * @Given /^I follow asset link "(?P<assettitle>(?:[^"]|\\")*)"$/
     */
    public function i_follow_asset_link($assettitle) {
        $xpath = '//a/span[contains(.,"' . $assettitle . '")]';

        // Now get all nodes.
        $linknodes = $this->find_all('xpath', $xpath);

        // Cycle through all nodes and if just one of them is visible break loop.
        foreach ($linknodes as $node) {
            $visible = $this->is_node_visible($node, behat_base::get_reduced_timeout());
            if ($visible) {
                break;
            }
        }

        if (!$visible) {
            // Oh dear, none of the links were visible.
            $msg = 'At least one node should be visible for the xpath "' . $node->getXPath();
            throw new ExpectationException($msg, $this->getSession());
        }

        // Hurray, we found a visible link - let's click it!
        $node->click();
    }

    /**
     * Go to current page setting item
     *
     * This can be used on front page, course, category or modules pages.
     *
     * @Given /^I navigate to "(?P<nodetext_string>(?:[^"]|\\")*)" in current page administration$/
     *
     * @throws ExpectationException
     * @param string $nodetext navigation node to click, may contain path, for example "Reports > Overview"
     * @return void
     */
    public function i_navigate_to_in_current_page_administration($nodetext) {
        $parentnodes = array_map('trim', explode('>', $nodetext));

        // Find the name of the first category of the administration block tree.
        $xpath = "//section[contains(@class,'block_settings')]//div[@id='settingsnav']/ul[1]/li[1]/p[1]/span";
        $node = $this->find('xpath', $xpath);

        array_unshift($parentnodes, $node->getText());
        $lastnode = array_pop($parentnodes);

        $this->select_node_in_navigation($lastnode, $parentnodes);
    }

    /**
     * Finds a node in the Navigation or Administration tree and clicks on it.
     *
     * @param string $nodetext
     * @param array $parentnodes
     * @throws ExpectationException
     */
    protected function select_node_in_navigation($nodetext, $parentnodes) {
        $nodetoclick = $this->find_node_in_navigation($nodetext, $parentnodes);
        // Throw exception if no node found.
        if (!$nodetoclick) {
            throw new ExpectationException('Navigation node "' . $nodetext . '" not found under "' .
                implode(' > ', $parentnodes) . '"', $this->getSession());
        }

        $this->execute('behat_general::i_click_on', [$nodetoclick, 'NodeElement']);
    }

    /**
     * Helper function to get top navigation node in tree.
     *
     * @throws ExpectationException if note not found.
     * @param string $nodetext name of top navigation node in tree.
     * @return NodeElement
     */
    protected function get_top_navigation_node($nodetext) {
        // Avoid problems with quotes.
        $nodetextliteral = behat_context_helper::escape($nodetext);
        $exception = new ExpectationException('Top navigation node "' . $nodetext . '" not found', $this->getSession());

        $xpath = // Navigation block.
        "//div[contains(concat(' ', normalize-space(@class), ' '), ' content ')]" .
        "/ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
        "/li[contains(concat(' ', normalize-space(@class), ' '), ' contains_branch ')]" .
        "/ul/li[contains(concat(' ', normalize-space(@class), ' '), ' contains_branch ')]" .
        "[p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
        "[span[normalize-space(.)={$nodetextliteral}] or a[normalize-space(.)={$nodetextliteral}]]]" .
        "|" .
        // Administration block.
        "//div[contains(concat(' ', normalize-space(@class), ' '), ' content ')]/div" .
            "/ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
            "/li[contains(concat(' ', normalize-space(@class), ' '), ' contains_branch ')]" .
            "/ul/li[contains(concat(' ', normalize-space(@class), ' '), ' contains_branch ')]" .
            "[p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
            "/span[normalize-space(.)={$nodetextliteral}]]" .
            "|" .
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' content ')]/div" .
            "/ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
            "/li[p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
            "/span[normalize-space(.)={$nodetextliteral}]]" .
            "|" .
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' content ')]/div" .
            "/ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
            "/li[p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
            "/a[normalize-space(.)={$nodetextliteral}]]";

        $node = $this->find('xpath', $xpath, $exception);

        return $node;
    }
    /**
     * Go to site administration item
     *
     *
     * @throws ExpectationException
     * @param string $nodetext navigation node to click, may contain path, for example "Reports > Overview"
     * @return void
     */
    public function i_navigate_to_in_site_administration($nodetext) {
        $parentnodes = array_map('trim', explode('>', $nodetext));
        array_unshift($parentnodes, get_string('administrationsite'));
        $lastnode = array_pop($parentnodes);
        $this->select_node_in_navigation($lastnode, $parentnodes);
    }

    /**
     * Click on an entry in the user menu.
     * @Given /^I follow "(?P<nodetext_string>(?:[^"]|\\")*)" in the user menu$/
     *
     * @param string $nodetext
     */
    public function i_follow_in_the_user_menu($nodetext) {
        if ($this->running_javascript()) {
            // The user menu must be expanded when JS is enabled.
            $xpath = "//a[@id='usermenu']";
            $this->execute("behat_general::i_click_on", [$this->escape($xpath), "xpath_element"]);
        }
        // Now select the link.
        // The CSS path is always present, with or without JS.
        $xpath = "//div[@id='usermenu-dropdown']";
        $this->execute(
            'behat_general::i_click_on_in_the',
            [$nodetext, "link", $xpath, "xpath_element"]
        );
    }
}
