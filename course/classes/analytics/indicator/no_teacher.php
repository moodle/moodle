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
 * No teacher indicator.
 *
 * @package   core_course
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\analytics\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * No teacher indicator.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class no_teacher extends \core_analytics\local\indicator\binary {

    public static function get_name() {
        return get_string('indicator:noteacher', 'moodle');
    }

    public static function required_sample_data() {
        // We require course because, although calculate_sample only reads context, we need the context to be course
        // or below.
        return array('context', 'course');
    }

    public function calculate_sample($sampleid, $sampleorigin, $notusedstarttime = false, $notusedendtime = false) {

        $context = $this->retrieve('context', $sampleid);

        $teacherroles = get_config('analytics', 'teacherroles');
        $teacherroleids = explode(',', $teacherroles);
        foreach ($teacherroleids as $role) {
            if (get_role_users($role, $context)) {
                return self::get_max_value();
            }
        }

        return self::get_min_value();
    }
}
