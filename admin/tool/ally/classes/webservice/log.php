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
 * Return log data.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\webservice;

use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../../../../lib/externallib.php');

/**
 * Return log data.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class log extends \external_api {
    use user_fill_from_context_error;

    /**
     * @return \external_function_parameters
     */
    public static function service_parameters() {
        return new \external_function_parameters([
            'query'    => new \external_value(PARAM_TEXT, 'query', VALUE_DEFAULT, null)
        ]);
    }

    /**
     * @return \external_multiple_structure
     */
    public static function service_returns() {
        return new \external_single_structure([
            'columns' => new \external_multiple_structure(
                new \external_single_structure([
                    'field'        => new \external_value(PARAM_ALPHANUMEXT, 'Column field'),
                    'title'        => new \external_value(PARAM_TEXT, 'Column title'),
                    'sortable'     => new \external_value(PARAM_BOOL, 'Column sortable'),
                    'tdComp'       => new \external_value(PARAM_ALPHANUMEXT, 'Row cell component', VALUE_OPTIONAL),
                    'thComp'       => new \external_value(PARAM_ALPHANUMEXT, 'Header cell component', VALUE_OPTIONAL)
                ])
            ),
            'data' => new \external_multiple_structure(
                new \external_single_structure([
                    'id'           => new \external_value(PARAM_INT, 'Log row id'),
                    'time'         => new \external_value(PARAM_TEXT, 'Time of log entry'),
                    'level'        => new \external_value(PARAM_ALPHA, 'Log level'),
                    'code'         => new \external_value(PARAM_TEXT, 'Message code'),
                    'details'      => new \external_single_structure([
                        'message'      => new \external_value(PARAM_RAW, 'Log row message'),
                        'explanation'  => new \external_value(PARAM_TEXT, 'Log row explanation'),
                        'data'         => new \external_value(PARAM_RAW, 'Log row data'),
                        'exception'    => new \external_value(PARAM_TEXT, 'Log row exception')
                    ])
                ])
            ),
            'query' => new \external_single_structure([
                'limit'            => new \external_value(PARAM_INT, 'Records limit'),
                'offset'           => new \external_value(PARAM_INT, 'Records offset'),
                'sort'             => new \external_value(PARAM_ALPHANUMEXT, 'Field to sort on', VALUE_OPTIONAL),
                'order'            => new \external_value(PARAM_ALPHA, 'Sort direction', VALUE_OPTIONAL)
            ]),
            'total' => new \external_value(PARAM_INT, 'Total records')
        ]);
    }

    /**
     * @param int $page
     * @param int $perpage
     * @return array
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     */
    public static function service($query) {
        global $DB;

        self::validate_context(\context_system::instance());
        require_capability('tool/ally:viewlogs', \context_system::instance());

        if ($query === null) {
            $query = (object) [
                'limit' => 20,
                'offset' => 0,
                'sort' => null,
                'order' => null
            ];
        } else {
            $query = json_decode($query);
        }

        $columns = [
            [
                'field' => 'id',
                'title' => get_string('id', 'tool_ally'),
                'sortable' => true
            ],
            [
                'field' => 'time',
                'title' => get_string('time'),
                'sortable' => true
            ],
            [
                'field' => 'level',
                'title' => get_string('level', 'tool_ally'),
                'sortable' => true
            ],
            [
                'field' => 'code',
                'title' => get_string('code', 'tool_ally'),
                'sortable' => true
            ],
            [
                'field' => 'details',
                'title' => get_string('message', 'tool_ally'),
                'sortable' => false,
                'tdComp' => 'tdLogDetails'
            ]
        ];

        $total = $DB->count_records('tool_ally_log');
        $sort = '';
        if ($query->sort && $query->order) {
            $sort = $query->sort.' '.$query->order;
        }
        $rs = $DB->get_records('tool_ally_log', null, $sort, '*', $query->offset, $query->limit);
        $data = [];
        foreach ($rs as $row) {
            $row->time = userdate($row->time);
            $details = new stdClass;
            $details->message = null;
            $details->data = null;
            $details->explanation = null;

            if (strpos($row->code, 'logger:') === 0) {
                $details->message = get_string($row->code, 'tool_ally');
            }
            if ($row->data) {
                $rowdata = unserialize($row->data);
                $details->data = '<pre>'.var_export($rowdata, true).'</pre>';
            }
            $details->exception = !empty(trim($row->exception)) ? $row->exception : null;
            $details->explanation = !empty(trim($row->explanation)) ? $row->explanation : null;
            $row->details = $details;
            $data[] = $row;
        }
        $return = [
            'columns' => $columns,
            'data' => $data,
            'query' => $query,
            'total' => $total
        ];

        return $return;
    }
}
