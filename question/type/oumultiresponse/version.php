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
 * OU multiple response question type version file.
 *
 * @package   qtype_oumultiresponse
 * @copyright 2008 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


$plugin->version   = 2025071500;
$plugin->requires  = 2024042200;
$plugin->component = 'qtype_oumultiresponse';
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '2.5 for Moodle 4.4+';

$plugin->dependencies = [
    'qtype_multichoice' => 2024042200,
];

$plugin->outestssufficient = true;
