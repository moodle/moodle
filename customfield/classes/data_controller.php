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
 * Customfield component data controller abstract class
 *
 * @package   core_customfield
 * @copyright 2018 Toni Barbera <toni@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_customfield;

use backup_nested_element;
use core_customfield\output\field_data;

defined('MOODLE_INTERNAL') || die;

/**
 * Base class for custom fields data controllers
 *
 * This class is a wrapper around the persistent data class that allows to define
 * how the element behaves in the instance edit forms.
 *
 * Custom field plugins must define a class
 * \{pluginname}\data_controller extends \core_customfield\data_controller
 *
 * @package core_customfield
 * @copyright 2018 Toni Barbera <toni@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class data_controller {
    /**
     * Data persistent
     *
     * @var data
     */
    protected $data;

    /**
     * Field that this data belongs to.
     *
     * @var field_controller
     */
    protected $field;

    /**
     * data_controller constructor.
     *
     * @param int $id
     * @param \stdClass|null $record
     */
    public function __construct(int $id, \stdClass $record) {
        $this->data = new data($id, $record);
    }

    /**
     * Creates an instance of data_controller
     *
     * Parameters $id, $record and $field can complement each other but not conflict.
     * If $id is not specified, fieldid must be present either in $record or in $field.
     * If $id is not specified, instanceid must be present in $record
     *
     * No DB queries are performed if both $record and $field are specified.

     * @param int $id
     * @param \stdClass|null $record
     * @param field_controller|null $field
     * @return data_controller
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function create(int $id, ?\stdClass $record = null, ?field_controller $field = null): data_controller {
        global $DB;
        if ($id && $record) {
            // This warning really should be in persistent as well.
            debugging('Too many parameters, either id need to be specified or a record, but not both.',
                DEBUG_DEVELOPER);
        }
        if ($id) {
            $record = $DB->get_record(data::TABLE, array('id' => $id), '*', MUST_EXIST);
        } else if (!$record) {
            $record = new \stdClass();
        }

        if (!$field && empty($record->fieldid)) {
            throw new \coding_exception('Not enough parameters to initialise data_controller - unknown field');
        }
        if (!$field) {
            $field = field_controller::create($record->fieldid);
        }
        if (empty($record->fieldid)) {
            $record->fieldid = $field->get('id');
        }
        if ($field->get('id') != $record->fieldid) {
            throw new \coding_exception('Field id from the record does not match field from the parameter');
        }
        $type = $field->get('type');
        $customfieldtype = "\\customfield_{$type}\\data_controller";
        if (!class_exists($customfieldtype) || !is_subclass_of($customfieldtype, self::class)) {
            throw new \moodle_exception('errorfieldtypenotfound', 'core_customfield', '', s($type));
        }
        $datacontroller = new $customfieldtype(0, $record);
        $datacontroller->field = $field;
        return $datacontroller;
    }

    /**
     * Returns the name of the field to be used on HTML forms.
     *
     * @return string
     */
    public function get_form_element_name(): string {
        return 'customfield_' . $this->get_field()->get('shortname');
    }

    /**
     * Persistent getter parser.
     *
     * @param string $property
     * @return mixed
     */
    final public function get($property) {
        return $this->data->get($property);
    }

    /**
     * Persistent setter parser.
     *
     * @param string $property
     * @param mixed $value
     * @return data
     */
    final public function set($property, $value) {
        return $this->data->set($property, $value);
    }

    /**
     * Return the name of the field in the db table {customfield_data} where the data is stored
     *
     * Must be one of the following:
     *   intvalue - can store integer values, this field is indexed
     *   decvalue - can store decimal values
     *   shortcharvalue - can store character values up to 255 characters long, this field is indexed
     *   charvalue - can store character values up to 1333 characters long, this field is not indexed but
     *     full text search is faster than on field 'value'
     *   value - can store character values of unlimited length ("text" field in the db)
     *
     * @return string
     */
    abstract public function datafield(): string;

    /**
     * Delete data. Element can override it if related information needs to be deleted as well (such as files)
     *
     * @return bool
     */
    public function delete() {
        return $this->data->delete();
    }

    /**
     * Persistent save parser.
     *
     * @return void
     */
    public function save() {
        $this->data->save();
    }

    /**
     * Field associated with this data
     *
     * @return field_controller
     */
    public function get_field(): field_controller {
        return $this->field;
    }

    /**
     * Saves the data coming from form
     *
     * @param \stdClass $datanew data coming from the form
     */
    public function instance_form_save(\stdClass $datanew) {
        $elementname = $this->get_form_element_name();
        if (!property_exists($datanew, $elementname)) {
            return;
        }
        $datafieldvalue = $value = $datanew->{$elementname};

        // For numeric datafields, persistent won't allow empty string, swap for null.
        $datafield = $this->datafield();
        if ($datafield === 'intvalue' || $datafield === 'decvalue') {
            $datafieldvalue = $datafieldvalue === '' ? null : $datafieldvalue;
        }

        $this->data->set($datafield, $datafieldvalue);
        $this->data->set('value', $value);
        $this->save();
    }

    /**
     * Prepares the custom field data related to the object to pass to mform->set_data() and adds them to it
     *
     * This function must be called before calling $form->set_data($object);
     *
     * @param \stdClass $instance the instance that has custom fields, if 'id' attribute is present the custom
     *    fields for this instance will be added, otherwise the default values will be added.
     */
    public function instance_form_before_set_data(\stdClass $instance) {
        $instance->{$this->get_form_element_name()} = $this->get_value();
    }

    /**
     * Checks if the value is empty
     *
     * @param mixed $value
     * @return bool
     */
    protected function is_empty($value): bool {
        if ($this->datafield() === 'value' || $this->datafield() === 'charvalue' || $this->datafield() === 'shortcharvalue') {
            return '' . $value === '';
        }
        return empty($value);
    }

    /**
     * Checks if the value is unique
     *
     * @param mixed $value
     * @return bool
     */
    protected function is_unique($value): bool {
        global $DB;

        // Ensure the "value" datafield can be safely compared across all databases.
        $datafield = $this->datafield();
        if ($datafield === 'value') {
            $datafield = $DB->sql_cast_to_char($datafield);
        }

        $where = "fieldid = ? AND {$datafield} = ?";
        $params = [$this->get_field()->get('id'), $value];
        if ($this->get('id')) {
            $where .= ' AND id <> ?';
            $params[] = $this->get('id');
        }
        return !$DB->record_exists_select('customfield_data', $where, $params);
    }

    /**
     * Called from instance edit form in validation()
     *
     * @param array $data
     * @param array $files
     * @return array array of errors
     */
    public function instance_form_validation(array $data, array $files): array {
        $errors = [];
        $elementname = $this->get_form_element_name();
        if ($this->get_field()->get_configdata_property('uniquevalues') == 1) {
            $value = $data[$elementname];
            if (!$this->is_empty($value) && !$this->is_unique($value)) {
                $errors[$elementname] = get_string('erroruniquevalues', 'core_customfield');
            }
        }
        return $errors;
    }

    /**
     * Called from instance edit form in definition_after_data()
     *
     * @param \MoodleQuickForm $mform
     */
    public function instance_form_definition_after_data(\MoodleQuickForm $mform) {

    }

    /**
     * Used by handlers to display data on various places.
     *
     * @return string
     */
    public function display(): string {
        global $PAGE;
        $output = $PAGE->get_renderer('core_customfield');
        return $output->render(new field_data($this));
    }

    /**
     * Returns the default value as it would be stored in the database (not in human-readable format).
     *
     * @return mixed
     */
    abstract public function get_default_value();

    /**
     * Returns the value as it is stored in the database or default value if data record is not present
     *
     * @return mixed
     */
    public function get_value() {
        if (!$this->get('id')) {
            return $this->get_default_value();
        }
        return $this->get($this->datafield());
    }

    /**
     * Return the context of the field
     *
     * @return \context
     */
    public function get_context(): \context {
        if ($this->get('contextid')) {
            return \context::instance_by_id($this->get('contextid'));
        } else if ($this->get('instanceid')) {
            return $this->get_field()->get_handler()->get_instance_context($this->get('instanceid'));
        } else {
            // Context is not yet known (for example, entity is not yet created).
            return \context_system::instance();
        }
    }

    /**
     * Add a field to the instance edit form.
     *
     * @param \MoodleQuickForm $mform
     */
    abstract public function instance_form_definition(\MoodleQuickForm $mform);

    /**
     * Returns value in a human-readable format or default value if data record is not present
     *
     * This is the default implementation that most likely needs to be overridden
     *
     * @return mixed|null value or null if empty
     */
    public function export_value() {
        $value = $this->get_value();

        if ($this->is_empty($value)) {
            return null;
        }

        if ($this->datafield() === 'intvalue') {
            return (int)$value;
        } else if ($this->datafield() === 'decvalue') {
            return (float)$value;
        } else if ($this->datafield() === 'value') {
            return format_text($value, $this->get('valueformat'), [
                'context' => $this->get_context(),
                'trusted' => $this->get('valuetrust'),
            ]);
        } else {
            return format_string($value, true, ['context' => $this->get_context()]);
        }
    }

    /**
     * Callback for backup, allowing custom fields to add additional data to the backup.
     * It is not an abstract method for backward compatibility reasons.
     *
     * @param \backup_nested_element $customfieldelement The custom field element to be backed up.
     */
    public function backup_define_structure(backup_nested_element $customfieldelement): void {
    }

    /**
     * Callback for restore, allowing custom fields to restore additional data from the backup.
     * It is not an abstract method for backward compatibility reasons.
     *
     * @param \restore_structure_step $step The restore step instance.
     * @param int $newid The new ID for the custom field data after restore.
     * @param int $oldid The original ID of the custom field data before backup.
     */
    public function restore_define_structure(\restore_structure_step $step, int $newid, int $oldid): void {
    }

    /**
     * Persistent to_record parser.
     *
     * @return \stdClass
     */
    final public function to_record() {
        return $this->data->to_record();
    }
}
