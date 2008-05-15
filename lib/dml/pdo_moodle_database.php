<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_database.php');
require_once($CFG->libdir.'/dml/pdo_moodle_recordset.php');

/**
 * Experimental pdo database class
 * @package dmlib
 */
abstract class pdo_moodle_database extends moodle_database {

    protected $pdb;
    protected $columns = array(); // I wish we had a shared memory cache for this :-(

    public function __construct($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix) {
        parent::__construct($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix);
    }

    public function connect() {
        try {
            $this->pdb = new PDO('mysql:host='.$this->dbhost.';dbname='.$this->dbname, $this->dbuser, $this->pass, array(PDO::ATTR_PERSISTENT => $this->dbpresist));
            $this->configure_dbconnection();
            return true;
        } catch (PDOException $ex) {
            return false;
        }
    }

    protected function configure_dbconnection() {
    }

    public function get_columns($table) {
        if (isset($this->columns[$table])) {
            return $this->columns[$table];
        }

        if (!$this->columns[$table] = array_change_key_case($this->db->MetaColumns($this->prefix.$table), CASE_LOWER)) {
            $this->columns[$table] = array();
        }

        return $this->columns[$table];
    }

    public function reset_columns($table=null) {
        if ($table) {
            unset($this->columns[$table]);
        } else {
            $this->columns[$table] = array();
        }
    }


    protected function report_error($sql, $params, $obj) {
        debugging($e->getMessage() .'<br /><br />'. s($sql));
    }

    public function set_debug($state) {
        //TODO
    }

    public function set_logging($state) {
        //TODO
    }

    public function execute($sql, array $params=null) {
        try {
            //$this->reset_columns(); // TODO: do we need to clean the cache here??
            list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
            $sth = $this->dbh->prepare($sql);
            return $sth->execute($params);
        } catch (PDOException $ex) {
            $this->report_error($sql, $params, $ex);
            return false;
        }
    }

    public function delete_records_select($table, $select, array $params=null) {
        try {
            if ($select) {
                $select = "WHERE $select";
            }
            $sql = "DELETE FROM {$this->prefix}$table $select";
            list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
            $sth = $this->dbh->prepare($sql);
            return $sth->execute($params);
        } catch (PDOException $ex) {
            $this->report_error($sql, $params, $ex);
            return false;
        }
    }

    public function get_recordset_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {
        try {
            list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
            $sth = $this->dbh->prepare($sql);
            error('TODO');
            return $this->create_recordset($sth);

        } catch (PDOException $ex) {
            $this->report_error($sql, $params, $ex);
            return false;
        }
    }

    protected function create_recordset($sth) {
        return new pdo_moodle_recordset($sth);
    }

    public function get_records_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {
        try {
            list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
            error('TODO');

        } catch (PDOException $ex) {
            $this->report_error($sql, $params, $ex);
            return false;
        }
    }

    public function get_fieldset_sql($sql, array $params=null) {
        try {
            list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
            error('TODO');

        } catch (PDOException $ex) {
            $this->report_error($sql, $params, $ex);
            return false;
        }
    }

    public function sql_substr() {
        error('TODO');
    }

    public function sql_concat() {
        error('TODO');
    }

    public function sql_concat_join($separator="' '", $elements=array()) {
        error('TODO');
    }

    public function begin_sql() {
        $this->pdb->beginTransaction();
        return true;
    }
    public function commit_sql() {
        $this->pdb->commit();
        return true;
    }
    public function rollback_sql() {
        $this->pdb->rollBack();
        return true;
    }

}
