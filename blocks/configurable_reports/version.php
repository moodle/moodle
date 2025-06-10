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
 * Version details
 *
 * Configurable Reports - A Moodle block for creating customizable reports
 *
 * @package       block_configurable_reports
 * @author        Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @copyright     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2024051300;
$plugin->requires = 2017111300;
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '4.1.0';
$plugin->supported = [400, 401];
$plugin->component = 'block_configurable_reports';
$plugin->cron = 86400;
