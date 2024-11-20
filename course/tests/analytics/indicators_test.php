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

namespace core_course\analytics;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../../../lib/completionlib.php');
require_once(__DIR__ . '/../../../completion/criteria/completion_criteria_self.php');
require_once(__DIR__ . '/../../../analytics/tests/fixtures/test_target_course_users.php');

/**
 * Unit tests for core_course indicators.
 *
 * @package   core_course
 * @category  test
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class indicators_test extends \advanced_testcase {

    /**
     * test_no_teacher
     *
     * @return void
     */
    public function test_no_teacher(): void {
        global $DB;

        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        $coursecontext2 = \context_course::instance($course2->id);

        $user = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($user->id, $course2->id, 'teacher');

        $indicator = new \core_course\analytics\indicator\no_teacher();

        $sampleids = array($course1->id => $course1->id, $course2->id => $course2->id);
        $data = array(
            $course1->id => array(
                'context' => $coursecontext1,
                'course' => $course1,
            ),
            $course2->id => array(
                'context' => $coursecontext2,
                'course' => $course2,
            ));
        $indicator->add_sample_data($data);

        list($values, $ignored) = $indicator->calculate($sampleids, 'course');
        $this->assertEquals($indicator::get_min_value(), $values[$course1->id][0]);
        $this->assertEquals($indicator::get_max_value(), $values[$course2->id][0]);
    }

    /**
     * test_completion_enabled
     *
     * @return void
     */
    public function test_completion_enabled(): void {
        global $DB;

        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course(array('enablecompletion' => 0));
        $course2 = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $course3 = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        // Criteria only for the last one.
        $criteriadata = new \stdClass();
        $criteriadata->id = $course3->id;
        $criteriadata->criteria_self = 1;
        $criterion = new \completion_criteria_self();
        $criterion->update_config($criteriadata);

        $indicator = new \core_course\analytics\indicator\completion_enabled();

        $sampleids = array($course1->id => $course1->id, $course2->id => $course2->id, $course3->id => $course3->id);
        $data = array(
            $course1->id => array(
                'course' => $course1,
            ),
            $course2->id => array(
                'course' => $course2,
            ),
            $course3->id => array(
                'course' => $course3,
            ));
        $indicator->add_sample_data($data);

        // Calculate using course samples.
        list($values, $ignored) = $indicator->calculate($sampleids, 'course');
        $this->assertEquals($indicator::get_min_value(), $values[$course1->id][0]);
        $this->assertEquals($indicator::get_min_value(), $values[$course2->id][0]);
        $this->assertEquals($indicator::get_max_value(), $values[$course3->id][0]);

        // Calculate using course_modules samples.
        $indicator->clear_sample_data();
        $data1 = $this->getDataGenerator()->create_module('data', array('course' => $course3->id),
                                                             array('completion' => 0));
        $data2 = $this->getDataGenerator()->create_module('data', array('course' => $course3->id),
                                                             array('completion' => 1));

        $sampleids = array($data1->cmid => $data1->cmid, $data2->cmid => $data2->cmid);
        $cm1 = $DB->get_record('course_modules', array('id' => $data1->cmid));
        $cm2 = $DB->get_record('course_modules', array('id' => $data2->cmid));
        $data = array(
            $cm1->id => array(
                'course' => $course3,
                'course_modules' => $cm1,
            ),
            $cm2->id => array(
                'course' => $course3,
                'course_modules' => $cm2,
            ));
        $indicator->add_sample_data($data);

        list($values, $ignored) = $indicator->calculate($sampleids, 'course_modules');
        $this->assertEquals($indicator::get_min_value(), $values[$cm1->id][0]);
        $this->assertEquals($indicator::get_max_value(), $values[$cm2->id][0]);
    }

    /**
     * test_potential_cognitive
     *
     * @return void
     */
    public function test_potential_cognitive(): void {
        global $DB;

        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();

        $course2 = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course2->id));

        $course3 = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course3->id));
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course3->id));

        $course4 = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course4->id));
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course4->id));

        $indicator = new \core_course\analytics\indicator\potential_cognitive_depth();

        $sampleids = array($course1->id => $course1->id, $course2->id => $course2->id, $course3->id => $course3->id,
            $course4->id => $course4->id);
        $data = array(
            $course1->id => array(
                'course' => $course1,
            ),
            $course2->id => array(
                'course' => $course2,
            ),
            $course3->id => array(
                'course' => $course3,
            ),
            $course4->id => array(
                'course' => $course4,
            ));
        $indicator->add_sample_data($data);

        list($values, $ignored) = $indicator->calculate($sampleids, 'course');
        $this->assertEquals($indicator::get_min_value(), $values[$course1->id][0]);

        // General explanation about the points, the max level is 5 so level 1 is -1, level 2 is -0.5, level 3 is 0,
        // level 4 is 0.5 and level 5 is 1.

        // Page cognitive is level 1 (the lower one).
        $this->assertEquals($indicator::get_min_value(), $values[$course2->id][0]);

        // The maximum cognitive depth level is 5, assign level is 5 therefore the potential cognitive depth is the max.
        $this->assertEquals($indicator::get_max_value(), $values[$course3->id][0]);

        // Forum level is 4.
        $this->assertEquals(0.5, $values[$course4->id][0]);

        // Calculate using course_modules samples.
        $course5 = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course5->id));
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course5->id));

        $sampleids = array($assign->cmid => $assign->cmid, $forum->cmid => $forum->cmid);
        $cm1 = $DB->get_record('course_modules', array('id' => $assign->cmid));
        $cm2 = $DB->get_record('course_modules', array('id' => $forum->cmid));
        $data = array(
            $cm1->id => array(
                'course' => $course5,
                'course_modules' => $cm1,
            ),
            $cm2->id => array(
                'course' => $course5,
                'course_modules' => $cm2,
            ));
        $indicator->clear_sample_data();
        $indicator->add_sample_data($data);

        list($values, $ignored) = $indicator->calculate($sampleids, 'course_modules');
        // Assign level is 5, the maximum level.
        $this->assertEquals($indicator::get_max_value(), $values[$cm1->id][0]);
        // Forum level is 4.
        $this->assertEquals(0.5, $values[$cm2->id][0]);

    }

    /**
     * test_potential_social
     *
     * @return void
     */
    public function test_potential_social(): void {
        global $DB;

        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();

        $course2 = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course2->id));

        $course3 = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course3->id));
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course3->id));

        $course4 = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course4->id));
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course4->id));

        $indicator = new \core_course\analytics\indicator\potential_social_breadth();

        $sampleids = array($course1->id => $course1->id, $course2->id => $course2->id, $course3->id => $course3->id,
            $course4->id => $course4->id);
        $data = array(
            $course1->id => array(
                'course' => $course1,
            ),
            $course2->id => array(
                'course' => $course2,
            ),
            $course3->id => array(
                'course' => $course3,
            ),
            $course4->id => array(
                'course' => $course4,
            ));
        $indicator->add_sample_data($data);

        list($values, $ignored) = $indicator->calculate($sampleids, 'course');
        $this->assertEquals($indicator::get_min_value(), $values[$course1->id][0]);

        // General explanation about the points, the max level is 2 so level 1 is -1, level 2 is 1.

        // Page social is level 1 (the lower level).
        $this->assertEquals($indicator::get_min_value(), $values[$course2->id][0]);

        // Forum is level 2 and assign is level 2.
        $this->assertEquals($indicator::get_max_value(), $values[$course3->id][0]);

        // Page is level 1 and assign is level 2, so potential level is the max one.
        $this->assertEquals($indicator::get_max_value(), $values[$course4->id][0]);

        // Calculate using course_modules samples.
        $course5 = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course5->id));
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course5->id));

        $sampleids = array($assign->cmid => $assign->cmid, $page->cmid => $page->cmid);
        $cm1 = $DB->get_record('course_modules', array('id' => $assign->cmid));
        $cm2 = $DB->get_record('course_modules', array('id' => $page->cmid));
        $data = array(
            $cm1->id => array(
                'course' => $course5,
                'course_modules' => $cm1,
            ),
            $cm2->id => array(
                'course' => $course5,
                'course_modules' => $cm2,
            ));
        $indicator->clear_sample_data();
        $indicator->add_sample_data($data);

        list($values, $ignored) = $indicator->calculate($sampleids, 'course_modules');
        // Assign social is level 2 (the max level).
        $this->assertEquals($indicator::get_max_value(), $values[$cm1->id][0]);
        // Page social is level 1 (the lower level).
        $this->assertEquals($indicator::get_min_value(), $values[$cm2->id][0]);
    }

    /**
     * test_activities_due
     *
     * @return void
     */
    public function test_activities_due(): void {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminuser();

        $course1 = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'student');

        $target = \core_analytics\manager::get_target('test_target_course_users');
        $indicators = array('\core_course\analytics\indicator\activities_due');
        foreach ($indicators as $key => $indicator) {
            $indicators[$key] = \core_analytics\manager::get_indicator($indicator);
        }

        $model = \core_analytics\model::create($target, $indicators);
        $model->enable('\core\analytics\time_splitting\single_range');
        $model->train();
    }
}
