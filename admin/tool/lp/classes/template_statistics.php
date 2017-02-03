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
 * Template statistics class
 *
 * @package    tool_lp
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp;
defined('MOODLE_INTERNAL') || die();

use core_competency\api;
use core_competency\plan;
use core_competency\template;

/**
 * Template statistics class.
 *
 * @package    tool_lp
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_statistics {

    /** @var $competencycount The number of competencies in the template */
    public $competencycount = 0;

    /** @var $unlinkedcompetencycount The number of unlinked competencies in the template */
    public $unlinkedcompetencycount = 0;

    /** @var $plancount The number of plans for the template */
    public $plancount = 0;

    /** @var $completedplancount The number of completed plans for the template */
    public $completedplancount = 0;

    /** @var $usercompetencyplancount The number of competencies in completed plans for the template */
    public $usercompetencyplancount = 0;

    /** @var $proficientusercompetencyplancount The number of proficient competencies in completed plans for the template */
    public $proficientusercompetencyplancount = 0;

    /** @var $leastproficientcompetencies The competencies in this template that were proficient the least times */
    public $leastproficientcompetencies = array();

    /**
     * Return the custom definition of the properties of this model.
     *
     * @param int $templateid The template we want to generate statistics for.
     */
    public function __construct($templateid) {
        $template = new template($templateid);
        $this->competencycount = api::count_competencies_in_template($template);
        $this->unlinkedcompetencycount = api::count_competencies_in_template_with_no_courses($template);

        $this->plancount = api::count_plans_for_template($template, 0);
        $this->completedplancount = api::count_plans_for_template($template, plan::STATUS_COMPLETE);

        $this->usercompetencyplancount = api::count_user_competency_plans_for_template($template);
        $this->proficientusercompetencyplancount = api::count_user_competency_plans_for_template($template, true);

        $this->leastproficientcompetencies = api::get_least_proficient_competencies_for_template($template, 0, 3);
    }
}
