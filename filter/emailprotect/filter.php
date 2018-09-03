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
 * Basic email protection filter.
 *
 * @package    filter
 * @subpackage emailprotect
 * @copyright  2004 Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This class looks for email addresses in Moodle text and
 * hides them using the Moodle obfuscate_text function.
 */
class filter_emailprotect extends moodle_text_filter {
    function filter($text, array $options = array()) {
    /// Do a quick check using stripos to avoid unnecessary work
        if (strpos($text, '@') === false) {
            return $text;
        }

    /// There might be an email in here somewhere so continue ...
        $matches = array();

    /// regular expression to define a standard email string.
        $emailregex = '((?:[\w\.\-])+\@(?:(?:[a-zA-Z\d\-])+\.)+(?:[a-zA-Z\d]{2,4}))';

    /// pattern to find a mailto link with the linked text.
        $pattern = '|(<a\s+href\s*=\s*[\'"]?mailto:)'.$emailregex.'([\'"]?\s*>)'.'(.*)'.'(</a>)|iU';
        $text = preg_replace_callback($pattern, 'filter_emailprotect_alter_mailto', $text);

    /// pattern to find any other email address in the text.
        $pattern = '/(^|\s+|>)'.$emailregex.'($|\s+|\.\s+|\.$|<)/i';
        $text = preg_replace_callback($pattern, 'filter_emailprotect_alter_email', $text);

        return $text;
    }
}


function filter_emailprotect_alter_email($matches) {
    return $matches[1].obfuscate_text($matches[2]).$matches[3];
}

function filter_emailprotect_alter_mailto($matches) {
    return obfuscate_mailto($matches[2], $matches[4]);
}


