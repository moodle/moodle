<?PHP // $Id$
///////////////////////////////////////////////////////////////////////////
//
// Moodle configuration file
// 
// This file should be located in the top-level directory.
//
///////////////////////////////////////////////////////////////////////////
// 
// NOTICE OF COPYRIGHT
//
// Moodle - Modular Object-Oriented Dynamic Learning Environment
//          http://moodle.com
// 
// Copyright (C) 2001  Martin Dougiamas  http://dougiamas.com
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details:
// 
//          http://www.gnu.org/copyleft/gpl.html
// 
////////////////////////////////////////////////////////////////////////////
//
// Site configuration variables are all stored in the CFG object.

// First, you need to configure the database where all Moodle data 
// will be stored.  This database must already have been created
// and a username/password created to access it.   See doc/INSTALL.

$CFG->dbtype    = "mysql";       // eg mysql (postgres7, oracle, access coming soon)
$CFG->dbhost    = "localhost";   // eg localhost 
$CFG->dbname    = "moodle";      // eg moodle
$CFG->dbuser    = "username";
$CFG->dbpass    = "password";


// Next you need to tell Moodle where it is located

$CFG->wwwroot   = "http://example.com/moodle";
$CFG->dirroot   = "/web/moodle";


// And where it can save files.  This directory should be writeable
// by the web server user (usually 'nobody' or 'apache'), but it should 
// not be accessible directly via the web.

$CFG->dataroot  = "/home/moodledata";


// Choose a theme from the "themes" folder.  Default theme is "standard".

$CFG->theme     = "standard";


// Choose a sitewide language - this will affect navigation, help etc

$CFG->lang     = "en";


// Give the full name (eg mail.example.com) of an SMTP server that the 
// web server machine has access to (to send mail).  You can specify 
// more than one server like this: "mail1.example.com;mail2.example.com"

$CFG->smtphosts  = "mail.example.com";

// Choose a password to be used by the cron script.  This helps avoid
// any problems caused by someone spamming moodle/admin/cron.php

$CFG->cronpassword = "fr0o6y";


// You should not need to change anything else. To continue setting up 
// Moodle, use your web browser to go to the moodle/admin web page.
///////////////////////////////////////////////////////////////////////////

$CFG->libdir    = "$CFG->dirroot/lib";

require("$CFG->libdir/setup.php");  // Sets up all libraries, sessions etc

?>
