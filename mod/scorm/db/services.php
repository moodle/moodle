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
 * SCORM external functions and service definitions.
 *
 * @package    mod_scorm
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

$functions = array(

    'mod_scorm_view_scorm' => array(
        'classname'     => 'mod_scorm_external',
        'methodname'    => 'view_scorm',
        'description'   => 'Trigger the course module viewed event.',
        'type'          => 'write',
        'capabilities'  => ''
    ),

    'mod_scorm_get_scorm_attempt_count' => array(
        'classname'     => 'mod_scorm_external',
        'methodname'    => 'get_scorm_attempt_count',
        'description'   => 'Return the number of attempts done by a user in the given SCORM.',
        'type'          => 'read',
        'capabilities'  => ''
    ),
);
