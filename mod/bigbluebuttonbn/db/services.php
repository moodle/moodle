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
 * URL external functions and service definitions.
 *
 * @package   mod_bigbluebuttonbn
 * @category  external
 * @copyright 2018 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */

defined('MOODLE_INTERNAL') || die;

$functions = [
    'mod_bigbluebuttonbn_can_join' => [
        'classname'     => 'mod_bigbluebuttonbn\external\can_join',
        'methodname'    => 'execute',
        'description'   => 'Returns information if the current user can join or not.',
        'type'          => 'read',
        'capabilities'  => 'mod/bigbluebuttonbn:view',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_bigbluebuttonbn_get_recordings' => [
        'classname'     => 'mod_bigbluebuttonbn\external\get_recordings',
        'methodname'    => 'execute',
        'description'   => 'Returns a list of recordings ready to be processed by a datatable.',
        'type'          => 'read',
        'ajax'          => true,
        'capabilities'  => 'mod/bigbluebuttonbn:view',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_bigbluebuttonbn_get_recordings_to_import' => [
        'classname'     => 'mod_bigbluebuttonbn\external\get_recordings_to_import',
        'methodname'    => 'execute',
        'description'   => 'Returns a list of recordings ready to import to be processed by a datatable.',
        'type'          => 'read',
        'ajax'          => true,
        'capabilities'  => 'mod/bigbluebuttonbn:importrecordings',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_bigbluebuttonbn_update_recording' => [
        'classname'     => 'mod_bigbluebuttonbn\external\update_recording',
        'methodname'    => 'execute',
        'description'   => 'Update a single recording',
        'type'          => 'write',
        'ajax'          => true,
        'capabilities'  => 'mod/bigbluebuttonbn:managerecordings',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_bigbluebuttonbn_end_meeting' => [
        'classname'     => 'mod_bigbluebuttonbn\external\end_meeting',
        'methodname'    => 'execute',
        'description'   => 'End a meeting',
        'type'          => 'write',
        'ajax'          => true,
        'capabilities'  => 'mod/bigbluebuttonbn:join',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_bigbluebuttonbn_completion_validate' => [
        'classname'     => 'mod_bigbluebuttonbn\external\completion_validate',
        'methodname'    => 'execute',
        'description'   => 'Validate completion',
        'type'          => 'write',
        'ajax'          => true,
        'capabilities'  => 'mod/bigbluebuttonbn:view',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_bigbluebuttonbn_meeting_info' => [
        'classname'     => 'mod_bigbluebuttonbn\external\meeting_info',
        'methodname'    => 'execute',
        'description'   => 'Get displayable information on the meeting',
        'type'          => 'read',
        'ajax'          => true,
        'capabilities'  => 'mod/bigbluebuttonbn:view',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_bigbluebuttonbn_get_bigbluebuttonbns_by_courses' => [
        'classname'     => 'mod_bigbluebuttonbn\external\get_bigbluebuttonbns_by_courses',
        'methodname'    => 'execute',
        'description'   => 'Returns a list of bigbluebuttonbns in a provided list of courses, if no list is provided
                            all bigbluebuttonbns that the user can view will be returned.',
        'type'          => 'read',
        'capabilities'  => 'mod/bigbluebuttonbn:view',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_bigbluebuttonbn_view_bigbluebuttonbn' => [
        'classname'     => 'mod_bigbluebuttonbn\external\view_bigbluebuttonbn',
        'methodname'    => 'execute',
        'description'   => 'Trigger the course module viewed event and update the module completion status.',
        'type'          => 'write',
        'capabilities'  => 'mod/bigbluebuttonbn:view',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE]
    ],
    'mod_bigbluebuttonbn_get_join_url' => [
        'classname'     => 'mod_bigbluebuttonbn\external\get_join_url',
        'methodname'    => 'execute',
        'description'   => 'Get the join URL for the meeting and create if it does not exist.',
        'type'          => 'write',
        'capabilities'  => 'mod/bigbluebuttonbn:join',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
];
