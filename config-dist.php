<?PHP
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// Moodle configuration file                                             //
//                                                                       //
// This file should be renamed "config.php" in the top-level directory   //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 3 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
unset($CFG);  // Ignore this line
global $CFG;  // This is necessary here for PHPUnit execution
$CFG = new stdClass();

//=========================================================================
// 1. DATABASE SETUP
//=========================================================================
// First, you need to configure the database where all Moodle data       //
// will be stored.  This database must already have been created         //
// and a username/password created to access it.                         //

$CFG->dbtype    = 'pgsql';      // 'pgsql', 'mysqli', 'mssql', 'sqlsrv' or 'oci'
$CFG->dblibrary = 'native';     // 'native' only at the moment
$CFG->dbhost    = 'localhost';  // eg 'localhost' or 'db.isp.com' or IP
$CFG->dbname    = 'moodle';     // database name, eg moodle
$CFG->dbuser    = 'username';   // your database username
$CFG->dbpass    = 'password';   // your database password
$CFG->prefix    = 'mdl_';       // prefix to use for all table names
$CFG->dboptions = array(
    'dbpersist' => false,       // should persistent database connections be
                                //  used? set to 'false' for the most stable
                                //  setting, 'true' can improve performance
                                //  sometimes
    'dbsocket'  => false,       // should connection via UNIX socket be used?
                                //  if you set it to 'true' or custom path
                                //  here set dbhost to 'localhost',
                                //  (please note mysql is always using socket
                                //  if dbhost is 'localhost' - if you need
                                //  local port connection use '127.0.0.1')
    'dbport'    => '',          // the TCP port number to use when connecting
                                //  to the server. keep empty string for the
                                //  default port
);


//=========================================================================
// 2. WEB SITE LOCATION
//=========================================================================
// Now you need to tell Moodle where it is located. Specify the full
// web address to where moodle has been installed.  If your web site
// is accessible via multiple URLs then choose the most natural one
// that your students would use.  Do not include a trailing slash
//
// If you need both intranet and Internet access please read
// http://docs.moodle.org/en/masquerading

$CFG->wwwroot   = 'http://example.com/moodle';


//=========================================================================
// 3. DATA FILES LOCATION
//=========================================================================
// Now you need a place where Moodle can save uploaded files.  This
// directory should be readable AND WRITEABLE by the web server user
// (usually 'nobody' or 'apache'), but it should not be accessible
// directly via the web.
//
// - On hosting systems you might need to make sure that your "group" has
//   no permissions at all, but that "others" have full permissions.
//
// - On Windows systems you might specify something like 'c:\moodledata'

$CFG->dataroot  = '/home/example/moodledata';


//=========================================================================
// 4. DATA FILES PERMISSIONS
//=========================================================================
// The following parameter sets the permissions of new directories
// created by Moodle within the data directory.  The format is in
// octal format (as used by the Unix utility chmod, for example).
// The default is usually OK, but you may want to change it to 0750
// if you are concerned about world-access to the files (you will need
// to make sure the web server process (eg Apache) can access the files.
// NOTE: the prefixed 0 is important, and don't use quotes.

$CFG->directorypermissions = 02777;


//=========================================================================
// 5. DIRECTORY LOCATION  (most people can just ignore this setting)
//=========================================================================
// A very few webhosts use /admin as a special URL for you to access a
// control panel or something.  Unfortunately this conflicts with the
// standard location for the Moodle admin pages.  You can work around this
// by renaming the admin directory in your installation, and putting that
// new name here.  eg "moodleadmin".  This should fix all admin links in Moodle.
// After any change you need to visit your new admin directory
// and purge all caches.

$CFG->admin = 'admin';


