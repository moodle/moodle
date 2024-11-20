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

namespace block_recentlyaccesseditems;

use block_recentlyaccesseditems\external\recentlyaccesseditems_item_exporter;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_value;
use context_user;
use context_module;

/**
 * External API class.
 *
 * @package    block_recentlyaccesseditems
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_recent_items_parameters() {
        return new external_function_parameters(
                array(
                        'limit' => new external_value(PARAM_INT, 'result set limit', VALUE_DEFAULT, 0)
                )
        );
    }

    /**
     * Get last accessed items by the logged user (activities or resources).
     *
     * @param  int $limit Max num of items to return
     * @return array List of items
     * @since Moodle 3.6
     */
    public static function get_recent_items(int $limit = 0) {
        global $USER, $PAGE;

        $userid = $USER->id;

        $params = self::validate_parameters(self::get_recent_items_parameters(),
            array(
                'limit' => $limit,
            )
        );

        $limit = $params['limit'];

        self::validate_context(context_user::instance($userid));

        $items = helper::get_recent_items($limit);

        $renderer = $PAGE->get_renderer('core');
        $recentitems = array_map(function($item) use ($renderer) {
            $context = context_module::instance($item->cmid);
            $exporter = new recentlyaccesseditems_item_exporter($item, ['context' => $context]);
            return $exporter->export($renderer);
        }, $items);

        return $recentitems;
    }

    /**
     * Returns description of method result value
     *
     * @return \core_external\external_description
     * @since Moodle 3.6
     */
    public static function get_recent_items_returns() {
        return new external_multiple_structure(recentlyaccesseditems_item_exporter::get_read_structure(),
                'The most recently accessed activities/resources by the logged user');
    }
}
