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

/// This class represent the XMLDB base class where all the common piezes
/// are defined

class XMLDBObject {

    var $name;
    var $comment;
    var $previous;
    var $next;
    var $hash;
    var $loaded;
    var $changed;
    var $errormsg;

    /**
     * Creates one new XMLDBObject
     */
    function XMLDBObject($name) {
        $this->name = $name;
        $this->comment = NULL;
        $this->previous = NULL;
        $this->next = NULL;
        $this->hash = NULL;
        $this->loaded = false;
        $this->changed = false;
        $this->errormsg = NULL;
    }

    /**
     * This function returns true/false, if the XMLDBObject has been loaded
     */
    function isLoaded() {
        return $this->loaded;
    }

    /**
     * This function returns true/false, if the XMLDBObject has changed
     */
    function hasChanged() {
        return $this->changed;
    }

    /**
     * This function returns the comment of one XMLDBObject
     */
    function getComment() {
        return $this->comment;
    }

    /**
     * This function returns the hash of one XMLDBObject
     */
    function getHash() {
        return $this->hash;
    }

    /**
     * This function will return the name of the previous XMLDBObject
     */
    function getPrevious() {
        return $this->previous;
    }

    /**
     * This function will return the name of the next XMLDBObject
     */
    function getNext() {
        return $this->next;
    }

    /**
     * This function will return the name of the XMLDBObject
     */
    function getName() {
        return $this->name;
    }

    /**
     * This function will return the error detected in the object
     */
    function getError() {
        return $this->errormsg;
    }

    /**
     * This function will set the comment of the XMLDB object
     */
    function setComment($comment) {
        $this->comment = $comment;
    }

    /**
     * This function will set the previous of the XMLDB object
     */
    function setPrevious($previous) {
        $this->previous = $previous;
    }

    /**
     * This function will set the next of the XMLDB object
     */
    function setNext($next) {
        $this->next = $next;
    }

    /**
     * This function will set the hash of the XMLDB object
     */
    function setHash($hash) {
        $this->hash = $hash;
    }

    /**
     * This function will set the loaded field of the XMLDB object
     */
    function setLoaded($loaded = true) {
        $this->loaded = $loaded;
    }

    /**
     * This function will set the changed field of the XMLDB object
     */
    function setChanged($changed = true) {
        $this->changed = $changed;
    }
    /**
     * This function will set the name field of the XMLDB object
     */
    function setName($name) {
        $this->name = $name;
    }


    /**
     * This function will check if one key name is ok or no (true/false)
     * only lowercase a-z, 0-9 and _ are allowed
     */
    function checkName () {
        $result = true;

        if ($this->name != eregi_replace('[^a-z0-9_ -]', '', $this->name)) {
            $result = false;
        }
        return $result;
    }

