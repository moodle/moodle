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
 * Wrapper for Html2Text
 *
 * This wrapper allows us to modify the upstream library without hacking it too much.
 *
 * @package    core
 * @copyright  2015 Andrew Nicols <andrew@nicols.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/html2text/Html2Text.php');
require_once(__DIR__ . '/override.php');

/**
 * Wrapper for Html2Text
 *
 * This wrapper allows us to modify the upstream library without hacking it too much.
 *
 * @package    core
 * @copyright  2015 Andrew Nicols <andrew@nicols.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_html2text extends \Html2Text\Html2Text {

    /**
     * Constructor.
     *
     * If the HTML source string (or file) is supplied, the class
     * will instantiate with that source propagated, all that has
     * to be done it to call get_text().
     *
     * @param string $html    Source HTML
     * @param array  $options Set configuration options
     */
    function __construct($html = '', $options = array()) {
        // Call the parent constructor.
        parent::__construct($html, $options);

        // MDL-27736: Trailing spaces before newline or tab.
        $this->entSearch[] = '/[ ]+([\n\t])/';
        $this->entReplace[] = '\\1';
    }

    /**
     * Strtoupper multibyte wrapper function with HTML entities handling.
     *
     * @param string $str Text to convert
     * @return string Converted text
     */
    protected function strtoupper($str) {
        return core_text::strtoupper($str);
    }
}
