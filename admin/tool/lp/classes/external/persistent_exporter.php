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
    protected $persistent = null;

    /** @var array $related List of related objects used to avoid DB queries. */
    protected $related = array();

    /**
     * Constructor - saves the persistent object, and the related objects.
     *
     * @param \tool_lp\persistent $persistent The persistent object to export.
     * @param array $related - An optional list of pre-loaded objects related to this persistent.
     */
    public final function __construct(\tool_lp\persistent $persistent, $related = array()) {
        $classname = static::define_class();
        if (!$persistent instanceof $classname) {
            throw new coding_exception('Invalid type for persistent. ' .
                                       'Expected: ' . $classname . ' got: ' . get_class($persistent));
        }
        $this->persistent = $persistent;

        // Cache the valid related objects.
        foreach (static::define_related() as $key => $classname) {
            if (isset($related[$key]) && ($related[$key] instanceof $classname)) {
                $this->related[$key] = $related[$key];
            } else {
                throw new coding_exception('Exporter class is missing required related data: ' . $key . ' => ' . $classname);
            }
        }
    }

    /**
     * Function to export the renderer data in a format that is suitable for a
     * mustache template. This means raw records are generated as in to_record,
     * but all strings are correctly passed through external_format_text (or external_format_string).
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass
     */
    final public function export(renderer_base $output) {
        $data = new stdClass();
        $properties = self::properties_definition(true);
        $context = $this->get_context();
        $values = (array) $this->persistent->to_record();
        $values += $this->get_values($output);
        $record = (object) $values;

        foreach ($properties as $property => $definition) {
            if (isset($data->$property)) {
                // This happens when we have already defined the format properties.
                continue;
            } else if (!property_exists($record, $property)) {
                // Whoops, we got something that wasn't defined.
                throw new coding_exception('Unexpected property ' . $property);
            }

            $data->$property = $record->$property;

            // If the field is PARAM_TEXT and has a format field.
            if ($propertyformat = self::get_format_field($properties, $property)) {
                $format = $record->$propertyformat;
                list($text, $format) = external_format_text($data->$property, $format, $context->id, 'tool_lp', '', 0);
                $data->$property = $text;
                $data->$propertyformat = $format;

            // If it's a PARAM_TEXT without format field.
            } else if ($definition['type'] === PARAM_TEXT) {
                $data->$property = external_format_string($data->$property, $context->id);
            }
        }

        return $data;
    }

    /**
     * Function to guess the correct context, falling back to system context.
     *
     * @return context
     */
    final protected function get_context() {
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
     * Get the additional values to inject while exporting.
     *
     * This should be overriden by child classes if needed. Existing persistent
     * properties cannot be overridden. For your convenience the format_text or
     * format_string functions do not need to be applied to PARAM_TEXT fields,
     * it will be done automatically during export.
     *
     * Note: These must be defined in {@link self::define_properties()}.
     *
     * @return array Keys are the property names, values are their values.
     */
    protected function get_values(renderer_base $output) {
        return array();
    }

    /**
     * Get the properties definition of this exporter.
     *
     * @param bool $additional Whether or not to include the additional properties.
     * @return array Keys are the property names, and value their definition.
     */
    final public static function properties_definition($additional = false) {
        $classname = static::define_class();
        $properties = $classname::properties_definition();
        if ($additional) {
            $customprops = static::define_properties();
            foreach ($customprops as $property => $definition) {
                // Ensures that null is set to its default.
                if (!isset($definition['null'])) {
                    $customprops[$property]['null'] = NULL_NOT_ALLOWED;
                }
            }
            $properties += $customprops;
        }
        return $properties;
    }

    /**
     * Returns the specific class the persistent should be an instance of.
     *
     * @return string
     */
    protected static function define_class() {
        throw new coding_exception('define_class() must be overidden.');
    }

    /**
     * Return the list of additional properties.
     *
     * Additional properties are only ever used for the read structure, and during
     * export of the persistent data.
     *
     * The format of the array returned by this method has to match the structure
     * defined in {@link \tool_lp\persistent::define_properties()}.
     *
     * @return array
     */
    protected static function define_properties() {
        return array();
    }

    /**
     * Returns a list of objects that are related to this persistent.
     *
     * Only objects listed here can be cached in this object.
     *
     * @return array of 'propertyname' => classname
     */
    protected static function define_related() {
        return array();
    }

    /**
     * Get the context structure.
     *
     * @return external_single_structure
     */
    final protected static function get_context_structure() {
        return array(
            'contextid' => new external_value(PARAM_INT, 'The context id', VALUE_OPTIONAL),
            'contextlevel' => new external_value(PARAM_ALPHA, 'The context level', VALUE_OPTIONAL),
            'instanceid' => new external_value(PARAM_INT, 'The Instance id', VALUE_OPTIONAL),
        );
    }

    /**
     * Get the format field name.
     *
     * @param  array $definitions List of properties definitions.
     * @param  string $property The name of the property that may have a format field.
     * @return bool|string False, or the name of the format property.
     */
    final protected static function get_format_field($definitions, $property) {
        $formatproperty = $property . 'format';
        if ($definitions[$property]['type'] == PARAM_TEXT && isset($definitions[$formatproperty])
                && $definitions[$formatproperty]['type'] == PARAM_INT) {
            return $formatproperty;
        }
        return false;
    }

    /**
     * Get the format structure.
     *
     * @param  string $property   The name of the property on which the format applies.
     * @param  array  $definition The definition of the format property.
     * @param  int    $required   Constant VALUE_*.
     * @return external_format_value
     */
    final protected static function get_format_structure($property, $definition, $required = VALUE_REQUIRED) {
        if (array_key_exists('default', $definition)) {
            $required = VALUE_DEFAULT;
        }
        return new external_format_value($property, $required);
    }

    /**
     * Returns the create structure.
     *
     * @return external_single_structure
     */
    final public static function get_create_structure() {
        $properties = self::properties_definition(false);
        $returns = array();

        foreach ($properties as $property => $definition) {
            if ($property == 'id') {
                // The can not be set on create.
                continue;

            } else if (isset($returns[$property]) && substr($property, -6) === 'format') {
                // We've already treated the format.
                continue;
            }

            $required = VALUE_REQUIRED;
            $default = null;

            // We cannot use isset here because we want to detect nulls.
            if (array_key_exists('default', $definition)) {
                $required = VALUE_DEFAULT;
                $default = $definition['default'];
            }

            // Magically treat the contextid fields.
            if ($property == 'contextid') {
                if (isset($properties['context'])) {
                    throw new coding_exception('There cannot be a context and a contextid column');
                }
                $returns += self::get_context_structure();

            } else {
                $returns[$property] = new external_value($definition['type'], $property, $required, $default, $definition['null']);

                // Magically treat the format properties.
                if ($formatproperty = self::get_format_field($properties, $property)) {
                    if (isset($returns[$formatproperty])) {
                        throw new coding_exception('The format for \'' . $property . '\' is already defined.');
                    }
                    $returns[$formatproperty] = self::get_format_structure($property,
                        $properties[$formatproperty], VALUE_REQUIRED);
                }
            }
        }

        return new external_single_structure($returns);
    }

    /**
     * Returns the read structure.
     *
     * @return external_single_structure
     */
    final public static function get_read_structure() {
        $properties = self::properties_definition(true);
        $returns = array();

        foreach ($properties as $property => $definition) {
            if (isset($returns[$property]) && substr($property, -6) === 'format') {
                // We've already treated the format.
                continue;
            }

            $type = $definition['type'];
            if ($definition['type'] == PARAM_TEXT) {
                // PARAM_TEXT always becomes PARAM_RAW because filters may be applied.
                $type = PARAM_RAW;
            }
            $returns[$property] = new external_value($type, $property);

            // Magically treat the format properties.
            if ($formatproperty = self::get_format_field($properties, $property)) {
                if (isset($returns[$formatproperty])) {
                    throw new coding_exception('The format for \'' . $property . '\' is already defined.');
                }
                $returns[$formatproperty] = self::get_format_structure($property, $properties[$formatproperty]);
            }
        }

        return new external_single_structure($returns);
    }

    /**
     * Returns the update structure.
     *
     * @return external_single_structure
     */
    final public static function get_update_structure() {
        $properties = self::properties_definition(false);
        $returns = array();

        foreach ($properties as $property => $definition) {
            if (isset($returns[$property]) && substr($property, -6) === 'format') {
                // We've already treated the format.
                continue;
            }

            $default = null;
            $required = VALUE_OPTIONAL;
            if ($property == 'id') {
                $required = VALUE_REQUIRED;
            }

            // Magically treat the contextid fields.
            if ($property == 'contextid') {
                if (isset($properties['context'])) {
                    throw new coding_exception('There cannot be a context and a contextid column');
                }
                $returns += self::get_context_structure();

            } else {
                $returns[$property] = new external_value($definition['type'], $property, $required, $default, $definition['null']);

                // Magically treat the format properties.
                if ($formatproperty = self::get_format_field($properties, $property)) {
                    if (isset($returns[$formatproperty])) {
                        throw new coding_exception('The format for \'' . $property . '\' is already defined.');
                    }
                    $returns[$formatproperty] = self::get_format_structure($property,
                        $properties[$formatproperty], VALUE_OPTIONAL);
                }
            }
        }

        return new external_single_structure($returns);
    }

}
