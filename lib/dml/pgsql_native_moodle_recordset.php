<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_recordset.php');

class pgsql_native_moodle_recordset extends moodle_recordset {

    protected $result;
    protected $current; // current row as array
    protected $bytea_oid;
    protected $blobs = array();

    public function __construct($result, $bytea_oid) {
        $this->result    = $result;
        $this->bytea_oid = $bytea_oid;

        // find out if there are any blobs
        $numrows = pg_num_fields($result);
        for($i=0; $i<$numrows; $i++) {
            $type_oid = pg_field_type_oid($result, $i);
            if ($type_oid == $this->bytea_oid) {
                $this->blobs[] = pg_field_name($result, $i);
            }
        }

        $this->current = $this->fetch_next();
    }

    public function __destruct() {
        $this->close();
    }

    private function fetch_next() {
        $row = pg_fetch_assoc($this->result);

        if ($this->blobs) {
            foreach ($this->blobs as $blob) {
                $row[$blob] = pg_unescape_bytea($row[$blob]);
            }
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
        if ($this->result) {
            pg_free_result($this->result);
            $this->result  = null;
        }
        $this->current = null;
        $this->blobs   = null;
    }
}
