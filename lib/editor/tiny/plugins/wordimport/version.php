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
 * Plugin version and other meta-data are defined here.
 *
 * @package     tiny_wordimport
 * @copyright   2024 University of Graz
 * @author      André Menrath <andre.menrath@uni-graz.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component    = 'tiny_wordimport';
$plugin->release      = '1.1.3';
$plugin->version      = 2025043000;
$plugin->requires     = 2022112803; // Requires Moodle 4.1.3 or higher.
$plugin->dependencies = ['booktool_wordimport' => 2025042300];
$plugin->maturity     = MATURITY_STABLE;
$plugin->supports     = [401, 500];
