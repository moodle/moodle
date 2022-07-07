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
    private $_iid;

    /**
     * @var string which script imports?
     */
    private $_type;

    /**
     * @var string|null Null if ok, error msg otherwise
     */
    private $_error;

    /**
     * @var array cached columns
     */
    private $_columns;

    /**
     * @var object file handle used during import
     */
    private $_fp;

    /**
     * Contructor
     *
     * @param int $iid import identifier
     * @param string $type which script imports?
     */
    public function __construct($iid, $type) {
        $this->_iid  = $iid;
        $this->_type = $type;
    }

    /**
     * Make sure the file is closed when this object is discarded.
     */
    public function __destruct() {
        $this->close();
    }

    /**
     * Parse this content
     *
     * @param string $content the content to parse.
     * @param string $encoding content encoding
     * @param string $delimiter_name separator (comma, semicolon, colon, cfg)
     * @param string $column_validation name of function for columns validation, must have one param $columns
     * @param string $enclosure field wrapper. One character only.
     * @return bool false if error, count of data lines if ok; use get_error() to get error string
     */
    public function load_csv_content($content, $encoding, $delimiter_name, $column_validation=null, $enclosure='"') {
        global $USER, $CFG;

        $this->close();
        $this->_error = null;

        $content = core_text::convert($content, $encoding, 'utf-8');
        // remove Unicode BOM from first line
        $content = core_text::trim_utf8_bom($content);
        // Fix mac/dos newlines
        $content = preg_replace('!\r\n?!', "\n", $content);
        // Remove any spaces or new lines at the end of the file.
        if ($delimiter_name == 'tab') {
            // trim() by default removes tabs from the end of content which is undesirable in a tab separated file.
            $content = trim($content, chr(0x20) . chr(0x0A) . chr(0x0D) . chr(0x00) . chr(0x0B));
        } else {
            $content = trim($content);
        }

        $csv_delimiter = csv_import_reader::get_delimiter($delimiter_name);
        // $csv_encode    = csv_import_reader::get_encoded_delimiter($delimiter_name);

        // Create a temporary file and store the csv file there,
        // do not try using fgetcsv() because there is nothing
        // to split rows properly - fgetcsv() itself can not do it.
        $tempfile = tempnam(make_temp_directory('/csvimport'), 'tmp');
        if (!$fp = fopen($tempfile, 'w+b')) {
            $this->_error = get_string('cannotsavedata', 'error');
            @unlink($tempfile);
            return false;
        }
        fwrite($fp, $content);
        fseek($fp, 0);
        // Create an array to store the imported data for error checking.
        $columns = array();
        // str_getcsv doesn't iterate through the csv data properly. It has
        // problems with line returns.
        while ($fgetdata = fgetcsv($fp, 0, $csv_delimiter, $enclosure)) {
            // Check to see if we have an empty line.
            if (count($fgetdata) == 1) {
                if ($fgetdata[0] !== null) {
                    // The element has data. Add it to the array.
                    $columns[] = $fgetdata;
                }
            } else {
                $columns[] = $fgetdata;
            }
        }
        $col_count = 0;

        // process header - list of columns
        if (!isset($columns[0])) {
            $this->_error = get_string('csvemptyfile', 'error');
            fclose($fp);
            unlink($tempfile);
            return false;
        } else {
            $col_count = count($columns[0]);
        }

        // Column validation.
        if ($column_validation) {
            $result = $column_validation($columns[0]);
            if ($result !== true) {
                $this->_error = $result;
                fclose($fp);
                unlink($tempfile);
                return false;
            }
        }

        $this->_columns = $columns[0]; // cached columns
        // check to make sure that the data columns match up with the headers.
        foreach ($columns as $rowdata) {
            if (count($rowdata) !== $col_count) {
                $this->_error = get_string('csvweirdcolumns', 'error');
                fclose($fp);
                unlink($tempfile);
                $this->cleanup();
                return false;
            }
        }

        $filename = $CFG->tempdir.'/csvimport/'.$this->_type.'/'.$USER->id.'/'.$this->_iid;
        $filepointer = fopen($filename, "w");
        // The information has been stored in csv format, as serialized data has issues
        // with special characters and line returns.
        $storedata = csv_export_writer::print_array($columns, ',', '"', true);
        fwrite($filepointer, $storedata);

        fclose($fp);
        unlink($tempfile);
        fclose($filepointer);

        $datacount = count($columns);
        return $datacount;
    }

    /**
     * Returns list of columns
     *
     * @return array
     */
    public function get_columns() {
        if (isset($this->_columns)) {
            return $this->_columns;
        }

        global $USER, $CFG;

        $filename = $CFG->tempdir.'/csvimport/'.$this->_type.'/'.$USER->id.'/'.$this->_iid;
        if (!file_exists($filename)) {
            return false;
        }
        $fp = fopen($filename, "r");
        $line = fgetcsv($fp);
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
    public function init() {
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
        return (fgetcsv($this->_fp) !== false);
    }

    /**
     * Get next line
     *
     * @return mixed false, or an array of values
     */
    public function next() {
        if (empty($this->_fp) or feof($this->_fp)) {
            return false;
        }
        if ($ser = fgetcsv($this->_fp)) {
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
    public function close() {
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
    public function get_error() {
        return $this->_error;
    }

    /**
     * Cleanup temporary data
     *
     * @global object
     * @global object
     * @param boolean $full true means do a full cleanup - all sessions for current user, false only the active iid
     */
    public function cleanup($full=false) {
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
    public static function get_delimiter_list() {
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
    public static function get_delimiter($delimiter_name) {
        global $CFG;
        switch ($delimiter_name) {
            case 'colon':     return ':';
            case 'semicolon': return ';';
            case 'tab':       return "\t";
            case 'cfg':       if (isset($CFG->CSV_DELIMITER)) { return $CFG->CSV_DELIMITER; } // no break; fall back to comma
            case 'comma':     return ',';
            default :         return ',';  // If anything else comes in, default to comma.
        }
    }

    /**
     * Get encoded delimiter character
     *
     * @global object
     * @param string separator name
     * @return string encoded delimiter char
     */
    public static function get_encoded_delimiter($delimiter_name) {
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
    public static function get_new_iid($type) {
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


/**
 * Utitily class for exporting of CSV files.
 * @copyright 2012 Adrian Greeve
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   core
 * @category  csv
 */
class csv_export_writer {
    /**
     * @var string $delimiter  The name of the delimiter. Supported types(comma, tab, semicolon, colon, cfg)
     */
    var $delimiter;
    /**
     * @var string $csvenclosure  How fields with spaces and commas are enclosed.
     */
    var $csvenclosure;
    /**
     * @var string $mimetype  Mimetype of the file we are exporting.
     */
    var $mimetype;
    /**
     * @var string $filename  The filename for the csv file to be downloaded.
     */
    var $filename;
    /**
     * @var string $path  The directory path for storing the temporary csv file.
     */
    var $path;
    /**
     * @var resource $fp  File pointer for the csv file.
     */
    protected $fp;

    /**
     * Constructor for the csv export reader
     *
     * @param string $delimiter      The name of the character used to seperate fields. Supported types(comma, tab, semicolon, colon, cfg)
     * @param string $enclosure      The character used for determining the enclosures.
     * @param string $mimetype       Mime type of the file that we are exporting.
     */
    public function __construct($delimiter = 'comma', $enclosure = '"', $mimetype = 'application/download') {
        $this->delimiter = $delimiter;
        // Check that the enclosure is a single character.
        if (strlen($enclosure) == 1) {
            $this->csvenclosure = $enclosure;
        } else {
            $this->csvenclosure = '"';
        }
        $this->filename = "Moodle-data-export.csv";
        $this->mimetype = $mimetype;
    }

    /**
     * Set the file path to the temporary file.
     */
    protected function set_temp_file_path() {
        global $USER, $CFG;
        make_temp_directory('csvimport/' . $USER->id);
        $path = $CFG->tempdir . '/csvimport/' . $USER->id. '/' . $this->filename;
        // Check to see if the file exists, if so delete it.
        if (file_exists($path)) {
            unlink($path);
        }
        $this->path = $path;
    }

    /**
     * Add data to the temporary file in csv format
     *
     * @param array $row  An array of values.
     */
    public function add_data($row) {
        if(!isset($this->path)) {
            $this->set_temp_file_path();
            $this->fp = fopen($this->path, 'w+');
        }
        $delimiter = csv_import_reader::get_delimiter($this->delimiter);
        fputcsv($this->fp, $row, $delimiter, $this->csvenclosure);
    }

    /**
     * Echos or returns a csv data line by line for displaying.
     *
     * @param bool $return  Set to true to return a string with the csv data.
     * @return string       csv data.
     */
    public function print_csv_data($return = false) {
        fseek($this->fp, 0);
        $returnstring = '';
        while (($content = fgets($this->fp)) !== false) {
            if (!$return){
                echo $content;
            } else {
                $returnstring .= $content;
            }
        }
        if ($return) {
            return $returnstring;
        }
    }

    /**
     * Set the filename for the uploaded csv file
     *
     * @param string $dataname    The name of the module.
     * @param string $extenstion  File extension for the file.
     */
    public function set_filename($dataname, $extension = '.csv') {
        $filename = clean_filename($dataname);
        $filename .= clean_filename('-' . gmdate("Ymd_Hi"));
        $filename .= clean_filename("-{$this->delimiter}_separated");
        $filename .= $extension;
        $this->filename = $filename;
    }

    /**
     * Output file headers to initialise the download of the file.
     */
    protected function send_header() {
        global $CFG;

        if (defined('BEHAT_SITE_RUNNING')) {
            // For text based formats - we cannot test the output with behat if we force a file download.
            return;
        }
        if (is_https()) { // HTTPS sites - watch out for IE! KB812935 and KB316431.
            header('Cache-Control: max-age=10');
            header('Pragma: ');
        } else { //normal http - prevent caching at all cost
            header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
            header('Pragma: no-cache');
        }
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        header("Content-Type: $this->mimetype\n");
        header("Content-Disposition: attachment; filename=\"$this->filename\"");
    }

    /**
     * Download the csv file.
     */
    public function download_file() {
        // If this file was requested from a form, then mark download as complete.
        \core_form\util::form_download_complete();

        $this->send_header();
        $this->print_csv_data();
        exit;
    }

    /**
     * Creates a file for downloading an array into a deliminated format.
     * This function is useful if you are happy with the defaults and all of your
     * information is in one array.
     *
     * @param string $filename    The filename of the file being created.
     * @param array $records      An array of information to be converted.
     * @param string $delimiter   The name of the delimiter. Supported types(comma, tab, semicolon, colon, cfg)
     * @param string $enclosure   How speical fields are enclosed.
     */
    public static function download_array($filename, array &$records, $delimiter = 'comma', $enclosure='"') {
        $csvdata = new csv_export_writer($delimiter, $enclosure);
        $csvdata->set_filename($filename);
        foreach ($records as $row) {
            $csvdata->add_data($row);
        }
        $csvdata->download_file();
    }

    /**
     * This will convert an array of values into a deliminated string.
     * Like the above function, this is for convenience.
     *
     * @param array $records     An array of information to be converted.
     * @param string $delimiter  The name of the delimiter. Supported types(comma, tab, semicolon, colon, cfg)
     * @param string $enclosure  How speical fields are enclosed.
     * @param bool $return       If true will return a string with the csv data.
     * @return string            csv data.
     */
    public static function print_array(array &$records, $delimiter = 'comma', $enclosure = '"', $return = false) {
        $csvdata = new csv_export_writer($delimiter, $enclosure);
        foreach ($records as $row) {
            $csvdata->add_data($row);
        }
        $data = $csvdata->print_csv_data($return);
        if ($return) {
            return $data;
        }
    }

    /**
     * Make sure that everything is closed when we are finished.
     */
    public function __destruct() {
        fclose($this->fp);
        unlink($this->path);
    }
}
