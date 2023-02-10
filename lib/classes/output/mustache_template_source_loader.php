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
 * Load template source strings.
 *
 * @package    core
 * @category   output
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

defined('MOODLE_INTERNAL') || die();

use \Mustache_Tokenizer;

/**
 * Load template source strings.
 *
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mustache_template_source_loader {

    /** @var $gettemplatesource Callback function to load the template source from full name */
    private $gettemplatesource = null;

    /**
     * Constructor that takes a callback to allow the calling code to specify how to retrieve
     * the source for a template name.
     *
     * If no callback is provided then default to the load from disk implementation.
     *
     * @param callable|null $gettemplatesource Callback to load template source by template name
     */
    public function __construct(callable $gettemplatesource = null) {
        if ($gettemplatesource) {
            // The calling code has specified a function for retrieving the template source
            // code by name and theme.
            $this->gettemplatesource = $gettemplatesource;
        } else {
            // By default we will pull the template from disk.
            $this->gettemplatesource = function($component, $name, $themename) {
                $fulltemplatename = $component . '/' . $name;
                $filename = mustache_template_finder::get_template_filepath($fulltemplatename, $themename);
                return file_get_contents($filename);
            };
        }
    }

    /**
     * Remove comments from mustache template.
     *
     * @param string $templatestr
     * @return string
     */
    protected function strip_template_comments($templatestr) : string {
        return preg_replace('/(?={{!)(.*)(}})/sU', '', $templatestr);
    }

    /**
     * Load the template source from the component and template name.
     *
     * @param string $component The moodle component (e.g. core_message)
     * @param string $name The template name (e.g. message_drawer)
     * @param string $themename The theme to load the template for (e.g. boost)
     * @param bool $includecomments If the comments should be stripped from the source before returning
     * @return string The template source
     */
    public function load(
        string $component,
        string $name,
        string $themename,
        bool $includecomments = false
    ) : string {
        global $CFG;
        // Get the template source from the callback.
        $source = ($this->gettemplatesource)($component, $name, $themename);

        // Remove comments from template.
        if (!$includecomments) {
            $source = $this->strip_template_comments($source);
        }
        if (!empty($CFG->debugtemplateinfo)) {
            return "<!-- template(JS): $name -->" . $source . "<!-- /template(JS): $name -->";
        }
        return $source;
    }

    /**
     * Load a template and some of the dependencies that will be needed in order to render
     * the template.
     *
     * The current implementation will return all of the templates and all of the strings in
     * each of those templates (excluding string substitutions).
     *
     * The return format is an array indexed with the dependency type (e.g. templates / strings) then
     * the component (e.g. core_message), and then the id (e.g. message_drawer).
     *
     * For example:
     * * We have 3 templates in core named foo, bar, and baz.
     * * foo includes bar and bar includes baz.
     * * foo uses the string 'home' from core
     * * baz uses the string 'help' from core
     *
     * If we load the template foo this function would return:
     * [
     *      'templates' => [
     *          'core' => [
     *              'foo' => '... template source ...',
     *              'bar' => '... template source ...',
     *              'baz' => '... template source ...',
     *          ]
     *      ],
     *      'strings' => [
     *          'core' => [
     *              'home' => 'Home',
     *              'help' => 'Help'
     *          ]
     *      ]
     * ]
     *
     * @param string $templatecomponent The moodle component (e.g. core_message)
     * @param string $templatename The template name (e.g. message_drawer)
     * @param string $themename The theme to load the template for (e.g. boost)
     * @param bool $includecomments If the comments should be stripped from the source before returning
     * @param array $seentemplates List of templates already processed / to be skipped.
     * @param array $seenstrings List of strings already processed / to be skipped.
     * @param string|null $lang moodle translation language, null means use current.
     * @return array
     */
    public function load_with_dependencies(
        string $templatecomponent,
        string $templatename,
        string $themename,
        bool $includecomments = false,
        array $seentemplates = [],
        array $seenstrings = [],
        string $lang = null
    ) : array {
        // Initialise the return values.
        $templates = [];
        $strings = [];
        $templatecomponent = trim($templatecomponent);
        $templatename = trim($templatename);
        // Get the requested template source.
        $templatesource = $this->load($templatecomponent, $templatename, $themename, $includecomments);
        // This is a helper function to save a value in one of the result arrays (either $templates or $strings).
        $save = function(array $results, array $seenlist, string $component, string $id, $value) use ($lang) {
            if (!isset($results[$component])) {
                // If the results list doesn't already contain this component then initialise it.
                $results[$component] = [];
            }

            // Save the value.
            $results[$component][$id] = $value;
            // Record that this item has been processed.
            array_push($seenlist, "$component/$id");
            // Return the updated results and seen list.
            return [$results, $seenlist];
        };
        // This is a helper function for processing a dependency. Does stuff like ignore duplicate processing,
        // common result formatting etc.
        $handler = function(array $dependency, array $ignorelist, callable $processcallback) use ($lang) {
            foreach ($dependency as $component => $ids) {
                foreach ($ids as $id) {
                    $dependencyid = "$component/$id";
                    if (array_search($dependencyid, $ignorelist) === false) {
                        $processcallback($component, $id);
                        // Add this to our ignore list now that we've processed it so that we don't
                        // process it again.
                        array_push($ignorelist, $dependencyid);
                    }
                }
            }

            return $ignorelist;
        };

        // Save this template as the first result in the $templates result array.
        list($templates, $seentemplates) = $save($templates, $seentemplates, $templatecomponent, $templatename, $templatesource);

        // Check the template for any dependencies that need to be loaded.
        $dependencies = $this->scan_template_source_for_dependencies($templatesource);

        // Load all of the lang strings that this template requires and add them to the
        // returned values.
        $seenstrings = $handler(
            $dependencies['strings'],
            $seenstrings,
            // Include $strings and $seenstrings by reference so that their values can be updated
            // outside of this anonymous function.
            function($component, $id) use ($save, &$strings, &$seenstrings, $lang) {
                $string = get_string_manager()->get_string($id, $component, null, $lang);
                // Save the string in the $strings results array.
                list($strings, $seenstrings) = $save($strings, $seenstrings, $component, $id, $string);
            }
        );

        // Load any child templates that we've found in this template and add them to
        // the return list of dependencies.
        $seentemplates = $handler(
            $dependencies['templates'],
            $seentemplates,
            // Include $strings, $seenstrings, $templates, and $seentemplates by reference so that their values can be updated
            // outside of this anonymous function.
            function($component, $id) use (
                $themename,
                $includecomments,
                &$seentemplates,
                &$seenstrings,
                &$templates,
                &$strings,
                $save,
                $lang
            ) {
                // We haven't seen this template yet so load it and it's dependencies.
                $subdependencies = $this->load_with_dependencies(
                    $component,
                    $id,
                    $themename,
                    $includecomments,
                    $seentemplates,
                    $seenstrings,
                    $lang
                );

                foreach ($subdependencies['templates'] as $component => $ids) {
                    foreach ($ids as $id => $value) {
                        // Include the child themes in our results.
                        list($templates, $seentemplates) = $save($templates, $seentemplates, $component, $id, $value);
                    }
                };

                foreach ($subdependencies['strings'] as $component => $ids) {
                    foreach ($ids as $id => $value) {
                        // Include any strings that the child templates need in our results.
                        list($strings, $seenstrings) = $save($strings, $seenstrings, $component, $id, $value);
                    }
                }
            }
        );

        return [
            'templates' => $templates,
            'strings' => $strings
        ];
    }

    /**
     * Scan over a template source string and return a list of dependencies it requires.
     * At the moment the list will only include other templates and strings.
     *
     * The return format is an array indexed with the dependency type (e.g. templates / strings) then
     * the component (e.g. core_message) with it's value being an array of the items required
     * in that component.
     *
     * For example:
     * If we have a template foo that includes 2 templates, bar and baz, and also 2 strings
     * 'home' and 'help' from the core component then the return value would look like:
     *
     * [
     *      'templates' => [
     *          'core' => ['foo', 'bar', 'baz']
     *      ],
     *      'strings' => [
     *          'core' => ['home', 'help']
     *      ]
     * ]
     *
     * @param string $source The template source
     * @return array
     */
    protected function scan_template_source_for_dependencies(string $source) : array {
        $tokenizer = new Mustache_Tokenizer();
        $tokens = $tokenizer->scan($source);
        $templates = [];
        $strings = [];
        $addtodependencies = function($dependencies, $component, $id) {
            $id = trim($id);
            $component = trim($component);

            if (!isset($dependencies[$component])) {
                // Initialise the component if we haven't seen it before.
                $dependencies[$component] = [];
            }

            // Add this id to the list of dependencies.
            array_push($dependencies[$component], $id);

            return $dependencies;
        };

        foreach ($tokens as $index => $token) {
            $type = $token['type'];
            $name = isset($token['name']) ? $token['name'] : null;

            if ($name) {
                switch ($type) {
                    case Mustache_Tokenizer::T_PARTIAL:
                        list($component, $id) = explode('/', $name, 2);
                        $templates = $addtodependencies($templates, $component, $id);
                        break;
                    case Mustache_Tokenizer::T_PARENT:
                        list($component, $id) = explode('/', $name, 2);
                        $templates = $addtodependencies($templates, $component, $id);
                        break;
                    case Mustache_Tokenizer::T_SECTION:
                        if ($name == 'str') {
                            list($id, $component) = $this->get_string_identifiers($tokens, $index);

                            if ($id) {
                                $strings = $addtodependencies($strings, $component, $id);
                            }
                        }
                        break;
                }
            }
        }

        return [
            'templates' => $templates,
            'strings' => $strings
        ];
    }

    /**
     * Gets the identifier and component of the string.
     *
     * The string could be defined on one, or multiple lines.
     *
     * @param array $tokens The templates token.
     * @param int $start The index of the start of the string token.
     * @return array A list of the string identifier and component.
     */
    protected function get_string_identifiers(array $tokens, int $start): array {
        $current = $start + 1;
        $parts = [];

        // Get the contents of the string tag.
        while ($tokens[$current]['type'] !== Mustache_Tokenizer::T_END_SECTION) {
            if (!isset($tokens[$current]['value']) || empty(trim($tokens[$current]['value']))) {
                // An empty line, so we should ignore it.
                $current++;
                continue;
            }

            // We need to remove any spaces before and after the string.
            $nospaces = trim($tokens[$current]['value']);

            // We need to remove any trailing commas so that the explode will not add an
            // empty entry where two paramters are on multiple lines.
            $clean = rtrim($nospaces, ',');

            // We separate the parts of a string with commas.
            $subparts = explode(',', $clean);

            // Store the parts.
            $parts = array_merge($parts, $subparts);

            $current++;
        }

        // The first text should be the first part of a str tag.
        $id = isset($parts[0]) ? trim($parts[0]) : null;

        // Default to 'core' for the component, if not specified.
        $component = isset($parts[1]) ? trim($parts[1]) : 'core';

        return [$id, $component];
    }
}
