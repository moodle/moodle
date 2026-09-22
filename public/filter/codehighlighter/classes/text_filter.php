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

namespace filter_codehighlighter;

/**
 * Code highlighter filter.
 *
 * Filter converting text code into a well-styled block of code.
 *
 * @package    filter_codehighlighter
 * @copyright  2023 Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_filter extends \core_filters\text_filter {
    #[\Override]
    public function setup($page, $context) {
        // The setup() runs once per string filtered on the page, so guard the cardinality.
        if (!$page->requires->should_create_one_time_item_now('filter_codehighlighter-scripts')) {
            return;
        }
        $page->requires->js_call_amd('filter_codehighlighter/prism-init');
    }

    #[\Override]
    public function filter($text, array $options = []): string {
        return $text;
    }
}
