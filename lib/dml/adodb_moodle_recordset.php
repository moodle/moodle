<?php

/**
 * Adodb basic moodle recordset class
 * @package dmlib
 */
class adodb_moodle_recordset implements moodle_recordset {
    private $rs;

    public function __construct($rs) {
        $this->rs = $rs;
    }

    public function current() {
        return (object)$this->rs->fields;
    }

    public function key() {
        return $this->rs->_currentRow;
    }

    public function next() {
        $this->rs->MoveNext();
    }

    public function rewind() {
        $this->rs->MoveFirst();
    }

    public function valid() {
        return !$this->rs->EOF;
    }

    public function close() {
        $this->rs->Close();
        $this->rs = null;
    }
}
