<?PHP // $Id$ 
      // enrol_flatfile.php - created with Moodle 1.7 beta + (2006101003)


$string['description'] = 'This method will repeatedly check for and process a specially-formatted text file in the location that you specify.
The file is a comma separated file assumed to have four or six fields per line:
<pre>
*  operation, role, idnumber(user), idnumber(course) [, starttime, endtime]
where:
*  operation        = add | del
*  role             = student | teacher | teacheredit
*  idnumber(user)   = idnumber in the user table NB not id
*  idnumber(course) = idnumber in the course table NB not id
*  starttime        = start time (in seconds since epoch) - optional
*  endtime          = end time (in seconds since epoch) - optional
</pre>
It could look something like this:
<pre>
   add, student, 5, CF101
   add, teacher, 6, CF101
   add, teacheredit, 7, CF101
   del, student, 8, CF101
   del, student, 17, CF101
   add, student, 21, CF101, 1091115000, 1091215000
</pre>';
$string['enrolname'] = 'Flat file';
$string['filelockedmail'] = 'The text file you are using for file-based enrolments ($a) can not be deleted by the cron process.  This usually means the permissions are wrong on it.  Please fix the permissions so that Moodle can delete the file, otherwise it might be processed repeatedly.';
$string['filelockedmailsubject'] = 'Important error: Enrolment file';
$string['location'] = 'File location';
$string['mailadmin'] = 'Notify admin by email';
$string['mailusers'] = 'Notify users by email';

?>
