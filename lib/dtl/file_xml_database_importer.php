<?php  //$Id$

/**
 * XML format importer class from file storage.
 */
class file_xml_database_importer extends xml_database_importer {
    /** Path to the XML data file. */
    protected $filepath;

    /**
     * Object constructor.
     *
     * @param string $filepath - path to the XML data file. Use null for PHP
     * input stream.
     * @param moodle_database $mdb Connection to the target database
     * @see xml_database_importer::__construct()
     * @param boolean $check_schema - whether or not to check that XML database
     * @see xml_database_importer::__construct()
     */
    public function __construct($filepath, moodle_database $mdb, $check_schema=true) {
        if (is_null($filepath)) {
            $filepath = 'php://input';
        }
        $this->filepath = $filepath;
        parent::__construct($mdb, $check_schema);
    }

    /**
     * Common import method: it opens the file storage, creates the parser, feeds
     * the XML parser with data, releases the parser and closes the file storage.
     * @return void
     */
    public function import_database() {
        $file = fopen($this->filepath, 'r');
        $parser = $this->get_parser();
        while ($data = fread($file, 65536)) {
            if (!xml_parse($parser, $data, feof($file))) {
                //TODO localize
                throw new import_exception("XML data not well-formed.");
            }
        }
        xml_parser_free($parser);
        fclose($file);
    }
}
