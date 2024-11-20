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

namespace core_filters;

use core\context;

/**
 * Filter manager subclass that does nothing. Having this simplifies the logic
 * of format_text, etc.
 *
 * @package core_filters
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class null_filter_manager {
    /**
     * As for the equivalent {@see filter_manager} method.
     *
     * @param string $text The text to filter
     * @param context $context not used.
     * @param array $options not used
     * @param null|array $skipfilters not used
     * @return string resulting text.
     */
    public function filter_text(
        $text,
        $context,
        array $options = [],
        ?array $skipfilters = null
    ) {
        return $text;
    }

    /**
     * As for the equivalent {@see filter_manager} method.
     *
     * @param string $string The text to filter
     * @param context $context not used.
     * @return string resulting string
     */
    public function filter_string($string, $context) {
        return $string;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(null_filter_manager::class, \null_filter_manager::class);
