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

namespace core_analytics;

use advanced_testcase;
use ReflectionClass;
use ReflectionMethod;
use stdClass;

/**
 * Unit tests for activities completed by classification.
 *
 * @package   core_analytics
 * @covers    \core_analytics\local\indicator\community_of_inquiry_activity
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class community_of_inquiry_activities_completed_by_test extends advanced_testcase {

    /**
     * availability_levels
     *
     * @return array
     */
    public static function availability_levels(): array {
        return array(
            'activity' => array('activity'),
            'section' => array('section'),
        );
    }

    /**
     * test_get_activities_with_availability
     *
     * @dataProvider availability_levels
     * @param string $availabilitylevel
     * @return void
     */
    public function test_get_activities_with_availability($availabilitylevel): void {

        list($course, $stu1) = $this->setup_course();

        // Forum1 is ignored as section 0 does not count.
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $modinfo = get_fast_modinfo($course, $stu1->id);
        $cm = $modinfo->get_cm($forum->cmid);

        if ($availabilitylevel === 'activity') {
            $availabilityinfo = new \core_availability\info_module($cm);
        } else if ($availabilitylevel === 'section') {
            $availabilityinfo = new \core_availability\info_section($cm->get_modinfo()->get_section_info($cm->sectionnum));
        } else {
            $this->fail('Unsupported availability level');
        }

        $fromtime = strtotime('2015-10-22 00:00:00 GMT');
        $untiltime = strtotime('2015-10-24 00:00:00 GMT');
        $structure = (object)array('op' => '|', 'show' => true, 'c' => array(
                (object)array('type' => 'date', 'd' => '<', 't' => $untiltime),
                (object)array('type' => 'date', 'd' => '>=', 't' => $fromtime)
        ));

        $method = new ReflectionMethod($availabilityinfo, 'set_in_database');
        $method->invoke($availabilityinfo, json_encode($structure));

        $this->setUser($stu1);

        // Reset modinfo we also want coursemodinfo cache definition to be cleared.
        get_fast_modinfo($course, $stu1->id, true);
        rebuild_course_cache($course->id, true);

        $modinfo = get_fast_modinfo($course, $stu1->id);

        $cm = $modinfo->get_cm($forum->cmid);

        $course = new \core_analytics\course($course);
        list($indicator, $method) = $this->instantiate_indicator('mod_forum', $course);

        // Condition from after provided end time.
        $this->assertCount(0, $method->invoke($indicator, strtotime('2015-10-20 00:00:00 GMT'),
            strtotime('2015-10-21 00:00:00 GMT'), $stu1));

        // Condition until before provided start time
        $this->assertCount(0, $method->invoke($indicator, strtotime('2015-10-25 00:00:00 GMT'),
            strtotime('2015-10-26 00:00:00 GMT'), $stu1));

        // Condition until after provided end time.
        $this->assertCount(0, $method->invoke($indicator, strtotime('2015-10-22 00:00:00 GMT'),
            strtotime('2015-10-23 00:00:00 GMT'), $stu1));

        // Condition until after provided start time and before provided end time.
        $this->assertCount(1, $method->invoke($indicator, strtotime('2015-10-22 00:00:00 GMT'),
            strtotime('2015-10-25 00:00:00 GMT'), $stu1));
    }

    /**
     * test_get_activities_with_weeks
     *
     * @return void
     */
    public function test_get_activities_with_weeks(): void {

        $startdate = gmmktime('0', '0', '0', 10, 24, 2015);
        $record = array(
            'format' => 'weeks',
            'numsections' => 4,
            'startdate' => $startdate,
        );

        list($course, $stu1) = $this->setup_course($record);

        // Forum1 is ignored as section 0 does not count.
        $forum1 = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('section' => 0));
        $forum2 = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('section' => 1));
        $forum3 = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('section' => 2));
        $forum4 = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('section' => 4));
        $forum5 = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('section' => 4));

        $course = new \core_analytics\course($course);
        list($indicator, $method) = $this->instantiate_indicator('mod_forum', $course);

        $this->setUser($stu1);

        $first = $startdate;
        $second = $startdate + WEEKSECS;
        $third = $startdate + (WEEKSECS * 2);
        $forth = $startdate + (WEEKSECS * 3);
        $this->assertCount(2, $method->invoke($indicator, $first, $first + WEEKSECS, $stu1));
        $this->assertCount(1, $method->invoke($indicator, $second, $second + WEEKSECS, $stu1));
        $this->assertCount(0, $method->invoke($indicator, $third, $third + WEEKSECS, $stu1));
        $this->assertCount(2, $method->invoke($indicator, $forth, $forth + WEEKSECS, $stu1));
    }

    /**
     * test_get_activities_by_section
     *
     * @return void
     */
    public function test_get_activities_by_section(): void {

        // This makes debugging easier, sorry WA's +8 :).
        $this->setTimezone('UTC');

        // 1 year.
        $startdate = gmmktime('0', '0', '0', 10, 24, 2015);
        $enddate = gmmktime('0', '0', '0', 10, 24, 2016);
        $numsections = 12;
        $record = array(
            'format' => 'topics',
            'numsections' => $numsections,
            'startdate' => $startdate,
            'enddate' => $enddate
        );

        list($course, $stu1) = $this->setup_course($record);

        // Forum1 is ignored as section 0 does not count.
        $forum1 = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('section' => 0));
        $forum2 = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('section' => 1));
        $forum3 = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('section' => 4));
        $forum4 = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('section' => 8));
        $forum5 = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('section' => 10));
        $forum6 = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('section' => 12));

        $course = new \core_analytics\course($course);
        list($indicator, $method) = $this->instantiate_indicator('mod_forum', $course);

        $this->setUser($stu1);

        // Split the course in quarters.
        $duration = ($enddate - $startdate) / 4;
        $first = $startdate;
        $second = $startdate + $duration;
        $third = $startdate + ($duration * 2);
        $forth = $startdate + ($duration * 3);
        $this->assertCount(1, $method->invoke($indicator, $first, $first + $duration, $stu1));
        $this->assertCount(1, $method->invoke($indicator, $second, $second + $duration, $stu1));
        $this->assertCount(1, $method->invoke($indicator, $third, $third + $duration, $stu1));
        $this->assertCount(2, $method->invoke($indicator, $forth, $forth + $duration, $stu1));

        // Split the course in as many parts as sections.
        $duration = ($enddate - $startdate) / $numsections;
        for ($i = 1; $i <= $numsections; $i++) {
            // The -1 because section 1 start represents the course start.
            $timeranges[$i] = $startdate + ($duration * ($i - 1));
        }
        $this->assertCount(1, $method->invoke($indicator, $timeranges[1], $timeranges[1] + $duration, $stu1));
        $this->assertCount(1, $method->invoke($indicator, $timeranges[4], $timeranges[4] + $duration, $stu1));
        $this->assertCount(1, $method->invoke($indicator, $timeranges[8], $timeranges[8] + $duration, $stu1));
        $this->assertCount(1, $method->invoke($indicator, $timeranges[10], $timeranges[10] + $duration, $stu1));
        $this->assertCount(1, $method->invoke($indicator, $timeranges[12], $timeranges[12] + $duration, $stu1));

        // Nothing here.
        $this->assertCount(0, $method->invoke($indicator, $timeranges[2], $timeranges[2] + $duration, $stu1));
        $this->assertCount(0, $method->invoke($indicator, $timeranges[3], $timeranges[3] + $duration, $stu1));
    }

    /**
     * test_get_activities_with_specific_restrictions
     *
     * @return void
     */
    public function test_get_activities_with_specific_restrictions(): void {

        list($course, $stu1) = $this->setup_course();

        $end = strtotime('2015-10-24 00:00:00 GMT');

        // 1 with time close, one without.
        $params = array('course' => $course->id);
        $assign1 = $this->getDataGenerator()->create_module('assign', $params);
        $params['duedate'] = $end;
        $assign2 = $this->getDataGenerator()->create_module('assign', $params);

        // 1 with time close, one without.
        $params = array('course' => $course->id);
        $choice1 = $this->getDataGenerator()->create_module('choice', $params);
        $params['timeclose'] = $end;
        $choice1 = $this->getDataGenerator()->create_module('choice', $params);

        // 1 with time close, one without.
        $params = array('course' => $course->id);
        $data1 = $this->getDataGenerator()->create_module('data', $params);
        $params['timeavailableto'] = $end;
        $data1 = $this->getDataGenerator()->create_module('data', $params);

        // 1 with time close, one without.
        $params = array('course' => $course->id);
        $feedback1 = $this->getDataGenerator()->create_module('feedback', $params);
        $params['timeclose'] = $end;
        $feedback1 = $this->getDataGenerator()->create_module('feedback', $params);

        // 1 with time close, one without.
        $params = array('course' => $course->id);
        $lesson1 = $this->getDataGenerator()->create_module('lesson', $params);
        $params['deadline'] = $end;
        $lesson1 = $this->getDataGenerator()->create_module('lesson', $params);

        // 1 with time close, one without.
        $params = array('course' => $course->id);
        $quiz1 = $this->getDataGenerator()->create_module('quiz', $params);
        $params['timeclose'] = $end;
        $quiz1 = $this->getDataGenerator()->create_module('quiz', $params);

        // 1 with time close, one without.
        $params = array('course' => $course->id);
        $scorm1 = $this->getDataGenerator()->create_module('scorm', $params);
        $params['timeclose'] = $end;
        $scorm1 = $this->getDataGenerator()->create_module('scorm', $params);

        // 1 with time close, one without.
        $params = array('course' => $course->id);
        $workshop1 = $this->getDataGenerator()->create_module('workshop', $params);
        $params['submissionend'] = $end;
        $workshop1 = $this->getDataGenerator()->create_module('workshop', $params);

        $course = new \core_analytics\course($course);

        $activitytypes = array('mod_assign', 'mod_choice', 'mod_data', 'mod_feedback', 'mod_lesson',
            'mod_quiz', 'mod_scorm', 'mod_workshop');
        foreach ($activitytypes as $activitytype) {

            list($indicator, $method) = $this->instantiate_indicator($activitytype, $course);

            $message = $activitytype . ' activity type returned activities do not match expected size';
            $this->assertCount(0, $method->invoke($indicator, strtotime('2015-10-20 00:00:00 GMT'),
                strtotime('2015-10-21 00:00:00 GMT'), $stu1), $message);

            $this->assertCount(0, $method->invoke($indicator, strtotime('2015-10-25 00:00:00 GMT'),
                strtotime('2015-10-26 00:00:00 GMT'), $stu1), $message);

            $this->assertCount(1, $method->invoke($indicator, strtotime('2015-10-22 00:00:00 GMT'),
                strtotime('2015-10-25 00:00:00 GMT'), $stu1), $message);
        }
    }

    /**
     * setup_course
     *
     * @param stdClass $courserecord
     * @return array
     */
    protected function setup_course($courserecord = null) {
        global $CFG;

        $this->resetAfterTest(true);

        $this->setAdminUser();

        $CFG->enableavailability = true;

        $course = $this->getDataGenerator()->create_course($courserecord);
        $stu1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($stu1->id, $course->id, 'student');

        return array($course, $stu1);
    }

    /**
     * Returns the module cognitive depth indicator and the reflection method.
     *
     * @param string $modulename
     * @param \core_analytics\course $course
     * @return array
     */
    private function instantiate_indicator($modulename, \core_analytics\course $course) {

        $classname = '\\' . $modulename . '\analytics\indicator\cognitive_depth';
        $indicator = new $classname();
        $class = new ReflectionClass($indicator);

        $property = $class->getProperty('course');
        $property->setValue($indicator, $course);

        $method = new ReflectionMethod($indicator, 'get_activities');

        return array($indicator, $method);
    }

}
