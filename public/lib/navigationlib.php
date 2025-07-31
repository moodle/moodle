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
 * This file contains classes used to manage the navigation structures within Moodle.
 *
 * @since      Moodle 2.0
 * @package    core
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// This file is deprecated, but it should never have been manually included by anything outside of a few select core libraries.
// Throwing an exception here should be fine because removing the manual inclusion should have no impact.
// @deprecated Since Moodle 5.1 MDL-82159.
// The constants NAVIGATION_CACHE_NAME and NAVIGATION_SITE_ADMIN_CACHE_NAME are deprecated and should not be used anymore.
// They are defined here for autocompletion within IDEs but should not be used outside of \core\navigation anyway.
throw new \core\exception\coding_exception(
    'This file should not be manually included by any component.',
);

/**
 * @deprecated Since Moodle 5.1 MDL-82159.
 */
define('NAVIGATION_CACHE_NAME', 'navigation');

/**
 * @deprecated Since Moodle 5.1 MDL-82159.
 */
define('NAVIGATION_SITE_ADMIN_CACHE_NAME', 'navigationsiteadmin');
