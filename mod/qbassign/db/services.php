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
 * Web service for mod qbassign
 * @package    mod_qbassign
 * @subpackage db
 * @since      Moodle 2.4
 * @copyright  2012 Paul Charsley
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(

        'mod_qbassign_copy_previous_attempt' => array(
            'classname'     => 'mod_qbassign_external',
            'methodname'    => 'copy_previous_attempt',
            'classpath'     => 'mod/qbassign/externallib.php',
            'description'   => 'Copy a students previous attempt to a new attempt.',
            'type'          => 'write',
            'capabilities'  => 'mod/qbassign:view, mod/qbassign:submit'
        ),

        'mod_qbassign_get_grades' => array(
                'classname'   => 'mod_qbassign_external',
                'methodname'  => 'get_grades',
                'classpath'   => 'mod/qbassign/externallib.php',
                'description' => 'Returns grades from the qbassignment',
                'type'        => 'read',
                'services'    => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_get_qbassignments' => array(
                'classname'   => 'mod_qbassign_external',
                'methodname'  => 'get_qbassignments',
                'classpath'   => 'mod/qbassign/externallib.php',
                'description' => 'Returns the courses and qbassignments for the users capability',
                'type'        => 'read',
                'services'    => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_get_submissions' => array(
                'classname' => 'mod_qbassign_external',
                'methodname' => 'get_submissions',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Returns the submissions for qbassignments',
                'type' => 'read',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_get_user_flags' => array(
                'classname' => 'mod_qbassign_external',
                'methodname' => 'get_user_flags',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Returns the user flags for qbassignments',
                'type' => 'read',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_set_user_flags' => array(
                'classname'   => 'mod_qbassign_external',
                'methodname'  => 'set_user_flags',
                'classpath'   => 'mod/qbassign/externallib.php',
                'description' => 'Creates or updates user flags',
                'type'        => 'write',
                'capabilities'=> 'mod/qbassign:grade',
                'services'    => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_get_user_mappings' => array(
                'classname' => 'mod_qbassign_external',
                'methodname' => 'get_user_mappings',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Returns the blind marking mappings for qbassignments',
                'type' => 'read',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_revert_submissions_to_draft' => array(
                'classname' => 'mod_qbassign_external',
                'methodname' => 'revert_submissions_to_draft',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Reverts the list of submissions to draft status',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_lock_submissions' => array(
                'classname' => 'mod_qbassign_external',
                'methodname' => 'lock_submissions',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Prevent students from making changes to a list of submissions',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_unlock_submissions' => array(
                'classname' => 'mod_qbassign_external',
                'methodname' => 'unlock_submissions',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Allow students to make changes to a list of submissions',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_save_submission' => array(
                'classname' => 'mod_qbassign_external',
                'methodname' => 'save_submission',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Update the current students submission',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_submit_for_grading' => array(
                'classname' => 'mod_qbassign_external',
                'methodname' => 'submit_for_grading',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Submit the current students qbassignment for grading',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_save_grade' => array(
                'classname' => 'mod_qbassign_external',
                'methodname' => 'save_grade',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Save a grade update for a single student.',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_save_grades' => array(
                'classname' => 'mod_qbassign_external',
                'methodname' => 'save_grades',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Save multiple grade updates for an qbassignment.',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_save_user_extensions' => array(
                'classname' => 'mod_qbassign_external',
                'methodname' => 'save_user_extensions',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Save a list of qbassignment extensions',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_reveal_identities' => array(
                'classname' => 'mod_qbassign_external',
                'methodname' => 'reveal_identities',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Reveal the identities for a blind marking qbassignment',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_view_grading_table' => array(
                'classname'     => 'mod_qbassign_external',
                'methodname'    => 'view_grading_table',
                'classpath'     => 'mod/qbassign/externallib.php',
                'description'   => 'Trigger the grading_table_viewed event.',
                'type'          => 'write',
                'capabilities'  => 'mod/qbassign:view, mod/qbassign:viewgrades',
                'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_view_submission_status' => array(
            'classname'     => 'mod_qbassign_external',
            'methodname'    => 'view_submission_status',
            'classpath'     => 'mod/qbassign/externallib.php',
            'description'   => 'Trigger the submission status viewed event.',
            'type'          => 'write',
            'capabilities'  => 'mod/qbassign:view',
            'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_get_submission_status' => array(
            'classname'     => 'mod_qbassign_external',
            'methodname'    => 'get_submission_status',
            'classpath'     => 'mod/qbassign/externallib.php',
            'description'   => 'Returns information about an qbassignment submission status for a given user.',
            'type'          => 'read',
            'capabilities'  => 'mod/qbassign:view',
            'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_list_participants' => array(
                'classname'     => 'mod_qbassign_external',
                'methodname'    => 'list_participants',
                'classpath'     => 'mod/qbassign/externallib.php',
                'description'   => 'List the participants for a single qbassignment, with some summary info about their submissions.',
                'type'          => 'read',
                'ajax'          => true,
                'capabilities'  => 'mod/qbassign:view, mod/qbassign:viewgrades',
                'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_qbassign_submit_grading_form' => array(
                'classname'     => 'mod_qbassign_external',
                'methodname'    => 'submit_grading_form',
                'classpath'     => 'mod/qbassign/externallib.php',
                'description'   => 'Submit the grading form data via ajax',
                'type'          => 'write',
                'ajax'          => true,
                'capabilities'  => 'mod/qbassign:grade',
                'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),
        'mod_qbassign_get_participant' => array(
                'classname'     => 'mod_qbassign_external',
                'methodname'    => 'get_participant',
                'classpath'     => 'mod/qbassign/externallib.php',
                'description'   => 'Get a participant for an qbassignment, with some summary info about their submissions.',
                'type'          => 'read',
                'ajax'          => true,
                'capabilities'  => 'mod/qbassign:view, mod/qbassign:viewgrades',
                'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),
        'mod_qbassign_view_qbassign' => array(
            'classname'     => 'mod_qbassign_external',
            'methodname'    => 'view_qbassign',
            'classpath'     => 'mod/qbassign/externallib.php',
            'description'   => 'Update the module completion status.',
            'type'          => 'write',
            'capabilities'  => 'mod/qbassign:view',
            'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),
        'mod_qbassign_start_submission' => [
            'classname'     => 'mod_qbassign\external\start_submission',
            'methodname'    => 'execute',
            'description'   => 'Start a submission for user if qbassignment has a time limit.',
            'type'          => 'write',
            'capabilities'  => 'mod/qbassign:view',
            'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ],
        'mod_qbassign_create_assignment_service' => [
                'classname' => 'mod_qbassign_external',
                'methodname' => 'create_assignment_service',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Create New assignment',
                'type' => 'write',
                'ajax' => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ],
        'mod_qbassign_get_assignment_service' => [
                'classname' => 'mod_qbassign_external',
                'methodname' => 'get_assignment_service',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'List assignment details using unique field',
                'type' => 'read',
                'ajax' => true,
                'capabilities'  => 'mod/qbassign:view',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ],
        'mod_qbassign_save_studentsubmission' => [
                'classname' => 'mod_qbassign_external',
                'methodname' => 'save_studentsubmission',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Save students submission',
                'type' => 'write',
                'ajax' => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ],
        'mod_qbassign_quiz_addition' => [
                'classname' => 'mod_qbassign_external',
                'methodname' => 'quiz_addition',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Import Quiz from manifest file',
                'type' => 'write',
                'ajax' => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ],
        'mod_qbassign_remove_submission' => [
                'classname' => 'mod_qbassign_external',
                'methodname' => 'remove_submission',
                'classpath' => 'mod/qbassign/externallib.php',
                'description' => 'Remove student submission details using unique field',
                'type' => 'read',
                'ajax' => true,
                'capabilities'  => 'mod/qbassign:view',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ],
);
