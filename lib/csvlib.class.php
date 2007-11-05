<?php
/*
 * General csv import library.
 * @author Petr Skoda
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

/**
 * Utitily class for importing of CSV files.
 */
class csv_import_reader {
    var $_iid;
    var $_type;
    var $_error;
    var $_columns; //cached columns
    var $_fp;

    /**
     * Contructor
     * @param int $iid import identifier
     * @param string $type which script imports?
     */
    function csv_import_reader($iid, $type) {
        $this->_iid  = $iid;
        $this->_type = $type;
    }

    /**
     * Parse this content
     * @param string &$content passed by ref for memory reasons, unset after return
     * @param string $encoding content encoding
     * @param string $delimiter_name separator (comma, semicolon, colon, cfg)
     * @param string $column_validation name of function for columns validation, must have one param $columns
     * @return false if error, count of data lines if ok; use get_error() to get error string
     */
    function load_csv_content(&$content, $encoding, $delimiter_name, $column_validation=null) {
        global $USER, $CFG;

        $this->close();
        $this->_error = null;

        $textlib = textlib_get_instance();

        $content = $textlib->convert($content, $encoding, 'utf-8');
        // remove Unicode BOM from first line
        $content = $textlib->trim_utf8_bom($content);
        // Fix mac/dos newlines
        $content = preg_replace('!\r\n?!', "\n", $content);
        // is there anyting in file?
        $columns = strtok($content, "\n");
        if ($columns === false) {
            $this->_error = get_string('csvemptyfile', 'error');
            return false;
        }
        $csv_delimiter = csv_import_reader::get_delimiter($delimiter_name);
        $csv_encode    = csv_import_reader::get_encoded_delimiter($delimiter_name);

        // process header - list of columns
        $columns   = explode($csv_delimiter, $columns);
        $col_count = count($columns);
        if ($col_count === 0) {
            $this->_error = get_string('csvemptyfile', 'error');
            return false;
        }

        foreach ($columns as $key=>$value) {
            $columns[$key] = str_replace($csv_encode, $csv_delimiter, trim($value));
        }
        if ($column_validation) {
            $result = $column_validation($columns);
            if ($result !== true) {
                $this->_error = $result;
                return false;
            }
        }
        $this->_columns = $columns; // cached columns

        // open file for writing
        $filename = $CFG->dataroot.'/temp/csvimport/'.$this->_type.'/'.$USER->id.'/'.$this->_iid;
        $fp = fopen($filename, "w");
        fwrite($fp, serialize($columns)."\n");

        // again - do we have any data for processing?
        $line = strtok("\n");
        $data_count = 0;
        while ($line !== false) {
            $line = explode($csv_delimiter, $line);
            foreach ($line as $key=>$value) {
                $line[$key] = str_replace($csv_encode, $csv_delimiter, trim($value));
            }
            if (count($line) !== $col_count) {
                // this is critical!!
                $this->_error = get_string('csvweirdcolumns', 'error');
                fclose($fp);
                $this->cleanup();
                return false;
            }
            fwrite($fp, serialize($line)."\n");
            $data_count++;
            $line = strtok("\n");
        }

        fclose($fp);
        return $data_count;
    }

    /**
     * Returns list of columns
     * @return array
     */
    function get_columns() {
        if (isset($this->_columns)) {
            return $this->_columns;
        }

        global $USER, $CFG;

        $filename = $CFG->dataroot.'/temp/csvimport/'.$this->_type.'/'.$USER->id.'/'.$this->_iid;
        if (!file_exists($filename)) {
            return false;
        }
        $fp = fopen($filename, "r");
        $line = fgets($fp);
        fclose($fp);
        if ($line === false) {
            return false;
        }
        $this->_columns = unserialize($line);
        return $this->_columns;
    }

    /**
     * Init iterator.
     */
    function init() {
        global $CFG, $USER;

        if (!empty($this->_fp)) {
            $this->close();
        }
        $filename = $CFG->dataroot.'/temp/csvimport/'.$this->_type.'/'.$USER->id.'/'.$this->_iid;
        if (!file_exists($filename)) {
            return false;
        }
        if (!$this->_fp = fopen($filename, "r")) {
            return false;
        }
        //skip header
        return (fgets($this->_fp) !== false);
    }

    /**
     * Get next line
     * @return array of values
     */
    function next() {
        if (empty($this->_fp) or feof($this->_fp)) {
            return false;
        }
        if ($ser = fgets($this->_fp)) {
            return unserialize($ser);
        } else {
            return false;
        }
    }

    /**
     * Release iteration related resources
     */
    function close() {
        if (!empty($this->_fp)) {
            fclose($this->_fp);
            $this->_fp = null;
        }
    }

    /**
     * Get last error
     * @return string error text of null if none
     */
    function get_error() {
        return $this->_error;
    }

    /**
     * Cleanup temporary data
     * @static if $full=true
     * @param boolean true menasdo a full cleanup - all sessions for current user, false only the active iid
     */
    function cleanup($full=false) {
        global $USER, $CFG;

        if ($full) {
            @remove_dir($CFG->dataroot.'/temp/csvimport/'.$this->_type.'/'.$USER->id);
        } else {
            @unlink($CFG->dataroot.'/temp/csvimport/'.$this->_type.'/'.$USER->id.'/'.$this->_iid);
        }
    }

    /**
     * Get list of cvs delimiters
     * @static
     * @return array suitable for selection box
     */
    function get_delimiter_list() {
        $delimiters = array('comma'=>',', 'semicolon'=>';', 'colon'=>':', 'tab'=>'\\t');
        if (isset($CFG->CSV_DELIMITER) and strlen($CFG->CSV_DELIMITER) === 1 and !in_array($CFG->CSV_DELIMITER, $delimiters)) {
            $delimiters['cfg'] = $CFG->CSV_DELIMITER;
        }
        return $delimiters;
    }

    /**
     * Get delimiter character
     * @static
     * @param string separator name
     * @return char delimiter char
     */
    function get_delimiter($delimiter_name) {
        switch ($delimiter_name) {
            case 'colon':     return ':';
            case 'semicolon': return ';';
            case 'tab':       return "\t";
            case 'cfg':       if (isset($CFG->CSV_DELIMITER)) { return $CFG->CSV_DELIMITER; } // no break; fall back to comma
            case 'comma':     return ',';
        }
    }

    /**
     * Get encoded delimiter character
     * @static
     * @param string separator name
     * @return char encoded delimiter char
     */
    function get_encoded_delimiter($delimiter_name) {
        global $CFG;
        if ($delimiter_name == 'cfg' and isset($CFG->CSV_ENCODE)) {
            return $CFG->CSV_ENCODE;
        }
        $delimiter = csv_import_reader::get_delimiter($delimiter_name);
        return '&#'.ord($delimiter);
    }

    /**
     * Create new import id
     * @static
     * @param string who imports?
     * @return int iid
     */
    function get_new_iid($type) {
        global $USER;

        if (!$filename = make_upload_directory('temp/csvimport/'.$type.'/'.$USER->id, false)) {
            error('Can not create temporary upload directory - verify moodledata permissions!');
        }

        // use current (non-conflicting) time stamp
        $iiid = time();
        while (file_exists($filename.'/'.$iiid)) {
            $iiid--;
        }

        return $iiid;
    }
}
?>
