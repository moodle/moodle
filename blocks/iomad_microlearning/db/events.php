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
 * @package    block_iomad_microlearning
 * @copyright  2019 E-Learn Design (http://www.e-learndesign.co.uk)
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// List of observers.
$observers = array(

    array(
        'eventname'   => '\block_iomad_microlearning\event\thread_created',
        'callback'    => 'block_iomad_microlearning_observer::thread_created',
        'includefile' => '/blocks/iomad_microlearning/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_microlearning\event\thread_deleted',
        'callback'    => 'block_iomad_microlearning_observer::thread_deleted',
        'includefile' => '/blocks/iomad_microlearning/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_microlearning\event\thread_schedule_updated',
        'callback'    => 'block_iomad_microlearning_observer::thread_schedule_updated',
        'includefile' => '/blocks/iomad_microlearning/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_microlearning\event\thread_updated',
        'callback'    => 'block_iomad_microlearning_observer::thread_updated',
        'includefile' => '/blocks/iomad_microlearning/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_microlearning\event\nugget_created',
        'callback'    => 'block_iomad_microlearning_observer::nugget_created',
        'includefile' => '/blocks/iomad_microlearning/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_microlearning\event\nugget_deleted',
        'callback'    => 'block_iomad_microlearning_observer::nugget_deleted',
        'includefile' => '/blocks/iomad_microlearning/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_microlearning\event\nugget_moved',
        'callback'    => 'block_iomad_microlearning_observer::nugget_moved',
        'includefile' => '/blocks/iomad_microlearning/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_microlearning\event\nugget_updated',
        'callback'    => 'block_iomad_microlearning_observer::nugget_updated',
        'includefile' => '/blocks/iomad_microlearning/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\course_module_completion_updated',
        'callback'    => 'block_iomad_microlearning_observer::course_module_completion_updated',
        'includefile' => '/blocks/iomad_microlearning/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\user_deleted',
        'callback'    => 'block_iomad_microlearning_observer::user_deleted',
        'includefile' => '/blocks/iomad_microlearning/classes/observer.php',
        'internal'    => false,
    ),

);
