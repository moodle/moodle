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
 * @package    block_migrate_users
 * @copyright  2019 onwards Louisiana State University
 * @copyright  2019 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    $capabilities = array(
 
    'block/migrate_users:myaddinstance' => array(
        'riskbitmask' => RISK_DATALOSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_PREVENT,
            'coursecreator' => CAP_PREVENT,
            'editingteacher' => CAP_PREVENT,
            'teacher' => CAP_PREVENT,
            'student' => CAP_PREVENT,
            'guest' => CAP_PREVENT,
            'user' => CAP_PREVENT,
            'frontpage' => CAP_PREVENT
        )
    ),
 
    'block/migrate_users:addinstance' => array(
        'riskbitmask' => RISK_DATALOSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'manager' => CAP_PREVENT,
            'coursecreator' => CAP_PREVENT,
            'editingteacher' => CAP_PREVENT,
            'teacher' => CAP_PREVENT,
            'student' => CAP_PREVENT,
            'guest' => CAP_PREVENT,
            'user' => CAP_PREVENT,
            'frontpage' => CAP_PREVENT
        )
    ),

    'block/migrate_users:migrate_user' => array(
        'riskbitmask' => RISK_DATALOSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_PREVENT,
            'coursecreator' => CAP_PREVENT,
            'editingteacher' => CAP_PREVENT,
            'teacher' => CAP_PREVENT,
            'student' => CAP_PREVENT,
            'guest' => CAP_PREVENT,
            'user' => CAP_PREVENT,
            'frontpage' => CAP_PREVENT
        )
    ),
);
