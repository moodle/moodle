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
 * Contains the version information for Panopto
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2016 with contributions from Spenser Jones (sjones@ambrose.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Initialize $plugin object if it hasn't been already.
$plugin = (isset($plugin) ? $plugin : new stdClass());

// Plugin version should normally be the same as the internal version.
// If an admin wants to install with an older version number, however, set that here.
$plugin->version = 2024120600;

// Requires this Moodle version - 4.1.0.
$plugin->requires = 2022112800;
$plugin->cron = 0;
$plugin->component = 'block_panopto';
$plugin->maturity = MATURITY_STABLE;
$plugin->dependencies = [
    'mod_forum' => ANY_VERSION,
];
/* End of file version.php */
