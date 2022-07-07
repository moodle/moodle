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
 * Adhoc task that clean up data related ro deleted search area.
 *
 * @package    core
 * @copyright  2019 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Class that cleans up data related to deleted search area.
 *
 * Custom data accepted:
 *  - areaid -> String search area id .
 *
 * @package     core
 * @copyright   2019 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class clean_up_deleted_search_area_task extends adhoc_task {

    /**
     * Run the task to clean up deleted search are data.
     */
    public function execute() {
        $areaid = $this->get_custom_data();

        try {
            \core_search\manager::clean_up_non_existing_area($areaid);
        } catch (\core_search\engine_exception $e) {
            mtrace('Search is not configured. Skip deleting index for search area ' . $areaid);
        }
    }
}
