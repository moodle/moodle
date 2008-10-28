<?php  //$Id$

/**
 * Abstract class for resultsets returned from database functions.
 * This is a simple Iterator with needed recorset closing support.
 *
 * The difference from old recorset is that the records are returned
 * as objects, not arrays. You should use "foreach ($recordset as $record) {}"
 * followed by "$recordset->close()".
 *
 * Do not forget to close all recordsets when they are not needed anymore!
 */
abstract class moodle_recordset implements Iterator {

    /**
     * Returns current record - fields as object properties, lowercase
     * @return object
     */
    //public abstract function current();

    /**
     * Returns the key of current row
     * @return int current row
     */
    //public abstract function key();

    /**
     * Moves forward to next row
     * @return void
     */
    //public abstract function next();

    /**
     * Revinds are not supported!
     * @return void
     */
    public function rewind() {
        // no seeking, sorry - let's ignore it ;-)
        return;
    }

    /**
     * Did we reach the end?
     * @return boolean
     */
    //public abstract function valid();

    /**
     * Free resources and connections, recordset can not be used anymore.
     * @return void
     */
    public abstract function close();
}
