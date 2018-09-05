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
 * Backup support for tool_log logstore subplugins.
 *
 * @package    tool_log
 * @category   backup
 * @copyright  2015 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Parent class of all the logstore subplugin implementations.
 *
 * Note: While this intermediate class is not strictly required and all the
 * subplugin implementations can extend directly {@link backup_subplugin},
 * it is always recommended to have it, both for better testing and also
 * for sharing code between all subplugins.
 */
abstract class backup_tool_log_logstore_subplugin extends backup_subplugin {
}
