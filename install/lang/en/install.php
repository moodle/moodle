<?PHP // $Id$ 
      // install.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2005010100)


$string['admindirerror'] = 'The admin directory specified is incorrect';
$string['admindirname'] = 'Admin Directory';
$string['admindirsetting'] = '    A very few webhosts use /admin as a special URL for you to access a 
    control panel or something.  Unfortunately this conflicts with the 
    standard location for the Moodle admin pages.  You can fix this by 
    renaming the admin directory in your installation, and putting that 
    new name here.  For example: <br /> <br /><b>moodleadmin</b><br /> <br />
    This will fix admin links in Moodle.';
$string['caution'] = 'Caution';
$string['chooselanguage'] = 'Choose a language';
$string['compatibilitysettings'] = 'Checking your PHP settings ...';
$string['configfilenotwritten'] = 'The installer script was not able to automatically create a config.php file containing your chosen settings, probably because the Moodle directory is not writeable. You can manually copy the following code into a file named config.php within the root directory of Moodle.';
$string['configfilewritten'] = 'config.php has been successfully created';
$string['configurationcomplete'] = 'Configuration completed';
$string['database'] = 'Database';
$string['databasesettings'] = '    Now you need to configure the database where most Moodle data
    will be stored.  This database must already have been created
    and a username and password created to access it.<br />
    <br /> <br />
       <b>Type:</b> mysql or postgres7<br />
       <b>Host:</b> eg localhost or db.isp.com<br />
       <b>Name:</b> database name, eg moodle<br />
       <b>User:</b> your database username<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> optional prefix to use for all table names';
$string['databasecreationsettings'] = '<p>Now you need to configure the database settings where most Moodle data
    will be stored.  The database will be created automatically by the installer
    with the default settings below or others that you specify in the editable 
    fields below. If security is an issue you may want to specify a password in the \"Password\" field.</p>
       <p><b>Type:</b> fixed to \"mysql\" by the installer<br />
       <b>Host:</b> fixed to \"localhost\" by the installer<br />
       <b>Name:</b> database name, eg moodle<br />
       <b>User:</b> fixed to \"root\" by the installer<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> optional prefix to use for all table names</p>';
$string['dataroot'] = 'Data Directory';
$string['datarooterror'] = 'The \'Data Directory\' you specified could not be found or created.  Either correct the path or create that directory manually.';
$string['dbconnectionerror'] = 'We could not connect to the database you specified. Please check your database settings.';
$string['dbcreationerror'] = 'Database creation error. Could not create the given database name with the settings provided';
$string['dbhost'] = 'Host Server';
$string['dbpass'] = 'Password';
$string['dbprefix'] = 'Tables prefix';
$string['dbtype'] = 'Type';
$string['directorysettings'] = '<p>Please confirm the locations of this Moodle installation.</p>

<p><b>Web Address:</b>
Specify the full web address where Moodle will be accessed.  
If your web site is accessible via multiple URLs then choose the 
most natural one that your students would use.  Do not include 
a trailing slash.</p>

<p><b>Moodle Directory:</b>
Specify the full directory path to this installation
Make sure the upper/lower case is correct.</p>

