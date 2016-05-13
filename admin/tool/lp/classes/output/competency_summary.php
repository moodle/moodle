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
 * Class containing data for competency_page page
 *
 * @package    tool_lp
 * @copyright  2015 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;
use stdClass;
use core_competency\api;
use tool_lp\external\competency_summary_exporter;

/**
 * Class containing data for competency summary
 *
 * @copyright  2015 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_summary implements renderable, templatable {

    /** @var \core_competency\competency_framework $framework This competency framework. */
    protected $framework = null;

    /** @var \core_competency\competency $competency. */
    protected $competency = null;

    /** @var \core_competency\competency[] $relatedcompetencies List of competencies. */
    protected $relatedcompetencies = array();

    /** @var course[] $courses List of courses. */
    protected $courses = array();

    /**
     * Construct this renderable.
     *
     * @param \core_competency\competency $competency Competency persistent.
     * @param \core_competency\competency_framework $framework framework persistent.
     * @param boolean $includerelated Include or not related competencies.
     * @param boolean $includecourses Include or not competency courses.
     */
    public function __construct($competency, $framework, $includerelated, $includecourses) {
        $this->competency = $competency;
        $this->framework = $framework;
        if ($includerelated) {
            $this->relatedcompetencies = api::list_related_competencies($competency->get_id());
        }

        if ($includecourses) {
            $this->courses = api::list_courses_using_competency($competency->get_id());
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $related = array(
            'context' => $this->framework->get_context(),
            'framework' => $this->framework,
            'linkedcourses' => $this->courses,
            'relatedcompetencies' => $this->relatedcompetencies,
            'competency' => $this->competency
        );

        $exporter = new competency_summary_exporter($this->competency, $related);
        $data = $exporter->export($output);

        return $data;
    }
}
