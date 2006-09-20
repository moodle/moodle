<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-3001 Martin Dougiamas        http://dougiamas.com  //
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
     * Load and the XMLDB structure from file
     */
    function loadXMLStructure() {
        if ($this->fileExists()) {
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
