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
 * @package   local_report_license_usage
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$observers = array(

    array(
        'eventname' => '\block_iomad_company_admin\event\user_license_assigned',
        'callback' => '\local_report_user_license_allocations\observer::user_license_assigned',
        'internal' => false,
    ),

    array(
        'eventname' => '\block_iomad_company_admin\event\user_license_unassigned',
        'callback' => '\local_report_user_license_allocations\observer::user_license_unassigned',
        'internal' => false,
    ),
);
