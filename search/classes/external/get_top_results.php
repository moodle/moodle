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

namespace core_search\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use moodle_exception;

/**
 * External function for retrieving top search results.
 *
 * @package    core_search
 * @copyright  2023 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.3
 */
class get_top_results extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {

        $baseparameters = get_results::execute_parameters();

        return new external_function_parameters(
            [
                'query' => $baseparameters->keys['query'],
                'filters' => $baseparameters->keys['filters'],
            ]
        );
    }

    /**
     * Gets top search results based on the provided query and filters.
     *
     * @param string $query the search query
     * @param array $filters filters to apply
     * @return array search results
     */
    public static function execute(string $query, array $filters = []): array {
        global $PAGE;

        $params = self::validate_parameters(self::execute_parameters(),
            [
                'query' => $query,
                'filters' => $filters,
            ]
        );

        $system = \context_system::instance();
        external_api::validate_context($system);

        require_capability('moodle/search:query', $system);

        if (\core_search\manager::is_global_search_enabled() === false) {
            throw new moodle_exception('globalsearchdisabled', 'search');
        }

        $search = \core_search\manager::instance();

        $data = new \stdClass();
        // First, mandatory parameters for consistency with web.
        $data->q = $params['query'];
        $data->title = $params['filters']['title'] ?? '';
        $data->timestart = $params['filters']['timestart'] ?? 0;
        $data->timeend = $params['filters']['timeend'] ?? 0;
        $data->areaids = $params['filters']['areaids'] ?? [];
        $data->courseids = $params['filters']['courseids'] ?? [];
        $data->contextids = $params['filters']['contextids'] ?? [];
        $data->userids = $params['filters']['userids'] ?? [];
        $data->groupids = $params['filters']['groupids'] ?? [];

        $cat = $params['filters']['cat'] ?? '';
        if (\core_search\manager::is_search_area_categories_enabled()) {
            $cat = \core_search\manager::get_search_area_category_by_name($cat);
        }
        if ($cat instanceof \core_search\area_category) {
            $data->cat = $cat->get_name();
        }

        $docs = $search->search_top($data);

        $return = [
            'warnings' => [],
            'results' => []
        ];

        // Convert results to simple data structures.
        if ($docs) {
            foreach ($docs as $doc) {
                $return['results'][] = $doc->export_doc($PAGE->get_renderer('core'));
            }
        }
        return $return;
    }

    /**
     * Webservice returns.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {

        return new external_single_structure(
            [
                'results' => new external_multiple_structure(
                    \core_search\external\document_exporter::get_read_structure()
                ),
            ]
        );
    }
}
