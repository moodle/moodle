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
 * Custom Moodle helper collection for mustache.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

/**
 * Custom Moodle helper collection for mustache.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mustache_helper_collection extends \Mustache_HelperCollection {

    /**
     * @var string[] Names of helpers that aren't allowed to be called within other helpers.
     */
    private $disallowednestedhelpers = [];

    /**
     * Helper Collection constructor.
     *
     * Optionally accepts an array (or Traversable) of `$name => $helper` pairs.
     *
     * @throws \Mustache_Exception_InvalidArgumentException if the $helpers argument isn't an array or Traversable
     *
     * @param array|\Traversable $helpers (default: null)
     * @param string[] $disallowednestedhelpers Names of helpers that aren't allowed to be called within other helpers.
     */
    public function __construct($helpers = null, array $disallowednestedhelpers = []) {
        $this->disallowednestedhelpers = $disallowednestedhelpers;
        parent::__construct($helpers);
    }

    /**
     * Add a helper to this collection.
     *
     * This function has overridden the parent implementation to provide disallowing
     * functionality for certain helpers to prevent them being called from within
     * other helpers. This is because the JavaScript helper can be used in a
     * security exploit if it can be nested.
     *
     * The function will wrap callable helpers in an anonymous function that strips
     * out the disallowed helpers from the source string before giving it to the
     * helper function. This prevents the disallowed helper functions from being
     * called by nested render functions from within other helpers.
     *
     * @see \Mustache_HelperCollection::add()
     * @param string $name
     * @param mixed  $helper
     */
    public function add($name, $helper) {

        $disallowedlist = $this->disallowednestedhelpers;

        if (is_callable($helper) && !empty($disallowedlist)) {
            $helper = function($source, \Mustache_LambdaHelper $lambdahelper) use ($helper, $disallowedlist) {

                // Temporarily override the disallowed helpers to return nothing
                // so that they can't be executed from within other helpers.
                $disabledhelpers = $this->disable_helpers($disallowedlist);
                // Call the original function with the modified sources.
                $result = call_user_func($helper, $source, $lambdahelper);
                // Restore the original disallowed helper implementations now
                // that this helper has finished executing so that the rest of
                // the rendering process continues to work correctly.
                $this->restore_helpers($disabledhelpers);
                // Lastly parse the returned string to strip out any unwanted helper
                // tags that were added through variable substitution (or other means).
                // This is done because a secondary render is called on the result
                // of a helper function if it still includes mustache tags. See
                // the section function of Mustache_Compiler for details.
                return $this->strip_disallowed_helpers($disallowedlist, $result);
            };
        }

        parent::add($name, $helper);
    }

    /**
     * Disable a list of helpers (by name) by changing their implementation to
     * simply return an empty string.
     *
     * @param  string[] $names List of helper names to disable
     * @return \Closure[] The original helper functions indexed by name
     */
    private function disable_helpers($names) {
        $disabledhelpers = [];

        foreach ($names as $name) {
            if ($this->has($name)) {
                $function = $this->get($name);
                // Null out the helper. Must call parent::add here to avoid
                // a recursion problem.
                parent::add($name, function() {
                    return '';
                });

                $disabledhelpers[$name] = $function;
            }
        }

        return $disabledhelpers;
    }

    /**
     * Restore the original helper implementations. Typically used after disabling
     * a helper.
     *
     * @param  \Closure[] $helpers The helper functions indexed by name
     */
    private function restore_helpers($helpers) {
        foreach ($helpers as $name => $function) {
            // Restore the helper functions. Must call parent::add here to avoid
            // a recursion problem.
            parent::add($name, $function);
        }
    }

    /**
     * Parse the given string and remove any reference to disallowed helpers.
     *
     * E.g.
     * $disallowedlist = ['js'];
     * $string = "core, move, {{#js}} some nasty JS hack {{/js}}"
     * result: "core, move, {{}}"
     *
     * @param  string[] $disallowedlist List of helper names to strip
     * @param  string $string String to parse
     * @return string Parsed string
     */
    public function strip_disallowed_helpers($disallowedlist, $string) {
        $starttoken = \Mustache_Tokenizer::T_SECTION;
        $endtoken = \Mustache_Tokenizer::T_END_SECTION;
        if ($endtoken == '/') {
            $endtoken = '\/';
        }

        $regexes = array_map(function($name) use ($starttoken, $endtoken) {
            // We only strip out the name of the helper (excluding delimiters)
            // the user is able to change the delimeters on a per template
            // basis so they may not be curly braces.
            return '/\s*' . $starttoken . '\s*'. $name . '\W+.*' . $endtoken . '\s*' . $name . '\s*/';
        }, $disallowedlist);

        // This will strip out unwanted helpers from the $source string
        // before providing it to the original helper function.
        // E.g.
        // Before:
        // "core, move, {{#js}} some nasty JS hack {{/js}}"
        // After:
        // "core, move, {{}}".
        return preg_replace_callback($regexes, function() {
            return '';
        }, $string);
    }

    /**
     * @deprecated Deprecated since Moodle 3.10 (MDL-69050) - use {@see strip_disallowed_helpers}
     */
    public function strip_blacklisted_helpers() {
        throw new \coding_exception('\core\output\mustache_helper_collection::strip_blacklisted_helpers() has been removed.');
    }
}
