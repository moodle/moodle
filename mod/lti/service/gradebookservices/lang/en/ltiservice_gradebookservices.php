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
 * Strings for component 'ltiservice_gradebookservices', language 'en'
 *
 * @package    ltiservice_gradebookservices
 * @copyright  2017 Cengage Learning http://www.cengage.com
 * @author     Dirk Singels, Diego del Blanco, Claude Vervoort
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['alwaysgs'] = 'Use this service for grade sync and column management ';
$string['grade_synchronization'] = 'IMS LTI Assignment and Grade Services';
$string['grade_synchronization_help'] = 'Whether to use the IMS LTI Assignment and Grade Services to synchronise grades instead of the Basic Outcomes service.

* **Do not use this service** -  Basic Outcomes features and configuration will be used
* **Use this service for grade sync only** - The service will populate the grades in an already existing gradebook column, but it will not be able to create new columns
* **Use this service for grade sync and column management** -  The service will be able to create and update gradebook columns and manage the grades.';
$string['ltiservice_gradebookservices'] = 'IMS LTI Assignment and Grade Services';
$string['modulename'] = 'LTI Grades';
$string['nevergs'] = 'Do not use this service';
$string['partialgs'] = 'Use this service for grade sync only';
$string['pluginname'] = 'LTI Assignment and Grade Services';
$string['privacy:metadata:externalpurpose'] = 'This information is sent to an external LTI provider.';
$string['privacy:metadata:feedback'] = 'The feedback the user received for this LTI activity.';
$string['privacy:metadata:grade'] = 'The grade the user received in Moodle for this LTI activity.';
$string['privacy:metadata:maxgrade'] = 'The max grade that can be achieved for this LTI activity.';
$string['privacy:metadata:timemodified'] = 'The last time the grade was updated';
$string['privacy:metadata:userid'] = 'The ID of the user using the LTI consumer.';
$string['taskcleanup'] = 'LTI Assignment and Grade Services table cleanup';
