<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * Code fragment to define the version of the iomadcertificate module
 *
 * @package    mod_iomadcertificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2016052300; // The current module version (Date: YYYYMMDDXX)
$plugin->requires  = 2019052000; // Requires this Moodle version (3.1)
$plugin->cron      = 0; // Period for cron to check this module (secs)
$plugin->component = 'mod_iomadcertificate';

$plugin->maturity  = MATURITY_STABLE;
$plugin->release  = '3.8.3 (Build: 20200511)'; // Human-friendly version name
