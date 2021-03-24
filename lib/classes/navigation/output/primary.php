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

namespace core\navigation\output;

use renderable;
use renderer_base;
use templatable;
use custom_menu;

/**
 * Primary navigation renderable
 *
 * This file combines primary nav, custom menu, lang menu and
 * usermenu into a standardized format for the frontend
 *
 * @package     core
 * @category    navigation
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class primary implements renderable, templatable {
    /** @var moodle_page $page the moodle page that the navigation belongs to */
    private $page = null;

    /**
     * primary constructor.
     * @param \moodle_page $page
     */
    public function __construct($page) {
        $this->page = $page;
    }

    /**
     * Combine the various menus into a standardized output.
     *
     * @param renderer_base|null $output
     * @return array
     */
    public function export_for_template(?renderer_base $output = null): array {
        if (!$output) {
            $output = $this->page->get_renderer('core');
        }

        return [
            'primary' => $this->get_primary_nav(),
            'custom' => $this->get_custom_menu($output),
            'lang' => $this->get_lang_menu($output),
            'user' => $this->get_user_menu(),
        ];
    }

    /**
     * Get the primary nav object and standardize the output
     *
     * @return array
     */
    protected function get_primary_nav(): array {
        $nodes = [];
        foreach ($this->page->primarynav->children as $node) {
            $nodes[] = [
                'title' => $node->get_title(),
                'url' => $node->action(),
                'text' => $node->text,
                'icon' => $node->icon,
                'isactive' => $node->isactive,
            ];
        }

        return $nodes;
    }

    /**
     * Custom menu items reside on the same level as the original nodes.
     * Fetch and convert the nodes to a standardised array.
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_custom_menu(renderer_base $output): array {
        global $CFG;

        // Early return if a custom menu does not exists.
        if (empty($CFG->custommenuitems)) {
            return [];
        }

        $custommenuitems = $CFG->custommenuitems;
        $currentlang = current_language();
        $custommenunodes = custom_menu::convert_text_to_menu_nodes($custommenuitems, $currentlang);
        $nodes = [];
        foreach ($custommenunodes as $node) {
            $nodes[] = $node->export_for_template($output);
        }

        return $nodes;
    }

    /**
     * Get a list of options for the lang picker.
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_lang_menu(renderer_base $output): array {
        // Early return if a lang menu does not exists.
        if (empty($output->lang_menu())) {
            return [];
        }

        $currentlang = current_language();
        $langs = get_string_manager()->get_list_of_translations();
        $nodes = [];

        // Add the lang picker if needed.
        foreach ($langs as $langtype => $langname) {
            $isactive = $langtype == $currentlang;
            $node = [
                'title' => $langname,
                'text' => $langname,
                'isactive' => $isactive,
                'url' => $isactive ? new \moodle_url('#') : new \moodle_url($this->page->url, ['lang' => $langtype]),
            ];

            $nodes[] = $node;
        }

        return $nodes;
    }

    /**
     * Get/Generate the user menu
     *
     * @return array
     */
    public function get_user_menu(): array {
        // Empty stub to add to.
        return [];
    }
}
