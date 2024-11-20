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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ExpectationException as ExpectationException;
use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

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
     * Checks whether a navigation node is active within the block navigation.
     *
     * @Given i should see :name is active in navigation
     *
     * @throws ElementNotFoundException
     * @param string      $element The name of the nav elemnent to look for.
     * @return void
     */
    public function i_should_see_is_active_in_navigation($element) {
        $this->execute("behat_general::assert_element_contains_text",
            [$element, '.block_navigation .active_tree_node', 'css_element']);
    }

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
        $hascollapsed = "li[@aria-expanded='false']/p";
        $notcollapsed = "li[@aria-expanded='true']/p";
        $match = "[normalize-space(.)={$nodetextliteral}]";

        // Avoid problems with quotes.
        $isbranch = ($branch) ? $hasbranch : '';
        if ($collapsed === true) {
            $iscollapsed = $hascollapsed;
        } else if ($collapsed === false) {
            $iscollapsed = $notcollapsed;
        } else {
            $iscollapsed = 'li/p';
        }

        // First check root nodes, it can be a span or link.
        $xpath  = "//ul{$hasblocktree}/{$hascollapsed}{$isbranch}/span{$match}|";
        $xpath  .= "//ul{$hasblocktree}/{$hascollapsed}{$isbranch}/a{$match}|";

        // Next search for the node containing the text within a link.
        $xpath .= "//ul{$hasblocktree}//ul/{$iscollapsed}{$isbranch}/a{$match}|";

        // Finally search for the node containing the text within a span.
        $xpath .= "//ul{$hasblocktree}//ul/{$iscollapsed}{$isbranch}/span{$match}";

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
            $xpath = "//div[contains(concat(' ', @class, ' '), ' usermenu ')]//a[contains(concat(' ', @class, ' '), ' dropdown-toggle ')]";
            $this->execute("behat_general::i_click_on", array($this->escape($xpath), "xpath_element"));
        }

        // Now select the link.
        // The CSS path is always present, with or without JS.
        $csspath = ".usermenu .dropdown-menu";

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
        $this->execute('behat_general::i_click_on', [$node, 'NodeElement']);
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
        $this->execute('behat_general::i_click_on', [$node, 'NodeElement']);
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
                    $this->execute('behat_general::i_click_on', [$siteadminlink, 'NodeElement']);
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
            $linode = $pnode->getParent();

            // Keep expanding all sub-parents if js enabled.
            if ($pnode && $this->running_javascript() && $linode->hasAttribute('aria-expanded') &&
                ($linode->getAttribute('aria-expanded') == "false")) {
                $this->js_trigger_click($pnode);

                // Wait for node to load, if not loaded before.
                if ($linode->hasAttribute('data-loaded') && $linode->getAttribute('data-loaded') == "false") {
                    $jscondition = '(document.evaluate("' . $linode->getXpath() . '", document, null, '.
                        'XPathResult.ANY_TYPE, null).iterateNext().getAttribute(\'data-loaded\') == "true")';

                    $this->getSession()->wait(behat_base::get_extended_timeout() * 1000, $jscondition);
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
        $exception = new ExpectationException('Top navigation node "' . $nodetext . ' not found in "', $this->getSession());

        // First find in navigation block.
        $xpath = "//div[contains(concat(' ', normalize-space(@class), ' '), ' card-text ')]" .
                "/ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
                "/li[contains(concat(' ', normalize-space(@class), ' '), ' contains_branch ')]" .
                "/ul/li[contains(concat(' ', normalize-space(@class), ' '), ' contains_branch ')]" .
                "[p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
                "/*[contains(normalize-space(.), " . $nodetextliteral .")]]" .
                "|" .
                "//div[contains(concat(' ', normalize-space(@class), ' '), ' card-text ')]/div" .
                "/ul[contains(concat(' ', normalize-space(@class), ' '), ' block_tree ')]" .
                "/li[p[contains(concat(' ', normalize-space(@class), ' '), ' branch ')]" .
                "/*[contains(normalize-space(.), " . $nodetextliteral .")]]";

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
        if (!$this->evaluate_script($navbuttonjs)) {
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
        $nodelist = array_map('trim', explode('>', $nodetext));
        $this->select_from_administration_menu($nodelist);
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
        $nodes = array_map('trim', explode('>', $element));
        $nodetext = end($nodes);

        // Find administration menu.
        if (!$menuxpath = $this->find_page_action_menu()) {
            $menuxpath = $this->find_header_administration_menu() ?: $this->find_page_administration_menu(true);
        }

        $this->toggle_page_administration_menu($menuxpath);
        $this->execute('behat_general::should_exist_in_the', [$nodetext, $selectortype, $menuxpath, 'xpath_element']);
        $this->toggle_page_administration_menu($menuxpath);
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
        $nodes = array_map('trim', explode('>', $element));
        $nodetext = end($nodes);

        // Find administration menu.
        $menuxpath = $this->find_header_administration_menu() ?: $this->find_page_administration_menu();
        if (!$menuxpath) {
            // Menu not found, exit.
            return;
        }

        $this->toggle_page_administration_menu($menuxpath);
        $this->execute('behat_general::should_not_exist_in_the', [$nodetext, $selectortype, $menuxpath, 'xpath_element']);
        $this->toggle_page_administration_menu($menuxpath);
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
        $nodelist = array_map('trim', explode('>', $nodetext));
        $this->i_select_from_primary_navigation(get_string('administrationsite'));
        $this->select_on_administration_page($nodelist);
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
            $this->execute('behat_general::i_visit', [$url]);
        }

        // Restore global user variable.
        $USER = $globuser;
    }

    /**
     * Open a given page, belonging to a plugin or core component.
     *
     * The page-type are interpreted by each plugin to work out the
     * corresponding URL. See the resolve_url method in each class like
     * behat_mod_forum. That method should document which page types are
     * recognised, and how the name identifies them.
     *
     * For pages belonging to core, the 'core > ' bit is omitted.
     *
     * @When /^I am on the (?<page>[^ "]*) page$/
     * @When /^I am on the "(?<page>[^"]*)" page$/
     *
     * @param string $page the component and page name.
     *      E.g. 'Admin notifications' or 'core_user > Preferences'.
     * @throws Exception if the specified page cannot be determined.
     */
    public function i_am_on_page(string $page) {
        $this->execute('behat_general::i_visit', [$this->resolve_page_helper($page)]);
    }

    /**
     * Open a given page logged in as a given user.
     *
     * This is like the combination
     *   When I log in as "..."
     *   And I am on the "..." page
     * but with the advantage that you go straight to the desired page, without
     * having to wait for the Dashboard to load.
     *
     * @When /^I am on the (?<page>[^ "]*) page logged in as (?<username>[^ "]*)$/
     * @When /^I am on the "(?<page>[^"]*)" page logged in as (?<username>[^ "]*)$/
     * @When /^I am on the (?<page>[^ "]*) page logged in as "(?<username>[^ "]*)"$/
     * @When /^I am on the "(?<page>[^"]*)" page logged in as "(?<username>[^ "]*)"$/
     *
     * @param string $page the type of page. E.g. 'Admin notifications' or 'core_user > Preferences'.
     * @param string $username the name of the user to log in as. E.g. 'admin'.
     * @throws Exception if the specified page cannot be determined.
     */
    public function i_am_on_page_logged_in_as(string $page, string $username) {
        self::execute('behat_auth::i_log_in_as', [$username, $this->resolve_page_helper($page)]);
    }

    /**
     * Helper used by i_am_on_page() and i_am_on_page_logged_in_as().
     *
     * @param string $page the type of page. E.g. 'Admin notifications' or 'core_user > Preferences'.
     * @return moodle_url the corresponding URL.
     */
    protected function resolve_page_helper(string $page): moodle_url {
        list($component, $name) = $this->parse_page_name($page);
        if ($component === 'core') {
            return $this->resolve_core_page_url($name);
        } else {
            $context = behat_context_helper::get('behat_' . $component);
            return $context->resolve_page_url($name);
        }
    }

    /**
     * Parse a full page name like 'Admin notifications' or 'core_user > Preferences'.
     *
     * E.g. parsing 'mod_quiz > View' gives ['mod_quiz', 'View'].
     *
     * @param string $page the full page name
     * @return array with two elements, component and page name.
     */
    protected function parse_page_name(string $page): array {
        $dividercount = substr_count($page, ' > ');
        if ($dividercount === 0) {
            return ['core', $page];
        } else if ($dividercount >= 1) {
            [$component, $name] = explode(' > ', $page, 2);
            if ($component === 'core') {
                throw new coding_exception('Do not specify the component "core > ..." for core pages.');
            }
            return [$component, $name];
        } else {
            throw new coding_exception('The page name must be in the form ' .
                    '"{page-name}" for core pages, or "{component} > {page-name}" ' .
                    'for pages belonging to other components. ' .
                    'For example "Admin notifications" or "mod_quiz > View".');
        }
    }

    /**
     * Open a given instance of a page, belonging to a plugin or core component.
     *
     * The instance identifier and page-type are interpreted by each plugin to
     * work out the corresponding URL. See the resolve_page_instance_url method
     * in each class like behat_mod_forum. That method should document which page
     * types are recognised, and how the name identifies them.
     *
     * For pages belonging to core, the 'core > ' bit is omitted.
     *
     * @When /^I am on the (?<identifier>[^ "]*) (?<type>[^ "]*) page$/
     * @When /^I am on the "(?<identifier>[^"]*)" "(?<type>[^"]*)" page$/
     * @When /^I am on the (?<identifier>[^ "]*) "(?<type>[^"]*)" page$/
     * @When /^I am on the "(?<identifier>[^"]*)" (?<type>[^ "]*) page$/
     *
     * @param string $identifier identifies the particular page. E.g. 'Test quiz'.
     * @param string $type the component and page type. E.g. 'mod_quiz > View'.
     * @throws Exception if the specified page cannot be determined.
     */
    public function i_am_on_page_instance(string $identifier, string $type) {
        $this->execute('behat_general::i_visit', [$this->resolve_page_instance_helper($identifier, $type)]);
    }

    /**
     * Open a given page logged in as a given user.
     *
     * This is like the combination
     *   When I log in as "..."
     *   And I am on the "..." "..." page
     * but with the advantage that you go straight to the desired page, without
     * having to wait for the Dashboard to load.
     *
     * @When /^I am on the (?<identifier>[^ "]*) (?<type>[^ "]*) page logged in as (?<username>[^ "]*)$/
     * @When /^I am on the "(?<identifier>[^"]*)" "(?<type>[^"]*)" page logged in as (?<username>[^ "]*)$/
     * @When /^I am on the (?<identifier>[^ "]*) "(?<type>[^"]*)" page logged in as (?<username>[^ "]*)$/
     * @When /^I am on the "(?<identifier>[^"]*)" (?<type>[^ "]*) page logged in as (?<username>[^ "]*)$/
     * @When /^I am on the (?<identifier>[^ "]*) (?<type>[^ "]*) page logged in as "(?<username>[^"]*)"$/
     * @When /^I am on the "(?<identifier>[^"]*)" "(?<type>[^"]*)" page logged in as "(?<username>[^"]*)"$/
     * @When /^I am on the (?<identifier>[^ "]*) "(?<type>[^"]*)" page logged in as "(?<username>[^"]*)"$/
     * @When /^I am on the "(?<identifier>[^"]*)" (?<type>[^ "]*) page logged in as "(?<username>[^"]*)"$/
     *
     * @param string $identifier identifies the particular page. E.g. 'Test quiz'.
     * @param string $type the component and page type. E.g. 'mod_quiz > View'.
     * @param string $username the name of the user to log in as. E.g. 'student'.
     * @throws Exception if the specified page cannot be determined.
     */
    public function i_am_on_page_instance_logged_in_as(string $identifier,
            string $type, string $username) {
        self::execute('behat_auth::i_log_in_as',
                [$username, $this->resolve_page_instance_helper($identifier, $type)]);
    }

    /**
     * Helper used by i_am_on_page() and i_am_on_page_logged_in_as().
     *
     * @param string $identifier identifies the particular page. E.g. 'Test quiz'.
     * @param string $pagetype the component and page type. E.g. 'mod_quiz > View'.
     * @return moodle_url the corresponding URL.
     */
    protected function resolve_page_instance_helper(string $identifier, string $pagetype): moodle_url {
        list($component, $type) = $this->parse_page_name($pagetype);
        if ($component === 'core') {
            return $this->resolve_core_page_instance_url($type, $identifier);
        } else {
            $context = behat_context_helper::get('behat_' . $component);
            return $context->resolve_page_instance_url($type, $identifier);
        }
    }

    /**
     * Convert core page names to URLs for steps like 'When I am on the "[page name]" page'.
     *
     * Recognised page names are:
     * | Homepage            | Homepage (normally dashboard).                                 |
     * | Admin notifications | Admin notification screen.                                     |
     *
     * @param string $name identifies which identifies this page, e.g. 'Homepage', 'Admin notifications'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_core_page_url(string $name): moodle_url {
        switch ($name) {
            case 'Homepage':
                return new moodle_url('/');

            case 'My courses':
                return new moodle_url('/my/courses.php');

            case 'Admin notifications':
                return new moodle_url('/admin/');

            case 'Content bank':
                return new moodle_url('/contentbank/');

            case 'My private files':
                return new moodle_url('/user/files.php');

            case 'System logs report':
                return new moodle_url('/report/log/index.php');

            case 'Profile':
                return new moodle_url('/user/view.php');

            case 'Profile advanced editing':
                return new moodle_url('/user/editadvanced.php', ['returnto' => 'profile']);

            case 'Profile editing':
                return new moodle_url('/user/edit.php', ['returnto' => 'profile']);

            default:
                throw new Exception('Unrecognised core page type "' . $name . '."');
        }
    }

    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "[page type]" page'.
     *
     * Recognised page names are:
     * | Page type                       | Identifier meaning        | description                                                      |
     * | Category                        | category idnumber         | List of courses in that category.                                |
     * | Course                          | course shortname          | Main course home pag                                             |
     * | Course editing                  | course shortname          | Edit settings page for the course                                |
     * | Activity                        | activity idnumber         | Start page for that activity                                     |
     * | Activity editing                | activity idnumber         | Edit settings page for that activity                             |
     * | [modname] Activity              | activity name or idnumber | Start page for that activity                                     |
     * | [modname] Activity editing      | activity name or idnumber | Edit settings page for that activity                             |
     * | Backup                          | course shortname          | Course to backup                                                 |
     * | Import                          | course shortname          | Course import from                                               |
     * | Restore                         | course shortname          | Course to restore from                                           |
     * | Reset                           | course shortname          | Course to reset                                                  |
     * | Course copy                     | course shortname          | Course to copy                                                   |
     * | Groups                          | course shortname          | Groups page for the course                                       |
     * | Groups overview                 | course shortname          | Groups overview page for the course                              |
     * | Groupings                       | course shortname          | Groupings page for the course                                    |
     * | Permissions                     | course shortname          | Permissions page for the course                                  |
     * | Enrolment methods               | course shortname          | Enrolment methods for the course                                 |
     * | Enrolled users                  | course shortname          | The main participants page                                       |
     * | Other users                     | course shortname          | The course other users page                                      |
     * | Course profile                  | course shortname          | The current user's profile for this course                       |
     * | Course profile editing          | course shortname          | The current user's profile editing page for this course          |
     * | Course profile advanced editing | course shortname          | The current user's advanced profile editing page for this course |
     *
     * Examples:
     *
     * When I am on the "Welcome to ECON101" "forum activity" page logged in as student1
     *
     * @param string $type identifies which type of page this is, e.g. 'Category page'.
     * @param string $identifier identifies the particular page, e.g. 'test-cat'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_core_page_instance_url(string $type, string $identifier): moodle_url {
        $type = strtolower($type);
        $coursenotfoundexception = new Exception(
            "The specified course with shortname, fullname, or idnumber '{$identifier}' does not exist",
        );

        switch ($type) {
            case 'category':
                $categoryid = $this->get_category_id($identifier);
                if (!$categoryid) {
                    throw new Exception('The specified category with idnumber "' . $identifier . '" does not exist');
                }
                return new moodle_url('/course/index.php', ['categoryid' => $categoryid]);

            case 'course editing':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/course/edit.php', ['id' => $courseid]);

            case 'course':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/course/view.php', ['id' => $courseid]);

            case 'activity':
                $cm = $this->get_course_module_for_identifier($identifier);
                if (!$cm) {
                    throw new Exception('The specified activity with idnumber "' . $identifier . '" does not exist');
                }
                return $cm->url;

            case 'activity editing':
                $cm = $this->get_course_module_for_identifier($identifier);
                if (!$cm) {
                    throw new Exception('The specified activity with idnumber "' . $identifier . '" does not exist');
                }
                return new moodle_url('/course/modedit.php', [
                    'update' => $cm->id,
                ]);
            case 'backup':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/backup/backup.php', ['id' => $courseid]);
            case 'import':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/backup/import.php', ['id' => $courseid]);
            case 'restore':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                $context = context_course::instance($courseid);
                return new moodle_url('/backup/restorefile.php', ['contextid' => $context->id]);
            case 'reset':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/course/reset.php', ['id' => $courseid]);
            case 'course copy':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/backup/copy.php', ['id' => $courseid]);
            case 'groups':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/group/index.php', ['id' => $courseid]);
            case 'groups overview':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/group/overview.php', ['id' => $courseid]);
            case 'groupings':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/group/groupings.php', ['id' => $courseid]);
            case 'permissions':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                $context = context_course::instance($courseid);
                return new moodle_url('/admin/roles/permissions.php', ['contextid' => $context->id]);
            case 'enrolment methods':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/enrol/instances.php', ['id' => $courseid]);
            case 'enrolled users':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/user/index.php', ['id' => $courseid]);
            case 'other users':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/enrol/otherusers.php', ['id' => $courseid]);
            case 'renameroles':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/enrol/renameroles.php', ['id' => $courseid]);

            case 'course profile':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/user/user.php', ['course' => $courseid]);

            case 'course profile editing':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/user/edit.php', [
                    'course' => $courseid,
                    'returnto' => 'profile',
                ]);

            case 'course profile advanced editing':
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url('/user/editadvanced.php', [
                    'course' => $courseid,
                    'returnto' => 'profile',
                ]);
        }

        // This next section handles page types starting with an activity name. For example:
        // "forum activity" or "quiz activity editing".
        $parts = explode(' ', $type);
        if (count($parts) > 1) {
            $modname = $parts[0];
            if ($parts[1] === 'activity') {
                $cm = $this->get_cm_by_activity_name($modname, $identifier);

                if (count($parts) == 2) {
                    // View page.
                    return new moodle_url($cm->url);
                }

                if ($parts[2] === 'editing') {
                    // Edit settings page.
                    return new moodle_url('/course/modedit.php', ['update' => $cm->id]);
                }

                if ($parts[2] === 'roles') {
                    // Locally assigned roles page.
                    return new moodle_url('/admin/roles/assign.php', ['contextid' => $cm->context->id]);
                }

                if ($parts[2] === 'permissions') {
                    // Permissions page.
                    return new moodle_url('/admin/roles/permissions.php', ['contextid' => $cm->context->id]);
                }

            } else if ($parts[1] === 'index' && count($parts) == 2) {
                $courseid = $this->get_course_id($identifier);
                if (!$courseid) {
                    throw $coursenotfoundexception;
                }
                return new moodle_url("/mod/$modname/index.php", ['id' => $courseid]);
            }
        }

        throw new Exception('Unrecognised core page type "' . $type . '."');
    }

    /**
     * Opens a new tab with given name on the same URL as current page and switches to it.
     *
     * @param string $name Tab name that can be used for switching later (no whitespace)
     * @When /^I open a tab named "(?<name>[^"]*)" on the current page$/
     */
    public function i_open_a_tab_on_the_current_page(string $name): void {
        $this->open_tab($name, 'location.href');
    }

    /**
     * Opens a new tab with given name on specified page, and switches to it.
     *
     * @param string $name Tab name that can be used for switching later (no whitespace)
     * @param string $page Page name
     * @When /^I open a tab named "(?<name>[^"]*)" on the "(?<page>[^"]*)" page$/
     */
    public function i_open_a_tab_on_the_page(string $name, string $page): void {
        if ($page === 'current') {
            $jstarget = 'location.href';
        } else {
            $jstarget = '"' . addslashes_js($this->resolve_page_helper($page)->out(false)) . '"';
        }
        $this->open_tab($name, $jstarget);
    }

    /**
     * Opens a new tab with given name (on specified page), and switches to it.
     *
     * @param string $name Tab name that can be used for switching later (no whitespace)
     * @param string $identifier Page identifier
     * @param string $page Page type
     * @When /^I open a tab named "(?<name>[^"]*)" on the "(?<identifier>[^"]*)" "(?<page>[^"]*)" page$/
     */
    public function i_open_a_tab_on_the_page_instance(string $name, string $identifier, string $page): void {
        $this->open_tab($name, '"' . addslashes_js(
            $this->resolve_page_instance_helper($identifier, $page)->out(false)) . '"');
    }

    /**
     * Opens a new tab at the given target URL.
     *
     * @param string $name Name for tab
     * @param string $jstarget Target in JavaScript syntax, i.e. if a string, must be quoted
     */
    protected function open_tab(string $name, string $jstarget): void {
        // Tab names aren't allowed spaces, and our JavaScript below doesn't do any escaping.
        if (clean_param($name, PARAM_ALPHANUMEXT) !== $name) {
            throw new Exception('Tab name may not contain whitespace or special characters: "' . $name . '"');
        }

        // Normally you can't open a tab unless in response to a user action, but presumably Behat
        // is exempt from this restriction, because it works to just open it directly.
        $this->execute_script('window.open(' . $jstarget . ', "' . $name . '");');
        $this->execute('behat_general::switch_to_window', [$name]);
    }

    /**
     * Opens the course homepage. (Consider using 'I am on the "shortname" "Course" page' step instead.)
     *
     * @Given /^I am on "(?P<coursefullname_string>(?:[^"]|\\")*)" course homepage$/
     * @throws coding_exception
     * @param string $coursefullname The full name of the course.
     * @return void
     */
    public function i_am_on_course_homepage($coursefullname) {
        $courseid = $this->get_course_id($coursefullname);
        $url = new moodle_url('/course/view.php', ['id' => $courseid]);
        $this->execute('behat_general::i_visit', [$url]);
    }

    /**
     * Open the course homepage with editing mode enabled.
     *
     * @param string $coursefullname The course full name of the course.
     */
    public function i_am_on_course_homepage_with_editing_mode_on($coursefullname) {
        $this->i_am_on_course_homepage_with_editing_mode_set_to($coursefullname, 'on');
    }

    /**
     * Open the course homepage with editing mode set to either on, or off.
     *
     * @Given I am on :coursefullname course homepage with editing mode :onoroff
     * @throws coding_exception
     * @param string $coursefullname The course full name of the course.
     * @param string $onoroff Whehter to switch editing on, or off.
     */
    public function i_am_on_course_homepage_with_editing_mode_set_to(string $coursefullname, string $onoroff): void {
        if ($onoroff !== 'on' && $onoroff !== 'off') {
            throw new coding_exception("Unknown editing mode '{$onoroff}'. Accepted values are 'on' and 'off'");
        }

        $courseid = $this->get_course_id($coursefullname);
        $context = context_course::instance($courseid);
        $courseurl = new moodle_url('/course/view.php', ['id' => $courseid]);

        $editmodeurl = new moodle_url('/editmode.php', [
            'context' => $context->id,
            'pageurl' => $courseurl->out(true),
            'setmode' => ($onoroff === 'on' ? 1 : 0),
        ]);
        $this->execute('behat_general::i_visit', [$editmodeurl]);
    }

    /**
     * Opens the flat navigation drawer if it is not already open
     *
     * @When /^I open flat navigation drawer$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     */
    public function i_open_flat_navigation_drawer() {
        if (!$this->running_javascript()) {
            // Navigation drawer is always open without JS.
            return;
        }
        $xpath = "//button[contains(@data-action,'toggle-drawer')]";
        $node = $this->find('xpath', $xpath);
        $expanded = $node->getAttribute('aria-expanded');
        if ($expanded === 'false') {
            $this->execute('behat_general::i_click_on', [$node, 'NodeElement']);
            $this->ensure_node_attribute_is_set($node, 'aria-expanded', 'true');
        }
    }

    /**
     * Closes the flat navigation drawer if it is open (does nothing if JS disabled)
     *
     * @When /^I close flat navigation drawer$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     */
    public function i_close_flat_navigation_drawer() {
        if (!$this->running_javascript()) {
            // Navigation drawer can not be closed without JS.
            return;
        }
        $xpath = "//button[contains(@data-action,'toggle-drawer')]";
        $node = $this->find('xpath', $xpath);
        $expanded = $node->getAttribute('aria-expanded');
        if ($expanded === 'true') {
            $this->execute('behat_general::i_click_on', [$node, 'NodeElement']);
        }
    }

    /**
     * Clicks link with specified id|title|alt|text in the primary navigation
     *
     * @When /^I select "(?P<link_string>(?:[^"]|\\")*)" from primary navigation$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $link
     */
    public function i_select_from_primary_navigation(string $link) {
        $this->execute('behat_general::i_click_on_in_the',
            [$link, 'link', '.primary-navigation .moremenu.navigation', 'css_element']
        );
    }

    /**
     * Clicks link with specified id|title|alt|text in the secondary navigation
     *
     * @When I select :link from secondary navigation
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $link
     */
    public function i_select_from_secondary_navigation(string $link) {
        $this->execute('behat_general::i_click_on_in_the',
            [$link, 'link', '.secondary-navigation .moremenu.navigation', 'css_element']
        );
    }

    /**
     * If we are not on the course main page, click on the course link in the navbar
     */
    protected function go_to_main_course_page() {
        $url = $this->getSession()->getCurrentUrl();
        if (!preg_match('|/course/view.php\?id=[\d]+$|', $url)) {
            $node = $this->find('xpath',
                '//header//div[@id=\'page-navbar\']//a[contains(@href,\'/course/view.php?id=\')]'
            );
            $this->execute('behat_general::i_click_on', [$node, 'NodeElement']);
        }
    }

    /**
     * Finds and clicks a link on the admin page (site administration or course administration)
     *
     * @param array $nodelist
     */
    protected function select_on_administration_page($nodelist) {
        $parentnodes = $nodelist;
        $lastnode = array_pop($parentnodes);
        $xpath = '//section[@id=\'region-main\']';

        // Check if there is a separate tab for this submenu of the page. If found go to it.
        if ($parentnodes) {
            $tabname = behat_context_helper::escape($parentnodes[0]);
            $tabxpath = '//ul[@role=\'tablist\']/li/a[contains(normalize-space(.), ' . $tabname . ')]';
            $menubarxpath = '//ul[@role=\'menubar\']/li/a[contains(normalize-space(.), ' . $tabname . ')]';
            $linkname = behat_context_helper::escape(get_string('moremenu'));
            $menubarmorexpath = '//ul[contains(@class,\'more-nav\')]/li/a[contains(normalize-space(.), ' . $linkname . ')]';
            $tabnode = $this->getSession()->getPage()->find('xpath', $tabxpath);
            $menunode = $this->getSession()->getPage()->find('xpath', $menubarxpath);
            $menubuttons = $this->getSession()->getPage()->findAll('xpath', $menubarmorexpath);
            if ($tabnode || $menunode) {
                $node = is_object($tabnode) ? $tabnode : $menunode;
                if ($this->running_javascript()) {
                    $this->execute('behat_general::i_click_on', [$node, 'NodeElement']);
                    // Click on the tab and add 'active' tab to the xpath.
                    $xpath .= '//div[contains(@class,\'active\')]';
                } else {
                    // Add the tab content selector to the xpath.
                    $tabid = behat_context_helper::escape(ltrim($node->getAttribute('href'), '#'));
                    $xpath .= '//div[@id = ' . $tabid . ']';
                }
                array_shift($parentnodes);
            } else if (count($menubuttons) > 0) {
                try {
                    $menubuttons[0]->isVisible();
                    try {
                        $this->execute('behat_general::i_click_on', [$menubuttons[1], 'NodeElement']);
                    } catch (Exception $e) {
                        $this->execute('behat_general::i_click_on', [$menubuttons[0], 'NodeElement']);
                    }
                    $moreitemxpath = '//ul[@data-region=\'moredropdown\']/li/a[contains(normalize-space(.), ' . $tabname . ')]';
                    if ($morenode = $this->getSession()->getPage()->find('xpath', $moreitemxpath)) {
                        $this->execute('behat_general::i_click_on', [$morenode, 'NodeElement']);
                        $xpath .= '//div[contains(@class,\'active\')]';
                        array_shift($parentnodes);
                    }
                } catch (Exception $e) {
                }
            }
        }

        // Find a section with the parent name in it.
        if ($parentnodes) {
            // Find the section on the page (links may be repeating in different sections).
            $section = behat_context_helper::escape($parentnodes[0]);
            $xpath .= '//div[@class=\'row\' and contains(.,'.$section.')]';
        }

        // Find a link and click on it.
        $linkname = behat_context_helper::escape($lastnode);
        $xpathlink = $xpathbutton = $xpath;
        $xpathlink .= '//a[contains(normalize-space(.), ' . $linkname . ')]';
        $xpathbutton .= '//button[contains(normalize-space(.), ' . $linkname . ')]';
        if ($node = $this->getSession()->getPage()->find('xpath', $xpathbutton)) {
            $this->execute('behat_general::i_click_on', [$node, 'NodeElement']);
        } else if (!$node = $this->getSession()->getPage()->find('xpath', $xpathlink)) {
             throw new ElementNotFoundException($this->getSession(), 'Link "' . join(' > ', $nodelist) . '"');
        } else {
            $this->execute('behat_general::i_click_on', [$node, 'NodeElement']);
        }
    }

    /**
     * Locates the administration menu in the <header> element and returns its xpath
     *
     * @param bool $mustexist if specified throws an exception if menu is not found
     * @return null|string
     */
    protected function find_header_administration_menu($mustexist = false) {
        $menuxpath = '//div[contains(@class,\'secondary-navigation\')]//nav[contains(@class,\'moremenu\')]';

        if ($mustexist) {
            $exception = new ElementNotFoundException($this->getSession(), 'Page header administration menu');
            $this->find('xpath', $menuxpath, $exception);
        } else if (!$this->getSession()->getPage()->find('xpath', $menuxpath)) {
            return null;
        }
        return $menuxpath;
    }

    /**
     * Locates the administration menu on the page (but not in the header) and returns its xpath
     *
     * @param bool $mustexist if specified throws an exception if menu is not found
     * @return null|string
     */
    protected function find_page_administration_menu($mustexist = false) {
        $menuxpath = '//div[@id=\'region-main-settings-menu\']';
        if ($mustexist) {
            $exception = new ElementNotFoundException($this->getSession(), 'Page administration menu');
            $this->find('xpath', $menuxpath, $exception);
        } else if (!$this->getSession()->getPage()->find('xpath', $menuxpath)) {
            return null;
        }
        return $menuxpath;
    }

    /**
     * Locates the action menu on the page (but not in the header) and returns its xpath
     *
     * @param null|bool $mustexist if specified throws an exception if menu is not found
     * @return null|string
     */
    protected function find_page_action_menu($mustexist = false) {
        $menuxpath = '//div[@id=\'action-menu-0-menubar\']';

        if ($mustexist) {
            $exception = new ElementNotFoundException($this->getSession(), 'Page check');
            $this->find('xpath', $menuxpath, $exception);
        } else if (!$this->getSession()->getPage()->find('xpath', $menuxpath)) {
            return null;
        }
        return $menuxpath;
    }

    /**
     * Toggles administration menu
     *
     * @param string $menuxpath (optional) xpath to the page administration menu if already known
     */
    protected function toggle_page_administration_menu($menuxpath = null) {
        if (!$menuxpath) {
            $menuxpath = $this->find_header_administration_menu() ?: $this->find_page_administration_menu();
        }
        if ($menuxpath && $this->running_javascript()) {
            $node = $this->find('xpath', $menuxpath . '//a[@data-toggle=\'dropdown\']');
            if ($node->isVisible()) {
                $this->execute('behat_general::i_click_on', [$node, 'NodeElement']);
            }
        }
    }

    /**
     * Finds a page edit cog and select an item from it
     *
     * If the page edit cog is in the page header and the item is not found there, click "More..." link
     * and find the item on the course/frontpage administration page
     *
     * @param array $nodelist
     * @throws ElementNotFoundException
     */
    protected function select_from_administration_menu($nodelist) {
        // Find administration menu.
        if ($menuxpath = $this->find_header_administration_menu()) {
            $isheader = true;
        } else if ($menuxpath = $this->find_page_action_menu(true)) {
            $isheader = false;
        } else {
            $menuxpath = $this->find_page_administration_menu(true);
            $isheader = false;
        }

        $this->execute('behat_navigation::toggle_page_administration_menu', [$menuxpath]);

        $firstnode = $nodelist[0];
        $firstlinkname = behat_context_helper::escape($firstnode);
        $firstlink = $this->getSession()->getPage()->find('xpath',
            $menuxpath . '//a[contains(normalize-space(.), ' . $firstlinkname . ')]'
        );

        if (!$isheader || count($nodelist) == 1) {
            $lastnode = end($nodelist);
            $linkname = behat_context_helper::escape($lastnode);
            $link = $this->getSession()->getPage()->find('xpath', $menuxpath . '//a[contains(normalize-space(.), ' . $linkname . ')]');
            if ($link) {
                $this->execute('behat_general::i_click_on', [$link, 'NodeElement']);
                return;
            }
        } else if ($firstlink) {
            $this->execute('behat_general::i_click_on', [$firstlink, 'NodeElement']);
            array_splice($nodelist, 0, 1);
            $this->select_on_administration_page($nodelist);
            return;
        }

        if ($isheader) {
            // Front page administration will have subnodes under "More...".
            $linkname = behat_context_helper::escape(get_string('morenavigationlinks'));
            $link = $this->getSession()->getPage()->find('xpath',
                $menuxpath . '//a[contains(normalize-space(.), ' . $linkname . ')]'
            );
            // Course administration will have subnodes under "Course administration".
            $courselinkname = behat_context_helper::escape(get_string('courseadministration'));
            $courselink = $this->getSession()->getPage()->find('xpath',
                $menuxpath . '//a[contains(normalize-space(.), ' . $courselinkname . ')]'
            );
            if ($link) {
                $this->execute('behat_general::i_click_on', [$link, 'NodeElement']);
                $this->select_on_administration_page($nodelist);
                return;
            } else if ($courselink) {
                $this->execute('behat_general::i_click_on', [$courselink, 'NodeElement']);
                $this->select_on_administration_page($nodelist);
                return;
            }
        }

        throw new ElementNotFoundException($this->getSession(),
                'Link "' . join(' > ', $nodelist) . '" in the current page edit menu"');
    }

    /**
     * Visit a fixture page for testing stuff that is not available in core.
     *
     * Please always, to prevent unwanted requests, protect behat fixture files with:
     *     defined('BEHAT_SITE_RUNNING') || die();
     *
     * @Given /^I am on fixture page "(?P<url_string>(?:[^"]|\\")*)"$/
     * @param string $url local path to fixture page
     */
    public function i_am_on_fixture_page($url) {
        $fixtureregex = '|^/[a-z0-9_\-/]*/tests/behat/fixtures/[a-z0-9_\-]*\.php$|';
        if (!preg_match($fixtureregex, $url)) {
            throw new coding_exception("URL {$url} is not a fixture URL");
        }
        $this->execute('behat_general::i_visit', [$url]);
    }

    /**
     * First checks to see if we are on this page via the breadcrumb. If not we then attempt to follow the link name given.
     *
     * @param  string $pagename Name of the breadcrumb item to check and follow.
     * @Given /^I follow the breadcrumb "(?P<url_string>(?:[^"]|\\")*)"$/
     */
    public function go_to_breadcrumb_location(string $pagename): void {
        $link = $this->getSession()->getPage()->find(
                'xpath',
                "//nav[@aria-label='Navigation bar']/ol/li[last()][contains(normalize-space(.), '" . $pagename . "')]"
        );
        if (!$link) {
            $this->execute("behat_general::i_click_on_in_the", [$pagename, 'link', 'page', 'region']);
        }
    }

    /**
     * Checks whether an item exists in the user menu.
     *
     * @Given :itemtext :selectortype should exist in the user menu
     * @Given :itemtext :selectortype should :not exist in the user menu
     *
     * @throws ElementNotFoundException
     * @param string $itemtext The menu item to find
     * @param string $selectortype The selector type
     * @param string|null $not Instructs to checks whether the element does not exist in the user menu, if defined
     * @return void
     */
    public function should_exist_in_user_menu($itemtext, $selectortype, $not = null) {
        $callfunction = is_null($not) ? 'should_exist_in_the' : 'should_not_exist_in_the';
        $this->execute("behat_general::{$callfunction}",
            [$itemtext, $selectortype, $this->get_user_menu_xpath(), 'xpath_element']);
    }

    /**
     * Checks whether an item exists in a given user submenu.
     *
     * @Given :itemtext :selectortype should exist in the :submenuname user submenu
     * @Given :itemtext :selectortype should :not exist in the :submenuname user submenu
     *
     * @throws ElementNotFoundException
     * @param string $itemtext The submenu item to find
     * @param string $selectortype The selector type
     * @param string $submenuname The name of the submenu
     * @param string|null $not Instructs to checks whether the element does not exist in the user menu, if defined
     * @return void
     */
    public function should_exist_in_user_submenu($itemtext, $selectortype, $submenuname, $not = null) {
        $callfunction = is_null($not) ? 'should_exist_in_the' : 'should_not_exist_in_the';
        $this->execute("behat_general::{$callfunction}",
            [$itemtext, $selectortype, $this->get_user_submenu_xpath($submenuname), 'xpath_element']);
    }

    /**
     * Checks whether a given user submenu is visible.
     *
     * @Then /^I should see "(?P<submenu_string>[^"]*)" user submenu$/
     *
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @param string $submenuname The name of the submenu
     * @return void
     */
    public function i_should_see_user_submenu($submenuname) {
        $this->execute('behat_general::should_be_visible',
            array($this->get_user_submenu_xpath($submenuname), 'xpath_element'));
    }

    /**
     * Return the xpath for the user menu element.
     *
     * @return string The xpath
     */
    protected function get_user_menu_xpath() {
        return "//div[contains(concat(' ', @class, ' '),  ' usermenu ')]" .
            "//div[contains(concat(' ', @class, ' '), ' dropdown-menu ')]" .
            "//div[@id='carousel-item-main']";
    }

    /**
     * Return the xpath for a given user submenu element.
     *
     * @param string $submenuname The name of the submenu
     * @return string The xpath
     */
    protected function get_user_submenu_xpath($submenuname) {
        return "//div[contains(concat(' ', @class, ' '),  ' usermenu ')]" .
            "//div[contains(concat(' ', @class, ' '), ' dropdown-menu ')]" .
            "//div[contains(concat(' ', @class, ' '), ' submenu ')][@aria-label='" . $submenuname . "']";
    }

    /**
     * Returns whether the user can edit the current page.
     *
     * @return bool
     */
    protected function is_editing_on() {
        $body = $this->find('xpath', "//body", false, false, 0);
        return $body->hasClass('editing');
    }

    /**
     * Turns editing mode on.
     * @Given I switch editing mode on
     * @Given I turn editing mode on
     */
    public function i_turn_editing_mode_on() {
        $this->execute('behat_forms::i_set_the_field_to', [get_string('editmode'), 1]);

        if (!$this->running_javascript()) {
            $this->execute('behat_general::i_click_on', [
                get_string('setmode', 'core'),
                'button',
            ]);
        }

        if (!$this->is_editing_on()) {
            throw new ExpectationException('The edit mode could not be turned on', $this->getSession());
        }
    }

    /**
     * Turns editing mode off.
     * @Given I switch editing mode off
     * @Given I turn editing mode off
     */
    public function i_turn_editing_mode_off() {
        $this->execute('behat_forms::i_set_the_field_to', [get_string('editmode'), 0]);

        if (!$this->running_javascript()) {
            $this->execute('behat_general::i_click_on', [
                get_string('setmode', 'core'),
                'button',
            ]);
        }

        if ($this->is_editing_on()) {
            throw new ExpectationException('The edit mode could not be turned off', $this->getSession());
        }
    }

    /**
     * The named item should exist in the named dropdown.
     *
     * @Then /^the "(?P<item_string>[^"]*)" item should (?P<not_bool>not )?exist in the "(?P<dropdown_string>[^"]*)" dropdown$/
     * @Then /^the "(?P<item_string>[^"]*)" item should (?P<not_bool>not )?exist in the "(?P<dropdown_string>[^"]*)" dropdown of the "(?P<container_string>[^"]*)" "(?P<containertype_string>[^"]*)"$/
     * @param string $item The text on the dropdown menu item
     * @param bool $not Whether to negate this search
     * @param string $dropdown The name of the dropdown
     * @param string $container The name of the container
     * @param string $containertype The type of the container
     */
    public function should_exist_in_dropdown(
        string $item,
        bool $not,
        string $dropdown,
        ?string $container = null,
        ?string $containertype = null,
    ): void {
        $containernode = null;
        if ($container && $containertype) {
            $containernode = $this->find(
                selector: $containertype,
                locator: $container,
                node: null,
            );
        }
        $this->should_exist_in_dropdown_in(
            item: $item,
            dropdown: $dropdown,
            container: $containernode,
            not: $not,
        );
    }

    /**
     * Helper to check whether an item exists in a dropdown.
     *
     * @param string $item The text of the item to look for
     * @param string $dropdown The name of the dropdown
     * @param null|NodeElement $container The container to look within
     */
    public function should_exist_in_dropdown_in(
        string $item,
        string $dropdown,
        null|NodeElement $container,
        bool $not,
    ): void {
        $dropdownnode = $this->find(
            selector: 'named_partial',
            locator: ['dropdown', $dropdown],
            node: $container ?? false,
        );

        if ($not) {
            try {
                $this->find(
                    selector: 'named_partial',
                    locator: ['dropdown_item', $item],
                    node: $dropdownnode,
                );

                throw new ExpectationException(
                    "The '{$item}' dropdown item was found in the '{$dropdown}' selector",
                    $this->getSession(),
                );
            } catch (ElementNotFoundException $e) {
                return;
            }
        } else {
            $this->find(
                selector: 'named_partial',
                locator: ['dropdown_item', $item],
                node: $dropdownnode,
            );
        }
    }

    /**
     * Close the block drawer if it is open.
     *
     * This is necessary as in Behat the block drawer is open at each page load (disregarding user's settings)
     * As the block drawer is positioned at the front of some contextual dialogs on the grade report for example.
     * @Given I close block drawer if open
     * @return void
     */
    public function i_close_block_drawer_if_open() {
        if ($this->running_javascript()) {
            $xpath = "//button[contains(@data-action,'closedrawer')][contains(@data-placement,'left')]";
            $node = $this->getSession()->getPage()->find('xpath', $xpath);
            if ($node && $node->isVisible()) {
                $ishidden = $node->getAttribute('aria-hidden-tab-index');
                if (!$ishidden) {
                    $this->execute('behat_general::i_click_on', [$node, 'NodeElement']);
                }
            }
        }
    }

    /**
     * I close the block drawer and keep it closed.
     *
     * @Given I keep block drawer closed
     * @return void
     */
    public function i_keep_block_drawer_closed() {
        set_user_preference('behat_keep_drawer_closed', 1);
        $this->i_close_block_drawer_if_open();
    }

    /**
     * Checks if a navigation menu item is active.
     *
     * @Then menu item :navigationmenuitem should be active
     * @param string $navigationmenuitem The navigation menu item name.
     */
    public function menu_item_should_be_active(string $navigationmenuitem): void {
        $elementselector = "//*//a/following-sibling::*//a[contains(text(), '$navigationmenuitem') and @aria-current='true']";
        $params = [$elementselector, "xpath_element"];
        $this->execute("behat_general::should_exist", $params);
    }

    /**
     * Checks if a navigation menu item is not active
     *
     * @Then menu item :navigationmenuitem should not be active
     * @param string $navigationmenuitem The navigation menu item name.
     */
    public function menu_item_should_not_be_active(string $navigationmenuitem): void {
        $elementselector = "//*//a/following-sibling::*//a[contains(text(), '$navigationmenuitem') and @aria-current='true']";
        $params = [$elementselector, "xpath_element"];
        $this->execute("behat_general::should_not_exist", $params);
    }

    /**
     * Sets a link to no longer navigate when selected.
     *
     * @When /^I update the href of the "(?P<locator_string>[^"]*)" "(?P<selector_string>[^"]*)" link to "(?P<href_string>[^"]*)"$/
     * @param string $locator The locator to use
     * @param string $selector selector type
     * @param string $href The value
     */
    public function i_update_the_link_to_go_nowhere(
        string $locator,
        string $selector,
        string $href,
    ): void {
        $this->require_javascript();
        $xpath = $this->find(
            selector: $selector,
            locator: $locator,
        )->getXpath();
        $script = <<<JS
            var result = document.evaluate("{$xpath}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null);
            var link = result.singleNodeValue;

            if (link) {
                link.setAttribute('href', '{$href}');
            } else {
                throw new Error('No element found with the XPath: ' + "$selector");
            }
        JS;

        $this->getSession()->executeScript($script);
    }
}
