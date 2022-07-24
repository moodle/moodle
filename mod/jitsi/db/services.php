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
 * @package    mod_jitsi
 * @copyright  2021 Sergio Comerón Sánchez-Paniagua <sergiocomeron@icloud.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
defined('MOODLE_INTERNAL') || die();

$functions = array(
        'mod_jitsi_state_record' => array(
                'classname'   => 'mod_jitsi_external',
                'methodname'  => 'state_record',
                'classpath'   => 'mod/jitsi/externallib.php',
                'description' => 'State session recording',
                'type'        => 'write',
                'ajax'        => true,
                'loginrequired' => false,
                'services' => [MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile'],
        ),

        'mod_jitsi_participating_session' => array(
                'classname'   => 'mod_jitsi_external',
                'methodname'  => 'participating_session',
                'classpath'   => 'mod/jitsi/classes/external.php',
                'description' => 'State session recording',
                'type'        => 'write',
                'ajax'        => true,
                'loginrequired' => false,
                'services' => [MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile'],
        ),

        'mod_jitsi_create_stream' => array(
                'classname'   => 'mod_jitsi_external',
                'methodname'  => 'create_stream',
                'classpath'   => 'mod/jitsi/classes/external.php',
                'description' => 'Create a stream',
                'type'        => 'write',
                'ajax'        => true,
                'loginrequired' => false,
                'services' => [MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile'],
        ),

        'mod_jitsi_view_jitsi' => array(
                'classname'     => 'mod_jitsi_external',
                'methodname'    => 'view_jitsi',
                'description'   => 'Trigger the course module viewed event.',
                'type'          => 'write',
                'capabilities'  => 'mod/jitsi:view',
                'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile'),
        ),
);
