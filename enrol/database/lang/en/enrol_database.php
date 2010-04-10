<?PHP // $Id$ 
      // enrol_database.php - created with Moodle 1.7 beta + (2006101003)


$string['autocreate'] = 'Courses can be created automatically if there are enrolments to a course that doesn\'t yet exist in Moodle.';
$string['autocreation_settings'] = 'Autocreation Settings';
$string['category'] = 'The category for auto-created courses.';
$string['course_fullname'] = 'The name of the field where the course fullname is stored.';
$string['course_id'] = 'The name of the field where the course ID is stored. The values of this field are used to match those in the \"enrol_db_l_coursefield\" field in Moodle\'s course table.';
$string['course_shortname'] = 'The name of the field where the course shortname is stored.';
$string['course_table'] = 'Then name of the table where we expect to find the course details in (short name, fullname, ID, etc.)';
$string['dbtype'] = 'Database type';
$string['dbhost'] = 'Server IP name or number';
$string['dbuser'] = 'Server user';
$string['dbpass'] = 'Server password';
$string['dbname'] = 'Database name';
$string['dbtable'] = 'Database table';
$string['defaultcourseroleid'] = 'The role that will be assigned by default if no other role is specified.';
$string['description'] = 'You can use a external database (of nearly any kind) to control your enrolments. It is assumed your external database contains a field containing a course ID, and a field containing a user ID. These are compared against fields that you choose in the local course and user tables.';
$string['disableunenrol'] = 'If set to yes users previously enrolled by the external database plugin will not be unenrolled by the same plugin regardless of the database contents.';
$string['enrolname'] = 'External Database';
$string['enrol_database_autocreation_settings'] = 'Auto-creation of new courses';
$string['general_options'] = 'General Options';
$string['host'] = 'Database server hostname.';
$string['ignorehiddencourse'] = 'If set to yes users will not be enroled on courses that are set to be unavailable to students.';
$string['localcoursefield'] = 'The name of the field in the course table that we are using to match entries in the remote database (eg idnumber).';
$string['localrolefield'] = 'The name of the field in the roles table that we are using to match entries in the remote database (eg shortname).';
$string['localuserfield'] = 'The name of the field in the user table that we are using to match entries in the remote database (eg idnumber).';
$string['local_fields_mapping'] = 'Moodle (local) database fields';
$string['name'] = 'The specific database to use.';
$string['pass'] = 'Password to access the server.';
$string['remote_fields_mapping'] = 'Enrolment (remote) database fields.';
$string['remotecoursefield'] = 'The name of the field in the remote table that we are using to match entries in the course table.';
$string['remoterolefield'] = 'The name of the field in the remote table that we are using to match entries in the roles table.';
$string['remoteuserfield'] = 'The name of the field in the remote table that we are using to match entries in the user table.';
$string['server_settings'] = 'External Database Server Settings';
$string['student_coursefield'] = 'The name of the field in the student enrolment table that we expect to find the course ID in.';
$string['student_l_userfield'] = 'The name of the field in the local user table that we use to match the user to a remote record for students (eg idnumber).';
$string['student_r_userfield'] = 'The name of the field in the remote student enrolment table that we expect to find the user ID in.';
$string['student_table'] = 'The name of the table where student enrolments are stored.';
$string['teacher_coursefield'] = 'The name of the field in the teacher enrolment table that we expect to find the course ID in.';
$string['teacher_l_userfield'] = 'The name of the field in the local user table that we use to match the user to a remote record for teachers (eg idnumber).';
$string['teacher_r_userfield'] = 'The name of the field in the remote teacher enrolment table that we expect to find the user ID in.';
$string['teacher_table'] = 'The name of the table where teacher enrolments are stored.';
$string['template'] = 'Optional: auto-created courses can copy their settings from a template course. Type here the shortname of the template course.';
$string['type'] = 'Database server type.';
$string['user'] = 'Username to access the server.';

?>
