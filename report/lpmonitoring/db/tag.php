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
 * Tag areas in component report_lpmonitoring
 *
 * @package   report_lpmonitoring
 * @copyright 2018 Université de Montréal
 * @author    Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tagareas = [
    [
        'itemtype' => 'competency_plan',
        'component' => 'report_lpmonitoring',
        'callback' => 'report_lpmonitoring_get_tagged_records',
        'callbackfile' => '/report/lpmonitoring/locallib.php',
    ],
];
