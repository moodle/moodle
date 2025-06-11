<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

use core\exception\response_aware_exception;
use core\router\response\not_found_response;

/**
 * This library contains all the Data Manipulation Language (DML) functions
 * used to interact with the DB
 *
 * This library contains all the Data Manipulation Language (DML) functions
 * used to interact with the DB. All the dunctions in this library must be
 * generic and work against the major number of RDBMS possible. This is the
 * list of currently supported and tested DBs: mysql, postresql, mssql, oracle
 *
 * This library is automatically included by Moodle core so you never need to
 * include it yourself.
 *
 * For more info about the functions available in this library, please visit:
 *     http://docs.moodle.org/en/DML_functions
 * (feel free to modify, improve and document such page, thanks!)
 *
 * @package    core
 * @category   dml
 * @subpackage dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Require the essential
require_once($CFG->libdir.'/dml/moodle_database.php');

/** Return false if record not found, show debug warning if multiple records found */
define('IGNORE_MISSING', 0);
/** Similar to IGNORE_MISSING but does not show debug warning if multiple records found, not recommended to be used */
define('IGNORE_MULTIPLE', 1);
/** Indicates exactly one record must exist */
define('MUST_EXIST', 2);

/**
 * DML exception class, use instead of throw new \moodle_exception() in dml code.
 *
 * @package    core
 * @category   dml
 * @subpackage dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dml_exception extends moodle_exception {
    /**
     * @param string $errorcode The name of the string from error.php to print.
     * @param mixed  $a Extra words and phrases that might be required in the error string.
     * @param string $debuginfo Optional debugging information.
     */
    function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}

/**
 * DML db connection exception - triggered if database not accessible.
 *
 * @package    core
 * @category   dml
 * @subpackage dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dml_connection_exception extends dml_exception {
    /**
     * Constructor
     * @param string $error Optional debugging information.
     */
    function __construct($error) {
        $errorinfo = $error;
        parent::__construct('dbconnectionfailed', NULL, $errorinfo);
    }
}

/**
 * DML db session wait exception - triggered when session lock request times out.
 *
 * @package    core
 * @category   dml
 * @subpackage dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dml_sessionwait_exception extends dml_exception {
    /**
     * Constructor
     */
    function __construct() {
        parent::__construct('sessionwaiterr');
    }
}

/**
 * DML read exception - triggered by some SQL syntax errors, etc.
 *
 * @package    core
 * @category   dml
 * @subpackage dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dml_read_exception extends dml_exception {
    /** @var string The name of the string from error.php to print.*/
    public $error;
    /** @var string The SQL that ran just before this read error.*/
    public $sql;
    /** @var array The SQL's related parameters.*/
    public $params;

    /**
     * Constructor
     * @param string $error The name of the string from error.php to print.
     * @param string $sql The SQL that ran just before this read error.
     * @param array $params The SQL's related parameters.(optional)
     */
    function __construct($error, $sql=null, ?array $params=null) {
        $this->error  = $error;
        $this->sql    = $sql;
        $this->params = $params;
        $errorinfo = $error."\n".$sql."\n[".var_export($params, true).']';
        parent::__construct('dmlreadexception', NULL, $errorinfo);
    }
}

/**
 * Caused by multiple records found in get_record() call.
 *
 * @package    core
 * @category   dml
 * @subpackage dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dml_multiple_records_exception extends dml_exception {
    /** @var string The SQL that ran just before this read error.*/
    public $sql;
    /** @var array The SQL's related parameters.*/
    public $params;

    /**
     * Constructor
     * @param string $sql The SQL that ran just before this read error.
     * @param array $params The SQL's related parameters.(optional)
     */
    function __construct($sql='', ?array $params=null) {
        $errorinfo = $sql."\n[".var_export($params, true).']';
        parent::__construct('multiplerecordsfound', null, $errorinfo);
    }
}

