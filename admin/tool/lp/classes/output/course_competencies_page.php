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
defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;
use stdClass;
use moodle_url;
use context_system;
use context_course;
use core_competency\api;
use tool_lp\course_competency_statistics;
use core_competency\competency;
use core_competency\course_competency;
use core_competency\external\performance_helper;
use core_competency\external\competency_exporter;
use core_competency\external\course_competency_exporter;
use core_competency\external\course_competency_settings_exporter;
use core_competency\external\user_competency_course_exporter;
use core_competency\external\user_competency_exporter;
use core_competency\external\plan_exporter;
use tool_lp\external\competency_path_exporter;
use tool_lp\external\course_competency_statistics_exporter;
use core_course\external\course_module_summary_exporter;

/**
 * Class containing data for course competencies page
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_competencies_page implements renderable, templatable {

    /** @var int $courseid Course id for this page. */
    protected $courseid = null;

    /** @var int $moduleid Module id for this page. */
    protected $moduleid = null;

    /** @var context $context The context for this page. */
    protected $context = null;

    /** @var \core_competency\course_competency[] $competencies List of competencies. */
    protected $coursecompetencylist = array();

    /** @var bool $canmanagecompetencyframeworks Can the current user manage competency frameworks. */
    protected $canmanagecompetencyframeworks = false;

    /** @var bool $canmanagecoursecompetencies Can the current user manage course competency frameworks.. */
    protected $canmanagecoursecompetencies = false;

    /** @var string $manageurl manage url. */
    protected $manageurl = null;

    /** @var bool */
    protected bool $canconfigurecoursecompetencies = false;

    /** @var bool */
    protected bool $cangradecompetencies = false;

    /** @var \core\persistent|null */
    protected $coursecompetencysettings = null;

    /** @var \tool_lp\course_competency_statistics|null */
    protected $coursecompetencystatistics = null;

    /**
     * Construct this renderable.
     * @param int $courseid The course record for this page.
     */
    public function __construct($courseid, $moduleid) {
        $this->context = context_course::instance($courseid);
        $this->courseid = $courseid;
        $this->moduleid = $moduleid;
        $this->coursecompetencylist = api::list_course_competencies($courseid);

        if ($this->moduleid > 0) {
            $modulecompetencies = api::list_course_module_competencies_in_course_module($this->moduleid);
            foreach ($this->coursecompetencylist as $ccid => $coursecompetency) {
                $coursecompetency = $coursecompetency['coursecompetency'];
                $found = false;
                foreach ($modulecompetencies as $mcid => $modulecompetency) {
                    if ($modulecompetency->get('competencyid') == $coursecompetency->get('competencyid')) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    // We need to filter out this competency.
                    unset($this->coursecompetencylist[$ccid]);
                }
            }
        }

        $this->canmanagecoursecompetencies = has_capability('moodle/competency:coursecompetencymanage', $this->context);
        $this->canconfigurecoursecompetencies = has_capability('moodle/competency:coursecompetencyconfigure', $this->context);
        $this->cangradecompetencies = has_capability('moodle/competency:competencygrade', $this->context);
        $this->coursecompetencysettings = api::read_course_competency_settings($courseid);
        $this->coursecompetencystatistics = new course_competency_statistics($courseid);

        // Check the lowest level in which the user can manage the competencies.
        $this->manageurl = null;
        $this->canmanagecompetencyframeworks = false;
        $contexts = array_reverse($this->context->get_parent_contexts(true));
        foreach ($contexts as $context) {
            $canmanage = has_capability('moodle/competency:competencymanage', $context);
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
        global $USER;

        $data = new stdClass();
        $data->courseid = $this->courseid;
        $data->moduleid = $this->moduleid;
        $data->pagecontextid = $this->context->id;
        $data->competencies = array();
        $data->pluginbaseurl = (new moodle_url('/admin/tool/lp'))->out(true);

        $gradable = is_enrolled($this->context, $USER, 'moodle/competency:coursecompetencygradable');
        if ($gradable) {
            $usercompetencycourses = api::list_user_competencies_in_course($this->courseid, $USER->id);
            $data->gradableuserid = $USER->id;

            if ($this->moduleid > 0) {
                $modulecompetencies = api::list_course_module_competencies_in_course_module($this->moduleid);
                foreach ($usercompetencycourses as $ucid => $usercoursecompetency) {
                    $found = false;
                    foreach ($modulecompetencies as $mcid => $modulecompetency) {
                        if ($modulecompetency->get('competencyid') == $usercoursecompetency->get('competencyid')) {
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        // We need to filter out this competency.
                        unset($usercompetencycourses[$ucid]);
                    }
                }
            }
        }

        $ruleoutcomelist = course_competency::get_ruleoutcome_list();
        $ruleoutcomeoptions = array();
        foreach ($ruleoutcomelist as $value => $text) {
            $ruleoutcomeoptions[$value] = array('value' => $value, 'text' => (string) $text, 'selected' => false);
        }

        $helper = new performance_helper();
        foreach ($this->coursecompetencylist as $coursecompetencyelement) {
            $coursecompetency = $coursecompetencyelement['coursecompetency'];
            $competency = $coursecompetencyelement['competency'];
            $context = $helper->get_context_from_competency($competency);

            $compexporter = new competency_exporter($competency, array('context' => $context));
            $ccexporter = new course_competency_exporter($coursecompetency, array('context' => $context));

            $ccoutcomeoptions = (array) (object) $ruleoutcomeoptions;
            $ccoutcomeoptions[$coursecompetency->get('ruleoutcome')]['selected'] = true;

            $coursemodules = api::list_course_modules_using_competency($competency->get('id'), $this->courseid);

            $fastmodinfo = get_fast_modinfo($this->courseid);
            $exportedmodules = array();
            foreach ($coursemodules as $cmid) {
                $cminfo = $fastmodinfo->cms[$cmid];
                $cmexporter = new course_module_summary_exporter(null, array('cm' => $cminfo));
                $exportedmodules[] = $cmexporter->export($output);
            }
            // Competency path.
            $pathexporter = new competency_path_exporter([
                'ancestors' => $competency->get_ancestors(),
                'framework' => $helper->get_framework_from_competency($competency),
                'context' => $context
            ]);

            // User learning plans.
            $plans = api::list_plans_with_competency($USER->id, $competency);
            $exportedplans = array();
            foreach ($plans as $plan) {
                $planexporter = new plan_exporter($plan, array('template' => $plan->get_template()));
                $exportedplans[] = $planexporter->export($output);
            }

            $onerow = array(
                'competency' => $compexporter->export($output),
                'coursecompetency' => $ccexporter->export($output),
                'ruleoutcomeoptions' => $ccoutcomeoptions,
                'coursemodules' => $exportedmodules,
                'comppath' => $pathexporter->export($output),
                'plans' => $exportedplans
            );
            if ($gradable) {
                $foundusercompetencycourse = false;
                foreach ($usercompetencycourses as $usercompetencycourse) {
                    if ($usercompetencycourse->get('competencyid') == $competency->get('id')) {
                        $foundusercompetencycourse = $usercompetencycourse;
                    }
                }
                if ($foundusercompetencycourse) {
                    $related = array(
                        'scale' => $helper->get_scale_from_competency($competency)
                    );
                    $exporter = new user_competency_course_exporter($foundusercompetencycourse, $related);
                    $onerow['usercompetencycourse'] = $exporter->export($output);
                }
            }
            array_push($data->competencies, $onerow);
        }

        $data->canmanagecompetencyframeworks = $this->canmanagecompetencyframeworks;
        $data->canmanagecoursecompetencies = $this->canmanagecoursecompetencies;
        $data->canconfigurecoursecompetencies = $this->canconfigurecoursecompetencies;
        $data->cangradecompetencies = $this->cangradecompetencies;
        $exporter = new course_competency_settings_exporter($this->coursecompetencysettings);
        $data->settings = $exporter->export($output);
        $related = array('context' => $this->context);
        $exporter = new course_competency_statistics_exporter($this->coursecompetencystatistics, $related);
        $data->statistics = $exporter->export($output);
        $data->manageurl = null;
        if ($this->canmanagecompetencyframeworks) {
            $data->manageurl = $this->manageurl->out(true);
        }

        return $data;
    }

}
