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

namespace tool_brickfield\local\htmlchecker\common;

use tool_brickfield\local\htmlchecker\brickfield_accessibility;

/**
 * This is a helper class which organizes all the HTML tags into groups for finding.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class html_elements {
    /**
     * @var array An array of HTML tag names and their attributes
     */
    public static $htmlelements = [
        'img'        => ['text' => false],
        'p'          => ['text' => true],
        'pre'        => ['text' => true],
        'span'       => ['text' => true],
        'div'        => ['text' => true],
        'applet'     => ['text' => false],
        'embed'      => ['text' => false, 'media' => true],
        'object'     => ['text' => false, 'media' => true],
        'area'       => ['imagemap' => true],
        'b'          => ['text' => true, 'non-emphasis' => true],
        'i'          => ['text' => true, 'non-emphasis' => true],
        'font'       => ['text' => true, 'font' => true],
        'h1'         => ['text' => true, 'header' => true],
        'h2'         => ['text' => true, 'header' => true],
        'h3'         => ['text' => true, 'header' => true],
        'h4'         => ['text' => true, 'header' => true],
        'h5'         => ['text' => true, 'header' => true],
        'h6'         => ['text' => true, 'header' => true],
        'ul'         => ['text' => true, 'list' => true],
        'dl'         => ['text' => true, 'list' => true],
        'ol'         => ['text' => true, 'list' => true],
        'blockquote' => ['text' => true, 'quote' => true],
        'q'          => ['text' => true, 'quote' => true],
        'acronym'    => ['acronym' => true, 'text' => true],
        'abbr'       => ['acronym' => true, 'text' => true],
        'input'      => ['form' => true],
        'select'     => ['form' => true],
        'textarea'   => ['form' => true],
        brickfield_accessibility::BA_ERROR_TAG => ['text' => true],
    ];

    /**
     * Retrieves elements by an option.
     * @param string $option The option to search fore
     * @param bool $value Whether the option should be true or false
     * @return array An array of HTML tag names
     */
    public static function get_elements_by_option(string $option, bool $value = true): array {
        $results = [];
        foreach (self::$htmlelements as $k => $element) {
            if (isset($element[$option]) && ($element[$option] == $value)) {
                $results[] = $k;
            }
        }
        return $results;
    }
}
