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
 * @package    report_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_competency\output;

use context_course;
use renderable;
use core_user;
use templatable;
use renderer_base;
use moodle_url;
use stdClass;
use core_competency\api;
use core_competency\external\user_competency_course_exporter;
use core_user\external\user_summary_exporter;
use core_competency\external\performance_helper;
use core_competency\url;
use core_competency\user_competency;
use tool_lp\external\competency_summary_exporter;
use core_course\external\course_summary_exporter;

/**
 * Class containing data for learning plan template competencies page
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report implements renderable, templatable {

    /** @var context $context */
    protected $context;
    /** @var int $courseid */
    protected $courseid;
    /** @var array $competencies */
    protected $competencies;

    /**
     * Construct this renderable.
     *
     * @param int $courseid The course id
     * @param int $userid The user id
     */
    public function __construct($courseid, $userid) {
        $this->courseid = $courseid;
        $this->userid = $userid;
        $this->context = context_course::instance($courseid);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $DB;

        $data = new stdClass();
        $data->courseid = $this->courseid;

        $course = $DB->get_record('course', array('id' => $this->courseid));
        $coursecontext = context_course::instance($course->id);
        $exporter = new course_summary_exporter($course, array('context' => $coursecontext));
        $coursecompetencysettings = api::read_course_competency_settings($course->id);
        $data->pushratingstouserplans = $coursecompetencysettings->get('pushratingstouserplans');
        $data->course = $exporter->export($output);

        $data->usercompetencies = array();
        $user = core_user::get_user($this->userid);

        $exporter = new user_summary_exporter($user);
        $data->user = $exporter->export($output);
        $data->usercompetencies = array();
        $coursecompetencies = api::list_course_competencies($this->courseid);
        $usercompetencycourses = api::list_user_competencies_in_course($this->courseid, $user->id);

        $helper = new performance_helper();
        foreach ($usercompetencycourses as $usercompetencycourse) {
            $onerow = new stdClass();
            $competency = null;
            foreach ($coursecompetencies as $coursecompetency) {
                if ($coursecompetency['competency']->get('id') == $usercompetencycourse->get('competencyid')) {
                    $competency = $coursecompetency['competency'];
                    break;
                }
            }
            if (!$competency) {
                continue;
            }

            $framework = $helper->get_framework_from_competency($competency);
            $scale = $helper->get_scale_from_competency($competency);

            $exporter = new user_competency_course_exporter($usercompetencycourse, array('scale' => $scale));
            $record = $exporter->export($output);
            $onerow->usercompetencycourse = $record;
            $exporter = new competency_summary_exporter(null, array(
                'competency' => $competency,
                'framework' => $framework,
                'context' => $framework->get_context(),
                'relatedcompetencies' => array(),
                'linkedcourses' => array()
            ));
            $onerow->competency = $exporter->export($output);
            array_push($data->usercompetencies, $onerow);
        }

        return $data;
    }
}
