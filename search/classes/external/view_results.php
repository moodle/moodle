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
use core_external\external_value;
use core_external\external_warnings;
use moodle_exception;

/**
 * External function for trigger view search results event.
 *
 * @package    core_search
 * @copyright  2023 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.3
 */
class view_results extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'query' => new external_value(PARAM_NOTAGS, 'the search query'),
                'filters' => new external_single_structure(
                    [
                        'title' => new external_value(PARAM_NOTAGS, 'result title', VALUE_OPTIONAL),
                        'areaids' => new external_multiple_structure(
                            new external_value(PARAM_RAW, 'areaid'), 'restrict results to these areas', VALUE_DEFAULT, []
                        ),
                        'courseids' => new external_multiple_structure(
                            new external_value(PARAM_INT, 'courseid'), 'restrict results to these courses', VALUE_DEFAULT, []
                        ),
                        'timestart' => new external_value(PARAM_INT, 'docs modified after this date', VALUE_DEFAULT, 0),
                        'timeend' => new external_value(PARAM_INT, 'docs modified before this date', VALUE_DEFAULT, 0)
                    ], 'filters to apply', VALUE_DEFAULT, []
                ),
                'page' => new external_value(PARAM_INT, 'results page number starting from 0, defaults to the first page',
                    VALUE_DEFAULT, 0)
            ]
        );
    }

    /**
     * Trigger view results event.
     *
     * @param string $query the search query
     * @param array $filters filters to apply
     * @param int $page results page
     * @return array status and warnings
     */
    public static function execute(string $query, array $filters = [], int $page = 0): array {

        $params = self::validate_parameters(self::execute_parameters(),
            [
                'query' => $query,
                'filters' => $filters,
                'page' => $page,
            ]
        );

        $system = \context_system::instance();
        external_api::validate_context($system);

        require_capability('moodle/search:query', $system);

        if (\core_search\manager::is_global_search_enabled() === false) {
            throw new moodle_exception('globalsearchdisabled', 'search');
        }

        $filters = new \stdClass();
        $filters->title = $params['filters']['title'] ?? '';
        $filters->timestart = $params['filters']['timestart'] ?? 0;
        $filters->timeend = $params['filters']['timeend'] ?? 0;
        $filters->areaids = $params['filters']['areaids'] ?? [];
        $filters->courseids = $params['filters']['courseids'] ?? [];

        \core_search\manager::trigger_search_results_viewed([
            'q' => $params['query'],
            'page' => $params['page'],
            'title' => !empty($filters->title) ? $filters->title : '',
            'areaids' => !empty($filters->areaids) ? $filters->areaids : [],
            'courseids' => !empty($filters->courseids) ? $filters->courseids : [],
            'timestart' => isset($filters->timestart) ? $filters->timestart : 0,
            'timeend' => isset($filters->timeend) ? $filters->timeend : 0
        ]);

        return ['status' => true, 'warnings' => []];
    }

    /**
     * Webservice returns.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(
            [
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            ]
        );
    }
}
