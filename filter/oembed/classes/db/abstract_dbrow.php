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
 * Base class for classes which map to db tables.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2016 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_oembed\db;
use stdClass;

defined('MOODLE_INTERNAL') || die();

class abstract_dbrow {

    /**
     * anstract_dbrow constructor.
     * @param stdClass $row
     */
    public function __construct($row) {

        if (!$row) {
            throw new \coding_exception('$row does not exist');
        }

        if (!$row instanceof stdClass) {
            throw new \coding_exception('$row must be an instance of std class', var_export($row, true));
        }

        $vars = array_keys(get_object_vars($this));

        foreach ($row as $key => $val) {
            if (!in_array($key, $vars)) {
                throw new \coding_exception('Row model '.get_class($this).' is missing key '.$key);
            }
            $this->$key = $val;
        }
    }
}
