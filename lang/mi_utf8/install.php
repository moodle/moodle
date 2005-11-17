<?PHP // $Id$ 
      // install.php - created with Moodle 1.4.1 (2004083101)


$string['admindirerror'] = 'The admin directory specified is incorrect';
$string['admindirname'] = 'Admin Directory';
$string['admindirsetting'] = '    A very few webhosts use /admin as a special URL for you to access a 
    control panel or something.  Unfortunately this conflicts with the 
    standard location for the Moodle admin pages.  You can fix this by 
    renaming the admin directory in your installation, and putting that 
    new name here.  For example: <br/> <br /><b>moodleadmin</b><br /> <br />
    This will fix admin links in Moodle.';
$string['caution'] = 'Kia t&#363;pato';
$string['chooselanguage'] = 'T&#299;pako he reo';
$string['compatibilitysettings'] = 'Checking your PHP settings ...';
$string['configfilenotwritten'] = 'The installer script was not able to automatically create a config.php file containing your chosen settings, probably because the Moodle directory is not writeable. You can manually copy the following code into a file named config.php within the root directory of Moodle.';
$string['configfilewritten'] = 'config.php has been successfully created';
$string['configurationcomplete'] = 'Configuration completed';
$string['database'] = 'Puna K&#333;rero';
$string['databasesettings'] = '    Now you need to configure the database where most Moodle data
    will be stored.  This database must already have been created
    and a username and password created to access it.<br/>
    <br /> <br />
       <b>Type:</b> mysql or postgres7<br />
       <b>Host:</b> eg localhost or db.isp.com<br />
       <b>Name:</b> database name, eg moodle<br />
       <b>User:</b> your database username<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> optional prefix to use for all table names';
$string['dataroot'] = 'K&#333;paki H&#333;tuku';
$string['datarooterror'] = 'The \'Data Directory\' you specified could not be found or created.  Either correct the path or create that directory manually.';
$string['dbconnectionerror'] = 'We could not connect to the database you specified. Please check your database settings.';
$string['dbcreationerror'] = 'Database creation error. Could not create the given database name with the settings provided';
$string['dbhost'] = 'T&#363;mau Rorohiko Tuku';
$string['dbpass'] = 'Kupu Whakauru';
$string['dbprefix'] = 'Tables prefix';
$string['dbtype'] = 'Momo';
$string['directorysettings'] = '<p>Whakat&#363;turu, kei whea te w&#257;hi noho o t&#275;nei whakaaturanga Moodle.</p>

<p><b>Web Address:</b>
Wh&#257;ititia te ingoa katoa o te Pae Tukutuku e kite ana a Moodle.  
Ki te kitenga o t&#257;u Pae Tukutuku mai ng&#257; URLs maha, t&#299;pakohia te ara pai m&#333; &#257;u tauira. Kaua e t&#257;piri he \'trailing slash\'.</p>

<p><b>Moodle Directory:</b>
Wh&#257;ititia te katoa o te ara k&#333;paki ki t&#275;nei whakauta.
Whakatikahia ng&#257; p&#363;matua/p&#363;iti.</p>

