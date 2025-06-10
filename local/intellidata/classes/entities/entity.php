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
 * Abstract class for IntelliData entities.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities;

use stdClass;
use lang_string;
use coding_exception;
use core\invalid_persistent_exception;
use local_intellidata\helpers\EventsHelper;

/**
 * Abstract class for core objects saved to the DB.
 *
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 */
abstract class entity {

    /** The entity type name. */
    const TYPE = null;

    /** @var array The model data. */
    private $data = [];

    /** @var array The fields to return. */
    protected $returnfields = [];

    /** @var array The list of validation errors. */
    private $errors = [];

    /** @var bool If the data was already validated. */
    private $validated = false;

    /** @var array The list of fields. */
    private $fields = [];

    /**
     * Entity constructor.
     *
     * @param $record
     * @param $returnfields
     */
    public function __construct($record = null, $returnfields = []) {
        if (count($returnfields)) {
            $this->returnfields = $returnfields;
        }
        if ($record) {
            static::set_values($record);
        }
    }

    /**
     * Insert Values from record.
     *
     * @return \stdClass
     */
    final public function set_values($record) {
        foreach ($record as $key => $value) {
            $this->data[$key] = $value;
        }
    }

    /**
     * Data setter.
     *
     * This is the main setter for all the properties. Developers can implement their own setters (set_propertyname)
     * and they will be called by this function. Custom setters should call internal_set() to finally set the value.
     * Internally this is not used {@link self::to_record()} or
     * {@link self::from_record()} because the data is not expected to be validated or changed when reading/writing
     * raw records from the DB.
     *
     * @param  string $property The property name.
     * @return $this
     */
    final public function set($property, $value) {
        return $this->raw_set($property, $value);
    }

    /**
     * Data getter.
     *
     * This is the main getter for all the properties. Developers can implement their own getters (get_propertyname)
     * and they will be called by this function. Custom getters can use raw_get to get the raw value.
     * Internally this is not used by {@link self::to_record()} or
     * {@link self::from_record()} because the data is not expected to be validated or changed when reading/writing
     * raw records from the DB.
     *
     * @param  string $property The property name.
     * @return mixed
     */
    final public function get($property) {
        return $this->raw_get($property);
    }

    /**
     * Get crud.
     *
     * @return mixed|string|null
     * @throws coding_exception
     */
    final public function get_crud() {
        if ($this->raw_get('crud')) {
            return $this->raw_get('crud');
        }

        return EventsHelper::CRUD_CREATED;
    }

    /**
     * Internal Data getter.
     *
     * This is the main getter for all the properties. Developers can implement their own getters
     * but they should be calling {@link self::get()} in order to retrieve the value. Essentially
     * the getters defined by the developers would only ever be used as helper methods and will not
     * be called internally at this stage. In other words, do not expect {@link self::to_record()} or
     * {@link self::from_record()} to use them.
     *
     * This is protected because it is only for raw low level access to the data fields.
     * Note this function is named raw_get and not get_raw to avoid naming clashes with a property named raw.
     *
     * @param  string $property The property name.
     * @return mixed
     */
    final protected function raw_get($property) {
        return isset($this->data[$property]) ? $this->data[$property] : null;
    }

    /**
     * Data setter.
     *
     * This is the main setter for all the properties. Developers can implement their own setters
     * but they should always be calling {@link self::set()} in order to set the value. Essentially
     * the setters defined by the developers are helper methods and will not be called internally
     * at this stage. In other words do not expect {@link self::to_record()} or
     * {@link self::from_record()} to use them.
     *
     * This is protected because it is only for raw low level access to the data fields.
     *
     * @param  string $property The property name.
     * @param  mixed $value The value.
     * @return $this
     */
    final protected function raw_set($property, $value) {

        $this->data[$property] = $value;

        return $this;
    }

    /**
     * Return the custom definition of the properties of this model.
     *
     * Each property MUST be listed here.
     *
     * The result of this method is cached internally for the whole request.
     *
     * The 'default' value can be a Closure when its value may change during a single request.
     * For example if the default value is based on a $CFG property, then it should be wrapped in a closure
     * to avoid running into scenarios where the true value of $CFG is not reflected in the definition.
     * Do not abuse closures as they obviously add some overhead.
     *
     * Examples:
     *
     * array(
     *     'property_name' => array(
     *         'default' => 'Default value',        // When not set, the property is considered as required.
     *         'message' => new lang_string(...),   // Defaults to invalid data error message.
     *         'null' => NULL_ALLOWED,              // Defaults to NULL_NOT_ALLOWED. Takes NULL_NOW_ALLOWED or NULL_ALLOWED.
     *         'type' => PARAM_TYPE,                // Mandatory.
     *         'choices' => array(1, 2, 3)          // An array of accepted values.
     *     )
     * )
     *
     * array(
     *     'dynamic_property_name' => array(
     *         'default' => function() {
     *             return $CFG->something;
     *         },
     *         'type' => PARAM_INT,
     *     )
     * )
     *
     * @return array Where keys are the property names.
     */
    protected static function define_properties() {
        return [];
    }

