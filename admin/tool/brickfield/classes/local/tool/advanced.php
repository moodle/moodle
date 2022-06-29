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

namespace tool_brickfield\local\tool;

/**
 * Class advanced.
 *
 * @package tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class advanced extends tool {

    /**
     * Provide a name for this tool, suitable for display on pages.
     * @return mixed|string
     * @throws \coding_exception
     */
    public static function toolname() {
        return get_string('advanced:toolname', 'tool_brickfield');
    }

    /**
     * Provide a short name for this tool, suitable for menus and selectors.
     * @return mixed|string
     * @throws \coding_exception
     */
    public static function toolshortname() {
        return get_string('advanced:toolshortname', 'tool_brickfield');
    }

    /**
     * Provide a lowercase name identifying this plugin. Should really be the same as the directory name.
     * @return string
     */
    public function pluginname() {
        return 'advanced';
    }

    /**
     * Return the data for renderer / template display.
     * @return \stdClass
     */
    protected function fetch_data(): \stdClass {
        $data = (object)[
            'valid' => true,
            'error' => '',
        ];

        return $data;
    }
}
