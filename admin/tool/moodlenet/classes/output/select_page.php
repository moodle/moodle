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
 * Select page renderable.
 *
 * @package    tool_moodlenet
 * @copyright  2020 Mathew May {@link https://mathew.solutions}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlenet\output;

defined('MOODLE_INTERNAL') || die;

use tool_moodlenet\local\import_info;

/**
 * Select page renderable.
 *
 * @package    tool_moodlenet
 * @copyright  2020 Mathew May {@link https://mathew.solutions}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class select_page implements \renderable, \templatable {

    /** @var import_info $importinfo resource and config information pertaining to an import. */
    protected $importinfo;

    /**
     * Inits the Select page renderable.
     *
     * @param import_info $importinfo resource and config information pertaining to an import.
     */
    public function __construct(import_info $importinfo) {
        $this->importinfo = $importinfo;
    }

    /**
     * Return the import info.
     *
     * @return import_info the import information.
     */
    public function get_import_info(): import_info {
        return $this->importinfo;
    }

    /**
     * Export the data.
     *
     * @param \renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(\renderer_base $output): \stdClass {

        // Prepare the context object.
        return (object) [
            'name' => $this->importinfo->get_resource()->get_name(),
            'type' => $this->importinfo->get_config()->type,
            'cancellink' => new \moodle_url('/my'),
        ];
    }
}
