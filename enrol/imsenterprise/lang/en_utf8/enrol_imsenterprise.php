<?PHP // $Id$ 
      // enrol_imsenterprise.php - created with Moodle 1.7 beta + (2006101003)


$string['aftersaving...'] = 'Once you have saved your settings, you may wish to';
$string['allowunenrol'] = 'Allow the IMS data to <strong>unenrol</strong> students/teachers';
$string['basicsettings'] = 'Basic settings';
$string['coursesettings'] = 'Course data options';
$string['createnewcategories'] = 'Create new (hidden) course categories if not found in Moodle';
$string['createnewcourses'] = 'Create new (hidden) courses if not found in Moodle';
$string['createnewusers'] = 'Create user accounts for users not yet registered in Moodle';
$string['cronfrequency'] = 'Frequency of processing';
$string['deleteusers'] = 'Delete user accounts when specified in IMS data';
$string['description'] = 'This method will repeatedly check for and process a specially-formatted text file in the location that you specify.  The file must follow the <a href=\'../help.php?module=enrol/imsenterprise&amp;file=formatoverview.html\'>IMS Enterprise specifications</a> containing person, group, and membership XML elements.';
$string['doitnow'] = 'perform an IMS Enterprise import right now';
$string['enrolname'] = 'IMS Enterprise file';
$string['filelockedmail'] = 'The text file you are using for IMS-file-based enrolments ($a) can not be deleted by the cron process.  This usually means the permissions are wrong on it.  Please fix the permissions so that Moodle can delete the file, otherwise it might be processed repeatedly.';
$string['filelockedmailsubject'] = 'Important error: Enrolment file';
$string['fixcasepersonalnames'] = 'Change personal names to Title Case';
$string['fixcaseusernames'] = 'Change usernames to lower case';
$string['imsrolesdescription'] = 'The IMS Enterprise specification includes 8 distinct role types. Please choose how you want them to be assigned in Moodle, including whether any of them should be ignored.';
$string['location'] = 'File location';
$string['logtolocation'] = 'Log file output location (blank for no logging)';
$string['mailadmins'] = 'Notify admin by email';
$string['mailusers'] = 'Notify users by email';
$string['miscsettings'] = 'Miscellaneous';
$string['processphoto'] = 'Add user photo data to profile';
$string['processphotowarning'] = 'Warning: Image processing is likely to add a significant burden to the server. You are recommended not to activate this option if large numbers of students are expected to be processed.';
$string['restricttarget'] = 'Only process data if the following target is specified';
$string['sourcedidfallback'] = 'Use the &quot;sourcedid&quot; for a person\'s userid if the &quot;userid&quot; field is not found';
$string['truncatecoursecodes'] = 'Truncate course codes to this length';
$string['usecapitafix'] = 'Tick this box if using &quot;Capita&quot; (their XML format is slightly wrong)';
$string['usersettings'] = 'User data options';
$string['zeroisnotruncation'] = '0 indicates no truncation';

?>
