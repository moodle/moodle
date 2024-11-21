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
 * Serializer.
 *
 * @package    block_xp
 * @copyright  2021 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\serializer;

use block_xp\external\external_multiple_structure;
use block_xp\external\external_single_structure;
use block_xp\external\external_value;
use block_xp\local\xp\levels_info_with_algo;

/**
 * Serializer.
 *
 * @package    block_xp
 * @copyright  2021 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class levels_info_serializer implements serializer_with_read_structure {

    /** @var level_serializer Level serializer. */
    protected $levelserializer;

    /**
     * Constructor.
     *
     * @param level_serializer $levelserializer The level serializer
     */
    public function __construct(level_serializer $levelserializer) {
        $this->levelserializer = $levelserializer;
    }

    /**
     * Serialize.
     *
     * @param mixed $info The levels info.
     * @return array|scalar
     */
    public function serialize($info) {
        return [
            'count' => $info->get_count(),
            // Use array_values() to drop the indexes for json_encode to later create an array.
            'levels' => array_values(array_map([$this->levelserializer, 'serialize'], $info->get_levels())),
            'algo' => $info instanceof levels_info_with_algo ? [
                'base' => $info->get_base(),
                'coef' => $info->get_coef(),
                'incr' => $info->get_incr(),
                'method' => $info->get_method(),
            ] : null,
        ];
    }

    /**
     * Return the structure for external services.
     *
     * @param int $required Value constant.
     * @param scalar $default Default value.
     * @param int $null Whether null is allowed.
     * @return external_value
     */
    public function get_read_structure($required = VALUE_REQUIRED, $default = null, $null = NULL_ALLOWED) {
        return new external_single_structure([
            'count' => new external_value(PARAM_INT),
            'levels' => new external_multiple_structure(
                $this->levelserializer->get_read_structure()
            ),
            'algo' => new external_single_structure([
                'method' => new external_value(PARAM_ALPHAEXT),
                'base' => new external_value(PARAM_INT),
                'coef' => new external_value(PARAM_FLOAT),
                'incr' => new external_value(PARAM_INT),
            ], VALUE_OPTIONAL, null),
        ], '', $required, $default);
    }

}
