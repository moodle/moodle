<?php // $Id$ 

$string['enrolname'] = 'External Database';
$string['description'] = 'You can use a external database (of nearly any kind) to control your enrolments. It is assumed your external database contains a field containing a course ID, and a field containing a user ID. These are compared against fields that you choose in the local course and user tables.';
$string['server_settings'] = 'External Database Server Settings';
$string['type'] = 'Database server type.';
$string['host'] = 'Database server hostname.';
$string['name'] = 'The specific database to use.';
$string['user'] = 'Username to access the server.';
$string['pass'] = 'Password to access the server.';
$string['local_fields_mapping'] = 'Moodle (local) database fields';
$string['local_coursefield'] = 'The name of the field in the course table that we are using to match entries in the remote database (eg idnumber).';
$string['remote_fields_mapping'] = 'Enrolment (remote) database fields.';
$string['student_table'] = 'The name of the table where student enrolments are stored.';
$string['student_coursefield'] = 'The name of the field in the student enrolment table that we expect to find the course ID in.';
$string['student_l_userfield'] = 'The name of the field in the local user table that we use to match the user to a remote record for students (eg idnumber).';
$string['student_r_userfield'] = 'The name of the field in the remote student enrolment table that we expect to find the user ID in.';
$string['teacher_table'] = 'The name of the table where teacher enrolments are stored.';
$string['teacher_coursefield'] = 'The name of the field in the teacher enrolment table that we expect to find the course ID in.';
$string['teacher_l_userfield'] = 'The name of the field in the local user table that we use to match the user to a remote record for teachers (eg idnumber).';
$string['teacher_r_userfield'] = 'The name of the field in the remote teacher enrolment table that we expect to find the user ID in.';
$string['autocreation_settings'] = 'Autocreation Settings';
$string['autocreate'] = 'Courses can be created automatically if there are enrolments to a course that doesn\'t yet exist in Moodle.';
$string['category'] = 'The category for auto-created courses.';
$string['template'] = 'Optional: auto-created courses can copy their settings from a template course. Type here the shortname of the template course.';
$string['course_table'] = 'Then name of the table where we expect to find the course details in (short name, fullname, ID, etc.)';
$string['course_fullname'] = 'The name of the field where the course fullname is stored.';
$string['course_shortname'] = 'The name of the field where the course shortname is stored.';
$string['course_id'] = 'The name of the field where the course ID is stored. The values of this field are used to match those in the \"enrol_db_l_coursefield\" field in Moodle\'s course table.';
$string['general_options'] = 'General Options';
?>
