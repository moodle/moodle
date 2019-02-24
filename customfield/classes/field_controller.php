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
 * Field controller abstract class
 *
 * @package   core_customfield
 * @copyright 2018 Toni Barbera <toni@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_customfield;

defined('MOODLE_INTERNAL') || die;

/**
 * Base class for custom fields controllers
 *
 * This class is a wrapper around the persistent field class that allows to define the field
 * configuration
 *
 * Custom field plugins must define a class
 * \{pluginname}\field_controller extends \core_customfield\field_controller
 *
 * @package core_customfield
 * @copyright 2018 Toni Barbera <toni@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class field_controller {

    /**
     * Field persistent class
     *
     * @var field
     */
    protected $field;

    /**
     * Category of the field.
     *
     * @var category_controller
     */
    protected $category;

    /**
     * Constructor.
     *
     * @param int $id
     * @param \stdClass|null $record
     */
    public function __construct(int $id = 0, \stdClass $record = null) {
        $this->field = new field($id, $record);
    }

    /**
     * Creates an instance of field_controller
     *
     * Parameters $id, $record and $category can complement each other but not conflict.
     * If $id is not specified, categoryid must be present either in $record or in $category.
     * If $id is not specified, type must be present in $record
     *
     * No DB queries are performed if both $record and $category are specified.
     *
     * @param int $id
     * @param \stdClass|null $record
     * @param category_controller|null $category
     * @return field_controller will return the instance of the class from the customfield element plugin
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function create(int $id, \stdClass $record = null, category_controller $category = null) : field_controller {
        global $DB;
        if ($id && $record) {
            // This warning really should be in persistent as well.
            debugging('Too many parameters, either id need to be specified or a record, but not both.',
                DEBUG_DEVELOPER);
        }
        if ($id) {
            if (!$record = $DB->get_record(field::TABLE, array('id' => $id), '*', IGNORE_MISSING)) {
                throw new \moodle_exception('fieldnotfound', 'core_customfield');
            }
        }

        if (empty($record->categoryid)) {
            if (!$category) {
                throw new \coding_exception('Not enough parameters to initialise field_controller - unknown category');
            } else {
                $record->categoryid = $category->get('id');
            }
        }
        if (empty($record->type)) {
            throw new \coding_exception('Not enough parameters to initialise field_controller - unknown field type');
        }

        $type = $record->type;
        if (!$category) {
            $category = category_controller::create($record->categoryid);
        }
        if ($category->get('id') != $record->categoryid) {
            throw new \coding_exception('Category of the field does not match category from the parameter');
        }

        $customfieldtype = "\\customfield_{$type}\\field_controller";
        if (!class_exists($customfieldtype) || !is_subclass_of($customfieldtype, self::class)) {
            throw new \moodle_exception('errorfieldtypenotfound', 'core_customfield', '', s($type));
        }
        $fieldcontroller = new $customfieldtype(0, $record);
        $fieldcontroller->category = $category;
        $category->add_field($fieldcontroller);
        return $fieldcontroller;
    }

    /**
     * Validate the data on the field configuration form
     *
     * Plugins can override it
     *
     * @param array $data from the add/edit profile field form
     * @param array $files
     * @return array associative array of error messages
     */
    public function config_form_validation(array $data, $files = array()) : array {
        return array();
    }


    /**
     * Persistent getter parser.
     *
     * @param string $property
     * @return mixed
     */
    final public function get(string $property) {
        return $this->field->get($property);
    }

    /**
     * Persistent setter parser.
     *
     * @param string $property
     * @param mixed $value
     * @return field
     */
    final public function set($property, $value) {
        return $this->field->set($property, $value);
    }

    /**
     * Delete a field and all associated data
     *
     * Plugins may override it if it is necessary to delete related data (such as files)
     *
     * Not that the delete() method from data_controller is not called here.
     *
     * @return bool
     */
    public function delete() : bool {
        global $DB;
        $DB->delete_records('customfield_data', ['fieldid' => $this->get('id')]);
        return $this->field->delete();
    }

    /**
     * Save or update the persistent class to database.
     *
     * @return void
     */
    public function save() {
        $this->field->save();
    }

    /**
     * Persistent to_record parser.
     *
     * @return \stdClass
     */
    final public function to_record() {
        return $this->field->to_record();
    }

    /**
     * Get the category associated with this field
     *
     * @return category_controller
     */
    public final function get_category() : category_controller {
        return $this->category;
    }

    /**
     * Get configdata property.
     *
     * @param string $property name of the property
     * @return mixed
     */
    public function get_configdata_property(string $property) {
        $configdata = $this->field->get('configdata');
        if (!isset($configdata[$property])) {
            return null;
        }
        return $configdata[$property];
    }

    /**
     * Returns a handler for this field
     *
     * @return handler
     */
    public final function get_handler() : handler {
        return $this->get_category()->get_handler();
    }

    /**
     * Prepare the field data to set in the configuration form
     *
     * Plugin can override if some preprocessing required for editor or filemanager fields
     *
     * @param \stdClass $formdata
     */
    public function prepare_for_config_form(\stdClass $formdata) {
    }

    /**
     * Add specific settings to the field configuration form, for example "default value"
     *
     * @param \MoodleQuickForm $mform
     */
    public abstract function config_form_definition(\MoodleQuickForm $mform);

    /**
     * Returns the field name formatted according to configuration context.
     *
     * @return string
     */
    public function get_formatted_name() : string {
        $context = $this->get_handler()->get_configuration_context();
        return format_string($this->get('name'), true, ['context' => $context]);
    }
}
