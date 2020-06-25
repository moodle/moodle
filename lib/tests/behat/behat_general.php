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
 * General use steps definitions.
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../behat/behat_base.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException,
    Behat\Mink\Exception\DriverException as DriverException,
    WebDriver\Exception\NoSuchElement as NoSuchElement,
    WebDriver\Exception\StaleElementReference as StaleElementReference,
    Behat\Gherkin\Node\TableNode as TableNode;

/**
 * Cross component steps definitions.
 *
 * Basic web application definitions from MinkExtension and
 * BehatchExtension. Definitions modified according to our needs
 * when necessary and including only the ones we need to avoid
 * overlapping and confusion.
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_general extends behat_base {

    /**
     * @var string used by {@link switch_to_window()} and
     * {@link switch_to_the_main_window()} to work-around a Chrome browser issue.
     */
    const MAIN_WINDOW_NAME = '__moodle_behat_main_window_name';

    /**
     * @var string when we want to check whether or not a new page has loaded,
     * we first write this unique string into the page. Then later, by checking
     * whether it is still there, we can tell if a new page has been loaded.
     */
    const PAGE_LOAD_DETECTION_STRING = 'new_page_not_loaded_since_behat_started_watching';

    /**
     * @var $pageloaddetectionrunning boolean Used to ensure that page load detection was started before a page reload
     * was checked for.
     */
    private $pageloaddetectionrunning = false;

    /**
     * Opens Moodle homepage.
     *
     * @Given /^I am on homepage$/
     */
    public function i_am_on_homepage() {
        $this->getSession()->visit($this->locate_path('/'));
    }

    /**
     * Opens Moodle site homepage.
     *
     * @Given /^I am on site homepage$/
     */
    public function i_am_on_site_homepage() {
        $this->getSession()->visit($this->locate_path('/?redirect=0'));
    }

    /**
     * Opens course index page.
     *
     * @Given /^I am on course index$/
     */
    public function i_am_on_course_index() {
        $this->getSession()->visit($this->locate_path('/course/index.php'));
    }

    /**
     * Reloads the current page.
     *
     * @Given /^I reload the page$/
     */
    public function reload() {
        $this->getSession()->reload();
    }

    /**
     * Follows the page redirection. Use this step after any action that shows a message and waits for a redirection
     *
     * @Given /^I wait to be redirected$/
     */
    public function i_wait_to_be_redirected() {

        // Xpath and processes based on core_renderer::redirect_message(), core_renderer::$metarefreshtag and
        // moodle_page::$periodicrefreshdelay possible values.
        if (!$metarefresh = $this->getSession()->getPage()->find('xpath', "//head/descendant::meta[@http-equiv='refresh']")) {
            // We don't fail the scenario if no redirection with message is found to avoid race condition false failures.
            return true;
        }

        // Wrapped in try & catch in case the redirection has already been executed.
        try {
            $content = $metarefresh->getAttribute('content');
        } catch (NoSuchElement $e) {
            return true;
        } catch (StaleElementReference $e) {
            return true;
        }

        // Getting the refresh time and the url if present.
        if (strstr($content, 'url') != false) {

            list($waittime, $url) = explode(';', $content);

            // Cleaning the URL value.
            $url = trim(substr($url, strpos($url, 'http')));

        } else {
            // Just wait then.
            $waittime = $content;
        }


        // Wait until the URL change is executed.
        if ($this->running_javascript()) {
            $this->getSession()->wait($waittime * 1000);

        } else if (!empty($url)) {
            // We redirect directly as we can not wait for an automatic redirection.
            $this->getSession()->getDriver()->getClient()->request('get', $url);

        } else {
            // Reload the page if no URL was provided.
            $this->getSession()->getDriver()->reload();
        }
    }

    /**
     * Switches to the specified iframe.
     *
     * @Given /^I switch to "(?P<iframe_name_string>(?:[^"]|\\")*)" iframe$/
     * @param string $iframename
     */
    public function switch_to_iframe($iframename) {

        // We spin to give time to the iframe to be loaded.
        // Using extended timeout as we don't know about which
        // kind of iframe will be loaded.
        $this->spin(
            function($context, $iframename) {
                $context->getSession()->switchToIFrame($iframename);

                // If no exception we are done.
                return true;
            },
            $iframename,
            behat_base::get_extended_timeout()
        );
    }

    /**
     * Switches to the iframe containing specified class.
     *
     * @Given /^I switch to "(?P<iframe_name_string>(?:[^"]|\\")*)" class iframe$/
     * @param string $classname
     */
    public function switch_to_class_iframe($classname) {
        // We spin to give time to the iframe to be loaded.
        // Using extended timeout as we don't know about which
        // kind of iframe will be loaded.
        $this->spin(
            function($context, $classname) {
                $iframe = $this->find('iframe', $classname);
                if (!empty($iframe->getAttribute('id'))) {
                    $iframename = $iframe->getAttribute('id');
                } else {
                    $iframename = $iframe->getAttribute('name');
                }
                $context->getSession()->switchToIFrame($iframename);

                // If no exception we are done.
                return true;
            },
            $classname,
            behat_base::get_extended_timeout()
        );
    }

    /**
     * Switches to the main Moodle frame.
     *
     * @Given /^I switch to the main frame$/
     */
    public function switch_to_the_main_frame() {
        $this->getSession()->switchToIFrame();
    }

    /**
     * Switches to the specified window. Useful when interacting with popup windows.
     *
     * @Given /^I switch to "(?P<window_name_string>(?:[^"]|\\")*)" window$/
     * @param string $windowname
     */
    public function switch_to_window($windowname) {
        // In Behat, some browsers (e.g. Chrome) are unable to switch to a
        // window without a name, and by default the main browser window does
        // not have a name. To work-around this, when we switch away from an
        // unnamed window (presumably the main window) to some other named
        // window, then we first set the main window name to a conventional
        // value that we can later use this name to switch back.
        $this->execute_script('if (window.name == "") window.name = "' . self::MAIN_WINDOW_NAME . '"');

        $this->getSession()->switchToWindow($windowname);
    }

    /**
     * Switches to the main Moodle window. Useful when you finish interacting with popup windows.
     *
     * @Given /^I switch to the main window$/
     */
    public function switch_to_the_main_window() {
        $this->getSession()->switchToWindow(self::MAIN_WINDOW_NAME);
    }

    /**
     * Accepts the currently displayed alert dialog. This step does not work in all the browsers, consider it experimental.
     * @Given /^I accept the currently displayed dialog$/
     */
    public function accept_currently_displayed_alert_dialog() {
        $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
    }

    /**
     * Dismisses the currently displayed alert dialog. This step does not work in all the browsers, consider it experimental.
     * @Given /^I dismiss the currently displayed dialog$/
     */
    public function dismiss_currently_displayed_alert_dialog() {
        $this->getSession()->getDriver()->getWebDriverSession()->dismiss_alert();
    }

    /**
     * Clicks link with specified id|title|alt|text.
     *
     * @When /^I follow "(?P<link_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $link
     */
    public function click_link($link) {

        $linknode = $this->find_link($link);
        $this->ensure_node_is_visible($linknode);
        $linknode->click();
    }

    /**
     * Waits X seconds. Required after an action that requires data from an AJAX request.
     *
     * @Then /^I wait "(?P<seconds_number>\d+)" seconds$/
     * @param int $seconds
     */
    public function i_wait_seconds($seconds) {
        if ($this->running_javascript()) {
            $this->getSession()->wait($seconds * 1000);
        } else {
            sleep($seconds);
        }
    }

    /**
     * Waits until the page is completely loaded. This step is auto-executed after every step.
     *
     * @Given /^I wait until the page is ready$/
     */
    public function wait_until_the_page_is_ready() {

        // No need to wait if not running JS.
        if (!$this->running_javascript()) {
            return;
        }

        $this->getSession()->wait(self::get_timeout() * 1000, self::PAGE_READY_JS);
    }

    /**
     * Waits until the provided element selector exists in the DOM
     *
     * Using the protected method as this method will be usually
     * called by other methods which are not returning a set of
     * steps and performs the actions directly, so it would not
     * be executed if it returns another step.

     * @Given /^I wait until "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" exists$/
     * @param string $element
     * @param string $selector
     * @return void
     */
    public function wait_until_exists($element, $selectortype) {
        $this->ensure_element_exists($element, $selectortype);
    }

    /**
     * Waits until the provided element does not exist in the DOM
     *
     * Using the protected method as this method will be usually
     * called by other methods which are not returning a set of
     * steps and performs the actions directly, so it would not
     * be executed if it returns another step.

     * @Given /^I wait until "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" does not exist$/
     * @param string $element
     * @param string $selector
     * @return void
     */
    public function wait_until_does_not_exists($element, $selectortype) {
        $this->ensure_element_does_not_exist($element, $selectortype);
    }

    /**
     * Generic mouse over action. Mouse over a element of the specified type.
     *
     * @When /^I hover "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)"$/
     * @param string $element Element we look for
     * @param string $selectortype The type of what we look for
     */
    public function i_hover($element, $selectortype) {

        // Gets the node based on the requested selector type and locator.
        $node = $this->get_selected_node($selectortype, $element);
        $node->mouseOver();
    }

    /**
     * Generic click action. Click on the element of the specified type.
     *
     * @When /^I click on "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)"$/
     * @param string $element Element we look for
     * @param string $selectortype The type of what we look for
     */
    public function i_click_on($element, $selectortype) {

        // Gets the node based on the requested selector type and locator.
        $node = $this->get_selected_node($selectortype, $element);
        $this->ensure_node_is_visible($node);
        $node->click();
    }

    /**
     * Sets the focus and takes away the focus from an element, generating blur JS event.
     *
     * @When /^I take focus off "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)"$/
     * @param string $element Element we look for
     * @param string $selectortype The type of what we look for
     */
    public function i_take_focus_off_field($element, $selectortype) {
        if (!$this->running_javascript()) {
            throw new ExpectationException('Can\'t take focus off from "' . $element . '" in non-js mode', $this->getSession());
        }
        // Gets the node based on the requested selector type and locator.
        $node = $this->get_selected_node($selectortype, $element);
        $this->ensure_node_is_visible($node);

        // Ensure element is focused before taking it off.
        $node->focus();
        $node->blur();
    }

    /**
     * Clicks the specified element and confirms the expected dialogue.
     *
     * @When /^I click on "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" confirming the dialogue$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $element Element we look for
     * @param string $selectortype The type of what we look for
     */
    public function i_click_on_confirming_the_dialogue($element, $selectortype) {
        $this->i_click_on($element, $selectortype);
        $this->accept_currently_displayed_alert_dialog();
    }

    /**
     * Clicks the specified element and dismissing the expected dialogue.
     *
     * @When /^I click on "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" dismissing the dialogue$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $element Element we look for
     * @param string $selectortype The type of what we look for
     */
    public function i_click_on_dismissing_the_dialogue($element, $selectortype) {
        $this->i_click_on($element, $selectortype);
        $this->dismiss_currently_displayed_alert_dialog();
    }

    /**
     * Click on the element of the specified type which is located inside the second element.
     *
     * @When /^I click on "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" in the "(?P<element_container_string>(?:[^"]|\\")*)" "(?P<text_selector_string>[^"]*)"$/
     * @param string $element Element we look for
     * @param string $selectortype The type of what we look for
     * @param string $nodeelement Element we look in
     * @param string $nodeselectortype The type of selector where we look in
     */
    public function i_click_on_in_the($element, $selectortype, $nodeelement, $nodeselectortype) {

        $node = $this->get_node_in_container($selectortype, $element, $nodeselectortype, $nodeelement);
        $this->ensure_node_is_visible($node);
        $node->click();
    }

    /**
     * Drags and drops the specified element to the specified container. This step does not work in all the browsers, consider it experimental.
     *
     * The steps definitions calling this step as part of them should
     * manage the wait times by themselves as the times and when the
     * waits should be done depends on what is being dragged & dropper.
     *
     * @Given /^I drag "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector1_string>(?:[^"]|\\")*)" and I drop it in "(?P<container_element_string>(?:[^"]|\\")*)" "(?P<selector2_string>(?:[^"]|\\")*)"$/
     * @param string $element
     * @param string $selectortype
     * @param string $containerelement
     * @param string $containerselectortype
     */
    public function i_drag_and_i_drop_it_in($source, $sourcetype, $target, $targettype) {
        if (!$this->running_javascript()) {
            throw new DriverException('Drag and drop steps require javascript');
        }

        $source = $this->find($sourcetype, $source);
        $target = $this->find($targettype, $target);

        if (!$source->isVisible()) {
            throw new ExpectationException("'{$source}' '{$sourcetype}' is not visible", $this->getSession());
        }
        if (!$target->isVisible()) {
            throw new ExpectationException("'{$target}' '{$targettype}' is not visible", $this->getSession());
        }

        $this->getSession()->getDriver()->dragTo($source->getXpath(), $target->getXpath());
    }

    /**
     * Checks, that the specified element is visible. Only available in tests using Javascript.
     *
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>(?:[^"]|\\")*)" should be visible$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @throws DriverException
     * @param string $element
     * @param string $selectortype
     * @return void
     */
    public function should_be_visible($element, $selectortype) {

        if (!$this->running_javascript()) {
            throw new DriverException('Visible checks are disabled in scenarios without Javascript support');
        }

        $node = $this->get_selected_node($selectortype, $element);
        if (!$node->isVisible()) {
            throw new ExpectationException('"' . $element . '" "' . $selectortype . '" is not visible', $this->getSession());
        }
    }

    /**
     * Checks, that the existing element is not visible. Only available in tests using Javascript.
     *
     * As a "not" method, it's performance could not be good, but in this
     * case the performance is good because the element must exist,
     * otherwise there would be a ElementNotFoundException, also here we are
     * not spinning until the element is visible.
     *
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>(?:[^"]|\\")*)" should not be visible$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @param string $element
     * @param string $selectortype
     * @return void
     */
    public function should_not_be_visible($element, $selectortype) {

        try {
            $this->should_be_visible($element, $selectortype);
        } catch (ExpectationException $e) {
            // All as expected.
            return;
        }
        throw new ExpectationException('"' . $element . '" "' . $selectortype . '" is visible', $this->getSession());
    }

    /**
     * Checks, that the specified element is visible inside the specified container. Only available in tests using Javascript.
     *
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" in the "(?P<element_container_string>(?:[^"]|\\")*)" "(?P<text_selector_string>[^"]*)" should be visible$/
     * @throws ElementNotFoundException
     * @throws DriverException
     * @throws ExpectationException
     * @param string $element Element we look for
     * @param string $selectortype The type of what we look for
     * @param string $nodeelement Element we look in
     * @param string $nodeselectortype The type of selector where we look in
     */
    public function in_the_should_be_visible($element, $selectortype, $nodeelement, $nodeselectortype) {

        if (!$this->running_javascript()) {
            throw new DriverException('Visible checks are disabled in scenarios without Javascript support');
        }

        $node = $this->get_node_in_container($selectortype, $element, $nodeselectortype, $nodeelement);
        if (!$node->isVisible()) {
            throw new ExpectationException(
                '"' . $element . '" "' . $selectortype . '" in the "' . $nodeelement . '" "' . $nodeselectortype . '" is not visible',
                $this->getSession()
            );
        }
    }

    /**
     * Checks, that the existing element is not visible inside the existing container. Only available in tests using Javascript.
     *
     * As a "not" method, it's performance could not be good, but in this
     * case the performance is good because the element must exist,
     * otherwise there would be a ElementNotFoundException, also here we are
     * not spinning until the element is visible.
     *
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" in the "(?P<element_container_string>(?:[^"]|\\")*)" "(?P<text_selector_string>[^"]*)" should not be visible$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @param string $element Element we look for
     * @param string $selectortype The type of what we look for
     * @param string $nodeelement Element we look in
     * @param string $nodeselectortype The type of selector where we look in
     */
    public function in_the_should_not_be_visible($element, $selectortype, $nodeelement, $nodeselectortype) {

        try {
            $this->in_the_should_be_visible($element, $selectortype, $nodeelement, $nodeselectortype);
        } catch (ExpectationException $e) {
            // All as expected.
            return;
        }
        throw new ExpectationException(
            '"' . $element . '" "' . $selectortype . '" in the "' . $nodeelement . '" "' . $nodeselectortype . '" is visible',
            $this->getSession()
        );
    }

    /**
     * Checks, that page contains specified text. It also checks if the text is visible when running Javascript tests.
     *
     * @Then /^I should see "(?P<text_string>(?:[^"]|\\")*)"$/
     * @throws ExpectationException
     * @param string $text
     */
    public function assert_page_contains_text($text) {

        // Looking for all the matching nodes without any other descendant matching the
        // same xpath (we are using contains(., ....).
        $xpathliteral = behat_context_helper::escape($text);
        $xpath = "/descendant-or-self::*[contains(., $xpathliteral)]" .
            "[count(descendant::*[contains(., $xpathliteral)]) = 0]";

        try {
            $nodes = $this->find_all('xpath', $xpath);
        } catch (ElementNotFoundException $e) {
            throw new ExpectationException('"' . $text . '" text was not found in the page', $this->getSession());
        }

        // If we are not running javascript we have enough with the
        // element existing as we can't check if it is visible.
        if (!$this->running_javascript()) {
            return;
        }

        // We spin as we don't have enough checking that the element is there, we
        // should also ensure that the element is visible. Using microsleep as this
        // is a repeated step and global performance is important.
        $this->spin(
            function($context, $args) {

                foreach ($args['nodes'] as $node) {
                    if ($node->isVisible()) {
                        return true;
                    }
                }

                // If non of the nodes is visible we loop again.
                throw new ExpectationException('"' . $args['text'] . '" text was found but was not visible', $context->getSession());
            },
            array('nodes' => $nodes, 'text' => $text),
            false,
            false,
            true
        );

    }

    /**
     * Checks, that page doesn't contain specified text. When running Javascript tests it also considers that texts may be hidden.
     *
     * @Then /^I should not see "(?P<text_string>(?:[^"]|\\")*)"$/
     * @throws ExpectationException
     * @param string $text
     */
    public function assert_page_not_contains_text($text) {

        // Looking for all the matching nodes without any other descendant matching the
        // same xpath (we are using contains(., ....).
        $xpathliteral = behat_context_helper::escape($text);
        $xpath = "/descendant-or-self::*[contains(., $xpathliteral)]" .
            "[count(descendant::*[contains(., $xpathliteral)]) = 0]";

        // We should wait a while to ensure that the page is not still loading elements.
        // Waiting less than self::get_timeout() as we already waited for the DOM to be ready and
        // all JS to be executed.
        try {
            $nodes = $this->find_all('xpath', $xpath, false, false, self::get_reduced_timeout());
        } catch (ElementNotFoundException $e) {
            // All ok.
            return;
        }

        // If we are not running javascript we have enough with the
        // element existing as we can't check if it is hidden.
        if (!$this->running_javascript()) {
            throw new ExpectationException('"' . $text . '" text was found in the page', $this->getSession());
        }

        // If the element is there we should be sure that it is not visible.
        $this->spin(
            function($context, $args) {

                foreach ($args['nodes'] as $node) {
                    // If element is removed from dom, then just exit.
                    try {
                        // If element is visible then throw exception, so we keep spinning.
                        if ($node->isVisible()) {
                            throw new ExpectationException('"' . $args['text'] . '" text was found in the page',
                                $context->getSession());
                        }
                    } catch (WebDriver\Exception\NoSuchElement $e) {
                        // Do nothing just return, as element is no more on page.
                        return true;
                    } catch (ElementNotFoundException $e) {
                        // Do nothing just return, as element is no more on page.
                        return true;
                    }
                }

                // If non of the found nodes is visible we consider that the text is not visible.
                return true;
            },
            array('nodes' => $nodes, 'text' => $text),
            behat_base::get_reduced_timeout(),
            false,
            true
        );
    }

    /**
     * Checks, that the specified element contains the specified text. When running Javascript tests it also considers that texts may be hidden.
     *
     * @Then /^I should see "(?P<text_string>(?:[^"]|\\")*)" in the "(?P<element_string>(?:[^"]|\\")*)" "(?P<text_selector_string>[^"]*)"$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @param string $text
     * @param string $element Element we look in.
     * @param string $selectortype The type of element where we are looking in.
     */
    public function assert_element_contains_text($text, $element, $selectortype) {

        // Getting the container where the text should be found.
        $container = $this->get_selected_node($selectortype, $element);

        // Looking for all the matching nodes without any other descendant matching the
        // same xpath (we are using contains(., ....).
        $xpathliteral = behat_context_helper::escape($text);
        $xpath = "/descendant-or-self::*[contains(., $xpathliteral)]" .
            "[count(descendant::*[contains(., $xpathliteral)]) = 0]";

        // Wait until it finds the text inside the container, otherwise custom exception.
        try {
            $nodes = $this->find_all('xpath', $xpath, false, $container);
        } catch (ElementNotFoundException $e) {
            throw new ExpectationException('"' . $text . '" text was not found in the "' . $element . '" element', $this->getSession());
        }

        // If we are not running javascript we have enough with the
        // element existing as we can't check if it is visible.
        if (!$this->running_javascript()) {
            return;
        }

        // We also check the element visibility when running JS tests. Using microsleep as this
        // is a repeated step and global performance is important.
        $this->spin(
            function($context, $args) {

                foreach ($args['nodes'] as $node) {
                    if ($node->isVisible()) {
                        return true;
                    }
                }

                throw new ExpectationException('"' . $args['text'] . '" text was found in the "' . $args['element'] . '" element but was not visible', $context->getSession());
            },
            array('nodes' => $nodes, 'text' => $text, 'element' => $element),
            false,
            false,
            true
        );
    }

    /**
     * Checks, that the specified element does not contain the specified text. When running Javascript tests it also considers that texts may be hidden.
     *
     * @Then /^I should not see "(?P<text_string>(?:[^"]|\\")*)" in the "(?P<element_string>(?:[^"]|\\")*)" "(?P<text_selector_string>[^"]*)"$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @param string $text
     * @param string $element Element we look in.
     * @param string $selectortype The type of element where we are looking in.
     */
    public function assert_element_not_contains_text($text, $element, $selectortype) {

        // Getting the container where the text should be found.
        $container = $this->get_selected_node($selectortype, $element);

        // Looking for all the matching nodes without any other descendant matching the
        // same xpath (we are using contains(., ....).
        $xpathliteral = behat_context_helper::escape($text);
        $xpath = "/descendant-or-self::*[contains(., $xpathliteral)]" .
            "[count(descendant::*[contains(., $xpathliteral)]) = 0]";

        // We should wait a while to ensure that the page is not still loading elements.
        // Giving preference to the reliability of the results rather than to the performance.
        try {
            $nodes = $this->find_all('xpath', $xpath, false, $container, self::get_reduced_timeout());
        } catch (ElementNotFoundException $e) {
            // All ok.
            return;
        }

        // If we are not running javascript we have enough with the
        // element not being found as we can't check if it is visible.
        if (!$this->running_javascript()) {
            throw new ExpectationException('"' . $text . '" text was found in the "' . $element . '" element', $this->getSession());
        }

        // We need to ensure all the found nodes are hidden.
        $this->spin(
            function($context, $args) {

                foreach ($args['nodes'] as $node) {
                    if ($node->isVisible()) {
                        throw new ExpectationException('"' . $args['text'] . '" text was found in the "' . $args['element'] . '" element', $context->getSession());
                    }
                }

                // If all the found nodes are hidden we are happy.
                return true;
            },
            array('nodes' => $nodes, 'text' => $text, 'element' => $element),
            behat_base::get_reduced_timeout(),
            false,
            true
        );
    }

    /**
     * Checks, that the first specified element appears before the second one.
     *
     * @Then :preelement :preselectortype should appear before :postelement :postselectortype
     * @Then :preelement :preselectortype should appear before :postelement :postselectortype in the :containerelement :containerselectortype
     * @throws ExpectationException
     * @param string $preelement The locator of the preceding element
     * @param string $preselectortype The selector type of the preceding element
     * @param string $postelement The locator of the latest element
     * @param string $postselectortype The selector type of the latest element
     * @param string $containerelement
     * @param string $containerselectortype
     */
    public function should_appear_before(
        string $preelement,
        string $preselectortype,
        string $postelement,
        string $postselectortype,
        ?string $containerelement = null,
        ?string $containerselectortype = null
    ) {
        $msg = "'{$preelement}' '{$preselectortype}' does not appear before '{$postelement}' '{$postselectortype}'";
        $this->check_element_order(
            $containerelement,
            $containerselectortype,
            $preelement,
            $preselectortype,
            $postelement,
            $postselectortype,
            $msg
        );
    }

    /**
     * Checks, that the first specified element appears after the second one.
     *
     * @Then :postelement :postselectortype should appear after :preelement :preselectortype
     * @Then :postelement :postselectortype should appear after :preelement :preselectortype in the :containerelement :containerselectortype
     * @throws ExpectationException
     * @param string $postelement The locator of the latest element
     * @param string $postselectortype The selector type of the latest element
     * @param string $preelement The locator of the preceding element
     * @param string $preselectortype The selector type of the preceding element
     * @param string $containerelement
     * @param string $containerselectortype
     */
    public function should_appear_after(
        string $postelement,
        string $postselectortype,
        string $preelement,
        string $preselectortype,
        ?string $containerelement = null,
        ?string $containerselectortype = null
    ) {
        $msg = "'{$postelement}' '{$postselectortype}' does not appear after '{$preelement}' '{$preselectortype}'";
        $this->check_element_order(
            $containerelement,
            $containerselectortype,
            $preelement,
            $preselectortype,
            $postelement,
            $postselectortype,
            $msg
        );
    }

    /**
     * Shared code to check whether an element is before or after another one.
     *
     * @param string $containerelement
     * @param string $containerselectortype
     * @param string $preelement The locator of the preceding element
     * @param string $preselectortype The locator of the preceding element
     * @param string $postelement The locator of the following element
     * @param string $postselectortype The selector type of the following element
     * @param string $msg Message to output if this fails
     */
    protected function check_element_order(
        ?string $containerelement,
        ?string $containerselectortype,
        string $preelement,
        string $preselectortype,
        string $postelement,
        string $postselectortype,
        string $msg
    ) {
        $containernode = false;
        if ($containerselectortype && $containerelement) {
            // Get the container node.
            $containernode = $this->get_selected_node($containerselectortype, $containerelement);
            $msg .= " in the '{$containerelement}' '{$containerselectortype}'";
        }

        list($preselector, $prelocator) = $this->transform_selector($preselectortype, $preelement);
        list($postselector, $postlocator) = $this->transform_selector($postselectortype, $postelement);

        $newlines = [
            "\r\n",
            "\r",
            "\n",
        ];
        $prexpath = str_replace($newlines, ' ', $this->find($preselector, $prelocator, false, $containernode)->getXpath());
        $postxpath = str_replace($newlines, ' ', $this->find($postselector, $postlocator, false, $containernode)->getXpath());

        if ($this->running_javascript()) {
            // The xpath to do this was running really slowly on certain Chrome versions so we are using
            // this DOM method instead.
            $js = <<<EOF
(function() {
    var a = document.evaluate("{$prexpath}", document, null, XPathResult.ANY_TYPE, null).iterateNext();
    var b = document.evaluate("{$postxpath}", document, null, XPathResult.ANY_TYPE, null).iterateNext();
    return a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING;
})()
EOF;
            $ok = $this->evaluate_script($js);
        } else {

            // Using following xpath axe to find it.
            $xpath = "{$prexpath}/following::*[contains(., {$postxpath})]";
            $ok = $this->getSession()->getDriver()->find($xpath);
        }

        if (!$ok) {
            throw new ExpectationException($msg, $this->getSession());
        }
    }

    /**
     * Checks, that element of specified type is disabled.
     *
     * @Then /^the "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should be disabled$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $element Element we look in
     * @param string $selectortype The type of element where we are looking in.
     */
    public function the_element_should_be_disabled($element, $selectortype) {

        // Transforming from steps definitions selector/locator format to Mink format and getting the NodeElement.
        $node = $this->get_selected_node($selectortype, $element);

        if (!$node->hasAttribute('disabled')) {
            throw new ExpectationException('The element "' . $element . '" is not disabled', $this->getSession());
        }
    }

    /**
     * Checks, that element of specified type is enabled.
     *
     * @Then /^the "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should be enabled$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $element Element we look on
     * @param string $selectortype The type of where we look
     */
    public function the_element_should_be_enabled($element, $selectortype) {

        // Transforming from steps definitions selector/locator format to mink format and getting the NodeElement.
        $node = $this->get_selected_node($selectortype, $element);

        if ($node->hasAttribute('disabled')) {
            throw new ExpectationException('The element "' . $element . '" is not enabled', $this->getSession());
        }
    }

    /**
     * Checks the provided element and selector type are readonly on the current page.
     *
     * @Then /^the "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should be readonly$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $element Element we look in
     * @param string $selectortype The type of element where we are looking in.
     */
    public function the_element_should_be_readonly($element, $selectortype) {
        // Transforming from steps definitions selector/locator format to Mink format and getting the NodeElement.
        $node = $this->get_selected_node($selectortype, $element);

        if (!$node->hasAttribute('readonly')) {
            throw new ExpectationException('The element "' . $element . '" is not readonly', $this->getSession());
        }
    }

    /**
     * Checks the provided element and selector type are not readonly on the current page.
     *
     * @Then /^the "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should not be readonly$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $element Element we look in
     * @param string $selectortype The type of element where we are looking in.
     */
    public function the_element_should_not_be_readonly($element, $selectortype) {
        // Transforming from steps definitions selector/locator format to Mink format and getting the NodeElement.
        $node = $this->get_selected_node($selectortype, $element);

        if ($node->hasAttribute('readonly')) {
            throw new ExpectationException('The element "' . $element . '" is readonly', $this->getSession());
        }
    }

    /**
     * Checks the provided element and selector type exists in the current page.
     *
     * This step is for advanced users, use it if you don't find anything else suitable for what you need.
     *
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should exist$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $element The locator of the specified selector
     * @param string $selectortype The selector type
     */
    public function should_exist($element, $selectortype) {
        // Will throw an ElementNotFoundException if it does not exist.
        $this->find($selectortype, $element);
    }

    /**
     * Checks that the provided element and selector type not exists in the current page.
     *
     * This step is for advanced users, use it if you don't find anything else suitable for what you need.
     *
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should not exist$/
     * @throws ExpectationException
     * @param string $element The locator of the specified selector
     * @param string $selectortype The selector type
     */
    public function should_not_exist($element, $selectortype) {
        // Will throw an ElementNotFoundException if it does not exist, but, actually it should not exist, so we try &
        // catch it.
        try {
            // The exception does not really matter as we will catch it and will never "explode".
            $exception = new ElementNotFoundException($this->getSession(), $selectortype, null, $element);

            // Using the spin method as we want a reduced timeout but there is no need for a 0.1 seconds interval
            // because in the optimistic case we will timeout.
            // If all goes good it will throw an ElementNotFoundExceptionn that we will catch.
            return $this->find($selectortype, $element, $exception, false, behat_base::get_reduced_timeout());
        } catch (ElementNotFoundException $e) {
            // We expect the element to not be found.
            return;
        }

        // The element was found and should not have been. Throw an exception.
        throw new ExpectationException("The '{$element}' '{$selectortype}' exists in the current page", $this->getSession());
    }

    /**
     * This step triggers cron like a user would do going to admin/cron.php.
     *
     * @Given /^I trigger cron$/
     */
    public function i_trigger_cron() {
        $this->getSession()->visit($this->locate_path('/admin/cron.php'));
    }

    /**
     * Runs a scheduled task immediately, given full class name.
     *
     * This is faster and more reliable than running cron (running cron won't
     * work more than once in the same test, for instance). However it is
     * a little less 'realistic'.
     *
     * While the task is running, we suppress mtrace output because it makes
     * the Behat result look ugly.
     *
     * Note: Most of the code relating to running a task is based on
     * admin/tool/task/cli/schedule_task.php.
     *
     * @Given /^I run the scheduled task "(?P<task_name>[^"]+)"$/
     * @param string $taskname Name of task e.g. 'mod_whatever\task\do_something'
     */
    public function i_run_the_scheduled_task($taskname) {
        global $CFG;
        require_once("{$CFG->libdir}/cronlib.php");

        $task = \core\task\manager::get_scheduled_task($taskname);
        if (!$task) {
            throw new DriverException('The "' . $taskname . '" scheduled task does not exist');
        }

        // Do setup for cron task.
        raise_memory_limit(MEMORY_EXTRA);
        cron_setup_user();

        // Get lock.
        $cronlockfactory = \core\lock\lock_config::get_lock_factory('cron');
        if (!$cronlock = $cronlockfactory->get_lock('core_cron', 10)) {
            throw new DriverException('Unable to obtain core_cron lock for scheduled task');
        }
        if (!$lock = $cronlockfactory->get_lock('\\' . get_class($task), 10)) {
            $cronlock->release();
            throw new DriverException('Unable to obtain task lock for scheduled task');
        }
        $task->set_lock($lock);
        if (!$task->is_blocking()) {
            $cronlock->release();
        } else {
            $task->set_cron_lock($cronlock);
        }

        try {
            // Prepare the renderer.
            cron_prepare_core_renderer();

            // Discard task output as not appropriate for Behat output!
            ob_start();
            $task->execute();
            ob_end_clean();

            // Restore the previous renderer.
            cron_prepare_core_renderer(true);

            // Mark task complete.
            \core\task\manager::scheduled_task_complete($task);
        } catch (Exception $e) {
            // Restore the previous renderer.
            cron_prepare_core_renderer(true);

            // Mark task failed and throw exception.
            \core\task\manager::scheduled_task_failed($task);

            throw new DriverException('The "' . $taskname . '" scheduled task failed', 0, $e);
        }
    }

    /**
     * Runs all ad-hoc tasks in the queue.
     *
     * This is faster and more reliable than running cron (running cron won't
     * work more than once in the same test, for instance). However it is
     * a little less 'realistic'.
     *
     * While the task is running, we suppress mtrace output because it makes
     * the Behat result look ugly.
     *
     * @Given /^I run all adhoc tasks$/
     * @throws DriverException
     */
    public function i_run_all_adhoc_tasks() {
        global $CFG, $DB;
        require_once("{$CFG->libdir}/cronlib.php");

        // Do setup for cron task.
        cron_setup_user();

        // Discard task output as not appropriate for Behat output!
        ob_start();

        // Run all tasks which have a scheduled runtime of before now.
        $timenow = time();

        while (!\core\task\manager::static_caches_cleared_since($timenow) &&
                $task = \core\task\manager::get_next_adhoc_task($timenow)) {
            // Clean the output buffer between tasks.
            ob_clean();

            // Run the task.
            cron_run_inner_adhoc_task($task);

            // Check whether the task record still exists.
            // If a task was successful it will be removed.
            // If it failed then it will still exist.
            if ($DB->record_exists('task_adhoc', ['id' => $task->get_id()])) {
                // End ouptut buffering and flush the current buffer.
                // This should be from just the current task.
                ob_end_flush();

                throw new DriverException('An adhoc task failed', 0);
            }
        }
        ob_end_clean();
    }

    /**
     * Checks that an element and selector type exists in another element and selector type on the current page.
     *
     * This step is for advanced users, use it if you don't find anything else suitable for what you need.
     *
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should exist in the "(?P<element2_string>(?:[^"]|\\")*)" "(?P<selector2_string>[^"]*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $element The locator of the specified selector
     * @param string $selectortype The selector type
     * @param string $containerelement The container selector type
     * @param string $containerselectortype The container locator
     */
    public function should_exist_in_the($element, $selectortype, $containerelement, $containerselectortype) {
        // Get the container node.
        $containernode = $this->find($containerselectortype, $containerelement);

        // Specific exception giving info about where can't we find the element.
        $locatorexceptionmsg = "{$element} in the {$containerelement} {$containerselectortype}";
        $exception = new ElementNotFoundException($this->getSession(), $selectortype, null, $locatorexceptionmsg);

        // Looks for the requested node inside the container node.
        $this->find($selectortype, $element, $exception, $containernode);
    }

    /**
     * Checks that an element and selector type does not exist in another element and selector type on the current page.
     *
     * This step is for advanced users, use it if you don't find anything else suitable for what you need.
     *
     * @Then /^"(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should not exist in the "(?P<element2_string>(?:[^"]|\\")*)" "(?P<selector2_string>[^"]*)"$/
     * @throws ExpectationException
     * @param string $element The locator of the specified selector
     * @param string $selectortype The selector type
     * @param string $containerelement The container selector type
     * @param string $containerselectortype The container locator
     */
    public function should_not_exist_in_the($element, $selectortype, $containerelement, $containerselectortype) {
        // Get the container node.
        $containernode = $this->find($containerselectortype, $containerelement);

        // Will throw an ElementNotFoundException if it does not exist, but, actually it should not exist, so we try &
        // catch it.
        try {
            // Looks for the requested node inside the container node.
            $this->find($selectortype, $element, false, $containernode, behat_base::get_reduced_timeout());
        } catch (ElementNotFoundException $e) {
            // We expect the element to not be found.
            return;
        }

        // The element was found and should not have been. Throw an exception.
        throw new ExpectationException(
            "The '{$element}' '{$selectortype}' exists in the '{$containerelement}' '{$containerselectortype}'",
            $this->getSession()
        );
    }

    /**
     * Change browser window size small: 640x480, medium: 1024x768, large: 2560x1600, custom: widthxheight
     *
     * Example: I change window size to "small" or I change window size to "1024x768"
     * or I change viewport size to "800x600". The viewport option is useful to guarantee that the
     * browser window has same viewport size even when you run Behat on multiple operating systems.
     *
     * @throws ExpectationException
     * @Then /^I change (window|viewport) size to "(small|medium|large|\d+x\d+)"$/
     * @Then /^I change the (window|viewport) size to "(small|medium|large|\d+x\d+)"$/
     * @param string $windowsize size of the window (small|medium|large|wxh).
     */
    public function i_change_window_size_to($windowviewport, $windowsize) {
        $this->resize_window($windowsize, $windowviewport === 'viewport');
    }

    /**
     * Checks whether there is an attribute on the given element that contains the specified text.
     *
     * @Then /^the "(?P<attribute_string>[^"]*)" attribute of "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should contain "(?P<text_string>(?:[^"]|\\")*)"$/
     * @throws ExpectationException
     * @param string $attribute Name of attribute
     * @param string $element The locator of the specified selector
     * @param string $selectortype The selector type
     * @param string $text Expected substring
     */
    public function the_attribute_of_should_contain($attribute, $element, $selectortype, $text) {
        // Get the container node (exception if it doesn't exist).
        $containernode = $this->get_selected_node($selectortype, $element);
        $value = $containernode->getAttribute($attribute);
        if ($value == null) {
            throw new ExpectationException('The attribute "' . $attribute. '" does not exist',
                    $this->getSession());
        } else if (strpos($value, $text) === false) {
            throw new ExpectationException('The attribute "' . $attribute .
                    '" does not contain "' . $text . '" (actual value: "' . $value . '")',
                    $this->getSession());
        }
    }

    /**
     * Checks that the attribute on the given element does not contain the specified text.
     *
     * @Then /^the "(?P<attribute_string>[^"]*)" attribute of "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should not contain "(?P<text_string>(?:[^"]|\\")*)"$/
     * @throws ExpectationException
     * @param string $attribute Name of attribute
     * @param string $element The locator of the specified selector
     * @param string $selectortype The selector type
     * @param string $text Expected substring
     */
    public function the_attribute_of_should_not_contain($attribute, $element, $selectortype, $text) {
        // Get the container node (exception if it doesn't exist).
        $containernode = $this->get_selected_node($selectortype, $element);
        $value = $containernode->getAttribute($attribute);
        if ($value == null) {
            throw new ExpectationException('The attribute "' . $attribute. '" does not exist',
                    $this->getSession());
        } else if (strpos($value, $text) !== false) {
            throw new ExpectationException('The attribute "' . $attribute .
                    '" contains "' . $text . '" (value: "' . $value . '")',
                    $this->getSession());
        }
    }

    /**
     * Checks the provided value exists in specific row/column of table.
     *
     * @Then /^"(?P<row_string>[^"]*)" row "(?P<column_string>[^"]*)" column of "(?P<table_string>[^"]*)" table should contain "(?P<value_string>[^"]*)"$/
     * @throws ElementNotFoundException
     * @param string $row row text which will be looked in.
     * @param string $column column text to search (or numeric value for the column position)
     * @param string $table table id/class/caption
     * @param string $value text to check.
     */
    public function row_column_of_table_should_contain($row, $column, $table, $value) {
        $tablenode = $this->get_selected_node('table', $table);
        $tablexpath = $tablenode->getXpath();

        $rowliteral = behat_context_helper::escape($row);
        $valueliteral = behat_context_helper::escape($value);
        $columnliteral = behat_context_helper::escape($column);

        if (preg_match('/^-?(\d+)-?$/', $column, $columnasnumber)) {
            // Column indicated as a number, just use it as position of the column.
            $columnpositionxpath = "/child::*[position() = {$columnasnumber[1]}]";
        } else {
            // Header can be in thead or tbody (first row), following xpath should work.
            $theadheaderxpath = "thead/tr[1]/th[(normalize-space(.)=" . $columnliteral . " or a[normalize-space(text())=" .
                    $columnliteral . "] or div[normalize-space(text())=" . $columnliteral . "])]";
            $tbodyheaderxpath = "tbody/tr[1]/td[(normalize-space(.)=" . $columnliteral . " or a[normalize-space(text())=" .
                    $columnliteral . "] or div[normalize-space(text())=" . $columnliteral . "])]";

            // Check if column exists.
            $columnheaderxpath = $tablexpath . "[" . $theadheaderxpath . " | " . $tbodyheaderxpath . "]";
            $columnheader = $this->getSession()->getDriver()->find($columnheaderxpath);
            if (empty($columnheader)) {
                $columnexceptionmsg = $column . '" in table "' . $table . '"';
                throw new ElementNotFoundException($this->getSession(), "\n$columnheaderxpath\n\n".'Column', null, $columnexceptionmsg);
            }
            // Following conditions were considered before finding column count.
            // 1. Table header can be in thead/tr/th or tbody/tr/td[1].
            // 2. First column can have th (Gradebook -> user report), so having lenient sibling check.
            $columnpositionxpath = "/child::*[position() = count(" . $tablexpath . "/" . $theadheaderxpath .
                "/preceding-sibling::*) + 1]";
        }

        // Check if value exists in specific row/column.
        // Get row xpath.
        // GoutteDriver uses DomCrawler\Crawler and it is making XPath relative to the current context, so use descendant.
        $rowxpath = $tablexpath."/tbody/tr[descendant::th[normalize-space(.)=" . $rowliteral .
                    "] | descendant::td[normalize-space(.)=" . $rowliteral . "]]";

        $columnvaluexpath = $rowxpath . $columnpositionxpath . "[contains(normalize-space(.)," . $valueliteral . ")]";

        // Looks for the requested node inside the container node.
        $coumnnode = $this->getSession()->getDriver()->find($columnvaluexpath);
        if (empty($coumnnode)) {
            $locatorexceptionmsg = $value . '" in "' . $row . '" row with column "' . $column;
            throw new ElementNotFoundException($this->getSession(), "\n$columnvaluexpath\n\n".'Column value', null, $locatorexceptionmsg);
        }
    }

    /**
     * Checks the provided value should not exist in specific row/column of table.
     *
     * @Then /^"(?P<row_string>[^"]*)" row "(?P<column_string>[^"]*)" column of "(?P<table_string>[^"]*)" table should not contain "(?P<value_string>[^"]*)"$/
     * @throws ElementNotFoundException
     * @param string $row row text which will be looked in.
     * @param string $column column text to search
     * @param string $table table id/class/caption
     * @param string $value text to check.
     */
    public function row_column_of_table_should_not_contain($row, $column, $table, $value) {
        try {
            $this->row_column_of_table_should_contain($row, $column, $table, $value);
        } catch (ElementNotFoundException $e) {
            // Table row/column doesn't contain this value. Nothing to do.
            return;
        }
        // Throw exception if found.
        throw new ExpectationException(
            '"' . $column . '" with value "' . $value . '" is present in "' . $row . '"  row for table "' . $table . '"',
            $this->getSession()
        );
    }

    /**
     * Checks that the provided value exist in table.
     *
     * First row may contain column headers or numeric indexes of the columns
     * (syntax -1- is also considered to be column index). Column indexes are
     * useful in case of multirow headers and/or presence of cells with colspan.
     *
     * @Then /^the following should exist in the "(?P<table_string>[^"]*)" table:$/
     * @throws ExpectationException
     * @param string $table name of table
     * @param TableNode $data table with first row as header and following values
     *        | Header 1 | Header 2 | Header 3 |
     *        | Value 1 | Value 2 | Value 3|
     */
    public function following_should_exist_in_the_table($table, TableNode $data) {
        $datahash = $data->getHash();

        foreach ($datahash as $row) {
            $firstcell = null;
            foreach ($row as $column => $value) {
                if ($firstcell === null) {
                    $firstcell = $value;
                } else {
                    $this->row_column_of_table_should_contain($firstcell, $column, $table, $value);
                }
            }
        }
    }

    /**
     * Checks that the provided values do not exist in a table.
     *
     * @Then /^the following should not exist in the "(?P<table_string>[^"]*)" table:$/
     * @throws ExpectationException
     * @param string $table name of table
     * @param TableNode $data table with first row as header and following values
     *        | Header 1 | Header 2 | Header 3 |
     *        | Value 1 | Value 2 | Value 3|
     */
    public function following_should_not_exist_in_the_table($table, TableNode $data) {
        $datahash = $data->getHash();

        foreach ($datahash as $value) {
            $row = array_shift($value);
            foreach ($value as $column => $value) {
                try {
                    $this->row_column_of_table_should_contain($row, $column, $table, $value);
                    // Throw exception if found.
                } catch (ElementNotFoundException $e) {
                    // Table row/column doesn't contain this value. Nothing to do.
                    continue;
                }
                throw new ExpectationException('"' . $column . '" with value "' . $value . '" is present in "' .
                    $row . '"  row for table "' . $table . '"', $this->getSession()
                );
            }
        }
    }

    /**
     * Given the text of a link, download the linked file and return the contents.
     *
     * This is a helper method used by {@link following_should_download_bytes()}
     * and {@link following_should_download_between_and_bytes()}
     *
     * @param string $link the text of the link.
     * @return string the content of the downloaded file.
     */
    public function download_file_from_link($link) {
        // Find the link.
        $linknode = $this->find_link($link);
        $this->ensure_node_is_visible($linknode);

        // Get the href and check it.
        $url = $linknode->getAttribute('href');
        if (!$url) {
            throw new ExpectationException('Download link does not have href attribute',
                    $this->getSession());
        }
        if (!preg_match('~^https?://~', $url)) {
            throw new ExpectationException('Download link not an absolute URL: ' . $url,
                    $this->getSession());
        }

        // Download the URL and check the size.
        $session = $this->getSession()->getCookie('MoodleSession');
        return download_file_content($url, array('Cookie' => 'MoodleSession=' . $session));
    }

    /**
     * Downloads the file from a link on the page and checks the size.
     *
     * Only works if the link has an href attribute. Javascript downloads are
     * not supported. Currently, the href must be an absolute URL.
     *
     * @Then /^following "(?P<link_string>[^"]*)" should download "(?P<expected_bytes>\d+)" bytes$/
     * @throws ExpectationException
     * @param string $link the text of the link.
     * @param number $expectedsize the expected file size in bytes.
     */
    public function following_should_download_bytes($link, $expectedsize) {
        $exception = new ExpectationException('Error while downloading data from ' . $link, $this->getSession());

        // It will stop spinning once file is downloaded or time out.
        $result = $this->spin(
            function($context, $args) {
                $link = $args['link'];
                return $this->download_file_from_link($link);
            },
            array('link' => $link),
            behat_base::get_extended_timeout(),
            $exception
        );

        // Check download size.
        $actualsize = (int)strlen($result);
        if ($actualsize !== (int)$expectedsize) {
            throw new ExpectationException('Downloaded data was ' . $actualsize .
                    ' bytes, expecting ' . $expectedsize, $this->getSession());
        }
    }

    /**
     * Downloads the file from a link on the page and checks the size is in a given range.
     *
     * Only works if the link has an href attribute. Javascript downloads are
     * not supported. Currently, the href must be an absolute URL.
     *
     * The range includes the endpoints. That is, a 10 byte file in considered to
     * be between "5" and "10" bytes, and between "10" and "20" bytes.
     *
     * @Then /^following "(?P<link_string>[^"]*)" should download between "(?P<min_bytes>\d+)" and "(?P<max_bytes>\d+)" bytes$/
     * @throws ExpectationException
     * @param string $link the text of the link.
     * @param number $minexpectedsize the minimum expected file size in bytes.
     * @param number $maxexpectedsize the maximum expected file size in bytes.
     */
    public function following_should_download_between_and_bytes($link, $minexpectedsize, $maxexpectedsize) {
        // If the minimum is greater than the maximum then swap the values.
        if ((int)$minexpectedsize > (int)$maxexpectedsize) {
            list($minexpectedsize, $maxexpectedsize) = array($maxexpectedsize, $minexpectedsize);
        }

        $exception = new ExpectationException('Error while downloading data from ' . $link, $this->getSession());

        // It will stop spinning once file is downloaded or time out.
        $result = $this->spin(
            function($context, $args) {
                $link = $args['link'];

                return $this->download_file_from_link($link);
            },
            array('link' => $link),
            behat_base::get_extended_timeout(),
            $exception
        );

        // Check download size.
        $actualsize = (int)strlen($result);
        if ($actualsize < $minexpectedsize || $actualsize > $maxexpectedsize) {
            throw new ExpectationException('Downloaded data was ' . $actualsize .
                    ' bytes, expecting between ' . $minexpectedsize . ' and ' .
                    $maxexpectedsize, $this->getSession());
        }
    }

    /**
     * Checks that the image on the page is the same as one of the fixture files
     *
     * @Then /^the image at "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should be identical to "(?P<filepath_string>(?:[^"]|\\")*)"$/
     * @throws ExpectationException
     * @param string $element The locator of the image
     * @param string $selectortype The selector type
     * @param string $filepath path to the fixture file
     */
    public function the_image_at_should_be_identical_to($element, $selectortype, $filepath) {
        global $CFG;

        // Get the container node (exception if it doesn't exist).
        $containernode = $this->get_selected_node($selectortype, $element);
        $url = $containernode->getAttribute('src');
        if ($url == null) {
            throw new ExpectationException('Element does not have src attribute',
                $this->getSession());
        }
        $session = $this->getSession()->getCookie('MoodleSession');
        $content = download_file_content($url, array('Cookie' => 'MoodleSession=' . $session));

        // Get the content of the fixture file.
        // Replace 'admin/' if it is in start of path with $CFG->admin .
        if (substr($filepath, 0, 6) === 'admin/') {
            $filepath = $CFG->admin . DIRECTORY_SEPARATOR . substr($filepath, 6);
        }
        $filepath = str_replace('/', DIRECTORY_SEPARATOR, $filepath);
        $filepath = $CFG->dirroot . DIRECTORY_SEPARATOR . $filepath;
        if (!is_readable($filepath)) {
            throw new ExpectationException('The file to compare to does not exist.', $this->getSession());
        }
        $expectedcontent = file_get_contents($filepath);

        if ($content !== $expectedcontent) {
            throw new ExpectationException('Image is not identical to the fixture. Received ' .
            strlen($content) . ' bytes and expected ' . strlen($expectedcontent) . ' bytes');
        }
    }

    /**
     * Prepare to detect whether or not a new page has loaded (or the same page reloaded) some time in the future.
     *
     * @Given /^I start watching to see if a new page loads$/
     */
    public function i_start_watching_to_see_if_a_new_page_loads() {
        if (!$this->running_javascript()) {
            throw new DriverException('Page load detection requires JavaScript.');
        }

        $session = $this->getSession();

        if ($this->pageloaddetectionrunning || $session->getPage()->find('xpath', $this->get_page_load_xpath())) {
            // If we find this node at this point we are already watching for a reload and the behat steps
            // are out of order. We will treat this as an error - really it needs to be fixed as it indicates a problem.
            throw new ExpectationException(
                'Page load expectation error: page reloads are already been watched for.', $session);
        }

        $this->pageloaddetectionrunning = true;

        $this->execute_script(
            'var span = document.createElement("span");
            span.setAttribute("data-rel", "' . self::PAGE_LOAD_DETECTION_STRING . '");
            span.setAttribute("style", "display: none;");
            document.body.appendChild(span);'
        );
    }

    /**
     * Verify that a new page has loaded (or the same page has reloaded) since the
     * last "I start watching to see if a new page loads" step.
     *
     * @Given /^a new page should have loaded since I started watching$/
     */
    public function a_new_page_should_have_loaded_since_i_started_watching() {
        $session = $this->getSession();

        // Make sure page load tracking was started.
        if (!$this->pageloaddetectionrunning) {
            throw new ExpectationException(
                'Page load expectation error: page load tracking was not started.', $session);
        }

        // As the node is inserted by code above it is either there or not, and we do not need spin and it is safe
        // to use the native API here which is great as exception handling (the alternative is slow).
        if ($session->getPage()->find('xpath', $this->get_page_load_xpath())) {
            // We don't want to find this node, if we do we have an error.
            throw new ExpectationException(
                'Page load expectation error: a new page has not been loaded when it should have been.', $session);
        }

        // Cancel the tracking of pageloaddetectionrunning.
        $this->pageloaddetectionrunning = false;
    }

    /**
     * Verify that a new page has not loaded (or the same page has reloaded) since the
     * last "I start watching to see if a new page loads" step.
     *
     * @Given /^a new page should not have loaded since I started watching$/
     */
    public function a_new_page_should_not_have_loaded_since_i_started_watching() {
        $session = $this->getSession();

        // Make sure page load tracking was started.
        if (!$this->pageloaddetectionrunning) {
            throw new ExpectationException(
                'Page load expectation error: page load tracking was not started.', $session);
        }

        // We use our API here as we can use the exception handling provided by it.
        $this->find(
            'xpath',
            $this->get_page_load_xpath(),
            new ExpectationException(
                'Page load expectation error: A new page has been loaded when it should not have been.',
                $this->getSession()
            )
        );
    }

    /**
     * Helper used by {@link a_new_page_should_have_loaded_since_i_started_watching}
     * and {@link a_new_page_should_not_have_loaded_since_i_started_watching}
     * @return string xpath expression.
     */
    protected function get_page_load_xpath() {
        return "//span[@data-rel = '" . self::PAGE_LOAD_DETECTION_STRING . "']";
    }

    /**
     * Wait unit user press Enter/Return key. Useful when debugging a scenario.
     *
     * @Then /^(?:|I )pause(?:| scenario execution)$/
     */
    public function i_pause_scenario_execution() {
        $message = "<colour:lightYellow>Paused. Press <colour:lightRed>Enter/Return<colour:lightYellow> to continue.";
        behat_util::pause($this->getSession(), $message);
    }

    /**
     * Presses a given button in the browser.
     * NOTE: Phantomjs and goutte driver reloads page while navigating back and forward.
     *
     * @Then /^I press the "(back|forward|reload)" button in the browser$/
     * @param string $button the button to press.
     * @throws ExpectationException
     */
    public function i_press_in_the_browser($button) {
        $session = $this->getSession();

        if ($button == 'back') {
            $session->back();
        } else if ($button == 'forward') {
            $session->forward();
        } else if ($button == 'reload') {
            $session->reload();
        } else {
            throw new ExpectationException('Unknown browser button.', $session);
        }
    }

    /**
     * Trigger a keydown event for a key on a specific element.
     *
     * @When /^I press key "(?P<key_string>(?:[^"]|\\")*)" in "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)"$/
     * @param string $key either char-code or character itself,
     *               may optionally be prefixed with ctrl-, alt-, shift- or meta-
     * @param string $element Element we look for
     * @param string $selectortype The type of what we look for
     * @throws DriverException
     * @throws ExpectationException
     */
    public function i_press_key_in_element($key, $element, $selectortype) {
        if (!$this->running_javascript()) {
            throw new DriverException('Key down step is not available with Javascript disabled');
        }
        // Gets the node based on the requested selector type and locator.
        $node = $this->get_selected_node($selectortype, $element);
        $modifier = null;
        $validmodifiers = array('ctrl', 'alt', 'shift', 'meta');
        $char = $key;
        if (strpos($key, '-')) {
            list($modifier, $char) = preg_split('/-/', $key, 2);
            $modifier = strtolower($modifier);
            if (!in_array($modifier, $validmodifiers)) {
                throw new ExpectationException(sprintf('Unknown key modifier: %s.', $modifier));
            }
        }
        if (is_numeric($char)) {
            $char = (int)$char;
        }

        $node->keyDown($char, $modifier);
        $node->keyPress($char, $modifier);
        $node->keyUp($char, $modifier);
    }

    /**
     * Press tab key on a specific element.
     *
     * @When /^I press tab key in "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)"$/
     * @param string $element Element we look for
     * @param string $selectortype The type of what we look for
     * @throws DriverException
     * @throws ExpectationException
     */
    public function i_post_tab_key_in_element($element, $selectortype) {
        if (!$this->running_javascript()) {
            throw new DriverException('Tab press step is not available with Javascript disabled');
        }
        // Gets the node based on the requested selector type and locator.
        $node = $this->get_selected_node($selectortype, $element);
        $driver = $this->getSession()->getDriver();
        if ($driver instanceof \Moodle\BehatExtension\Driver\MoodleSelenium2Driver) {
            $driver->post_key("\xEE\x80\x84", $node->getXpath());
        } else {
            $driver->keyDown($node->getXpath(), "\t");
        }
    }

    /**
     * Checks if database family used is using one of the specified, else skip. (mysql, postgres, mssql, oracle, etc.)
     *
     * @Given /^database family used is one of the following:$/
     * @param TableNode $databasefamilies list of database.
     * @return void.
     * @throws \Moodle\BehatExtension\Exception\SkippedException
     */
    public function database_family_used_is_one_of_the_following(TableNode $databasefamilies) {
        global $DB;

        $dbfamily = $DB->get_dbfamily();

        // Check if used db family is one of the specified ones. If yes then return.
        foreach ($databasefamilies->getRows() as $dbfamilytocheck) {
            if ($dbfamilytocheck[0] == $dbfamily) {
                return;
            }
        }

        throw new \Moodle\BehatExtension\Exception\SkippedException();
    }

    /**
     * Checks focus is with the given element.
     *
     * @Then /^the focused element is( not)? "(?P<node_string>(?:[^"]|\\")*)" "(?P<node_selector_string>[^"]*)"$/
     * @param string $not optional step verifier
     * @param string $nodeelement Element identifier
     * @param string $nodeselectortype Element type
     * @throws DriverException If not using JavaScript
     * @throws ExpectationException
     */
    public function the_focused_element_is($not, $nodeelement, $nodeselectortype) {
        if (!$this->running_javascript()) {
            throw new DriverException('Checking focus on an element requires JavaScript');
        }

        $element = $this->find($nodeselectortype, $nodeelement);
        $xpath = addslashes_js($element->getXpath());
        $script = 'return (function() { return document.activeElement === document.evaluate("' . $xpath . '",
                document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue; })(); ';
        $targetisfocused = $this->evaluate_script($script);
        if ($not == ' not') {
            if ($targetisfocused) {
                throw new ExpectationException("$nodeelement $nodeselectortype is focused", $this->getSession());
            }
        } else {
            if (!$targetisfocused) {
                throw new ExpectationException("$nodeelement $nodeselectortype is not focused", $this->getSession());
            }
        }
    }

    /**
     * Checks focus is with the given element.
     *
     * @Then /^the focused element is( not)? "(?P<n>(?:[^"]|\\")*)" "(?P<ns>[^"]*)" in the "(?P<c>(?:[^"]|\\")*)" "(?P<cs>[^"]*)"$/
     * @param string $not string optional step verifier
     * @param string $element Element identifier
     * @param string $selectortype Element type
     * @param string $nodeelement Element we look in
     * @param string $nodeselectortype The type of selector where we look in
     * @throws DriverException If not using JavaScript
     * @throws ExpectationException
     */
    public function the_focused_element_is_in_the($not, $element, $selectortype, $nodeelement, $nodeselectortype) {
        if (!$this->running_javascript()) {
            throw new DriverException('Checking focus on an element requires JavaScript');
        }
        $element = $this->get_node_in_container($selectortype, $element, $nodeselectortype, $nodeelement);
        $xpath = addslashes_js($element->getXpath());
        $script = 'return (function() { return document.activeElement === document.evaluate("' . $xpath . '",
                document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue; })(); ';
        $targetisfocused = $this->evaluate_script($script);
        if ($not == ' not') {
            if ($targetisfocused) {
                throw new ExpectationException("$nodeelement $nodeselectortype is focused", $this->getSession());
            }
        } else {
            if (!$targetisfocused) {
                throw new ExpectationException("$nodeelement $nodeselectortype is not focused", $this->getSession());
            }
        }
    }

    /**
     * Manually press tab key.
     *
     * @When /^I press( shift)? tab$/
     * @param string $shift string optional step verifier
     * @throws DriverException
     */
    public function i_manually_press_tab($shift = '') {
        if (!$this->running_javascript()) {
            throw new DriverException($shift . ' Tab press step is not available with Javascript disabled');
        }

        $value = ($shift == ' shift') ? [\WebDriver\Key::SHIFT . \WebDriver\Key::TAB] : [\WebDriver\Key::TAB];
        $this->getSession()->getDriver()->getWebDriverSession()->activeElement()->postValue(['value' => $value]);
    }

    /**
     * Trigger click on node via javascript instead of actually clicking on it via pointer.
     * This function resolves the issue of nested elements.
     *
     * @When /^I click on "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" skipping visibility check$/
     * @param string $element
     * @param string $selectortype
     */
    public function i_click_on_skipping_visibility_check($element, $selectortype) {

        // Gets the node based on the requested selector type and locator.
        $node = $this->get_selected_node($selectortype, $element);
        $this->js_trigger_click($node);
    }

    /**
     * Checks, that the specified element contains the specified text a certain amount of times.
     * When running Javascript tests it also considers that texts may be hidden.
     *
     * @Then /^I should see "(?P<elementscount_number>\d+)" occurrences of "(?P<text_string>(?:[^"]|\\")*)" in the "(?P<element_string>(?:[^"]|\\")*)" "(?P<text_selector_string>[^"]*)"$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @param int    $elementscount How many occurrences of the element we look for.
     * @param string $text
     * @param string $element Element we look in.
     * @param string $selectortype The type of element where we are looking in.
     */
    public function i_should_see_occurrences_of_in_element($elementscount, $text, $element, $selectortype) {

        // Getting the container where the text should be found.
        $container = $this->get_selected_node($selectortype, $element);

        // Looking for all the matching nodes without any other descendant matching the
        // same xpath (we are using contains(., ....).
        $xpathliteral = behat_context_helper::escape($text);
        $xpath = "/descendant-or-self::*[contains(., $xpathliteral)]" .
                "[count(descendant::*[contains(., $xpathliteral)]) = 0]";

        $nodes = $this->find_all('xpath', $xpath, false, $container);

        if ($this->running_javascript()) {
            $nodes = array_filter($nodes, function($node) {
                return $node->isVisible();
            });
        }

        if ($elementscount != count($nodes)) {
            throw new ExpectationException('Found '.count($nodes).' elements in column. Expected '.$elementscount,
                    $this->getSession());
        }
    }

    /**
     * Visit a local URL relative to the behat root.
     *
     * @When I visit :localurl
     *
     * @param string|moodle_url $localurl The URL relative to the behat_wwwroot to visit.
     */
    public function i_visit($localurl): void {
        $localurl = new moodle_url($localurl);
        $this->getSession()->visit($this->locate_path($localurl->out_as_local_url(false)));
    }
}
