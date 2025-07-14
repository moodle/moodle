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

namespace core_analytics\tests;

use phpunit_util;

/**
 * A trait to check machine learning configurations.
 *
 * @package    core_analytics
 * @category   test
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait mlbackend_helper_trait {
    /**
     * Check if mlbackend_python is configured.
     *
     * @return bool
     */
    public static function is_mlbackend_python_configured(): bool {
        if (defined('TEST_MLBACKEND_PYTHON_HOST') && defined('TEST_MLBACKEND_PYTHON_PORT')
                && defined('TEST_MLBACKEND_PYTHON_USERNAME') && defined('TEST_MLBACKEND_PYTHON_USERNAME')) {
            return true;
        }
        return false;
    }

    /**
     * Generate test courses.
     *
     * @param int $ncourses Number of courses to generate.
     * @param array $params Additional parameters for the courses.
     * @param bool $ismulticlass Whether to generate courses for multi-classification.
     */
    public function generate_courses(
        int $ncourses,
        array $params = [],
        bool $ismulticlass = false,
    ): void {
        $params = $params + [
            'startdate' => mktime(0, 0, 0, 10, 24, 2015),
            'enddate' => mktime(0, 0, 0, 2, 24, 2016),
        ];
        for ($i = 0; $i < $ncourses; $i++) {
            $name = 'a' . random_string(10);
            $courseparams = ['shortname' => $name, 'fullname' => $name] + $params;
            phpunit_util::get_data_generator()->create_course($courseparams);
        }
        for ($i = 0; $i < $ncourses; $i++) {
            $name = 'b' . random_string(10);
            $courseparams = ['shortname' => $name, 'fullname' => $name] + $params;
            phpunit_util::get_data_generator()->create_course($courseparams);
        }
        if ($ismulticlass) {
            for ($i = 0; $i < $ncourses; $i++) {
                $name = 'c' . random_string(10);
                $courseparams = ['shortname' => $name, 'fullname' => $name] + $params;
                phpunit_util::get_data_generator()->create_course($courseparams);
            }
        }
    }
}