    /**
     * This function will check that all the elements in one array
     * have a correct name [a-z0-9_]
     */
    function checkNameValues(&$arr) {
        $result = true;
    /// TODO: Perhaps, add support for reserved words

    /// Check the name only contains valid chars
        if ($arr) {
            foreach($arr as $element) {
                if (!$element->checkName()) {
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * Reconstruct previous/next attributes.
     */
    function fixPrevNext(&$arr) {
        global $CFG;

        if (empty($CFG->xmldbreconstructprevnext)) {
            return false;
        }
        $tweaked = false;

        $prev = null;
        foreach ($arr as $key=>$el) {
            $prev_value = $arr[$key]->previous;
            $next_value = $arr[$key]->next;

            $arr[$key]->next     = null;
            $arr[$key]->previous = null;
            if ($prev !== null) {
                $arr[$prev]->next    = $arr[$key]->name;
                $arr[$key]->previous = $arr[$prev]->name;
            }
            $prev = $key;

            if ($prev_value != $arr[$key]->previous or $next_value != $arr[$key]->next) {
                $tweaked = true;
            }
        }

        return $tweaked;
    }

    /**
     * This function will check that all the elements in one array
     * have a consistent info in their previous/next fields
     */
    function checkPreviousNextValues(&$arr) {
        global $CFG;
        if (!empty($CFG->xmldbdisablenextprevchecking)) {
            return true;
        }
        $result = true;
    /// Check that only one element has the previous not set
        if ($arr) {
            $counter = 0;
            foreach($arr as $element) {
                if (!$element->getPrevious()) {
                    $counter++;
                }
            }
            if ($counter != 1) {
                debugging('The number of tables with previous not set is different from 1', DEBUG_DEVELOPER);
                $result = false;
            }
        }
    /// Check that only one element has the next not set
        if ($result && $arr) {
            $counter = 0;
            foreach($arr as $element) {
                if (!$element->getNext()) {
                    $counter++;
                }
            }
            if ($counter != 1) {
                debugging('The number of tables with next not set is different from 1', DEBUG_DEVELOPER);
                $result = false;
            }
        }
    /// Check that all the previous elements are existing elements
        if ($result && $arr) {
            foreach($arr as $element) {
                if ($element->getPrevious()) {
                    $i = $this->findObjectInArray($element->getPrevious(), $arr);
                    if ($i === NULL) {
                        debugging('Table ' . $element->getName() . ' says PREVIOUS="' . $element->getPrevious() . '" but that other table does not exist.', DEBUG_DEVELOPER);
                        $result = false;
                    }
                }
            }
        }
    /// Check that all the next elements are existing elements
        if ($result && $arr) {
            foreach($arr as $element) {
                if ($element->getNext()) {
                    $i = $this->findObjectInArray($element->getNext(), $arr);
                    if ($i === NULL) {
                        debugging('Table ' . $element->getName() . ' says NEXT="' . $element->getNext() . '" but that other table does not exist.', DEBUG_DEVELOPER);
                        $result = false;
                    }
                }
            }
        }
    /// Check that there aren't duplicates in the previous values
        if ($result && $arr) {
            $existarr = array();
            foreach($arr as $element) {
                if (in_array($element->getPrevious(), $existarr)) {
                    $result = false;
                    debugging('Table ' . $element->getName() . ' says PREVIOUS="' . $element->getPrevious() . '" but another table has already said that!', DEBUG_DEVELOPER);
                } else {
                    $existarr[] = $element->getPrevious();
                }
            }
        }
    /// Check that there aren't duplicates in the next values
        if ($result && $arr) {
            $existarr = array();
            foreach($arr as $element) {
                if (in_array($element->getNext(), $existarr)) {
                    $result = false;
                    debugging('Table ' . $element->getName() . ' says NEXT="' . $element->getNext() . '" but another table has already said that!', DEBUG_DEVELOPER);
                } else {
                    $existarr[] = $element->getNext();
                }
            }
        }
        return $result;
    }

    /**
     * This function will order all the elements in one array, following
     * the previous/next rules
     */
    function orderElements($arr) {
        global $CFG;
        $result = true;
        if (!empty($CFG->xmldbdisablenextprevchecking)) {
            return $arr;
        }
    /// Create a new array
        $newarr = array();
        if (!empty($arr)) {
            $currentelement = NULL;
        /// Get the element without previous
            foreach($arr as $key => $element) {
                if (!$element->getPrevious()) {
                    $currentelement = $arr[$key];
                    $newarr[0] = $arr[$key];
                }
            }
            if (!$currentelement) {
                $result = false;
            }
        /// Follow the next rules
            $counter = 1;
            while ($result && $currentelement->getNext()) {
                $i = $this->findObjectInArray($currentelement->getNext(), $arr);
                $currentelement = $arr[$i];
                $newarr[$counter] = $arr[$i];
                $counter++;
            }
        /// Compare number of elements between original and new array
            if ($result && count($arr) != count($newarr)) {
                $result = false;
            }
        /// Check that previous/next is ok (redundant but...)
            if ($this->checkPreviousNextValues($newarr)) {
                $result = $newarr;
            } else {
                $result = false;
            }
        } else {
            $result = array();
        }
        return $result;
    }

    /**
     * Returns the position of one object in the array.
     */
    function &findObjectInArray($objectname, $arr) {
        foreach ($arr as $i => $object) {
            if ($objectname == $object->getName()) {
                return $i;
            }
        }
        $null = NULL;
        return $null;
    }

    /**
     * This function will display a readable info about the XMLDBObject
     * (should be implemented inside each XMLDBxxx object)
     */
    function readableInfo() {
        return get_class($this);
    }

    /**
     * This function will perform the central debug of all the XMLDB classes
     * being called automatically every time one error is found. Apart from 
     * the main actions performed in it (XMLDB agnostic) it looks for one
     * function called xmldb_debug() and invokes it, passing both the
     * message code and the whole object. 
     * So, to perform custom debugging just add such function to your libs.
     * 
     * Call to the external hook function can be disabled by request by
     * defining XMLDB_SKIP_DEBUG_HOOK
     */
    function debug($message) {

    /// Check for xmldb_debug($message, $xmldb_object)
        $funcname = 'xmldb_debug';
    /// If exists and XMLDB_SKIP_DEBUG_HOOK is undefined
        if (function_exists($funcname) && !defined('XMLDB_SKIP_DEBUG_HOOK')) {
            $funcname($message, $this);
        }
    }

    /**
     * Returns one array of elements from one comma separated string,
     * supporting quoted strings containing commas and concat function calls
     */
    function comma2array($string) {

        $arr = array();

        $foundquotes  = array();
        $foundconcats = array();

    /// Extract all the concat elements from the string
        preg_match_all("/(CONCAT\(.*?\))/is", $string, $matches);
        foreach (array_unique($matches[0]) as $key=>$value) {
            $foundconcats['<#'.$key.'#>'] = $value;
        }
        if (!empty($foundconcats)) {
            $string = str_replace($foundconcats,array_keys($foundconcats),$string);
        }

    /// Extract all the quoted elements from the string (skipping 
    /// backslashed quotes that are part of the content.
        preg_match_all("/('.*?[^\\\]')/is", $string, $matches);
        foreach (array_unique($matches[0]) as $key=>$value) {
            $foundquotes['<%'.$key.'%>'] = $value;
        }
        if (!empty($foundquotes)) {
            $string = str_replace($foundquotes,array_keys($foundquotes),$string);
        }

    /// Explode safely the string
        $arr = explode (',', $string);

    /// Put the concat and quoted elements back again, triming every element
        if ($arr) {
            foreach ($arr as $key => $element) {
            /// Clear some spaces
                $element = trim($element);
            /// Replace the quoted elements if exists
                if (!empty($foundquotes)) {
                    $element = str_replace(array_keys($foundquotes), $foundquotes, $element);
                }
            /// Replace the concat elements if exists
                if (!empty($foundconcats)) {
                    $element = str_replace(array_keys($foundconcats), $foundconcats, $element);
                }
            /// Delete any backslash used for quotes. XMLDB stuff will add them before insert
                $arr[$key] = str_replace("\\'", "'", $element);
            }
        }

        return $arr;
    }
}

?>
