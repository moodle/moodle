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
 * Label module version info
 *
 * @package    mod
 * @subpackage Training Event
 * @copyright  2014 E-Learn Design Ltd. {@link https://www.e-learndesign.co.uk}
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$plugin->release  = '4.3.3+ (Build: 20240301)'; // Human-friendly version name
$plugin->component  = 'mod_trainingevent';
$plugin->version  = 2024030100;  // The current module version (Date: YYYYMMDDXX).
$plugin->requires = 2019052000;  // Requires this Moodle version.
$plugin->cron     = 0;           // Period for cron to check this module (secs).
