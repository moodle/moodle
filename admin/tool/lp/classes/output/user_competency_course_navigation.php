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
 * User competency page class.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\output;

use renderable;
use renderer_base;
use templatable;
use context_course;
use \tool_lp\external\user_summary_exporter;
use \tool_lp\external\competency_exporter;
use stdClass;

/**
 * User competency course navigation class.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_competency_course_navigation implements renderable, templatable {

    /** @var userid */
    protected $userid;

    /** @var competencyid */
    protected $competencyid;

    /** @var courseid */
    protected $courseid;

    /** @var baseurl */
    protected $baseurl;

    /**
     * Construct.
     *
     * @param $userid
     * @param $competencyid
     * @param $courseid
     */
    public function __construct($userid, $competencyid, $courseid, $baseurl) {
        $this->userid = $userid;
        $this->competencyid = $competencyid;
        $this->courseid = $courseid;
        $this->baseurl = $baseurl;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $DB, $SESSION, $PAGE;

        $context = context_course::instance($this->courseid);

        $data = new stdClass();
        $data->userid = $this->userid;
        $data->competencyid = $this->competencyid;
        $data->courseid = $this->courseid;
        $data->baseurl = $this->baseurl;

        if (has_capability('tool/lp:coursecompetencymanage', $context)) {
            $course = $DB->get_record('course', array('id' => $this->courseid));
            $currentgroup = optional_param('group', null, PARAM_INT);
            $select = groups_allgroups_course_menu($course, $PAGE->url, true, $currentgroup);
            // Fetch showactive.
            $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
            $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
            $showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $context);

            // Fetch current active group.
            $groupmode = groups_get_course_groupmode($course);
            $currentgroup = $SESSION->activegroup[$course->id][$groupmode][$course->defaultgroupingid];

            $users = get_enrolled_users($context, 'tool/lp:coursecompetencygradable', $currentgroup,
                                        'u.*', null, 0, 0, $showonlyactiveenrol);

            $data->users = array();
            foreach ($users as $user) {
                $exporter = new user_summary_exporter($user);
                $user = $exporter->export($output);
                if ($user->id == $this->userid) {
                    $user->selected = true;
                }
                $data->users[] = $user;
            }
            $data->hasusers = true;
        } else {
            $data->users = array();
            $data->hasusers = false;
        }

        $coursecompetencies = \tool_lp\api::list_course_competencies($this->courseid);
        $data->competencies = array();
        $contextcache = array();
        foreach ($coursecompetencies as $coursecompetency) {
            $frameworkid = $coursecompetency['competency']->get_competencyframeworkid();
            if (!isset($contextcache[$frameworkid])) {
                $contextcache[$frameworkid] = $coursecompetency['competency']->get_context();
            }
            $context = $contextcache[$frameworkid];
            $coursecompetencycontext = $context;
            $exporter = new competency_exporter($coursecompetency['competency'], array('context' => $coursecompetencycontext));
            $competency = $exporter->export($output);
            if ($competency->id == $this->competencyid) {
                $competency->selected = true;
            }
            $data->competencies[] = $competency;
        }
        $data->hascompetencies = count($data->competencies);
        return $data;
    }
}
