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
 * Customfield component output.
 *
 * @package   core_customfield
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_customfield\output;

use core_customfield\api;
use core_customfield\handler;
use renderable;
use templatable;

defined('MOODLE_INTERNAL') || die;

/**
 * Class management
 *
 * @package core_customfield
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class management implements renderable, templatable {

    /**
     * @var handler
     */
    protected $handler;
    /**
     * @var
     */
    protected $categoryid;

    /**
     * management constructor.
     *
     * @param \core_customfield\handler $handler
     */
    public function __construct(\core_customfield\handler $handler) {
        $this->handler = $handler;
    }

    /**
     * Export for template
     *
     * @param \renderer_base $output
     * @return array|object|\stdClass
     */
    public function export_for_template(\renderer_base $output) {
        $data = new \stdClass();

        $fieldtypes = $this->handler->get_available_field_types();

        $data->component = $this->handler->get_component();
        $data->area = $this->handler->get_area();
        $data->itemid = $this->handler->get_itemid();
        $data->usescategories = $this->handler->uses_categories();
        $categories = $this->handler->get_categories_with_fields();

        $categoriesarray = array();

        foreach ($categories as $category) {

            $categoryarray = array();
            $categoryarray['id'] = $category->get('id');
            $categoryarray['nameeditable'] = $output->render(api::get_category_inplace_editable($category, true));
            $categoryarray['movetitle'] = get_string('movecategory', 'core_customfield',
                $category->get_formatted_name());

            $categoryarray['fields'] = array();

            foreach ($category->get_fields() as $field) {

                $fieldname = $field->get_formatted_name();
                $fieldarray['type'] = $fieldtypes[$field->get('type')];
                $fieldarray['id'] = $field->get('id');
                $fieldarray['name'] = $fieldname;
                $fieldarray['shortname'] = $field->get('shortname');
                $fieldarray['movetitle'] = get_string('movefield', 'core_customfield', $fieldname);

                $categoryarray['fields'][] = $fieldarray;
            }

            $menu = new \action_menu();
            $menu->set_menu_trigger(get_string('createnewcustomfield', 'core_customfield'));

            foreach ($fieldtypes as $type => $fieldname) {
                $action = new \action_menu_link_secondary(new \moodle_url('#'), null, $fieldname,
                    ['data-role' => 'addfield', 'data-categoryid' => $category->get('id'), 'data-type' => $type,
                        'data-typename' => $fieldname]);
                $menu->add($action);
            }
            $menu->attributes['class'] .= ' float-left mr-1';

            $categoryarray['addfieldmenu'] = $output->render($menu);

            $categoriesarray[] = $categoryarray;
        }

        $data->categories = $categoriesarray;

        if (empty($data->categories)) {
            $data->nocategories = get_string('nocategories', 'core_customfield');
        }

        return $data;
    }
}
