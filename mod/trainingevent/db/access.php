<?php
// This file is part of course classroom module for Moodle - http://moodle.org/
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
 * @package    mod
 * @subpackage trainingevent
 * @copyright  2011- E-Learn Design Ltd.
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$capabilities = array(

    'mod/trainingevent:addinstance' => array(
        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'companycourseeditor' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:manageactivities'
    ),

    'mod/trainingevent:invite' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'companycourseeditor' => CAP_ALLOW,
            'companycoursenoneditor' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        )
    ),

    'mod/trainingevent:add' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'companycourseeditor' => CAP_ALLOW,
            'companycoursenoneditor' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        )
    ),

    'mod/trainingevent:viewattendees' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'companycourseeditor' => CAP_ALLOW,
            'companycoursenoneditor' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        )
    ),

    'mod/trainingevent:resetattendees' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'companycourseeditor' => CAP_ALLOW,
            'companycoursenoneditor' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        )
    ),

    'mod/trainingevent:grade' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'companycourseeditor' => CAP_ALLOW,
            'companycoursenoneditor' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        )
    ),

);
