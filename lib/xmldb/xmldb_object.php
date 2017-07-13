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
 * This class represent the XMLDB base class where all the common pieces are defined
 *
 * @package    core_xmldb
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


class xmldb_object {

    /** @var string name of obejct */
    protected $name;

    /** @var string comment on object */
    protected $comment;

    /** @var xmldb_object */
    protected $previous;

    /** @var xmldb_object */
    protected $next;

    /** @var string hash of object */
    protected $hash;

    /** @var bool is it loaded yet */
    protected $loaded;

    /** @var bool was object changed */
    protected $changed;

    /** @var string error message */
    protected $errormsg;

    /**
     * Creates one new xmldb_object
     * @param string $name
     */
    public function __construct($name) {
        $this->name = $name;
        $this->comment = null;
        $this->previous = null;
        $this->next = null;
        $this->hash = null;
        $this->loaded = false;
        $this->changed = false;
        $this->errormsg = null;
    }

    /**
     * This function returns true/false, if the xmldb_object has been loaded
     * @return bool
     */
    public function isLoaded() {
        return $this->loaded;
    }

    /**
     * This function returns true/false, if the xmldb_object has changed
     * @return bool
     */
    public function hasChanged() {
        return $this->changed;
    }

    /**
     * This function returns the comment of one xmldb_object
     * @return string
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * This function returns the hash of one xmldb_object
     * @return string
     */
    public function getHash() {
        return $this->hash;
    }

    /**
     * This function will return the name of the previous xmldb_object
     * @return xmldb_object
     */
    public function getPrevious() {
        return $this->previous;
    }

    /**
     * This function will return the name of the next xmldb_object
     * @return xmldb_object
     */
    public function getNext() {
        return $this->next;
    }

    /**
     * This function will return the name of the xmldb_object
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * This function will return the error detected in the object
     * @return string
     */
    public function getError() {
        return $this->errormsg;
    }

    /**
     * This function will set the comment of the xmldb_object
     * @param string $comment
     */
    public function setComment($comment) {
        $this->comment = $comment;
    }

    /**
     * This function will set the previous of the xmldb_object
     * @param xmldb_object $previous
     */
    public function setPrevious($previous) {
        $this->previous = $previous;
    }

    /**
     * This function will set the next of the xmldb_object
     * @param xmldb_object $next
     */
    public function setNext($next) {
        $this->next = $next;
    }

    /**
     * This function will set the hash of the xmldb_object
     * @param string $hash
     */
    public function setHash($hash) {
        $this->hash = $hash;
    }

    /**
     * This function will set the loaded field of the xmldb_object
     * @param bool $loaded
     */
    public function setLoaded($loaded = true) {
        $this->loaded = $loaded;
    }

    /**
     * This function will set the changed field of the xmldb_object
     * @param bool $changed
     */
    public function setChanged($changed = true) {
        $this->changed = $changed;
    }
    /**
     * This function will set the name field of the xmldb_object
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }


    /**
     * This function will check if one key name is ok or no (true/false)
     * only lowercase a-z, 0-9 and _ are allowed
     * @return bool
     */
    public function checkName () {
        $result = true;

        if ($this->name != preg_replace('/[^a-z0-9_ -]/i', '', $this->name)) {
            $result = false;
        }
        return $result;
    }

    /**
     * This function will check that all the elements in one array
     * have a correct name [a-z0-9_]
     * @param array $arr
     * @return bool
     */
    public function checkNameValues($arr) {
        $result = true;
        // TODO: Perhaps, add support for reserved words

        // Check the name only contains valid chars
        if ($arr) {
            foreach($arr as $element) {
                if (!$element->checkName()) {
                    $result = false;
                }
            }
        }
        // Check there aren't duplicate names
        if ($arr) {
            $existing_fields = array();
            foreach($arr as $element) {
                if (in_array($element->getName(), $existing_fields)) {
                    debugging('Object ' . $element->getName() . ' is duplicated!', DEBUG_DEVELOPER);
                    $result = false;
                }
                $existing_fields[] = $element->getName();
            }
        }
        return $result;
    }

