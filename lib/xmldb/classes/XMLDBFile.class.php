<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// This class represents an entire XMLDB file

class XMLDBFile extends XMLDBObject {

    var $path;
    var $schema;
    var $dtd;
    var $xmldb_structure;

    /**
     * Constructor of the XMLDBFile
     */
    function XMLDBFile ($path) {
        parent::XMLDBObject($path);
        $this->path = $path;
        $this->xmldb_structure = NULL;
    }

    /**
     * Determine if the XML file exists
     */
    function fileExists() {
        if (file_exists($this->path) && is_readable($this->path)) {
            return true;
        }
        return false;
    }

    /**
     * Determine if the XML is writeable
     */
    function fileWriteable() {
        if (is_writeable(dirname($this->path))) {
            return true;
        }
        return false;
    }

    function &getStructure() {
        return $this->xmldb_structure;
    }

    /**
     * This function will check/validate the XML file for correctness 
     * Dinamically if will use the best available checker/validator
     * (expat syntax checker or DOM schema validator
     */
    function validateXMLStructure() {

    /// Going to perform complete DOM schema validation
        if (extension_loaded('dom') && method_exists(new DOMDocument(), 'load')) {
        /// Let's capture errors
            if (function_exists('libxml_use_internal_errors')) {
                libxml_use_internal_errors(true); // This function is PHP5 only (MDL-8730)
            }

        /// Create and load XML file
            $parser = new DOMDocument();
            $parser->load($this->path);
        /// Only validate if we have a schema
            if (!empty($this->schema) && file_exists($this->schema)) {
                $parser->schemaValidate($this->schema);
            }
        /// Check for errors
            $errors = false;
            if (function_exists('libxml_get_errors')) {
                $errors = libxml_get_errors();
            }

        /// Prepare errors
            if (!empty($errors)) {
            /// Create one structure to store errors
                $structure = new XMLDBStructure($this->path);
            /// Add errors to structure
                $structure->errormsg = 'XML Error: ';
                foreach ($errors as $error) {
                    $structure->errormsg .= sprintf("%s at line %d. ",
                                                     trim($error->message, "\n\r\t ."),
                                                     $error->line);
                }
            /// Add structure to file
                $this->xmldb_structure = $structure;
            /// Check has failed
                return false;
            }
        }
    /// Going to perform expat simple check (no validation)
        else if (function_exists('xml_parser_create')) {
            $parser = xml_parser_create();
            if (!xml_parse($parser, file_get_contents($this->path))) {
            /// Create one structure to store errors
                $structure = new XMLDBStructure($this->path);
            /// Add error to structure
                $structure->errormsg = sprintf("XML Error: %s at line %d", 
                         xml_error_string(xml_get_error_code($parser)),
                         xml_get_current_line_number($parser));
            /// Add structure to file
                $this->xmldb_structure = $structure;
            /// Check has failed
                return false;
            }
        /// Free parser resources
            xml_parser_free($parser);
        }
    /// Arriving here, something is really wrong because nor dom not expat are present
        else {
            return false;
        }
        return true;
    }

    /**
     * Load and the XMLDB structure from file
     */
    function loadXMLStructure() {
        if ($this->fileExists()) {
        /// Let's validate the XML file
            if (!$this->validateXMLStructure()) {
                return false;
            }
        /// File exists, so let's process it
        /// Load everything to a big array
            $xmlarr = xmlize(file_get_contents($this->path));
        /// Convert array to xmldb structure
            $this->xmldb_structure = $this->arr2XMLDBStructure($xmlarr);
        /// Analize results
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
     * This function takes an xmlized array and put it into one XMLDBStructure
     */
    function arr2XMLDBStructure ($xmlarr) {
        $structure = new XMLDBStructure($this->path);
        $structure->arr2XMLDBStructure($xmlarr);
        return $structure;
    }

    /**
     * This function sets the DTD of the XML file
     */
    function setDTD($path) {
        $this->dtd = $path;
    }

    /**
     * This function sets the schema of the XML file
     */
    function setSchema($path) {
        $this->schema = $path;
    }

    /**
     * This function saves the whole XMLDBStructure to its file
     */
    function saveXMLFile() {

        $result = true;

        $structure =& $this->getStructure();

        $result = file_put_contents($this->path, $structure->xmlOutput());

        return $result;
    }
}

?>
