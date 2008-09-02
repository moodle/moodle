<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas     http://dougiamas.com  //
// Copyright (C) 2008 onwards Andrei Bautu                               //
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

// DTL == Dtatabase Transfer Library
// This library includes all the required functions used to handle
// transfer of data from one database to another.

require_once($CFG->libdir.'/ddllib.php');

require_once($CFG->libdir.'/dtl/database_exporter.php');
require_once($CFG->libdir.'/dtl/xml_database_exporter.php');
require_once($CFG->libdir.'/dtl/file_xml_database_exporter.php');
require_once($CFG->libdir.'/dtl/string_xml_database_exporter.php');
require_once($CFG->libdir.'/dtl/database_mover.php');
require_once($CFG->libdir.'/dtl/database_importer.php');
require_once($CFG->libdir.'/dtl/xml_database_importer.php');
require_once($CFG->libdir.'/dtl/file_xml_database_importer.php');
require_once($CFG->libdir.'/dtl/string_xml_database_importer.php');

/**
 * Exception class for db transfer
 * @see moodle_exception
 */
class dbtransfer_exception extends moodle_exception {
    function __construct($errorcode, $a=null, $link='', $debuginfo=null) {
        global $CFG;
        if (empty($link)) {
            $link = "$CFG->wwwroot/$CFG->admin/";
        }
        parent::__construct($errorcode, 'dbtransfer', $link, $a, $debuginfo);
    }
}

