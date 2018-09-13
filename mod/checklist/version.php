<?php
// This file is part of the Checklist plugin for Moodle - http://moodle.org/
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
 * Code fragment to define the version of checklist
 * This fragment is called by moodle_needs_upgrading() and /admin/index.php
 *
 * @author  Davo Smith <moodle@davosmith.co.uk>
 * @package mod/checklist
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

$plugin->version = 2018042100;  // The current module version (Date: YYYYMMDDXX).
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '3.4+ (Build: 2018042100)';
$plugin->requires = 2017111300; // Moodle 3.4+.
$plugin->component = 'mod_checklist';
