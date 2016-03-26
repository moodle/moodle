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
 * Document representation.
 *
 * @package    search_solr
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace search_solr;

defined('MOODLE_INTERNAL') || die();

/**
 * Respresents a document to index.
 *
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class document extends \core_search\document {

    /**
     * Formats the timestamp according to the search engine needs.
     *
     * @param int $timestamp
     * @return string
     */
    public static function format_time_for_engine($timestamp) {
        return gmdate(\search_solr\engine::DATE_FORMAT, $timestamp);
    }

    /**
     * Formats the timestamp according to the search engine needs.
     *
     * @param int $timestamp
     * @return string
     */
    public static function format_string_for_engine($string) {
        // 2^15 default. We could convert this to a setting as is possible to
        // change the max in solr.
        return \core_text::str_max_bytes($string, 32766);
    }

    /**
     * Returns a timestamp from the value stored in the search engine.
     *
     * @param string $time
     * @return int
     */
    public static function import_time_from_engine($time) {
        return strtotime($time);
    }

    /**
     * Overwritten to use markdown format as we use markdown for solr highlighting.
     *
     * @return int
     */
    protected function get_text_format() {
        return FORMAT_HTML;
    }

    /**
     * Formats a text string coming from the search engine.
     *
     * @param  string $text Text to format
     * @return string HTML text to be renderer
     */
    protected function format_text($text) {
        // Since we allow output for highlighting, we need to encode html entities.
        // This ensures plaintext html chars don't become valid html.
        $out = s($text);

        $startcount = 0;
        $endcount = 0;

        // Remove end/start pairs that span a few common seperation characters. Allows us to highlight phrases instead of words.
        $regex = '|'.engine::HIGHLIGHT_END.'([ .,-]{0,3})'.engine::HIGHLIGHT_START.'|';
        $out = preg_replace($regex, '$1', $out);

        // Now replace our start and end highlight markers.
        $out = str_replace(engine::HIGHLIGHT_START, '<span class="highlight">', $out, $startcount);
        $out = str_replace(engine::HIGHLIGHT_END, '</span>', $out, $endcount);

        // This makes sure any highlight tags are balanced, incase truncation or the highlight text contained our markers.
        while ($startcount > $endcount) {
            $out .= '</span>';
            $endcount++;
        }
        while ($startcount < $endcount) {
            $out = '<span class="highlight">' . $out;
            $endcount++;
        }

        return parent::format_text($out);
    }
}
