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

use tool_brickfield\sitedata;

/**
 * Class checktyperesults.
 *
 * @package tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checktyperesults extends tool {

    /**
     * Provide a name for this tool, suitable for display on pages.
     * @return mixed|string
     * @throws \coding_exception
     */
    public static function toolname(): string {
        return get_string('checktyperesults:toolname', 'tool_brickfield');
    }

    /**
     * Provide a short name for this tool, suitable for menus and selectors.
     * @return mixed|string
     * @throws \coding_exception
     */
    public static function toolshortname(): string {
        return get_string('checktyperesults:toolshortname', 'tool_brickfield');
    }

    /**
     * Provide a lowercase name identifying this plugin. Should really be the same as the directory name.
     * @return string
     */
    public function pluginname(): string {
        return 'checktyperesults';
    }

    /**
     * Return the data for renderer / template display.
     * @return \stdClass
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function fetch_data(): \stdClass {
        $filter = $this->get_filter();
        if (!$filter->validate_filters()) {
            return (object)[
                'valid' => false,
                'error' => $filter->get_errormessage(),
            ];

        }
        $data = (object)[
            'valid' => true,
            'error' => '',
            'data' => (new sitedata())->get_checkgroup_data($filter),
        ];
        if ($filter->categoryid != 0) {
            $data->countdata = count($filter->courseids);
        } else {
            $data->countdata = sitedata::get_total_courses_checked();
        }

        return $data;
    }
}
