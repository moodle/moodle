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

use Behat\Mink\Exception\ExpectationException as ExpectationException;
use Behat\Mink\Exception\DriverException as DriverException;

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
     * Helper function to get a navigation nodes text element given its text from within the navigation block.
     *
     * This function finds the node with the given text from within the navigation block.
     * It checks to make sure the node is visible, and then returns it.
     *
     * @param string $text
     * @param bool $branch Set this true if you're only interested in the node if its a branch.
     * @param null|bool $collapsed Set this to true or false if you want the node to either be collapsed or not.
     *    If its left as null then we don't worry about it.
     * @param null|string|Exception|false $exception The exception to throw if the node is not found.
     * @return \Behat\Mink\Element\NodeElement
     */
    protected function get_node_text_node($text, $branch = false, $collapsed = null, $exception = null) {
        if ($exception === null) {
            $exception = new ExpectationException('The "' . $text . '" node could not be found', $this->getSession());
        } else if (is_string($exception)) {
            $exception = new ExpectationException($exception, $this->getSession());
        }

        $nodetextliteral = behat_context_helper::escape($text);
        $hasblocktree = "[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]";
        $hasbranch = "[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]";
        $hascollapsed = "li[@aria-expanded='false']";
        $notcollapsed = "li[@aria-expanded='true']";
        $match = "[normalize-space(.)={$nodetextliteral}]";

        // Avoid problems with quotes.
        $isbranch = ($branch) ? $hasbranch : '';
        if ($collapsed === true) {
            $iscollapsed = $hascollapsed;
        } else if ($collapsed === false) {
            $iscollapsed = $notcollapsed;
        } else {
            $iscollapsed = 'li';
        }

        // First check root nodes, it can be a span or link.
        $xpath  = "//ul{$hasblocktree}/$hascollapsed/p{$isbranch}/span{$match}|";
        $xpath  .= "//ul{$hasblocktree}/$hascollapsed/p{$isbranch}/a{$match}|";

        // Next search for the node containing the text within a link.
        $xpath .= "//ul{$hasblocktree}//ul/{$iscollapsed}/p{$isbranch}/a{$match}|";

        // Finally search for the node containing the text within a span.
        $xpath .= "//ul{$hasblocktree}//ul/{$iscollapsed}/p{$isbranch}/span{$match}";

        $node = $this->find('xpath', $xpath, $exception);
        $this->ensure_node_is_visible($node);
        return $node;
    }

    /**
     * Returns true if the navigation node with the given text is expandable.
     *
     * @Given /^navigation node "([^"]*)" should be expandable$/
     *
     * @throws ExpectationException
     * @param string $nodetext
     * @return bool
     */
    public function navigation_node_should_be_expandable($nodetext) {
        if (!$this->running_javascript()) {
            // Nodes are only expandable when JavaScript is enabled.
            return false;
        }

        $node = $this->get_node_text_node($nodetext, true);
        $node = $node->getParent();
        if ($node->hasClass('emptybranch')) {
            throw new ExpectationException('The "' . $nodetext . '" node is not expandable', $this->getSession());
        }

        return true;
    }

    /**
     * Returns true if the navigation node with the given text is not expandable.
     *
     * @Given /^navigation node "([^"]*)" should not be expandable$/
     *
     * @throws ExpectationException
     * @param string $nodetext
     * @return bool
     */
    public function navigation_node_should_not_be_expandable($nodetext) {
        if (!$this->running_javascript()) {
            // Nodes are only expandable when JavaScript is enabled.
            return false;
        }

        $node = $this->get_node_text_node($nodetext);
        $node = $node->getParent();

        if ($node->hasClass('emptybranch') || $node->hasClass('tree_item')) {
            return true;
        }
        throw new ExpectationException('The "' . $nodetext . '" node is expandable', $this->getSession());
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
            $xpath = "//div[@class='usermenu']//a[contains(concat(' ', @class, ' '), ' toggle-display ')]";
            $this->execute("behat_general::i_click_on", array($this->escape($xpath), "xpath_element"));
        }

        // Now select the link.
        // The CSS path is always present, with or without JS.
        $csspath = ".usermenu [data-rel='menu-content']";

        $this->execute('behat_general::i_click_on_in_the',
            array($nodetext, "link", $csspath, "css_element")
        );
    }

    /**
     * Expands the selected node of the navigation tree that matches the text.
     * @Given /^I expand "(?P<nodetext_string>(?:[^"]|\\")*)" node$/
     *
     * @throws ExpectationException
     * @param string $nodetext
     * @return bool|void
     */
    public function i_expand_node($nodetext) {

        // This step is useless with Javascript disabled as Moodle auto expands
        // all of tree's nodes; adding this because of scenarios that shares the
        // same steps with and without Javascript enabled.
        if (!$this->running_javascript()) {
            if ($nodetext === get_string('administrationsite')) {
                // Administration menu is not loaded by default any more. Click the link to expand.
                $this->execute('behat_general::i_click_on_in_the',
                    array($nodetext, "link", get_string('administration'), "block")
                );
                return true;
            }
            return true;
        }

        $node = $this->get_node_text_node($nodetext, true, true, 'The "' . $nodetext . '" node can not be expanded');
        // Check if the node is a link AND a branch.
        if (strtolower($node->getTagName()) === 'a') {
            // We just want to expand the node, we don't want to follow it.
            $node = $node->getParent();
        }
        $node->click();
    }

    /**
     * Collapses the selected node of the navigation tree that matches the text.
     *
     * @Given /^I collapse "(?P<nodetext_string>(?:[^"]|\\")*)" node$/
     * @throws ExpectationException
     * @param string $nodetext
     * @return bool|void
     */
    public function i_collapse_node($nodetext) {

        // No collapsible nodes with non-JS browsers.
        if (!$this->running_javascript()) {
            return true;
        }

        $node = $this->get_node_text_node($nodetext, true, false, 'The "' . $nodetext . '" node can not be collapsed');
        // Check if the node is a link AND a branch.
        if (strtolower($node->getTagName()) === 'a') {
            // We just want to expand the node, we don't want to follow it.
            $node = $node->getParent();
        }
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
                $siteadminlink = $this->getSession()->getPage()->find('named_exact', array('link', "'" . $siteadminstr . "'"));
                if ($siteadminlink) {
                    $siteadminlink->click();
                }
            }
        }

        // Get top level node.
        $node = $this->get_top_navigation_node($parentnodes[0]);

        // Expand all nodes.
        for ($i = 0; $i < $countparentnode; $i++) {
            if ($i > 0) {
                // Sub nodes within top level node.
                $node = $this->get_navigation_node($parentnodes[$i], $node);
            }

            // Keep expanding all sub-parents if js enabled.
            if ($this->running_javascript() && $node->hasAttribute('aria-expanded') &&
                ($node->getAttribute('aria-expanded') == "false")) {

                $xpath = "/p[contains(concat(' ', normalize-space(@class), ' '), ' tree_item ')]";
                $nodetoexpand = $node->find('xpath', $xpath);

                $this->ensure_node_is_visible($nodetoexpand);

                // If node is a link then some driver click in the middle of the node, which click on link and
                // page gets redirected. To ensure expansion works in all cases, check if the node to expand is a
                // link and if yes then click on link and wait for it to navigate to next page with node expanded.
                $nodetoexpandliteral = behat_context_helper::escape($parentnodes[$i]);
                $nodetoexpandxpathlink = $xpath . "/a[normalize-space(.)=" . $nodetoexpandliteral . "]";

                if ($nodetoexpandlink = $node->find('xpath', $nodetoexpandxpathlink)) {
                    $behatgeneralcontext = behat_context_helper::get('behat_general');
                    $nodetoexpandlink->click();
                    $behatgeneralcontext->wait_until_the_page_is_ready();
                } else {
                    $nodetoexpand->click();
                }

                // Wait for node to load, if not loaded before.
                $linode = $nodetoexpand->getParent();
                if ($linode && $linode->hasAttribute('data-loaded') && $linode->getAttribute('data-loaded') == "false") {
                    $jscondition = '(document.evaluate("' . $linode->getXpath() . '", document, null, '.
                        'XPathResult.ANY_TYPE, null).iterateNext().getAttribute(\'data-loaded\') == "true")';

                    $this->getSession()->wait(self::EXTENDED_TIMEOUT * 1000, $jscondition);
                }
            }
        }

        // Finally, click on requested node under navigation.
        $nodetextliteral = behat_context_helper::escape($nodetext);
        $xpath = "/ul/li/p[contains(concat(' ', normalize-space(@class), ' '), ' tree_item ')]" .
                "/a[normalize-space(.)=" . $nodetextliteral . "]";
        $nodetoclick = $node->find('xpath', $xpath);

        // Throw exception if no node found.
        if (!$nodetoclick) {
            throw new ExpectationException('Navigation node "' . $nodetext . '" not found under "' .
                implode($parentnodes, ' > ') . '"', $this->getSession());
        }

        $nodetoclick->click();
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
            "/span[normalize-space(.)=" . $nodetextliteral ."]]" .
            "|" .
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' content ')]/div" .
            "/ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
            "/li[p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
            "/a[normalize-space(.)=" . $nodetextliteral ."]]";

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
        $nodetextliteral = behat_context_helper::escape($nodetext);

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

    /**
     * Step to open the navigation bar if it is needed.
     *
     * The top log in and log out links are hidden when middle or small
     * size windows (or devices) are used. This step returns a step definition
     * clicking to expand the navbar if it is hidden.
     *
     * @Given /^I expand navigation bar$/
     */
    public function get_expand_navbar_step() {

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

        $this->execute('behat_general::i_click_on', array(".btn-navbar", "css_element"));
    }
}
