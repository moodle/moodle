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
 * Renderer class for tool customlang
 *
 * @package     tool_customlang
 * @category    output
 * @copyright   2019 Bas Brands <bas@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_customlang\output;

defined('MOODLE_INTERNAL') || die();


/**
 * Renderer for the customlang tool.
 *
 * @copyright 2019 Bas Brands <bas@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Defer to template.
     *
     * @param tool_customlang_translator $translator
     * @return string Html for the translator
     */
    protected function render_tool_customlang_translator(\tool_customlang_translator $translator) {
        $renderabletranslator = new translator($translator);
        $templatevars = $renderabletranslator->export_for_template($this);
        return $this->render_from_template('tool_customlang/translator', $templatevars);
    }

    /**
     * Defer to template.
     *
     * @param tool_customlang_menu $menu
     * @return string html the customlang menu buttons
     */
    protected function render_tool_customlang_menu(\tool_customlang_menu $menu) {
        $output = '';
        foreach ($menu->get_items() as $item) {
            $output .= $this->single_button($item->url, $item->title, $item->method);
        }
        return $this->box($output, 'menu');
    }
}
