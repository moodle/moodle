<?php // $Id$ 

$string['enrolname'] = 'External Database';

$string['autocreate'] = 'Courses can be created automatically if there are
                         enrolments to a course  that doesn\'t yet exist 
                                    in Moodle.';
$string['category'] = 'The category for auto-created courses.';
$string['description'] = 'You can use a external database (of nearly any kind) to control your enrolments.  It is assumed your external database contains a field containing a course ID, and a field containing a user ID.  These are compared against fields that you choose in the local course and user tables.';
$string['dbtype'] = 'Database server type';
$string['dbhost'] = 'Database server hostname ';
$string['dbuser'] = 'Username to access the server';
$string['dbpass'] = 'Password to access the server';
$string['dbname'] = 'The specific database to use';
$string['dbtable'] = 'The table in that database';
$string['general_options'] = 'General Options';
$string['field_mapping'] = 'Field Mapping';
$string['localcoursefield'] = 'The name of the field in the course table that we are using to match entries in the remote database (eg idnumber)';
$string['localuserfield'] = 'The name of the field in the local user table that we use to match the user to a remote record (eg idnumber)';
$string['server_settings'] = 'Server Settings';
$string['remotecoursefield'] = 'The field in the remote database we expect to find the course ID in';
$string['remoteuserfield'] = 'The field in the remote database we expect to find the user ID in';
$string['template'] = 'Optional: auto-created courses can copy 
                       their settings from a template course.';

?>
