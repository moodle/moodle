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
 * Format tiles web services defintions
 *
 * @package   format_tiles
 * @category  event
 * @copyright 2018 David Watson {@link http://evolutioncode.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array (
    'format_tiles_set_image' => array(
        'classname'   => 'format_tiles_external',
        'methodname'  => 'set_image',
        'classpath'   => 'course/format/tiles/externallib.php',
        'description' => 'Set tile icon (intended to be used from AJAX)',
        'type'        => 'write',
        'ajax'        => true,
        'loginrequired' => true,
        'capabilities' => 'moodle/course:update'
    ),
    'format_tiles_log_mod_view' => array(
        'classname'   => 'format_tiles_external',
        'methodname'  => 'log_mod_view',
        'classpath'   => 'course/format/tiles/externallib.php',
        'description' => 'Trigger course module view event (for log) for a resource (for modal use)',
        'type'        => 'write',
        'ajax'        => true,
        'loginrequired' => true,
        'capabilities' => 'mod/[modulename]:view'
    ),
    'format_tiles_get_single_section_page_html' => array(
        'classname'   => 'format_tiles_external',
        'methodname'  => 'get_single_section_page_html',
        'classpath'   => 'course/format/tiles/externallib.php',
        'description' => 'Get HTML for single section page for this course (i.e. tile contents)',
        'type'        => 'read',
        'ajax'        => true,
        'loginrequired' => true,
        'capabilities' => '' // Enrolment check, not capability - see externallib.php.
    ),
    'format_tiles_log_tile_click' => array(
        'classname'   => 'format_tiles_external',
        'methodname'  => 'log_tile_click',
        'classpath'   => 'course/format/tiles/externallib.php',
        'description' => 'Trigger course view event for a section (for log) on section tile click',
        'type'        => 'write',
        'ajax'        => true,
        'loginrequired' => true,
        'capabilities' => ''  // Enrolment check, not capability - see externallib.php.
    ),
    'format_tiles_get_mod_page_html' => array(
        'classname'   => 'format_tiles_external',
        'methodname'  => 'get_mod_page_html',
        'classpath'   => 'course/format/tiles/externallib.php',
        'description' => 'Return the HTML for a page course module (for modal use)',
        'type'        => 'read',
        'ajax'        => true,
        'loginrequired' => true,
        'capabilities' => 'mod/page:view'
    ),
    'format_tiles_get_icon_set' => array(
        'classname'   => 'format_tiles_external',
        'methodname'  => 'get_icon_set',
        'classpath'   => 'course/format/tiles/externallib.php',
        'description' => 'Return the available icon set (for editing teacher)',
        'type'        => 'read',
        'ajax'        => true,
        'loginrequired' => true,
        'capabilities' => 'moodle/course:update'
    ),
    'format_tiles_set_session_width' => array(
        'classname'   => 'format_tiles_external',
        'methodname'  => 'set_session_width',
        'classpath'   => 'course/format/tiles/externallib.php',
        'description' => 'Set session width of tiles window (so that tiles can be shown with correct width on page load',
        'type'        => 'write',
        'ajax'        => true,
        'loginrequired' => true,
        'capabilities' => '' // Enrolment check, not capability - see externallib.php.
    ),
    'format_tiles_get_section_information' => array(
        'classname'   => 'format_tiles_external',
        'methodname'  => 'get_section_information',
        'classpath'   => 'course/format/tiles/externallib.php',
        'description' => 'Get information for a section including availability info to refresh tile info on progress',
        'type'        => 'read',
        'ajax'        => true,
        'loginrequired' => true,
        'capabilities' => '' // Enrolment check, not capability - see externallib.php.
    ),
    // To enable us to use this core service even in Moodle 3.10 or lower, we add it here and rebrand it as format_tiles.
    // This is not necessary in Moodle 3.11 or higher (as they allow access to it from ajax as core_completion_update.. anyway).
    'format_tiles_update_activity_completion_status_manually' => array(
        'classname' => 'core_completion_external',
        'methodname' => 'update_activity_completion_status_manually',
        'description' => 'Update completion status for the current user in an activity, only for activities with manual tracking.',
        'type' => 'write',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        'ajax' => true,
    ),
);
