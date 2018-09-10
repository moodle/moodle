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
 * Instance settings form
 *
 * @package   block_checklist
 * @copyright 2010 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2018040200;
$plugin->requires = 2017111300; // Moodle 3.4+.
$plugin->release = '3.4+ (Build: 2018040200)';
$plugin->component = 'block_checklist';
$plugin->maturity = MATURITY_STABLE;
$plugin->dependencies = ['mod_checklist' => 2010041800]; // Must have checklist activity module installed.
