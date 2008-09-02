<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas     http://dougiamas.com  //
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

/// This library contains all the Data Manipulation Language (DML) functions
/// used to interact with the DB. All the dunctions in this library must be
/// generic and work against the major number of RDBMS possible. This is the
/// list of currently supported and tested DBs: mysql, postresql, mssql, oracle

/// This library is automatically included by Moodle core so you never need to
/// include it yourself.

/// For more info about the functions available in this library, please visit:
///     http://docs.moodle.org/en/DML_functions
/// (feel free to modify, improve and document such page, thanks!)

require_once($CFG->libdir.'/dml/moodle_database.php');

/**
 * Sets up global $DB moodle_database instance
 * @return void
 */
function setup_DB() {
    global $CFG, $DB;

    if (isset($DB)) {
        return;
    }

    if (!isset($CFG->dbuser)) {
        $CFG->dbuser = '';
    }

    if (!isset($CFG->dbpass)) {
        $CFG->dbpass = '';
    }

    if (!isset($CFG->dbname)) {
        $CFG->dbname = '';
    }

    if (!isset($CFG->dbpersist)) {
        $CFG->dbpersist = false;
    }

    if (!isset($CFG->dblibrary)) {
        $CFG->dblibrary = 'adodb';
    }

    if (!isset($CFG->dboptions)) {
        $CFG->dboptions = array();
    }

    $DB = moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary);

    $CFG->dbfamily = $DB->get_dbfamily(); // TODO: BC only for now

    $driverstatus = $DB->driver_installed();

    if ($driverstatus !== true) {
        print_error('dbdriverproblem', 'error', '', $driverstatus);
    }

    if (debugging('', DEBUG_ALL)) {
        // catch errors
        ob_start();
    } else {
        $prevdebug = error_reporting(0);
    }
    if (!$DB->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->dbpersist, $CFG->prefix, $CFG->dboptions)) {
        if (debugging('', DEBUG_ALL)) {
            if ($dberr = ob_get_contents()) {
                $dberr = '<p><em>'.$dberr.'</em></p>';
            }
            ob_end_clean();
        } else {
            $dberr = '';
        }
        if (empty($CFG->noemailever) and !empty($CFG->emailconnectionerrorsto)) {
            if (file_exists($CFG->dataroot.'/emailcount')){
                $fp = fopen($CFG->dataroot.'/emailcount', 'r');
                $content = fread($fp, 24);
                fclose($fp);
                if((time() - (int)$content) > 600){
                    @mail($CFG->emailconnectionerrorsto,
                        'WARNING: Database connection error: '.$CFG->wwwroot,
                        'Connection error: '.$CFG->wwwroot);
                    $fp = fopen($CFG->dataroot.'/emailcount', 'w');
                    fwrite($fp, time());
                }
            } else {
               @mail($CFG->emailconnectionerrorsto,
                    'WARNING: Database connection error: '.$CFG->wwwroot,
                    'Connection error: '.$CFG->wwwroot);
               $fp = fopen($CFG->dataroot.'/emailcount', 'w');
               fwrite($fp, time());
            }
        }
        print_error('dbconnectionfailed', 'error', '', $dberr);
    }
    if (debugging('', DEBUG_ALL)) {
        ob_end_clean();
    } else {
        error_reporting($prevdebug);
    }

    return true;
}
