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
 * DML exception class, use instead of error() in dml code.
 */
class dml_exception extends moodle_exception {
    function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}

/**
 * DML db connection exception - triggered if database not accessible.
 */
class dml_connection_exception extends dml_exception {
    function __construct($error) {
        $errorinfo = '<em>'.s($error).'</em>';
        parent::__construct('dbconnectionfailed', NULL, $errorinfo);
    }
}

/**
 * DML read exception - triggered by SQL syntax errors, missing tables, etc.
 */
class dml_read_exception extends dml_exception {
    public $error;
    public $sql;
    public $params;

    function __construct($error, $sql=null, array $params=null) {
        $this->error  = $error;
        $this->sql    = $sql;
        $this->params = $params;
        $errorinfo = s($error).'<br /><br />'.s($sql).'<br />['.s(var_export($params, true)).']';
        parent::__construct('dmlreadexception', NULL, $errorinfo);
    }
}

/**
 * DML read exception - triggered by SQL syntax errors, missing tables, etc.
 */
class dml_write_exception extends dml_exception {
    public $error;
    public $sql;
    public $params;

    function __construct($error, $sql=null, array $params=null) {
        $this->error  = $error;
        $this->sql    = $sql;
        $this->params = $params;
        $errorinfo = s($error).'<br /><br />'.s($sql).'<br />['.s(var_export($params, true)).']';
        parent::__construct('dmlwriteexception', NULL, $errorinfo);
    }
}

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

    if (!isset($CFG->dblibrary)) {
        switch ($CFG->dbtype) {
            case 'postgres7' :
                $CFG->dbtype = 'pgsql';
                // continue, no break here
            case 'pgsql' :
                $CFG->dblibrary = 'native';
                break;

            case 'mysql' :
                $CFG->dbtype = 'mysqli';
                // continue, no break here
            case 'mysqli' :
                $CFG->dblibrary = 'native';
                break;

            default:
                // the rest of drivers is not converted yet - keep adodb for now
                $CFG->dblibrary = 'adodb';
        }
    }

    if (!isset($CFG->dboptions)) {
        $CFG->dboptions = array();
    }

    if (isset($CFG->dbpersist)) {
        $CFG->dboptions['dbpersist'] = $CFG->dbpersist;
    }

    if (!$DB = moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary)) {
        throw new dml_exception('dbdriverproblem', "Unknown driver $CFG->dblibrary/$CFG->dbtype");
    }

    try {
        $DB->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->prefix, $CFG->dboptions);
    } catch (moodle_exception $e) {
        if (empty($CFG->noemailever) and !empty($CFG->emailconnectionerrorsto)) {
            if (file_exists($CFG->dataroot.'/emailcount')){
                $fp = @fopen($CFG->dataroot.'/emailcount', 'r');
                $content = @fread($fp, 24);
                @fclose($fp);
                if((time() - (int)$content) > 600){
                    @mail($CFG->emailconnectionerrorsto,
                        'WARNING: Database connection error: '.$CFG->wwwroot,
                        'Connection error: '.$CFG->wwwroot);
                    $fp = @fopen($CFG->dataroot.'/emailcount', 'w');
                    @fwrite($fp, time());
                }
            } else {
               @mail($CFG->emailconnectionerrorsto,
                    'WARNING: Database connection error: '.$CFG->wwwroot,
                    'Connection error: '.$CFG->wwwroot);
               $fp = @fopen($CFG->dataroot.'/emailcount', 'w');
               @fwrite($fp, time());
            }
        }
        // rethrow the exception
        throw $e;
    }

    $CFG->dbfamily = $DB->get_dbfamily(); // TODO: BC only for now

    return true;
}
