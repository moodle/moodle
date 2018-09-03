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
 * Completion Progress block capability setup
 *
 * @package    block_completion_progress
 * @copyright  2016 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die();

$capabilities = array (
    'block/completion_progress:overview' => array (
        'riskbitmask'   => RISK_PERSONAL,
        'captype'       => 'read',
        'contextlevel'  => CONTEXT_BLOCK,
        'archetypes'    => array (
            'teacher'           => CAP_ALLOW,
            'editingteacher'    => CAP_ALLOW,
            'manager'           => CAP_ALLOW,
            'coursecreator'     => CAP_ALLOW
        )
    ),

    'block/completion_progress:showbar' => array (
        'captype'       => 'read',
        'contextlevel'  => CONTEXT_BLOCK,
        'archetypes'    => array (
            'teacher'           => CAP_ALLOW,
            'editingteacher'    => CAP_ALLOW,
            'student'           => CAP_ALLOW,
        )
    ),

    'block/completion_progress:addinstance' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW,
            'coursecreator'  => CAP_ALLOW
        ),

        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),

    'block/completion_progress:myaddinstance' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'user' => CAP_ALLOW
        ),

        'clonepermissionsfrom' => 'moodle/my:manageblocks'
    ),
);
