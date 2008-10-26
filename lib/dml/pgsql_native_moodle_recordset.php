<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_recordset.php');

class pgsql_native_moodle_recordset extends moodle_recordset {

    protected $result;
    protected $current; // current row as array

    public function __construct($result) {
        $this->result  = $result;
        $this->current = $this->fetch_next();
    }

    public function __destruct() {
        $this->close();
    }

    private function fetch_next() {
        $row = pg_fetch_assoc($this->result);
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

    public function rewind() {
        // we can not seek, sorry - let's ignore it ;-)
    }

    public function valid() {
        return !empty($this->current);
    }

    public function close() {
        if ($this->result) {
            pg_free_result($this->result);
            $this->result  = null;
        }
        $this->current = null;
    }
}
