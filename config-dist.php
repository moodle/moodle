<?PHP // $Id$
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
// the Free Software Foundation; either version 2 of the License, or     //
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
$CFG = new stdClass();

//=========================================================================
// 1. DATABASE SETUP
//=========================================================================
// First, you need to configure the database where all Moodle data       //
// will be stored.  This database must already have been created         //
// and a username/password created to access it.                         //
//                                                                       //
//   mysql      - the prefix is optional, but useful when installing     //
//                into databases that already contain tables.            //
//
//   postgres7  - the prefix is REQUIRED, regardless of whether the      //
//                database already contains tables.                      //
//                                                                       //
// A special case exists when using PostgreSQL databases via sockets.    //
// Define dbhost as follows, leaving dbname, dbuser, dbpass BLANK!:      //
//    $CFG->dbhost = " user='muser' password='mpass' dbname='mdata'";    //
//

$CFG->dbtype    = 'mysql';       // mysql or postgres7 (for now)
$CFG->dbhost    = 'localhost';   // eg localhost or db.isp.com
$CFG->dbname    = 'moodle';      // database name, eg moodle
$CFG->dbuser    = 'username';    // your database username
$CFG->dbpass    = 'password';    // your database password
$CFG->prefix    = 'mdl_';        // Prefix to use for all table names

$CFG->dbpersist = false;         // Should database connections be reused?
                 // "false" is the most stable setting
                 // "true" can improve performance sometimes


//=========================================================================
// 1.5. SECRET PASSWORD SALT
//=========================================================================
// User password salt is very important security feature, it is created
// automatically in installer, you have to uncomment and modify value
// on the next line if you are creating config.php manually.
//
// $CFG->passwordsaltmain = 'a_very_long_random_string_of_characters#@6&*1';
//
// After changing the main salt you have to copy old value into one
// of the following settings - this allows migration to the new salt
// during the next login of each user.
//
// $CFG->passwordsaltalt1 = '';
// $CFG->passwordsaltalt2 = '';
// $CFG->passwordsaltalt3 = '';
// ....
// $CFG->passwordsaltalt19 = '';
// $CFG->passwordsaltalt20 = '';


//=========================================================================
// 2. WEB SITE LOCATION
//=========================================================================
// Now you need to tell Moodle where it is located. Specify the full
// web address to where moodle has been installed.  If your web site
// is accessible via multiple URLs then choose the most natural one
// that your students would use.  Do not include a trailing slash

$CFG->wwwroot   = 'http://example.com/moodle';


//=========================================================================
// 3. SERVER FILES LOCATION
//=========================================================================
// Next, specify the full OS directory path to this same location
// Make sure the upper/lower case is correct.  Some examples:
//
//    $CFG->dirroot = 'C:\program files\easyphp\www\moodle';    // Windows
//    $CFG->dirroot = '/var/www/html/moodle';     // Redhat Linux
//    $CFG->dirroot = '/home/example/public_html/moodle'; // Cpanel host

$CFG->dirroot   = '/home/example/public_html/moodle';


//=========================================================================
// 4. DATA FILES LOCATION
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
// 5. DATA FILES PERMISSIONS
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
// 6. DIRECTORY LOCATION  (most people can just ignore this setting)
//=========================================================================
// A very few webhosts use /admin as a special URL for you to access a
// control panel or something.  Unfortunately this conflicts with the
// standard location for the Moodle admin pages.  You can fix this by
// renaming the admin directory in your installation, and putting that
// new name here.  eg "moodleadmin".  This will fix admin links in Moodle.

$CFG->admin = 'admin';