//=========================================================================
// 6. OTHER MISCELLANEOUS SETTINGS (ignore these for new installations)
//=========================================================================
//
// These are additional tweaks for which no GUI exists in Moodle yet.
//
// Starting in PHP 5.3 administrators should specify default timezone
// in PHP.ini, you can also specify it here if needed.
// See details at: http://php.net/manual/en/function.date-default-timezone-set.php
// List of time zones at: http://php.net/manual/en/timezones.php
//     date_default_timezone_set('Australia/Perth');
//
// Change the key pair lifetime for Moodle Networking
// The default is 28 days. You would only want to change this if the key
// was not getting regenerated for any reason. You would probably want
// make it much longer. Note that you'll need to delete and manually update
// any existing key.
//      $CFG->mnetkeylifetime = 28;
//
// Allow user passwords to be included in backup files. Very dangerous
// setting as far as it publishes password hashes that can be unencrypted
// if the backup file is publicy available. Use it only if you can guarantee
// that all your backup files remain only privacy available and are never
// shared out from your site/institution!
//      $CFG->includeuserpasswordsinbackup = true;
//
// Completely disable user creation when restoring a course, bypassing any
// permissions granted via roles and capabilities. Enabling this setting
// results in the restore process stopping when a user attempts to restore a
// course requiring users to be created.
//     $CFG->disableusercreationonrestore = true;
//
// Keep the temporary directories used by backup and restore without being
// deleted at the end of the process. Use it if you want to debug / view
// all the information stored there after the process has ended. Note that
// those directories may be deleted (after some ttl) both by cron and / or
// by new backup / restore invocations.
//     $CFG->keeptempdirectoriesonbackup = true;
//
// Modify the restore process in order to force the "user checks" to assume
// that the backup originated from a different site, so detection of matching
// users is performed with different (more "relaxed") rules. Note that this is
// only useful if the backup file has been created using Moodle < 1.9.4 and the
// site has been rebuilt from scratch using backup files (not the best way btw).
// If you obtain user conflicts on restore, rather than enabling this setting
// permanently, try restoring the backup on a different site, back it up again
// and then restore on the target server.
//    $CFG->forcedifferentsitecheckingusersonrestore = true;
//
// Prevent stats processing and hide the GUI
//      $CFG->disablestatsprocessing = true;
//
// Setting this to true will enable admins to edit any post at any time
//      $CFG->admineditalways = true;
//
// These variables define DEFAULT block variables for new courses
// If this one is set it overrides all others and is the only one used.
//      $CFG->defaultblocks_override = 'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity';
//
// These variables define the specific settings for defined course formats.
// They override any settings defined in the formats own config file.
//      $CFG->defaultblocks_site = 'site_main_menu,course_list:course_summary,calendar_month';
//      $CFG->defaultblocks_social = 'participants,search_forums,calendar_month,calendar_upcoming,social_activities,recent_activity,course_list';
//      $CFG->defaultblocks_topics = 'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity';
//      $CFG->defaultblocks_weeks = 'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity';
//
// These blocks are used when no other default setting is found.
//      $CFG->defaultblocks = 'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity';
//
// You can specify a different class to be created for the $PAGE global, and to
// compute which blocks appear on each page. However, I cannot think of any good
// reason why you would need to change that. It just felt wrong to hard-code the
// the class name. You are strongly advised not to use these to settings unless
// you are absolutely sure you know what you are doing.
//      $CFG->moodlepageclass = 'moodle_page';
//      $CFG->moodlepageclassfile = "$CFG->dirroot/local/myplugin/mypageclass.php";
//      $CFG->blockmanagerclass = 'block_manager';
//      $CFG->blockmanagerclassfile = "$CFG->dirroot/local/myplugin/myblockamanagerclass.php";
//
// Seconds for files to remain in caches. Decrease this if you are worried
// about students being served outdated versions of uploaded files.
//     $CFG->filelifetime = 86400;
//
// Some web servers can offload the file serving from PHP process,
// comment out one the following options to enable it in Moodle:
//     $CFG->xsendfile = 'X-Sendfile';           // Apache {@see https://tn123.org/mod_xsendfile/}
//     $CFG->xsendfile = 'X-LIGHTTPD-send-file'; // Lighttpd {@see http://redmine.lighttpd.net/projects/lighttpd/wiki/X-LIGHTTPD-send-file}
//     $CFG->xsendfile = 'X-Accel-Redirect';     // Nginx {@see http://wiki.nginx.org/XSendfile}
// If your X-Sendfile implementation (usually Nginx) uses directory aliases specify them
// in the following array setting:
//     $CFG->xsendfilealiases = array(
//         '/dataroot/' => $CFG->dataroot,
//         '/cachedir/' => '/var/www/moodle/cache',    // for custom $CFG->cachedir locations
//         '/tempdir/'  => '/var/www/moodle/temp',     // for custom $CFG->tempdir locations
//         '/filedir'   => '/var/www/moodle/filedir',  // for custom $CFG->filedir locations
//     );
//
// YUI caching may be sometimes improved by slasharguments:
//     $CFG->yuislasharguments = 1;
// Some servers may need a special rewrite rule to work around internal path length limitations:
// RewriteRule (^.*/theme/yui_combo\.php)(/.*) $1?file=$2
//
//
// By default all user sessions should be using locking, uncomment
// the following setting to prevent locking for guests and not-logged-in
// accounts. This may improve performance significantly.
//     $CFG->sessionlockloggedinonly = 1;
//
// If this setting is set to true, then Moodle will track the IP of the
// current user to make sure it hasn't changed during a session.  This
// will prevent the possibility of sessions being hijacked via XSS, but it
// may break things for users coming using proxies that change all the time,
// like AOL.
//      $CFG->tracksessionip = true;
//
// The following lines are for handling email bounces.
//      $CFG->handlebounces = true;
//      $CFG->minbounces = 10;
//      $CFG->bounceratio = .20;
// The next lines are needed both for bounce handling and any other email to module processing.
// mailprefix must be EXACTLY four characters.
// Uncomment and customise this block for Postfix
//      $CFG->mailprefix = 'mdl+'; // + is the separator for Exim and Postfix.
//      $CFG->mailprefix = 'mdl-'; // - is the separator for qmail
//      $CFG->maildomain = 'youremaildomain.com';
//
// Enable when setting up advanced reverse proxy load balancing configurations,
// it may be also necessary to enable this when using port forwarding.
//      $CFG->reverseproxy = true;
//
// Enable when using external SSL appliance for performance reasons.
// Please note that site may be accessible via https: or https:, but not both!
//      $CFG->sslproxy = true;
//
// This setting will cause the userdate() function not to fix %d in
// date strings, and just let them show with a zero prefix.
//      $CFG->nofixday = true;
//
// This setting will make some graphs (eg user logs) use lines instead of bars
//      $CFG->preferlinegraphs = true;
//
// Enabling this will allow custom scripts to replace existing moodle scripts.
// For example: if $CFG->customscripts/course/view.php exists then
// it will be used instead of $CFG->wwwroot/course/view.php
// At present this will only work for files that include config.php and are called
// as part of the url (index.php is implied).
// Some examples are:
//      http://my.moodle.site/course/view.php
//      http://my.moodle.site/index.php
//      http://my.moodle.site/admin            (index.php implied)
// Custom scripts should not include config.php
// Warning: Replacing standard moodle scripts may pose security risks and/or may not
// be compatible with upgrades. Use this option only if you are aware of the risks
// involved.
// Specify the full directory path to the custom scripts
//      $CFG->customscripts = '/home/example/customscripts';
//
// Performance profiling
//
//   If you set Debug to "Yes" in the Configuration->Variables page some
//   performance profiling data will show up on your footer (in default theme).
//   With these settings you get more granular control over the capture
//   and printout of the data
//
//   Capture performance profiling data
//   define('MDL_PERF'  , true);
//
//   Capture additional data from DB
//   define('MDL_PERFDB'  , true);
//
//   Print to log (for passive profiling of production servers)
//   define('MDL_PERFTOLOG'  , true);
//
//   Print to footer (works with the default theme)
//   define('MDL_PERFTOFOOT', true);
//
//   Enable earlier profiling that causes more code to be covered
//   on every request (db connections, config load, other inits...).
//   Requires extra configuration to be defined in config.php like:
//   profilingincluded, profilingexcluded, profilingautofrec,
//   profilingallowme, profilingallowall, profilinglifetime
//       $CFG->earlyprofilingenabled = true;
//
// Force displayed usernames
//   A little hack to anonymise user names for all students.  If you set these
//   then all non-teachers will always see these for every person.
//       $CFG->forcefirstname = 'Bruce';
//       $CFG->forcelastname  = 'Simpson';
//
// The following setting will turn SQL Error logging on. This will output an
// entry in apache error log indicating the position of the error and the statement
// called. This option will action disregarding error_reporting setting.
//     $CFG->dblogerror = true;
//
// The following setting will log every database query to a table called adodb_logsql.
// Use this setting on a development server only, the table grows quickly!
//     $CFG->logsql = true;
//
// The following setting will turn on username logging into Apache log. For full details regarding setting
// up of this function please refer to the install section of the document.
//     $CFG->apacheloguser = 0; // Turn this feature off. Default value.
//     $CFG->apacheloguser = 1; // Log user id.
//     $CFG->apacheloguser = 2; // Log full name in cleaned format. ie, Darth Vader will be displayed as darth_vader.
//     $CFG->apacheloguser = 3; // Log username.
// To get the values logged in Apache's log, add to your httpd.conf
// the following statements. In the General part put:
//     LogFormat "%h %l %{MOODLEUSER}n %t \"%r\" %s %b \"%{Referer}i\" \"%{User-Agent}i\"" moodleformat
// And in the part specific to your Moodle install / virtualhost:
//     CustomLog "/your/path/to/log" moodleformat
// CAUTION: Use of this option will expose usernames in the Apache log,
// If you are going to publish your log, or the output of your web stats analyzer
// this will weaken the security of your website.
//
// Email database connection errors to someone.  If Moodle cannot connect to the
// database, then email this address with a notice.
//
//     $CFG->emailconnectionerrorsto = 'your@emailaddress.com';
//
// Set the priority of themes from highest to lowest. This is useful (for
// example) in sites where the user theme should override all other theme
// settings for accessibility reasons. You can also disable types of themes
// (other than site)  by removing them from the array. The default setting is:
//      $CFG->themeorder = array('course', 'category', 'session', 'user', 'site');
// NOTE: course, category, session, user themes still require the
// respective settings to be enabled
//
// It is possible to add extra themes directory stored outside of $CFG->dirroot.
// This local directory does not have to be accessible from internet.
//
//     $CFG->themedir = '/location/of/extra/themes';
//
// It is possible to specify different cache and temp directories, use local fast filesystem
// for normal web servers. Server clusters MUST use shared filesystem for cachedir!
// The directories must not be accessible via web.
//
//     $CFG->tempdir = '/var/www/moodle/temp';
//     $CFG->cachedir = '/var/www/moodle/cache';
//
// Some filesystems such as NFS may not support file locking operations.
// Locking resolves race conditions and is strongly recommended for production servers.
//     $CFG->preventfilelocking = false;
//
// If $CFG->langstringcache is enabled (which should always be in production
// environment), Moodle keeps aggregated strings in its own internal format
// optimised for performance. By default, this on-disk cache is created in
// $CFG->cachedir/lang. In cluster environment, you may wish to specify
// an alternative location of this cache so that each web server in the cluster
// uses its own local cache and does not need to access the shared dataroot.
// Make sure that the web server process has write permission to this location
// and that it has permission to remove the folder, too (so that the cache can
// be pruned).
//
//     $CFG->langcacheroot = '/var/www/moodle/htdocs/altcache/lang';
//
// If $CFG->langcache is enabled (which should always be in production
// environment), Moodle stores the list of available languages in a cache file.
// By default, the file $CFG->dataroot/languages is used. You may wish to
// specify an alternative location of this cache file.
//
//     $CFG->langmenucachefile = '/var/www/moodle/htdocs/altcache/languages';
//
// Site default language can be set via standard administration interface. If you
// want to have initial error messages for eventual database connection problems
// localized too, you have to set your language code here.
//
//     $CFG->lang = 'yourlangcode'; // for example 'cs'
//
// When Moodle is about to perform an intensive operation it raises PHP's memory
// limit. The following setting should be used on large sites to set the raised
// memory limit to something higher.
// The value for the settings should be a valid PHP memory value. e.g. 512M, 1G
//
//     $CFG->extramemorylimit = '1G';
//
// Moodle 2.4 introduced a new cache API.
// The cache API stores a configuration file within the Moodle data directory and
// uses that rather than the database in order to function in a stand-alone manner.
// Using altcacheconfigpath you can change the location where this config file is
// looked for.
// It can either be a directory in which to store the file, or the full path to the
// file if you want to take full control. Either way it must be writable by the
// webserver.
//
//     $CFG->altcacheconfigpath = '/var/www/shared/moodle.cache.config.php
//
// The CSS files the Moodle produces can be extremely large and complex, especially
// if you are using a custom theme that builds upon several other themes.
// In Moodle 2.3 a CSS optimiser was added as an experimental feature for advanced
// users. The CSS optimiser organises the CSS in order to reduce the overall number
// of rules and styles being sent to the client. It does this by collating the
// CSS before it is cached removing excess styles and rules and stripping out any
// extraneous content such as comments and empty rules.
// The following settings are used to enable and control the optimisation.
//
// Enable the CSS optimiser. This will only optimise the CSS if themedesignermode
// is not enabled. This can be set through the UI however it is noted here as well
// because the other CSS optimiser settings can not be set through the UI.
//
//      $CFG->enablecssoptimiser = true;
//
// If set the CSS optimiser will add stats about the optimisation to the top of
// the optimised CSS file. You can then inspect the CSS to see the affect the CSS
// optimiser is having.
//
//      $CFG->cssoptimiserstats = true;
//
// If set the CSS that is optimised will still retain a minimalistic formatting
// so that anyone wanting to can still clearly read it.
//
//      $CFG->cssoptimiserpretty = true;
//
// Use the following flag to completely disable the Available update notifications
// feature and hide it from the server administration UI.
//
//      $CFG->disableupdatenotifications = true;
//
// Use the following flag to completely disable the Automatic updates deployment
// feature and hide it from the server administration UI.
//
//      $CFG->disableupdateautodeploy = true;
//
// Use the following flag to completely disable the On-click add-on installation
// feature and hide it from the server administration UI.
//
//      $CFG->disableonclickaddoninstall = true;
//
// As of version 2.4 Moodle serves icons as SVG images if the users browser appears
// to support SVG.
// For those wanting to control the serving of SVG images the following setting can
// be defined in your config.php.
// If it is not defined then the default (browser detection) will occur.
//
// To ensure they are always used when available:
//      $CFG->svgicons = true;
//
// To ensure they are never used even when available:
//      $CFG->svgicons = false;
//
// Some administration options allow setting the path to executable files. This can
// potentially cause a security risk. Set this option to true to disable editing
// those config settings via the web. They will need to be set explicitly in the
// config.php file
//      $CFG->preventexecpath = true;
//
//=========================================================================
// 7. SETTINGS FOR DEVELOPMENT SERVERS - not intended for production use!!!
//=========================================================================
//
// Force a debugging mode regardless the settings in the site administration
// @error_reporting(E_ALL | E_STRICT); // NOT FOR PRODUCTION SERVERS!
// @ini_set('display_errors', '1');    // NOT FOR PRODUCTION SERVERS!
// $CFG->debug = (E_ALL | E_STRICT);   // === DEBUG_DEVELOPER - NOT FOR PRODUCTION SERVERS!
// $CFG->debugdisplay = 1;             // NOT FOR PRODUCTION SERVERS!
//
// You can specify a comma separated list of user ids that that always see
// debug messages, this overrides the debug flag in $CFG->debug and $CFG->debugdisplay
// for these users only.
// $CFG->debugusers = '2';
//
// Prevent theme caching
// $CFG->themedesignermode = true; // NOT FOR PRODUCTION SERVERS!
//
// Prevent JS caching
// $CFG->cachejs = false; // NOT FOR PRODUCTION SERVERS!
//
// Prevent core_string_manager on-disk cache
// $CFG->langstringcache = false; // NOT FOR PRODUCTION SERVERS!
//
// When working with production data on test servers, no emails or other messages
// should ever be send to real users
// $CFG->noemailever = true;    // NOT FOR PRODUCTION SERVERS!
//
// Divert all outgoing emails to this address to test and debug emailing features
// $CFG->divertallemailsto = 'root@localhost.local'; // NOT FOR PRODUCTION SERVERS!
//
// Uncomment if you want to allow empty comments when modifying install.xml files.
// $CFG->xmldbdisablecommentchecking = true;    // NOT FOR PRODUCTION SERVERS!
//
// Since 2.0 sql queries are not shown during upgrade by default.
// Please note that this setting may produce very long upgrade page on large sites.
// $CFG->upgradeshowsql = true; // NOT FOR PRODUCTION SERVERS!
//
// Add SQL queries to the output of cron, just before their execution
// $CFG->showcronsql = true;
//
// Force developer level debug and add debug info to the output of cron
// $CFG->showcrondebugging = true;
//
//=========================================================================
// 8. FORCED SETTINGS
//=========================================================================
// It is possible to specify normal admin settings here, the point is that
// they can not be changed through the standard admin settings pages any more.
//
// Core settings are specified directly via assignment to $CFG variable.
// Example:
//   $CFG->somecoresetting = 'value';
//
// Plugin settings have to be put into a special array.
// Example:
//   $CFG->forced_plugin_settings = array('pluginname'  => array('settingname' => 'value', 'secondsetting' => 'othervalue'),
//                                        'otherplugin' => array('mysetting' => 'myvalue', 'thesetting' => 'thevalue'));
//
//=========================================================================
// 9. PHPUNIT SUPPORT
//=========================================================================
// $CFG->phpunit_prefix = 'phpu_';
// $CFG->phpunit_dataroot = '/home/example/phpu_moodledata';
// $CFG->phpunit_directorypermissions = 02777; // optional
//
//
//=========================================================================
// 10. SECRET PASSWORD SALT
//=========================================================================
// A single site-wide password salt is no longer required *unless* you are
// upgrading an older version of Moodle (prior to 2.5), or if you are using
// a PHP version below 5.3.7. If upgrading, keep any values from your old
// config.php file. If you are using PHP < 5.3.7 set to a long random string
// below:
//
// $CFG->passwordsaltmain = 'a_very_long_random_string_of_characters#@6&*1';
//
// You may also have some alternative salts to allow migration from previously
// used salts.
//
// $CFG->passwordsaltalt1 = '';
// $CFG->passwordsaltalt2 = '';
// $CFG->passwordsaltalt3 = '';
// ....
// $CFG->passwordsaltalt19 = '';
// $CFG->passwordsaltalt20 = '';
//
//
//=========================================================================
// 11. BEHAT SUPPORT
//=========================================================================
// Behat needs a separate data directory and unique database prefix:
//
// $CFG->behat_prefix = 'bht_';
// $CFG->behat_dataroot = '/home/example/bht_moodledata';
//
// Behat uses http://localhost:8000 as default URL to run
// the acceptance tests, you can override this value.
// Example:
//   $CFG->behat_wwwroot = 'http://192.168.1.250:8000';
//
// You can override default Moodle configuration for Behat and add your own
// params; here you can add more profiles, use different Mink drivers than Selenium...
// These params would be merged with the default Moodle behat.yml, giving priority
// to the ones specified here. The array format is YAML, following the Behat
// params hierarchy. More info: http://docs.behat.org/guides/7.config.html
// Example:
//   $CFG->behat_config = array(
//       'default' => array(
//           'formatter' => array(
//               'name' => 'pretty',
//               'parameters' => array(
//                   'decorated' => true,
//                   'verbose' => false
//               )
//           )
//       ),
//       'Mac-Firefox' => array(
//           'extensions' => array(
//               'Behat\MinkExtension\Extension' => array(
//                   'selenium2' => array(
//                       'browser' => 'firefox',
//                       'capabilities' => array(
//                           'platform' => 'OS X 10.6',
//                           'version' => 20
//                       )
//                   )
//               )
//           )
//       ),
//       'Mac-Safari' => array(
//           'extensions' => array(
//               'Behat\MinkExtension\Extension' => array(
//                   'selenium2' => array(
//                       'browser' => 'safari',
//                       'capabilities' => array(
//                           'platform' => 'OS X 10.8',
//                           'version' => 6
//                       )
//                   )
//               )
//           )
//       )
//   );
//
// You can completely switch to test environment when "php admin/tool/behat/cli/util --enable",
// this means that all the site accesses will be routed to the test environment instead of
// the regular one, so NEVER USE THIS SETTING IN PRODUCTION SITES. This setting is useful
// when working with cloud CI (continous integration) servers which requires public sites to run the
// tests, or in testing/development installations when you are developing in a pre-PHP 5.4 server.
// Note that with this setting enabled $CFG->behat_wwwroot is ignored and $CFG->behat_wwwroot
// value will be the regular $CFG->wwwroot value.
// Example:
//   $CFG->behat_switchcompletely = true;
//
// You can force the browser session (not user's sessions) to restart after N seconds. This could
// be useful if you are using a cloud-based service with time restrictions in the browser side.
// Setting this value the browser session that Behat is using will be restarted. Set the time in
// seconds. Is not recommended to use this setting if you don't explicitly need it.
// Example:
//   $CFG->behat_restart_browser_after = 7200;     // Restarts the browser session after 2 hours
//

//=========================================================================
// ALL DONE!  To continue installation, visit your main page with a browser
//=========================================================================

require_once(dirname(__FILE__) . '/lib/setup.php'); // Do not edit

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
