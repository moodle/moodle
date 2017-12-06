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
 * Database column information.
 *
 * @package    core_dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Detailed database field information.
 *
 * It is based on the adodb library's ADOFieldObject object.
 * 'column' does mean 'the field' here.
 *
 * @package    core_dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @property-read string $name Name of column - lowercase.
 * @property-read string $type Driver dependent native data type. Not standardised, it's used to find meta_type.
 *
 * Max length:
 *  character type - number of characters
 *  blob - number of bytes
 *  integer - number of digits
 *  float - digits left from floating point
 *  boolean - 1
 * @property-read int    $max_length size of the database field, eg how much data can you put in there.
 *
 * @property-read int    $scale Scale of field, decimal points (float), null otherwise.
 * @property-read bool   $not_null true if the field is set to NOT NULL.
 * @property-read bool   $primary_key true if the field is the primary key. (usually 'id').
 * @property-read bool   $auto_increment True if field is autoincrementing or sequence.
 * @property-read bool   $binary True if the field is binary.
 * @property-read bool   $has_default True if the default value is defined.
 * @property-read string $default_value The default value (if defined).
 * @property-read bool   $unique True if the field values are unique, false if not.
 *
 * Standardised one character column type, uppercased and enumerated as follows:
 * R - counter (integer primary key)
 * I - integers
 * N - numbers (floats)
 * C - characters and strings
 * X - texts
 * B - binary blobs
 * L - boolean (1 bit)
 * T - timestamp - unsupported
 * D - date - unsupported
 * @property-read string $meta_type Standardised one character column type, uppercased and enumerated: R,I,N,C,X,B,L,T,D
 */
class database_column_info {

    /**
     * @var array The internal storage of column data.
     */
    protected $data;

    /**
     * Magic set function.  This is a read only object and you aren't allowed to write to any variables.
     *
     * @param string $name ignored.
     * @param mixed $value ignored.
     * @throws coding_exception You are not allowed to set data on database_column_info
     */
    public function __set($name, $value) {
        throw new coding_exception('database_column_info is a ready only object to allow for faster caching.');
    }

    /**
     * Magic get function.
     *
     * @param string $variablename variable name to return the value of.
     * @return mixed The variable contents.
     *
     * @throws coding_exception You cannot request a variable that is not allowed.
     */
    public function __get($variablename) {
        if (isset($this->data[$variablename]) || array_key_exists($variablename, $this->data)) {
            return $this->data[$variablename];
        }
        throw new coding_exception('Asked for a variable that is not available . ('.$variablename.').');
    }

    /**
     * Magic isset function.
     *
     * @param string $variablename The name of the property to test if isset().
     * @return bool Whether the value is set or not.
     */
    public function __isset($variablename) {
        return isset($this->data[$variablename]);
    }

    /**
     * Constructor
     *
     * @param mixed $data object or array with properties
     */
    public function __construct($data) {
        // Initialize all the allowed variables to null so the array key exists.
        $validelements = array('name', 'type', 'max_length', 'scale', 'not_null', 'primary_key',
                               'auto_increment', 'binary', 'has_default',  'default_value',
                               'unique', 'meta_type');
        foreach ($validelements as $element) {
            if (isset($data->$element)) {
                $this->data[$element] = $data->$element;
            } else {
                $this->data[$element] = null;
            }
        }

        switch ($this->data['meta_type']) {
            case 'R': // normalise counters (usually 'id')
                $this->data['binary']         = false;
                $this->data['has_default']    = false;
                $this->data['default_value']  = null;
                $this->data['unique']         = true;
                break;
            case 'C':
                $this->data['auto_increment'] = false;
                $this->data['binary']         = false;
                break;
        }
    }
}
