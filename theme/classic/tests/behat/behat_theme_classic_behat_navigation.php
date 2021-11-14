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
 * Navigation step definition overrides for the Classic theme.
 *
 * @package    theme_classic
 * @category   test
 * @copyright  2019 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: No MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/tests/behat/behat_navigation.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException;
use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Step definitions and overrides to navigate through the navigation tree nodes in the Classic theme.
 *
 * @package    theme_classic
 * @category   test
 * @copyright  2019 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_classic_behat_navigation extends behat_navigation {
    /**
     * Navigate to an item in a current page administration menu.
     *
     * @throws ExpectationException
     * @param string $nodetext The navigation node/path to follow, eg "Course administration > Edit settings"
     * @return void
     */
    public function i_navigate_to_in_current_page_administration($nodetext) {
        $parentnodes = array_map('trim', explode('>', $nodetext));

        // Find the name of the first category of the administration block tree.
        $xpath = "//section[contains(@class,'block_settings')]//div[@id='settingsnav']/ul[1]/li[1]/p[1]/span";
        $node = $this->find('xpath', $xpath);

        array_unshift($parentnodes, $node->getText());
        $lastnode = array_pop($parentnodes);
        try {
            $this->select_node_in_navigation($lastnode, $parentnodes);
        } catch (Exception $e) {
            try {
                $this->execute("behat_general::click_link", $lastnode);
            } catch (Exception $e) {
                // We must be in a weird state i.e. Add competencies to course.
                $this->execute("behat_general::click_link", array_pop($parentnodes));
                $this->execute('behat_forms::press_button', $lastnode);
            }
        }
    }

    /**
     * Navigate to an item within the site administration menu.
     *
     * @throws ExpectationException
     * @param string $nodetext The navigation node/path to follow, excluding "Site administration" itself, eg "Grades > Scales"
     * @return void
     */
    public function i_navigate_to_in_site_administration($nodetext) {
        $parentnodes = array_map('trim', explode('>', $nodetext));
        array_unshift($parentnodes, get_string('administrationsite'));
        $lastnode = array_pop($parentnodes);
        $this->select_node_in_navigation($lastnode, $parentnodes);
    }

    /**
     * Helper function to get top navigation node in the tree.
     *
     * @throws ExpectationException if node not found.
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
     * Check that current page administration contains an element.
     *
     * @throws ElementNotFoundException
     * @param string $element The locator of the specified selector.
     *     This may be a path, for example "Subscription mode > Forced subscription"
     * @param string $selectortype The selector type (link or text)
     * @return void
     */
    public function should_exist_in_current_page_administration($element, $selectortype) {
        $nodes = array_map('trim', explode('>', $element));
        $nodetext = end($nodes);

        // Find administration menu.
        $rootxpath = $this->find_header_administration_menu() ?: $this->find_page_administration_menu(true);
        $menuxpath = $rootxpath . '/p/../ul[1]';

        for ($i = 0; $i < (count($nodes) - 1); $i++) {
            $menuxpath .= "/li/p/span[contains(text(), '{$nodes[$i]}')]/../../ul[1]";
        }

        if ($selectortype == 'link') {
            $menuxpath .= "/li/p[a[contains(text(), '{$nodetext}')]";
            $menuxpath .= "|a/span[contains(text(), '{$nodetext}')]]";
        } else {
            $menuxpath .= "/li/p/span[contains(text(), '{$nodes[$i]}')]";
        }

        $exception = new ElementNotFoundException($this->getSession(), "\"{$element}\" \"{$selectortype}\"");
        try {
            $this->find('xpath', $menuxpath, $exception);
        } catch (Exception $e) {
            // For question bank a different approach.
            $menuxpath = $rootxpath . "//div[contains(@class, 'dropdown-menu')]";
            if ($selectortype === 'link') {
                $menuxpath .= "//a[contains(text(), 'Categories')]";
            }
            $this->find('xpath', $menuxpath, $e);
        }
    }

    /**
     * Check that current page administration does not contains an element.
     *
     * @throws ExpectationException
     * @param string $element The locator of the specified selector.
     *     This may be a path, for example "Subscription mode > Forced subscription"
     * @param string $selectortype The selector type (link or text)
     * @return void
     */
    public function should_not_exist_in_current_page_administration($element, $selectortype) {
        try {
            $menuxpath = $this->find_header_administration_menu() ?: $this->find_page_administration_menu(true);
        } catch (Exception $e) {
            // If an exception was thrown, it means the root note does not exist, so we can conclude the test is a success.
            return;
        }

        // Test if the element exists.
        try {
            $this->should_exist_in_current_page_administration($element, $selectortype);
        } catch (ElementNotFoundException $e) {

            // If an exception was thrown, it means the element does not exist, so the test is successful.
            return;
        }

        // If the try block passed, the element exists, so throw an exception.
        $exception = 'The "' . $element . '" "' . $selectortype . '" was found, but should not exist';
        throw new ExpectationException($exception, $this->getSession());
    }

    /**
     * Check that the page administration menu exists on the page.
     *
     * This confirms the existence of the menu, which authorised users should have access to.
     * @Given /^I should see the page administration menu$/
     *
     * @throws ExpectationException
     * @return void
     */
    public function page_administration_exists() {
        $menuxpath = "//section[contains(@class,'block_settings')]//div[@id='settingsnav']";
        $this->ensure_element_exists($menuxpath, 'xpath_element');
    }

    /**
     * Check that the page administration menu does not exist on the page.
     *
     * This confirms the absence of the menu, which unauthorised users should not have access to.
     * @Given /^I should not see the page administration menu$/
     *
     * @throws ExpectationException
     * @return void
     */
    public function page_administration_does_not_exist() {
        $menuxpath = "//section[contains(@class,'block_settings')]//div[@id='settingsnav']";
        $this->ensure_element_does_not_exist($menuxpath, 'xpath_element');
    }

    /**
     * Locate the administration menu on the page (but not in the header) and return its xpath.
     *
     * @throws ElementNotFoundException
     * @param bool $mustexist If true, throws an exception if menu is not found
     * @return null|string
     */
    protected function find_page_administration_menu($mustexist = false) {
        $menuxpath = "//section[contains(@class,'block_settings')]//div[@id='settingsnav']/ul[1]/li[1]";

        if ($mustexist) {
            $exception = new ElementNotFoundException($this->getSession(), 'Page administration menu');
            $this->find('xpath', $menuxpath, $exception);

        } else if (!$this->getSession()->getPage()->find('xpath', $menuxpath)) {
            return null;
        }

        return $menuxpath;
    }

    /**
     * Turns editing mode off.
     */
    public function i_turn_editing_mode_off(): void {
        $buttonnames = [get_string('turneditingoff'), get_string('updatemymoodleoff'), get_string('blockseditoff')];
        foreach ($buttonnames as $buttonname) {
            if ($editbutton = $this->getSession()->getPage()->findButton($buttonname)) {
                $this->execute('behat_general::i_click_on', [$editbutton, 'NodeElement']);
                return;
            }
        }
        // Click the turneditingoff link in the Site Administration block.
        if ($this->is_editing_on()) {
            $this->execute('behat_general::i_click_on', [get_string('turneditingoff'), "link"]);
        }
    }

    /**
     * Turns editing mode on.
     */
    public function i_turn_editing_mode_on(): void {
        $buttonnames = [get_string('turneditingon'), get_string('updatemymoodleon'), get_string('blocksediton')];
        foreach ($buttonnames as $buttonname) {
            if ($editbutton = $this->getSession()->getPage()->findButton($buttonname)) {
                $this->execute('behat_general::i_click_on', [$editbutton, 'NodeElement']);
                return;
            }
        }

        if (!$this->is_editing_on()) {
            $this->execute('behat_general::i_click_on', [get_string('turneditingon'), "link"]);
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
            $menubarmorexpath = '//ul[@role=\'menubar\']/li/a[contains(normalize-space(.), ' . $linkname . ')]';
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
                    return;
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
        $xpath .= '//a[contains(normalize-space(.), ' . $linkname . ')]';
        if (!$node = $this->getSession()->getPage()->find('xpath', $xpath)) {
            throw new ElementNotFoundException($this->getSession(), 'Link "' . join(' > ', $nodelist) . '"');
        }
        $this->execute('behat_general::i_click_on', [$node, 'NodeElement']);
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
            $exception = new ElementNotFoundException($this->getSession(), 'Page header administration menu');
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
            $this->execute('behat_general::i_click_on', [$node, 'NodeElement']);
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

        $this->execute('behat_navigation::toggle_page_administration_menu', [$menuxpath]);

        if (!$isheader || count($nodelist) == 1) {
            $lastnode = end($nodelist);
            $linkname = behat_context_helper::escape($lastnode);
            $link = $this->getSession()->getPage()->find('xpath', $menuxpath . '//a[contains(normalize-space(.), ' .
                $linkname . ')]'
            );
            if ($link) {
                $this->execute('behat_general::i_click_on', [$link, 'NodeElement']);
                return;
            }
        }

        if ($isheader) {
            // Course administration and Front page administration will have subnodes under "More...".
            $linkname = behat_context_helper::escape(get_string('morenavigationlinks'));
            $link = $this->getSession()->getPage()->find('xpath', $menuxpath . '//a[contains(normalize-space(.), ' .
                $linkname . ')]'
            );
            if ($link) {
                $this->execute('behat_general::i_click_on', [$link, 'NodeElement']);
                $this->select_on_administration_page($nodelist);
                return;
            }
        }

        throw new ElementNotFoundException($this->getSession(),
            'Link "' . join(' > ', $nodelist) . '" in the current page edit menu"');
    }
}
