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
//

/**
 * It is a filter that allows to visualize formulas generated with
 * MathType image service.
 *
 * Replaces all substrings '«math ... «/math»' '<math ... </math>'
 * generated with MathType by the corresponding image.
 *
 * @package    filter_wiris
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_wiris;


defined('MOODLE_INTERNAL') || die();

// Import all available 'subfilters'.
use filter_wiris\subfilters\filter_wiris_client;
use filter_wiris\subfilters\filter_wiris_php;

require_once("$CFG->dirroot/filter/wiris/subfilters/client.php");
require_once("$CFG->dirroot/filter/wiris/subfilters/php.php");

/**
 * Class filter_wiris
 * This class extends the moodle_text_filter and provides a method to filter text using the Wiris filter.
 */
class text_filter extends \core_filters\text_filter {

    /**
     * Filters the given text using the Wiris filter.
     *
     * @param  string $text    The text to be filtered.
     * @param  array  $options An array of options for the filter (optional).
     * @return string The filtered text.
     */
    public function filter($text, array $options = []) {

        switch (get_config('filter_wiris', 'rendertype')) {
                // Client-side render: Uses the Javascript third-party lib.
            case 'client':
                $subfilter = new filter_wiris_client($this->context, $this->localconfig);
                break;
                // Server-sider render: Uses the PHP third-party lib (default).
            case 'php':
            default:
                $subfilter = new filter_wiris_php($this->context, $this->localconfig);
                break;
        }

        // Our custom Haxe-transpiled EReg was obsolete, so we have to do a replacement that used to happen in
        // filterMath here instead.
        // This fixes the xmlns=¨http://www.w3.org/1998/Math/MathML¨ being converted into a link by the
        // "Convert URLs into links and images" filter by Moodle when it is applied before the Wiris filter.
        // Looks for every SafeXML instance, and within there, removes the surrounding <a>...</a>.
        $text = preg_replace_callback(
            '/«math.*?«\\/math»/',
            function ($matches) {
                return preg_replace('/<a href="[^\"]*"[^>]*>([^<]*)<\\/a>|<a href="[^\"]*">/', '$1', $matches[0]);
            },
            $text
        );

        return $subfilter->filter($text, $options);
    }
}
