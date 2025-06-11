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
 * @author    Guy Thomas
 * @copyright Copyright (c) 2017 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException,
    Behat\Mink\Element\NodeElement as NodeElement;

require_once(__DIR__ . '/../../../../lib/tests/behat/behat_general.php');

/**
 * Overrides to fix intermittent failures.
 *
 * @author    Guy Thomas
 * @copyright Copyright (c) 2017 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_snap_behat_general extends behat_general {

    /**
     * Get nodes containing text in the page or, if specified, within a container.
     * @param string $text
     * @param bool|NodeElement $container
     * @return array|bool
     */
    protected function get_nodes_containing_text($text, $container = false) {
        // Looking for all the matching nodes without any other descendant matching the
        // same xpath (we are using contains(., ....).
        $xpathliteral = behat_context_helper::escape($text);
        $xpath = "/descendant-or-self::*[contains(., $xpathliteral)]" .
            "[count(descendant::*[contains(., $xpathliteral)]) = 0]";

        // We should wait a while to ensure that the page is not still loading elements.
        // Giving preference to the reliability of the results rather than to the performance.
        try {
            if ($container) {
                $nodes = $this->find_all('xpath', $xpath, false, $container, behat_base::get_reduced_timeout());
            } else {
                $nodes = $this->find_all('xpath', $xpath);
            }
        } catch (ElementNotFoundException $e) {
            $nodes = false;
        }

        return $nodes;
    }

    /**
     * Check that text is visible in nodes, throw error if not.
     * @param NodeElement[] $nodes
     * @param string $text
     * @param bool|string $element
     */
    protected function check_text_visible_in_nodes($nodes, $text, $element = false) {
        $args = [
            'nodes' => $nodes, 'text' => $text, 'element' => $element,
        ];

        $this->spin(
            function($context, $args) {
                foreach ($args['nodes'] as $node) {
                    if ($node->isVisible()) {
                        return true;
                    }
                }
                if ($args['element']) {
                    $msg = '"' . $args['text'] . '" text was found in the "' . $args['element'];
                    $msg .= '" element but was not visible';
                    throw new ExpectationException($msg, $context->getSession());
                } else {
                    $msg = '"' . $args['text'] . '" text was found but was not visible';
                    throw new ExpectationException($msg, $context->getSession());
                }
            },
            $args,
            false,
            false,
            true,
        );
    }

    /**
     * Check text is not visible in nodes and throw error if so.
     * @param NodeElement[] $nodes
     * @param string $text
     * @param bool|string $element
     * @param bool|string $selectortype
     */
    protected function check_text_not_visible_in_nodes($nodes, $text, $element = false, $selectortype = false) {
        // We need to ensure all the found nodes are hidden.
        $this->spin(
            function($context, $args) {

                foreach ($args['nodes'] as $node) {
                    if ($node->isVisible()) {
                        if ($args['element']) {
                            throw new ExpectationException(
                                '"' . $args['text'] . '" text was found in the "' . $args['element'] . '" element',
                                $context->getSession()
                            );
                        } else {
                            throw new ExpectationException(
                                '"' . $args['text'] . '" text was found in the page',
                                $context->getSession()
                            );
                        }
                    }
                }

                // If all the found nodes are hidden we are happy.
                return true;
            },
            array('nodes' => $nodes, 'text' => $text, 'element' => $element, 'selectortype' => $selectortype),
            behat_base::get_reduced_timeout(),
            false,
            true
        );
    }

    public function assert_page_contains_text($text) {
        $nodes = $this->get_nodes_containing_text($text);
        if (empty($nodes)) {
            $this->wait_for_pending_js();
            sleep(2);
            $nodes = $this->get_nodes_containing_text($text);
            if (empty($nodes)) {
                throw new ExpectationException('"' . $text . '" text was not found in the page', $this->getSession());
            }
        }

        // If we are not running javascript we have enough with the
        // element existing as we can't check if it is visible.
        if (!$this->running_javascript()) {
            return;
        }

        // We also check the element visibility when running JS tests.
        try {
            $this->check_text_visible_in_nodes($nodes, $text);
        } catch (ExpectationException $e) {
            // Rather than just fail we will make a second attempt after a brief pause.
            // This is to cope with intermittent failures.
            // It's less expensive to try again then have the entire test suite run again to deal with an intermittent
            // fault!
            $this->wait_for_pending_js();
            sleep(2);
            $nodes = $this->get_nodes_containing_text($text);
            if (empty($nodes)) {
                throw new ExpectationException('"' . $text . '" text was not found in the page [Snap behat]', $this->getSession());
            }
            $this->check_text_visible_in_nodes($nodes, $text);
        }
    }

    public function assert_page_not_contains_text($text) {
        $nodes = $this->get_nodes_containing_text($text);
        if (empty($nodes) || $this->recheck_for_nodes_not_containing_text(10, $text)) {
            // No nodes found so no text found!
            return;
        }

        // If we are not running javascript we have enough with the
        // element being found as we can't check if it is visible.
        if (!$this->running_javascript()) {
            throw new ExpectationException('"' . $text . '" text was found in the page', $this->getSession());
        }

        try {
            $this->check_text_not_visible_in_nodes($nodes, $text);
        } catch (ExpectationException $e) {
            // Rather than just fail we will make a second attempt after a brief pause.
            // This is to cope with intermittent failures.
            // It's less expensive to try again then have the entire test suite run again to deal with an intermittent
            // fault!
            $this->wait_for_pending_js();
            sleep(2);
            $nodes = $this->get_nodes_containing_text($text);
            if (empty($nodes)) {
                // No nodes found so no text found!
                return;
            }
            $this->check_text_not_visible_in_nodes($nodes, $text);
        }
    }

    public function assert_element_contains_text($text, $element, $selectortype) {

        // Getting the container where the text should be found.
        $container = $this->get_selected_node($selectortype, $element);

        $nodes = $this->get_nodes_containing_text($text, $container);
        if (empty($nodes)) {
            $this->wait_for_pending_js();
            sleep(2);
            $nodes = $this->get_nodes_containing_text($text, $container);
            if (empty($nodes)) {
                $msg = '"' . $text . '" text was not found in the "' . $element . '" element';
                throw new ExpectationException($msg, $this->getSession());
            }
        }

        // If we are not running javascript we have enough with the
        // element existing as we can't check if it is visible.
        if (!$this->running_javascript()) {
            return;
        }

        // We also check the element visibility when running JS tests.
        try {
            $this->check_text_visible_in_nodes($nodes, $text, $element);
        } catch (ExpectationException $e) {
            // Rather than just fail we will make a second attempt after a brief pause.
            // This is to cope with intermittent failures.
            // It's less expensive to try again then have the entire test suite run again to deal with an intermittent
            // fault!
            $this->wait_for_pending_js();
            sleep(2);
            $nodes = $this->get_nodes_containing_text($text, $container);
            if (empty($nodes)) {
                $msg = '"' . $text . '" text was not found in the "' . $element . '" element';
                throw new ExpectationException($msg, $this->getSession());
            }
            $this->check_text_visible_in_nodes($nodes, $text, $element);
        }
    }

    public function assert_element_not_contains_text($text, $element, $selectortype) {

        // Getting the container where the text should be found.
        $container = $this->get_selected_node($selectortype, $element);
        $nodes = $this->get_nodes_containing_text($text, $container);
        if (empty($nodes) || $this->recheck_for_nodes_not_containing_text(10, $text, $container)) {
            // No nodes found so no text found!
            return;
        }

        // If we are not running javascript we have enough with the
        // element being found as we can't check if it is visible.
        if (!$this->running_javascript()) {
            throw new ExpectationException('"' . $text . '" text was found in the "' . $element . '" element', $this->getSession());
        }

        try {
            $this->check_text_not_visible_in_nodes($nodes, $text, $element, $selectortype);
        } catch (ExpectationException $e) {
            // Rather than just fail we will make a second attempt after a brief pause.
            // This is to cope with intermittent failures.
            // It's less expensive to try again then have the entire test suite run again to deal with an intermittent
            // fault!
            $this->wait_for_pending_js();
            sleep(2);
            $nodes = $this->get_nodes_containing_text($text, $container);
            if (empty($nodes)) {
                // No nodes found so no text found!
                return;
            }
            $this->check_text_not_visible_in_nodes($nodes, $text, $element, $selectortype);
        }
    }

    public function recheck_for_nodes_not_containing_text($times, $text, $container = false) {
        $this->wait_for_pending_js();
        sleep(2);
        $nodes = $this->get_nodes_containing_text($text, $container);
        if (!empty($nodes)) {
            if ($times > 0) {
                return $this->recheck_for_nodes_not_containing_text($times - 1, $text, $container);
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * Checks, that the first specified element appears after the second one.
     * Copied from BehatGeneral in mahara/testing/frameworks/behat/classes/BehatGeneral.php
     *
     * @codingStandardsIgnoreStart
     * @Then /^"(?P<following_element_string>(?:[^"]|\\")*)" "(?P<selector1_string>(?:[^"]|\\")*)" should appear after the "(?P<preceding_element_string>(?:[^"]|\\")*)" "(?P<selector2_string>(?:[^"]|\\")*)"$/
     * @codingStandardsIgnoreEnd
     * @throws ExpectationException
     * @param string $postelement The locator of the latest element
     * @param string $postselectortype The selector type of the latest element
     * @param string $preelement The locator of the preceding element
     * @param string $preselectortype The selector type of the preceding element
     */
    public function theme_behat_should_appear_after(
        string $postelement,
        string $postselectortype,
        string $preelement,
        string $preselectortype
    ) {
        // We allow postselectortype as a non-text based selector.
        list($postselector, $postlocator) = $this->transform_selector($postselectortype, $postelement);
        list($preselector, $prelocator) = $this->transform_selector($preselectortype, $preelement);

        $postxpath = $this->find($postselector, $postlocator)->getXpath();
        $prexpath = $this->find($preselector, $prelocator)->getXpath();

        // Using preceding xpath axe to find it.
        $msg = '"'.$postelement.'" "'.$postselectortype.'" does not appear after "'.$preelement.'" "'.$preselectortype.'"';
        $xpath = $postxpath.'/preceding::*[contains(., '.$prexpath.')]';
        if (!$this->getSession()->getDriver()->find($xpath)) {
            throw new ExpectationException($msg, $this->getSession());
        }
    }

    /**
     * Checks, that the first specified element appears before the second one.
     * Copied from BehatGeneral in mahara/testing/frameworks/behat/classes/BehatGeneral.php
     *
     * @codingStandardsIgnoreStart
     * @Given /^"(?P<preceding_element_string>(?:[^"]|\\")*)" "(?P<selector1_string>(?:[^"]|\\")*)" should appear before the "(?P<following_element_string>(?:[^"]|\\")*)" "(?P<selector2_string>(?:[^"]|\\")*)"$/
     * @codingStandardsIgnoreEnd
     * @throws ExpectationException
     * @param string $preelement The locator of the preceding element
     * @param string $preselectortype The locator of the preceding element
     * @param string $postelement The locator of the latest element
     * @param string $postselectortype The selector type of the latest element
     *
     */
    public function theme_snap_should_appear_before($preelement, $preselectortype, $postelement, $postselectortype) {

        // We allow postselectortype as a non-text based selector.
        list($preselector, $prelocator) = $this->transform_selector($preselectortype, $preelement);
        list($postselector, $postlocator) = $this->transform_selector($postselectortype, $postelement);

        $prexpath = $this->find($preselector, $prelocator)->getXpath();
        $postxpath = $this->find($postselector, $postlocator)->getXpath();

        // Using following xpath axe to find it.
        $msg = '"'.$preelement.'" "'.$preselectortype.'" does not appear before "'.$postelement.'" "'.$postselectortype.'"';
        $xpath = $prexpath.'/following::*[contains(., '.$postxpath.')]';
        if (!$this->getSession()->getDriver()->find($xpath)) {
            throw new ExpectationException($msg, $this->getSession());
        }
    }

    /**
     * Assigns a role to a user in the frontpage
     *
     * @codingStandardsIgnoreLine
     * @Given /^I assign "([^"]*)" the role of "([^"]*)" in the frontpage$/
     * @throws ExpectationException
     * @param string $user
     * @param string $role
     */
    public function theme_snap_i_assign_user_the_role_of_role_in_the_frontpage($user, $role) {
        global $SITE, $DB;

        $context = context_course::instance($SITE->id);
        $roles = get_assignable_roles($context, ROLENAME_SHORT, false);
        $roleid = array_search($role, $roles);

        $user = $DB->get_record('user', array('username' => $user), '*', MUST_EXIST);

        role_assign($roleid, $user->id, $context->id);
    }
}
