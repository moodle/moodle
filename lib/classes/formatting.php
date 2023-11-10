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

namespace core;

/**
 * Content formatting methods for Moodle.
 *
 * @package   core
 * @copyright 2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class formatting {
    /**
     * Given a simple string, this function returns the string
     * processed by enabled string filters if $CFG->filterall is enabled
     *
     * This function should be used to print short strings (non html) that
     * need filter processing e.g. activity titles, post subjects,
     * glossary concepts.
     *
     * @staticvar bool $strcache
     * @param string $string The string to be filtered. Should be plain text, expect
     * possibly for multilang tags.
     * @param boolean $striplinks To strip any link in the result text. Moodle 1.8 default changed from false to true! MDL-8713
     * @param array $options options array/object or courseid
     * @return string
     */
    public function format_string(
        $string,
        $striplinks = true,
        $options = null,
    ): string {
        global $CFG, $PAGE;

        if ($string === '' || is_null($string)) {
            // No need to do any filters and cleaning.
            return '';
        }

        // We'll use a in-memory cache here to speed up repeated strings.
        static $strcache = false;

        if (empty($CFG->version) or $CFG->version < 2013051400 or during_initial_install()) {
            // Do not filter anything during installation or before upgrade completes.
            return $string = strip_tags($string);
        }

        if ($strcache === false or count($strcache) > 2000) {
            // This number might need some tuning to limit memory usage in cron.
            $strcache = array();
        }

        if (is_numeric($options)) {
            // Legacy courseid usage.
            $options  = array('context' => context_course::instance($options));
        } else {
            // Detach object, we can not modify it.
            $options = (array)$options;
        }

        if (empty($options['context'])) {
            // Fallback to $PAGE->context this may be problematic in CLI and other non-standard pages :-(.
            $options['context'] = $PAGE->context;
        } else if (is_numeric($options['context'])) {
            $options['context'] = context::instance_by_id($options['context']);
        }
        if (!isset($options['filter'])) {
            $options['filter'] = true;
        }

        $options['escape'] = !isset($options['escape']) || $options['escape'];

        if (!$options['context']) {
            // We did not find any context? weird.
            return $string = strip_tags($string);
        }

        // Calculate md5.
        $cachekeys = array(
            $string, $striplinks, $options['context']->id,
            $options['escape'], current_language(), $options['filter']
        );
        $md5 = md5(implode('<+>', $cachekeys));

        // Fetch from cache if possible.
        if (isset($strcache[$md5])) {
            return $strcache[$md5];
        }

        // First replace all ampersands not followed by html entity code
        // Regular expression moved to its own method for easier unit testing.
        $string = $options['escape'] ? replace_ampersands_not_followed_by_entity($string) : $string;

        if (!empty($CFG->filterall) && $options['filter']) {
            $filtermanager = \filter_manager::instance();
            $filtermanager->setup_page_for_filters($PAGE, $options['context']); // Setup global stuff filters may have.
            $string = $filtermanager->filter_string($string, $options['context']);
        }

        // If the site requires it, strip ALL tags from this string.
        if (!empty($CFG->formatstringstriptags)) {
            if ($options['escape']) {
                $string = str_replace(array('<', '>'), array('&lt;', '&gt;'), strip_tags($string));
            } else {
                $string = strip_tags($string);
            }
        } else {
            // Otherwise strip just links if that is required (default).
            if ($striplinks) {
                // Strip links in string.
                $string = strip_links($string);
            }
            $string = clean_text($string);
        }

        // Store to cache.
        $strcache[$md5] = $string;

        return $string;
    }
}
