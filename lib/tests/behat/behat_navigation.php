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
        $hascollapsed = "p[@aria-expanded='false']";
        $notcollapsed = "p[@aria-expanded='true']";
        $match = "[normalize-space(.)={$nodetextliteral}]";

        // Avoid problems with quotes.
        $isbranch = ($branch) ? $hasbranch : '';
        if ($collapsed === true) {
            $iscollapsed = $hascollapsed;
        } else if ($collapsed === false) {
            $iscollapsed = $notcollapsed;
        } else {
            $iscollapsed = 'p';
        }

        // First check root nodes, it can be a span or link.
        $xpath  = "//ul{$hasblocktree}/li/{$hascollapsed}{$isbranch}/span{$match}|";
        $xpath  .= "//ul{$hasblocktree}/li/{$hascollapsed}{$isbranch}/a{$match}|";

        // Next search for the node containing the text within a link.
        $xpath .= "//ul{$hasblocktree}//ul/li/{$iscollapsed}{$isbranch}/a{$match}|";

        // Finally search for the node containing the text within a span.
        $xpath .= "//ul{$hasblocktree}//ul/li/{$iscollapsed}{$isbranch}/span{$match}";

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
     * @todo MDL-57281 deprecate in Moodle 3.1
     *
     * @throws ExpectationException
     * @param string $nodetext navigation node to click.
     * @param string $parentnodes comma seperated list of parent nodes.
     * @return void
     */
    public function i_navigate_to_node_in($nodetext, $parentnodes) {
        // This step needs to be deprecated and replaced with one of:
        // - I navigate to "PATH" in current page administration
        // - I navigate to "PATH" in site administration
        // - I navigate to course participants
        // - I navigate to "PATH" in the course gradebook
        // - I click on "LINK" "link" in the "Navigation" "block" .
        $parentnodes = array_map('trim', explode('>', $parentnodes));
        $this->select_node_in_navigation($nodetext, $parentnodes);
    }

    /**
     * Finds a node in the Navigation or Administration tree
     *
     * @param string $nodetext
     * @param array $parentnodes
     * @param string $nodetype node type (link or text)
     * @return NodeElement|null
     * @throws ExpectationException when one of the parent nodes is not found
     */
    protected function find_node_in_navigation($nodetext, $parentnodes, $nodetype = 'link') {
        // Site admin is different and needs special treatment.
        $siteadminstr = get_string('administrationsite');

        // Create array of all parentnodes.
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

            // The p node contains the aria jazz.
            $pnodexpath = "/p[contains(concat(' ', normalize-space(@class), ' '), ' tree_item ')]";
            $pnode = $node->find('xpath', $pnodexpath);

            // Keep expanding all sub-parents if js enabled.
            if ($pnode && $this->running_javascript() && $pnode->hasAttribute('aria-expanded') &&
                ($pnode->getAttribute('aria-expanded') == "false")) {

                $this->js_trigger_click($pnode);

                // Wait for node to load, if not loaded before.
                if ($pnode->hasAttribute('data-loaded') && $pnode->getAttribute('data-loaded') == "false") {
                    $jscondition = '(document.evaluate("' . $pnode->getXpath() . '", document, null, '.
                        'XPathResult.ANY_TYPE, null).iterateNext().getAttribute(\'data-loaded\') == "true")';

                    $this->getSession()->wait(self::EXTENDED_TIMEOUT * 1000, $jscondition);
                }
            }
        }

        // Finally, click on requested node under navigation.
        $nodetextliteral = behat_context_helper::escape($nodetext);
        $tagname = ($nodetype === 'link') ? 'a' : 'span';
        $xpath = "/ul/li/p[contains(concat(' ', normalize-space(@class), ' '), ' tree_item ')]" .
            "/{$tagname}[normalize-space(.)=" . $nodetextliteral . "]";
        return $node->find('xpath', $xpath);
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
            "[span[normalize-space(.)=" . $nodetextliteral ."] or a[normalize-space(.)=" . $nodetextliteral ."]]]" .
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
        $xpath = '//div[contains(@class,\'block_settings\')]//div[@id=\'settingsnav\']/ul/li[1]/p[1]/span';
        $node = $this->find('xpath', $xpath);
        array_unshift($parentnodes, $node->getText());
        $lastnode = array_pop($parentnodes);
        $this->select_node_in_navigation($lastnode, $parentnodes);
    }

    /**
     * Checks that current page administration contains text
     *
     * @Given /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should exist in current page administration$/
     *
     * @throws ExpectationException
     * @param string $element The locator of the specified selector.
     *     This may be a path, for example "Subscription mode > Forced subscription"
     * @param string $selectortype The selector type (link or text)
     * @return void
     */
    public function should_exist_in_current_page_administration($element, $selectortype) {
        $parentnodes = array_map('trim', explode('>', $element));
        // Find the name of the first category of the administration block tree.
        $xpath = '//div[contains(@class,\'block_settings\')]//div[@id=\'settingsnav\']/ul/li[1]/p[1]/span';
        $node = $this->find('xpath', $xpath);
        array_unshift($parentnodes, $node->getText());
        $lastnode = array_pop($parentnodes);

        if (!$this->find_node_in_navigation($lastnode, $parentnodes, strtolower($selectortype))) {
            throw new ExpectationException(ucfirst($selectortype) . ' "' . $element .
                '" not found in current page administration"', $this->getSession());
        }
    }

    /**
     * Checks that current page administration contains text
     *
     * @Given /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should not exist in current page administration$/
     *
     * @throws ExpectationException
     * @param string $element The locator of the specified selector.
     *     This may be a path, for example "Subscription mode > Forced subscription"
     * @param string $selectortype The selector type (link or text)
     * @return void
     */
    public function should_not_exist_in_current_page_administration($element, $selectortype) {
        $parentnodes = array_map('trim', explode('>', $element));
        // Find the name of the first category of the administration block tree.
        $xpath = '//div[contains(@class,\'block_settings\')]//div[@id=\'settingsnav\']/ul/li[1]/p[1]/span';
        $node = $this->find('xpath', $xpath);
        array_unshift($parentnodes, $node->getText());
        $lastnode = array_pop($parentnodes);

        if ($this->find_node_in_navigation($lastnode, $parentnodes, strtolower($selectortype))) {
            throw new ExpectationException(ucfirst($selectortype) . ' "' . $element .
                '" found in current page administration"', $this->getSession());
        }
    }

    /**
     * Go to site administration item
     *
     * @Given /^I navigate to "(?P<nodetext_string>(?:[^"]|\\")*)" in site administration$/
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
     * Opens the current users profile page in edit mode.
     *
     * @Given /^I open my profile in edit mode$/
     * @throws coding_exception
     * @return void
     */
    public function i_open_my_profile_in_edit_mode() {
        global $USER;

        $user = $this->get_session_user();
        $globuser = $USER;
        $USER = $user; // We need this set to the behat session user so we can call isloggedin.

        $systemcontext = context_system::instance();

        $bodynode = $this->find('xpath', 'body');
        $bodyclass = $bodynode->getAttribute('class');
        $matches = [];
        if (preg_match('/(?<=^course-|\scourse-)\d+/', $bodyclass, $matches) && !empty($matches)) {
            $courseid = intval($matches[0]);
        } else {
            $courseid = SITEID;
        }

        if (isloggedin() && !isguestuser($user) && !is_mnet_remote_user($user)) {
            if (is_siteadmin($user) ||  has_capability('moodle/user:update', $systemcontext)) {
                $url = new moodle_url('/user/editadvanced.php', array('id' => $user->id, 'course' => SITEID,
                    'returnto' => 'profile'));
            } else if (has_capability('moodle/user:editownprofile', $systemcontext)) {
                $userauthplugin = false;
                if (!empty($user->auth)) {
                    $userauthplugin = get_auth_plugin($user->auth);
                }
                if ($userauthplugin && $userauthplugin->can_edit_profile()) {
                    $url = $userauthplugin->edit_profile_url();
                    if (empty($url)) {
                        if (empty($course)) {
                            $url = new moodle_url('/user/edit.php', array('id' => $user->id, 'returnto' => 'profile'));
                        } else {
                            $url = new moodle_url('/user/edit.php', array('id' => $user->id, 'course' => $courseid,
                                'returnto' => 'profile'));
                        }
                    }

                }
            }
            $this->getSession()->visit($this->locate_path($url->out_as_local_url()));
        }

        // Restore global user variable.
        $USER = $globuser;
    }

    /**
     * Opens the course homepage.
     *
     * @Given /^I am on "(?P<coursefullname_string>(?:[^"]|\\")*)" course homepage$/
     * @throws coding_exception
     * @param string $coursefullname The full name of the course.
     * @return void
     */
    public function i_am_on_course_homepage($coursefullname) {
        global $DB;
        $course = $DB->get_record("course", array("fullname" => $coursefullname), 'id', MUST_EXIST);
        $url = new moodle_url('/course/view.php', ['id' => $course->id]);
        $this->getSession()->visit($this->locate_path($url->out_as_local_url(false)));
    }

    /**
     * Opens the course homepage with editing mode on.
     *
     * @Given /^I am on "(?P<coursefullname_string>(?:[^"]|\\")*)" course homepage with editing mode on$/
     * @throws coding_exception
     * @param string $coursefullname The course full name of the course.
     * @return void
     */
    public function i_am_on_course_homepage_with_editing_mode_on($coursefullname) {
        global $DB;
        $course = $DB->get_record("course", array("fullname" => $coursefullname), 'id', MUST_EXIST);
        $url = new moodle_url('/course/view.php', ['id' => $course->id]);
        $this->getSession()->visit($this->locate_path($url->out_as_local_url(false)));
        try {
            $this->execute("behat_forms::press_button", get_string('turneditingon'));
        } catch (Exception $e) {
            $this->execute("behat_navigation::i_navigate_to_in_current_page_administration", [get_string('turneditingon')]);
        }
    }
}
