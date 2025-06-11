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
 * Board module version identification.
 * @package     mod_board
 * @author      Karen Holland <karen@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$plugin->component = 'mod_board'; // Full name of the plugin (used for diagnostics).
$plugin->version   = 2024102002; // The current module version Use 2024.10.20 as base for 4.04.
$plugin->requires  = 2024042200; // Moodle 4.04 and up.
$plugin->release = '1.404.02';
$plugin->maturity  = MATURITY_STABLE;
