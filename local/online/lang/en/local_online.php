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

// Setting strings.
$string['pluginname'] = 'ONLINE Enrollment Provider';
$string['pluginname_desc'] = 'ONLINE enrollment provider enhances the UES enrollment
process with ONLINE enrollment information. The provider will give a link to
admins to allow them to manually process student meta information:

- Perform [reprocess]({$a->reprocessurl})
';

$string['credential_location'] = 'Credential Location';
$string['credential_location_desc'] = 'For security purposes, the login credentials for the ONLINE web service is stored on a local secure server. This is the complete url to access the credentials.';

$string['wsdl_location'] = 'SOAP WSDL';
$string['wsdl_location_desc'] = 'This is the wsdl used in SOAP requests to ONLINE\'s Data Access Service. The Moodle data directory *{$a->dataroot}* is assumed as the path base.';

$string['student_data'] = 'Process Student Data';
$string['student_data_desc'] = 'This will enable processing student data in the `postprocess` section of the ONLINE provider';

$string['anonymous_numbers'] = 'Process LAW Numbers';
$string['anonymous_numbers_desc'] = 'This will enable processing anonymous numbers in the `postprocess` section of the ONLINE provider';

$string['degree_candidates'] = 'Process Degree Candidacy';
$string['degree_candidates_desc'] = 'This will enabled processing degree candidate information in the `postprocess` section of the ONLINE provider';

$string['sports_information'] = 'Sports Information';
$string['sports_information_desc'] = 'This will enable the pulling of student athletic information in the `postprocess` section of the ONLINE provider';

$string['semester_source'] = 'Semester serviceId';
$string['semester_source_desc'] = 'The web service id for campus semesters';

$string['course_source'] = 'Courses serviceId';
$string['course_source_desc'] = 'The web service id for courses per semester';

$string['teacher_by_department'] = 'Dept instructors serviceId';
$string['teacher_by_department_desc'] = 'The web service id for all instructors in a given department';

$string['student_by_department'] = 'Dept students serviceId';
$string['student_by_department_desc'] = 'The web service id for all students in a given department';

$string['teacher_source'] = 'Sec instructor serviceId';
$string['teacher_source_desc'] = 'The web service id for all instructors in a given section';

$string['student_source'] = 'Sec student serviceId';
$string['student_source_desc'] = 'The web service id for all students in a given section';

$string['student_data_source'] = 'Student data serviceId';
$string['student_data_source_desc'] = 'the web service id for all student data in a given semester';

$string['student_degree_source'] = 'Degree candidate serviceId';
$string['student_degree_source_desc'] = 'The web service id for degree candidate info for a given semester';

$string['student_anonymous_source'] = 'Anonymous # serviceId';
$string['student_anonymous_source_desc'] = 'The web service id for anonymous numbers';

$string['student_ath_source'] = 'Athlete info serviceId';
$string['student_ath_source_desc'] = 'The web service id for student athletes';

// Error strings.
$string['bad_file'] = 'Provide a *.wsdl* file';
$string['no_file'] = 'The WSDL does not exists in wsdl_location';
$string['bad_url'] = 'Provide a valid url (define either a http or https protocol)';
$string['bad_resp'] = 'Invalid credentials in credential location request';

// Reprocess strings.
$string['no_permission'] = 'You do no have sufficient permission to access this page.';
$string['reprocess'] = 'Reprocess Student Data';
$string['reprocess_confirm'] = 'You are about to reprocess student meta
information for all recognized semesters in session. Continue?';
