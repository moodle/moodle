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

namespace filter_emailprotect;

/**
 * Basic email protection filter.
 *
 * This class looks for email addresses in Moodle text and hides them using the Moodle obfuscate_text function.
 *
 * @package    filter_emailprotect
 * @subpackage emailprotect
 * @copyright  2004 Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_filter extends \core_filters\text_filter {
    #[\Override]
    public function filter($text, array $options = []) {
        // Do a quick check using stripos to avoid unnecessary work.
        if (strpos($text, '@') === false) {
            return $text;
        }

        // Regular expression to define a standard email string.
        $emailregex = '((?:[\w\.\-])+\@(?:(?:[a-zA-Z\d\-])+\.)+(?:[a-zA-Z\d]{2,4}))';

        // Pattern to find a mailto link with the linked text.
        $pattern = '|(<a\s+href\s*=\s*[\'"]?mailto:)' . $emailregex . '([\'"]?\s*>)' . '(.*)' . '(</a>)|iU';
        $text = preg_replace_callback($pattern, [self::class, 'alter_mailto'], $text);

        // Pattern to find any other email address in the text.
        $pattern = '/(^|\s+|>)' . $emailregex . '($|\s+|\.\s+|\.$|<)/i';
        $text = preg_replace_callback($pattern, [self::class, 'alter_email'], $text);

        return $text;
    }

    /**
     * Obfuscate the email address.
     *
     * @param mixed $matches
     * @return string
     */
    private function alter_email($matches) {
        return $matches[1] . obfuscate_text($matches[2]) . $matches[3];
    }

    /**
     * Obfuscate the mailto link.
     *
     * @param mixed $matches
     * @return string
     */
    private function alter_mailto($matches) {
        return obfuscate_mailto($matches[2], $matches[4]);
    }
}
