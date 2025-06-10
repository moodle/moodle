<?php
// This file is part of Moodle - https://moodle.org/
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
 * File only retained to mantain compatibility with old versions of the filter.
 *
 * @deprecated This file is no longer required in Moodle 4.5+.
 * @package    filter_wiris
 * @subpackage wiris
 * @copyright  2023 WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// For backwards compatibility with Moodle 4.4 and below.
if ($CFG->branch < 405) {
    class_alias('\moodle_text_filter', '\core_filters\text_filter');
    require_once(__DIR__ . '/classes/text_filter.php');
    class_alias('filter_wiris\text_filter', '\filter_wiris');
}
