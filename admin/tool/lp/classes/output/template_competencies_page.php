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
 * Class containing data for learning plan template competencies page
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;
use stdClass;
use context;
use context_system;
use moodle_url;
use core_competency\external\template_exporter;
use core_competency\template;
use core_competency\api;
use core_competency\external\performance_helper;
use tool_lp\external\competency_summary_exporter;
use tool_lp\external\template_statistics_exporter;
use tool_lp\template_statistics;

/**
 * Class containing data for learning plan template competencies page
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_competencies_page implements renderable, templatable {

    /** @var template $template Template for this page. */
    protected $template = null;

    /** @var \core_competency\competency[] $competencies List of competencies. */
    protected $competencies = array();

    /** @var bool $canmanagecompetencyframeworks Can the current user manage competency frameworks. */
    protected $canmanagecompetencyframeworks = false;

    /** @var bool $canmanagecoursecompetencies Can the current user manage course competency frameworks.. */
    protected $canmanagecoursecompetencies = false;

    /** @var string $manageurl manage url. */
    protected $manageurl = null;

    /** @var context $pagecontext The page context. */
    protected $pagecontext = null;

    /** @var template_statistics $templatestatistics The generated summary statistics for this template. */
    protected $templatestatistics = null;

    /**
     * Construct this renderable.
     *
     * @param template $template The learning plan template.
     * @param context $pagecontext The page context.
     */
    public function __construct(template $template, context $pagecontext) {
        $this->pagecontext = $pagecontext;
        $this->template = $template;
        $this->templatestatistics = new template_statistics($template->get('id'));
        $this->competencies = api::list_competencies_in_template($template);
        $this->canmanagecompetencyframeworks = has_capability('moodle/competency:competencymanage', $this->pagecontext);
        $this->canmanagetemplatecompetencies = has_capability('moodle/competency:templatemanage', $this->pagecontext);
        $this->manageurl = new moodle_url('/admin/tool/lp/competencyframeworks.php',
            array('pagecontextid' => $this->pagecontext->id));
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->template = (new template_exporter($this->template))->export($output);
        $data->pagecontextid = $this->pagecontext->id;
        $data->competencies = array();
        $helper = new performance_helper();
        foreach ($this->competencies as $competency) {
            $context = $helper->get_context_from_competency($competency);
            $framework = $helper->get_framework_from_competency($competency);

            $courses = api::list_courses_using_competency($competency->get('id'));
            $relatedcompetencies = api::list_related_competencies($competency->get('id'));

            $related = array(
                'competency' => $competency,
                'linkedcourses' => $courses,
                'context' => $context,
                'relatedcompetencies' => $relatedcompetencies,
                'framework' => $framework
            );
            $exporter = new competency_summary_exporter(null, $related);
            $record = $exporter->export($output);

            array_push($data->competencies, $record);
        }

        $data->pluginbaseurl = (new moodle_url('/admin/tool/lp'))->out(false);
        $data->canmanagecompetencyframeworks = $this->canmanagecompetencyframeworks;
        $data->canmanagetemplatecompetencies = $this->canmanagetemplatecompetencies;
        $data->manageurl = $this->manageurl->out(true);
        $exporter = new template_statistics_exporter($this->templatestatistics);
        $data->statistics = $exporter->export($output);
        $data->showcompetencylinks = true;

        return $data;
    }
}
