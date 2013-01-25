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
     * Returns fixed step argument (with \\" replaced back to ").
     *
     * \\ is the chars combination to add when you
     * want to escape the " character that is used as var
     * delimiter.
     *
     * @see Behat\MinkExtension\Context\MinkContext
     * @param string $argument
     * @return string
     */
    protected function fixStepArgument($argument) {
        return str_replace('\\"', '"', $argument);
    }

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
     * Requires the exception to provide more accurate feedback to tests writers.
     *
     * @throws Exception If it timeouts without receiving something != false from the closure
     * @param Closure $lambda The function to execute.
     * @param Exception $exception The exception to throw in case it time outs.
     * @param array $args Arguments to pass to the closure
     * @return mixed The value returned by the closure
     */
    protected function spin($lambda, $exception, $args, $timeout = false) {

        // Using default timeout which is pretty high.
        if (!$timeout) {
            $timeout = self::TIMEOUT;
        }

        for ($i = 0; $i < $timeout; $i++) {

            // We catch the exception thrown by the step definition to execute it again.
            try {

                // We don't check with !== because most of the time closures will return
                // direct Behat methods returns and we are not sure it will be always (bool)false.
                if ($return = $lambda($this, $args)) {
                    return $return;
                }
            } catch(Exception $e) {
                // We wait until no exception is thrown or timeout expires.
                continue;
            }

            sleep(1);
        }

        // Throwing exception to the user.
        throw $exception;
    }

}
