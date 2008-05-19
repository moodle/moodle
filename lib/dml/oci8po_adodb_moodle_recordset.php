<?php

/**
 * Oracle moodle recordest with special hacks
 * @package dmlib
 */
class oci8po_adodb_moodle_recordset extends adodb_moodle_recordset {
    private $rs;

    public function __construct($rs) {
        $this->rs = $rs;
    }

    public function current() {
        /// Really DIRTY HACK for Oracle - needed because it can not see difference from NULL and ''
        /// this can not be removed even if we chane db defaults :-(
        $fields = $this->rs->fields;
        array_walk($fields, 'onespace2empty');
        return (object)$fields;
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
    }
}