/**
 * Caused by missing record that is required for normal operation.
 *
 * @package    core
 * @category   dml
 * @subpackage dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dml_missing_record_exception extends dml_exception implements response_aware_exception {
    /** @var string A table's name.*/
    public $tablename;
    /** @var string An SQL query.*/
    public $sql;
    /** @var array The SQL's parameters.*/
    public $params;

    /**
     * Constructor
     * @param string $tablename The table name if known, '' if unknown.
     * @param string $sql Optional SQL query.
     * @param array $params Optional SQL query's parameters.
     */
    function __construct($tablename, $sql='', ?array $params=null) {
        if (empty($tablename)) {
            $tablename = null;
        }
        $this->tablename = $tablename;
        $this->sql       = $sql;
        $this->params    = $params;

        switch ($tablename) {
            case null:
                $errcode = 'invalidrecordunknown';
                break;
            case 'course':
                $errcode = empty($sql) ? 'invalidcourseid' : 'invalidrecord';
                break;
            case 'course_modules':
                $errcode = 'invalidcoursemodule';
                break;
            case 'user':
                $errcode = 'invaliduser';
                break;
            default:
                $errcode = 'invalidrecord';
                break;
        }
        $errorinfo = $sql."\n[".var_export($params, true).']';
        parent::__construct($errcode, $tablename, $errorinfo);
    }

    #[\Override]
    public function get_response_classname(): string {
        return not_found_response::class;
    }
}

/**
 * DML write exception - triggered by some SQL syntax errors, etc.
 *
 * @package    core
 * @category   dml
 * @subpackage dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dml_write_exception extends dml_exception {
    /** @var string The name of the string from error.php to print.*/
    public $error;
    /** @var string The SQL that ran just before this write error.*/
    public $sql;
    /** @var array The SQL's related parameters.*/
    public $params;

    /**
     * Constructor
     * @param string $error The name of the string from error.php to print.
     * @param string $sql The SQL that ran just before this write error.
     * @param array $params The SQL's related parameters.(optional)
     */
    function __construct($error, $sql=null, ?array $params=null) {
        $this->error  = $error;
        $this->sql    = $sql;
        $this->params = $params;
        $errorinfo = $error."\n".$sql."\n[".var_export($params, true).']';
        parent::__construct('dmlwriteexception', NULL, $errorinfo);
    }
}

/**
 * DML transaction exception - triggered by problems related to DB transactions.
 *
 * @todo MDL-20625 Use the info from $transaction for debugging purposes.
 *
 * @package    core
 * @category   dml
 * @subpackage dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dml_transaction_exception extends dml_exception {
    /** @var moodle_transaction An instance of a transaction.*/
    public $transaction;

    /**
     * Constructor
     * @param ?string $debuginfo Optional debugging information.
     * @param ?moodle_transaction $transaction The instance of the transaction.(Optional)
     */
    function __construct($debuginfo=null, $transaction=null) {
        $this->transaction = $transaction; // TODO: MDL-20625 use the info from $transaction for debugging purposes
        parent::__construct('dmltransactionexception', NULL, $debuginfo);
    }
}

/**
 * Sets up global $DB moodle_database instance
 *
 * @global stdClass $CFG The global configuration instance.
 * @see config.php
 * @see config-dist.php
 * @global stdClass $DB The global moodle_database instance.
 * @return void|bool Returns true when finished setting up $DB. Returns void when $DB has already been set.
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
        $CFG->dblibrary = 'native';
        // use new drivers instead of the old adodb driver names
        switch ($CFG->dbtype) {
            case 'postgres7' :
                $CFG->dbtype = 'pgsql';
                break;

            case 'oci8po':
                $CFG->dbtype = 'oci';
                break;

            case 'mysql' :
                $CFG->dbtype = 'mysqli';
                break;
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
            $body = "Connection error: ".$CFG->wwwroot.
                "\n\nInfo:".
                "\n\tError code: ".$e->errorcode.
                "\n\tDebug info: ".$e->debuginfo.
                "\n\tServer: ".$_SERVER['SERVER_NAME']." (".$_SERVER['SERVER_ADDR'].")";
            if (file_exists($CFG->dataroot.'/emailcount')){
                $fp = @fopen($CFG->dataroot.'/emailcount', 'r');
                $content = @fread($fp, 24);
                @fclose($fp);
                if((time() - (int)$content) > 600){
                    //email directly rather than using messaging
                    @mail($CFG->emailconnectionerrorsto,
                        'WARNING: Database connection error: '.$CFG->wwwroot,
                        $body);
                    $fp = @fopen($CFG->dataroot.'/emailcount', 'w');
                    @fwrite($fp, time());
                }
            } else {
               //email directly rather than using messaging
               @mail($CFG->emailconnectionerrorsto,
                    'WARNING: Database connection error: '.$CFG->wwwroot,
                    $body);
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
