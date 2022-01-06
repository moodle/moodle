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
 * External interface library for customfields component
 *
 * @package   core_customfield
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . "/externallib.php");

/**
 * Class core_customfield_external
 *
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_customfield_external extends external_api {

    /**
     * Parameters for delete_field
     *
     * @return external_function_parameters
     */
    public static function delete_field_parameters() {
        return new external_function_parameters(
                array('id' => new external_value(PARAM_INT, 'Custom field ID to delete', VALUE_REQUIRED))
        );
    }

    /**
     * Delete custom field function
     *
     * @param int $id
     */
    public static function delete_field($id) {
        $params = self::validate_parameters(self::delete_field_parameters(), ['id' => $id]);

        $record = \core_customfield\field_controller::create($params['id']);
        $handler = $record->get_handler();
        if (!$handler->can_configure()) {
            throw new moodle_exception('nopermissionconfigure', 'core_customfield');
        }
        $handler->delete_field_configuration($record);
    }

    /**
     * Return for delete_field
     */
    public static function delete_field_returns() {
    }

    /**
     * Parameters for reload template function
     *
     * @return external_function_parameters
     */
    public static function reload_template_parameters() {
        return new external_function_parameters(
            array(
                'component' => new external_value(PARAM_COMPONENT, 'component', VALUE_REQUIRED),
                'area' => new external_value(PARAM_ALPHANUMEXT, 'area', VALUE_REQUIRED),
                'itemid' => new external_value(PARAM_INT, 'itemid', VALUE_REQUIRED)
            )
        );
    }

    /**
     * Reload template function
     *
     * @param string $component
     * @param string $area
     * @param int $itemid
     * @return array|object|stdClass
     */
    public static function reload_template($component, $area, $itemid) {
        global $PAGE;

        $params = self::validate_parameters(self::reload_template_parameters(),
                      ['component' => $component, 'area' => $area, 'itemid' => $itemid]);

        $PAGE->set_context(context_system::instance());
        $handler = \core_customfield\handler::get_handler($params['component'], $params['area'], $params['itemid']);
        self::validate_context($handler->get_configuration_context());
        if (!$handler->can_configure()) {
            throw new moodle_exception('nopermissionconfigure', 'core_customfield');
        }
        $output = $PAGE->get_renderer('core_customfield');
        $outputpage = new \core_customfield\output\management($handler);
        return $outputpage->export_for_template($output);
    }

    /**
     * Ajax returns on reload template.
     *
     * @return external_single_structure
     */
    public static function reload_template_returns() {
        return new external_single_structure(
            array(
                'component' => new external_value(PARAM_COMPONENT, 'component'),
                'area' => new external_value(PARAM_ALPHANUMEXT, 'area'),
                'itemid' => new external_value(PARAM_INT, 'itemid'),
                'usescategories' => new external_value(PARAM_BOOL, 'view has categories'),
                'categories' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'id'),
                            'nameeditable' => new external_value(PARAM_RAW, 'inplace editable name'),
                            'addfieldmenu' => new external_value(PARAM_RAW, 'addfieldmenu'),
                            'fields' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'name' => new external_value(PARAM_NOTAGS, 'name'),
                                        'shortname' => new external_value(PARAM_NOTAGS, 'shortname'),
                                        'type' => new external_value(PARAM_NOTAGS, 'type'),
                                        'editfieldurl' => new external_value(PARAM_URL, 'edit field url'),
                                        'id' => new external_value(PARAM_INT, 'id'),
                                    )
                                )
                            , '', VALUE_OPTIONAL),
                        )
                    )
                ),
            )
        );
    }

    /**
     * Parameters for delete category
     *
     * @return external_function_parameters
     */
    public static function delete_category_parameters() {
        return new external_function_parameters(
                array('id' => new external_value(PARAM_INT, 'category ID to delete', VALUE_REQUIRED))
        );
    }

    /**
     * Delete category function
     *
     * @param int $id
     */
    public static function delete_category($id) {
        $category = core_customfield\category_controller::create($id);
        $handler = $category->get_handler();
        self::validate_context($handler->get_configuration_context());
        if (!$handler->can_configure()) {
            throw new moodle_exception('nopermissionconfigure', 'core_customfield');
        }
        $handler->delete_category($category);
    }

    /**
     * Return for delete category
     */
    public static function delete_category_returns() {
    }


    /**
     * Parameters for create category
     *
     * @return external_function_parameters
     */
    public static function create_category_parameters() {
        return new external_function_parameters(
            array(
                'component' => new external_value(PARAM_COMPONENT, 'component', VALUE_REQUIRED),
                'area' => new external_value(PARAM_ALPHANUMEXT, 'area', VALUE_REQUIRED),
                'itemid' => new external_value(PARAM_INT, 'itemid', VALUE_REQUIRED)
            )
        );
    }

    /**
     * Create category function
     *
     * @param string $component
     * @param string $area
     * @param int    $itemid
     * @return mixed
     */
    public static function create_category($component, $area, $itemid) {
        $params = self::validate_parameters(self::create_category_parameters(),
            ['component' => $component, 'area' => $area, 'itemid' => $itemid]);

        $handler = \core_customfield\handler::get_handler($params['component'], $params['area'], $params['itemid']);
        self::validate_context($handler->get_configuration_context());
        if (!$handler->can_configure()) {
            throw new moodle_exception('nopermissionconfigure', 'core_customfield');
        }
        return $handler->create_category();
    }

    /**
     * Return for create category
     */
    public static function create_category_returns() {
        return new external_value(PARAM_INT, 'Id of the category');
    }

    /**
     * Parameters for move field.
     *
     * @return external_function_parameters
     */
    public static function move_field_parameters() {
        return new external_function_parameters(
                ['id' => new external_value(PARAM_INT, 'Id of the field to move', VALUE_REQUIRED),
                 'categoryid' => new external_value(PARAM_INT, 'New parent category id', VALUE_REQUIRED),
                 'beforeid'   => new external_value(PARAM_INT, 'Id of the field before which it needs to be moved',
                     VALUE_DEFAULT, 0)]
        );
    }

    /**
     * Move/reorder field. Move a field to another category and/or change sortorder of fields
     *
     * @param int $id field id
     * @param int $categoryid
     * @param int $beforeid
     */
    public static function move_field($id, $categoryid, $beforeid) {
        $params = self::validate_parameters(self::move_field_parameters(),
            ['id' => $id, 'categoryid' => $categoryid, 'beforeid' => $beforeid]);
        $field = \core_customfield\field_controller::create($params['id']);
        $handler = $field->get_handler();
        self::validate_context($handler->get_configuration_context());
        if (!$handler->can_configure()) {
            throw new moodle_exception('nopermissionconfigure', 'core_customfield');
        }
        $handler->move_field($field, $params['categoryid'], $params['beforeid']);
    }

    /**
     * Return for move field
     */
    public static function move_field_returns() {
    }

    /**
     * Return for move category
     *
     * @return external_function_parameters
     */
    public static function move_category_parameters() {
        return new external_function_parameters(
                ['id' => new external_value(PARAM_INT, 'Category ID to move', VALUE_REQUIRED),
                 'beforeid'   => new external_value(PARAM_INT, 'Id of the category before which it needs to be moved',
                     VALUE_DEFAULT, 0)]
        );
    }

    /**
     * Reorder categories. Move category to the new position
     *
     * @param int $id category id
     * @param int $beforeid
     */
    public static function move_category(int $id, int $beforeid) {
        $params = self::validate_parameters(self::move_category_parameters(),
            ['id' => $id, 'beforeid' => $beforeid]);
        $category = core_customfield\category_controller::create($id);
        $handler = $category->get_handler();
        self::validate_context($handler->get_configuration_context());
        if (!$handler->can_configure()) {
            throw new moodle_exception('nopermissionconfigure', 'core_customfield');
        }
        $handler->move_category($category, $params['beforeid']);
    }

    /**
     * Return for move category
     */
    public static function move_category_returns() {
    }
}
