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
 * Base class of all steps definitions.
 *
 * This script is only called from Behat as part of it's integration
 * in Moodle.
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException,
    Behat\Mink\Element\NodeElement as NodeElement;

/**
 * Steps definitions base class.
 *
 * To extend by the steps definitions of the different Moodle components.
 *
 * It can not contain steps definitions to avoid duplicates, only utility
 * methods shared between steps.
 *
 * @method NodeElement find_field(string $locator) Finds a form element
 * @method NodeElement find_button(string $locator) Finds a form input submit element or a button
 * @method NodeElement find_link(string $locator) Finds a link on a page
 * @method NodeElement find_file(string $locator) Finds a forum input file element
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_base extends Behat\MinkExtension\Context\RawMinkContext {

    /**
     * Small timeout.
     *
     * A reduced timeout for cases where self::TIMEOUT is too much
     * and a simple $this->getSession()->getPage()->find() could not
     * be enough.
     */
    const REDUCED_TIMEOUT = 2;

    /**
     * The timeout for each Behat step (load page, wait for an element to load...).
     */
    const TIMEOUT = 6;

    /**
     * And extended timeout for specific cases.
     */
    const EXTENDED_TIMEOUT = 10;

    /**
     * The JS code to check that the page is ready.
     */
    const PAGE_READY_JS = '(typeof M !== "undefined" && M.util && M.util.pending_js && !Boolean(M.util.pending_js.length)) && (document.readyState === "complete")';

    /**
     * Locates url, based on provided path.
     * Override to provide custom routing mechanism.
     *
     * @see Behat\MinkExtension\Context\MinkContext
     * @param string $path
     * @return string
     */
    protected function locate_path($path) {
        $starturl = rtrim($this->getMinkParameter('base_url'), '/') . '/';
        return 0 !== strpos($path, 'http') ? $starturl . ltrim($path, '/') : $path;
    }

    /**
     * Returns the first matching element.
     *
     * @link http://mink.behat.org/#traverse-the-page-selectors
     * @param string $selector The selector type (css, xpath, named...)
     * @param mixed $locator It depends on the $selector, can be the xpath, a name, a css locator...
     * @param Exception $exception Otherwise we throw exception with generic info
     * @param NodeElement $node Spins around certain DOM node instead of the whole page
     * @param int $timeout Forces a specific time out (in seconds).
     * @return NodeElement
     */
    protected function find($selector, $locator, $exception = false, $node = false, $timeout = false) {

        // Returns the first match.
        $items = $this->find_all($selector, $locator, $exception, $node, $timeout);
        return count($items) ? reset($items) : null;
    }

    /**
     * Returns all matching elements.
     *
     * Adapter to Behat\Mink\Element\Element::findAll() using the spin() method.
     *
     * @link http://mink.behat.org/#traverse-the-page-selectors
     * @param string $selector The selector type (css, xpath, named...)
     * @param mixed $locator It depends on the $selector, can be the xpath, a name, a css locator...
     * @param Exception $exception Otherwise we throw expcetion with generic info
     * @param NodeElement $node Spins around certain DOM node instead of the whole page
     * @param int $timeout Forces a specific time out (in seconds). If 0 is provided the default timeout will be applied.
     * @return array NodeElements list
     */
    protected function find_all($selector, $locator, $exception = false, $node = false, $timeout = false) {

        // Generic info.
        if (!$exception) {

            // With named selectors we can be more specific.
            if ($selector == 'named') {
                $exceptiontype = $locator[0];
                $exceptionlocator = $locator[1];

                // If we are in a @javascript session all contents would be displayed as HTML characters.
                if ($this->running_javascript()) {
                    $locator[1] = html_entity_decode($locator[1], ENT_NOQUOTES);
                }

            } else {
                $exceptiontype = $selector;
                $exceptionlocator = $locator;
            }

            $exception = new ElementNotFoundException($this->getSession(), $exceptiontype, null, $exceptionlocator);
        }

        $params = array('selector' => $selector, 'locator' => $locator);
        // Pushing $node if required.
        if ($node) {
            $params['node'] = $node;
        }

        // How much we will be waiting for the element to appear.
        if (!$timeout) {
            $timeout = self::TIMEOUT;
            $microsleep = false;
        } else {
            // Spinning each 0.1 seconds if the timeout was forced as we understand
            // that is a special case and is good to refine the performance as much
            // as possible.
            $microsleep = true;
        }

        // Waits for the node to appear if it exists, otherwise will timeout and throw the provided exception.
        return $this->spin(
            function($context, $args) {

                // If no DOM node provided look in all the page.
                if (empty($args['node'])) {
                    return $context->getSession()->getPage()->findAll($args['selector'], $args['locator']);
                }

                // For nodes contained in other nodes we can not use the basic named selectors
                // as they include unions and they would look for matches in the DOM root.
                $elementxpath = $context->getSession()->getSelectorsHandler()->selectorToXpath($args['selector'], $args['locator']);

                // Split the xpath in unions and prefix them with the container xpath.
                $unions = explode('|', $elementxpath);
                foreach ($unions as $key => $union) {
                    $union = trim($union);

                    // We are in the container node.
                    if (strpos($union, '.') === 0) {
                        $union = substr($union, 1);
                    } else if (strpos($union, '/') !== 0) {
                        // Adding the path separator in case it is not there.
                        $union = '/' . $union;
                    }
                    $unions[$key] = $args['node']->getXpath() . $union;
                }

                // We can not use usual Element::find() as it prefixes with DOM root.
                return $context->getSession()->getDriver()->find(implode('|', $unions));
            },
            $params,
            $timeout,
            $exception,
            $microsleep
        );
    }

    /**
     * Finds DOM nodes in the page using named selectors.
     *
     * The point of using this method instead of Mink ones is the spin
     * method of behat_base::find() that looks for the element until it
     * is available or it timeouts, this avoids the false failures received
     * when selenium tries to execute commands on elements that are not
     * ready to be used.
     *
     * All steps that requires elements to be available before interact with
     * them should use one of the find* methods.
     *
     * The methods calls requires a {'find_' . $elementtype}($locator)
     * format, like find_link($locator), find_select($locator),
     * find_button($locator)...
     *
     * @link http://mink.behat.org/#named-selectors
     * @throws coding_exception
     * @param string $name The name of the called method
     * @param mixed $arguments
     * @return NodeElement
     */
    public function __call($name, $arguments) {

        if (substr($name, 0, 5) !== 'find_') {
            throw new coding_exception('The "' . $name . '" method does not exist');
        }

        // Only the named selector identifier.
        $cleanname = substr($name, 5);

        // All named selectors shares the interface.
        if (count($arguments) !== 1) {
            throw new coding_exception('The "' . $cleanname . '" named selector needs the locator as it\'s single argument');
        }

        // Redirecting execution to the find method with the specified selector.
        // It will detect if it's pointing to an unexisting named selector.
        return $this->find('named',
            array(
                $cleanname,
                $this->getSession()->getSelectorsHandler()->xpathLiteral($arguments[0])
            )
        );
    }

    /**
     * Escapes the double quote character.
     *
     * Double quote is the argument delimiter, it can be escaped
     * with a backslash, but we auto-remove this backslashes
     * before the step execution, this method is useful when using
     * arguments as arguments for other steps.
     *
     * @param string $string
     * @return string
     */
    public function escape($string) {
        return str_replace('"', '\"', $string);
    }

    /**
     * Executes the passed closure until returns true or time outs.
     *
     * In most cases the document.readyState === 'complete' will be enough, but sometimes JS
     * requires more time to be completely loaded or an element to be visible or whatever is required to
     * perform some action on an element; this method receives a closure which should contain the
     * required statements to ensure the step definition actions and assertions have all their needs
     * satisfied and executes it until they are satisfied or it timeouts. Redirects the return of the
     * closure to the caller.
     *
     * The closures requirements to work well with this spin method are:
     * - Must return false, null or '' if something goes wrong
     * - Must return something != false if finishes as expected, this will be the (mixed) value
     * returned by spin()
     *
     * The arguments of the closure are mixed, use $args depending on your needs.
     *
     * You can provide an exception to give more accurate feedback to tests writers, otherwise the
     * closure exception will be used, but you must provide an exception if the closure does not throw
     * an exception.
     *
     * @throws Exception If it timeouts without receiving something != false from the closure
     * @param Function|array|string $lambda The function to execute or an array passed to call_user_func (maps to a class method)
     * @param mixed $args Arguments to pass to the closure
     * @param int $timeout Timeout in seconds
     * @param Exception $exception The exception to throw in case it time outs.
     * @param bool $microsleep If set to true it'll sleep micro seconds rather than seconds.
     * @return mixed The value returned by the closure
     */
    protected function spin($lambda, $args = false, $timeout = false, $exception = false, $microsleep = false) {

        // Using default timeout which is pretty high.
        if (!$timeout) {
            $timeout = self::TIMEOUT;
        }
        if ($microsleep) {
            // Will sleep 1/10th of a second by default for self::TIMEOUT seconds.
            $loops = $timeout * 10;
        } else {
            // Will sleep for self::TIMEOUT seconds.
            $loops = $timeout;
        }

        // DOM will never change on non-javascript case; do not wait or try again.
        if (!$this->running_javascript()) {
            $loops = 1;
        }

        for ($i = 0; $i < $loops; $i++) {
            // We catch the exception thrown by the step definition to execute it again.
            try {
                // We don't check with !== because most of the time closures will return
                // direct Behat methods returns and we are not sure it will be always (bool)false
                // if it just runs the behat method without returning anything $return == null.
                if ($return = call_user_func($lambda, $this, $args)) {
                    return $return;
                }
            } catch (Exception $e) {
                // We would use the first closure exception if no exception has been provided.
                if (!$exception) {
                    $exception = $e;
                }
                // We wait until no exception is thrown or timeout expires.
                continue;
            }

            if ($this->running_javascript()) {
                if ($microsleep) {
                    usleep(100000);
                } else {
                    sleep(1);
                }
            }
        }

        // Using coding_exception as is a development issue if no exception has been provided.
        if (!$exception) {
            $exception = new coding_exception('spin method requires an exception if the callback does not throw an exception');
        }

        // Throwing exception to the user.
        throw $exception;
    }

    /**
     * Gets a NodeElement based on the locator and selector type received as argument from steps definitions.
     *
     * Use behat_base::get_text_selector_node() for text-based selectors.
     *
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $selectortype
     * @param string $element
     * @return NodeElement
     */
    protected function get_selected_node($selectortype, $element) {

        // Getting Mink selector and locator.
        list($selector, $locator) = $this->transform_selector($selectortype, $element);

        // Returns the NodeElement.
        return $this->find($selector, $locator);
    }

    /**
     * Gets a NodeElement based on the locator and selector type received as argument from steps definitions.
     *
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $selectortype
     * @param string $element
     * @return NodeElement
     */
    protected function get_text_selector_node($selectortype, $element) {

        // Getting Mink selector and locator.
        list($selector, $locator) = $this->transform_text_selector($selectortype, $element);

        // Returns the NodeElement.
        return $this->find($selector, $locator);
    }

    /**
     * Gets the requested element inside the specified container.
     *
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param mixed $selectortype The element selector type.
     * @param mixed $element The element locator.
     * @param mixed $containerselectortype The container selector type.
     * @param mixed $containerelement The container locator.
     * @return NodeElement
     */
    protected function get_node_in_container($selectortype, $element, $containerselectortype, $containerelement) {

        // Gets the container, it will always be text based.
        $containernode = $this->get_text_selector_node($containerselectortype, $containerelement);

        list($selector, $locator) = $this->transform_selector($selectortype, $element);

        // Specific exception giving info about where can't we find the element.
        $locatorexceptionmsg = $element . '" in the "' . $containerelement. '" "' . $containerselectortype. '"';
        $exception = new ElementNotFoundException($this->getSession(), $selectortype, null, $locatorexceptionmsg);

        // Looks for the requested node inside the container node.
        return $this->find($selector, $locator, $exception, $containernode);
    }

    /**
     * Transforms from step definition's argument style to Mink format.
     *
     * Mink has 3 different selectors css, xpath and named, where named
     * selectors includes link, button, field... to simplify and group multiple
     * steps in one we use the same interface, considering all link, buttons...
     * at the same level as css selectors and xpath; this method makes the
     * conversion from the arguments received by the steps to the selectors and locators
     * required to interact with Mink.
     *
     * @throws ExpectationException
     * @param string $selectortype It can be css, xpath or any of the named selectors.
     * @param string $element The locator (or string) we are looking for.
     * @return array Contains the selector and the locator expected by Mink.
     */
    protected function transform_selector($selectortype, $element) {

        // Here we don't know if an allowed text selector is being used.
        $selectors = behat_selectors::get_allowed_selectors();
        if (!isset($selectors[$selectortype])) {
            throw new ExpectationException('The "' . $selectortype . '" selector type does not exist', $this->getSession());
        }

        return behat_selectors::get_behat_selector($selectortype, $element, $this->getSession());
    }

    /**
     * Transforms from step definition's argument style to Mink format.
     *
     * Delegates all the process to behat_base::transform_selector() checking
     * the provided $selectortype.
     *
     * @throws ExpectationException
     * @param string $selectortype It can be css, xpath or any of the named selectors.
     * @param string $element The locator (or string) we are looking for.
     * @return array Contains the selector and the locator expected by Mink.
     */
    protected function transform_text_selector($selectortype, $element) {

        $selectors = behat_selectors::get_allowed_text_selectors();
        if (empty($selectors[$selectortype])) {
            throw new ExpectationException('The "' . $selectortype . '" selector can not be used to select text nodes', $this->getSession());
        }

        return $this->transform_selector($selectortype, $element);
    }

    /**
     * Returns whether the scenario is running in a browser that can run Javascript or not.
     *
     * @return boolean
     */
    protected function running_javascript() {
        return get_class($this->getSession()->getDriver()) !== 'Behat\Mink\Driver\GoutteDriver';
    }

    /**
     * Spins around an element until it exists
     *
     * @throws ExpectationException
     * @param string $element
     * @param string $selectortype
     * @return void
     */
    protected function ensure_element_exists($element, $selectortype) {

        // Getting the behat selector & locator.
        list($selector, $locator) = $this->transform_selector($selectortype, $element);

        // Exception if it timesout and the element is still there.
        $msg = 'The "' . $element . '" element does not exist and should exist';
        $exception = new ExpectationException($msg, $this->getSession());

        // It will stop spinning once the find() method returns true.
        $this->spin(
            function($context, $args) {
                // We don't use behat_base::find as it is already spinning.
                if ($context->getSession()->getPage()->find($args['selector'], $args['locator'])) {
                    return true;
                }
                return false;
            },
            array('selector' => $selector, 'locator' => $locator),
            self::EXTENDED_TIMEOUT,
            $exception,
            true
        );

    }

    /**
     * Spins until the element does not exist
     *
     * @throws ExpectationException
     * @param string $element
     * @param string $selectortype
     * @return void
     */
    protected function ensure_element_does_not_exist($element, $selectortype) {

        // Getting the behat selector & locator.
        list($selector, $locator) = $this->transform_selector($selectortype, $element);

        // Exception if it timesout and the element is still there.
        $msg = 'The "' . $element . '" element exists and should not exist';
        $exception = new ExpectationException($msg, $this->getSession());

        // It will stop spinning once the find() method returns false.
        $this->spin(
            function($context, $args) {
                // We don't use behat_base::find() as we are already spinning.
                if (!$context->getSession()->getPage()->find($args['selector'], $args['locator'])) {
                    return true;
                }
                return false;
            },
            array('selector' => $selector, 'locator' => $locator),
            self::EXTENDED_TIMEOUT,
            $exception,
            true
        );
    }

    /**
     * Ensures that the provided node is visible and we can interact with it.
     *
     * @throws ExpectationException
     * @param NodeElement $node
     * @return void Throws an exception if it times out without the element being visible
     */
    protected function ensure_node_is_visible($node) {

        if (!$this->running_javascript()) {
            return;
        }

        // Exception if it timesout and the element is still there.
        $msg = 'The "' . $node->getXPath() . '" xpath node is not visible and it should be visible';
        $exception = new ExpectationException($msg, $this->getSession());

        // It will stop spinning once the isVisible() method returns true.
        $this->spin(
            function($context, $args) {
                if ($args->isVisible()) {
                    return true;
                }
                return false;
            },
            $node,
            self::EXTENDED_TIMEOUT,
            $exception,
            true
        );
    }

    /**
     * Ensures that the provided element is visible and we can interact with it.
     *
     * Returns the node in case other actions are interested in using it.
     *
     * @throws ExpectationException
     * @param string $element
     * @param string $selectortype
     * @return NodeElement Throws an exception if it times out without being visible
     */
    protected function ensure_element_is_visible($element, $selectortype) {

        if (!$this->running_javascript()) {
            return;
        }

        $node = $this->get_selected_node($selectortype, $element);
        $this->ensure_node_is_visible($node);

        return $node;
    }

    /**
     * Ensures that all the page's editors are loaded.
     *
     * @deprecated since Moodle 2.7 MDL-44084 - please do not use this function any more.
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @return void
     */
    protected function ensure_editors_are_loaded() {
        global $CFG;

        if (empty($CFG->behat_usedeprecated)) {
            debugging('Function behat_base::ensure_editors_are_loaded() is deprecated. It is no longer required.');
        }
        return;
    }

    /**
     * Change browser window size.
     *   - small: 640x480
     *   - medium: 1024x768
     *   - large: 2560x1600
     *
     * @param string $windowsize size of window.
     * @throws ExpectationException
     */
    protected function resize_window($windowsize) {
        // Non JS don't support resize window.
        if (!$this->running_javascript()) {
            return;
        }

        switch ($windowsize) {
            case "small":
                $width = 640;
                $height = 480;
                break;
            case "medium":
                $width = 1024;
                $height = 768;
                break;
            case "large":
                $width = 2560;
                $height = 1600;
                break;
            default:
                preg_match('/^(\d+x\d+)$/', $windowsize, $matches);
                if (empty($matches) || (count($matches) != 2)) {
                    throw new ExpectationException("Invalid screen size, can't resize", $this->getSession());
                }
                $size = explode('x', $windowsize);
                $width = (int) $size[0];
                $height = (int) $size[1];
        }
        $this->getSession()->getDriver()->resizeWindow($width, $height);
    }

    /**
     * Waits for all the JS to be loaded.
     *
     * @throws \Exception
     * @throws NoSuchWindow
     * @throws UnknownError
     * @return bool True or false depending whether all the JS is loaded or not.
     */
    public function wait_for_pending_js() {
        // Waiting for JS is only valid for JS scenarios.
        if (!$this->running_javascript()) {
            return;
        }
        // We don't use behat_base::spin() here as we don't want to end up with an exception
        // if the page & JSs don't finish loading properly.
        for ($i = 0; $i < self::EXTENDED_TIMEOUT * 10; $i++) {
            $pending = '';
            try {
                $jscode = '
                    return function() {
                        if (typeof M === "undefined") {
                            if (document.readyState === "complete") {
                                return "";
                            } else {
                                return "incomplete";
                            }
                        } else if (' . self::PAGE_READY_JS . ') {
                            return "";
                        } else if (typeof M.util !== "undefined") {
                            return M.util.pending_js.join(":");
                        } else {
                            return "incomplete";
                        }
                    }();';
                $pending = $this->getSession()->evaluateScript($jscode);
            } catch (NoSuchWindow $nsw) {
                // We catch an exception here, in case we just closed the window we were interacting with.
                // No javascript is running if there is no window right?
                $pending = '';
            } catch (UnknownError $e) {
                // M is not defined when the window or the frame don't exist anymore.
                if (strstr($e->getMessage(), 'M is not defined') != false) {
                    $pending = '';
                }
            }
            // If there are no pending JS we stop waiting.
            if ($pending === '') {
                return true;
            }
            // 0.1 seconds.
            usleep(100000);
        }
        // Timeout waiting for JS to complete. It will be catched and forwarded to behat_hooks::i_look_for_exceptions().
        // It is unlikely that Javascript code of a page or an AJAX request needs more than self::EXTENDED_TIMEOUT seconds
        // to be loaded, although when pages contains Javascript errors M.util.js_complete() can not be executed, so the
        // number of JS pending code and JS completed code will not match and we will reach this point.
        throw new \Exception('Javascript code and/or AJAX requests are not ready after ' . self::EXTENDED_TIMEOUT .
            ' seconds. There is a Javascript error or the code is extremely slow.');
    }
    /**
     * Internal step definition to find exceptions, debugging() messages and PHP debug messages.
     *
     * Part of behat_hooks class as is part of the testing framework, is auto-executed
     * after each step so no features will splicitly use it.
     *
     * @throws Exception Unknown type, depending on what we caught in the hook or basic \Exception.
     * @see Moodle\BehatExtension\Tester\MoodleStepTester
     */
    public function look_for_exceptions() {
        // Wrap in try in case we were interacting with a closed window.
        try {
            // Exceptions.
            $exceptionsxpath = "//div[@data-rel='fatalerror']";
            // Debugging messages.
            $debuggingxpath = "//div[@data-rel='debugging']";
            // PHP debug messages.
            $phperrorxpath = "//div[@data-rel='phpdebugmessage']";
            // Any other backtrace.
            $othersxpath = "(//*[contains(., ': call to ')])[1]";
            $xpaths = array($exceptionsxpath, $debuggingxpath, $phperrorxpath, $othersxpath);
            $joinedxpath = implode(' | ', $xpaths);
            // Joined xpath expression. Most of the time there will be no exceptions, so this pre-check
            // is faster than to send the 4 xpath queries for each step.
            if (!$this->getSession()->getDriver()->find($joinedxpath)) {
                return;
            }
            // Exceptions.
            if ($errormsg = $this->getSession()->getPage()->find('xpath', $exceptionsxpath)) {
                // Getting the debugging info and the backtrace.
                $errorinfoboxes = $this->getSession()->getPage()->findAll('css', 'div.alert-error');
                // If errorinfoboxes is empty, try find notifytiny (original) class.
                if (empty($errorinfoboxes)) {
                    $errorinfoboxes = $this->getSession()->getPage()->findAll('css', 'div.notifytiny');
                }
                $errorinfo = $this->get_debug_text($errorinfoboxes[0]->getHtml()) . "\n" .
                    $this->get_debug_text($errorinfoboxes[1]->getHtml());
                $msg = "Moodle exception: " . $errormsg->getText() . "\n" . $errorinfo;
                throw new \Exception(html_entity_decode($msg));
            }
            // Debugging messages.
            if ($debuggingmessages = $this->getSession()->getPage()->findAll('xpath', $debuggingxpath)) {
                $msgs = array();
                foreach ($debuggingmessages as $debuggingmessage) {
                    $msgs[] = $this->get_debug_text($debuggingmessage->getHtml());
                }
                $msg = "debugging() message/s found:\n" . implode("\n", $msgs);
                throw new \Exception(html_entity_decode($msg));
            }
            // PHP debug messages.
            if ($phpmessages = $this->getSession()->getPage()->findAll('xpath', $phperrorxpath)) {
                $msgs = array();
                foreach ($phpmessages as $phpmessage) {
                    $msgs[] = $this->get_debug_text($phpmessage->getHtml());
                }
                $msg = "PHP debug message/s found:\n" . implode("\n", $msgs);
                throw new \Exception(html_entity_decode($msg));
            }
            // Any other backtrace.
            // First looking through xpath as it is faster than get and parse the whole page contents,
            // we get the contents and look for matches once we found something to suspect that there is a backtrace.
            if ($this->getSession()->getDriver()->find($othersxpath)) {
                $backtracespattern = '/(line [0-9]* of [^:]*: call to [\->&;:a-zA-Z_\x7f-\xff][\->&;:a-zA-Z0-9_\x7f-\xff]*)/';
                if (preg_match_all($backtracespattern, $this->getSession()->getPage()->getContent(), $backtraces)) {
                    $msgs = array();
                    foreach ($backtraces[0] as $backtrace) {
                        $msgs[] = $backtrace . '()';
                    }
                    $msg = "Other backtraces found:\n" . implode("\n", $msgs);
                    throw new \Exception(htmlentities($msg));
                }
            }
        } catch (NoSuchWindow $e) {
            // If we were interacting with a popup window it will not exists after closing it.
        }
    }
    /**
     * Converts HTML tags to line breaks to display the info in CLI
     *
     * @param string $html
     * @return string
     */
    protected function get_debug_text($html) {

        // Replacing HTML tags for new lines and keeping only the text.
        $notags = preg_replace('/<+\s*\/*\s*([A-Z][A-Z0-9]*)\b[^>]*\/*\s*>*/i', "\n", $html);
        return preg_replace("/(\n)+/s", "\n", $notags);
    }

    /**
     * Helper function to execute api in a given context.
     *
     * @param string $contextapi context in which api is defined.
     * @param array $params list of params to pass.
     * @throws Exception
     */
    protected function execute($contextapi, $params = array()) {
        if (!is_array($params)) {
            $params = array($params);
        }

        // Get required context and execute the api.
        $contextapi = explode("::", $contextapi);
        $context = behat_context_helper::get($contextapi[0]);
        call_user_func_array(array($context, $contextapi[1]), $params);

        // NOTE: Wait for pending js and look for exception are not optional, as this might lead to unexpected results.
        // So don't make them optional for performance reasons.

        // Wait for pending js.
        $this->wait_for_pending_js();

        // Look for exceptions.
        $this->look_for_exceptions();
    }
}
