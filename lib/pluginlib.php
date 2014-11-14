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
 * Defines classes used for plugins management
 *
 * This library provides a unified interface to various plugin types in
 * Moodle. It is mainly used by the plugins management admin page and the
 * plugins check page during the upgrade.
 *
 * @todo MDL-46122 This file will be removed in 2.9
 * @package    core
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

debugging('lib/pluginlib.php has been deprecated, the classes that used to exist are now autoloaded. Please removed ' .
          'any calls to include or require this file.', DEBUG_DEVELOPER);