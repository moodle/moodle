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
// Copyright (C) 1999-2004  Martin Dougiamas  http://dougiamas.com       //
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
//    $CFG->dirroot = 'c:\program files\easyphp\www\moodle';    // Windows
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

$CFG->directorypermissions = 0777;


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
//
// Prevent users from updating their profile images
//      $CFG->disableuserimages = true;  
//
// Prevent scheduled backups from operating (and hide the GUI for them)
// Useful for webhost operators who have alternate methods of backups
//      $CFG->disablescheduledbackups = true;
//
// Restrict certain usernames from doing things that may mess up a site
// This is especially useful for demonstration teacher accounts
//      $CFG->restrictusers = 'teacher,fred,jim';
//
// Turning this on will make Moodle filter more than usual, including
// forum subjects, activity names and so on (in ADDITION to the normal 
// texts like forum postings, journals etc).  This is mostly only useful 
// when using the multilang filter.   This feature may not be complete.
//      $CFG->filterall = true;
//
// Setting this to true will enable admins to edit any post at any time
//      $CFG->admineditalways = true;
//
// This variable will override the default block configuration on newly
// created courses, or on upgraded courses from Moodle 1.2.1 and earlier.
// The names here should all be existing blocks in the "blocks" directory.
//      $CFG->defaultblocks = "participants,activity_modules,search_forums,admin,course_list:news_items,calendar_upcoming,recent_activity";
//
// This setting will put Moodle in Unicode mode.  It's very new and 
// most likely doesn't work yet.   THIS IS FOR DEVELOPERS ONLY, IT IS
// NOT RECOMMENDED FOR PRODUCTION SITES
//      $CFG->unicode = true;



//=========================================================================
// ALL DONE!  To continue installation, visit your main page with a browser
//=========================================================================
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
