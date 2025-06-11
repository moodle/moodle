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
 * Feed service.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\webservice;

defined('MOODLE_INTERNAL') || die();

use core_external\external_api;
use core_external\external_value;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use theme_snap\local;

/**
 * Feed service.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ws_feed extends external_api {
    /**
     * @return external_function_parameters
     */
    public static function service_parameters() {
        $parameters = [
            'feedid' => new external_value(PARAM_TEXT, 'Feed identifier', VALUE_REQUIRED),
            'page' => new external_value(PARAM_INT, 'Page', VALUE_DEFAULT),
            'pagesize' => new external_value(PARAM_INT, 'Page size', VALUE_DEFAULT),
            'maxid' => new external_value(PARAM_INT, 'Max item id', VALUE_DEFAULT),
            'courseid' => new external_value(PARAM_INT, 'Course id', VALUE_DEFAULT, 0),
        ];
        return new external_function_parameters($parameters);
    }

    /**
     * @return external_multiple_structure
     */
    public static function service_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'iconUrl'      => new external_value(PARAM_URL, 'URL of icon'),
                'iconDesc'     => new external_value(PARAM_RAW, 'Description of icon'),
                'iconClass'    => new external_value(PARAM_RAW, 'CSS class of icon'),
                'title'        => new external_value(PARAM_RAW, 'Feed item title'),
                'subTitle'     => new external_value(PARAM_RAW, 'Feed item subtitle'),
                'actionUrl'    => new external_value(PARAM_URL, 'Feed item action url'),
                'description'  => new external_value(PARAM_RAW, 'Feed item description'),
                'extraClasses' => new external_value(PARAM_RAW, 'Feed item extra CSS classes'),
                'fromCache'    => new external_value(PARAM_INT, 'Data from cache flag'),
                'itemId'       => new external_value(PARAM_INT, 'Id item we are sending', VALUE_DEFAULT),
                'urlParameter' => new external_value(PARAM_BOOL,'Flag to add URL parameter', VALUE_OPTIONAL),
                'modName'      => new external_value(PARAM_RAW,'Module name', VALUE_OPTIONAL),
            ])
        );
    }

    /**
     * @param string $feedid
     * @param null|int $page
     * @param null|int $pagesize
     * @param int $maxid
     * @param int $courseid
     * @return array
     */
    public static function service($feedid, $page = 0, $pagesize = 3, $maxid = -1, $courseid = 0) {
        $params = self::validate_parameters(self::service_parameters(), [
            'feedid' => $feedid,
            'page' => $page,
            'pagesize' => $pagesize,
            'maxid' => $maxid,
            'courseid' => $courseid,
        ]);
        self::validate_context(\context_system::instance());

        return local::get_feed($params['feedid'], $params['page'], $params['pagesize'], $params['maxid'], $params['courseid']);
    }
}
