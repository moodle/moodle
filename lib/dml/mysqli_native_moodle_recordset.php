<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_recordset.php');

class mysqli_native_moodle_recordset extends moodle_recordset {

    protected $result;
    protected $current;
    protected $row;

    public function __construct($result) {
        $this->result  = $result;
        $this->current = $this->fetch_next();
        $this->row     = 0;
    }

    public function __destruct() {
        $this->close();
    }

    private function fetch_next() {
        if ($row = $this->result->fetch_assoc()) {
            $row = array_change_key_case($row, CASE_LOWER);
            $row = (object)$row;
        }
        return $row;
    }

    public function current() {
        return $this->current;
    }

    public function key() {
        return $this->row;
    }

    public function next() {
        if ($this->current = $this->fetch_next()) {
            $this->row++;
        }
        return $this->current;
    }

    public function rewind() {
        // we can not seek, sorry - let's ignore it ;-)
    }

    public function valid() {
        return !is_null($this->current);
    }

    public function close() {
        if ($this->result) {
            $this->result->close();
            $this->result  = null;
        }
        $this->current = null;
        $this->row     = 0;
    }
}
