<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas     http://dougiamas.com  //
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

// This library includes all the required functions used to handle the DB
// structure (DDL) independently of the underlying RDBMS in use. All the functions
// rely on the XMLDBDriver classes to be able to generate the correct SQL
// syntax needed by each DB.
//
// To define any structure to be created we'll use the schema defined
// by the XMLDB classes, for tables, fields, indexes, keys and other
// statements instead of direct handling of SQL sentences.
//
// This library should be used, exclusively, by the installation and
// upgrade process of Moodle.
//
// For further documentation, visit http://docs.moodle.org/en/DDL_functions

/// Add required XMLDB constants
require_once($CFG->libdir.'/xmldb/xmldb_constants.php');

/// Add required XMLDB DB classes
require_once($CFG->libdir.'/xmldb/xmldb_object.php');
require_once($CFG->libdir.'/xmldb/xmldb_file.php');
require_once($CFG->libdir.'/xmldb/xmldb_structure.php');
require_once($CFG->libdir.'/xmldb/xmldb_table.php');
require_once($CFG->libdir.'/xmldb/xmldb_field.php');
require_once($CFG->libdir.'/xmldb/xmldb_key.php');
require_once($CFG->libdir.'/xmldb/xmldb_index.php');
require_once($CFG->libdir.'/xmldb/xmldb_statement.php');

require_once($CFG->libdir.'/ddl/sql_generator.php');
require_once($CFG->libdir.'/ddl/database_manager.php');