    /**
     * Reconstruct previous/next attributes.
     * @param array $arr
     * @return bool true if $arr modified
     */
    public function fixPrevNext(&$arr) {
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
     * This function will order all the elements in one array, following
     * the previous/next rules
     * @param array $arr
     * @return array|bool
     */
    public function orderElements($arr) {
        $result = true;

        // Create a new array
        $newarr = array();
        if (!empty($arr)) {
            $currentelement = null;
            // Get the element without previous
            foreach($arr as $key => $element) {
                if (!$element->getPrevious()) {
                    $currentelement = $arr[$key];
                    $newarr[0] = $arr[$key];
                }
            }
            if (!$currentelement) {
                $result = false;
            }
            // Follow the next rules
            $counter = 1;
            while ($result && $currentelement->getNext()) {
                $i = $this->findObjectInArray($currentelement->getNext(), $arr);
                $currentelement = $arr[$i];
                $newarr[$counter] = $arr[$i];
                $counter++;
            }
            // Compare number of elements between original and new array
            if ($result && count($arr) != count($newarr)) {
                $result = false;
            } else if ($newarr) {
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
     * @param string $objectname
     * @param array $arr
     * @return mixed
     */
    public function findObjectInArray($objectname, $arr) {
        foreach ($arr as $i => $object) {
            if ($objectname == $object->getName()) {
                return $i;
            }
        }
        return null;
    }

    /**
     * This function will display a readable info about the xmldb_object
     * (should be implemented inside each XMLDBxxx object)
     * @return string
     */
    public function readableInfo() {
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
     * @param string $message
     */
    public function debug($message) {

        // Check for xmldb_debug($message, $xmldb_object)
        $funcname = 'xmldb_debug';
        // If exists and XMLDB_SKIP_DEBUG_HOOK is undefined
        if (function_exists($funcname) && !defined('XMLDB_SKIP_DEBUG_HOOK')) {
            $funcname($message, $this);
        }
    }

    /**
     * Returns one array of elements from one comma separated string,
     * supporting quoted strings containing commas and concat function calls
     * @param string $string
     * @return array
     */
    public function comma2array($string) {

        $foundquotes  = array();
        $foundconcats = array();

        // Extract all the concat elements from the string
        preg_match_all("/(CONCAT\(.*?\))/is", $string, $matches);
        foreach (array_unique($matches[0]) as $key=>$value) {
            $foundconcats['<#'.$key.'#>'] = $value;
        }
        if (!empty($foundconcats)) {
            $string = str_replace($foundconcats,array_keys($foundconcats),$string);
        }

        // Extract all the quoted elements from the string (skipping
        // backslashed quotes that are part of the content.
        preg_match_all("/(''|'.*?[^\\\\]')/is", $string, $matches);
        foreach (array_unique($matches[0]) as $key=>$value) {
            $foundquotes['<%'.$key.'%>'] = $value;
        }
        if (!empty($foundquotes)) {
            $string = str_replace($foundquotes,array_keys($foundquotes),$string);
        }

        // Explode safely the string
        $arr = explode (',', $string);

        // Put the concat and quoted elements back again, trimming every element
        if ($arr) {
            foreach ($arr as $key => $element) {
                // Clear some spaces
                $element = trim($element);
                // Replace the quoted elements if exists
                if (!empty($foundquotes)) {
                    $element = str_replace(array_keys($foundquotes), $foundquotes, $element);
                }
                // Replace the concat elements if exists
                if (!empty($foundconcats)) {
                    $element = str_replace(array_keys($foundconcats), $foundconcats, $element);
                }
                // Delete any backslash used for quotes. XMLDB stuff will add them before insert
                $arr[$key] = str_replace("\\'", "'", $element);
            }
        }

        return $arr;
    }

    /**
     * Validates the definition of objects and returns error message.
     *
     * The error message should not be localised because it is intended for developers,
     * end users and admins should never see these problems!
     *
     * @param xmldb_table $xmldb_table optional when object is table
     * @return string null if ok, error message if problem found
     */
    public function validateDefinition(xmldb_table $xmldb_table=null) {
        return null;
    }
}
