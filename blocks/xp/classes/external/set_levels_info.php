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
 * External function.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\external;

use block_xp\di;
use block_xp\local\xp\algo_levels_info;
use core_text;

/**
 * External function.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_levels_info extends external_api {

    /**
     * External function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT),
            'levels' => new external_multiple_structure(new external_single_structure([
                'level' => new external_value(PARAM_INT),
                'xprequired' => new external_value(PARAM_INT),
                'metadata' => new external_multiple_structure(new external_single_structure([
                    'name' => new external_value(PARAM_ALPHAEXT),
                    'value' => new external_value(PARAM_RAW, '', VALUE_OPTIONAL, null),
                ]), '', VALUE_DEFAULT, []),
                // Keps for backwards compatibility, but no longer used.
                'name' => new external_value(PARAM_NOTAGS, '', VALUE_DEFAULT, ''),
                'description' => new external_value(PARAM_NOTAGS, '', VALUE_DEFAULT, ''),
            ])),
            'algo' => new external_single_structure([
                'method' => new external_value(PARAM_ALPHANUMEXT),
                'base' => new external_value(PARAM_INT),
                'incr' => new external_value(PARAM_INT),
                'coef' => new external_value(PARAM_FLOAT),
            ]),
        ]);
    }

    /**
     * Allow AJAX use.
     *
     * @return true
     */
    public static function execute_is_allowed_from_ajax() {
        return true;
    }

    /**
     * External function.
     *
     * @param int $courseid The course ID.
     * @param array $levels The levels.
     * @param array $algo The algo.
     * @return object
     */
    public static function execute($courseid, $levels, $algo) {
        global $USER;
        $params = self::validate_parameters(self::execute_parameters(), compact('courseid', 'levels', 'algo'));

        // Pre-checks.
        $worldfactory = di::get('course_world_factory');
        $world = $worldfactory->get_world($courseid);
        $courseid = $world->get_courseid(); // Ensure that we get the real course ID.
        self::validate_context($world->get_context());

        // Permission checks.
        $perms = $world->get_access_permissions();
        $perms->require_manage();

        // Save the things.
        $writer = di::get('levels_info_writer');
        $writer->save_for_world($world, [
            'levels' => $params['levels'],
            'algo' => $params['algo'],
        ]);

        return (object) ['success' => true];
    }

    /**
     * External function return definition.
     *
     * @return external_description
     */
    public static function execute_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL),
        ]);
    }

    /**
     * Clean levels info data.
     *
     * @param array $levels The levels.
     * @param array $algo The algo.
     * @deprecated Since Level Up XP 3.15, use the levels_info_writer instead.
     */
    public static function clean_levels_info_data($levels, $algo) {
        // Sort levels.
        usort($levels, function($l1, $l2) {
            return $l1['level'] - $l2['level'];
        });

        // Pseudo validation, we basically ignore errors.
        if (count($levels) < 2 || count($levels) > 99) {
            $levelsinfo = algo_levels_info::make_from_defaults();

        } else {
            $lastpts = null;
            $levelsdata = array_reduce(array_keys($levels), function($carry, $key) use ($levels, &$lastpts) {
                $level = $levels[$key];
                $levelnb = $level['level'];

                if ($lastpts === null) {
                    $xp = 0;
                } else {
                    $xp = min(max($lastpts + 1, $level['xprequired']), PHP_INT_MAX);
                }

                $carry['xp'][$levelnb] = $xp;
                if (!empty($level['name'])) {
                    $carry['name'][$levelnb] = core_text::substr($level['name'], 0, 40);
                }
                if (!empty($level['description'])) {
                    $carry['desc'][$levelnb] = core_text::substr($level['description'], 0, 280);
                }

                $lastpts = $xp;
                return $carry;
            }, ['xp' => [], 'name' => [], 'desc' => []]);

            // Normalise data if it's incorrect.
            $algo['base'] = min(max(1, $algo['base']), PHP_INT_MAX);
            $algo['coef'] = min(max(1, $algo['coef']), PHP_INT_MAX);
            $algo['incr'] = min(max(0, $algo['incr']), PHP_INT_MAX);
            $algo['method'] = !in_array($algo['method'], ['flat', 'linear', 'relative']) ? 'relative' : $algo['method'];

            $levelsinfo = new algo_levels_info([
                'xp' => $levelsdata['xp'],
                'desc' => $levelsdata['desc'],
                'name' => $levelsdata['name'],
                'base' => $algo['base'],
                'coef' => $algo['coef'],
                'incr' => $algo['incr'],
                'method' => $algo['method'],
            ]);
        }

        return $levelsinfo;
    }

}
