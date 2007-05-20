$Id: README.txt,v 1.3 2007/05/20 06:00:29 skodak Exp $

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999-2004  Martin Dougiamas  http://dougiamas.com       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// Book module original code                                             //
// Copyright (C) 2004  Petr Skoda (petr.skoda@vslib.cz)                  //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
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

Book module for Moodle - version 1.1 (stable release)
===============================================================================
Created by:
      Petr Skoda (skodak) - most of the coding & design
      Mojmir Volf         - CSS formatted printer friendly format

Developed for Technical University of Liberec (Czech Republic).
Many ideas and code were taken from other Moodle modules and Moodle itself;-)

Installation:
    1/ upgrade your Moodle installation to version 1.5.1 or later
       (you are advised to install latest official stable 1.5.2+ branch)
    2/ download book.zip to your moodle/mod directory and unzip it there
    3/ go to http://yoursite.com/admin - all necessary tables will be created


List of features - version 1.2:

    * easy to use - new teachers can quickly create multipage study materials
    * two levels - only chapters and subchapters
    * possible automatic chapter numbering
    * chapters authored in build-in HTML editor (html stored in database,
      images in course data)
    * printing support - all chapters can be displayed on one CSS formatted page
    * backup/restore with internal link preservation
    * support for global searching in study materials (see contrib/search)
    * import from html files (relinking of images, flash, Java applets and
      relative links)
    * works with MySQL and PostgreSQL databases
    * no need to move language packs any more


Intentionally omitted features:

    * more chapter levels - it would encourage teachers to write too much
      complex and long books, better use standard standalone HTML editor and
      import it as Resource. DocBook format is another suitable solution.
    * TOC hiding in normal view - instead use printer friendly view
    * PDF export - there is no elegant way AFAIK to convert HTML to PDF,
      use virtual PDF printer or better use DocBook format for authoring
    * detailed student tracking (postponed till officially supported)
    * export as zipped set of HTML pages - instead use browser command
      Save page as... in print view

Future:
    * I like eXe editor, I would like to improve Book in this direction ... ;-)

CHANGELOG:
== 1.1RC1 - 2004/11/15 =======================================================
    * compatible ONLY with 1.4.2 and later !!!
    * added sesskey for enhanced security
    * navigation links do not enter hidden chapters
    * import - correct linking when slasharguments are off
    * removed coursefiles.php
== 1.1    - 2005/01/01 =======================================================
    * compatible ONLY with 1.4.3 and later!
    * removed some unused searching stuff
    * fixed headers in print.php
== 1.2RC - 2005/07/13 ==========================================================
    * compatible with 1.5.1 + only
    * improved restore
    * fixed selection of directories for import
    * html editor for summary
    * fixed postgresql upgrade
    * no need to move language packs
== 1.2 - 2006/03/11 ==========================================================
    * removed 64kB page content limit in mysql
    * exit button now goes to course section anchor
    * chater title now in browser title bar
    * added translations be and sv
    * during import chapters with names "*_sub.htm?" are imported as subchapters
    * deleting in import selection windows now works
    * last version before transition to 1.6
== 1.3alpha - 2006/03/12 ======================================================
    * compatible with 1.6dev only, use previous version for 1.5.x
    * unicode upgrade supported - not much tested
    * backup fixes - from Penny Leach, thanks!
    * all languages converted to utf-8
    * moved to contrib/book_16 directory
    * added proper content encoding conversion during import
== 1.4alpha - 2007/05/20 ====================================================
    * compatible with 1.8
    * export link enabled - thanks Eloy!
    * no new features

skodak
