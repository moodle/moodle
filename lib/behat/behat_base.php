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
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Steps definitions base class.
 *
 * To extend by the steps definitions of the different Moodle components.
 *
 * It can not contain steps definitions to avoid duplicates, only utility
 * methods shared between steps.
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_base extends Behat\MinkExtension\Context\RawMinkContext {

    /**
     * The timeout for each Behat step (load page, wait for an element to load...).
     */
    const TIMEOUT = 6;

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
     * @return NodeElement
     */
    protected function find($selector, $locator, $exception = false, $node = false) {

        // Returns the first match.
        $items = $this->find_all($selector, $locator, $exception, $node);
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
     * @return array NodeElements list
     */
    protected function find_all($selector, $locator, $exception = false, $node = false) {

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
            self::TIMEOUT,
            $exception
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
     * closure exception will be used, but you must provide an exception if the closure does not throws
     * an exception.
     *
     * @throws Exception            If it timeouts without receiving something != false from the closure
     * @param  Closure   $lambda    The function to execute.
     * @param  mixed     $args      Arguments to pass to the closure
     * @param  int       $timeout   Timeout
     * @param  Exception $exception The exception to throw in case it time outs.
     * @return mixed The value returned by the closure
     */
    protected function spin($lambda, $args = false, $timeout = false, $exception = false) {

        // Using default timeout which is pretty high.
        if (!$timeout) {
            $timeout = self::TIMEOUT;
        }

        for ($i = 0; $i < $timeout; $i++) {

            // We catch the exception thrown by the step definition to execute it again.
            try {

                // We don't check with !== because most of the time closures will return
                // direct Behat methods returns and we are not sure it will be always (bool)false
                // if it just runs the behat method without returning anything $return == null.
                if ($return = $lambda($this, $args)) {
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

            sleep(1);
        }

        // Using coding_exception as is a development issue if no exception has been provided.
        if (!$exception) {
            $exception = new coding_exception('spin method requires an exception if the closure doesn\'t throw an exception itself');
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

}
