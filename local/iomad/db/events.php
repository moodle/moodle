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
 * Add event handlers for the quiz
 *
 * @package    local_iomad
 * @copyright  2016 E-Learn Design (http://www.e-learndesign.co.uk)
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/iomad/classes/observer.php');

// List of observers.
$observers = array(

    array(
        'eventname'   => '\core\event\competency_framework_created',
        'callback'    => 'local_iomad_observer::competency_framework_created',
    ),

    array(
        'eventname'   => '\core\event\competency_framework_deleted',
        'callback'    => 'local_iomad_observer::competency_framework_deleted',
    ),

    array(
        'eventname'   => '\core\event\competency_template_created',
        'callback'    => 'local_iomad_observer::competency_template_created',
    ),

    array(
        'eventname'   => '\core\event\competency_template_deleted',
        'callback'    => 'local_iomad_observer::competency_template_deleted',
    ),

    array(
        'eventname'   => '\core\event\course_completed',
        'callback'    => 'local_iomad_observer::course_completed',
    ),
);

