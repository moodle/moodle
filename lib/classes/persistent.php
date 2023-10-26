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
 * Abstract class for objects saved to the DB.
 *
 * @package    core
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use invalid_parameter_exception;
use lang_string;
use ReflectionMethod;
use stdClass;
use renderer_base;

/**
 * Abstract class for core objects saved to the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class persistent {

    /** The table name. */
    const TABLE = null;

    /** @var array The model data. */
    private $data = array();

    /** @var array The list of validation errors. */
    private $errors = array();

    /** @var boolean If the data was already validated. */
    private $validated = false;

    /**
     * Create an instance of this class.
     *
     * @param int $id If set, this is the id of an existing record, used to load the data.
     * @param stdClass $record If set will be passed to {@link self::from_record()}.
     */
    public function __construct($id = 0, stdClass $record = null) {
        global $CFG;

        if ($id > 0) {
            $this->raw_set('id', $id);
            $this->read();
        }
        if (!empty($record)) {
            $this->from_record($record);
        }
        if ($CFG->debugdeveloper) {
            $this->verify_protected_methods();
        }
    }

    /**
     * This function is used to verify that custom getters and setters are declared as protected.
     *
     * Persistent properties should always be accessed via get('property') and set('property', 'value') which
     * will call the custom getter or setter if it exists. We do not want to allow inconsistent access to the properties.
     */
    final protected function verify_protected_methods() {
        $properties = static::properties_definition();

        foreach ($properties as $property => $definition) {
            $method = 'get_' . $property;
            if (method_exists($this, $method)) {
                $reflection = new ReflectionMethod($this, $method);
                if (!$reflection->isProtected()) {
                    throw new coding_exception('The method ' . get_class($this) . '::'. $method . ' should be protected.');
                }
            }
            $method = 'set_' . $property;
            if (method_exists($this, $method)) {
                $reflection = new ReflectionMethod($this, $method);
                if (!$reflection->isProtected()) {
                    throw new coding_exception('The method ' . get_class($this) . '::'. $method . ' should be protected.');
                }
            }
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
     *
     * @throws coding_exception
     */
    final public function set($property, $value) {
        if (!static::has_property($property)) {
            throw new coding_exception('Unexpected property \'' . s($property) .'\' requested.');
        }
        $methodname = 'set_' . $property;
        if (method_exists($this, $methodname)) {
            $this->$methodname($value);
            return $this;
        }
        return $this->raw_set($property, $value);
    }

    /**
     * Data setter for multiple properties
     *
     * Internally calls {@see set} on each property
     *
     * @param array $values Array of property => value elements
     * @return $this
     */
    final public function set_many(array $values): self {
        foreach ($values as $property => $value) {
            $this->set($property, $value);
        }
        return $this;
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
        if (!static::has_property($property)) {
            throw new coding_exception('Unexpected property \'' . s($property) .'\' requested.');
        }
        $methodname = 'get_' . $property;
        if (method_exists($this, $methodname)) {
            return $this->$methodname();
        }

        $properties = static::properties_definition();
        // If property can be NULL and value is NULL it needs to return null.
        if ($properties[$property]['null'] === NULL_ALLOWED && $this->raw_get($property) === null) {
            return null;
        }
        // Deliberately cast boolean types as such, because clean_param will cast them to integer.
        if ($properties[$property]['type'] === PARAM_BOOL) {
            return (bool)$this->raw_get($property);
        }

        return clean_param($this->raw_get($property), $properties[$property]['type']);
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
        if (!static::has_property($property)) {
            throw new coding_exception('Unexpected property \'' . s($property) .'\' requested.');
        }
        if (!array_key_exists($property, $this->data) && !static::is_property_required($property)) {
            $this->raw_set($property, static::get_property_default_value($property));
        }
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
        if (!static::has_property($property)) {
            throw new coding_exception('Unexpected property \'' . s($property) .'\' requested.');
        }
        if (!array_key_exists($property, $this->data) || $this->data[$property] != $value) {
            // If the value is changing, we invalidate the model.
            $this->validated = false;
        }
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
        return array();
    }

    /**
     * Get the properties definition of this model..
     *
     * @return array
     */
    final public static function properties_definition() {
        global $CFG;

        static $cachedef = [];
        if (isset($cachedef[static::class])) {
            return $cachedef[static::class];
        }

        $cachedef[static::class] = static::define_properties();
        $def = &$cachedef[static::class];
        $def['id'] = array(
            'default' => 0,
            'type' => PARAM_INT,
        );
        $def['timecreated'] = array(
            'default' => 0,
            'type' => PARAM_INT,
        );
        $def['timemodified'] = array(
            'default' => 0,
            'type' => PARAM_INT
        );
        $def['usermodified'] = array(
            'default' => 0,
            'type' => PARAM_INT
        );

        // List of reserved property names. Mostly because we have methods (getters/setters) which would confict with them.
        // Think about backwards compability before adding new ones here!
        $reserved = array('errors', 'formatted_properties', 'records', 'records_select', 'property_default_value',
            'property_error_message', 'sql_fields');

        foreach ($def as $property => $definition) {

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

        return $def;
    }

    /**
     * Gets all the formatted properties.
     *
     * Formatted properties are properties which have a format associated with them.
     *
     * @return array Keys are property names, values are property format names.
     */
    final public static function get_formatted_properties() {
        $properties = static::properties_definition();

        $formatted = array();
        foreach ($properties as $property => $definition) {
            $propertyformat = $property . 'format';
            if (($definition['type'] == PARAM_RAW || $definition['type'] == PARAM_CLEANHTML)
                    && array_key_exists($propertyformat, $properties)
                    && $properties[$propertyformat]['type'] == PARAM_INT) {
                $formatted[$property] = $propertyformat;
            }
        }

        return $formatted;
    }

    /**
     * Gets the default value for a property.
     *
     * This assumes that the property exists.
     *
     * @param string $property The property name.
     * @return mixed
     */
    final protected static function get_property_default_value($property) {
        $properties = static::properties_definition();
        if (!isset($properties[$property]['default'])) {
            return null;
        }
        $value = $properties[$property]['default'];
        if ($value instanceof \Closure) {
            return $value();
        }
        return $value;
    }

    /**
     * Gets the error message for a property.
     *
     * This assumes that the property exists.
     *
     * @param string $property The property name.
     * @return lang_string
     */
    final protected static function get_property_error_message($property) {
        $properties = static::properties_definition();
        if (!isset($properties[$property]['message'])) {
            return new lang_string('invaliddata', 'error');
        }
        return $properties[$property]['message'];
    }

    /**
     * Returns whether or not a property was defined.
     *
     * @param  string $property The property name.
     * @return boolean
     */
    final public static function has_property($property) {
        $properties = static::properties_definition();
        return isset($properties[$property]);
    }

    /**
     * Returns whether or not a property is required.
     *
     * By definition a property with a default value is not required.
     *
     * @param  string $property The property name.
     * @return boolean
     */
    final public static function is_property_required($property) {
        $properties = static::properties_definition();
        return !array_key_exists('default', $properties[$property]);
    }

    /**
     * Populate this class with data from a DB record.
     *
     * Note that this does not use any custom setter because the data here is intended to
     * represent what is stored in the database.
     *
     * @param \stdClass $record A DB record.
     * @return static
     */
    final public function from_record(stdClass $record) {
        $properties = static::properties_definition();
        $record = array_intersect_key((array) $record, $properties);
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
        $data = new stdClass();
        $properties = static::properties_definition();
        foreach ($properties as $property => $definition) {
            $data->$property = $this->raw_get($property);
        }
        return $data;
    }

    /**
     * Load the data from the DB.
     *
     * @return static
     */
    final public function read() {
        global $DB;

        if ($this->get('id') <= 0) {
            throw new coding_exception('id is required to load');
        }
        $record = $DB->get_record(static::TABLE, array('id' => $this->get('id')), '*', MUST_EXIST);
        $this->from_record($record);

        // Validate the data as it comes from the database.
        $this->validated = true;

        return $this;
    }

    /**
     * Hook to execute before a create.
     *
     * Please note that at this stage the data has already been validated and therefore
     * any new data being set will not be validated before it is sent to the database.
     *
     * This is only intended to be used by child classes, do not put any logic here!
     *
     * @return void
     */
    protected function before_create() {
    }

    /**
     * Insert a record in the DB.
     *
     * @return static
     */
    final public function create() {
        global $DB, $USER;

        if ($this->raw_get('id')) {
            // The validation methods rely on the ID to know if we're updating or not, the ID should be
            // falsy whenever we are creating an object.
            throw new coding_exception('Cannot create an object that has an ID defined.');
        }

        if (!$this->is_valid()) {
            throw new invalid_persistent_exception($this->get_errors());
        }

        // Before create hook.
        $this->before_create();

        // We can safely set those values bypassing the validation because we know what we're doing.
        $now = time();
        $this->raw_set('timecreated', $now);
        $this->raw_set('timemodified', $now);
        $this->raw_set('usermodified', $USER->id);

        $record = $this->to_record();
        unset($record->id);

        $id = $DB->insert_record(static::TABLE, $record);
        $this->raw_set('id', $id);

        // We ensure that this is flagged as validated.
        $this->validated = true;

        // After create hook.
        $this->after_create();

        return $this;
    }

    /**
     * Hook to execute after a create.
     *
     * This is only intended to be used by child classes, do not put any logic here!
     *
     * @return void
     */
    protected function after_create() {
    }

    /**
     * Hook to execute before an update.
     *
     * Please note that at this stage the data has already been validated and therefore
     * any new data being set will not be validated before it is sent to the database.
     *
     * This is only intended to be used by child classes, do not put any logic here!
     *
     * @return void
     */
    protected function before_update() {
    }

    /**
     * Update the existing record in the DB.
     *
     * @return bool True on success.
     */
    final public function update() {
        global $DB, $USER;

        if ($this->raw_get('id') <= 0) {
            throw new coding_exception('id is required to update');
        } else if (!$this->is_valid()) {
            throw new invalid_persistent_exception($this->get_errors());
        }

        // Before update hook.
        $this->before_update();

        // We can safely set those values after the validation because we know what we're doing.
        $this->raw_set('timemodified', time());
        $this->raw_set('usermodified', $USER->id);

        $record = $this->to_record();
        unset($record->timecreated);
        $record = (array) $record;

        // Save the record.
        $result = $DB->update_record(static::TABLE, $record);

        // We ensure that this is flagged as validated.
        $this->validated = true;

        // After update hook.
        $this->after_update($result);

        return $result;
    }

    /**
     * Hook to execute after an update.
     *
     * This is only intended to be used by child classes, do not put any logic here!
     *
     * @param bool $result Whether or not the update was successful.
     * @return void
     */
    protected function after_update($result) {
    }

    /**
     * Saves the record to the database.
     *
     * If this record has an ID, then {@link self::update()} is called, otherwise {@link self::create()} is called.
     * Before and after hooks for create() or update() will be called appropriately.
     *
     * @return void
     */
    final public function save() {
        if ($this->raw_get('id') <= 0) {
            $this->create();
        } else {
            $this->update();
        }
    }

    /**
     * Hook to execute before a delete.
     *
     * This is only intended to be used by child classes, do not put any logic here!
     *
     * @return void
     */
    protected function before_delete() {
    }

    /**
     * Delete an entry from the database.
     *
     * @return bool True on success.
     */
    final public function delete() {
        global $DB;

        if ($this->raw_get('id') <= 0) {
            throw new coding_exception('id is required to delete');
        }

        // Hook before delete.
        $this->before_delete();

        $result = $DB->delete_records(static::TABLE, array('id' => $this->raw_get('id')));

        // Hook after delete.
        $this->after_delete($result);

        // Reset the ID to avoid any confusion, this also invalidates the model's data.
        if ($result) {
            $this->raw_set('id', 0);
        }

        return $result;
    }

    /**
     * Hook to execute after a delete.
     *
     * This is only intended to be used by child classes, do not put any logic here!
     *
     * @param bool $result Whether or not the delete was successful.
     * @return void
     */
    protected function after_delete($result) {
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

    /**
     * Validates the data.
     *
     * Developers can implement addition validation by defining a method as follows. Note that
     * the method MUST return a lang_string() when there is an error, and true when the data is valid.
     *
     * protected function validate_propertyname($value) {
     *     if ($value !== 'My expected value') {
     *         return new lang_string('invaliddata', 'error');
     *     }
     *     return true
     * }
     *
     * It is OK to use other properties in your custom validation methods when you need to, however note
     * they might not have been validated yet, so try not to rely on them too much.
     *
     * Note that the validation methods should be protected. Validating just one field is not
     * recommended because of the possible dependencies between one field and another,also the
     * field ID can be used to check whether the object is being updated or created.
     *
     * When validating foreign keys the persistent should only check that the associated model
     * exists. The validation methods should not be used to check for a change in that relationship.
     * The API method setting the attributes on the model should be responsible for that.
     * E.g. On a course model, the method validate_categoryid will check that the category exists.
     * However, if a course can never be moved outside of its category it would be up to the calling
     * code to ensure that the category ID will not be altered.
     *
     * @return array|true Returns true when the validation passed, or an array of properties with errors.
     */
    final public function validate() {
        global $CFG;

        // Before validate hook.
        $this->before_validate();

        // If this object has not been validated yet.
        if ($this->validated !== true) {

            $errors = array();
            $properties = static::properties_definition();
            foreach ($properties as $property => $definition) {

                // Get the data, bypassing the potential custom getter which could alter the data.
                $value = $this->raw_get($property);

                // Check if the property is required.
                if ($value === null && static::is_property_required($property)) {
                    $errors[$property] = new lang_string('requiredelement', 'form');
                    continue;
                }

                // Check that type of value is respected.
                try {
                    if ($definition['type'] === PARAM_BOOL && $value === false) {
                        // Validate_param() does not like false with PARAM_BOOL, better to convert it to int.
                        $value = 0;
                    }
                    if ($definition['type'] === PARAM_CLEANHTML) {
                        // We silently clean for this type. It may introduce changes even to valid data.
                        $value = clean_param($value, PARAM_CLEANHTML);
                    }
                    validate_param($value, $definition['type'], $definition['null']);
                } catch (invalid_parameter_exception $e) {
                    $errors[$property] = static::get_property_error_message($property);
                    continue;
                }

                // Check that the value is part of a list of allowed values.
                if (isset($definition['choices']) && !in_array($value, $definition['choices'])) {
                    $errors[$property] = static::get_property_error_message($property);
                    continue;
                }

                // Call custom validation method.
                $method = 'validate_' . $property;
                if (method_exists($this, $method)) {

                    // Warn the developers when they are doing something wrong.
                    if ($CFG->debugdeveloper) {
                        $reflection = new ReflectionMethod($this, $method);
                        if (!$reflection->isProtected()) {
                            throw new coding_exception('The method ' . get_class($this) . '::'. $method . ' should be protected.');
                        }
                    }

                    $valid = $this->{$method}($value);
                    if ($valid !== true) {
                        if (!($valid instanceof lang_string)) {
                            throw new coding_exception('Unexpected error message.');
                        }
                        $errors[$property] = $valid;
                        continue;
                    }
                }
            }

            $this->validated = true;
            $this->errors = $errors;
        }

        return empty($this->errors) ? true : $this->errors;
    }

    /**
     * Returns whether or not the model is valid.
     *
     * @return boolean True when it is.
     */
    final public function is_valid() {
        return $this->validate() === true;
    }

    /**
     * Returns the validation errors.
     *
     * @return array
     */
    final public function get_errors() {
        $this->validate();
        return $this->errors;
    }

    /**
     * Extract a record from a row of data.
     *
     * Most likely used in combination with {@link self::get_sql_fields()}. This method is
     * simple enough to be used by non-persistent classes, keep that in mind when modifying it.
     *
     * e.g. persistent::extract_record($row, 'user'); should work.
     *
     * @param stdClass $row The row of data.
     * @param string $prefix The prefix the data fields are prefixed with, defaults to the table name followed by underscore.
     * @return stdClass The extracted data.
     */
    public static function extract_record($row, $prefix = null) {
        if ($prefix === null) {
            $prefix = str_replace('_', '', static::TABLE) . '_';
        }
        $prefixlength = strlen($prefix);

        $data = new stdClass();
        foreach ($row as $property => $value) {
            if (strpos($property, $prefix) === 0) {
                $propertyname = substr($property, $prefixlength);
                $data->$propertyname = $value;
            }
        }

        return $data;
    }

    /**
     * Load a list of records.
     *
     * @param array $filters Filters to apply.
     * @param string $sort Field to sort by.
     * @param string $order Sort order.
     * @param int $skip Limitstart.
     * @param int $limit Number of rows to return.
     *
     * @return static[]
     */
    public static function get_records($filters = array(), $sort = '', $order = 'ASC', $skip = 0, $limit = 0) {
        global $DB;

        $orderby = '';
        if (!empty($sort)) {
            $orderby = $sort . ' ' . $order;
        }

        $records = $DB->get_records(static::TABLE, $filters, $orderby, '*', $skip, $limit);
        $instances = array();

        foreach ($records as $record) {
            $newrecord = new static(0, $record);
            array_push($instances, $newrecord);
        }
        return $instances;
    }

    /**
     * Load a single record.
     *
     * @param array $filters Filters to apply.
     * @param int $strictness Similar to the internal DB get_record call, indicate whether a missing record should be
     *      ignored/return false ({@see IGNORE_MISSING}) or should cause an exception to be thrown ({@see MUST_EXIST})
     * @return false|static
     */
    public static function get_record(array $filters = [], int $strictness = IGNORE_MISSING) {
        global $DB;

        $record = $DB->get_record(static::TABLE, $filters, '*', $strictness);
        return $record ? new static(0, $record) : false;
    }

    /**
     * Load a list of records based on a select query.
     *
     * @param string $select
     * @param array $params
     * @param string $sort
     * @param string $fields
     * @param int $limitfrom
     * @param int $limitnum
     * @return static[]
     */
    public static function get_records_select($select, $params = null, $sort = '', $fields = '*', $limitfrom = 0, $limitnum = 0) {
        global $DB;

        $records = $DB->get_records_select(static::TABLE, $select, $params, $sort, $fields, $limitfrom, $limitnum);

        // We return class instances.
        $instances = array();
        foreach ($records as $key => $record) {
            $instances[$key] = new static(0, $record);
        }

        return $instances;

    }

    /**
     * Return the list of fields for use in a SELECT clause.
     *
     * Having the complete list of fields prefixed allows for multiple persistents to be fetched
     * in a single query. Use {@link self::extract_record()} to extract the records from the query result.
     *
     * @param string $alias The alias used for the table.
     * @param string $prefix The prefix to use for each field, defaults to the table name followed by underscore.
     * @return string The SQL fragment.
     */
    public static function get_sql_fields($alias, $prefix = null) {
        global $CFG;
        $fields = array();

        if ($prefix === null) {
            $prefix = str_replace('_', '', static::TABLE) . '_';
        }

        // Get the properties and move ID to the top.
        $properties = static::properties_definition();
        $id = $properties['id'];
        unset($properties['id']);
        $properties = array('id' => $id) + $properties;

        foreach ($properties as $property => $definition) {
            $as = $prefix . $property;
            $fields[] = $alias . '.' . $property . ' AS ' . $as;

            // Warn developers that the query will not always work.
            if ($CFG->debugdeveloper && strlen($as) > 30) {
                throw new coding_exception("The alias '$as' for column '$alias.$property' exceeds 30 characters" .
                    " and will therefore not work across all supported databases.");
            }
        }

        return implode(', ', $fields);
    }

    /**
     * Count a list of records.
     *
     * @param array $conditions An array of conditions.
     * @return int
     */
    public static function count_records(array $conditions = array()) {
        global $DB;

        $count = $DB->count_records(static::TABLE, $conditions);
        return $count;
    }

    /**
     * Count a list of records.
     *
     * @param string $select
     * @param array $params
     * @return int
     */
    public static function count_records_select($select, $params = null) {
        global $DB;

        $count = $DB->count_records_select(static::TABLE, $select, $params);
        return $count;
    }

    /**
     * Check if a record exists by ID.
     *
     * @param int $id Record ID.
     * @return bool
     */
    public static function record_exists($id) {
        global $DB;
        return $DB->record_exists(static::TABLE, array('id' => $id));
    }

    /**
     * Check if a records exists.
     *
     * @param string $select
     * @param array $params
     * @return bool
     */
    public static function record_exists_select($select, array $params = null) {
        global $DB;
        return $DB->record_exists_select(static::TABLE, $select, $params);
    }

}
