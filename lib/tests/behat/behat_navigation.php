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
 * Navigation steps definitions.
 *
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions to navigate through the navigation tree nodes.
 *
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_navigation extends behat_base {

    /**
     * Expands the selected node of the navigation tree that matches the text.
     * @Given /^I expand "(?P<nodetext_string>(?:[^"]|\\")*)" node$/
     *
     * @throws ExpectationException
     * @param string $nodetext
     */
    public function i_expand_node($nodetext) {

        // This step is useless with Javascript disabled as Moodle auto expands
        // all of tree's nodes; adding this because of scenarios that shares the
        // same steps with and without Javascript enabled.
        if (!$this->running_javascript()) {
            if ($nodetext === get_string('administrationsite')) {
                // Administration menu is not loaded by default any more. Click the link to expand.
                return new Given('I click on "'.$nodetext.'" "link" in the "'.get_string('administration').'" "block"');
            }
            return true;
        }

        // Avoid problems with quotes.
        $nodetextliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($nodetext);

        $xpath = "//ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
            "/child::li[contains(concat(' ', normalize-space(@class), ' '), ' collapsed ')]" .
            "/child::p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
            "/child::span[normalize-space(.)=$nodetextliteral]" .
            "|" .
            "//ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
            "/descendant::li[not(contains(concat(' ', normalize-space(@class), ' '), ' collapsed '))]" .
            "/descendant::li[contains(concat(' ', normalize-space(@class), ' '), ' collapsed ')]" .
            "/child::p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
            "/child::span[normalize-space(.)=$nodetextliteral]";

        $exception = new ExpectationException('The "' . $nodetext . '" node can not be expanded', $this->getSession());
        $node = $this->find('xpath', $xpath, $exception);
        $this->ensure_node_is_visible($node);
        $node->click();
    }

    /**
     * Collapses the selected node of the navigation tree that matches the text.
     *
     * @Given /^I collapse "(?P<nodetext_string>(?:[^"]|\\")*)" node$/
     * @throws ExpectationException
     * @param string $nodetext
     */
    public function i_collapse_node($nodetext) {

        // No collapsible nodes with non-JS browsers.
        if (!$this->running_javascript()) {
            return true;
        }

        // Avoid problems with quotes.
        $nodetextliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($nodetext);

        $xpath = "//ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
            "/child::li[not(contains(concat(' ', normalize-space(@class), ' '), ' collapsed '))]" .
            "/child::p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
            "/child::span[normalize-space(.)=$nodetextliteral]" .
            "|" .
            "//ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
            "/descendant::li[not(contains(concat(' ', normalize-space(@class), ' '), ' collapsed '))]" .
            "/child::p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
            "/child::span[normalize-space(.)=$nodetextliteral]";

        $exception = new ExpectationException('The "' . $nodetext . '" node can not be collapsed', $this->getSession());
        $node = $this->find('xpath', $xpath, $exception);
        $node->click();
    }

    /**
     * Click link in navigation tree that matches the text in parentnode/s (seperated using greater-than character if more than one)
     *
     * @Given /^I navigate to "(?P<nodetext_string>(?:[^"]|\\")*)" node in "(?P<parentnodes_string>(?:[^"]|\\")*)"$/
     *
     * @throws ExpectationException
     * @param string $nodetext navigation node to click.
     * @param string $parentnodes comma seperated list of parent nodes.
     * @return void
     */
    public function i_navigate_to_node_in($nodetext, $parentnodes) {

        // Site admin is different and needs special treatment.
        $siteadminstr = get_string('administrationsite');

        // Create array of all parentnodes.
        $parentnodes = array_map('trim', explode('>', $parentnodes));
        $countparentnode = count($parentnodes);

        // If JS is disabled and Site administration is not expanded we
        // should follow it, so all the lower-level nodes are available.
        if (!$this->running_javascript()) {
            if ($parentnodes[0] === $siteadminstr) {
                // We don't know if there if Site admin is already expanded so
                // don't wait, it is non-JS and we already waited for the DOM.
                if ($siteadminlink = $this->getSession()->getPage()->find('named', array('link', "'" . $siteadminstr . "'"))) {
                    $siteadminlink->click();
                }
            }
        }

        // Expand first node, and get it.
        $node = $this->get_top_navigation_node($parentnodes[0]);

        // Expand parent, sub-parent nodes in navigation if js enabled.
        if ($node->hasClass('collapsed') || ($node->hasAttribute('data-loaded') && $node->getAttribute('data-loaded') == 0)) {
            $xpath = "/p[contains(concat(' ', normalize-space(@class), ' '), ' tree_item ')]/span";
            $nodetoexpand = $node->find('xpath', $xpath);

            if ($this->running_javascript()) {
                $this->ensure_node_is_visible($nodetoexpand);
                $nodetoexpand->click();

                // Site administration node needs to be expanded.
                if ($parentnodes[0] === $siteadminstr) {
                    $this->getSession()->wait(self::TIMEOUT * 1000, self::PAGE_READY_JS);
                }
            }
        }

        // If sub-parent nodes then get to the last one.
        if ($countparentnode > 1) {
            for ($i = 1; $i < $countparentnode; $i++) {
                $node = $this->get_navigation_node($parentnodes[$i], $node);

                // Keep expanding all sub-parents if js enabled.
                if ($this->running_javascript()) {
                    $xpath = "/p[contains(concat(' ', normalize-space(@class), ' '), ' tree_item ')]";
                    if ($node->hasClass('collapsed')) {
                        $nodetoexpand = $node->find('xpath', $xpath);
                        if ($this->running_javascript()) {
                            $this->ensure_node_is_visible($nodetoexpand);
                            $nodetoexpand->click();
                        }
                    }
                }
            }
        }

        // Finally, click on requested node under navigation.
        $nodetextliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($nodetext);
        $xpath = "/ul/li/p[contains(concat(' ', normalize-space(@class), ' '), ' tree_item ')]" .
                "/a[normalize-space(.)=" . $nodetextliteral . "]";
        $node = $node->find('xpath', $xpath);

        // Throw exception if no node found.
        if (!$node) {
            throw new ExpectationException('Navigation node "' . $nodetext . '" not found under "' .
                $parentnodes . '"', $this->getSession());
        }

        if ($this->running_javascript()) {
            $this->ensure_node_is_visible($node);
        }

        $node->click();
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
        $nodetextliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($nodetext);
        $exception = new ExpectationException('Top navigation node "' . $nodetext . ' not found in "', $this->getSession());

        // First find in navigation block.
        $xpath = "//div[contains(concat(' ', normalize-space(@class), ' '), ' content ')]" .
            "/ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
            "/li[contains(concat(' ', normalize-space(@class), ' '), ' contains_branch ')]" .
            "/ul/li[contains(concat(' ', normalize-space(@class), ' '), ' contains_branch ')]" .
            "[p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
            "/span[normalize-space(.)=" . $nodetextliteral ."]]" .
            "|" .
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' content ')]/div" .
            "/ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
            "/li[contains(concat(' ', normalize-space(@class), ' '), ' contains_branch ')]" .
            "/ul/li[contains(concat(' ', normalize-space(@class), ' '), ' contains_branch ')]" .
            "[p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
            "/span[normalize-space(.)=" . $nodetextliteral ."]]" .
            "|" .
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' content ')]/div" .
            "/ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
            "/li[p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
            "/span[normalize-space(.)=" . $nodetextliteral ."]]";
            $node = $this->find('xpath', $xpath, $exception);

        return $node;
    }

    /**
     * Helper function to get sub-navigation node.
     *
     * @throws ExpectationException if note not found.
     * @param string $nodetext node to find.
     * @param NodeElement $parentnode parent navigation node.
     * @return NodeElement.
     */
    protected function get_navigation_node($nodetext, $parentnode = null) {

        // Avoid problems with quotes.
        $nodetextliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($nodetext);

        $xpath = "/ul/li[contains(concat(' ', normalize-space(@class), ' '), ' contains_branch ')]" .
            "[child::p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
            "/child::span[normalize-space(.)=" . $nodetextliteral ."]]";
        $node = $parentnode->find('xpath', $xpath);
        if (!$node) {
            $xpath = "/ul/li[contains(concat(' ', normalize-space(@class), ' '), ' contains_branch ')]" .
                "[child::p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
                "/child::a[normalize-space(.)=" . $nodetextliteral ."]]";
            $node = $parentnode->find('xpath', $xpath);
        }

        if (!$node) {
            throw new ExpectationException('Sub-navigation node "' . $nodetext . '" not found under "' .
                $parentnode->getText() . '"', $this->getSession());
        }
        return $node;
    }
}
