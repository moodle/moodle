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
 * Generic exporter to take a stdClass and prepare it for return by webservice.
 *
 * @package    core_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency\external;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use stdClass;
use renderer_base;
use context;
use context_system;
use coding_exception;
use external_single_structure;
use external_multiple_structure;
use external_value;
use external_format_value;

/**
 * Generic exporter to take a stdClass and prepare it for return by webservice, or as the context for a template.
 *
 * templatable classes implementing export_for_template, should always use a standard exporter if it exists.
 * External functions should always use a standard exporter if it exists.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class exporter {

    /** @var array $related List of related objects used to avoid DB queries. */
    protected $related = array();

    /** @var stdClass|array The data of this exporter. */
    protected $data = null;

    /**
     * Constructor - saves the persistent object, and the related objects.
     *
     * @param mixed $data - Either an stdClass or an array of values.
     * @param array $related - An optional list of pre-loaded objects related to this object.
     */
    public function __construct($data, $related = array()) {
        $this->data = $data;
        // Cache the valid related objects.
        foreach (static::define_related() as $key => $classname) {
            $isarray = false;
            $nullallowed = false;

            // Allow ? to mean null is allowed.
            if (substr($classname, -1) === '?') {
                $classname = substr($classname, 0, -1);
                $nullallowed = true;
            }

            // Allow [] to mean an array of values.
            if (substr($classname, -2) === '[]') {
                $classname = substr($classname, 0, -2);
                $isarray = true;
            }

            $missingdataerr = 'Exporter class is missing required related data: (' . get_called_class() . ') ';

            if ($nullallowed && array_key_exists($key, $related) && $related[$key] === null) {
                $this->related[$key] = $related[$key];

            } else if ($isarray) {
                if (array_key_exists($key, $related) && is_array($related[$key])) {
                    foreach ($related[$key] as $index => $value) {
                        if (!$value instanceof $classname) {
                            throw new coding_exception($missingdataerr . $key . ' => ' . $classname . '[]');
                        }
                    }
                    $this->related[$key] = $related[$key];
                } else {
                    throw new coding_exception($missingdataerr . $key . ' => ' . $classname . '[]');
                }

            } else {
                if (array_key_exists($key, $related) && $related[$key] instanceof $classname) {
                    $this->related[$key] = $related[$key];
                } else {
                    throw new coding_exception($missingdataerr . $key . ' => ' . $classname);
                }
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
        $properties = self::read_properties_definition();
        $context = $this->get_context();
        $values = (array) $this->data;

        $othervalues = $this->get_other_values($output);
        if (array_intersect_key($values, $othervalues)) {
            // Attempt to replace a standard property.
            throw new coding_exception('Cannot override a standard property value.');
        }
        $values += $othervalues;
        $record = (object) $values;

        foreach ($properties as $property => $definition) {
            if (isset($data->$property)) {
                // This happens when we have already defined the format properties.
                continue;
            } else if (!property_exists($record, $property) && array_key_exists('default', $definition)) {
                // We have a default value for this property.
                $record->$property = $definition['default'];
            } else if (!property_exists($record, $property) && !empty($definition['optional'])) {
                // Fine, this property can be omitted.
                continue;
            } else if (!property_exists($record, $property)) {
                // Whoops, we got something that wasn't defined.
                throw new coding_exception('Unexpected property ' . $property);
            }

            $data->$property = $record->$property;

            // If the field is PARAM_RAW and has a format field.
            if ($propertyformat = self::get_format_field($properties, $property)) {
                if (!property_exists($record, $propertyformat)) {
                    // Whoops, we got something that wasn't defined.
                    throw new coding_exception('Unexpected property ' . $propertyformat);
                }
                $format = $record->$propertyformat;
                list($text, $format) = external_format_text($data->$property, $format, $context->id, 'core_competency', '', 0);
                $data->$property = $text;
                $data->$propertyformat = $format;

            } else if ($definition['type'] === PARAM_TEXT) {
                if (!empty($definition['multiple'])) {
                    foreach ($data->$property as $key => $value) {
                        $data->{$property}[$key] = external_format_string($value, $context->id);
                    }
                } else {
                    $data->$property = external_format_string($data->$property, $context->id);
                }
            }
        }

        return $data;
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
        } else {
            $context = context_system::instance();
        }
        return $context;
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * These are additional generated values that are not passed in through $data
     * to the exporter. For a persistent exporter - these are generated values that
     * do not exist in the persistent class. For your convenience the format_text or
     * format_string functions do not need to be applied to PARAM_TEXT fields,
     * it will be done automatically during export.
     *
     * These values are only used when returning data via {@link self::export()},
     * they are not used when generating any of the different external structures.
     *
     * Note: These must be defined in {@link self::define_other_properties()}.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        return array();
    }

    /**
     * Get the read properties definition of this exporter. Read properties combines the
     * default properties from the model (persistent or stdClass) with the properties defined
     * by {@link self::define_other_properties()}.
     *
     * @return array Keys are the property names, and value their definition.
     */
    final public static function read_properties_definition() {
        $properties = static::properties_definition();
        $customprops = static::define_other_properties();
        foreach ($customprops as $property => $definition) {
            // Ensures that null is set to its default.
            if (!isset($definition['null'])) {
                $customprops[$property]['null'] = NULL_NOT_ALLOWED;
            }
        }
        $properties += $customprops;
        return $properties;
    }

    /**
     * Get the properties definition of this exporter used for create, and update structures.
     * The read structures are returned by: {@link self::read_properties_definition()}.
     *
     * @return array Keys are the property names, and value their definition.
     */
    final public static function properties_definition() {
        $properties = static::define_properties();
        foreach ($properties as $property => $definition) {
            // Ensures that null is set to its default.
            if (!isset($definition['null'])) {
                $properties[$property]['null'] = NULL_NOT_ALLOWED;
            }
        }
        return $properties;
    }

    /**
     * Return the list of additional properties used only for display.
     *
     * Additional properties are only ever used for the read structure, and during
     * export of the persistent data.
     *
     * The format of the array returned by this method has to match the structure
     * defined in {@link \core_competency\persistent::define_properties()}. The display properties
     * can however do some more fancy things. They can define 'multiple' => true to wrap
     * values in an external_multiple_structure automatically - or they can define the
     * type as a nested array of more properties in order to generate a nested
     * external_single_structure.
     *
     * You can specify an array of values by including a 'multiple' => true array value. This
     * will result in a nested external_multiple_structure.
     * E.g.
     *
     *       'arrayofbools' => array(
     *           'type' => PARAM_BOOL,
     *           'multiple' => true
     *       ),
     *
     * You can return a nested array in the type field, which will result in a nested external_single_structure.
     * E.g.
     *      'competency' => array(
     *          'type' => competency_exporter::read_properties_definition()
     *       ),
     *
     * Other properties can be specifically marked as optional, in which case they do not need
     * to be included in the export in {@link self::get_other_values()}. This is useful when exporting
     * a substructure which cannot be set as null due to webservices protocol constraints.
     * E.g.
     *      'competency' => array(
     *          'type' => competency_exporter::read_properties_definition(),
     *          'optional' => true
     *       ),
     *
     * @return array
     */
    protected static function define_other_properties() {
        return array();
    }

    /**
     * Return the list of properties.
     *
     * The format of the array returned by this method has to match the structure
     * defined in {@link \core_competency\persistent::define_properties()}.
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
     * The class name can be suffixed:
     * - with [] to indicate an array of values.
     * - with ? to indicate that 'null' is allowed.
     *
     * @return array of 'propertyname' => array('type' => classname, 'required' => true)
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
        if (($definitions[$property]['type'] == PARAM_RAW || $definitions[$property]['type'] == PARAM_CLEANHTML)
                && isset($definitions[$formatproperty])
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
        $properties = self::properties_definition();
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
        $properties = self::read_properties_definition();

        return self::get_read_structure_from_properties($properties);
    }

    /**
     * Returns the read structure from a set of properties (recursive).
     *
     * @param array $properties The properties.
     * @param int $required Whether is required.
     * @param mixed $default The default value.
     * @return external_single_structure
     */
    final protected static function get_read_structure_from_properties($properties, $required = VALUE_REQUIRED, $default = null) {
        $returns = array();
        foreach ($properties as $property => $definition) {
            if (isset($returns[$property]) && substr($property, -6) === 'format') {
                // We've already treated the format.
                continue;
            }
            $thisvalue = null;

            $type = $definition['type'];
            $proprequired = VALUE_REQUIRED;
            $propdefault = null;
            if (array_key_exists('default', $definition)) {
                $propdefault = $definition['default'];
            }
            if (array_key_exists('optional', $definition)) {
                // Mark as optional. Note that this should only apply to "reading" "other" properties.
                $proprequired = VALUE_OPTIONAL;
            }

            if (is_array($type)) {
                // This is a nested array of more properties.
                $thisvalue = self::get_read_structure_from_properties($type, $proprequired, $propdefault);
            } else {
                if ($definition['type'] == PARAM_TEXT || $definition['type'] == PARAM_CLEANHTML) {
                    // PARAM_TEXT always becomes PARAM_RAW because filters may be applied.
                    $type = PARAM_RAW;
                }
                $thisvalue = new external_value($type, $property, $proprequired, $propdefault, $definition['null']);
            }
            if (!empty($definition['multiple'])) {
                $returns[$property] = new external_multiple_structure($thisvalue, '', $proprequired, $propdefault);
            } else {
                $returns[$property] = $thisvalue;

                // Magically treat the format properties (not possible for arrays).
                if ($formatproperty = self::get_format_field($properties, $property)) {
                    if (isset($returns[$formatproperty])) {
                        throw new coding_exception('The format for \'' . $property . '\' is already defined.');
                    }
                    $returns[$formatproperty] = self::get_format_structure($property, $properties[$formatproperty]);
                }
            }
        }

        return new external_single_structure($returns, '', $required, $default);
    }

    /**
     * Returns the update structure.
     *
     * This structure can never be included at the top level for an external function signature
     * because it contains optional parameters.
     *
     * @return external_single_structure
     */
    final public static function get_update_structure() {
        $properties = self::properties_definition();
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
