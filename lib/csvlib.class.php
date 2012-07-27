<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This is a one-line short description of the file
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    core
 * @subpackage lib
 * @copyright  Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Utitily class for importing of CSV files.
 * @copyright Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   moodlecore
 */
class csv_import_reader {
    /**
     * @var int import identifier
     */
    var $_iid;
    /**
     * @var string which script imports?
     */
    var $_type;
    /**
     * @var string|null Null if ok, error msg otherwise
     */
    var $_error;
    /**
     * @var array cached columns
     */
    var $_columns;
    /**
     * @var object file handle used during import
     */
    var $_fp;
    /**
     * @var string delimiter of the records.
     */
    var $_delimiter;
    /**
     * @var string enclosure of each field.
     */
    var $_enclosure;
    /**
     * Contructor
     *
     * @param int $iid import identifier
     * @param string $type which script imports?
     */
    function csv_import_reader($iid, $type) {
        $this->_iid  = $iid;
        $this->_type = $type;
    }

    /**
     * Parse this content
     *
     * @global object
     * @global object
     * @param string $content passed by ref for memory reasons, unset after return
     * @param string $encoding content encoding
     * @param string $delimiter_name separator (comma, semicolon, colon, cfg)
     * @param string $column_validation name of function for columns validation, must have one param $columns
     * @return bool false if error, count of data lines if ok; use get_error() to get error string
     */
    function load_csv_content(&$content, $encoding, $delimiter_name, $column_validation=null, $enclosure='"') {
        global $USER, $CFG;

        $this->close();
        $this->_error = null;

        $content = textlib::convert($content, $encoding, 'utf-8');
        // remove Unicode BOM from first line
        $content = textlib::trim_utf8_bom($content);
        // Fix mac/dos newlines
        $content = preg_replace('!\r\n?!', "\n", $content);

        $csv_delimiter = csv_import_reader::get_delimiter($delimiter_name);
//        $csv_encode    = csv_import_reader::get_encoded_delimiter($delimiter_name);
        $this->_delimiter = $csv_delimiter;
        $this->_enclosure = $enclosure;

        // create a temporary file and store the csv file there in csv file format.
        $filename = $CFG->tempdir.'/csvimport/'.$this->_type.'/'.$USER->id.'/'.$this->_iid;
        $fp = fopen($filename, 'w+');
        fwrite($fp, $content);
        fseek($fp, 0);
        // Create an array to store the imported data for error checking.
        $columns = array();
        while ($fgetdata = fgetcsv($fp, 0, $csv_delimiter, $enclosure)) {
            $columns[] = $fgetdata;
        }

        $col_count = 0;
        // process header - list of columns
        if (!isset($columns[0])) {
            $this->_error = get_string('csvemptyfile', 'error');
            fclose($fp);
            $this->cleanup();
            return false;
        } else {
            $col_count = count($columns[0]);
        }
        // column validation
        if ($column_validation) {
            $result = $column_validation($columns[0]);
            if ($result !== true) {
                $this->_error = $result;
                fclose($fp);
                $this->cleanup();
                return false;
            }
        }

        $this->_columns = $columns[0]; // cached columns
        // check to make sure that the data columns match up with the headers.
        foreach ($columns as $rowdata) {
            if (count($rowdata) !== $col_count) {
                $this->_error = get_string('csvweirdcolumns', 'error');
                fclose($fp);
                $this->cleanup();
                return false;
            }
        }
        $datacount = count($columns);
        return $datacount;
    }

    /**
     * Returns list of columns
     *
     * @return array
     */
    function get_columns() {
        if (isset($this->_columns)) {
            return $this->_columns;
        }

        global $USER, $CFG;

        $filename = $CFG->tempdir.'/csvimport/'.$this->_type.'/'.$USER->id.'/'.$this->_iid;
        if (!file_exists($filename)) {
            return false;
        }
        $fp = fopen($filename, "r");
        $line = fgetcsv($fp, 0, $this->_delimiter, $this->_enclosure);
        fclose($fp);
        if ($line === false) {
            return false;
        }
        $this->_columns = $line;
        return $this->_columns;
    }

    /**
     * Init iterator.
     *
     * @global object
     * @global object
     * @return bool Success
     */
    function init() {
        global $CFG, $USER;

        if (!empty($this->_fp)) {
            $this->close();
        }
        $filename = $CFG->tempdir.'/csvimport/'.$this->_type.'/'.$USER->id.'/'.$this->_iid;
        if (!file_exists($filename)) {
            return false;
        }
        if (!$this->_fp = fopen($filename, "r")) {
            return false;
        }
        //skip header
        return (fgetcsv($this->_fp, 0, $this->_delimiter, $this->_enclosure) !== false);
    }

    /**
     * Get next line
     *
     * @return mixed false, or an array of values
     */
    function next() {
        if (empty($this->_fp) or feof($this->_fp)) {
            return false;
        }
        if ($ser = fgetcsv($this->_fp, 0, $this->_delimiter, $this->_enclosure)) {
            return $ser;
        } else {
            return false;
        }
    }

    /**
     * Release iteration related resources
     *
     * @return void
     */
    function close() {
        if (!empty($this->_fp)) {
            fclose($this->_fp);
            $this->_fp = null;
        }
    }

    /**
     * Get last error
     *
     * @return string error text of null if none
     */
    function get_error() {
        return $this->_error;
    }

    /**
     * Cleanup temporary data
     *
     * @global object
     * @global object
     * @param boolean $full true means do a full cleanup - all sessions for current user, false only the active iid
     */
    function cleanup($full=false) {
        global $USER, $CFG;

        if ($full) {
            @remove_dir($CFG->tempdir.'/csvimport/'.$this->_type.'/'.$USER->id);
        } else {
            @unlink($CFG->tempdir.'/csvimport/'.$this->_type.'/'.$USER->id.'/'.$this->_iid);
        }
    }

    /**
     * Get list of cvs delimiters
     *
     * @return array suitable for selection box
     */
    static function get_delimiter_list() {
        global $CFG;
        $delimiters = array('comma'=>',', 'semicolon'=>';', 'colon'=>':', 'tab'=>'\\t');
        if (isset($CFG->CSV_DELIMITER) and strlen($CFG->CSV_DELIMITER) === 1 and !in_array($CFG->CSV_DELIMITER, $delimiters)) {
            $delimiters['cfg'] = $CFG->CSV_DELIMITER;
        }
        return $delimiters;
    }

    /**
     * Get delimiter character
     *
     * @param string separator name
     * @return string delimiter char
     */
    static function get_delimiter($delimiter_name) {
        global $CFG;
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
     *
     * @global object
     * @param string separator name
     * @return string encoded delimiter char
     */
    static function get_encoded_delimiter($delimiter_name) {
        global $CFG;
        if ($delimiter_name == 'cfg' and isset($CFG->CSV_ENCODE)) {
            return $CFG->CSV_ENCODE;
        }
        $delimiter = csv_import_reader::get_delimiter($delimiter_name);
        return '&#'.ord($delimiter);
    }

    /**
     * Create new import id
     *
     * @global object
     * @param string who imports?
     * @return int iid
     */
    static function get_new_iid($type) {
        global $USER;

        $filename = make_temp_directory('csvimport/'.$type.'/'.$USER->id);

        // use current (non-conflicting) time stamp
        $iiid = time();
        while (file_exists($filename.'/'.$iiid)) {
            $iiid--;
        }

        return $iiid;
    }
}
