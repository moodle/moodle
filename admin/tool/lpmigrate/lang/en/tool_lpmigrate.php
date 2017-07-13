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
 * Language strings.
 *
 * @package    tool_lpmigrate
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allowedcourses'] = 'Courses allowed';
$string['allowedcourses_help'] = 'Select courses to be migrated to the new framework. If no course is specified, then all courses will be migrated.';
$string['continuetoframeworks'] = 'Continue to frameworks';
$string['coursecompetencymigrations'] = 'Course competency migrations';
$string['coursemodulecompetencymigrations'] = 'Course activity and resource competency migrations';
$string['coursesfound'] = 'Courses found';
$string['coursemodulesfound'] = 'Course activities or resources found';
$string['coursestartdate'] = 'Courses start date';
$string['coursestartdate_help'] = 'If enabled, courses with a start date prior to the date specified will not be migrated.';
$string['disallowedcourses'] = 'Disallowed courses';
$string['disallowedcourses_help'] = 'Select any courses which should NOT be migrated to the new framework.';
$string['errorcannotmigratetosameframework'] = 'Cannot migrate from and to the same framework.';
$string['errorcouldnotmapcompetenciesinframework'] = 'Could not map to any competency in this framework.';
$string['errors'] = 'Errors';
$string['errorwhilemigratingcoursecompetencywithexception'] = 'Error while migrating the course competency: {$a}';
$string['errorwhilemigratingmodulecompetencywithexception'] = 'Error while migrating the activity or resource competency: {$a}';
$string['excludethese'] = 'Exclude these';
$string['explanation'] = 'This tool can be used to update a competency framework to a newer version. It searches for competencies in courses and activities using the older framework, and updates the links to point to the new framework.

It is not recommended to edit the old set of competencies directly, as this would change all of the competencies that have already been awarded in users\' learning plans.

Typically you would import the new version of a framework, hide the old framework, then use this tool to migrate new courses to the new framework.';
$string['findingcoursecompetencies'] = 'Finding course competencies';
$string['findingmodulecompetencies'] = 'Finding activity and resource competencies';
$string['frameworks'] = 'Frameworks';
$string['limittothese'] = 'Limit to these';
$string['lpmigrate:frameworksmigrate'] = 'Migrate frameworks';
$string['migrateframeworks'] = 'Migrate frameworks';
$string['migratefrom'] = 'Migrate from';
$string['migratefrom_help'] = 'Select the older framework currently in use.';
$string['migratemore'] = 'Migrate more';
$string['migrateto'] = 'Migrate to';
$string['migrateto_help'] = 'Select the newer version of the framework. It is only possible to select a framework which is not hidden.';
$string['migratingcourses'] = 'Migrating courses';
$string['missingmappings'] = 'Missing mappings';
$string['performmigration'] = 'Perform migration';
$string['pluginname'] = 'Competencies migration tool';
$string['results'] = 'Results';
$string['startdatefrom'] = 'Start date from';
$string['unmappedin'] = 'Unmapped in {$a}';
$string['warningcouldnotremovecoursecompetency'] = 'The course competency could not be removed.';
$string['warningcouldnotremovemodulecompetency'] = 'The activity or resource competency could not be removed.';
$string['warningdestinationcoursecompetencyalreadyexists'] = 'The destination course competency already exists.';
$string['warningdestinationmodulecompetencyalreadyexists'] = 'The destination activity or resource competency already exists.';
$string['warnings'] = 'Warnings';
