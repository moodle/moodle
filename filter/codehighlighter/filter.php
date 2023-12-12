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
 * Code highlighter filter.
 *
 * Filter converting text code into a well-styled block of code.
 *
 * @package    filter_codehighlighter
 * @copyright  2023 Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_codehighlighter extends moodle_text_filter {
    /**
     * Apply the filter to the text
     *
     * @param string $text to be processed by the text
     * @param array $options filter options
     * @return string text after processing
     */
    public function filter($text, array $options = []): string {
        global $PAGE;

        if (!isset($options['originalformat'])) {
            return $text;
        }

        // The pattern.
        $re = '/<pre.+?class=".*?language-.*?"><code>/i';

        // Stops looking after the first match.
        preg_match($re, $text, $matches);
        if ($matches) {
            $PAGE->requires->js_call_amd('filter_codehighlighter/prism-init');
        }

        return $text;
    }
}
