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
 * @package   mod_iomadcertificate
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @basedon   mod_certificate by Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(

    'mod_iomadcertificate_get_iomadcertificates_by_courses' => array(
        'classname'     => 'mod_iomadcertificate_external',
        'methodname'    => 'get_iomadcertificates_by_courses',
        'description'   => 'Returns a list of iomadcertificate instances in a provided set of courses, if
                            no courses are provided then all the iomadcertificate instances the user has access to will be returned.',
        'type'          => 'read',
        'capabilities'  => 'mod/iomadcertificate:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile'),
    ),

    'mod_iomadcertificate_view_iomadcertificate' => array(
        'classname'     => 'mod_iomadcertificate_external',
        'methodname'    => 'view_iomadcertificate',
        'description'   => 'Trigger the course module viewed event and update the module completion status.',
        'type'          => 'write',
        'capabilities'  => 'mod/iomadcertificate:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile'),
    ),

    'mod_iomadcertificate_issue_iomadcertificate' => array(
        'classname'     => 'mod_iomadcertificate_external',
        'methodname'    => 'issue_iomadcertificate',
        'description'   => 'Create new iomadcertificate record, or return existing record for the current user.',
        'type'          => 'write',
        'capabilities'  => 'mod/iomadcertificate:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile'),
    ),

    'mod_iomadcertificate_get_issued_iomadcertificates' => array(
        'classname'     => 'mod_iomadcertificate_external',
        'methodname'    => 'get_issued_iomadcertificates',
        'description'   => 'Get the list of issued iomadcertificates for the current user.',
        'type'          => 'read',
        'capabilities'  => 'mod/iomadcertificate:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile'),
    ),
);
