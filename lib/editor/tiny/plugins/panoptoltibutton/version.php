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
 * Panopto LTI Button version.
 *
 * @copyright Panopto 2009 - 2023
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// The current plugin version (Date: YYYYMMDDXX).
$plugin->version = 2024120600;

// Requires this Moodle version - 4.1.0
$plugin->requires = 2022112800;

// Full name of the plugin (used for diagnostics).
$plugin->component = 'tiny_panoptoltibutton';

// Supported versions.
$plugin->supported = [
    // Support from the Moodle 4.1 series.
    401,

    // To the Moodle 4.5 series.
    405,
];

// This is considered as ready for production sites.
$plugin->maturity = MATURITY_BETA;

// Dependencies.
$plugin->dependencies = [
    'block_panopto' => 2022122000,
    'mod_lti' => ANY_VERSION,
];
