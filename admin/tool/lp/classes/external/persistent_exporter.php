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
 * Abstract class for tool_lp objects saved to the DB.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;

require_once($CFG->libdir . '/externallib.php');

use stdClass;
use renderer_base;
use context;
use context_system;
use coding_exception;
use external_single_structure;
use external_value;
use external_format_value;

/**
 * An extended version of the persistent class with a default implementation of export
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class persistent_exporter {

    /** @var \tool_lp\persistent The persistent object we will export. */
    var $persistent = null;

    /** @var array $related List of related objects used to avoid DB queries. */
    var $related = array();

    /**
     * Returns a list of objects that are related to this persistent. Only objects listed here
     * will be cached in this object.
     *
     * @return array of 'propertyname' => classname
     */
    protected static function get_related() {
        return array();
    }

    /**
     * Returns the specific class the persistent should be an instance of.
     *
     * @return string
     */
    protected static function get_persistent_class() {
        throw new coding_exception('get_persistent_class() must be overidden.');
    }

    /**
     * Constructor - saves the persistent object, and the related objects.
     *
     * @param \tool_lp\persistent $persistent The persistent object to export.
     * @param array $related - An optional list of pre-loaded objects related to this persistent.
     */
    function __construct(\tool_lp\persistent $persistent, $related = array()) {
        $classname = static::get_persistent_class();
        if (!$persistent instanceof $classname) {
            throw new coding_exception('Invalid type for persistent. ' .
                                       'Expected: ' . static::get_persistent_class() . ' got: ' . get_class($persistent));
        }
        $this->persistent = $persistent;

        // Cache the valid related objects.
        foreach (static::get_related() as $key => $classname) {
            if (isset($related[$key]) && ($related[$key] instanceof $classname)) {
                $this->related[$key] = $related[$key];
            } else {
                throw new coding_exception('Exporter class is missing required related data: ' . $key . ' => ' . $classname);
            }
        }
    }

    /**
     * Function to guess the correct context, falling back to system context.
     *
     * @return context
     */
    protected function get_context() {
        $context = null;
        if (isset($this->related['context']) && $this->related['context'] instanceof context) {
            $context = $this->related['context'];
        } else if (method_exists($this->persistent, 'get_context')) {
            $context = $this->persistent->get_context();
        } else {
            $context = context_system::instance();
        }
        return $context;
    }

    /**
     * Function to export the renderer data in a format that is suitable for a
     * mustache template. This means raw records are generated as in to_record,
     * but all strings are correctly passed through external_format_text (or external_format_string).
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export(renderer_base $output) {
        $data = new stdClass();
        $properties = $this->persistent->properties_definition();
        $context = $this->get_context();
        $record = $this->persistent->to_record();

        foreach ($properties as $property => $definition) {
            if (!isset($data->$property)) {
                $data->$property = $record->$property;
                if ($definition['type'] === PARAM_TEXT) {
                    $propertyformat = $property . 'format';

                    if (isset($properties[$propertyformat]) && $properties[$propertyformat]['type'] == PARAM_INT) {
                        $format = $record->$propertyformat;
                        list($text, $format) = external_format_text($data->$property, $format, $context->id, 'tool_lp', '', 0);
                        $data->$property = $text;
                        $data->$propertyformat = $format;
                    } else {
                        $data->$property = external_format_string($data->$property, $context->id);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Modify the list of fields exported for 'read'. This is used when we return additional 'virtual' fields
     * that are useful for display, but are not part of the persistent definition.
     *
     * @param array $fields - The standard list of fields for this persistent.
     * @return array The modified list of fields, passed to new external_single_structure.
     */
    public static function export_read_properties_structure($fields) {
        return $fields;
    }

    /**
     * Function to export the structure of the persistent, so it can be re-used in all
     * external function params/returns definitions.
     *
     * @param string $for - One of 'create', 'update' or 'read' - There are different structures for each.
     * @return external_single_structure
     */
    public static function export_structure($for) {
        $classname = static::get_persistent_class();
        $properties = $classname::properties_definition();
        $returns = array();

        if (!in_array($for, array('create', 'update', 'read'))) {
            throw new coding_exception('First parameter for persistent_exporter::export_structure must ' .
                                       'be one of "create", "update" or "read".');
        }

        foreach ($properties as $property => $definition) {
            $required = VALUE_REQUIRED;
            if ($for == 'update') {
                $required = VALUE_OPTIONAL;
            }
            $default = null;
            $nullallowed = NULL_NOT_ALLOWED;
            // We cannot use isset here because we want to detect nulls.
            if ($for == 'create' && array_key_exists('default', $definition)) {
                $required = VALUE_DEFAULT;
                $default = $definition['default'];
            }
            if (!empty($definition['null'])) {
                $nullallowed = NULL_ALLOWED;
            }
            if ($property == 'id') {
                if ($for == 'create') {
                    continue;
                } else {
                    $required = VALUE_REQUIRED;
                }
            }
            if ($property == 'contextid') {
                if ($for == 'create') {
                    $returns['contextid'] = new external_value(PARAM_INT, 'The context id', VALUE_OPTIONAL);
                    $returns['contextlevel'] = new external_value(PARAM_ALPHA, 'The context level', VALUE_OPTIONAL);
                    $returns['instanceid'] = new external_value(PARAM_INT, 'The Instance id', VALUE_OPTIONAL);
                } else {
                    $returns['contextid'] = new external_value(PARAM_INT, 'The context id', VALUE_OPTIONAL);
                }
            } else {
                if ($definition['type'] == PARAM_FORMAT) {
                    $returns[$property] = new external_format_value($property, $required, $default, $nullallowed);
                } else {
                    $returns[$property] = new external_value($definition['type'], $property, $required, $default, $nullallowed);
                }
            }
        }
        if ($for == 'read') {
            $returns = static::export_read_properties_structure($returns);
        }
        return new external_single_structure($returns);
    }
}
