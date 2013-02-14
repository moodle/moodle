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
    protected function locatePath($path) {
        $startUrl = rtrim($this->getMinkParameter('base_url'), '/') . '/';
        return 0 !== strpos($path, 'http') ? $startUrl . ltrim($path, '/') : $path;
    }

    /**
     * Adapter to Behat\Mink\Element\Element::find() using the spin() method.
     *
     * @link http://mink.behat.org/#traverse-the-page-selectors
     * @param Exception $exception Otherwise we throw expcetion with generic info
     * @param string $selector The selector type (css, xpath, named...)
     * @param mixed $locator It depends on the $selector, can be the xpath, a name, a css locator...
     * @return NodeElement
     */
    protected function find($selector, $locator, $exception = false) {

        // Generic info.
        if (!$exception) {

            // With named selectors we can be more specific.
            if ($selector == 'named') {
                $exceptiontype = $locator[0];
                $exceptionlocator = $locator[1];
            } else {
                $exceptiontype = $selector;
                $exceptionlocator = $locator;
            }

            $exception = new ElementNotFoundException($this->getSession(), $exceptiontype, null, $exceptionlocator);
        }

        // Waits for the node to appear if it exists, otherwise will timeout and throw the provided exception.
        return $this->spin(
            function($context, $args) {
                return $context->getSession()->getPage()->find($args[0], $args[1]);
            },
            array($selector, $locator),
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
     * @param string $method The name of the called method
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
            } catch(Exception $e) {

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

}
