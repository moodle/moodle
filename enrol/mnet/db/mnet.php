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
 * This file contains the mnet services for the mnet enrolment plugin
 *
 * If we rewrite MNet as proposed in MDL-21993 this file would contain
 * just a declaration of xml-rpc methods that this plugin publishes.
 *
 * @since      2.0
 * @package    enrol
 * @subpackage mnet
 * @copyright  2010 Penny Leach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$publishes = array(
    'mnet_enrol' => array(
        'apiversion' => 1,
        'classname'  => 'enrol_mnet_mnetservice_enrol',
        'filename'   => 'enrol.php',
        'methods'    => array(
            'available_courses',
            'user_enrolments',
            'enrol_user',
            'unenrol_user',
            'course_enrolments'
        ),
    ),
);
$subscribes = array(
    'mnet_enrol' => array(
        'available_courses' => 'enrol/mnet/enrol.php/available_courses',
        'user_enrolments'   => 'enrol/mnet/enrol.php/user_enrolments',
        'enrol_user'        => 'enrol/mnet/enrol.php/enrol_user',
        'unenrol_user'      => 'enrol/mnet/enrol.php/unenrol_user',
        'course_enrolments' => 'enrol/mnet/enrol.php/course_enrolments',
    ),
);
