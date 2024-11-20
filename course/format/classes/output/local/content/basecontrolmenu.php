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

use action_menu;
use action_menu_link_secondary;
use core\output\named_templatable;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use moodle_url;
use pix_icon;
use renderable;
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

    /** @var string the menu ID */
    protected $menuid;

    /** @var action_menu the action menu */
    protected $menu;

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
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return null|array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): ?stdClass {
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
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return action_menu|null the action menu or null if no action menu is available
     */
    public function get_action_menu(\renderer_base $output): ?action_menu {

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
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return action_menu|null the action menu
     */
    public function get_default_action_menu(\renderer_base $output): ?action_menu {
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
        foreach ($controls as $value) {
            $url = empty($value['url']) ? '' : $value['url'];
            $icon = empty($value['icon']) ? '' : $value['icon'];
            $name = empty($value['name']) ? '' : $value['name'];
            $attr = empty($value['attr']) ? [] : $value['attr'];
            $class = empty($value['pixattr']['class']) ? '' : $value['pixattr']['class'];
            $al = new action_menu_link_secondary(
                    new moodle_url($url),
                    new pix_icon($icon, '', null, ['class' => "smallicon " . $class]),
                    $name,
                    $attr
            );
            $menu->add($al);
        }
        return $menu;
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
}
