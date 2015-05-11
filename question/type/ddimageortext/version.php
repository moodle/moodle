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
 * Version information for the drag-and-drop onto image question type.
 *
 * @package   qtype_ddimageortext
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2015051000;
$plugin->requires  = 2013101800;
$plugin->cron      = 0;
$plugin->component = 'qtype_ddimageortext';
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.8 for Moodle 2.6+';

$plugin->dependencies = array(
    'qtype_gapselect' => 2014111200,
);

$plugin->outestssufficient = true;
