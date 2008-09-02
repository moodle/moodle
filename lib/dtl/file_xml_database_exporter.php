<?php  //$Id$

/**
 * XML format exporter class to file storage.
 */
class file_xml_database_exporter extends xml_database_exporter {
    /** Path to the XML data file. */
    protected $filepath;
    /** File descriptor for the output file. */
    protected $file;

    /**
     * Object constructor.
     *
     * @param string $filepath - path to the XML data file. Use 'php://output' for PHP
     * output stream.
     * @param moodle_database $mdb Connection to the source database
     * @see xml_database_exporter::__construct()
     * @param boolean $check_schema - whether or not to check that XML database
     * @see xml_database_exporter::__construct()
     */
    public function __construct($filepath, moodle_database $mdb, $check_schema=true) {
        parent::__construct($mdb, $check_schema);
        $this->filepath = $filepath;
    }

    /**
     * Specific output method for the file XML sink.
     */
    protected function output($text) {
        fwrite($this->file, $text);
    }

    /**
     * Specific implementation for file exporting the database: it opens output stream, calls
     * superclass @see database_exporter::export_database() and closes output stream.
     *
     * @exception dbtransfer_exception if any checking (e.g. database schema) fails
     *
     * @param string $description a user description of the data.
     */
    public function export_database($description=null) {
        // TODO: add exception if file creation fails
        $this->file = fopen($this->filepath, 'wb');
        parent::export_database($description);
        fclose($this->file);
    }
}
