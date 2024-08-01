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

namespace core_filters;

use core\context;
use core\context\system as context_system;
use moodle_page;

/**
 * Class to manage the filtering of strings. It is intended that this class is
 * only used by weblib.php. Client code should probably be using the
 * format_text and format_string functions.
 *
 * This class is a singleton.
 *
 * @package core_filters
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_manager {
    /**
     * @var text_filter[][] This list of active filters, by context, for filtering content.
     * An array contextid => ordered array of filter name => filter objects.
     */
    protected $textfilters = [];

    /**
     * @var text_filter[][] This list of active filters, by context, for filtering strings.
     * An array contextid => ordered array of filter name => filter objects.
     */
    protected $stringfilters = [];

    /** @var array Exploded version of $CFG->stringfilters. */
    protected $stringfilternames = [];

    /** @var filter_manager Holds the singleton instance. */
    protected static $singletoninstance;

    /**
     * Constructor. Protected. Use {@see instance()} instead.
     */
    protected function __construct() {
        $this->stringfilternames = filter_get_string_filters();
    }

    /**
     * Factory method. Use this to get the filter manager.
     *
     * @return filter_manager the singleton instance.
     */
    public static function instance() {
        global $CFG;
        if (is_null(self::$singletoninstance)) {
            if (!empty($CFG->perfdebug) && $CFG->perfdebug > 7) {
                self::$singletoninstance = new performance_measuring_filter_manager();
            } else {
                self::$singletoninstance = new self();
            }
        }
        return self::$singletoninstance;
    }

    /**
     * Resets the caches, usually to be called between unit tests
     */
    public static function reset_caches() {
        if (self::$singletoninstance) {
            self::$singletoninstance->unload_all_filters();
        }
        self::$singletoninstance = null;
    }

    /**
     * Unloads all filters and other cached information
     */
    protected function unload_all_filters() {
        $this->textfilters = [];
        $this->stringfilters = [];
        $this->stringfilternames = [];
    }

    /**
     * Load all the filters required by this context.
     *
     * @param context $context the context.
     */
    protected function load_filters($context) {
        $filters = filter_get_active_in_context($context);
        $this->textfilters[$context->id] = [];
        $this->stringfilters[$context->id] = [];
        foreach ($filters as $filtername => $localconfig) {
            $filter = $this->make_filter_object($filtername, $context, $localconfig);
            if (is_null($filter)) {
                continue;
            }
            $this->textfilters[$context->id][$filtername] = $filter;
            if (in_array($filtername, $this->stringfilternames)) {
                $this->stringfilters[$context->id][$filtername] = $filter;
            }
        }
    }

    /**
     * Factory method for creating a filter.
     *
     * @param string $filtername The filter name, for example 'tex'.
     * @param context $context context object.
     * @param array $localconfig array of local configuration variables for this filter.
     * @return ?text_filter The filter, or null, if this type of filter is
     *      not recognised or could not be created.
     */
    protected function make_filter_object($filtername, $context, $localconfig) {
        global $CFG;

        $filterclass = "\\filter_{$filtername}\\text_filter";
        if (class_exists($filterclass)) {
            return new $filterclass($context, $localconfig);
        }

        $path = $CFG->dirroot . '/filter/' . $filtername . '/filter.php';
        if (!is_readable($path)) {
            return null;
        }
        include_once($path);

        $filterclassname = 'filter_' . $filtername;
        if (class_exists($filterclassname)) {
            debugging(
                "Inclusion of filters from 'filter/{$filtername}/filter.php' " .
                    "using the '{$filterclassname}' class naming has been deprecated. " .
                    "Please rename your class to {$filterclass} and move it to 'filter/{$filtername}/classes/text_filter.php'. " .
                    "See MDL-82427 for more information.",
                DEBUG_DEVELOPER,
            );
            return new $filterclassname($context, $localconfig);
        }

        return null;
    }

    /**
     * Apply a list of filters to some content.
     * @param string $text
     * @param text_filter[] $filterchain array filter name => filter object.
     * @param array $options options passed to the filters.
     * @param null|array $skipfilters of filter names. Any filters that should not be applied to this text.
     * @return string $text
     */
    protected function apply_filter_chain(
        $text,
        $filterchain,
        array $options = [],
        ?array $skipfilters = null
    ) {
        if (!isset($options['stage'])) {
            $filtermethod = 'filter';
        } else if (in_array($options['stage'], ['pre_format', 'pre_clean', 'post_clean', 'string'], true)) {
            $filtermethod = 'filter_stage_' . $options['stage'];
        } else {
            $filtermethod = 'filter';
            debugging('Invalid filter stage specified in options: ' . $options['stage'], DEBUG_DEVELOPER);
        }
        if ($text === null || $text === '') {
            // Nothing to filter.
            return '';
        }
        foreach ($filterchain as $filtername => $filter) {
            if ($skipfilters !== null && in_array($filtername, $skipfilters)) {
                continue;
            }
            $text = $filter->$filtermethod($text, $options);
        }
        return $text;
    }

    /**
     * Get all the filters that apply to a given context for calls to format_text.
     *
     * @param context $context
     * @return moodle_text_filter[] A text filter
     */
    protected function get_text_filters($context) {
        if (!isset($this->textfilters[$context->id])) {
            $this->load_filters($context);
        }
        return $this->textfilters[$context->id];
    }

    /**
     * Get all the filters that apply to a given context for calls to format_string.
     *
     * @param context $context the context.
     * @return moodle_text_filter[] A text filter
     */
    protected function get_string_filters($context) {
        if (!isset($this->stringfilters[$context->id])) {
            $this->load_filters($context);
        }
        return $this->stringfilters[$context->id];
    }

    /**
     * Filter some text
     *
     * @param string $text The text to filter
     * @param context $context the context.
     * @param array $options options passed to the filters
     * @param null|array $skipfilters of filter names. Any filters that should not be applied to this text.
     * @return string resulting text
     */
    public function filter_text(
        $text,
        $context,
        array $options = [],
        ?array $skipfilters = null
    ) {
        $text = $this->apply_filter_chain($text, $this->get_text_filters($context), $options, $skipfilters);
        if (!isset($options['stage']) || $options['stage'] === 'post_clean') {
            // Remove <nolink> tags for XHTML compatibility after the last filtering stage.
            $text = str_replace(['<nolink>', '</nolink>'], '', $text);
        }
        return $text;
    }

    /**
     * Filter a piece of string
     *
     * @param string $string The text to filter
     * @param context $context the context.
     * @return string resulting string
     */
    public function filter_string($string, $context) {
        return $this->apply_filter_chain($string, $this->get_string_filters($context), ['stage' => 'string']);
    }

    /**
     * Setup page with filters requirements and other prepare stuff.
     *
     * This method is used by {@see format_text()} and {@see format_string()}
     * in order to allow filters to setup any page requirement (js, css...)
     * or perform any action needed to get them prepared before filtering itself
     * happens by calling to each every active setup() method.
     *
     * Note it's executed for each piece of text filtered, so filter implementations
     * are responsible of controlling the cardinality of the executions that may
     * be different depending of the stuff to prepare.
     *
     * @param moodle_page $page the page we are going to add requirements to.
     * @param context $context the context which contents are going to be filtered.
     * @since Moodle 2.3
     */
    public function setup_page_for_filters($page, $context) {
        $filters = $this->get_text_filters($context);
        foreach ($filters as $filter) {
            $filter->setup($page, $context);
        }
    }

    /**
     * Setup the page for globally available filters.
     *
     * This helps setting up the page for filters which may be applied to
     * the page, even if they do not belong to the current context, or are
     * not yet visible because the content is lazily added (ajax). This method
     * always uses to the system context which determines the globally
     * available filters.
     *
     * This should only ever be called once per request.
     *
     * @param moodle_page $page The page.
     * @since Moodle 3.2
     */
    public function setup_page_for_globally_available_filters($page) {
        $context = context_system::instance();
        $filterdata = filter_get_globally_enabled_filters_with_config();
        foreach ($filterdata as $name => $config) {
            if (isset($this->textfilters[$context->id][$name])) {
                $filter = $this->textfilters[$context->id][$name];
            } else {
                $filter = $this->make_filter_object($name, $context, $config);
                if (is_null($filter)) {
                    continue;
                }
            }
            $filter->setup($page, $context);
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(filter_manager::class, \filter_manager::class);
