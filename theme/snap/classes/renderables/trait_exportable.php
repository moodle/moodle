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
 * Trait for facilitating auto exporting.
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\renderables;

use stdClass;

trait trait_exportable {

    /**
     * Makes an object suitable for exporting - converts objects to string where necessary - e.g. moodle_urls.
     * @param array|object $object
     * @return string
     */
    public function convert_object_for_export($object) {

        if (is_array($object)) {
            foreach ($object as $key => $val) {
                $object[$key] = $this->convert_object_for_export($val);
            }
            return $object;
        }

        // Get protected vars so we can exclude them from get_object_vars - note, get_object_vars can return protected
        // variables if they are in scope and we don't want them!
        $reflect = new \ReflectionClass($object);
        $props   = $reflect->getProperties(\ReflectionProperty::IS_PROTECTED);
        $protected = [];
        foreach ($props as $prop) {
            $protected[] = $prop->getName();
        }

        // Get publicly accessible vars, which also includes those that have been set manually, e.g. $myobj->test = 123;.
        $vals = get_object_vars($object);
        $public = [];
        foreach ($vals as $key => $val) {
            if (!in_array($key, $protected)) {
                $public[$key] = $val;
            }
        }

        // Convert to string if no public properties, else iterate through properties and recurse.
        if (empty($public)) {
            if ($reflect->hasMethod('__toString')) {
                return strval($object);
            }
        } else {
            foreach ($public as $key => $val) {
                if (is_array($val)) {
                    $this->convert_object_for_export($val);
                }
                if ($val instanceof \moodle_url) {
                    $object->$key = $this->convert_object_for_export($val);
                    continue;
                }
                if (is_object($val)) {
                    if ($val instanceof \renderable || get_class($val) === 'stdClass') {
                        $object->$key = $this->convert_object_for_export($val);
                    } else {
                        if (method_exists($val, '__toString')) {
                            $object->$key = strval($val);
                        } else {
                            $object->$key = $this->convert_object_for_export($val);
                        }
                    }
                }
            }
        }

        return $object;
    }

    /**
     * @param \renderer_base $output
     * @return object
     */
    public function export_for_template(\renderer_base $output) {
        $clone = clone $this;
        return $this->convert_object_for_export($clone);
    }
}
