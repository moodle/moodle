<?php // $Id$
     
$string['admindirerror'] = 'The admin directory specified is incorrect';
$string['admindirname'] = 'Admin Directory';
$string['admindirsetting'] = "
    <p>A very few webhosts use /admin as a special URL for you to access a 
    control panel or something.  Unfortunately this conflicts with the 
    standard location for the Moodle admin pages.  You can fix this by 
    renaming the admin directory in your installation, and putting that 
    new name here.  For example: <blockquote> moodleadmin</blockquote>.  
    This will fix admin links in Moodle.</p>";
$string['chooselanguage'] = 'Choose a language';
$string['configfilenotwritten'] = "The installer script was not able to automatically create a config.php file containing your chosen settings. Please copy the following code into a file named config.php within the root directory of Moodle.";
$string['configfilewritten'] = "config.php has been successfully created";
$string['configurationcomplete'] = 'Configuration completed';

$string['database'] = 'Database';
$string['databasesettings'] = "
    <p>Now you need to configure the database where most Moodle data
    will be stored.  This database must already have been created
    and a username and password created to access it.</p>
    <p>Type: mysql or postgres7<br />
       Host Server: eg localhost or db.isp.com<br />
       Name: database name, eg moodle<br />
       User: your database username<br />
       Password: your database password<br />
       Tables Prefix: prefix to use for all table names</p>";
$string['dataroot'] = 'Data';
$string['datarooterror'] = "The 'Data' setting is incorrect";
$string['dbconnectionerror'] = 'Database connection error. Please check your database settings';
$string['dbcreationerror'] = 'Database creation error. Could not create the given database name with the settings provided';
$string['dbhost'] = 'Host Server';
$string['dbpass'] = 'Password';
$string['dbprefix'] = 'Tables prefix';
$string['dbtype'] = 'Type';
$string['directorysettings'] = "
    <p><b>WWW:</b>
    You need to tell Moodle where it is located. Specify the full
    web address to where moodle has been installed.  If your web site 
    is accessible via multiple URLs then choose the most natural one 
    that your students would use.  Do not include a trailing slash</p>
    <p><b>Directory:</b>
    Specify the full OS directory path to this same location
    Make sure the upper/lower case is correct</p>
    <p><b>Data:</b>
    You need a place where Moodle can save uploaded files.  This
    directory should be readable AND WRITEABLE by the web server user 
    (usually 'nobody' or 'apache'), but it should not be accessible 
    directly via the web.</p>";

$string['dirroot'] = 'Directory';
$string['dirrooterror'] = "The 'Directory' setting was incorrect. Try the following setting";
$string['wwwroot'] = 'WWW';
$string['wwwrooterror'] = "The 'WWW' setting is incorrect";

?>