    /**
     * Get the properties definition of this model..
     *
     * @return array
     */
    final public static function properties_definition($returnfields = []) {
        global $CFG;

        $def = static::define_properties();

        // List of reserved property names. Mostly because we have methods (getters/setters) which would confict with them.
        // Think about backwards compability before adding new ones here!
        $reserved = ['errors', 'formatted_properties', 'property_default_value', 'property_error_message'];

        foreach ($def as $property => $definition) {

            // Include only return fields.
            if (count($returnfields) && !in_array($property, $returnfields)) {
                unset($def[$property]);
                continue;
            }

            // Ensures that the null property is always set.
            if (!array_key_exists('null', $definition)) {
                $def[$property]['null'] = NULL_NOT_ALLOWED;
            }

            // Warn the developers when they are doing something wrong.
            if ($CFG->debugdeveloper) {
                if (!array_key_exists('type', $definition)) {
                    throw new coding_exception('Missing type for: ' . $property);
                } else if (isset($definition['message']) && !($definition['message'] instanceof lang_string)) {
                    throw new coding_exception('Invalid error message for: ' . $property);
                } else if (in_array($property, $reserved)) {
                    throw new coding_exception('This property cannot be defined: ' . $property);
                }
            }
        }

        $def['recordtimecreated'] = [
            'default' => 0,
            'type' => PARAM_FLOAT,
            'description' => 'Timestamp when record created.',
            'null' => NULL_NOT_ALLOWED,
        ];
        $def['recordusermodified'] = [
            'default' => 0,
            'type' => PARAM_INT,
            'description' => 'User ID who is related to the record.',
            'null' => NULL_NOT_ALLOWED,
        ];
        $def['crud'] = [
            'default' => EventsHelper::CRUD_CREATED,
            'type' => PARAM_TEXT,
            'description' => 'Record CRUD.',
            'null' => NULL_ALLOWED,
        ];

        return $def;
    }

    /**
     * Populate this class with data from a DB record.
     *
     * Note that this does not use any custom setter because the data here is intended to
     * represent what is stored in the database.
     *
     * @param \stdClass $record A DB record.
     * @return persistent
     */
    final public function from_record(stdClass $record) {
        $record = (array) $record;
        foreach ($record as $property => $value) {
            $this->raw_set($property, $value);
        }
        return $this;
    }

    /**
     * Create a DB record from this class.
     *
     * Note that this does not use any custom getter because the data here is intended to
     * represent what is stored in the database.
     *
     * @return \stdClass
     */
    final public function to_record() {
        $record = [];

        if (empty($this->fields)) {
            $this->fields = static::properties_definition($this->returnfields);
        }

        foreach ($this->fields as $property => $definition) {
            if (array_key_exists($property, $this->data)) {
                $record[$property] = $this->data[$property];
            } else {
                $record[$property] = null;
            }
        }

        return (object)$record;
    }

    /**
     * Hook to execute before an export.
     *
     * Please note that at this stage the data has already been validated and therefore
     * any new data being set will not be validated before it is sent to the database.
     *
     * This is only intended to be used by child classes, do not put any logic here!
     *
     * @return void
     */
    protected function before_export() {
    }

    /**
     * Hook to execute after an export.
     *
     * @return void
     */
    public function after_export($record) {
        return $record;
    }

    /**
     * Data type parameters changed by version.
     *
     * @param array $datatypeparams
     * @return array
     */
    public static function change_parameters_by_version($datatypeparams) {
        return $datatypeparams;
    }

    /**
     * Insert a record in the DB.
     *
     * @return persistent
     */
    final public function export() {
        global $USER;

        // Before create hook.
        $this->before_export();

        // We can safely set those values bypassing the validation because we know what we're doing.
        $now = microtime(true);
        $this->raw_set('recordtimecreated', $now);
        $this->raw_set('recordusermodified', $USER->id);
        $this->raw_set('crud', $this->get_crud());

        return $this->to_record();
    }

    /**
     * Export data with after_export() action.
     *
     * @return null
     * @throws invalid_persistent_exception
     */
    final public function export_data() {
        return $this->after_export($this->export());
    }

    /**
     * Prepare entity data for export.
     *
     * @param \stdClass $object
     * @param array $fields
     * @param string $table
     *
     * @return \stdClass
     * @throws invalid_persistent_exception
     */
    public static function prepare_export_data($object, $fields = [], $table = '') {
        return $object;
    }

    /**
     * Hook to execute before the validation.
     *
     * This hook will not affect the validation results in any way but is useful to
     * internally set properties which will need to be validated.
     *
     * This is only intended to be used by child classes, do not put any logic here!
     *
     * @return void
     */
    protected function before_validate() {
    }
}
