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
 * @package    block_simple_restore
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Be sure no one accesses the page directly.
defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'block/simple_restore:canrestore' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW
        )
    ),
    'block/simple_restore:canrestorearchive' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager'        => CAP_ALLOW,
            'coursecreator'  => CAP_ALLOW,
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'user'           => CAP_ALLOW,
        )
    ),
    'block/simple_restore:addinstance' => array(
        'riskbitmask' => RISK_DATALOSS,
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'manager'        => CAP_ALLOW,
            'coursecreator'  => CAP_ALLOW,
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'user'           => CAP_ALLOW,
        ),
    'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),

    'block/simple_restore:myaddinstance' => array(
       'riskbitmask' => RISK_DATALOSS,
       'captype' => 'write',
       'contextlevel' => CONTEXT_SYSTEM,
       'archetypes' => array(
           'frontpage' => CAP_ALLOW,
           'user'      => CAP_ALLOW,
       )
   ),
);
