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
use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

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
            $node->click();
            $this->ensure_node_attribute_is_set($node, 'aria-expanded', 'true');
            $this->wait_for_pending_js();
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
            $node->click();
            $this->wait_for_pending_js();
        }
    }

    /**
     * Clicks link with specified id|title|alt|text in the flat navigation drawer.
     *
     * @When /^I select "(?P<link_string>(?:[^"]|\\")*)" from flat navigation drawer$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $link
     */
    public function i_select_from_flat_navigation_drawer($link) {
        $this->i_open_flat_navigation_drawer();
        $this->execute('behat_general::i_click_on_in_the', [$link, 'link', '#nav-drawer', 'css_element']);
    }

    /**
     * If we are not on the course main page, click on the course link in the navbar
     */
    protected function go_to_main_course_page() {
        $url = $this->getSession()->getCurrentUrl();
        if (!preg_match('|/course/view.php\?id=[\d]+$|', $url)) {
            $this->find('xpath', '//header//div[@id=\'page-navbar\']//a[contains(@href,\'/course/view.php?id=\')]')->click();
            $this->execute('behat_general::wait_until_the_page_is_ready');
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
            if ($node = $this->getSession()->getPage()->find('xpath', $tabxpath)) {
                if ($this->running_javascript()) {
                    // Click on the tab and add 'active' tab to the xpath.
                    $node->click();
                    $xpath .= '//div[contains(@class,\'active\')]';
                } else {
                    // Add the tab content selector to the xpath.
                    $tabid = behat_context_helper::escape(ltrim($node->getAttribute('href'), '#'));
                    $xpath .= '//div[@id = ' . $tabid . ']';
                }
                array_shift($parentnodes);
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
        $xpath .= '//a[contains(normalize-space(.), ' . $linkname . ')]';
        if (!$node = $this->getSession()->getPage()->find('xpath', $xpath)) {
            throw new ElementNotFoundException($this->getSession(), 'Link "' . join(' > ', $nodelist) . '"" not found on the page');
        }
        $node->click();
        $this->wait_for_pending_js();
    }

    /**
     * Locates the administration menu in the <header> element and returns its xpath
     *
     * @param bool $mustexist if specified throws an exception if menu is not found
     * @return null|string
     */
    protected function find_header_administration_menu($mustexist = false) {
        $menuxpath = '//header[@id=\'page-header\']//div[contains(@class,\'moodle-actionmenu\')]';
        if ($mustexist) {
            $exception = new ElementNotFoundException($this->getSession(), 'Page header administration menu is not found');
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
            $exception = new ElementNotFoundException($this->getSession(), 'Page administration menu is not found');
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
            $this->find('xpath', $menuxpath . '//a[@data-toggle=\'dropdown\']')->click();
            $this->wait_for_pending_js();
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
        } else {
            $menuxpath = $this->find_page_administration_menu(true);
            $isheader = false;
        }

        $this->toggle_page_administration_menu($menuxpath);

        if (!$isheader || count($nodelist) == 1) {
            $lastnode = end($nodelist);
            $linkname = behat_context_helper::escape($lastnode);
            $link = $this->getSession()->getPage()->find('xpath', $menuxpath . '//a[contains(normalize-space(.), ' . $linkname . ')]');
            if ($link) {
                $link->click();
                $this->wait_for_pending_js();
                return;
            }
        }

        if ($isheader) {
            // Course administration and Front page administration will have subnodes under "More...".
            $linkname = behat_context_helper::escape(get_string('morenavigationlinks'));
            $link = $this->getSession()->getPage()->find('xpath', $menuxpath . '//a[contains(normalize-space(.), ' . $linkname . ')]');
            if ($link) {
                $link->click();
                $this->execute('behat_general::wait_until_the_page_is_ready');
                $this->select_on_administration_page($nodelist);
                return;
            }
        }

        throw new ElementNotFoundException($this->getSession(),
            'Link "' . join(' > ', $nodelist) . '" not found in the current page edit menu"');
    }

    public function should_exist_in_current_page_administration($element, $selectortype) {
        $nodes = array_map('trim', explode('>', $element));
        $nodetext = end($nodes);

        // Find administration menu.
        $menuxpath = $this->find_header_administration_menu() ?: $this->find_page_administration_menu(true);

        $this->toggle_page_administration_menu($menuxpath);
        $this->execute('behat_general::should_exist_in_the', [$nodetext, $selectortype, $menuxpath, 'xpath_element']);
        $this->toggle_page_administration_menu($menuxpath);
    }

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

    public function i_navigate_to_node_in($nodetext, $parentnodes) {
        $parentnodes = array_map('trim', explode('>', $parentnodes));
        $nodelist = array_merge($parentnodes, [$nodetext]);
        $firstnode = array_shift($nodelist);

        if ($firstnode === get_string('administrationsite')) {
            $this->i_select_from_flat_navigation_drawer(get_string('administrationsite'));
            $this->select_on_administration_page($nodelist);
            return;
        }

        if ($firstnode === get_string('sitepages')) {
            if ($nodetext === get_string('calendar', 'calendar')) {
                $this->i_select_from_flat_navigation_drawer($nodetext);
            } else {
                // TODO MDL-57120 other links under "Site pages" are not accessible without navigation block.
                $this->select_node_in_navigation($nodetext, $parentnodes);
            }
            return;
        }

        if ($firstnode === get_string('courseadministration')) {
            // Administration menu is available only on the main course page where settings in Administration
            // block (original purpose of the step) are available on every course page.
            $this->go_to_main_course_page();
        }

        $this->select_from_administration_menu($nodelist);
    }

    public function i_navigate_to_in_current_page_administration($nodetext) {
        $nodelist = array_map('trim', explode('>', $nodetext));
        $this->select_from_administration_menu($nodelist);
    }

    public function i_navigate_to_in_site_administration($nodetext) {
        $nodelist = array_map('trim', explode('>', $nodetext));
        $this->i_select_from_flat_navigation_drawer(get_string('administrationsite'));
        $this->select_on_administration_page($nodelist);
    }
}
