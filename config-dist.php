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
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2003  Martin Dougiamas  http://dougiamas.com       //
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

$CFG->dbtype    = 'mysql';       // mysql or postgres7 (for now)
$CFG->dbhost    = 'localhost';   // eg localhost or db.isp.com 
$CFG->dbname    = 'moodle';      // database name, eg moodle
$CFG->dbuser    = 'username';    // your database username
$CFG->dbpass    = 'password';    // your database password

$CFG->dbpersist = true;          // Use persistent database connection? 
                                 // (should be 'true' for 99% of sites)

$CFG->prefix    = 'mdl_';        // Prefix to use for all table names


//=========================================================================
// 2. WEB SITE LOCATION
//=========================================================================
// Now you need to tell Moodle where it is located. Specify the full
// web address to where moodle has been installed.  If your web site 
// is accessible via multiple URLs then choose the most natural one 
// that your students would use.  Do not include a trailing slash.

$CFG->wwwroot   = 'http://example.com/moodle';


//=========================================================================
// 3. SERVER FILES LOCATION
//=========================================================================
// Next, specify the full OS directory path to this same location
// For Windows this might be something like this:
//
//    $CFG->dirroot = 'C:\Program Files\Easyphp\www\moodle';
//
// NOTE: Make sure all the upper/lower case is EXACTLY the same as it is 
// on your computer otherwise you may experience some problems (bug in PHP)

$CFG->dirroot   = '/web/moodle';


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
// - On Windows systems you might specify something like 'C:\moodledata'

$CFG->dataroot  = '/home/moodledata';


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
// ALL DONE!  To continue your setup, visit your Moodle web page.
//=========================================================================

require_once("$CFG->dirroot/lib/setup.php");       // Do not change this

// MAKE SURE WHEN YOU EDIT THIS FILE THAT THERE ARE NO SPACES, BLANK LINES, 
// RETURNS, OR ANYTHING ELSE AFTER THE TWO CHARACTERS ON THE NEXT LINE.
?>
