<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
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

/// This file contains all the constants and variables used
/// by the XMLDB interface

/// First, some constants to be used by actions
    define('ACTION_NONE',             0);  //Default flags for class
    define('ACTION_GENERATE_HTML',    1);  //The invoke function will return HTML
    define('ACTION_GENERATE_XML',     2);  //The invoke function will return HTML
    define('ACTION_HAVE_SUBACTIONS',  1);  //The class can have subaction

/// Now the allowed DB Field Types
    define ('XMLDB_TYPE_INCORRECT',   0);  //Wrong DB Type
    define ('XMLDB_TYPE_INTEGER',     1);  //Integer
    define ('XMLDB_TYPE_NUMBER',      2);  //Decimal number
    define ('XMLDB_TYPE_FLOAT',       3);  //Floating Point number
    define ('XMLDB_TYPE_CHAR',        4);  //String
    define ('XMLDB_TYPE_TEXT',        5);  //Text
    define ('XMLDB_TYPE_BINARY',      6);  //Binary
    define ('XMLDB_TYPE_DATETIME',    7);  //Datetime
    define ('XMLDB_TYPE_TIMESTAMP',   8);  //Timestamp

/// Now the allowed DB Keys
    define ('XMLDB_KEY_INCORRECT',     0);  //Wrong DB Key
    define ('XMLDB_KEY_PRIMARY',       1);  //Primary Keys
    define ('XMLDB_KEY_UNIQUE',        2);  //Unique Keys
    define ('XMLDB_KEY_FOREIGN',       3);  //Foreign Keys
    define ('XMLDB_KEY_CHECK',         4);  //Check Constraints - NOT USED!
    define ('XMLDB_KEY_FOREIGN_UNIQUE',5);  //Foreign Key + Unique Key

/// Now the allowed Statement Types
    define ('XMLDB_STATEMENT_INCORRECT',   0);  //Wrong Statement Type
    define ('XMLDB_STATEMENT_INSERT',      1);  //Insert Statements
    define ('XMLDB_STATEMENT_UPDATE',      2);  //Update Statements
    define ('XMLDB_STATEMENT_DELETE',      3);  //Delete Statements
    define ('XMLDB_STATEMENT_CUSTOM',      4);  //Custom Statements

/// Some other useful Constants
    define ('XMLDB_UNSIGNED',        true);  //If the field is going to be unsigned
    define ('XMLDB_NOTNULL',         true);  //If the field is going to be not null
    define ('XMLDB_SEQUENCE',        true);  //If the field is going to be a sequence
    define ('XMLDB_ENUM',            true);  //If the field is going to be a enumeration of possible fields
    define ('XMLDB_INDEX_UNIQUE',    true);  //If the index is going to be unique
    define ('XMLDB_INDEX_NOTUNIQUE',false);  //If the index is NOT going to be unique

/// Some strings used widely
    define ('XMLDB_LINEFEED', "\n");
    define ('XMLDB_PHP_HEADER', '    if ($result && $oldversion < XXXXXXXXXX) {' . XMLDB_LINEFEED);
    define ('XMLDB_PHP_FOOTER', '    }' . XMLDB_LINEFEED);
?>