<p><b>Data Directory:</b>
You need a place where Moodle can save uploaded files.  This
directory should be readable AND WRITEABLE by the web server user 
(usually \'nobody\' or \'apache\'), but it should not be accessible 
directly via the web.</p>';
$string['dirroot'] = 'K&#333;paki Moodle';
$string['dirrooterror'] = 'The \'Moodle Directory\' setting seems to be incorrect - we can\'t find a Moodle installation there.  The value below has been reset.';
$string['download'] = 'Tuku Mai';
$string['fail'] = 'K&#257;ore he P&#257;hi';
$string['fileuploads'] = 'K&#333;nae Tuku Atu';
$string['fileuploadserror'] = 'Me whakak&#257;ngia t&#275;neki';
$string['fileuploadshelp'] = '<p>File uploading seems to be disabled on your server.</p>

<p>Moodle can still be installed, but without this ability, you will not be able 
   to upload course files or new user profile images.

<p>To enable file uploading you (or your system administrator) will need to 
   edit the main php.ini file on your system and change the setting for 
   <b>file_uploads</b> to \'1\'.</p>';
$string['gdversion'] = 'Whakaaturanga GD ';
$string['gdversionerror'] = 'The GD library should be present to process and create images';
$string['gdversionhelp'] = '<p>Your server does not seem to have GD installed.</p>

<p>GD is a library that is required by PHP to allow Moodle to process images 
   (such as the user profile icons) and to create new images (such as 
   the log graphs).  Moodle will still work without GD - these features 
   will just not be available to you.</p>

<p>To add GD to PHP under Unix, compile PHP using the --with-gd parameter.</p>

<p>Under Windows you can usually edit php.ini and uncomment the line referencing libgd.dll.</p>';
$string['installation'] = 'Whakauta';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Me whakaweto t&#275;nei';
$string['magicquotesruntimehelp'] = '<p>Magic quotes runtime should be turned off for Moodle to function properly.</p>

<p>Normally it is off by default ... see the setting <b>magic_quotes_runtime</b> in your php.ini file.</p>

<p>If you don\'t have access to your php.ini, you might be able to place the following line in a file 
   called .htaccess within your Moodle directory:
   <blockquote>php_value magic_quotes_runtime Off</blockquote>
</p>   
   ';
$string['memorylimit'] = 'Tau P&#363;mahara';
$string['memorylimiterror'] = 'The PHP memory limit is set quite low ... you may run into problems later.';
$string['memorylimithelp'] = '<p>The PHP memory limit for your server is currently set to $a.</p>

<p>This may cause Moodle to have memory problems later on, especially 
   if you have a lot of modules enabled and/or a lot of users.

<p>We recommend that you configure PHP with a higher limit if possible, like 16M.  
   There are several ways of doing this that you can try:
<ol>
<li>If you are able to, recompile PHP with <i>--enable-memory-limit</i>.  
    This will allow Moodle to set the memory limit itself.
<li>If you have access to your php.ini file, you can change the <b>memory_limit</b> 
    setting in there to something like 16M.  If you don\'t have access you might 
    be able to ask your administrator to do this for you.
<li>On some PHP servers you can create a .htaccess file in the Moodle directory 
    containing this line:
    <p><blockquote>php_value memory_limit 16M</blockquote></p>
    <p>However, on some servers this will prevent <b>all</b> PHP pages from working 
    (you will see errors when you look at pages) so you\'ll have to remove the .htaccess file.
</ol>';
$string['pass'] = 'P&#257;hi';
$string['phpversion'] = 'Whakaaturanga PHP ';
$string['phpversionerror'] = 'Kaua e heke i te Whakaaturanga PHP 4.1.0';
$string['phpversionhelp'] = '<p>Ka whai atu te Moodle ki ng&#257; Whakaaturanga PHP piki ake ki te 4.1.0.</p>
<p>Kei te mahi koe i te whakaaturanga $a</p>
<p>Whakapai ake i te PHP, neke atu r&#257;nei ki t&#275;tehi rorohiko tuku me te PHP hou!</p>';
$string['safemode'] = 'T&#363;momo Haumaru';
$string['safemodeerror'] = 'T&#275;n&#257; pea';
$string['safemodehelp'] = '<p>Moodle may have a variety of problems with safe mode on, not least is that 
   it probably won\'t be allowed to create new files.</p>
   
<p>Safe mode is usually only enabled by paranoid public web hosts, so you may have 
   to just find a new web hosting company for your Moodle site.</p>
   
<p>You can try continuing the install if you like, but expect a few problems later on.</p>';
$string['sessionautostart'] = 'Session Auto Start';
$string['sessionautostarterror'] = 'Me Whakaweto';
$string['sessionautostarthelp'] = '<p>Moodle requires session support and will not function without it.</p>

<p>Sessions can be enabled in the php.ini file ... look for the session.auto_start parameter.</p>';
$string['wwwroot'] = 'Nohoanga Tukutuku';
$string['wwwrooterror'] = 'K&#257;ore te Pae Tukutuku e whai mana - ko t&#275;neki whakauta Moodle k&#257;ore ki reira pea.';

?>