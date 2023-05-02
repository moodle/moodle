<?php
// This file is part of Moodle-oembed-Filter
//
// Moodle-oembed-Filter is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle-oembed-Filter is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle-oembed-Filter.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Filter for component 'filter_oembed'
 *
 * @package   filter_oembed
 * @copyright Erich M. Wappis / Guy Thomas 2016
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Mat Cannings
 * @author James McQuillan
 * @author Vin Bhalerao
 * @author Erich M. Wappis <erich.wappis@uni-graz.at>
 * @author Guy Thomas <brudinie@googlemail.com>
 * @author Mike Churchward <mike.churchward@poetgroup.org>
 */

defined('MOODLE_INTERNAL') || die();

use filter_oembed\service\oembed;

require_once($CFG->libdir.'/filelib.php');
/**
 * Main filter class for embedded remote content.
 *
 * @package    filter_oembed
 * @copyright Erich M. Wappis / Guy Thomas 2016
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_oembed extends moodle_text_filter {

    /**
     * content gets filtered, links either wrapped in an <a> tag or in a <div> tag with class="oembed"
     * will be replaced by embeded content
     *
     * @param $text HTML to be processed.
     * @param $options
     * @return string String containing processed HTML.
     */
    public function filter($text, array $options = array()) {
        global $PAGE;

        static $initialised = false;

        if (!$initialised) {
            $PAGE->requires->js_call_amd('filter_oembed/oembed', 'init');
            $initialised = true;
        }

        $targettag = get_config('filter_oembed', 'targettag');

        if ($targettag == 'atag' && stripos($text, '</a>') === false) {
            // Performance shortcut - all regexes below end with the </a> tag.
            // If not present nothing can match.
            return $text;
        }

        $filtered = $text; // We need to return the original value if regex fails!
        if ($targettag == 'divtag') {
            $search = '/\<div\s[^\>]*data-oembed-href="(.*?)"(.*?)>(.*?)\<\/div\>/';
        } else { // Using 'atag'.
            $search = '/\<a\s[^\>]*href="(.*?)"(?:.*?)>(?:.*?)\<\/a\>/is';
        }

        $filtered = preg_replace_callback($search, 'self::find_oembeds_callback', $filtered);
        if (empty($filtered)) {
            // If $filtered is emtpy return original $text.
            return $text;
        } else {
            return $filtered;
        }
    }

    /**
     * Callback function to be used by the main filter
     *
     * @param $match array An array of matched groups, where [1] is the URL matched.
     *
     */
    private static function find_oembeds_callback($match) {
        $instance = oembed::get_instance();
        $result = $instance->html_output($match[1]);
        if (empty($result)) {
            $result = $match[0];
        }
        return $result;
    }
}
