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
 * Course competency statistics class
 *
 * @package    tool_lp
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp;
defined('MOODLE_INTERNAL') || die();

use core_competency\api;

/**
 * Course competency statistics class.
 *
 * @package    tool_lp
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_competency_statistics {

    /** @var $competencycount The number of competencies in the course */
    public $competencycount = 0;

    /** @var $proficientcompetencycount The number of proficient competencies for the current user */
    public $proficientcompetencycount = 0;

    /** @var $leastproficientcompetencies The competencies in this course that were proficient the least times */
    public $leastproficientcompetencies = array();

    /**
     * Return the custom definition of the properties of this model.
     *
     * @param int $courseid The course we want to generate statistics for.
     */
    public function __construct($courseid) {
        global $USER;

        $this->competencycount = api::count_competencies_in_course($courseid);
        $this->proficientcompetencycount = api::count_proficient_competencies_in_course_for_user($courseid, $USER->id);
        $this->leastproficientcompetencies = api::get_least_proficient_competencies_for_course($courseid, 0, 3);
    }
}
