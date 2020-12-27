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
 * @package tool_generator
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['bigfile'] = 'Big file {$a}';
$string['courseexplanation'] = 'This tool creates standard test courses that include many
sections, activities, and files.

This is intended to provide a standardised measure for checking the reliability
and performance of various system components (such as backup and restore).

This test is important because there have been many cases previously where,
faced with real-life use cases (e.g. a course with 1,000 activities), the system
does not work.

Courses created using this feature can occupy a large amount of database and
filesystem space (tens of gigabytes). You will need to delete the courses
(and wait for various cleanup runs) to release this space again.

**Do not use this feature on a live system**. Use only on a developer server.
(To avoid accidental use, this feature is disabled unless you have also selected
DEVELOPER debugging level.)';

$string['coursesize_0'] = 'XS (~10KB; create in ~1 second)';
$string['coursesize_1'] = 'S (~10MB; create in ~30 seconds)';
$string['coursesize_2'] = 'M (~100MB; create in ~2 minutes)';
$string['coursesize_3'] = 'L (~1GB; create in ~30 minutes)';
$string['coursesize_4'] = 'XL (~10GB; create in ~2 hours)';
$string['coursesize_5'] = 'XXL (~20GB; create in ~4 hours)';
$string['coursewithoutusers'] = 'The selected course has no users';
$string['createcourse'] = 'Create course';
$string['createtestplan'] = 'Create test plan';
$string['creating'] = 'Creating course';
$string['done'] = 'done ({$a}s)';
$string['downloadtestplan'] = 'Download test plan';
$string['downloadusersfile'] = 'Download users file';
$string['error_nocourses'] = 'There are no courses to generate the test plan';
$string['error_noforumdiscussions'] = 'The selected course does not contain forum discussions';
$string['error_noforuminstances'] = 'The selected course does not contain forum module instances';
$string['error_noforumreplies'] = 'The selected course does not contain forum replies';
$string['error_nonexistingcourse'] = 'The specified course does not exist';
$string['error_nopageinstances'] = 'The selected course does not contain page module instances';
$string['error_notdebugging'] = 'Not available on this server because debugging is not set to DEVELOPER';
$string['error_nouserspassword'] = 'You need to set $CFG->tool_generator_users_password in config.php to generate the test plan';
$string['fullname'] = 'Test course: {$a->size}';
$string['maketestcourse'] = 'Make test course';
$string['maketestplan'] = 'Make JMeter test plan';
$string['notenoughusers'] = 'The selected course does not have enough users';
$string['pluginname'] = 'Development data generator';
$string['progress_checkaccounts'] = 'Checking user accounts ({$a})';
$string['progress_coursecompleted'] = 'Course completed ({$a}s)';
$string['progress_createaccounts'] = 'Creating user accounts ({$a->from} - {$a->to})';
$string['progress_createassignments'] = 'Creating assignments ({$a})';
$string['progress_createbigfiles'] = 'Creating big files ({$a})';
$string['progress_createcourse'] = 'Creating course {$a}';
$string['progress_createforum'] = 'Creating forum ({$a} posts)';
$string['progress_createpages'] = 'Creating pages ({$a})';
$string['progress_createsmallfiles'] = 'Creating small files ({$a})';
$string['progress_enrol'] = 'Enrolling users into course ({$a})';
$string['progress_sitecompleted'] = 'Site completed ({$a}s)';
$string['shortsize_0'] = 'XS';
$string['shortsize_1'] = 'S';
$string['shortsize_2'] = 'M';
$string['shortsize_3'] = 'L';
$string['shortsize_4'] = 'XL';
$string['shortsize_5'] = 'XXL';
$string['sitesize_0'] = 'XS (~10MB; 3 courses, created in ~30 seconds)';
$string['sitesize_1'] = 'S (~50MB; 8 courses, created in ~2 minutes)';
$string['sitesize_2'] = 'M (~200MB; 73 courses, created in ~10 minutes)';
$string['sitesize_3'] = 'L (~1\'5GB; 277 courses, created in ~1\'5 hours)';
$string['sitesize_4'] = 'XL (~10GB; 1065 courses, created in ~5 hours)';
$string['sitesize_5'] = 'XXL (~20GB; 4177 courses, created in ~10 hours)';
$string['size'] = 'Size of course';
$string['smallfiles'] = 'Small files';
$string['targetcourse'] = 'Test target course';
$string['testplanexplanation'] = 'This tool creates a JMeter test plan file along with the user credentials file.

This test plan is designed to work along with {$a}, which makes easier to run the test plan in a specific Moodle environment, gathers information about the runs and compares the results, so you will need to download it and use it\'s test_runner.sh script or follow the installation and usage instructions.

You need to set a password for the course users in config.php (e.g. $CFG->tool_generator_users_password = \'moodle\';). There is no default value for this password to prevent unintended usages of the tool. You need to use the update passwords option in case your course users have other passwords or they were generated by tool_generator but without setting a $CFG->tool_generator_users_password value.

It is part of tool_generator so it works well with the courses generated by the courses and the site generators, it can
also be used with any course that contains, at least:

* Enough enrolled users (depends on the test plan size you select) with the password reset to \'moodle\'
* A page module instance
* A forum module instance with at least one discussion and one reply

You might want to consider your servers capacity when running large test plans as the amount to load generated by JMeter
can be specially big. The ramp up period has been adjusted according to the number of threads (users) to reduce this kind
of issues but the load is still huge.

**Do not run the test plan on a live system**. This feature only creates the files to feed JMeter so is not dangerous by
itself, but you should **NEVER** run this test plan in a production site.

';
$string['testplansize_0'] = 'XS ({$a->users} users, {$a->loops} loops and {$a->rampup} rampup period)';
$string['testplansize_1'] = 'S ({$a->users} users, {$a->loops} loops and {$a->rampup} rampup period)';
$string['testplansize_2'] = 'M ({$a->users} users, {$a->loops} loops and {$a->rampup} rampup period)';
$string['testplansize_3'] = 'L ({$a->users} users, {$a->loops} loops and {$a->rampup} rampup period)';
$string['testplansize_4'] = 'XL ({$a->users} users, {$a->loops} loops and {$a->rampup} rampup period)';
$string['testplansize_5'] = 'XXL ({$a->users} users, {$a->loops} loops and {$a->rampup} rampup period)';
$string['updateuserspassword'] = 'Update course users password';
$string['updateuserspassword_help'] = 'JMeter needs to login as the course users, you can set the users password using $CFG->tool_generator_users_password in config.php; this setting updates the course user\'s password according to $CFG->tool_generator_users_password. It can be useful in case you are using a course not generated by tool_generator or $CFG->tool_generator_users_password was not set when you created the test courses.';
$string['privacy:metadata'] = 'The Development data generator plugin does not store any personal data.';
