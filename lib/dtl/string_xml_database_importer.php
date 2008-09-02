<?php  //$Id$

/**
 * XML format importer class from memory storage (i.e. string).
 */
class string_xml_database_importer extends xml_database_importer {
    /** String with XML data. */
    protected $data;

    /**
     * Object constructor.
     *
     * @param string data - string with XML data
     * @param moodle_database $mdb Connection to the target database
     * @see xml_database_importer::__construct()
     * @param boolean $check_schema - whether or not to check that XML database
     * @see xml_database_importer::__construct()
     */
    public function __construct($data, moodle_database $mdb, $check_schema=true) {
        parent::__construct($mdb, $check_schema);
        $this->data = $data;
    }

    /**
     * Common import method: it creates the parser, feeds the XML parser with
     * data, releases the parser.
     * @return void
     */
    public function import_database() {
        $parser = $this->get_parser();
        if (!xml_parse($parser, $this->data, true)) {
            throw new dbtransfer_exception('malformedxmlexception');
        }
        xml_parser_free($parser);
    }
}
