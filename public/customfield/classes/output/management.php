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
use core_customfield\shared;
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
        global $DB;
        $data = new \stdClass();

        $fieldtypes = $this->handler->get_available_field_types();

        $data->component = $this->handler->get_component();
        $data->area = $this->handler->get_area();
        $data->itemid = $this->handler->get_itemid();
        $data->usescategories = $this->handler->uses_categories();
        $categories = $this->handler->get_categories_with_fields(true);

        // Get all enabled shared categories at once.
        $sharedcategoriesenabled = shared::get_records([
            'component' => $data->component,
            'area' => $data->area,
            'itemid' => $data->itemid,
        ]);

        $categoriesarray = array();
        $movablefieldscount = 0;
        $movablecategoriescount = 0;
        foreach ($categories as $category) {

            $canedit = $data->component === $category->get('component') && $data->area === $category->get('area');

            $categoryarray = array();
            $categoryarray['id'] = $category->get('id');
            $categoryarray['name'] = $category->get_formatted_name();
            $categoryarray['nameeditable'] = $canedit ? $output->render(api::get_category_inplace_editable($category, true)) :
                $category->get_formatted_name();
            $categoryarray['movetitle'] = get_string('movecategory', 'core_customfield',
                $category->get_formatted_name());
            $categoryarray['canedit'] = $canedit;

            $toggleenabled = (bool) array_filter(
                $sharedcategoriesenabled,
                fn($record) => $record->get('categoryid') === $category->get('id')
            );
            $attributes = [
                ['name' => 'data-id', 'value' => $category->get('id')],
                ['name' => 'data-action', 'value' => 'shared-toggle'],
                ['name' => 'data-state', 'value' => $toggleenabled],
                ['name' => 'data-component', 'value' => $data->component],
                ['name' => 'data-area', 'value' => $data->area],
                ['name' => 'data-itemid', 'value' => $data->itemid],
            ];
            if (!$canedit) {
                $categoryarray['toggle'] = $output->render_from_template('core/toggle', [
                    'id' => 'shared-toggle-' . $category->get('id'),
                    'checked' => $toggleenabled,
                    'extraattributes' => $attributes,
                    'label' => get_string('enableplugin', 'core_admin', $category->get_formatted_name()),
                    'labelclasses' => 'visually-hidden',
                ]);
            }

            $categoryarray['fields'] = array();

            foreach ($category->get_fields() as $field) {

                $fieldname = $field->get_formatted_name();
                $fieldarray['type'] = $fieldtypes[$field->get('type')];
                $fieldarray['id'] = $field->get('id');
                $fieldarray['name'] = $fieldname;
                $fieldarray['shortname'] = $field->get('shortname');
                $fieldarray['movetitle'] = get_string('movefield', 'core_customfield', $fieldname);
                $categoryarray['canedit'] = $canedit;

                $categoryarray['fields'][] = $fieldarray;
                if ($canedit) {
                    $movablefieldscount++;
                }
            }

            if ($canedit) {
                $menu = new \action_menu();
                $menu->set_menu_trigger(get_string('createnewcustomfield', 'core_customfield'));

                foreach ($fieldtypes as $type => $fieldname) {
                    $params = [
                        'data-role' => 'addfield',
                        'data-categoryid' => $category->get('id'),
                        'data-type' => $type,
                        'data-typename' => $fieldname,
                    ];
                    $action = new \action_menu_link_secondary(new \core\url('#'), null, $fieldname, $params);
                    $menu->add($action);
                }
                $menu->attributes['class'] .= ' float-start me-1';

                $categoryarray['addfieldmenu'] = $output->render($menu);
                $movablecategoriescount++;
            } else {
                $categoryarray['addfieldmenu'] = '';
            }

            $categoriesarray[] = $categoryarray;
        }

        $data->categories = $categoriesarray;
        $data->canmovecategories = $movablecategoriescount > 1;
        // Can move fields if there are more than one field or if there are multiple categories.
        $data->canmovefields = $movablefieldscount > 1 || $data->canmovecategories;

        if (empty($data->categories)) {
            $data->nocategories = get_string('nocategories', 'core_customfield');
        }

        return $data;
    }
}
