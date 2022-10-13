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
 * Provides tool_pluginskel\local\skel\lang_php_file class.
 *
 * @package     tool_pluginskel
 * @subpackage  skel
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_pluginskel\local\skel;

use tool_pluginskel\local\util\exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing the plugin's lang strings file.
 *
 * @copyright 2016 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lang_file extends php_internal_file {

    /**
     * Set the data to be eventually rendered.
     *
     * @param array $data
     */
    public function set_data(array $data) {

        parent::set_data($data);
        $this->data['lang_strings'][] = ['id' => 'pluginname', 'text' => $this->data['name'] ?? $this->data['component']];
    }

    /**
     * Return the data for the template.
     *
     * @return array
     */
    protected function get_template_data() {

        $strings = fullclone($this->data['lang_strings']);
        \core_collator::asort_array_of_arrays_by_key($strings, 'id');
        $this->data['lang_strings'] = array_values($strings);

        return parent::get_template_data();
    }

    /**
     * Returns a list of the variables needed to render the template.
     *
     * @param string $plugintype
     * @return string[]
     */
    public static function get_template_variables($plugintype = null) {

        $templatevars = array(
            array('name' => 'name', 'type' => 'text', 'required' => true),
            array('name' => 'lang_strings', 'type' => 'numeric-array', 'values' => array(
                array('name' => 'id', 'type' => 'text'),
                array('name' => 'text', 'type' => 'text')))
        );

        return $templatevars;
    }
}
