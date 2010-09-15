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
 * Cohort enrolment plugin event handler definition.
 *
 * @package    enrol
 * @subpackage cohort
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/* List of handlers */
$handlers = array (
    'cohort_member_added' => array (
        'handlerfile'      => '/enrol/cohort/locallib.php',
        'handlerfunction'  => array('enrol_cohort_handler', 'member_added'),
        'schedule'         => 'instant',
        'internal'         => 1,
    ),

    'cohort_member_removed' => array (
        'handlerfile'      => '/enrol/cohort/locallib.php',
        'handlerfunction'  => array('enrol_cohort_handler', 'member_removed'),
        'schedule'         => 'instant',
        'internal'         => 1,
    ),

    'cohort_deleted' => array (
        'handlerfile'      => '/enrol/cohort/locallib.php',
        'handlerfunction'  => array('enrol_cohort_handler', 'deleted'),
        'schedule'         => 'instant',
        'internal'         => 1,
    ),
);
