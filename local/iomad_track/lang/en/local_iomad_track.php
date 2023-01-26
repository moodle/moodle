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

$string['pluginname'] = 'IOMAD completion tracking';
$string['privacy:metadata'] = 'The Local IOMAD completion tracking plugin only shows data stored in other locations.';
$string['privacy:metadata:local_iomad_track:id'] = 'Local IOMAD track id';
$string['privacy:metadata:local_iomad_track:courseid'] = 'Course id';
$string['privacy:metadata:local_iomad_track:coursename'] = 'Course name.';
$string['privacy:metadata:local_iomad_track:userid'] = 'User id';
$string['privacy:metadata:local_iomad_track:companyid'] = 'User company id';
$string['privacy:metadata:local_iomad_track:timecompleted'] = 'Course time completed';
$string['privacy:metadata:local_iomad_track:timeenrolled'] = 'Course time enrolled';
$string['privacy:metadata:local_iomad_track:timestarted'] = 'Course time started';
$string['privacy:metadata:local_iomad_track:finalscore'] = 'Course final score';
$string['privacy:metadata:local_iomad_track:licenseid'] = 'Licese id';
$string['privacy:metadata:local_iomad_track:licensename'] = 'License name';
$string['privacy:metadata:local_iomad_track:licenseallocated'] = 'Unix timestamp of time license was allocated';
$string['privacy:metadata:local_iomad_track:modifiedtime'] = 'Record modified time';
$string['privacy:metadata:local_iomad_track'] = 'Local iomad track user information';
$string['privacy:metadata:local_iomad_track_certs:id'] = 'Local iomad track certificate record id';
$string['privacy:metadata:local_iomad_track_certs:trackid'] = 'Certificate track id';
$string['privacy:metadata:local_iomad_track_certs:filename'] = 'Certificate filename';
$string['privacy:metadata:local_iomad_track_certs'] = 'Local iomad track certificate info';
$string['fixtracklicensetask'] = 'IOMAD track fix license tracking details adhoc task';
$string['iomad_track:importfrommoodle'] = 'Import completion information from Moodle tables';
$string['importcompletionsfrommoodle'] = 'Import stored completion information from Moodle tables';
$string['importcompletionsfrommoodlefull'] = 'This will run an AdHoc task to import all of the completion information from Moodle to the IOMAD reporting tables';
$string['importcompletionsfrommoodlefullwitherrors'] = 'This will run an AdHoc task to import SOME of the completion information from Moodle to the IOMAD reporting tables. Not all courses have completion enabled or criteria set up and their information will be missed out.  If you want to know which courses these are use the check link on the previous page';
$string['importmoodlecompletioninformation'] = 'Adhoc task to import completion information from Moodle tables';
$string['fixenrolleddatetask'] = 'Adhoc task to update the stored completion information to use the enrolment timecreated timestamp where this is not already set.';
$string['fixcourseclearedtask'] = 'Adhoc task to update the coursecleared field in the stored completion records';
$string['fixtracklicensetask'] = 'Adhoc task to fix stored records license information';
$string['importcompletionrecords'] = 'Import completion records';
$string['uploadcompletionresult'] = 'Upload completion file result';
$string['completionimportfromfile'] = 'Completion import from file';
$string['importcompletionsfromfile'] = 'Import completion information from file';
$string['courseswithoutcompletionenabledcouunt'] = 'Number of courses which do not have completion enable = {$a}';
$string['courseswithoutcompletioncriteriacouunt'] ='Number of courses which have no completion criteria = {$a}';
$string['checkcoursestatusmoodle'] = 'Check course settings for import';
