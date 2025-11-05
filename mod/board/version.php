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

/** @var stdClass $plugin */
$plugin->component = 'mod_board'; // Full name of the plugin (used for diagnostics).
$plugin->version   = 2025070714; // The current module version Use 2025.07.07 as base for 4.5.
$plugin->requires  = 2024100700; // Moodle 4.5.0 and up.
$plugin->release = '1.405.00';
$plugin->maturity  = MATURITY_STABLE;
$plugin->supported = [405, 500];
