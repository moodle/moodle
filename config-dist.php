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


//=========================================================================
// 1. DATABASE SETUP
//=========================================================================
// First, you need to configure the database where all Moodle data       //
// will be stored.  This database must already have been created         //
// and a username/password created to access it.                         //
//                                                                       //
//   mysql      - the prefix is optional, but useful when installing     //
//                into databases that already contain tables.            //
//   postgres7  - the prefix is REQUIRED, regardless of whether the      //
//                database is empty of not.                              //

$CFG->dbtype    = "mysql";       // mysql or postgres7 (for now)
$CFG->dbhost    = "localhost";   // eg localhost or db.isp.com 
$CFG->dbname    = "moodle";      // database name, eg moodle
$CFG->dbuser    = "username";    // your database username
$CFG->dbpass    = "password";    // your database password

$CFG->prefix    = "mdl_";        // Prefix to use for all table names


//=========================================================================
// 2. WEB SITE LOCATION
//=========================================================================
// Now you need to tell Moodle where it is located. Specify the full
// web address where moodle has been installed (without trailing slash)

$CFG->wwwroot   = "http://example.com/moodle";


//=========================================================================
// 3. SERVER FILES LOCATION
//=========================================================================
// Next, specify the full OS directory path to this same location
// For Windows this might be something like "C:\apache\htdocs\moodle"

$CFG->dirroot   = "/web/moodle";


//=========================================================================
// 4. DATA FILES LOCATION
//=========================================================================
// Now you need a place where Moodle can save uploaded files.  This
// directory should be writeable by the web server user (usually 'nobody'
// or 'apache'), but it should not be accessible directly via the web.
// - On hosting systems you might need to make sure that your "group" has
//   no permissions at all, but that "others" have full permissions.
// - On Windows systems you might specify something like "C:\moodledata"

$CFG->dataroot  = "/home/moodledata";


//=========================================================================
// 5. TROUBLESHOOTING  (most people can just ignore this setting)
//=========================================================================
// A very small percentage of servers have a bug which causes HTTP_REFERER
// not to work.  The symptoms of this are that you fill out the configure
// form during Moodle setup but when hit save you find yourself on the 
// same form, unable to progress.  If this happens to you, come back here
// and set the following to true.  Otherwise this should always be false.

$CFG->buggy_referer = false;


//=========================================================================
// 6. ALL DONE!  To continue your setup, visit your Moodle web page.
//=========================================================================


$CFG->libdir    = "$CFG->dirroot/lib";   // Do not change this
require_once("$CFG->libdir/setup.php");       // Do not change this

// MAKE SURE WHEN YOU EDIT THIS FILE THAT THERE ARE NO SPACES, BLANK LINES, 
// RETURNS, OR ANYTHING ELSE AFTER THE TWO CHARACTERS ON THE NEXT LINE.
?>
