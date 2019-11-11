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
 * Helper to get behat contexts from other contexts.
 *
 * @package    core
 * @category   test
 * @copyright  2014 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Testwork\Environment\Environment;

/**
 * Helper to get behat contexts.
 *
 * @package    core
 * @category   test
 * @copyright  2014 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_context_helper {

    /**
     * Behat environment.
     *
     * @var Environment
     */
    protected static $environment = null;

    /**
     * @var Escaper::escapeLiteral
     */
    protected static $escaper;

    /**
     * @var array keep track of nonexisting contexts, to avoid exception tracking.
     */
    protected static $nonexistingcontexts = array();

    /**
     * Sets the browser session.
     *
     * @param Environment $environment
     * @return void
     * @deprecated since 3.2 MDL-55072 - please use behat_context_helper::set_environment()
     * @todo MDL-55365 This will be deleted in Moodle 3.6.
     */
    public static function set_session(Environment $environment) {
        debugging('set_session is deprecated. Please use set_environment instead.', DEBUG_DEVELOPER);

        self::set_environment($environment);
    }

    /**
     * Sets behat environment.
     *
     * @param Environment $environment
     * @return void
     */
    public static function set_environment(Environment $environment) {
        self::$environment = $environment;
    }

    /**
     * Gets the required context.
     *
     * Getting a context you get access to all the steps
     * that uses direct API calls; steps returning step chains
     * can not be executed like this.
     *
     * @throws Behat\Behat\Context\Exception\ContextNotFoundException
     * @param string $classname Context identifier (the class name).
     * @return behat_base
     */
    public static function get($classname) {
        if (self::$environment->hasContextClass($classname)) {
            return self::$environment->getContext($classname);
        }

        $suitename = self::$environment->getSuite()->getName();
        // If default suite, then get the default theme name.
        if ($suitename == 'default') {
            $suitename = theme_config::DEFAULT_THEME;
        }
        $overridencontextname = 'behat_theme_'.$suitename.'_'.$classname;

        // If contexts has not been checked before and doesn't exist then just use core one.
        if (!isset(self::$nonexistingcontexts[$overridencontextname])) {
            try {
                $subcontext = self::$environment->getContext($overridencontextname);

                return $subcontext;
            } catch (Behat\Behat\Context\Exception\ContextNotFoundException $e) {
                // If context not found then it's not overridden.
                self::$nonexistingcontexts[$overridencontextname] = 1;
            }
        }

        // Get the actual context.
        return self::$environment->getContext($classname);
    }

    /**
     * Return whether there is a context of the specified classname.
     *
     * @param string $classname
     * @return bool
     */
    public static function has_context(string $classname): bool {
        return self::$environment->hasContextClass($classname);
    }

    /**
     * Translates string to XPath literal.
     *
     * @param string $label label to escape
     * @return string escaped string.
     */
    public static function escape($label) {
        if (empty(self::$escaper)) {
            self::$escaper = new \Behat\Mink\Selector\Xpath\Escaper();
        }
        return self::$escaper->escapeLiteral($label);
    }
}
