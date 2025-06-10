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
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localml
 * @copyright  2017 Macmillan Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// We defined the web service functions to install.
defined('MOODLE_INTERNAL') || die();
$functions = array(
        'local_ml_create_assignment' => array(
            'classname'   => 'local_ml_external',
            'methodname'  => 'create_assignment',
            'classpath'   => 'local/ml/externallib.php',
            'description' => 'Creates new assignment.',
            'type'        => 'write',
        ),
        'local_ml_create_external_tool' => array(
            'classname'   => 'local_ml_external',
            'methodname'  => 'create_external_tool',
            'classpath'   => 'local/ml/externallib.php',
            'description' => 'Creates new external tool.',
            'type'        => 'read',
        ),
        'local_ml_get_course_lti' => array(
            'classname'   => 'local_ml_external',
            'methodname'  => 'get_course_lti',
            'classpath'   => 'local/ml/externallib.php',
            'description' => 'Returns course lti.',
            'type'        => 'read',
        ),
        'local_ml_delete_module' => array(
            'classname'   => 'local_ml_external',
            'methodname'  => 'delete_module',
            'classpath'   => 'local/ml/externallib.php',
            'description' => 'Deletes module',
            'type'        => 'read',
        ),
        'local_ml_delete_assignment' => array(
            'classname'   => 'local_ml_external',
            'methodname'  => 'delete_assignment',
            'classpath'   => 'local/ml/externallib.php',
            'description' => 'Deletes assignment instance',
            'type'        => 'read',
        ),
        'local_ml_update_external_tool' => array(
            'classname'   => 'local_ml_external',
            'methodname'  => 'update_external_tool',
            'classpath'   => 'local/ml/externallib.php',
            'description' => 'Updates external tool',
            'type'        => 'read',
        ),
        'local_ml_update_grade_item_name' => array(
            'classname'   => 'local_ml_external',
            'methodname'  => 'update_grade_item_name',
            'classpath'   => 'local/ml/externallib.php',
            'description' => 'Updates grade item name.',
            'type'        => 'write',
        )
);

// We define the services to install as pre-build services.
// A pre-build service is not editable by administrator.
$services = array(
        // The index is the name that shows up under Built-in services in External Services.
        'Macmillan Learning service' => array(
                'functions' => array ('local_ml_create_assignment',
                                      'local_ml_create_external_tool',
                                      'local_ml_get_course_lti',
                                      'local_ml_update_grade_item_name'),
                // If set, the web service user need this capability to access
                // any function of this service. For example: 'some/capability:specified'
                // If enabled, the Moodle administrator must link some user to this service
                // into the administration.
                'restrictedusers' => 0,
                // If enabled, the service can be reachable on a default installation.
                'enabled' => 1,
        )
);
