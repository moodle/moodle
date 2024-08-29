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
 * HTML tidy text filter.
 *
 * Note: This plugin has been disabled as the underlying library is known to break content in a many cases.
 *
 * See MDL-82790 for more information.
 *
 * @package    filter
 * @subpackage tidy
 * @copyright  2004 Hannes Gassert <hannes at mediagonal dot ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_tidy extends moodle_text_filter {
    #[\Override]
    public function filter($text, array $options = []) {
        return $text;
    }
}
