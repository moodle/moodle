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

$CFG->dbtype    = "mysql";       // eg mysql (postgres7, oracle, access etc coming soon)
$CFG->dbhost    = "localhost";   // eg localhost 
$CFG->dbname    = "moodle";      // eg moodle
$CFG->dbuser    = "username";
$CFG->dbpass    = "password";


// Next you need to tell Moodle where it is located.
// Specify the full URL that moodle has been installed in:

$CFG->wwwroot   = "http://example.com/moodle";


// and now the full OS directory path to this same location:

$CFG->dirroot   = "/web/moodle";


// Now you need a place where Moodle can save uploaded files.  This directory 
// should be writeable by the web server user (usually 'nobody' or 'apache'), 
// but it should not be accessible directly via the web.

$CFG->dataroot  = "/home/moodledata";


// Choose a theme from the "themes" folder.  Current choices include 
// "standard", "standardblue", "standardgreen" and "standardred", 
// but feel free to copy one and make new themes!

$CFG->theme     = "standard";


// Choose a sitewide language - this will affect text, buttons etc
// See lib/languages.php for a full list of standard language codes.

$CFG->lang     = "en";      // Currently the only option


// Choose a sitewide locale - this will affect the display of dates
// You need to have this locale data installed on your operating 
// system.  If you don't know what to choose try using the same 
// string as the language.

$CFG->locale     = "en";


// Give the full names of local SMTP servers that Moodle should use to
// send mail (eg "mail.a.com" or "mail.a.com;mail.b.com").
// If this is left empty (eg "") then Moodle will attempt to use PHP mail.

$CFG->smtphosts  = "";


// There is no way, currently, for PHP to automatically tell whether the 
// graphic library GD is version 1.* or 2.*.  Specify here (either 1 or 2).

$CFG->gdversion = 1;


// If students haven't logged in for a very long time, then they are 
// automatically unsubscribed from courses.  This parameter specifies
// that time limit, in DAYS.

$CFG->longtimenosee = 100;


// You should not need to change anything else. To continue setting up 
// Moodle, use your web browser to go to the moodle/admin web page.
///////////////////////////////////////////////////////////////////////////

$CFG->libdir    = "$CFG->dirroot/lib";

require("$CFG->libdir/setup.php");  // Sets up all libraries, sessions etc

?>
