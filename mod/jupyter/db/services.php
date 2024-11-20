<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Jupyter external functions and service definitions.
 *
 * @package     mod_jupyter
 * @category    external
 * @copyright   KIB3 StuPro SS2022 Development Team of the University of Stuttgart
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$functions = [
    // The name of your web service function, as discussed above.
    'mod_jupyter_submit_notebook' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => 'mod_jupyter\external\submit_notebook',

        // A brief, human-readable, description of the web service function.
        'description' => 'Submits notebookfile for autograding.',

        // Options include read, and write.
        'type'        => 'write',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            // A standard Moodle install includes one default service:
            // - MOODLE_OFFICIAL_MOBILE_SERVICE.
            // Specifying this service means that your function will be available for
            // use in the Moodle Mobile App.
            MOODLE_OFFICIAL_MOBILE_SERVICE,
        ]
    ],
        // The name of your web service function, as discussed above.
        'mod_jupyter_reset_notebook' => [
            // The name of the namespaced class that the function is located in.
            'classname'   => 'mod_jupyter\external\reset_notebook',

            // A brief, human-readable, description of the web service function.
            'description' => 'Reset notebookfile to its default version by reupload. Renames any naming collisions.',

            // Options include read, and write.
            'type'        => 'write',

            // Whether the service is available for use in AJAX calls from the web.
            'ajax'        => true,

            // An optional list of services where the function will be included.
            'services' => [
                // A standard Moodle install includes one default service:
                // - MOODLE_OFFICIAL_MOBILE_SERVICE.
                // Specifying this service means that your function will be available for
                // use in the Moodle Mobile App.
                MOODLE_OFFICIAL_MOBILE_SERVICE,
            ]
        ],
    ];

$services = [
    // The name of the service.
    // This does not need to include the component name.
    'JupyterHub' => [

        // A list of external functions available in this service.
        'functions' => [
            'mod_jupyter_submit_notebook',
            'mod_jupyter_reset_notebook',
        ],

        // If enabled, the Moodle administrator must link a user to this service from the Web UI.
        'restrictedusers' => 0,

        // Whether the service is enabled by default or not.
        'enabled' => 1,

        // Whether to allow file downloads.
        'downloadfiles' => 0,

        // Whether to allow file uploads.
        'uploadfiles'  => 0,
    ]
];

