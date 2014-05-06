<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'install', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   install
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['admindirerror'] = 'The admin directory specified is incorrect';
$string['admindirname'] = 'Admin directory';
$string['admindirsetting'] = 'A very few webhosts use /admin as a special URL for you to access a 
    control panel or something.  Unfortunately this conflicts with the 
    standard location for the Moodle admin pages.  You can fix this by 
    renaming the admin directory in your installation, and putting that 
    new name here.  For example: <br /> <br /><b>moodleadmin</b><br /> <br />
    This will fix admin links in Moodle.';
$string['admindirsettinghead'] = 'Setting the admin directory ...';
$string['admindirsettingsub'] = 'A very few webhosts use /admin as a special URL for you to access a 
    control panel or something.  Unfortunately this conflicts with the 
    standard location for the Moodle admin pages.  You can fix this by 
    renaming the admin directory in your installation, and putting that 
    new name here.  For example: <br /> <br /><b>moodleadmin</b><br /> <br />
    This will fix admin links in Moodle.';
$string['availablelangs'] = 'Available language packs';
$string['caution'] = 'Caution';
$string['cliadminpassword'] = 'New admin user password';
$string['cliadminusername'] = 'Admin account username';
$string['clialreadyconfigured'] = 'The configuration file config.php already exists. Please use admin/cli/install_database.php to install Moodle for this site.';
$string['clialreadyinstalled'] = 'The configuration file config.php already exists. Please use admin/cli/install_database.php to upgrade Moodle for this site.';
$string['cliinstallfinished'] = 'Installation completed successfully.';
$string['cliinstallheader'] = 'Moodle {$a} command line installation program';
$string['climustagreelicense'] = 'In non interactive mode you must agree to license by specifying --agree-license option';
$string['clitablesexist'] = 'Database tables already present, cli installation can not continue.';
$string['compatibilitysettings'] = 'Checking your PHP settings ...';
$string['compatibilitysettingshead'] = 'Checking your PHP settings ...';
$string['compatibilitysettingssub'] = 'Your server should pass all these tests to make Moodle run properly';
$string['configfilenotwritten'] = 'The installer script was not able to automatically create a config.php file containing your chosen settings, probably because the Moodle directory is not writeable. You can manually copy the following code into a file named config.php within the root directory of Moodle.';
$string['configfilewritten'] = 'config.php has been successfully created';
$string['configurationcomplete'] = 'Configuration completed';
$string['configurationcompletehead'] = 'Configuration completed';
$string['configurationcompletesub'] = 'Moodle made an attempt to save your configuration in a file in the root of your Moodle installation.';
$string['database'] = 'Database';
$string['databasehead'] = 'Database settings';
$string['databasehost'] = 'Database host';
$string['databasename'] = 'Database name';
$string['databasepass'] = 'Database password';
$string['databaseport'] = 'Database port';
$string['databasesocket'] = 'Unix socket';
$string['databasetypehead'] = 'Choose database driver';
$string['databasetypesub'] = 'Moodle supports several types of database servers. Please contact server administrator if you do not know which type to use.';
$string['databaseuser'] = 'Database user';
$string['dataroot'] = 'Data directory';
$string['datarooterror'] = 'The \'data directory\' you specified could not be found or created.  Either correct the path or create that directory manually.';
$string['datarootpermission'] = 'Data directories permission';
$string['datarootpublicerror'] = 'The \'data directory\' you specified is directly accessible via web, you must use different directory.';
$string['dbconnectionerror'] = 'We could not connect to the database you specified. Please check your database settings.';
$string['dbcreationerror'] = 'Database creation error. Could not create the given database name with the settings provided';
$string['dbhost'] = 'Host server';
$string['dbpass'] = 'Password';
$string['dbport'] = 'Port';
$string['dbprefix'] = 'Tables prefix';
$string['dbtype'] = 'Type';
$string['directorysettings'] = '<p>Please confirm the locations of this Moodle installation.</p>

<p><b>Web address:</b>
Specify the full web address where Moodle will be accessed.  
If your web site is accessible via multiple URLs then choose the 
most natural one that your students would use.  Do not include 
a trailing slash.</p>

<p><b>Moodle directory:</b>
Specify the full directory path to this installation
Make sure the upper/lower case is correct.</p>

