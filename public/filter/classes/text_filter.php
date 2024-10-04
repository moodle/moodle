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
use core\exception\coding_exception;
use moodle_page;

/**
 * Base class for text filters. You just need to override this class and
 * implement the filter method.
 *
 * @package core_filters
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class text_filter {
    /** @var context The context we are in. */
    protected $context;

    /** @var array Any local configuration for this filter in this context. */
    protected $localconfig;

    /**
     * Set any context-specific configuration for this filter.
     *
     * @param context $context The current context.
     * @param array $localconfig Any context-specific configuration for this filter.
     */
    public function __construct($context, array $localconfig) {
        $this->context = $context;
        $this->localconfig = $localconfig;
    }

    /**
     * Setup page with filter requirements and other prepare stuff.
     *
     * Override this method if the filter needs to setup page
     * requirements or needs other stuff to be executed.
     *
     * Note this method is invoked from {@see setup_page_for_filters()}
     * for each piece of text being filtered, so it is responsible
     * for controlling its own execution cardinality.
     *
     * @param moodle_page $page the page we are going to add requirements to.
     * @param context $context the context which contents are going to be filtered.
     * @since Moodle 2.3
     */
    public function setup($page, $context) {
        // Override me, if needed.
    }

    /**
     * Override this function to actually implement the filtering.
     *
     * Filter developers must make sure that filtering done after text cleaning
     * does not introduce security vulnerabilities.
     *
     * @param string $text some HTML content to process.
     * @param array $options options passed to the filters
     * @return string the HTML content after the filtering has been applied.
     */
    abstract public function filter($text, array $options = []);

    /**
     * Filter text before changing format to HTML.
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    public function filter_stage_pre_format(string $text, array $options): string {
        // NOTE: override if necessary.
        return $text;
    }

    /**
     * Filter HTML text before sanitising text.
     *
     * NOTE: this is called even if $options['noclean'] is true and text is not cleaned.
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    public function filter_stage_pre_clean(string $text, array $options): string {
        // NOTE: override if necessary.
        return $text;
    }

    /**
     * Filter HTML text at the very end after text is sanitised.
     *
     * NOTE: this is called even if $options['noclean'] is true and text is not cleaned.
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    public function filter_stage_post_clean(string $text, array $options): string {
        // NOTE: override if necessary.
        return $this->filter($text, $options);
    }

    /**
     * Filter simple text coming from format_string().
     *
     * Note that unless $CFG->formatstringstriptags is disabled
     * HTML tags are not expected in returned value.
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    public function filter_stage_string(string $text, array $options): string {
        // NOTE: override if necessary.
        return $this->filter($text, $options);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(text_filter::class, \moodle_text_filter::class);
