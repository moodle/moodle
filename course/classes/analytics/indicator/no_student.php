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
 * No student indicator.
 *
 * @package   core_course
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\analytics\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * No student indicator.
 *
 * @package   core_course
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class no_student extends \core_analytics\local\indicator\binary {

    /**
     * Student role ids.
     *
     * @var array|null
     */
    protected $studentroleids = null;

    /**
     * Returns the name.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return \lang_string
     */
    public static function get_name() : \lang_string {
        return new \lang_string('indicator:nostudent', 'moodle');
    }

    /**
     * required_sample_data
     *
     * @return string[]
     */
    public static function required_sample_data() {
        // We require course because, although calculate_sample only reads context, we need the context to be course
        // or below.
        return array('context', 'course');
    }

    /**
     * calculate_sample
     *
     * @param int $sampleid
     * @param string $sampleorigin
     * @param int|false $notusedstarttime
     * @param int|false $notusedendtime
     * @return float
     */
    public function calculate_sample($sampleid, $sampleorigin, $notusedstarttime = false, $notusedendtime = false) {

        $context = $this->retrieve('context', $sampleid);

        if (is_null($this->studentroleids)) {
            $this->studentroleids = array_keys(get_archetype_roles('student'));
        }

        foreach ($this->studentroleids as $role) {
            // We look for roles, not enrolments as a student assigned at category level is supposed to be a
            // course student.
            $students = get_role_users($role, $context, false, 'u.id', 'u.id');
            if ($students) {
                return self::get_max_value();
            }
        }

        return self::get_min_value();
    }
}
