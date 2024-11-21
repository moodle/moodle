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
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\serializer;

use block_xp\external\external_single_structure;
use block_xp\external\external_value;
use block_xp\local\xp\level_with_badge;
use block_xp\local\xp\level_with_description;
use block_xp\local\xp\level_with_name;

/**
 * Serializer.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class level_serializer implements serializer_with_read_structure {

    /** @var url_serializer URL serializer. */
    protected $urlserializer;

    /**
     * Constructor.
     *
     * @param url_serializer $urlserializer The URL serializer.
     */
    public function __construct(url_serializer $urlserializer) {
        $this->urlserializer = $urlserializer;
    }

    /**
     * Serialize.
     *
     * @param mixed $level The level.
     * @return array
     */
    public function serialize($level) {
        $url = $level instanceof level_with_badge ? $level->get_badge_url() : null;
        return [
            'level' => $level->get_level(),
            'xprequired' => $level->get_xp_required(),
            'badgeurl' => $url ? $this->urlserializer->serialize($url) : $url,
            'name' => $level instanceof level_with_name ? $level->get_name() : null,
            'description' => $level instanceof level_with_description ? $level->get_description() : null,
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
            'level' => new external_value(PARAM_INT),
            'xprequired' => new external_value(PARAM_INT),
            'badgeurl' => $this->urlserializer->get_read_structure(),
            'name' => new external_value(PARAM_NOTAGS),
            'description' => new external_value(PARAM_NOTAGS),
        ], '', $required, $default);
    }

}
