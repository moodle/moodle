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
 * Class for preparing data for Users.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\custom;

use stdClass;
use lang_string;
use core\invalid_persistent_exception;
use local_intellidata\helpers\DBManagerHelper;
use local_intellidata\helpers\EventsHelper;
use local_intellidata\services\datatypes_service;
use local_intellidata\services\dbschema_service;

/**
 * Class for preparing data for Users.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entity {

    /** @var string The entity type name. */
    public static $datatype = null;

    /** @var array The model data. */
    private $data = [];

    /** @var array The fields to return. */
    protected $returnfields = [];

    /** @var array The list of validation errors. */
    private $errors = [];

    /** @var array The list of fields. */
    private $fields = [];

    /**
     * Entity constructor.
     *
     * @param $datatype
     * @param $record
     * @param $returnfields
     */
    public function __construct($datatype, $record = null, $returnfields = []) {

        self::$datatype = $datatype;

        if (count($returnfields)) {
            $this->returnfields = $returnfields;
        }
        if ($record) {
            self::set_values($record);
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
     * @return \local_intellidata\entities\entity
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
     * Get the properties definition of this model..
     *
     * @return array
     */
    final public static function properties_definition($returnfields = []) {
        global $CFG;

        $def = self::define_properties();

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
            'null' => NULL_NOT_ALLOWED,
        ];
        $def['recordusermodified'] = [
            'default' => 0,
            'type' => PARAM_INT,
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
        $this->data = (array)$record;
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
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        $fields = [];
        $dbschema = new dbschema_service();

        if (!self::$datatype) {
            return $fields;
        }

        $columns = $dbschema->get_table_columns(datatypes_service::get_optional_table(self::$datatype));

        foreach ($columns as $column) {
            $fields[$column->name] = [
                'type' => PARAM_RAW,
                'description' => $column->name,
                'default' => DBManagerHelper::get_field_default_value($column),
                'null' => NULL_ALLOWED,
            ];
        }

        return $fields;
    }
}
