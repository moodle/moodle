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

// List of observers.
$observers = array(

    array(
        'eventname'   => '\block_iomad_company_admin\event\company_created',
        'callback'    => 'local_iomad_observer::company_created',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\company_license_created',
        'callback'    => 'local_iomad_observer::company_license_created',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\company_license_deleted',
        'callback'    => 'local_iomad_observer::company_license_deleted',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\company_license_updated',
        'callback'    => 'local_iomad_observer::company_license_updated',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\company_suspended',
        'callback'    => 'local_iomad_observer::company_suspended',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\company_unsuspended',
        'callback'    => 'local_iomad_observer::company_unsuspended',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\user_deleted',
        'callback'    => 'local_iomad_observer::user_deleted',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\company_updated',
        'callback'    => 'local_iomad_observer::company_updated',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\company_user_assigned',
        'callback'    => 'local_iomad_observer::company_user_assigned',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\company_user_unassigned',
        'callback'    => 'local_iomad_observer::company_user_unassigned',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\competency_framework_created',
        'callback'    => 'local_iomad_observer::competency_framework_created',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\competency_framework_deleted',
        'callback'    => 'local_iomad_observer::competency_framework_deleted',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\competency_template_created',
        'callback'    => 'local_iomad_observer::competency_template_created',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\competency_template_deleted',
        'callback'    => 'local_iomad_observer::competency_template_deleted',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\course_completed',
        'callback'    => 'local_iomad_observer::course_completed',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\user_enrolment_created',
        'callback'    => 'local_iomad_observer::user_enrolment_created',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\user_created',
        'callback'    => 'local_iomad_observer::user_created',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\user_deleted',
        'callback'    => 'local_iomad_observer::user_deleted',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\user_license_assigned',
        'callback'    => 'local_iomad_observer::user_license_assigned',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\user_license_unassigned',
        'callback'    => 'local_iomad_observer::user_license_unassigned',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\user_license_used',
        'callback'    => 'local_iomad_observer::user_license_used',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\user_course_expired',
        'callback'    => 'local_iomad_observer::user_course_expired',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\user_suspended',
        'callback'    => 'local_iomad_observer::user_suspended',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\user_unsuspended',
        'callback'    => 'local_iomad_observer::user_unsuspended',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\user_updated',
        'callback'    => 'local_iomad_observer::user_updated',
        'includefile' => '/local/iomad/classes/observer.php',
        'internal'    => false,
    ),
);