<p><b>Data directory:</b>
You need a place where Moodle can save uploaded files.  This
directory should be readable AND WRITEABLE by the web server user 
(usually \'nobody\' or \'apache\'), but it must not be accessible 
directly via the web. The installer will try to create it if doesn\'t exist.</p>';
$string['directorysettingshead'] = 'Please confirm the locations of this Moodle installation';
$string['directorysettingssub'] = '<b>Web address:</b>
Specify the full web address where Moodle will be accessed.  
If your web site is accessible via multiple URLs then choose the 
most natural one that your students would use.  Do not include 
a trailing slash.
<br />
<br />
<b>Moodle directory:</b>
Specify the full directory path to this installation
Make sure the upper/lower case is correct.
<br />
<br />
<b>Data directory:</b>
You need a place where Moodle can save uploaded files.  This
directory must be readable AND WRITEABLE by the web server user 
(usually \'nobody\' or \'apache\'), but it must not be accessible 
directly via the web. The installer will try to create it if doesn\'t exist.';
$string['dirroot'] = 'Moodle directory';
$string['dirrooterror'] = 'The \'Moodle directory\' setting seems to be incorrect - we can\'t find a Moodle installation there. The value below has been reset.';
$string['download'] = 'Download';
$string['downloadlanguagebutton'] = 'Download the &quot;{$a}&quot; language pack';
$string['downloadlanguagehead'] = 'Download language pack';
$string['downloadlanguagenotneeded'] = 'You may continue the installation process using the default language pack, "{$a}".';
$string['downloadlanguagesub'] = 'You now have the option of downloading a language pack and continuing the installation process in this language.<br /><br />If you are unable to download the language pack, the installation process will continue in English. (Once the installation process is complete, you will have the opportunity to download and install additional language packs.)';
$string['doyouagree'] = 'Do you agree ? (yes/no):';
$string['environmenthead'] = 'Checking your environment ...';
$string['environmentsub'] = 'We are checking if the various components of your system meet the system requirements';
$string['environmentsub2'] = 'Each Moodle release has some minimum PHP version requirement and a number of mandatory PHP extensions.
Full environment check is done before each install and upgrade. Please contact server administrator if you do not know how to install new version or enable PHP extensions.';
$string['errorsinenvironment'] = 'Environment check failed!';
$string['fail'] = 'Fail';
$string['fileuploads'] = 'File uploads';
$string['fileuploadserror'] = 'This should be on';
$string['fileuploadshelp'] = '<p>File uploading seems to be disabled on your server.</p>

<p>Moodle can still be installed, but without this ability, you will not be able 
   to upload course files or new user profile images.</p>

<p>To enable file uploading you (or your system administrator) will need to 
   edit the main php.ini file on your system and change the setting for 
   <b>file_uploads</b> to \'1\'.</p>';
$string['chooselanguage'] = 'Choose a language';
$string['chooselanguagehead'] = 'Choose a language';
$string['chooselanguagesub'] = 'Please choose a language for the installation. This language will also be used as the default language for the site, though it may be changed later.';
$string['inputdatadirectory'] = 'Data directory:';
$string['inputwebadress'] = 'Web address :';
$string['inputwebdirectory'] = 'Moodle directory:';
$string['installation'] = 'Installation';
$string['langdownloaderror'] = 'Unfortunately the language "{$a}" could not be downloaded. The installation process will continue in English.';
$string['langdownloadok'] = 'The language "{$a}" was installed successfully. The installation process will continue in this language.';
$string['memorylimit'] = 'Memory limit';
$string['memorylimiterror'] = 'The PHP memory limit is set quite low ... you may run into problems later.';
$string['memorylimithelp'] = '<p>The PHP memory limit for your server is currently set to {$a}.</p>

<p>This may cause Moodle to have memory problems later on, especially 
   if you have a lot of modules enabled and/or a lot of users.</p>

<p>We recommend that you configure PHP with a higher limit if possible, like 40M.  
   There are several ways of doing this that you can try:</p>
<ol>
<li>If you are able to, recompile PHP with <i>--enable-memory-limit</i>.  
    This will allow Moodle to set the memory limit itself.</li>
<li>If you have access to your php.ini file, you can change the <b>memory_limit</b> 
    setting in there to something like 40M.  If you don\'t have access you might 
    be able to ask your administrator to do this for you.</li>
<li>On some PHP servers you can create a .htaccess file in the Moodle directory 
    containing this line:
    <blockquote><div>php_value memory_limit 40M</div></blockquote>
    <p>However, on some servers this will prevent <b>all</b> PHP pages from working 
    (you will see errors when you look at pages) so you\'ll have to remove the .htaccess file.</p></li>
</ol>';
$string['mssqlextensionisnotpresentinphp'] = 'PHP has not been properly configured with the MSSQL extension so that it can communicate with SQL*Server.  Please check your php.ini file or recompile PHP.';
$string['mysqliextensionisnotpresentinphp'] = 'PHP has not been properly configured with the MySQLi extension so that it can communicate with MySQL.  Please check your php.ini file or recompile PHP.  MySQLi extension is not available for PHP 4.';
$string['nativemariadb'] = 'MariaDB (native/mariadb)';
$string['nativemariadbhelp'] = '<p>The database is where most of the Moodle settings and data are stored and must be configured here.</p>
<p>The database name, username, and password are required fields; table prefix is optional.</p>
<p>If the database currently does not exist, and the user you specify has permission, Moodle will attempt to create a new database with the correct permissions and settings.</p>
<p>This driver is not compatible with legacy MyISAM engine.</p>';
$string['nativemysqli'] = 'Improved MySQL (native/mysqli)';
$string['nativemysqlihelp'] = '<p>The database is where most of the Moodle settings and data are stored and must be configured here.</p>
<p>The database name, username, and password are required fields; table prefix is optional.</p>
<p>If the database currently does not exist, and the user you specify has permission, Moodle will attempt to create a new database with the correct permissions and settings.</p>';
$string['nativemssql'] = 'SQL*Server FreeTDS (native/mssql)';
$string['nativemssqlhelp'] = 'Now you need to configure the database where most Moodle data will be stored.
This database must already have been created and a username and password created to access it. Table prefix is mandatory.';
$string['nativeoci'] = 'Oracle (native/oci)';
$string['nativeocihelp'] = 'Now you need to configure the database where most Moodle data will be stored.
This database must already have been created and a username and password created to access it. Table prefix is mandatory.';
$string['nativepgsql'] = 'PostgreSQL (native/pgsql)';
$string['nativepgsqlhelp'] = '<p>The database is where most of the Moodle settings and data are stored and must be configured here.</p>
<p>The database name, username, password and table prefix are required fields.</p>
<p>The database must already exist and the user must have access to both read, and write to it.</p>';
$string['nativesqlsrv'] = 'SQL*Server Microsoft (native/sqlsrv)';
$string['nativesqlsrvhelp'] = 'Now you need to configure the database where most Moodle data will be stored.
This database must already have been created and a username and password created to access it. Table prefix is mandatory.';
$string['nativesqlsrvnodriver'] = 'Microsoft Drivers for SQL Server for PHP are not installed or not configured properly.';
$string['nativesqlsrvnonwindows'] = 'Microsoft Drivers for SQL Server for PHP are available only for Windows OS.';
$string['ociextensionisnotpresentinphp'] = 'PHP has not been properly configured with the OCI8 extension so that it can communicate with Oracle.  Please check your php.ini file or recompile PHP.';
$string['pass'] = 'Pass';
$string['paths'] = 'Paths';
$string['pathserrcreatedataroot'] = 'Data directory ({$a->dataroot}) cannot be created by the installer.';
$string['pathshead'] = 'Confirm paths';
$string['pathsrodataroot'] = 'Dataroot directory is not writable.';
$string['pathsroparentdataroot'] = 'Parent directory ({$a->parent}) is not writeable. Data directory ({$a->dataroot}) cannot be created by the installer.';
$string['pathssubadmindir'] = 'A very few webhosts use /admin as a special URL for you to access a
control panel or something.  Unfortunately this conflicts with the standard location for the Moodle admin pages.  You can fix this by
renaming the admin directory in your installation, and putting that  new name here.  For example: <em>moodleadmin</em>. This will fix admin links in Moodle.';
$string['pathssubdataroot'] = '<p>A directory where Moodle will store all file content uploaded by users.</p>
<p>This directory should be both readable and writeable by the web server user (usually \'www-data\', \'nobody\', or \'apache\').</p>
<p>It must not be directly accessible over the web.</p>
<p>If the directory does not currently exist, the installation process will attempt to create it.</p>';
$string['pathssubdirroot'] = '<p>The full path to the directory containing the Moodle code.</p>';
$string['pathssubwwwroot'] = '<p>The full address where Moodle will be accessed i.e. the address that users will enter into the address bar of their browser to access Moodle.</p>
<p>It is not possible to access Moodle using multiple addresses. If your site is accessible via multiple addresses then choose the easiest one and set up a permanent redirect for each of the other addresses.</p>
<p>If your site is accessible both from the Internet, and from an internal network (sometimes called an Intranet), then use the public address here.</p>
<p>If the current address is not correct, please change the URL in your browser\'s address bar and restart the installation.</p>';
$string['pathsunsecuredataroot'] = 'Dataroot location is not secure';
$string['pathswrongadmindir'] = 'Admin directory does not exist';
$string['pgsqlextensionisnotpresentinphp'] = 'PHP has not been properly configured with the PGSQL extension so that it can communicate with PostgreSQL.  Please check your php.ini file or recompile PHP.';
$string['phpextension'] = '{$a} PHP extension';
$string['phpversion'] = 'PHP version';
$string['phpversionhelp'] = '<p>Moodle requires a PHP version of at least 4.3.0 or 5.1.0 (5.0.x has a number of known problems).</p>
<p>You are currently running version {$a}</p>
<p>You must upgrade PHP or move to a host with a newer version of PHP!<br />
(In case of 5.0.x you could also downgrade to 4.4.x version)</p>';
$string['releasenoteslink'] = 'For information about this version of Moodle, please see the release notes at {$a}';
$string['safemode'] = 'Safe mode';
$string['safemodeerror'] = 'Moodle may have trouble with safe mode on';
$string['safemodehelp'] = '<p>Moodle may have a variety of problems with safe mode on, not least is that 
   it probably won\'t be allowed to create new files.</p>
   
<p>Safe mode is usually only enabled by paranoid public web hosts, so you may have 
   to just find a new web hosting company for your Moodle site.</p>
   
<p>You can try continuing the install if you like, but expect a few problems later on.</p>';
$string['sessionautostart'] = 'Session auto start';
$string['sessionautostarterror'] = 'This should be off';
$string['sessionautostarthelp'] = '<p>Moodle requires session support and will not function without it.</p>

<p>Sessions can be enabled in the php.ini file ... look for the session.auto_start parameter.</p>';
$string['sqliteextensionisnotpresentinphp'] = 'PHP has not been properly configured with the SQLite extension.  Please check your php.ini file or recompile PHP.';
$string['upgradingqtypeplugin'] = 'Upgrading question/type plugin';
$string['welcomep10'] = '{$a->installername} ({$a->installerversion})';
$string['welcomep20'] = 'You are seeing this page because you have successfully installed and 
    launched the <strong>{$a->packname} {$a->packversion}</strong> package in your computer. Congratulations!';
$string['welcomep30'] = 'This release of the <strong>{$a->installername}</strong> includes the applications 
    to create an environment in which <strong>Moodle</strong> will operate, namely:';
$string['welcomep40'] = 'The package also includes <strong>Moodle {$a->moodlerelease} ({$a->moodleversion})</strong>.';
$string['welcomep50'] = 'The use of all the applications in this package is governed by their respective 
    licences. The complete <strong>{$a->installername}</strong> package is 
    <a href="http://www.opensource.org/docs/definition_plain.html">open source</a> and is distributed 
    under the <a href="http://www.gnu.org/copyleft/gpl.html">GPL</a> license.';
$string['welcomep60'] = 'The following pages will lead you through some easy to follow steps to 
    configure and set up <strong>Moodle</strong> on your computer. You may accept the default 
    settings or, optionally, amend them to suit your own needs.';
$string['welcomep70'] = 'Click the "Next" button below to continue with the set up of <strong>Moodle</strong>.';
$string['wwwroot'] = 'Web address';
$string['wwwrooterror'] = 'The \'Web Address\' does not appear to be valid - this Moodle installation doesn\'t appear to be there. The value below has been reset.';
