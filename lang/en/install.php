<?php // $Id$
     
$string['admindirerror'] = 'The admin directory specified is incorrect';
$string['admindirname'] = 'Admin Directory';
$string['admindirsetting'] = "
    A very few webhosts use /admin as a special URL for you to access a 
    control panel or something.  Unfortunately this conflicts with the 
    standard location for the Moodle admin pages.  You can fix this by 
    renaming the admin directory in your installation, and putting that 
    new name here.  For example: <br/>&nbsp;<br /><b>moodleadmin</b><br />&nbsp;<br />
    This will fix admin links in Moodle.";
$string['chooselanguage'] = 'Choose a language';
$string['compatibilitysettings'] = 'The following is a check on the compatibility of the server to run Moodle';
$string['configfilenotwritten'] = "The installer script was not able to automatically create a config.php file containing your chosen settings. Please copy the following code into a file named config.php within the root directory of Moodle.";
$string['configfilewritten'] = "config.php has been successfully created";
$string['configurationcomplete'] = 'Configuration completed';

$string['database'] = 'Database';
$string['databasesettings'] = "
    Now you need to configure the database where most Moodle data
    will be stored.  This database must already have been created
    and a username and password created to access it.<br/>
    <br />&nbsp;<br />
       <b>Type:</b> mysql or postgres7<br />
       <b>Host:</b> eg localhost or db.isp.com<br />
       <b>Name:</b> database name, eg moodle<br />
       <b>User:</b> your database username<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> prefix to use for all table names";
$string['dataroot'] = 'Data';
$string['datarooterror'] = "The 'Data' setting is incorrect";
$string['dbconnectionerror'] = 'Database connection error. Please check your database settings';
$string['dbcreationerror'] = 'Database creation error. Could not create the given database name with the settings provided';
$string['dbhost'] = 'Host Server';
$string['dbpass'] = 'Password';
$string['dbprefix'] = 'Tables prefix';
$string['dbtype'] = 'Type';
$string['directorysettings'] = "
    <b>WWW:</b>
    You need to tell Moodle where it is located. Specify the full
    web address to where moodle has been installed.  If your web site 
    is accessible via multiple URLs then choose the most natural one 
    that your students would use.  Do not include a trailing slash<br/>&nbsp;<br/>
    <b>Directory:</b>
    Specify the full OS directory path to this same location
    Make sure the upper/lower case is correct<br/>&nbsp;<br/>
    <b>Data:</b>
    You need a place where Moodle can save uploaded files.  This
    directory should be readable AND WRITEABLE by the web server user 
    (usually 'nobody' or 'apache'), but it should not be accessible 
    directly via the web.";

$string['dirroot'] = 'Directory';
$string['dirrooterror'] = "The 'Directory' setting was incorrect. Try the following setting";
$string['fail'] = 'Fail';
$string['fileuploads'] = 'File Uploads';
$string['fileuploadserror'] = 'This should be on';
$string['fileuploadshelp'] = 'Moodle requires file uploading to be switched on';
$string['gdversion'] = 'GD version';
$string['gdversionerror'] = 'The GD library should be present to process and create images';
$string['gdversionhelp'] = 'The GD library should be present to process and create images';
$string['installation'] = 'Installation';
$string['memorylimit'] = 'Memory Limit';
$string['memorylimiterror'] = 'The memory limit needs to be set to 16M or more or be changeable';
$string['memorylimithelp'] = "The memory limit needs to be set to 16M or more or be changeable. Your current memory limit is set to \$a";
$string['pass'] = 'Pass';
$string['PHPversion'] = 'PHP version';
$string['PHPversionerror'] = 'PHP version must be at least 4.1.0';
$string['phpversionhelp'] = "Moodle requires a PHP version of at least 4.1.0. You are currently running version \$a";
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Moodle can not handle files properly with safe mode on';
$string['safemodehelp'] = 'Moodle can not handle files properly with safe mode on';
$string['sessionautostart'] = 'Session Auto Start';
$string['sessionautostarterror'] = 'This should be off';
$string['sessionautostarthelp'] = 'Session auto start should be turned off';
$string['sessionsavepath'] = 'Session Save Path';
$string['sessionsavepatherror'] = 'It seems your server does not support sessions';
$string['sessionsavepathhelp'] = 'Moodle requires session support';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'This should be off';
$string['magicquotesruntimehelp'] = 'Magic quotes should be turned off';
$string['wwwroot'] = 'WWW';
$string['wwwrooterror'] = "The 'WWW' setting is incorrect";

?>
