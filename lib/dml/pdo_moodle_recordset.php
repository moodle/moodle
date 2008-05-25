<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_recordset.php');

/**
 * Experimental pdo recordset
 * @package dmlib
 */
class pdo_moodle_recordset extends moodle_recordset {
    private $sht;

    public function __construct($sth) {
        $this->sth = $sth;
    }

    public function current() {
        error('TODO');
    }

    public function key() {
        error('TODO');
    }

    public function next() {
        error('TODO');
    }

    public function rewind() {
        error('TODO');
    }

    public function valid() {
        error('TODO');
    }

    public function close() {
        $this->sth->closeCursor();
        $this->sth = null;
    }
}
