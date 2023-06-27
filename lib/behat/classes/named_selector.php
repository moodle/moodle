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
 * Moodle-specific common functions for named selectors.
 *
 * @package    core
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Common functions for named selectors.
 *
 * This has to be a trait, because we need this in both the classes
 * behat_exact_named_selector and behat_partial_named_selector, and
 * those classes have to be subclasses of \Behat\Mink\Selector\ExactNamedSelector
 * and \Behat\Mink\Selector\PartialNamedSelector. This trait is a way achieve
 * that without duplciated code.
 *
 * @package    core
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait behat_named_selector {

    /**
     * Registers new XPath selector with specified name.
     *
     * @param string $component
     * @param behat_component_named_selector $selector
     */
    public function register_component_selector(string $component, behat_component_named_selector $selector) {
        $alias = $selector->get_alias($component);
        $name = $selector->get_name($component);
        static::$allowedselectors[$alias] = $name;

        if ($selector->is_text_selector()) {
            static::$allowedtextselectors[$alias] = $name;
        }

        // We must use Reflection here. The replacements property is private and cannot be accessed otherwise.
        // This is due to an API limitation in Mink.
        $rc = new \ReflectionClass(\Behat\Mink\Selector\NamedSelector::class);
        $r = $rc->getProperty('replacements');
        $r->setAccessible(true);
        $replacements = $r->getValue($this);

        $selectorxpath = strtr($selector->get_combined_xpath(), $replacements);

        parent::registerNamedXpath($name, $selectorxpath);
    }

    /**
     * Registers new XPath selector with specified name.
     *
     * @param string $component
     * @param behat_component_named_replacement $replacement
     */
    public function register_replacement(string $component, behat_component_named_replacement $replacement) {
        // We must use Reflection here. The replacements property is private and cannot be accessed otherwise.
        // This is due to an API limitation in Mink.
        $rc = new \ReflectionClass(\Behat\Mink\Selector\NamedSelector::class);
        $r = $rc->getProperty('replacements');
        $r->setAccessible(true);
        $existing = $r->getValue($this);

        $from = $replacement->get_from($component);

        if (isset($existing[$from])) {
            throw new \coding_exception("A named replacement already exists in the partial named selector for '{$from}'.  " .
                "Replacement names must be unique, and should be namespaced to the component");
        }

        $translatedto = strtr($replacement->get_to(), $existing);
        $this->registerReplacement($from, $translatedto);
    }

    /**
     * Check whether the specified selector has been deprecated and marked for replacement.
     *
     * @param string $selector
     * @return bool
     */
    public static function is_deprecated_selector(string $selector): bool {
        return array_key_exists($selector, static::$deprecatedselectors);
    }

    /**
     * Fetch the replacement name of a deprecated selector.
     *
     * @param string $selector
     * @return bool
     */
    public static function get_deprecated_replacement(string $selector): ?string {
        return static::$deprecatedselectors[$selector];
    }
}
