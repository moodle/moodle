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
use core_competency\external\competency_exporter;
use core_user\external\user_summary_exporter;
use core_competency\external\performance_helper;
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
     * @param int $userid
     * @param int $competencyid
     * @param int $courseid
     * @param string $baseurl
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
        global $CFG, $DB, $PAGE;

        $context = context_course::instance($this->courseid);

        $data = new stdClass();
        $data->userid = $this->userid;
        $data->competencyid = $this->competencyid;
        $data->courseid = $this->courseid;
        $data->baseurl = $this->baseurl;
        $data->groupselector = '';

        if (has_any_capability(array('moodle/competency:usercompetencyview', 'moodle/competency:coursecompetencymanage'),
                $context)) {
            $course = $DB->get_record('course', array('id' => $this->courseid));
            $currentgroup = groups_get_course_group($course, true);
            if ($currentgroup !== false) {
                $select = groups_allgroups_course_menu($course, $PAGE->url, true, $currentgroup);
                $data->groupselector = $select;
            }
            // Fetch showactive.
            $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
            $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
            $showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $context);

            $users = get_enrolled_users($context, 'moodle/competency:coursecompetencygradable', $currentgroup,
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

        $coursecompetencies = \core_competency\api::list_course_competencies($this->courseid);
        $data->competencies = array();
        $helper = new performance_helper();
        foreach ($coursecompetencies as $coursecompetency) {
            $coursecompetencycontext = $helper->get_context_from_competency($coursecompetency['competency']);
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
