<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_recordset.php');

class oci_native_moodle_recordset extends moodle_recordset {

    protected $stmt;
    protected $current;

    public function __construct($stmt) {
        $this->stmt  = $stmt;
        $this->current = $this->fetch_next();
    }

    public function __destruct() {
        $this->close();
    }

    private function fetch_next() {
        if ($row = oci_fetch_assoc($this->stmt)) {
            $row = array_change_key_case($row, CASE_LOWER);
            unset($row['oracle_rownum']);
            array_walk($row, array('oci_native_moodle_database', 'onespace2empty'));
        }
        return $row;
    }

    public function current() {
        return (object)$this->current;
    }

    public function key() {
    /// return first column value as key
        if (!$this->current) {
            return false;
        }
        $key = reset($this->current);
        return $key;
    }

    public function next() {
        $this->current = $this->fetch_next();
    }

    public function valid() {
        return !empty($this->current);
    }

    public function close() {
        if ($this->stmt) {
            oci_free_statement($this->stmt);
            $this->stmt  = null;
        }
        $this->current = null;
    }
}
