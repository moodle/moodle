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
 * This class represent one XMLDB file
 *
 * @package    core_xmldb
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


class xmldb_file extends xmldb_object {

    /** @var string path to file */
    protected $path;

    /** @var string path to schema */
    protected $schema;

    /** @var  string document dtd */
    protected $dtd;

    /** @var xmldb_structure the structure stored in file */
    protected $xmldb_structure;

    /**
     * Constructor of the xmldb_file
     * @param string $path
     */
    public function __construct($path) {
        parent::__construct($path);
        $this->path = $path;
        $this->xmldb_structure = null;
    }

    /**
     * Determine if the XML file exists
     * @return bool
     */
    public function fileExists() {
        if (file_exists($this->path) && is_readable($this->path)) {
            return true;
        }
        return false;
    }

    /**
     * Determine if the XML is writeable
     * @return bool
     */
    public function fileWriteable() {
        if (is_writeable(dirname($this->path))) {
            return true;
        }
        return false;
    }

    public function getStructure() {
        return $this->xmldb_structure;
    }

    /**
     * This function will check/validate the XML file for correctness
     * Dynamically if will use the best available checker/validator
     * (expat syntax checker or DOM schema validator
     * @return true
     */
    public function validateXMLStructure() {

        // Create and load XML file
        $parser = new DOMDocument();
        $contents = file_get_contents($this->path);
        if (strpos($contents, '<STATEMENTS>')) {
            //delete the removed STATEMENTS section, it would not validate
            $contents = preg_replace('|<STATEMENTS>.*</STATEMENTS>|s', '', $contents);
        }

        // Let's capture errors
        $olderrormode = libxml_use_internal_errors(true);

        // Clear XML error flag so that we don't incorrectly report failure
        // when a previous xml parse failed
        libxml_clear_errors();

        $parser->loadXML($contents);
        // Only validate if we have a schema
        if (!empty($this->schema) && file_exists($this->schema)) {
            $parser->schemaValidate($this->schema);
        }
        // Check for errors
        $errors = libxml_get_errors();

        // Stop capturing errors
        libxml_use_internal_errors($olderrormode);

        // Prepare errors
        if (!empty($errors)) {
            // Create one structure to store errors
            $structure = new xmldb_structure($this->path);
            // Add errors to structure
            $structure->errormsg = 'XML Error: ';
            foreach ($errors as $error) {
                $structure->errormsg .= sprintf("%s at line %d. ",
                                                 trim($error->message, "\n\r\t ."),
                                                 $error->line);
            }
            // Add structure to file
            $this->xmldb_structure = $structure;
            // Check has failed
            return false;
        }

        return true;
    }

    /**
     * Load and the XMLDB structure from file
     * @return true
     */
    public function loadXMLStructure() {
        if ($this->fileExists()) {
            // Let's validate the XML file
            if (!$this->validateXMLStructure()) {
                return false;
            }
            $contents = file_get_contents($this->path);
            if (strpos($contents, '<STATEMENTS>')) {
                //delete the removed STATEMENTS section, it would not validate
                $contents = preg_replace('|<STATEMENTS>.*</STATEMENTS>|s', '', $contents);
                debugging('STATEMENTS section is not supported any more, please use db/install.php or db/log.php');
            }
            // File exists, so let's process it
            // Load everything to a big array
            $xmlarr = xmlize($contents);
            // Convert array to xmldb structure
            $this->xmldb_structure = $this->arr2xmldb_structure($xmlarr);
            // Analyze results
            if ($this->xmldb_structure->isLoaded()) {
                $this->loaded = true;
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * This function takes an xmlized array and put it into one xmldb_structure
     * @param array $xmlarr
     * @return xmldb_structure
     */
    public function arr2xmldb_structure ($xmlarr) {
        $structure = new xmldb_structure($this->path);
        $structure->arr2xmldb_structure($xmlarr);
        return $structure;
    }

    /**
     * This function sets the DTD of the XML file
     * @param string
     */
    public function setDTD($path) {
        $this->dtd = $path;
    }

    /**
     * This function sets the schema of the XML file
     * @param string
     */
    public function setSchema($path) {
        $this->schema = $path;
    }

    /**
     * This function saves the whole xmldb_structure to its file
     * @return int|bool false on failure, number of written bytes on success
     */
    public function saveXMLFile() {

        $structure = $this->getStructure();

        $result = file_put_contents($this->path, $structure->xmlOutput());

        return $result;
    }
}
