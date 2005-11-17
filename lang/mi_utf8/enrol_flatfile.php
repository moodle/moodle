<?PHP // $Id$ 
      // enrol_flatfile.php - created with Moodle 1.4.1 (2004083101)


$string['description'] = 'This method will repeatedly check for and process a specially-formatted text file in the location that you specify.  The file can look something like this: 
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
$string['filelockedmailsubject'] = 'H&#275; Nui: K&#333;nae Puka Whakauru';
$string['location'] = 'Tauw&#257;hi o te K&#333;nae';
$string['mailadmin'] = 'Whakam&#333;hio atu ki te Kaiwhakahaere m&#257; te im&#275;ra';
$string['mailusers'] = 'Whakam&#333;hio atu ki ng&#257; Kaiwhakauru m&#257; te im&#275;ra';

?>