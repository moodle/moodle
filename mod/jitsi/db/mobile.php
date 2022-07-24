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

 * Jitsi module capability definition
 *
 * @package    mod_jitsi
 * @copyright  2021 Arnes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$addons = [
    'mod_jitsi' => [ // Plugin identifier.
        'handlers' => [ // Different places where the plugin will display content.
            'jitsimeeting' => [ // Handler unique name (alphanumeric).
                'displaydata' => [
                    'icon' => $CFG->wwwroot . '/mod/jitsi/pix/icon.svg',
                    'class' => '',
                ],
                'delegate' => 'CoreCourseModuleDelegate', // Delegate (where to display the link to the plugin).
                'method' => 'mobile_presession_view', // Main function in \mod_jitsi\output\mobile.
                'offlinefunctions' => [
                    'mobile_presession_view' => [],
                    'mobile_session_view' => [],
                ], // Function that needs to be downloaded for offline.
            ],
        ],
        'lang' => [ // Language strings that are used in all the handlers.
            ['pluginname', 'jitsi'],
            ['instruction', 'jitsi'],
            ['access', 'jitsi'],
            ['nostart', 'jitsi'],
            ['finish', 'jitsi'],
            ['buttonopeninbrowser', 'jitsi'],
            ['buttonopenwithapp', 'jitsi'],
            ['buttondownloadapp', 'jitsi'],
            ['appaccessinfo', 'jitsi'],
            ['appinstalledtext', 'jitsi'],
            ['appnotinstalledtext', 'jitsi'],
            ['desktopaccessinfo', 'jitsi']
        ],
    ],
];
