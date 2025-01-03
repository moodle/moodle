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
 * Plugin services.
 *
 * @package     local_aiquestions
 * @category    admin
 * @copyright   2023 Ruthy Salomon <ruthy.salomon@gmail.com> , Yedidia Klein <yedidia@openapp.co.il>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Define the web service functions of our plugin.
$functions = [
    // The name of your web service function.
    'local_aiquestions_check_state' => [
        'classname'   => 'local_aiquestions\external\check_state',
        'description' => 'Check state of questions generation',
        'type'        => 'read',
        'ajax'        => true,
    ],
];

// Define the services and functions.
$services = array(
    'AI Questions Services' => array(
            'functions' => array ('local_aiquestions_check_state'),
            'restrictedusers' => 0,
            'enabled' => 1,
    )
);
