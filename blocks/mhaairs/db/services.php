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
 * Block MHAAIRS web services.
 *
 * @package     block_mhaairs
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @copyright   2013 Moodlerooms inc.
 * @author      Teresa Hardy <thardy@moodlerooms.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
        // Depracated.
        'block_mhaairs_gradebookservice' => array(
                'classname' => 'block_mhaairs_gradebookservice_external',
                'methodname' => 'gradebookservice',
                'classpath' => 'blocks/mhaairs/externallib.php',
                'description' => 'Runs the grade_update() function',
                'type' => 'write',
                'capabilities' => 'moodle/grade:manage, moodle/grade:edit',
                'testclientpath' => 'blocks/mhaairs/admin/testclient_forms.php',
        ),

        'block_mhaairs_update_grade' => array(
                'classname' => 'block_mhaairs_gradebookservice_external',
                'methodname' => 'update_grade',
                'classpath' => 'blocks/mhaairs/externallib.php',
                'description' => 'Creates/updates/deletes mhaairs grade item.',
                'type' => 'write',
                'capabilities' => 'moodle/grade:manage, moodle/grade:edit',
                'testclientpath' => 'blocks/mhaairs/admin/testclient_forms.php',
        ),

        'block_mhaairs_get_grade' => array(
                'classname' => 'block_mhaairs_gradebookservice_external',
                'methodname' => 'get_grade',
                'classpath' => 'blocks/mhaairs/externallib.php',
                'description' => 'Returns grade item info and or student grade for the item.',
                'type' => 'read',
                'capabilities' => 'moodle/grade:view, moodle/grade:viewall, moodle/grade:viewhidden',
                'testclientpath' => 'blocks/mhaairs/admin/testclient_forms.php',
        ),

        'block_mhaairs_get_user_info' => array(
                'classname' => 'block_mhaairs_utilservice_external',
                'methodname' => 'get_user_info',
                'classpath' => 'blocks/mhaairs/externallib.php',
                'description' => 'Returns user info including list of courses the user in enrolled in.',
                'type' => 'read',
                'capabilities' => 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail',
                'testclientpath' => 'blocks/mhaairs/admin/testclient_forms.php',
        ),

        'block_mhaairs_validate_login' => array(
                'classname' => 'block_mhaairs_utilservice_external',
                'methodname' => 'validate_login',
                'classpath' => 'blocks/mhaairs/externallib.php',
                'description' => 'Authenticates user login.',
                'type' => 'read',
                'testclientpath' => 'blocks/mhaairs/admin/testclient_forms.php',
        ),

        'block_mhaairs_get_environment_info' => array(
                'classname' => 'block_mhaairs_utilservice_external',
                'methodname' => 'get_environment_info',
                'classpath' => 'blocks/mhaairs/externallib.php',
                'description' => 'Returns environment info.',
                'type' => 'read',
                'testclientpath' => 'blocks/mhaairs/admin/testclient_forms.php',
        ),
);

// We define the services to install as pre-build services.
// A pre-build service is not editable by administrator.
$services = array(
        'MHAAIRS Gradebook Service' => array(
                'functions' => array (
                    'block_mhaairs_gradebookservice',
                    'block_mhaairs_update_grade',
                    'block_mhaairs_get_grade',
                ),
                'shortname' => 'mhaairs_gradebook',
                'restrictedusers'   => 0,
                'enabled'           => 0
        ),

        'MHAAIRS Util Service' => array(
                'functions' => array (
                    'block_mhaairs_get_user_info',
                    'block_mhaairs_validate_login',
                    'block_mhaairs_get_environment_info',
                ),
                'shortname' => 'mhaairs_util',
                'restrictedusers'   => 0,
                'enabled'           => 0
        ),
);
