<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_recordset.php');

/**
 * Experimental pdo recordset
 * @package dml
 */
class pdo_moodle_recordset extends moodle_recordset {

    private $sth;
    protected $fields;
    protected $rowCount = -1;

    public function __construct($sth) {
        $this->sth = $sth;
        $this->sth->setFetchMode(PDO::FETCH_ASSOC);
    }

    public function current() {
        return (object)$this->fields;
    }

    public function key() {
    /// return first column value as key
        return reset($this->fields);
    }

    public function next() {
        $this->fields = $this->sth->fetch();
        if ($this->fields) {
            ++$this->rowCount;
        }
        return $this->fields !== false;
    }

    public function rewind() {
        $this->fields = $this->sth->fetch();
        if ($this->fields) {
            $this->rowCount = 0;
        }
    }

    public function valid() {
        if($this->rowCount < 0) {
            $this->rewind();
        }
        return $this->fields !== FALSE;
    }

    public function close() {
        $this->sth->closeCursor();
        $this->sth = null;
    }
}
