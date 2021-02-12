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
 * @package   local_iomad_signup
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$observers = array(

    array(
        'eventname' => 'core\event\course_completed',
        'callback' => '\local_iomad_track\observer::course_completed',
        'internal' => false,
    ),

    array(
        'eventname' => 'core\event\course_updated',
        'callback' => '\local_iomad_track\observer::course_updated',
        'internal' => false,
    ),

    array(
        'eventname' => '\block_iomad_company_admin\event\company_license_updated',
        'callback' => '\local_iomad_track\observer::company_license_updated',
        'internal' => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\user_license_assigned',
        'callback'    => '\local_iomad_track\observer::user_license_assigned',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\user_license_unassigned',
        'callback'    => '\local_iomad_track\observer::user_license_unassigned',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\user_license_unassigned',
        'callback'    => '\local_iomad_track\observer::user_license_unassigned',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\company_user_assigned',
        'callback'    => '\local_iomad_track\observer::company_user_assigned',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\company_course_updated',
        'callback'    => '\local_iomad_track\observer::company_course_updated',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\user_enrolment_created',
        'callback'    => '\local_iomad_track\observer::user_enrolment_created',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\block_iomad_company_admin\event\user_license_used',
        'callback'    => '\local_iomad_track\observer::user_license_used',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\user_enrolment_deleted',
        'callback'    => '\local_iomad_track\observer::user_enrolment_deleted',
        'internal'    => false,
    ),

    array(
        'eventname'   => '\core\event\user_graded',
        'callback'    => '\local_iomad_track\observer::user_graded',
        'internal'    => false,
    ),
);
