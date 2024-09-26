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
use Behat\Mink\Exception\DriverException;

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
        $definedclassname = self::get_theme_override($classname);
        if ($definedclassname) {
            return self::$environment->getContext($definedclassname);
        }

        // Just fall back on getContext to ensure that we throw the correct exception.
        return self::$environment->getContext($classname);
    }

    /**
     * Get the context for the specified component or subsystem.
     *
     * @param string $component The component or subsystem to find the context for
     * @return behat_base|null
     */
    public static function get_component_context(string $component): ?behat_base {
        $component = str_replace('core_', '', $component);

        if ($classname = self::get_theme_override("behat_{$component}")) {
            return self::get($classname);
        }

        return null;
    }

    /**
     * Find all Behat contexts which match the specified context class name prefix.
     *
     * Moodle uses a consistent class naming scheme for all Behat contexts, whereby the context name is in the format:
     *
     *     behat_{component}
     *
     * This method will return all contexts which match the specified prefix.
     *
     * For example, to find all editors, you would pass in 'behat_editor', and this might return:
     * - behat_editor_tiny
     * - behat_editor_textarea
     *
     * @param string $prefix The prefix to search for
     * @return \Behat\Behat\Context\Context[]
     */
    public static function get_prefixed_contexts(string $prefix): array {
        if (!is_a(self::$environment, \Behat\Behat\Context\Environment\InitializedContextEnvironment::class)) {
            throw new DriverException(
                'Cannot get prefixed contexts - the environment is not an InitializedContextEnvironment'
            );
        }

        return array_filter(self::$environment->getContexts(), function($context) use ($prefix): bool {
            return (strpos(get_class($context), $prefix) === 0);
        });
    }

    /**
     * Check for any theme override of the specified class name.
     *
     * @param string $classname
     * @return string|null
     */
    protected static function get_theme_override(string $classname): ?string {
        $suitename = self::$environment->getSuite()->getName();
        // If default suite, then get the default theme name.
        if ($suitename == 'default') {
            $suitename = theme_config::DEFAULT_THEME;
        }

        $overrideclassname = "behat_theme_{$suitename}_{$classname}";
        if (self::$environment->hasContextClass($overrideclassname)) {
            return $overrideclassname;
        }

        try {
            $themeconfig = theme_config::load($suitename);
        } catch (Exception $e) {
            // This theme has no theme config.
            return null;
        }

        // The theme will use all core contexts, except the one overridden by theme or its parent.
        if (isset($themeconfig->parents)) {
            foreach ($themeconfig->parents as $parent) {
                $overrideclassname = "behat_theme_{$parent}_{$classname}";
                if (self::$environment->hasContextClass($overrideclassname)) {
                    return $overrideclassname;
                }
            }
        }

        if (self::$environment->hasContextClass($classname)) {
            return $classname;
        }

        return null;
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
