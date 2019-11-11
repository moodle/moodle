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
 * Api customfield package
 *
 * @package   core_customfield
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_customfield;

use core\output\inplace_editable;
use core_customfield\event\category_created;
use core_customfield\event\category_deleted;
use core_customfield\event\category_updated;
use core_customfield\event\field_created;
use core_customfield\event\field_deleted;
use core_customfield\event\field_updated;

defined('MOODLE_INTERNAL') || die;

/**
 * Class api
 *
 * @package core_customfield
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * For the given instance and list of fields fields retrieves data associated with them
     *
     * @param field_controller[] $fields list of fields indexed by field id
     * @param int $instanceid
     * @param bool $adddefaults
     * @return data_controller[] array of data_controller objects indexed by fieldid. All fields are present,
     *    some data_controller objects may have 'id', some not
     *     If ($adddefaults): All fieldids are present, some data_controller objects may have 'id', some not.
     *     If (!$adddefaults): Only fieldids with data are present, all data_controller objects have 'id'.
     */
    public static function get_instance_fields_data(array $fields, int $instanceid, bool $adddefaults = true) : array {
        return self::get_instances_fields_data($fields, [$instanceid], $adddefaults)[$instanceid];
    }

    /**
     * For given list of instances and fields retrieves data associated with them
     *
     * @param field_controller[] $fields list of fields indexed by field id
     * @param int[] $instanceids
     * @param bool $adddefaults
     * @return data_controller[][] 2-dimension array, first index is instanceid, second index is fieldid.
     *     If ($adddefaults): All instanceids and all fieldids are present, some data_controller objects may have 'id', some not.
     *     If (!$adddefaults): All instanceids are present but only fieldids with data are present, all
     *         data_controller objects have 'id'.
     */
    public static function get_instances_fields_data(array $fields, array $instanceids, bool $adddefaults = true) : array {
        global $DB;

        // Create the results array where instances and fields order is the same as in the input arrays.
        $result = array_fill_keys($instanceids, array_fill_keys(array_keys($fields), null));

        if (empty($instanceids) || empty($fields)) {
            return $result;
        }

        // Retrieve all existing data.
        list($sqlfields, $params) = $DB->get_in_or_equal(array_keys($fields), SQL_PARAMS_NAMED, 'fld');
        list($sqlinstances, $iparams) = $DB->get_in_or_equal($instanceids, SQL_PARAMS_NAMED, 'ins');
        $sql = "SELECT d.*
                  FROM {customfield_field} f
                  JOIN {customfield_data} d ON (f.id = d.fieldid AND d.instanceid {$sqlinstances})
                 WHERE f.id {$sqlfields}";
        $fieldsdata = $DB->get_recordset_sql($sql, $params + $iparams);
        foreach ($fieldsdata as $data) {
            $result[$data->instanceid][$data->fieldid] = data_controller::create(0, $data, $fields[$data->fieldid]);
        }
        $fieldsdata->close();

        if ($adddefaults) {
            // Add default data where it was not retrieved.
            foreach ($instanceids as $instanceid) {
                foreach ($fields as $fieldid => $field) {
                    if ($result[$instanceid][$fieldid] === null) {
                        $result[$instanceid][$fieldid] =
                            data_controller::create(0, (object)['instanceid' => $instanceid], $field);
                    }
                }
            }
        } else {
            // Remove null-placeholders for data that was not retrieved.
            foreach ($instanceids as $instanceid) {
                $result[$instanceid] = array_filter($result[$instanceid]);
            }
        }

        return $result;
    }

    /**
     * Retrieve a list of all available custom field types
     *
     * @return   array   a list of the fieldtypes suitable to use in a select statement
     */
    public static function get_available_field_types() {
        $fieldtypes = array();

        $plugins = \core\plugininfo\customfield::get_enabled_plugins();
        foreach ($plugins as $type => $unused) {
            $fieldtypes[$type] = get_string('pluginname', 'customfield_' . $type);
        }
        asort($fieldtypes);

        return $fieldtypes;
    }

    /**
     * Updates or creates a field with data that came from a form
     *
     * @param field_controller $field
     * @param \stdClass $formdata
     */
    public static function save_field_configuration(field_controller $field, \stdClass $formdata) {
        foreach ($formdata as $key => $value) {
            if ($key === 'configdata' && is_array($formdata->configdata)) {
                $field->set($key, json_encode($value));
            } else if ($key === 'id' || ($key === 'type' && $field->get('id'))) {
                continue;
            } else if (field::has_property($key)) {
                $field->set($key, $value);
            }
        }

        $isnewfield = empty($field->get('id'));

        // Process files in description.
        if (isset($formdata->description_editor)) {
            if (!$field->get('id')) {
                // We need 'id' field to store files used in description.
                $field->save();
            }

            $data = (object) ['description_editor' => $formdata->description_editor];
            $textoptions = $field->get_handler()->get_description_text_options();
            $data = file_postupdate_standard_editor($data, 'description', $textoptions, $textoptions['context'],
                'core_customfield', 'description', $field->get('id'));
            $field->set('description', $data->description);
            $field->set('descriptionformat', $data->descriptionformat);
        }

        // Save the field.
        $field->save();

        if ($isnewfield) {
            // Move to the end of the category.
            self::move_field($field, $field->get('categoryid'));
        }

        if ($isnewfield) {
            field_created::create_from_object($field)->trigger();
        } else {
            field_updated::create_from_object($field)->trigger();
        }
    }

    /**
     * Change fields sort order, move field to another category
     *
     * @param field_controller $field field that needs to be moved
     * @param int $categoryid category that needs to be moved
     * @param int $beforeid id of the category this category needs to be moved before, 0 to move to the end
     */
    public static function move_field(field_controller $field, int $categoryid, int $beforeid = 0) {
        global $DB;

        if ($field->get('categoryid') != $categoryid) {
            // Move field to another category. Validate that this category exists and belongs to the same component/area/itemid.
            $category = $field->get_category();
            $DB->get_record(category::TABLE, [
                'component' => $category->get('component'),
                'area' => $category->get('area'),
                'itemid' => $category->get('itemid'),
                'id' => $categoryid], 'id', MUST_EXIST);
            $field->set('categoryid', $categoryid);
            $field->save();
            field_updated::create_from_object($field)->trigger();
        }

        // Reorder fields in the target category.
        $records = $DB->get_records(field::TABLE, ['categoryid' => $categoryid], 'sortorder, id', '*');

        $id = $field->get('id');
        $fieldsids = array_values(array_diff(array_keys($records), [$id]));
        $idx = $beforeid ? array_search($beforeid, $fieldsids) : false;
        if ($idx === false) {
            // Set as the last field.
            $fieldsids = array_merge($fieldsids, [$id]);
        } else {
            // Set before field with id $beforeid.
            $fieldsids = array_merge(array_slice($fieldsids, 0, $idx), [$id], array_slice($fieldsids, $idx));
        }

        foreach (array_values($fieldsids) as $idx => $fieldid) {
            // Use persistent class to update the sortorder for each field that needs updating.
            if ($records[$fieldid]->sortorder != $idx) {
                $f = ($fieldid == $id) ? $field : new field(0, $records[$fieldid]);
                $f->set('sortorder', $idx);
                $f->save();
            }
        }
    }

    /**
     * Delete a field
     *
     * @param field_controller $field
     */
    public static function delete_field_configuration(field_controller $field) : bool {
        $event = field_deleted::create_from_object($field);
        get_file_storage()->delete_area_files($field->get_handler()->get_configuration_context()->id, 'core_customfield',
            'description', $field->get('id'));
        $result = $field->delete();
        $event->trigger();
        return $result;
    }

    /**
     * Returns an object for inplace editable
     *
     * @param category_controller $category category that needs to be moved
     * @param bool $editable
     * @return inplace_editable
     */
    public static function get_category_inplace_editable(category_controller $category, bool $editable = true) : inplace_editable {
        return new inplace_editable('core_customfield',
                                    'category',
                                    $category->get('id'),
                                    $editable,
                                    $category->get_formatted_name(),
                                    $category->get('name'),
                                    get_string('editcategoryname', 'core_customfield'),
                                    get_string('newvaluefor', 'core_form', format_string($category->get('name')))
        );
    }

    /**
     * Reorder categories, move given category before another category
     *
     * @param category_controller $category category that needs to be moved
     * @param int $beforeid id of the category this category needs to be moved before, 0 to move to the end
     */
    public static function move_category(category_controller $category, int $beforeid = 0) {
        global $DB;
        $records = $DB->get_records(category::TABLE, [
            'component' => $category->get('component'),
            'area' => $category->get('area'),
            'itemid' => $category->get('itemid')
        ], 'sortorder, id', '*');

        $id = $category->get('id');
        $categoriesids = array_values(array_diff(array_keys($records), [$id]));
        $idx = $beforeid ? array_search($beforeid, $categoriesids) : false;
        if ($idx === false) {
            // Set as the last category.
            $categoriesids = array_merge($categoriesids, [$id]);
        } else {
            // Set before category with id $beforeid.
            $categoriesids = array_merge(array_slice($categoriesids, 0, $idx), [$id], array_slice($categoriesids, $idx));
        }

        foreach (array_values($categoriesids) as $idx => $categoryid) {
            // Use persistent class to update the sortorder for each category that needs updating.
            if ($records[$categoryid]->sortorder != $idx) {
                $c = ($categoryid == $id) ? $category : category_controller::create(0, $records[$categoryid]);
                $c->set('sortorder', $idx);
                $c->save();
            }
        }
    }

    /**
     * Insert or update custom field category
     *
     * @param category_controller $category
     */
    public static function save_category(category_controller $category) {
        $isnewcategory = empty($category->get('id'));

        $category->save();

        if ($isnewcategory) {
            // Move to the end.
            self::move_category($category);
            category_created::create_from_object($category)->trigger();
        } else {
            category_updated::create_from_object($category)->trigger();
        }
    }

    /**
     * Delete a custom field category
     *
     * @param category_controller $category
     * @return bool
     */
    public static function delete_category(category_controller $category) : bool {
        $event = category_deleted::create_from_object($category);

        // Delete all fields.
        foreach ($category->get_fields() as $field) {
            self::delete_field_configuration($field);
        }

        $result = $category->delete();
        $event->trigger();
        return $result;
    }

    /**
     * Returns a list of categories with their related fields.
     *
     * @param string $component
     * @param string $area
     * @param int $itemid
     * @return category_controller[]
     */
    public static function get_categories_with_fields(string $component, string $area, int $itemid) : array {
        global $DB;

        $categories = [];

        $options = [
                'component' => $component,
                'area'      => $area,
                'itemid'    => $itemid
        ];

        $plugins = \core\plugininfo\customfield::get_enabled_plugins();
        list($sqlfields, $params) = $DB->get_in_or_equal(array_keys($plugins), SQL_PARAMS_NAMED, 'param', true, null);

        $fields = 'f.*, ' . join(', ', array_map(function($field) {
                return "c.$field AS category_$field";
        }, array_diff(array_keys(category::properties_definition()), ['usermodified', 'timemodified'])));
        $sql = "SELECT $fields
                  FROM {customfield_category} c
             LEFT JOIN {customfield_field} f ON c.id = f.categoryid AND f.type $sqlfields
                 WHERE c.component = :component AND c.area = :area AND c.itemid = :itemid
              ORDER BY c.sortorder, f.sortorder";
        $fieldsdata = $DB->get_recordset_sql($sql, $options + $params);

        foreach ($fieldsdata as $data) {
            if (!array_key_exists($data->category_id, $categories)) {
                $categoryobj = new \stdClass();
                foreach ($data as $key => $value) {
                    if (preg_match('/^category_(.*)$/', $key, $matches)) {
                        $categoryobj->{$matches[1]} = $value;
                    }
                }
                $category = category_controller::create(0, $categoryobj);
                $categories[$categoryobj->id] = $category;
            } else {
                $category = $categories[$data->categoryid];
            }
            if ($data->id) {
                $fieldobj = new \stdClass();
                foreach ($data as $key => $value) {
                    if (!preg_match('/^category_/', $key)) {
                        $fieldobj->{$key} = $value;
                    }
                }
                $field = field_controller::create(0, $fieldobj, $category);
            }
        }
        $fieldsdata->close();

        return $categories;
    }

    /**
     * Prepares the object to pass to field configuration form set_data() method
     *
     * @param field_controller $field
     * @return \stdClass
     */
    public static function prepare_field_for_config_form(field_controller $field) : \stdClass {
        if ($field->get('id')) {
            $formdata = $field->to_record();
            $formdata->configdata = $field->get('configdata');
            // Preprocess the description.
            $textoptions = $field->get_handler()->get_description_text_options();
            file_prepare_standard_editor($formdata, 'description', $textoptions, $textoptions['context'], 'core_customfield',
                'description', $formdata->id);
        } else {
            $formdata = (object)['categoryid' => $field->get('categoryid'), 'type' => $field->get('type'), 'configdata' => []];
        }
        // Allow field to do more preprocessing (usually for editor or filemanager elements).
        $field->prepare_for_config_form($formdata);
        return $formdata;
    }

    /**
     * Get a list of the course custom fields that support course grouping in
     * block_myoverview
     * @return array $shortname => $name
     */
    public static function get_fields_supporting_course_grouping() {
        global $DB;
        $sql = "
            SELECT f.*
              FROM {customfield_field} f
              JOIN {customfield_category} cat ON cat.id = f.categoryid
             WHERE cat.component = 'core_course' AND cat.area = 'course'
             ORDER BY f.name
        ";
        $ret = [];
        $fields = $DB->get_records_sql($sql);
        foreach ($fields as $field) {
            $inst = field_controller::create(0, $field);
            if ($inst->supports_course_grouping()) {
                $ret[$inst->get('shortname')] = $inst->get('name');
            }
        }
        return $ret;
    }
}
