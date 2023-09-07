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
use \core_search\manager;
use moodle_exception;

/**
 * External function for return the list of search areas.
 *
 * @package    core_search
 * @copyright  2023 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.3
 */
class get_search_areas_list extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'cat' => new external_value(PARAM_NOTAGS, 'category to filter areas', VALUE_DEFAULT, ''),
            ]
        );
    }


    /**
     * Return list of search areas.
     *
     * @param string $cat category to filter areas
     * @return array search areas and warnings
     */
    public static function execute(string $cat = ''): array {

        $params = self::validate_parameters(self::execute_parameters(), ['cat' => $cat]);

        $system = \context_system::instance();
        external_api::validate_context($system);

        require_capability('moodle/search:query', $system);

        if (manager::is_global_search_enabled() === false) {
            throw new moodle_exception('globalsearchdisabled', 'search');
        }

        $areas = [];
        $allsearchareas = manager::get_search_area_categories();
        $enabledsearchareas = manager::get_search_areas_list(true);

        foreach ($allsearchareas as $categoryid => $searchareacategory) {
            if (!empty($params['cat']) && $params['cat'] != $categoryid) {
                continue;
            }

            $searchareas = $searchareacategory->get_areas();
            $catname = $searchareacategory->get_visiblename();
            foreach ($searchareas as $areaid => $searcharea) {
                if (key_exists($areaid, $enabledsearchareas)) {
                    $name = $searcharea->get_visible_name();
                    $areas[$name] = ['id' => $areaid, 'name' => $name, 'categoryid' => $categoryid, 'categoryname' => $catname];
                }
            }
        }

        ksort($areas);

        return ['areas' => $areas, 'warnings' => []];
    }

    /**
     * Webservice returns.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(
            [
                'areas' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'id' => new external_value(PARAM_ALPHANUMEXT, 'search area id'),
                            'categoryid' => new external_value(PARAM_NOTAGS, 'category id'),
                            'categoryname' => new external_value(PARAM_NOTAGS, 'category name'),
                            'name' => new external_value(PARAM_TEXT, 'search area name'),
                        ], 'Search area'
                    ), 'Search areas'
                ),
                'warnings' => new external_warnings()
            ]
        );
    }
}
