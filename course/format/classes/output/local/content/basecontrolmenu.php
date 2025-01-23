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

namespace core_courseformat\output\local\content;

use core\context\course as context_course;
use core\output\action_menu;
use core\output\action_menu\link_secondary;
use core\output\named_templatable;
use core\output\pix_icon;
use core\output\renderable;
use core\output\renderer_base;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use core\url;
use section_info;
use cm_info;
use stdClass;

/**
 * Base class to render course element controls.
 *
 * @package   core_courseformat
 * @copyright 2024 Amaia Anabitarte <amaia@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class basecontrolmenu implements named_templatable, renderable {

    use courseformat_named_templatable;

    /** @var course_format the course format class */
    protected $format;

    /** @var section_info the course section class */
    protected $section;

    /** @var cm_info the course module class */
    protected $mod;

    /** @var stdClass the course instance */
    protected stdClass $course;

    /** @var context_course the course context */
    protected $coursecontext;

    /** @var string the menu ID */
    protected $menuid;

    /** @var action_menu the action menu */
    protected $menu;

    /** @var url The base URL for the course or the section */
    protected url $baseurl;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @param cm_info|null $mod the module info
     * @param string $menuid the ID value for the menu
     */
    public function __construct(course_format $format, section_info $section, ?cm_info $mod = null, string $menuid = '') {
        $this->format = $format;
        $this->section = $section;
        $this->mod = $mod;
        $this->menuid = $menuid;
        $this->course = $format->get_course();
        $this->coursecontext = $format->get_context();
        $this->baseurl = $format->get_view_url($format->get_sectionnum(), ['navigation' => true]);
    }

    /**
     * Change the default base URL to return after each action.
     *
     * @param url $baseurl
     */
    public function set_baseurl(url $baseurl) {
        $this->baseurl = $baseurl;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return null|array data context for a mustache template
     */
    public function export_for_template(renderer_base $output): ?stdClass {
        $menu = $this->get_action_menu($output);
        if (empty($menu)) {
            return new stdClass();
        }

        $data = (object)[
            'menu' => $output->render($menu),
            'hasmenu' => true,
            'id' => $this->menuid,
        ];

        return $data;
    }

    /**
     * Generate the action menu element.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return action_menu|null the action menu or null if no action menu is available
     */
    public function get_action_menu(renderer_base $output): ?action_menu {

        if (!empty($this->menu)) {
            return $this->menu;
        }

        $this->menu = $this->get_default_action_menu($output);
        return $this->menu;
    }

    /**
     * Generate the default action menu.
     *
     * This method is public in case some block needs to modify the menu before output it.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return action_menu|null the action menu
     */
    public function get_default_action_menu(renderer_base $output): ?action_menu {
        return null;
    }

    /**
     * Format control array into an action_menu.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return action_menu|null the action menu
     */
    protected function format_controls(array $controls): ?action_menu {
        if (empty($controls)) {
            return null;
        }

        $menu = new action_menu();
        $menu->set_kebab_trigger(get_string('edit'));
        $menu->attributes['class'] .= ' section-actions';
        $menu->attributes['data-sectionid'] = $this->section->id;
        foreach ($controls as $item) {
            // Actions not available for the user can be null.
            if ($item === null) {
                continue;
            }

            // TODO remove this if as part of MDL-83530.
            if (is_array($item)) {
                // Some third party formats from 4.5 and older can use array to define the action menu items.
                $item = $this->normalize_action_menu_link($item);
            }

            $menu->add($item);
        }
        return $menu;
    }

    /**
     * Nromalize the action menu item, or return null if it is not possible.
     *
     * Traditionally, this class uses array to define the action menu items,
     * for backward compatibility, this method will normalize the array into
     * the correct action_menu_link object.
     *
     * @todo Remove this method in Moodle 6.0 (MDL-83530).
     * @param array|null $itemdata the item data
     * @return void
     */
    private function normalize_action_menu_link(
        array|null $itemdata
    ): ?link_secondary {
        debugging(
            "Using arrays as action menu items is deprecated, use a compatible menu item instead.",
            DEBUG_DEVELOPER
        );
        if (empty($itemdata)) {
            return null;
        }
        $url = empty($itemdata['url']) ? '' : $itemdata['url'];
        $icon = empty($itemdata['icon']) ? '' : $itemdata['icon'];
        $name = empty($itemdata['name']) ? '' : $itemdata['name'];
        $attr = empty($itemdata['attr']) ? [] : $itemdata['attr'];
        $class = empty($itemdata['pixattr']['class']) ? '' : $itemdata['pixattr']['class'];
        return new link_secondary(
            url: new url($url),
            icon: new pix_icon($icon, '', null, ['class' => "smallicon " . $class]),
            text: $name,
            attributes: $attr,
        );
    }

    /**
     * Generate the edit control items of a section.
     *
     * This method must remain public until the final deprecation of section_edit_control_items.
     *
     * @return array of edit control items
     */
    public function section_control_items() {
        return [];
    }

    /**
     * Adds a new control item after a given control item.
     *
     * If the control item is not found, the new control item is added at the beginning.
     *
     * @param array $controls array of edit control items
     * @param string $aftername name of the control item after which the new control item will be added
     * @param string $newkey key of the new control item
     * @param mixed $newcontrol new control item to be added (anything compatible with an action menu or null)
     */
    protected function add_control_after(array $controls, string $aftername, string $newkey, mixed $newcontrol): array {
        if (!array_key_exists($aftername, $controls)) {
            return array_merge([$newkey => $newcontrol], $controls);
        }
        $newcontrols = [];
        $found = false;
        foreach ($controls as $keyname => $control) {
            $newcontrols[$keyname] = $control;
            if ($keyname === $aftername) {
                $newcontrols[$newkey] = $newcontrol;
                $found = true;
            }
        }
        if (!$found) {
            $newcontrols[$newkey] = $newcontrol;
        }
        return $newcontrols;
    }
}
