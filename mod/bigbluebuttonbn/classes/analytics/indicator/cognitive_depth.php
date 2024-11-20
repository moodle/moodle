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
 * Cognitive depth indicator - BigBlueButtonBN.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */

namespace mod_bigbluebuttonbn\analytics\indicator;

use cm_info;
use lang_string;

/**
 * Cognitive depth indicator - bigbluebuttonbn.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cognitive_depth extends activity_base {

    /**
     * Returns the name.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return lang_string
     */
    public static function get_name(): lang_string {
        return new lang_string('indicator:cognitivedepth', 'mod_bigbluebuttonbn');
    }

    /**
     * Returns the indicator type.
     *
     * @return string
     */
    public function get_indicator_type() {
        return self::INDICATOR_COGNITIVE;
    }

    /**
     * Returns the cognitive depth level.
     *
     * @param cm_info $cm
     *
     * @return int
     */
    public function get_cognitive_depth_level(cm_info $cm) {
        return self::COGNITIVE_LEVEL_4;
    }
}
