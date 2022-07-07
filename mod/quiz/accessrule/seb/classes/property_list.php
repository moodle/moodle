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
 * Wrapper for CFPropertyList to handle low level iteration.
 *
 * @package    quizaccess_seb
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quizaccess_seb;

use CFPropertyList\CFArray;
use CFPropertyList\CFBoolean;
use CFPropertyList\CFData;
use CFPropertyList\CFDate;
use CFPropertyList\CFDictionary;
use CFPropertyList\CFNumber;
use CFPropertyList\CFPropertyList;
use CFPropertyList\CFString;
use CFPropertyList\CFType;
use \DateTime;

defined('MOODLE_INTERNAL') || die();

/**
 * Wrapper for CFPropertyList to handle low level iteration.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class property_list {

    /** A random 4 character unicode string to replace backslashes during json_encode. */
    private const BACKSLASH_SUBSTITUTE = "ؼҷҍԴ";

    /** @var CFPropertyList $cfpropertylist */
    private $cfpropertylist;

    /**
     * property_list constructor.
     *
     * @param string $xml A Plist XML string.
     */
    public function __construct(string $xml = '') {
        $this->cfpropertylist = new CFPropertyList();

        if (empty($xml)) {
            // If xml not provided, create a blank PList with root dictionary set up.
            $this->cfpropertylist->add(new CFDictionary([]));
        } else {
            // Parse the XML into a PList object.
            $this->cfpropertylist->parse($xml, CFPropertyList::FORMAT_XML);
        }
    }

    /**
     * Add a new element to the root dictionary element.
     *
     * @param string $key Key to assign to new element.
     * @param CFType $element The new element. May be a collection such as an array.
     */
    public function add_element_to_root(string $key, CFType $element) {
        // Get the PList's root dictionary and add new element.
        $this->cfpropertylist->getValue()->add($key, $element);
    }

    /**
     * Get value of element identified by key.
     *
     * @param string $key Key of element.
     * @return mixed Value of element found, or null if none found.
     */
    public function get_element_value(string $key) {
        $result = null;
        $this->plist_map( function($elvalue, $elkey, $parent) use ($key, &$result) {
            // Convert date to iso 8601 if date object.
            if ($key === $elkey) {
                $result = $elvalue->getValue();
            }
        }, $this->cfpropertylist->getValue());

        if (is_array($result)) {
            // Turn CFType elements in PHP elements.
            $result = $this->array_serialize_cftypes($result);
        }
        return $result;
    }

    /**
     * Update the value of any element with matching key.
     *
     * Only allow string, number and boolean elements to be updated.
     *
     * @param string $key Key of element to update.
     * @param mixed $value Value to update element with.
     */
    public function update_element_value(string $key, $value) {
        if (is_array($value)) {
            throw new \invalid_parameter_exception('Use update_element_array to update a collection.');
        }
        $this->plist_map( function($elvalue, $elkey, $parent) use ($key, $value) {
            // Set new value.
            if ($key === $elkey) {
                $element = $parent->get($elkey);
                // Limit update to boolean and strings types, and check value matches expected type.
                if (($element instanceof CFString && is_string($value))
                        || ($element instanceof CFNumber && is_numeric($value))
                        || ($element instanceof CFBoolean && is_bool($value))) {
                    $element->setValue($value);
                } else {
                    throw new \invalid_parameter_exception(
                            'Only string, number and boolean elements can be updated, or value type does not match element type: '
                            . get_class($element));
                }
            }
        }, $this->cfpropertylist->getValue());
    }

    /**
     * Update the array of any dict or array element with matching key.
     *
     * Will replace array.
     *
     * @param string $key Key of element to update.
     * @param array $value Array to update element with.
     */
    public function update_element_array(string $key, array $value) {
        // Validate new array.
        foreach ($value as $element) {
            // If any element is not a CFType instance, then throw exception.
            if (!($element instanceof CFType)) {
                throw new \invalid_parameter_exception('New array must only contain CFType objects.');
            }
        }
        $this->plist_map( function($elvalue, $elkey, $parent) use ($key, $value) {
            if ($key === $elkey) {
                $element = $parent->get($elkey);
                // Replace existing element with new element and array but same key.
                if ($element instanceof CFDictionary) {
                    $parent->del($elkey);
                    $parent->add($elkey, new CFDictionary($value));
                } else if ($element instanceof CFArray) {
                    $parent->del($elkey);
                    $parent->add($elkey, new CFArray($value));
                }
            }
        }, $this->cfpropertylist->getValue());
    }

    /**
     * Delete any element with a matching key.
     *
     * @param string $key Key of element to delete.
     */
    public function delete_element(string $key) {
        $this->plist_map( function($elvalue, $elkey, $parent) use ($key) {
            // Convert date to iso 8601 if date object.
            if ($key === $elkey) {
                $parent->del($key);
            }
        }, $this->cfpropertylist->getValue());
    }

    /**
     * Helper function to either set or update a CF type value to the plist.
     *
     * @param string $key
     * @param CFType $input
     */
    public function set_or_update_value(string $key, CFType $input) {
        $value = $this->get_element_value($key);
        if (empty($value)) {
            $this->add_element_to_root($key, $input);
        } else {
            $this->update_element_value($key, $input->getValue());
        }
    }

    /**
     * Convert the PList to XML.
     *
     * @return string XML ready for creating an XML file.
     */
    public function to_xml() : string {
        return $this->cfpropertylist->toXML();
    }

    /**
     * Return a JSON representation of the PList. The JSON is constructed to be used to generate a SEB Config Key.
     *
     * See the developer documention for SEB for more information on the requirements on generating a SEB Config Key.
     * https://safeexambrowser.org/developer/seb-config-key.html
     *
     * 1. Don't add any whitespace or line formatting to the SEB-JSON string.
     * 2. Don't add character escaping (also backshlashes "\" as found in URL filter rules should not be escaped).
     * 3. All <dict> elements from the plist XML must be ordered (alphabetically sorted) by their key names. Use a
     * recursive method to apply ordering also to nested dictionaries contained in the root-level dictionary and in
     * arrays. Use non-localized (culture invariant), non-ASCII value based case insensitive ordering. For example the
     * key <key>allowWlan</key> comes before <key>allowWLAN</key>. Cocoa/Obj-C and .NET/C# usually use this case
     * insensitive ordering as default, but PHP for example doesn't.
     * 4. Remove empty <dict> elements (key/value). Current versions of SEB clients should anyways not generate empty
     * dictionaries, but this was possible with outdated versions. If config files have been generated that time, such
     * elements might still be around.
     * 5. All string elements must be UTF8 encoded.
     * 6. Base16 strings should use lower-case a-f characters, even though this isn't relevant in the current
     * implementation of the Config Key calculation.
     * 7. <data> plist XML elements must be converted to Base64 strings.
     * 8. <date> plist XML elements must be converted to ISO 8601 formatted strings.
     *
     * @return string A json encoded string.
     */
    public function to_json() : string {
        // Create a clone of the PList, so main list isn't mutated.
        $jsonplist = new CFPropertyList();
        $jsonplist->parse($this->cfpropertylist->toXML(), CFPropertyList::FORMAT_XML);

        // Pass root dict to recursively convert dates to ISO 8601 format, encode strings to UTF-8,
        // lock data to Base 64 encoding and remove empty dictionaries.
        $this->prepare_plist_for_json_encoding($jsonplist->getValue());

        // Serialize PList to array.
        $plistarray = $jsonplist->toArray();

        // Sort array alphabetically by key using case insensitive, natural sorting. See point 3 for more information.
        $plistarray = $this->array_sort($plistarray);

        // Encode in JSON with following rules from SEB docs.
        // 1. Don't add any whitespace or line formatting to the SEB-JSON string.
        // 2. Don't add unicode or slash escaping.
        $json = json_encode($plistarray, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

        // There is no way to prevent json_encode from escaping backslashes. We replace each backslash with a unique string
        // prior to encoding in prepare_plist_for_json_encoding(). We can then replace the substitute with a single backslash.
        $json = str_replace(self::BACKSLASH_SUBSTITUTE, "\\", $json);
        return $json;
    }

    /**
     * Recursively convert PList date values from unix to iso 8601 format, and ensure strings are UTF 8 encoded.
     *
     * This will mutate the PList.
     */

    /**
     * Recursively convert PList date values from unix to iso 8601 format, and ensure strings are UTF 8 encoded.
     *
     * This will mutate the PList.
     * @param \Iterator $root The root element of the PList. Must be a dictionary or array.
     */
    private function prepare_plist_for_json_encoding($root) {
        $this->plist_map( function($value, $key, $parent) {
            // Convert date to ISO 8601 if date object.
            if ($value instanceof CFDate) {
                $date = DateTime::createFromFormat('U', $value->getValue());
                $date->setTimezone(new \DateTimeZone('UTC')); // Zulu timezone a.k.a. UTC+00.
                $isodate = $date->format('c');
                $value->setValue($isodate);
            }
            // Make sure strings are UTF 8 encoded.
            if ($value instanceof CFString) {
                // As literal backslashes will be lost during encoding, we must replace them with a unique substitute to be
                // reverted after JSON encoding.
                $string = str_replace("\\", self::BACKSLASH_SUBSTITUTE, $value->getValue());
                $value->setValue(mb_convert_encoding($string, 'UTF-8'));
            }
            // Data should remain base 64 encoded, so convert to base encoded string for export. Otherwise
            // CFData will decode the data when serialized.
            if ($value instanceof CFData) {
                $data = trim($value->getCodedValue());
                $parent->del($key);
                $parent->add($key, new CFString($data));
            }
            // Empty dictionaries should be removed.
            if ($value instanceof CFDictionary && empty($value->getValue())) {
                $parent->del($key);
            }
        }, $root);

    }

    /**
     * Iterate through the PList elements, and call the callback on each.
     *
     * @param callable $callback A callback function called for every element.
     * @param \Iterator $root The root element of the PList. Must be a dictionary or array.
     * @param bool $recursive Whether the function should traverse dicts and arrays recursively.
     */
    private function plist_map(callable $callback, \Iterator $root, bool $recursive = true) {
        $root->rewind();
        while ($root->valid()) {
            $value = $root->current();
            $key = $root->key();

            // Recursively traverse all dicts and arrays if flag is true.
            if ($recursive && $value instanceof \Iterator) {
                $this->plist_map($callback, $value);
            }

            // Callback function called for every element.
            $callback($value, $key, $root);

            $root->next();
        }
    }

    /**
     * Recursively sort array alphabetically by key.
     *
     * @param array $array Top level array to process.
     * @return array Processed array.
     */
    private function array_sort(array $array) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->array_sort($array[$key]);
            }
        }
        // Sort assoc array. From SEB docs - "Use non-localized (culture invariant), non-ASCII value based case
        // insensitive ordering."
        if ($this->is_associative_array($array)) {
            ksort($array, SORT_STRING | SORT_FLAG_CASE);
        }

        return $array;
    }

    /**
     * Recursively remove empty arrays.
     *
     * @param array $array Top level array to process.
     * @return array Processed array.
     */
    private function array_remove_empty_arrays(array $array) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->array_remove_empty_arrays($array[$key]);
            }

            // Remove empty arrays.
            if (is_array($array[$key]) && empty($array[$key])) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * If an array contains CFType objects, wrap array in a CFDictionary to allow recursive serialization of data
     * into a standard PHP array.
     *
     * @param array $array Array containing CFType objects.
     * @return array Standard PHP array.
     */
    private function array_serialize_cftypes(array $array) : array {
        $array = new CFDictionary($array); // Convert back to CFDictionary so serialization is recursive.
        return $array->toArray(); // Serialize.
    }

    /**
     * Check if an array is associative or sequential.
     *
     * @param array $array Array to check.
     * @return bool False if not associative.
     */
    private function is_associative_array(array $array) {
        if (empty($array)) {
            return false;
        }
        // Check that all keys are not sequential integers starting from 0 (Which is what PHP arrays have behind the scenes.)
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
