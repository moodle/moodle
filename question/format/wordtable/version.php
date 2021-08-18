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
 * Code fragment to define the version of wordtable for Moodle 2.x
 *
 * This fragment is called by moodle_needs_upgrading() and /admin/index.php
 *
 * @package    qformat_wordtable
 * @copyright  2010-2016 Eoin Campbell
 * @author     Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 **/


defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2020071101;        // The current plugin version (Date: YYYYMMDDXX).
$plugin->maturity  = MATURITY_STABLE;  // Maturity level.
$plugin->component  = 'qformat_wordtable';  // Plugin name.
$plugin->release  = '3.6.3 (Build: 2020071101)';  // The current module release in human-readable form (x.y).
$plugin->requires = 2011070100.03;  // Requires Moodle 2.1 or later.
$plugin->cron     = 0;           // Period for cron to check this module (secs).