//=========================================================================
// 7. OTHER MISCELLANEOUS SETTINGS (ignore these for new installations)
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
// Prevent scheduled backups from operating (and hide the GUI for them)
// Useful for webhost operators who have alternate methods of backups
//      $CFG->disablescheduledbackups = true;
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
//      $CFG->defaultblocks_override = 'participants,activity_modules,search_forums,admin,course_list:news_items,calendar_upcoming,recent_activity';
//
// These variables define the specific settings for defined course formats.
// They override any settings defined in the formats own config file.
//      $CFG->defaultblocks_site = 'site_main_menu,admin,course_list:course_summary,calendar_month';
//      $CFG->defaultblocks_social = 'participants,search_forums,calendar_month,calendar_upcoming,social_activities,recent_activity,admin,course_list';
//      $CFG->defaultblocks_topics = 'participants,activity_modules,search_forums,admin,course_list:news_items,calendar_upcoming,recent_activity';
//      $CFG->defaultblocks_weeks = 'participants,activity_modules,search_forums,admin,course_list:news_items,calendar_upcoming,recent_activity';
//
// These blocks are used when no other default setting is found.
//      $CFG->defaultblocks = 'participants,activity_modules,search_forums,admin,course_list:news_items,calendar_upcoming,recent_activity';
//
//
// Allow unicode characters in uploaded files, generated reports, etc.
// This setting is new and not much tested, there are known problems
// with backup/restore that will not be solved, because native infozip
// binaries are doing some weird conversions - use internal PHP zipping instead.
// NOT RECOMMENDED FOR PRODUCTION SITES
//     $CFG->unicodecleanfilename = true;
//
// Seconds for files to remain in caches. Decrease this if you are worried
// about students being served outdated versions of uploaded files.
//     $CFG->filelifetime = 86400;
//
// This setting will prevent the 'My Courses' page being displayed when a student
// logs in. The site front page will always show the same (logged-out) view.
//     $CFG->disablemycourses = true;
//
// Enable this option if you need fully working default frontpage role,
// please note it might cause serious memory and performance issues,
// also there should not be any negative capabilities in default
// frontpage role (MDL-19039).
//     $CFG->fullusersbycapabilityonfrontpage = true;
//
// If this setting is set to true, then Moodle will track the IP of the 
// current user to make sure it hasn't changed during a session.  This 
// will prevent the possibility of sessions being hijacked via XSS, but it 
// may break things for users coming using proxies that change all the time,
// like AOL.
//      $CFG->tracksessionip = true;
//
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
// The following setting will tell Moodle to respect your PHP session 
// settings. Use this if you want to control session configuration
// from php.ini, httpd.conf or .htaccess files. 
//      $CFG->respectsessionsettings = true;
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
// By default, only queries that take longer than 0.05 seconds are logged. To change that,
// set the following variable. For example, to lot all queries:
//     $CFG->logsqlmintime = 0.0;
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
// NOTE: if you are using custompix in your theme, see /fixpix.php.
// 
// special magic evil developer only wanting to edit the xmldb files manually
// AND don't use the XMLDBEditor nor the prev/next stuff at all (Mahara and others)
// Uncomment these if you're lazy like Penny
// $CFG->xmldbdisablecommentchecking = true;
// $CFG->xmldbdisablenextprevchecking = true;
//
// special magig evil developer only wanting to edit xmldb files manually
// AND allowing the XMLDBEditor to recostruct the prev/next elements every
// time one file is loaded and saved (Moodle).
// Uncomment this if you're lazy like Petr
// $CFG->xmldbreconstructprevnext = true;
//
// Set the priority of themes from highest to lowest. This is useful (for
// example) in sites where the user theme should override all other theme
// settings for accessibility reasons. You can also disable types of themes
// by removing them from the array. The default setting is:
//      $CFG->themeorder = array('page', 'course', 'category', 'session', 'user', 'site');
// NOTE: course, category, session, user themes still require the
// respective settings to be enabled
//
// When working with production data on test servers, no emails should ever be send to real users
// $CFG->noemailever = true;
//
//
//=========================================================================
// ALL DONE!  To continue installation, visit your main page with a browser
//=========================================================================
if ($CFG->wwwroot == 'http://example.com/moodle') {
    echo "<p>Error detected in configuration file</p>";
    echo "<p>Your server address can not be: \$CFG->wwwroot = 'http://example.com/moodle';</p>";
    die;
}

if (file_exists("$CFG->dirroot/lib/setup.php"))  {       // Do not edit
    include_once("$CFG->dirroot/lib/setup.php");
} else {
    if ($CFG->dirroot == dirname(__FILE__)) {
        echo "<p>Could not find this file: $CFG->dirroot/lib/setup.php</p>";
        echo "<p>Are you sure all your files have been uploaded?</p>";
    } else {
        echo "<p>Error detected in config.php</p>";
        echo "<p>Error in: \$CFG->dirroot = '$CFG->dirroot';</p>";
        echo "<p>Try this: \$CFG->dirroot = '".dirname(__FILE__)."';</p>";
    }
    die;
}
// MAKE SURE WHEN YOU EDIT THIS FILE THAT THERE ARE NO SPACES, BLANK LINES,
// RETURNS, OR ANYTHING ELSE AFTER THE TWO CHARACTERS ON THE NEXT LINE.
?>
