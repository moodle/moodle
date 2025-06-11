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
 * Course total grade tests
 * @author    Guy Thomas
 * @copyright Copyright (c) 2017 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_snap;
use theme_snap\local;
use theme_snap\course_total_gradeTest;

/**
 * Course total grade tests
 * @author    Guy Thomas
 * @copyright Copyright (c) 2017 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_total_grade_Test extends \advanced_testcase {

    /**
     * @var array
     */
    private $courses = [];

    /**
     * @var array
     */
    private $users = [];

    /**
     * @var array
     */
    private $courseassignments = [];

    /**
     * @var int
     */
    private $scaleid;

    /**
     * @var int
     */
    private $usercount = 5;

    /**
     * @var int
     */
    private $coursecount = 5;

    /**
     * @var int - This will be set automatically to the number of items returned by grade_item_settings if not overriden.
     */
    private $assigncount = 0;

    /**
     * Return grade item settings array.
     * @return array
     */
    private function grade_item_settings() {
        $gradeitemsettings = [
            [
                'hidden' => 0,
                'grademin' => 0,
                'grademax' => 100,
            ],
            [
                'hidden' => 0,
                'grademin' => 0,
                'grademax' => 80,
            ],
            [
                'hidden' => 0,
                'aggregationcoef' => 25,
                'grademax' => 80,
            ],
            [
                'hidden' => 0,
                'aggregationcoef' => 50,
                'grademax' => 100,
            ],
            [
                'hidden' => 1,
                'grademin' => 0,
                'grademax' => 100,
            ],
            [
                'hidden' => 1,
                'grademin' => 0,
                'grademax' => 80,
            ],
            [
                'hidden' => 1,
                'aggregationcoef' => 25,
                'grademax' => 80,
            ],
            [
                'hidden' => 1,
                'aggregationcoef' => 50,
                'grademax' => 100,
            ],
            [
                'hidden' => 0,
                'grademin' => 0,
                'grademax' => 100,
                'gradetype' => GRADE_TYPE_SCALE,
                'scaleid' => $this->scaleid,
            ],
            [
                'hidden' => 0,
                'grademin' => 0,
                'grademax' => 80,
                'gradetype' => GRADE_TYPE_SCALE,
                'scaleid' => $this->scaleid,
            ],
            [
                'hidden' => 0,
                'aggregationcoef' => 25,
                'grademax' => 80,
                'gradetype' => GRADE_TYPE_SCALE,
                'scaleid' => $this->scaleid,
            ],
            [
                'hidden' => 0,
                'aggregationcoef' => 50,
                'grademax' => 100,
                'gradetype' => GRADE_TYPE_SCALE,
                'scaleid' => $this->scaleid,
            ],
            [
                'hidden' => 1,
                'grademin' => 0,
                'grademax' => 100,
                'gradetype' => GRADE_TYPE_SCALE,
                'scaleid' => $this->scaleid,
            ],
            [
                'hidden' => 1,
                'grademin' => 0,
                'grademax' => 80,
                'gradetype' => GRADE_TYPE_SCALE,
                'scaleid' => $this->scaleid,
            ],
            [
                'hidden' => 1,
                'aggregationcoef' => 25,
                'grademax' => 80,
                'gradetype' => GRADE_TYPE_SCALE,
                'scaleid' => $this->scaleid,
            ],
            [
                'hidden' => 1,
                'aggregationcoef' => 50,
                'grademax' => 100,
                'gradetype' => GRADE_TYPE_SCALE,
                'scaleid' => $this->scaleid,
            ],
        ];
        return $gradeitemsettings;
    }

    /**
     * @param null|int $usercount
     * @param null|int $coursecount
     * @param null|int $assigncount
     * @throws coding_exception
     */
    private function init($usercount = null, $coursecount = null, $assigncount = null) {
        global $DB;

        $gradeitemsettings = $this->grade_item_settings();

        if (!empty($usercount)) {
            $this->usercount = $usercount;
        }
        if (!empty($coursecount)) {
            $this->coursecount = $coursecount;
        }
        if (!empty($assigncount)) {
            $this->assigncount = $assigncount;
        } else {
            $this->assigncount = count($gradeitemsettings);
        }

        $dg = $this->getDataGenerator();
        $assigngen = $dg->get_plugin_generator('mod_assign');

        // Make a scale.
        $this->scaleid = $DB->insert_record('scale', (object) [
            'courseid' => 0,
            'userid' => 0,
            'name' => 'Test scale',
            'scale' => 'A, B, C, D',
            'description' => 'Test scale desc',
        ]);

        $gi = 0;

        for ($u = 0; $u < $this->usercount; $u++) {
            $user = $dg->create_user();
            $this->users[$u] = $user;
        }
        for ($c = 0; $c < $this->coursecount; $c++) {
            $course = $dg->create_course();
            $this->courses[$c] = $course;
            // Enrol students on course.
            foreach ($this->users as $user) {
                $dg->enrol_user($user->id, $course->id, 'student');
            }

            // Create some gradable items and grade.
            for ($a = 0; $a < $this->assigncount; $a++) {
                $gi++;
                $gradeitemsetting = $gradeitemsettings[$gi];
                if ($gi >= count($gradeitemsettings) - 1) {
                    $gi = 0;
                }
                if (!isset($courseassignments[$c])) {
                    $this->courseassignments[$c] = [];
                }
                $record = array('course' => $course);
                $instance = $assigngen->create_instance($record);

                $cm = get_coursemodule_from_instance('assign', $instance->id);
                $cm = \cm_info::create($cm);

                $assign = new \assign($cm->context, $cm, $course);
                $gradeitem = $assign->get_grade_item();
                \grade_object::set_properties($gradeitem, $gradeitemsetting);
                $gradeitem->update();
                $assignrow = $assign->get_instance();
                $grades = array();

                // Do grading.
                foreach ($this->users as $user) {
                    $grades[$user->id] = (object)[
                        'rawgrade' => intval(rand(0, 99)),
                        'userid' => $user->id,
                    ];
                }
                $assignrow->cmidnumber = null;
                assign_grade_item_update($assignrow, $grades);
                grade_regrade_final_grades($course->id);

                $this->courseassignments[$c][$a] = $assign;

            }
        }
    }

    protected function setUp(): void {
        global $CFG;

        $this->resetAfterTest(true);

        require_once($CFG->dirroot.'/mod/assign/tests/base_test.php');
    }

    /**
     * Does this course have any visible feedback for current user?.
     *
     * @param \stdClass $course
     * @return \stdClass
     */
    private static function course_grade_user_report($course) {
        global $USER;

        $failobj = (object)[
            'fromcache' => false, // Useful for debugging and unit testing.
            'feedback' => false,
        ];

        if (!isloggedin() || isguestuser()) {
            return $failobj;
        }

        // Get course context.
        $coursecontext = \context_course::instance($course->id);
        // Security check - should they be allowed to see course grade?
        $onlyactive = true;
        if (!is_enrolled($coursecontext, $USER, 'moodle/grade:view', $onlyactive)) {
            return $failobj;
        }
        // Security check - are they allowed to see the grade report for the course?
        if (!has_capability('gradereport/user:view', $coursecontext)) {
            return $failobj;
        }
        // See if user can view hidden grades for this course.
        $canviewhidden = has_capability('moodle/grade:viewhidden', $coursecontext);
        // Do not show grade if grade book disabled for students.
        // Note - moodle/grade:viewall is a capability held by teachers and thus used to exclude them from not getting
        // the grade.
        if (empty($course->showgrades) && !has_capability('moodle/grade:viewall', $coursecontext)) {
            return $failobj;
        }
        // Get course grade_item.
        $courseitem = \grade_item::fetch_course_item($course->id);
        // Get the stored grade.
        $coursegrade = new \grade_grade(array('itemid' => $courseitem->id, 'userid' => $USER->id));
        $coursegrade->grade_item =& $courseitem;

        $feedbackurl = new \moodle_url('/grade/report/user/index.php', array('id' => $course->id));
        // Default feedbackobj.
        $feedbackobj = (object)[
            'feedbackurl' => $feedbackurl->out(),
        ];

        if (!$coursegrade->is_hidden() || $canviewhidden) {
            // Use user grade report to get course total - this is to take hidden grade settings into account.
            $gpr = new \grade_plugin_return(array(
                    'type' => 'report',
                    'plugin' => 'user',
                    'courseid' => $course->id,
                    'userid' => $USER->id, )
            );
            $report = new \gradereport_user\report\user($course->id, $gpr, $coursecontext, $USER->id);
            $report->fill_table();

            $coursetotal = end($report->tabledata);
            $coursegrade = $coursetotal['grade']['content'];
            $ignoregrades = [
                '-',
                '&nbsp;',
                get_string('error'),
            ];
            if (!in_array($coursegrade, $ignoregrades)) {
                $feedbackobj->coursegrade = $coursegrade;
            }
        }

        // Cache object.
        $feedbackobj->timestamp = microtime(true);
        $feedbackobj->fromcache = false; // We set the cache, we didn't get it from the cache.

        return $feedbackobj;
    }

    /**
     * Output performance data.
     * @param string $reporttxt
     * @param float $total
     * @param float $avgusertimepercourse
     * @param float $avgusertimeallcourses
     * @return string
     */
    private function output_performance($reporttxt, $total, $avgusertimepercourse, $avgusertimeallcourses) {
        $msg = "\n".$reporttxt.' timings for '.$this->coursecount.' courses'.
            ' X '.$this->assigncount.' gradable items'.
            ' X '.$this->usercount.' enrolled students'.
            "\n total time : ".$total.' seconds'.
            "\n average user time per course: ".$avgusertimepercourse.' seconds'.
            "\n average user time all courses: ".$avgusertimeallcourses.' seconds';
        return $msg;
    }

    /**
     * Standard performance test.
     * @param string $reporttxt
     * @param string $callback
     * @param array $args
     *
     * @return int
     */
    private function run_performance_test($reporttxt, $callback, array $args) {
        $usertimespercourse = [];
        $usertimesallcourses = [];
        $start = microtime(true);

        foreach ($this->users as $user) {
            $uas = microtime(true);
            foreach ($this->courses as $course) {
                $us = microtime(true);
                $this->setUser($user);
                // The args always has course as the first param.
                array_unshift($args, $course);
                call_user_func_array($callback, $args);
                $ue = microtime(true);
                $usertimespercourse[] = $ue - $us;
            }
            $uae = microtime(true);
            $usertimesallcourses[] = $uae - $uas;
        }

        $end = microtime(true);
        $total = $end - $start;
        $avgusertimepercourse = array_sum($usertimespercourse) / count($usertimespercourse);
        $avgusertimeallcourses = array_sum($usertimesallcourses) / count($usertimesallcourses);

        mtrace($this->output_performance($reporttxt, $total, $avgusertimepercourse, $avgusertimeallcourses));

        return $total;
    }

    public function test_performance() {

        if (stripos(__DIR__, 'vagrant/www/joule2') === false) {
            $this->markTestSkipped('Not to be run as part of regular CI');
        }

        // Manually adjust these parameters to get different performance test results.
        $this->init(5, 20, 0);

        // Test legacy (user report) method.
        $title = 'Legacy method (user report)';
        $legacytime = $this->run_performance_test($title, 'self::course_grade_user_report', []);

        // Test new overview report method.
        $title = 'New method (course_total_grade class)';
        $newtime = $this->run_performance_test($title, 'theme_snap\local::course_grade', []);

        $this->assertLessThan($legacytime, $newtime);
    }

    public function test_consistency() {
        $this->markTestSkipped('Is failing on bamboo will be review it on INT-17968');
        $this->init();

        // Consistancy test.
        foreach ($this->courses as $course) {

            $userranksetting = grade_get_setting($course->id, 'report_user_showrank');
            grade_set_setting($course->id, 'report_overview_showrank', $userranksetting);

            foreach ($this->users as $user) {
                $this->setUser($user);

                $settings = [
                    GRADE_REPORT_HIDE_TOTAL_IF_CONTAINS_HIDDEN,
                    GRADE_REPORT_SHOW_TOTAL_IF_CONTAINS_HIDDEN,
                    GRADE_REPORT_SHOW_REAL_TOTAL_IF_CONTAINS_HIDDEN,
                ];

                $settingdisplaytypes = [
                    GRADE_DISPLAY_TYPE_REAL,
                    GRADE_DISPLAY_TYPE_REAL_PERCENTAGE,
                    GRADE_DISPLAY_TYPE_REAL_LETTER,
                    GRADE_DISPLAY_TYPE_PERCENTAGE,
                    GRADE_DISPLAY_TYPE_PERCENTAGE_REAL,
                    GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER,
                    GRADE_DISPLAY_TYPE_LETTER,
                    GRADE_DISPLAY_TYPE_LETTER_REAL,
                    GRADE_DISPLAY_TYPE_LETTER_PERCENTAGE,
                ];

                foreach ($settings as $setting) {
                    grade_set_setting($course->id, 'report_user_showtotalsifcontainhidden', $setting);
                    grade_set_setting($course->id, 'report_overview_showtotalsifcontainhidden', $setting);
                    foreach ($settingdisplaytypes as $displaytype) {
                        grade_set_setting($course->id, 'displaytype', $displaytype);
                        $legacyfeedback = self::course_grade_user_report($course);
                        $coursegradeobj = local::course_grade($course);
                        $coursegrade = isset($coursegradeobj->coursegrade) ? $coursegradeobj->coursegrade : null;

                        if (!empty($legacyfeedback->coursegrade)) {
                            $message  = "\n\n". 'report_user_showtotalsifcontainhidden = '.$setting;
                            $message .= "\n\n". 'displaytype = '.$displaytype;
                            $this->assertSame($legacyfeedback->coursegrade, $coursegrade['value'], $message);
                        } else {
                            $this->assertEmpty($coursegrade, 'course grade is '.var_export($coursegrade, true));
                        }
                    }
                }
            }
        }
    }
}
