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

namespace Html2Text {
    function mb_internal_encoding($encoding = null) {
        static $internalencoding = 'utf-8';
        if ($encoding !== null) {
            $internalencoding = $encoding;
            return true;
        } else {
            return $internalencoding;
        }
    }

    function mb_substr($str, $start, $length, $encoding = null) {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        return \core_text::substr($str, $start, $length, $encoding);
    }

    function mb_strlen($str, $encoding = null) {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        return \core_text::strlen($str, $encoding);
    }

    function mb_strtolower($str, $encoding = null) {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        return \core_text::strtolower($str, $encoding);
    }
}
