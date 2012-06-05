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
 */
class database_column_info {
    /**
     * Name of column - lowercase.
     * @var string
     */
    public $name;

    /**
     * Driver dependent native data type.
     * Not standardised, its used to find meta_type.
     * @var string
     */
    public $type;

    /**
     * Max length:
     *  character type - number of characters
     *  blob - number of bytes
     *  integer - number of digits
     *  float - digits left from floating point
     *  boolean - 1
     * @var int
     */
    public $max_length;

    /**
     * Scale
     * float - decimal points
     * other - null
     * @var int
     */
    public $scale;

    /**
     * True if not null, false otherwise
     * @var bool
     */
    public $not_null;

    /**
     * True if column is primary key.
     * (usually 'id').
     * @var bool
     */
    public $primary_key;

    /**
     * True if filed autoincrementing
     * (usually 'id' only)
     * @var bool
     */
    public $auto_increment;

    /**
     * True if binary
     * @var bool
     */
    public $binary;

    /**
     * True if integer unsigned, false if signed.
     * Null for other types
     * @var integer
     * @deprecated since 2.3
     */
    public $unsigned;

    /**
     * True if the default value is defined.
     * @var bool
     */
    public $has_default;

    /**
     * The default value (if defined).
     * @var string
     */
    public $default_value;

    /**
     * True if field values are unique, false if not.
     * @var bool
     */
    public $unique;

    /**
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
     * @var string
     */
    public $meta_type;

    /**
     * Constructor
     * @param mixed $data object or array with properties
     */
    public function __construct($data) {
        foreach ($data as $key=>$value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        switch ($this->meta_type) {
            case 'R': // normalise counters (usually 'id')
                $this->binary         = false;
                $this->has_default    = false;
                $this->default_value  = null;
                $this->unique         = true;
                break;
            case 'C':
                $this->auto_increment = false;
                $this->binary         = false;
                break;
        }
    }
}
