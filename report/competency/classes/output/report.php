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
use tool_lp\external\competency_exporter;
use tool_lp\external\course_summary_exporter;
use tool_lp\external\user_competency_exporter;
use tool_lp\external\user_summary_exporter;
use tool_lp\user_competency;
use renderable;
use templatable;
use renderer_base;
use moodle_url;
use stdClass;
use tool_lp\api;

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
    /** @var int $groupid */
    protected $groupid;
    /** @var boolean $onlyactive */
    protected $onlyactive;
    /** @var array $competencies */
    protected $competencies;
    /** @var array $users */
    protected $users;

    /**
     * Construct this renderable.
     *
     * @param int $courseid The course id
     * @param int $groupid The group id
     * @param bool $onlyactive Only show active (not suspended) students.
     */
    public function __construct($courseid, $groupid, $onlyactive) {
        $this->courseid = $courseid;
        $this->groupid = $groupid;
        $this->onlyactive = $onlyactive;
        $this->context = context_course::instance($courseid);
        // Get all the competencies in this course.
        $this->competencies = api::list_course_competencies($courseid);

        // Get all the users in this course.
        // tool/lp:coursecompetencygradable
        $this->users = get_enrolled_users($this->context, 'tool/lp:coursecompetencygradable', $groupid,
                                          'u.*', null, 0, 0, $onlyactive);

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
        $data->groupid = $this->groupid;
        $data->onlyactive = $this->onlyactive;

        $competencies = array();
        $contextcache = array();
        foreach ($this->competencies as $coursecompetency) {
            $competency = $coursecompetency['competency'];
            if (!isset($contextcache[$competency->get_competencyframeworkid()])) {
                $contextcache[$competency->get_competencyframeworkid()] = $competency->get_context();
            }
            $context = $contextcache[$competency->get_competencyframeworkid()];
            $exporter = new competency_exporter($competency, array('context' => $context));
            $record = $exporter->export($output);
            array_push($competencies, $record);
        }
        $data->competencies = $competencies;

        $course = $DB->get_record('course', array('id' => $this->courseid));
        $coursecontext = context_course::instance($course->id);
        $exporter = new course_summary_exporter($course, array('context' => $coursecontext));
        $data->course = $exporter->export($output);

        $data->pluginbaseurl = (new moodle_url('/admin/tool/lp/'))->out(false);
        $data->usercompetencies = array();
        $scalecache = array();
        $frameworkcache = array();
        foreach ($this->users as $user) {
            $usercompetencies = api::list_user_competencies_in_course($this->courseid, $user->id);
            $onerow = new stdClass();
            $exporter = new user_summary_exporter($user);
            $onerow->user = $exporter->export($output);
            $onerow->usercompetencies = array();

            foreach ($this->competencies as $coursecompetency) {
                $competency = $coursecompetency['competency'];
                $usercompetency = new user_competency(0, (object) array('userid' => $user->id, 'competencyid' => $competency->get_id()));
                foreach ($usercompetencies as $uc) {
                    if ($uc->get_competencyid() == $competency->get_id()) {
                        $usercompetency = $uc;
                        break;
                    }
                }

                // Fetch the scale.
                $scaleid = $competency->get_scaleid();
                if ($scaleid === null) {
                    if (!isset($frameworkcache[$competency->get_competencyframeworkid()])) {
                        $frameworkcache[$competency->get_competencyframeworkid()] = $competency->get_framework();
                    }
                    $framework = $frameworkcache[$competency->get_competencyframeworkid()];
                    $scaleid = $framework->get_scaleid();
                    if (!isset($scalecache[$scaleid])) {
                        $scalecache[$competency->get_scaleid()] = $framework->get_scale();
                    }

                } else if (!isset($scalecache[$scaleid])) {
                    $scalecache[$competency->get_scaleid()] = $competency->get_scale();
                }
                $scale = $scalecache[$competency->get_scaleid()];

                $exporter = new user_competency_exporter($usercompetency, array('scale' => $scale));
                $record = $exporter->export($output);
                array_push($onerow->usercompetencies, $record);
            }
            array_push($data->usercompetencies, $onerow);
        }

        return $data;
    }
}
