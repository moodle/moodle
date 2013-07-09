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

defined('MOODLE_INTERNAL') || die();

/**
 * Plugin for Moodle 'Toolbar Toggle' button.
 *
 * @package   tinymce_pdw
 * @copyright 2013 Jason Fowler
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tinymce_pdw extends editor_tinymce_plugin {
    protected function update_init_params(array &$params, context $context,
            array $options = null) {

        // Add button before 'undo' in advancedbuttons1.
        $this->add_button_before($params, 1, ' | ', '');
        $this->add_button_before($params, 1, 'pdw_toggle', '');
        $params['pdw_toggle_on'] = 1;
        $params['pdw_toggle_toolbars'] = '2,3';

        // Add JS file, which uses default name.
        $this->add_js_plugin($params);
    }
}
