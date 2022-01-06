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
 * Choice group external functions and service definitions.
 *
 * @package    mod_choicegroup
 * @category   external
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(

    'mod_choicegroup_get_choicegroup_options' => array(
        'classname'     => 'mod_choicegroup_external',
        'methodname'    => 'get_choicegroup_options',
        'description'   => 'Retrieve options for a specific choicegroup.',
        'type'          => 'read',
        'capabilities'  => 'mod/choicegroup:choose',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile')
    ),

    'mod_choicegroup_submit_choicegroup_response' => array(
        'classname'     => 'mod_choicegroup_external',
        'methodname'    => 'submit_choicegroup_response',
        'description'   => 'Submit responses to a specific choicegroup item.',
        'type'          => 'write',
        'capabilities'  => 'mod/choicegroup:choose',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile')
    ),

    'mod_choicegroup_view_choicegroup' => array(
        'classname'     => 'mod_choicegroup_external',
        'methodname'    => 'view_choicegroup',
        'description'   => 'Trigger the course module viewed event and update the module completion status.',
        'type'          => 'write',
        'capabilities'  => '',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile')
    ),

    'mod_choicegroup_delete_choicegroup_responses' => array(
        'classname'     => 'mod_choicegroup_external',
        'methodname'    => 'delete_choicegroup_responses',
        'description'   => 'Delete the given submitted responses in a choice group',
        'type'          => 'write',
        'capabilities'  => 'mod/choicegroup:choose',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile')
    ),
);