<p><b>Data Directory:</b>
You need a place where Moodle can save uploaded files.  This
directory should be readable AND WRITEABLE by the web server user 
(usually \'nobody\' or \'apache\'), but it should not be accessible 
directly via the web.</p>';
$string['dirroot'] = 'Moodle Directory';
$string['dirrooterror'] = 'The \'Moodle Directory\' setting seems to be incorrect - we can\'t find a Moodle installation there.  The value below has been reset.';
$string['download'] = 'Download';
$string['fail'] = 'Fail';
$string['fileuploads'] = 'File Uploads';
$string['fileuploadserror'] = 'This should be on';
$string['fileuploadshelp'] = '<p>File uploading seems to be disabled on your server.</p>

<p>Moodle can still be installed, but without this ability, you will not be able 
   to upload course files or new user profile images.</p>

<p>To enable file uploading you (or your system administrator) will need to 
   edit the main php.ini file on your system and change the setting for 
   <b>file_uploads</b> to \'1\'.</p>';
$string['gdversion'] = 'GD version';
$string['gdversionerror'] = 'The GD library should be present to process and create images';
$string['gdversionhelp'] = '<p>Your server does not seem to have GD installed.</p>

<p>GD is a library that is required by PHP to allow Moodle to process images 
   (such as the user profile icons) and to create new images (such as 
   the log graphs).  Moodle will still work without GD - these features 
   will just not be available to you.</p>

<p>To add GD to PHP under Unix, compile PHP using the --with-gd parameter.</p>

<p>Under Windows you can usually edit php.ini and uncomment the line referencing libgd.dll.</p>';
$string['installation'] = 'Installation';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'This should be off';
$string['magicquotesruntimehelp'] = '<p>Magic quotes runtime should be turned off for Moodle to function properly.</p>

<p>Normally it is off by default ... see the setting <b>magic_quotes_runtime</b> in your php.ini file.</p>

<p>If you don\'t have access to your php.ini, you might be able to place the following line in a file 
   called .htaccess within your Moodle directory:
   <blockquote>php_value magic_quotes_runtime Off</blockquote>
</p>   
   ';
$string['memorylimit'] = 'Memory Limit';
$string['memorylimiterror'] = 'The PHP memory limit is set quite low ... you may run into problems later.';
$string['memorylimithelp'] = '<p>The PHP memory limit for your server is currently set to $a.</p>

<p>This may cause Moodle to have memory problems later on, especially 
   if you have a lot of modules enabled and/or a lot of users.</p>

<p>We recommend that you configure PHP with a higher limit if possible, like 16M.  
   There are several ways of doing this that you can try:</p>
<ol>
<li>If you are able to, recompile PHP with <i>--enable-memory-limit</i>.  
    This will allow Moodle to set the memory limit itself.</li>
<li>If you have access to your php.ini file, you can change the <b>memory_limit</b> 
    setting in there to something like 16M.  If you don\'t have access you might 
    be able to ask your administrator to do this for you.</li>
<li>On some PHP servers you can create a .htaccess file in the Moodle directory 
    containing this line:
    <p><blockquote>php_value memory_limit 16M</blockquote></p>
    <p>However, on some servers this will prevent <b>all</b> PHP pages from working 
    (you will see errors when you look at pages) so you\'ll have to remove the .htaccess file.</p></li>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP has not been properly configured with the MySQL extension so that it can communicate with MySQL.  Please check your php.ini file or recompile PHP.';
$string['pass'] = 'Pass';
$string['phpversion'] = 'PHP version';
$string['phpversionerror'] = 'PHP version must be at least 4.1.0';
$string['phpversionhelp'] = '<p>Moodle requires a PHP version of at least 4.1.0.</p>
<p>You are currently running version $a</p>
<p>You must upgrade PHP or move to a host with a newer version of PHP!</p>';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Moodle may have trouble with safe mode on';
$string['safemodehelp'] = '<p>Moodle may have a variety of problems with safe mode on, not least is that 
   it probably won\'t be allowed to create new files.</p>
   
<p>Safe mode is usually only enabled by paranoid public web hosts, so you may have 
   to just find a new web hosting company for your Moodle site.</p>
   
<p>You can try continuing the install if you like, but expect a few problems later on.</p>';
$string['sessionautostart'] = 'Session Auto Start';
$string['sessionautostarterror'] = 'This should be off';
$string['sessionautostarthelp'] = '<p>Moodle requires session support and will not function without it.</p>

<p>Sessions can be enabled in the php.ini file ... look for the session.auto_start parameter.</p>';
$string['wwwroot'] = 'Web address';
$string['wwwrooterror'] = 'The web address does not appear to be valid - this Moodle installation doesn\'t appear to be there.';
$string['welcomep10'] = '$a->installername ($a->installerversion)';
$string['welcomep20'] = 'You are seeing this page because you have successfully installed and 
    launched the <strong>$a->packname $a->packversion</strong> package in your computer. Congratulations!';
$string['welcomep30'] = 'This release of the <strong>$a->installername</strong> includes the applications 
    which create an environment in <strong>Moodle</strong> will operate, namely:';
$string['welcomep40'] = 'The package also includes <strong>Moodle $a->moodlerelease ($a->moodleversion)</strong>.';
$string['welcomep50'] = 'The use of all the applications in this package is governed by their respective 
    licences. The complete <strong>$a->installername</strong> package is 
    <a href=\"http://www.opensource.org/docs/definition_plain.html\">open source</a> and is distributed 
    under the <a href=\"http://www.gnu.org/copyleft/gpl.html\">GPL</a> license.';
$string['welcomep60'] = 'The following pages will lead you through some easy to follow steps to 
    configure and set up <strong>Moodle</strong> on your computer. You may accept the default 
    settings or, optionally, amend them to suit your own needs.';
$string['welcomep70'] = 'Click the \"Next\" button below to continue with the set up of <strong>Moodle</strong>.';
?>
