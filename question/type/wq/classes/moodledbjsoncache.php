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
 * This class implements WIRIS StorageAndCache interface
 * to store WIRIS data on MUC and Moodle database.
 *
 * @package    filter
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class moodledbjsoncache {

    private $cachetable;
    private $keyfield;
    private $jsonfield;
    private $timecreatedfield;

    /**
     * Constructores for WIRIS file cache.
     * @param String $area   cache area.
     * @param String $module cache definition.
     */
    public function __construct($tablename, $keyfield, $jsonfield) {
        $this->cachetable = $tablename;
        $this->keyfield = $keyfield;
        $this->jsonfield = $jsonfield;
        $this->timecreatedfield = 'timecreated';
    }

    /**
     * Delete the given key from the cache
     * @param key The key to delete.
     * @throw Error On unexpected exception.
     */
    public function delete($key) {
    }

    /**
     * Deletes all the data in the cache.
     * @throw moodle_exception failing purgue the cache.
     */
    // @codingStandardsIgnoreStart
    public function deleteAll() {
    // @codingStandardsIgnoreEnd
        global $DB;
        $DB->delete_records($this->cachetable, null);
    }

    /**
     * Retrieves the value for the given key for the cache.
     * @param key The key for for the data being requested.
     * @return Bytes The data retrieved from the cache. Returns null on cache miss or error.
     */
    public function get($key) {

        $parsedkey = $this->parse_key($key);

        global $DB;

        $jsonfield = $this->jsonfield;

        if ($DB->record_exists($this->cachetable, array($this->keyfield => $parsedkey))) {

            $record = $DB->get_record($this->cachetable, array($this->keyfield => $parsedkey));
            if (strpos($key, '.svg') !== false) {
                return haxe_io_Bytes::ofData(com_wiris_system_Utf8::toBytes($record->$jsonfield));
            } else if (strpos($key, '.txt') !== false) {
                return haxe_io_Bytes::ofData(com_wiris_system_Utf8::toBytes($record->alt));
            } else {
                return null;
            }
            if (isset($record->$jsonfield) && $record->$jsonfield != '') {

                $base64decoder = new com_wiris_system_Base64();
                if (strpos($key, '.png') !== false) {
                    $jsonfield = $this->jsonfield;
                    $json = com_wiris_util_json_JSon::decode($record->$jsonfield, true)->get('result')->get('content');

                    // Cache interface returns an array of Bytes. When we are using the database to
                    // store cache the data should be converted to a Bytes object.
                    $jsontodecode = new StdClass();
                    $jsontodecode->b = $json;

                    return '' != $jsontodecode;
                } else {
                    $jsonfield = $this->jsonfield;
                    $json = com_wiris_util_json_JSon::decode($record->$jsonfield, true)->get('result')->get('alt');
                    $jsontodecode = new StdClass();
                    $jsontodecode->b = $json;

                    return '' != $jsontodecode;
                }

            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Retrieves the name of the key for the cache without the extension.
     * @key The key with a extension.
     */
    private function parse_key($key) {
        $separatedkey = explode(".", $key);
        return $separatedkey[0];
    }

    /**
     * Stores a (key, value) pair to the cache. If the key exists, updates the value.
     * @param key The key for the data being requested.
     * @param value The data to set against the key.
     * @throw Error On unexpected exception storing the value.
     */
    public function set($key, $value) {

        $jsonhash = new Hash();
        $jsonhash->set('status', 'ok');
        $innerhash = new Hash();
        $innerhash->set('content', '');
        $innerhash->set('alt', '');
        $jsonhash->set('result', $innerhash);
        $parsedkey = $this->parse_key($key);
        $bytevalue = com_wiris_system_Utf8::toBytes($value)->b;
        global $DB;
        if (!$DB->record_exists($this->cachetable, array($this->keyfield => $parsedkey))) {

            // Variable $value is an array of bytes, we need the content of the array.
            try {
                // Accesibility (alt field).
                if (strpos($key, '.txt') === false) {
                    $DB->insert_record($this->cachetable, array($this->keyfield => $parsedkey, $this->alt => $bytevalue,
                                         $this->timecreatedfield => time()));
                } else {
                    // Image (svg or base64 format).
                    $DB->insert_record($this->cachetable, array($this->keyfield => $parsedkey, $this->jsonfield => $bytevalue,
                                         $this->timecreatedfield => time()));
                }
            } catch (dml_exception $ex) {
                // Concurrent write access to the same - unexisting - md5
                // are possible in some scenarios (like a quiz)
                // if a write_exception occurs, formula has been created
                // is not a real exception.
                if ($ex instanceof dml_write_exception) {
                    return;
                }
                throw $ex;
            }

        } else { // Exists the row in db.

            $jsonfield = $this->jsonfield;
            $record = $DB->get_record($this->cachetable, array($this->keyfield => $parsedkey));

            if (strpos($key, '.txt') === false) {
                $record->$jsonfield = $bytevalue;
                $DB->update_record($this->cachetable, $record);
            } else {  // ... .txt otherwise.
                $record->alt = $bytevalue;
                $DB->update_record($this->cachetable, $record);
            }
        }
    }
}
