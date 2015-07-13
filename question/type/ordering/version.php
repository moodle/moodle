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
 * Version information for the ORDERING question type
 *
 * @package    qtype
 * @subpackage ordering
 * @copyright  2010 Thomas Robb
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// prevent direct access to this script
defined('MOODLE_INTERNAL') || die();

$plugin->cron      = 0;
$plugin->component = 'qtype_ordering';
$plugin->maturity  = MATURITY_STABLE; // ALPHA=50, BETA=100, RC=150, STABLE=200
$plugin->release   = '2015-07-13 (23)';
$plugin->version   = 2015071323;
$plugin->requires  = 2010112400; // Moodle 2.0
