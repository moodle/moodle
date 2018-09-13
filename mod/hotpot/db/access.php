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
 * mod/hotpot/db/access.php
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    // Ability to add a new hotpot to the course.
    'mod/hotpot:addinstance' => array(
        'riskbitmask'  => RISK_XSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'editingteacher' => CAP_ALLOW,
            'manager'  => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:manageactivities'
    ),

    // ability to attempt HotPot as a student (and submit results)
    'mod/hotpot:attempt' => array(
        'riskbitmask'  => RISK_SPAM,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => array(
            'student'  => CAP_ALLOW,
            'teacher'  => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'  => CAP_ALLOW
        )
    ),

    // ability to delete anyone's attempts at a HotPot
    'mod/hotpot:deleteallattempts' => array(
        'riskbitmask'  => RISK_DATALOSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => array(
            'teacher'  => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'  => CAP_ALLOW
        )
    ),

    // ability to delete one's own attempts at a HotPot
    'mod/hotpot:deletemyattempts' => array(
        'riskbitmask'  => RISK_DATALOSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => array(
            'teacher'  => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'  => CAP_ALLOW
        )
    ),

    // ability to ignore time limits (used for accessibility legislation compliance)
    //'mod/hotpot:ignoretimelimits' => array(
    //    'captype'      => 'read',
    //    'contextlevel' => CONTEXT_MODULE,
    //    'archetypes'   => array()
    //),

    // ability to edit the HotPot settings
    'mod/hotpot:manage' => array(
        'riskbitmask'  => RISK_SPAM,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => array(
            'editingteacher' => CAP_ALLOW,
            'manager'  => CAP_ALLOW
        )
    ),

    // ability to preview a HotPot as a teacher (and submit results)
    // access restrictions, such as open/close time, will be ignored
    'mod/hotpot:preview' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => array(
            'teacher'  => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'  => CAP_ALLOW
        )
    ),

    // ability to view anyone's attempts at a HotPot
    'mod/hotpot:reviewallattempts' => array(
        'riskbitmask'  => RISK_PERSONAL,
        'captype'      => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => array(
            'teacher'  => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'  => CAP_ALLOW
        )
    ),

    // ability to view one's own attempts at a HotPot
    'mod/hotpot:reviewmyattempts' => array(
        'riskbitmask'  => RISK_PERSONAL,
        'captype'      => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => array(
            'student'  => CAP_ALLOW,
            'teacher'  => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'  => CAP_ALLOW
        )
    ),

    // ability to view the entry page for a HotPot
    'mod/hotpot:view' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => array(
            'guest' => CAP_ALLOW,
            'student'  => CAP_ALLOW,
            'teacher'  => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'  => CAP_ALLOW
        )
    ),
);
