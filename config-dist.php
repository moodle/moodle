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
// Copyright (C) 2001-2002  Martin Dougiamas  http://dougiamas.com       //
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


///////////////////////////////////////////////////////////////////////////
// First, you need to configure the database where all Moodle data       //
// will be stored.  This database must already have been created         //
// and a username/password created to access it.  If you specify mysql   //
// then Moodle can set up all your tables for you.  If you try to use    //
// a different database you will need to set up all your tables by hand  //
// which could be a big job.    See doc/install.html                     //

$CFG->dbtype    = "mysql";     // mysql or postgres7 
$CFG->dbhost    = "localhost"; // eg localhost 
$CFG->dbname    = "moodletest";    // eg moodle
$CFG->dbuser    = "username";
$CFG->dbpass    = "password";

$CFG->prefix    = "mdl_";      // Prefix value to use for all table names


///////////////////////////////////////////////////////////////////////////
// Now you need to tell Moodle where it is located. Specify the full
// web address where moodle has been installed (without trailing slash)

$CFG->wwwroot   = "http://example.com/moodle";


///////////////////////////////////////////////////////////////////////////
// Next, specify the full OS directory path to this same location
// For Windows this might be something like "C:\apache\htdocs\moodle"

$CFG->dirroot   = "/web/moodle";


///////////////////////////////////////////////////////////////////////////
// Now you need a place where Moodle can save uploaded files.  This
// directory should be writeable by the web server user (usually 'nobody'
// or 'apache'), but it should not be accessible directly via the web.
// On Windows systems you might specify something like "C:\moodledata"

$CFG->dataroot  = "/home/moodledata";


///////////////////////////////////////////////////////////////////////////
// A very small percentage of servers have a bug which causes HTTP_REFERER
// not to work.  The symptoms of this are that you fill out the configure
// form during Moodle setup but when hit save you find yourself on the 
// same form, unable to progress.  If this happens to you, come back here
// and set the following to true.  Otherwise this should always be false.

$CFG->buggy_referer = false;


///////////////////////////////////////////////////////////////////////////
// To continue the setup, use your web browser to go to your Moodle page //
///////////////////////////////////////////////////////////////////////////

$CFG->libdir    = "$CFG->dirroot/lib";   // Do not change this
require("$CFG->libdir/setup.php");       // Do not change this

?>
