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
 * Capabilities.
 *
 * This files lists capabilities related to tool_lp.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    'tool/lp:competencymanage' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSECAT,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        )
    ),
    'tool/lp:competencyread' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSECAT,
        'archetypes' => array(
            'user' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/block:view'
    ),
    'tool/lp:coursecompetencymanage' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/site:backup'
    ),
    'tool/lp:coursecompetencyread' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'user' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/block:view'
    ),
    'tool/lp:planmanage' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => array(
        ),
        'clonepermissionsfrom' => 'moodle/site:config'
    ),
    'tool/lp:planmanagedraft' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => array(
        ),
        'clonepermissionsfrom' => 'moodle/site:config'
    ),
    'tool/lp:planmanageown' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => array(
        ),
        'clonepermissionsfrom' => 'moodle/site:config'
    ),
    'tool/lp:planmanageowndraft' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => array(
        ),
        'clonepermissionsfrom' => 'moodle/site:config'
    ),
    'tool/lp:planview' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => array(
        ),
        'clonepermissionsfrom' => 'moodle/site:config'
    ),
    'tool/lp:planviewdraft' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => array(
        ),
        'clonepermissionsfrom' => 'moodle/site:config'
    ),
    'tool/lp:planviewown' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => array(
            'user' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/block:view'
    ),
    'tool/lp:planviewowndraft' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => array(
        ),
        'clonepermissionsfrom' => 'moodle/site:config'
    ),
    'tool/lp:templatemanage' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSECAT,
        'archetypes' => array(
        ),
        'clonepermissionsfrom' => 'moodle/site:config'
    ),
    'tool/lp:templateread' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSECAT,
        'archetypes' => array(
            'user' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/block:view'
    ),
    // User evidence.
    'tool/lp:userevidencemanage' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => array(
        ),
        'clonepermissionsfrom' => 'moodle/site:config'
    ),
    'tool/lp:userevidencemanageown' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => array(
            'user' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/block:view'
    ),
    'tool/lp:userevidenceread' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => array(
        ),
        'clonepermissionsfrom' => 'moodle/site:config'
    ),
    'tool/lp:competencysuggestgrade' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE, // And CONTEXT_USER.
        'archetypes' => array(
            'teacher' => CAP_ALLOW
        ),
    ),
    'tool/lp:competencygrade' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE, // And CONTEXT_USER.
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
    ),
);
