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
 * IMSCP external functions and service definitions.
 *
 * @package    mod_imscp
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(

    'mod_imscp_view_imscp' => array(
        'classname'     => 'mod_imscp_external',
        'methodname'    => 'view_imscp',
        'description'   => 'Simulate the view.php web interface imscp: trigger events, completion, etc...',
        'type'          => 'write',
        'capabilities'  => 'mod/imscp:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_imscp_get_imscps_by_courses' => array(
        'classname'     => 'mod_imscp_external',
        'methodname'    => 'get_imscps_by_courses',
        'description'   => 'Returns a list of IMSCP instances in a provided set of courses,
                            if no courses are provided then all the IMSCP instances the user has access to will be returned.',
        'type'          => 'read',
        'capabilities'  => 'mod/imscp:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

);
