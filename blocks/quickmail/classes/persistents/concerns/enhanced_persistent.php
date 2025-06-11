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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\persistents\concerns;

defined('MOODLE_INTERNAL') || die();

use block_quickmail_string;
use \dml_missing_record_exception;
use block_quickmail\persistents\concerns\can_be_soft_deleted;

trait enhanced_persistent {
    // Custom Static Methods.
    /**
     * Creates a new persistent record with the given array
     *
     * @param  array  $data
     * @return object (persistent)
     * @throws dml_missing_record_exception
     */
    public static function create_new($data = []) {
        $model = new self(0, (object) $data);

        $model->create();

        return $model;
    }

    /**
     * Finds this persistent's record by id number or returns null
     *
     * @param  int  $id
     * @return object (persistent)|null
     * @throws dml_missing_record_exception
     */
    public static function find_or_null($id = 0) {
        // If no persistent id was passed, return null.
        if (!$id) {
            return null;
        }

        // Try to find and return model, otherwise return null.
        try {
            $model = new self($id);

            // Make sure this model has not been soft-deleted.
            if (self::supports_soft_deletes()) {
                if ($model->get('timedeleted')) {
                    return null;
                }
            }

            return $model;
        } catch (dml_missing_record_exception $e) {
            return null;
        }
    }

    /**
     * Returns a human readable date for the given model attribute
     *
     * @param  string  $attr  (must be a date attribute)
     * @return string
     */
    public function get_readable_date($attr) {
        $datetime = $this->get($attr);

        if ($datetime && is_numeric($datetime)) {
            return userdate($datetime, get_string('strftimedatetime'));
        }

        return block_quickmail_string::get('never');
    }

    /**
     * Returns a trimmed, shortened, "preview" string with appendage and default if no content
     *
     * @param  string  $attr     the model attribute to be previewed
     * @param  int     $length     number of characters to be displayed
     * @param  string  $appendage  a string to be appended if string is cut off
     * @param  string  $default    default string to be returned is no string is given
     * @return string
     */
    public function render_preview_string($attr, $length, $appendage = '...', $default = '--') {
        $string = trim($this->get($attr));

        if (!$string) {
            return $default;
        }

        if (strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = explode("\n", $string, 2);
            $string = $string[0] . $appendage;
        }

        return $string;
    }

    /**
     * Reports whether or not this persistent can be soft deleted
     *
     * @return bool
     */
    public static function supports_soft_deletes() {
        if (!$traits = class_uses(static::class)) {
            return false;
        }

        return in_array(can_be_soft_deleted::class, $traits);
    }

    /**
     * Reports whether or not this persistent has been soft deleted
     *
     * @return bool
     */
    public function is_soft_deleted() {
        return (bool) $this->get('timedeleted');
    }

}
