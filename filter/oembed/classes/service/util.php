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
 * General utility class
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace filter_oembed\service;

use external_value;
use coding_exception;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/externallib.php');

/**
 * General utility class.
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class util {
    /**
     * Returns an array of \external_values based on a class or object for use with defining a webservice.
     *
     * NOTE: Current limitations - does not recurse to properties that are object instances or arrays.
     *
     * @param $classorobject
     * @throws coding_exception
     * @return external_value[]
     */
    public static function define_class_for_webservice($classorobject) {
        $reflect = new \ReflectionClass($classorobject);

        $public = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        $singlemembers = [];
        foreach ($public as $property) {
            $name = $property->getName();
            $comment = $property->getDocComment();
            $regex = '/(?<=\*\s@wsparam\s)(\S*)\s(.*)/';
            $matches = [];
            $haswsparamdoc = preg_match($regex, $comment, $matches);
            if ($haswsparamdoc === 1) {
                if (!defined($matches[1])) {
                    throw new coding_exception('Unknown / incompatible var type '.$matches[1].' for '.$name);
                }
                if (count($matches) < 3) {
                    throw new coding_exception('Missing description for '.$name);
                }
                $description = $matches[2];
                $type = constant($matches[1]);
            } else {
                $regex = '/(?<=\*\s@var\s)(\S*)\s(.*)/';
                $matches = [];
                $aliases = [
                    'bool' => PARAM_BOOL,
                    'str' => PARAM_RAW,
                    'string' => PARAM_RAW,
                    'int' => PARAM_INT,
                    'integer' => PARAM_INT
                ];
                $hasvardoc = preg_match($regex, $comment, $matches);
                if ($hasvardoc !== 1) {
                    throw new coding_exception('Property without @var or @wsparam doc');
                }
                if (count($matches) < 3) {
                    throw new coding_exception('Missing description for '.$name);
                }
                $description = $matches[2];
                $type = $matches[1];
                if (isset($aliases[$type])) {
                    $type = $aliases[$type];
                } else {
                    throw new coding_exception('Unknown / incompatible var type '.$type.' for '.$name);
                }
            }

            $regex = '/(?<=\*\s@wsrequired\s)/';
            $required = preg_match($regex, $comment, $matches);

            $singlemembers[$name] = new external_value($type, $description, $required);

        }

        return $singlemembers;
    }
}
