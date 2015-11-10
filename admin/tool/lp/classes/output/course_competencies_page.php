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
 * Class containing data for course competencies page
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;
use moodle_url;
use context_system;
use context_course;
use tool_lp\api;
use tool_lp\external\competency_exporter;

/**
 * Class containing data for course competencies page
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_competencies_page implements renderable, templatable {

    /** @var int $courseid Course id for this page. */
    protected $courseid = null;

    /** @var context $context The context for this page. */
    protected $context = null;

    /** @var \tool_lp\competency[] $competencies List of competencies. */
    protected $competencies = array();

    /** @var bool $canmanagecompetencyframeworks Can the current user manage competency frameworks. */
    protected $canmanagecompetencyframeworks = false;

    /** @var bool $canmanagecoursecompetencies Can the current user manage course competency frameworks.. */
    protected $canmanagecoursecompetencies = false;

    /** @var string $manageurl manage url. */
    protected $manageurl = null;

    /**
     * Construct this renderable.
     * @param int $courseid The course record for this page.
     */
    public function __construct($courseid) {
        $this->context = context_course::instance($courseid);
        $this->courseid = $courseid;
        $this->competencies = api::list_competencies_in_course($courseid);
        $this->canmanagecoursecompetencies = has_capability('tool/lp:coursecompetencymanage', $this->context);

        // Check the lowest level in which the user can manage the competencies.
        $this->manageurl = null;
        $this->canmanagecompetencyframeworks = false;
        $contexts = array_reverse($this->context->get_parent_contexts(true));
        foreach ($contexts as $context) {
            $canmanage = has_capability('tool/lp:competencymanage', $context);
            if ($canmanage) {
                $this->manageurl = new moodle_url('/admin/tool/lp/competencyframeworks.php',
                    array('pagecontextid' => $context->id));
                $this->canmanagecompetencyframeworks = true;
                break;
            }
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->courseid = $this->courseid;
        $data->pagecontextid = $this->context->id;
        $data->competencies = array();

        $contextcache = array();
        foreach ($this->competencies as $competency) {
            if (!isset($contextcache[$competency->get_competencyframeworkid()])) {
                $contextcache[$competency->get_competencyframeworkid()] = $competency->get_framework()->get_context();
            }
            $context = $contextcache[$competency->get_competencyframeworkid()];

            $exporter = new competency_exporter($competency, array('context' => $context));
            $record = $exporter->export_for_template($output);
            array_push($data->competencies, $record);
        }
        $data->canmanagecompetencyframeworks = $this->canmanagecompetencyframeworks;
        $data->canmanagecoursecompetencies = $this->canmanagecoursecompetencies;
        $data->manageurl = null;
        if ($this->canmanagecompetencyframeworks) {
            $data->manageurl = $this->manageurl->out(true);
        }

        return $data;
    }
}
