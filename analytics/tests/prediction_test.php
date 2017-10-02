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
 * Unit tests for evaluation, training and prediction.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/test_indicator_max.php');
require_once(__DIR__ . '/fixtures/test_indicator_min.php');
require_once(__DIR__ . '/fixtures/test_indicator_fullname.php');
require_once(__DIR__ . '/fixtures/test_indicator_random.php');
require_once(__DIR__ . '/fixtures/test_target_shortname.php');
require_once(__DIR__ . '/fixtures/test_static_target_shortname.php');

require_once(__DIR__ . '/../../course/lib.php');

/**
 * Unit tests for evaluation, training and prediction.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_analytics_prediction_testcase extends advanced_testcase {

    /**
     * test_static_prediction
     *
     * @return void
     */
    public function test_static_prediction() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminuser();

        $model = $this->add_perfect_model('test_static_target_shortname');
        $model->enable('\core\analytics\time_splitting\no_splitting');
        $this->assertEquals(1, $model->is_enabled());
        $this->assertEquals(1, $model->is_trained());

        // No training for static models.
        $results = $model->train();
        $trainedsamples = $DB->get_records('analytics_train_samples', array('modelid' => $model->get_id()));
        $this->assertEmpty($trainedsamples);
        $this->assertEmpty($DB->count_records('analytics_used_files',
            array('modelid' => $model->get_id(), 'action' => 'trained')));

        // Now we create 2 hidden courses (only hidden courses are getting predictions).
        $courseparams = array('shortname' => 'aaaaaa', 'fullname' => 'aaaaaa', 'visible' => 0);
        $course1 = $this->getDataGenerator()->create_course($courseparams);
        $courseparams = array('shortname' => 'bbbbbb', 'fullname' => 'bbbbbb', 'visible' => 0);
        $course2 = $this->getDataGenerator()->create_course($courseparams);

        $result = $model->predict();

        // Var $course1 predictions should be 1 == 'a', $course2 predictions should be 0 == 'b'.
        $correct = array($course1->id => 1, $course2->id => 0);
        foreach ($result->predictions as $uniquesampleid => $predictiondata) {
            list($sampleid, $rangeindex) = $model->get_time_splitting()->infer_sample_info($uniquesampleid);

            // The range index is not important here, both ranges prediction will be the same.
            $this->assertEquals($correct[$sampleid], $predictiondata->prediction);
        }

        // 1 range for each analysable.
        $predictedranges = $DB->get_records('analytics_predict_samples', array('modelid' => $model->get_id()));
        $this->assertCount(2, $predictedranges);
        $this->assertEquals(1, $DB->count_records('analytics_used_files',
            array('modelid' => $model->get_id(), 'action' => 'predicted')));
        // 2 predictions for each range.
        $this->assertEquals(2, $DB->count_records('analytics_predictions',
            array('modelid' => $model->get_id())));

        // No new generated files nor records as there are no new courses available.
        $model->predict();
        $predictedranges = $DB->get_records('analytics_predict_samples', array('modelid' => $model->get_id()));
        $this->assertCount(2, $predictedranges);
        $this->assertEquals(1, $DB->count_records('analytics_used_files',
            array('modelid' => $model->get_id(), 'action' => 'predicted')));
        $this->assertEquals(2, $DB->count_records('analytics_predictions',
            array('modelid' => $model->get_id())));
    }

    /**
     * test_ml_training_and_prediction
     *
     * @dataProvider provider_ml_training_and_prediction
     * @param string $timesplittingid
     * @param int $predictedrangeindex
     * @param int $nranges
     * @param string $predictionsprocessorclass
     * @return void
     */
    public function test_ml_training_and_prediction($timesplittingid, $predictedrangeindex, $nranges, $predictionsprocessorclass) {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminuser();
        set_config('enabled_stores', 'logstore_standard', 'tool_log');

        $ncourses = 10;

        // Generate training data.
        $params = array(
            'startdate' => mktime(0, 0, 0, 10, 24, 2015),
            'enddate' => mktime(0, 0, 0, 2, 24, 2016),
        );
        for ($i = 0; $i < $ncourses; $i++) {
            $name = 'a' . random_string(10);
            $courseparams = array('shortname' => $name, 'fullname' => $name) + $params;
            $this->getDataGenerator()->create_course($courseparams);
        }
        for ($i = 0; $i < $ncourses; $i++) {
            $name = 'b' . random_string(10);
            $courseparams = array('shortname' => $name, 'fullname' => $name) + $params;
            $this->getDataGenerator()->create_course($courseparams);
        }

        // We repeat the test for all prediction processors.
        $predictionsprocessor = \core_analytics\manager::get_predictions_processor($predictionsprocessorclass, false);
        if ($predictionsprocessor->is_ready() !== true) {
            $this->markTestSkipped('Skipping ' . $predictionsprocessorclass . ' as the predictor is not ready.');
        }

        set_config('predictionsprocessor', $predictionsprocessorclass, 'analytics');

        $model = $this->add_perfect_model();
        $model->enable($timesplittingid);

        // No samples trained yet.
        $this->assertEquals(0, $DB->count_records('analytics_train_samples', array('modelid' => $model->get_id())));

        $results = $model->train();
        $this->assertEquals(1, $model->is_enabled());
        $this->assertEquals(1, $model->is_trained());

        // 20 courses * the 3 model indicators * the number of time ranges of this time splitting method.
        $indicatorcalc = 20 * 3 * $nranges;
        $this->assertEquals($indicatorcalc, $DB->count_records('analytics_indicator_calc'));

        // 1 training file was created.
        $trainedsamples = $DB->get_records('analytics_train_samples', array('modelid' => $model->get_id()));
        $this->assertCount(1, $trainedsamples);
        $samples = json_decode(reset($trainedsamples)->sampleids, true);
        $this->assertCount($ncourses * 2, $samples);
        $this->assertEquals(1, $DB->count_records('analytics_used_files',
            array('modelid' => $model->get_id(), 'action' => 'trained')));

        $courseparams = $params + array('shortname' => 'aaaaaa', 'fullname' => 'aaaaaa', 'visible' => 0);
        $course1 = $this->getDataGenerator()->create_course($courseparams);
        $courseparams = $params + array('shortname' => 'bbbbbb', 'fullname' => 'bbbbbb', 'visible' => 0);
        $course2 = $this->getDataGenerator()->create_course($courseparams);

        // They will not be skipped for prediction though.
        $result = $model->predict();

        // Var $course1 predictions should be 1 == 'a', $course2 predictions should be 0 == 'b'.
        $correct = array($course1->id => 1, $course2->id => 0);
        foreach ($result->predictions as $uniquesampleid => $predictiondata) {
            list($sampleid, $rangeindex) = $model->get_time_splitting()->infer_sample_info($uniquesampleid);

            // The range index is not important here, both ranges prediction will be the same.
            $this->assertEquals($correct[$sampleid], $predictiondata->prediction);
        }

        // 1 range will be predicted.
        $predictedranges = $DB->get_records('analytics_predict_samples', array('modelid' => $model->get_id()));
        $this->assertCount(1, $predictedranges);
        foreach ($predictedranges as $predictedrange) {
            $this->assertEquals($predictedrangeindex, $predictedrange->rangeindex);
            $sampleids = json_decode($predictedrange->sampleids, true);
            $this->assertCount(2, $sampleids);
            $this->assertContains($course1->id, $sampleids);
            $this->assertContains($course2->id, $sampleids);
        }
        $this->assertEquals(1, $DB->count_records('analytics_used_files',
            array('modelid' => $model->get_id(), 'action' => 'predicted')));
        // 2 predictions.
        $this->assertEquals(2, $DB->count_records('analytics_predictions',
            array('modelid' => $model->get_id())));

        // No new generated files nor records as there are no new courses available.
        $model->predict();
        $predictedranges = $DB->get_records('analytics_predict_samples', array('modelid' => $model->get_id()));
        $this->assertCount(1, $predictedranges);
        foreach ($predictedranges as $predictedrange) {
            $this->assertEquals($predictedrangeindex, $predictedrange->rangeindex);
        }
        $this->assertEquals(1, $DB->count_records('analytics_used_files',
            array('modelid' => $model->get_id(), 'action' => 'predicted')));
        $this->assertEquals(2, $DB->count_records('analytics_predictions',
            array('modelid' => $model->get_id())));

        // New samples that can be used for prediction.
        $courseparams = $params + array('shortname' => 'cccccc', 'fullname' => 'cccccc', 'visible' => 0);
        $course3 = $this->getDataGenerator()->create_course($courseparams);
        $courseparams = $params + array('shortname' => 'dddddd', 'fullname' => 'dddddd', 'visible' => 0);
        $course4 = $this->getDataGenerator()->create_course($courseparams);

        $result = $model->predict();

        $predictedranges = $DB->get_records('analytics_predict_samples', array('modelid' => $model->get_id()));
        $this->assertCount(1, $predictedranges);
        foreach ($predictedranges as $predictedrange) {
            $this->assertEquals($predictedrangeindex, $predictedrange->rangeindex);
            $sampleids = json_decode($predictedrange->sampleids, true);
            $this->assertCount(4, $sampleids);
            $this->assertContains($course1->id, $sampleids);
            $this->assertContains($course2->id, $sampleids);
            $this->assertContains($course3->id, $sampleids);
            $this->assertContains($course4->id, $sampleids);
        }
        $this->assertEquals(2, $DB->count_records('analytics_used_files',
            array('modelid' => $model->get_id(), 'action' => 'predicted')));
        $this->assertEquals(4, $DB->count_records('analytics_predictions',
            array('modelid' => $model->get_id())));

        // New visible course (for training).
        $course5 = $this->getDataGenerator()->create_course(array('shortname' => 'aaa', 'fullname' => 'aa'));
        $course6 = $this->getDataGenerator()->create_course();
        $result = $model->train();
        $this->assertEquals(2, $DB->count_records('analytics_used_files',
            array('modelid' => $model->get_id(), 'action' => 'trained')));

        // Update one of the courses to not visible, it should be used again for prediction.
        $course5->visible = 0;
        update_course($course5);

        $model->predict();
        $this->assertEquals(1, $DB->count_records('analytics_predict_samples',
            array('modelid' => $model->get_id())));
        $this->assertEquals(2, $DB->count_records('analytics_used_files',
            array('modelid' => $model->get_id(), 'action' => 'predicted')));
        $this->assertEquals(4, $DB->count_records('analytics_predictions',
            array('modelid' => $model->get_id())));

        set_config('enabled_stores', '', 'tool_log');
        get_log_manager(true);
    }

    /**
     * provider_ml_training_and_prediction
     *
     * @return array
     */
    public function provider_ml_training_and_prediction() {
        $cases = array(
            'no_splitting' => array('\core\analytics\time_splitting\no_splitting', 0, 1),
            'quarters' => array('\core\analytics\time_splitting\quarters', 3, 4)
        );

        // We need to test all system prediction processors.
        return $this->add_prediction_processors($cases);
    }


    /**
     * Basic test to check that prediction processors work as expected.
     *
     * @dataProvider provider_ml_test_evaluation
     * @param string $modelquality
     * @param int $ncourses
     * @param array $expected
     * @param string $predictionsprocessorclass
     * @return void
     */
    public function test_ml_evaluation($modelquality, $ncourses, $expected, $predictionsprocessorclass) {
        $this->resetAfterTest(true);
        $this->setAdminuser();
        set_config('enabled_stores', 'logstore_standard', 'tool_log');

        $sometimesplittings = '\core\analytics\time_splitting\weekly,' .
            '\core\analytics\time_splitting\single_range,' .
            '\core\analytics\time_splitting\quarters';
        set_config('timesplittings', $sometimesplittings, 'analytics');

        if ($modelquality === 'perfect') {
            $model = $this->add_perfect_model();
        } else if ($modelquality === 'random') {
            $model = $this->add_random_model();
        } else {
            throw new \coding_exception('Only perfect and random accepted as $modelquality values');
        }

        // Generate training data.
        $params = array(
            'startdate' => mktime(0, 0, 0, 10, 24, 2015),
            'enddate' => mktime(0, 0, 0, 2, 24, 2016),
        );
        for ($i = 0; $i < $ncourses; $i++) {
            $name = 'a' . random_string(10);
            $params = array('shortname' => $name, 'fullname' => $name) + $params;
            $this->getDataGenerator()->create_course($params);
        }
        for ($i = 0; $i < $ncourses; $i++) {
            $name = 'b' . random_string(10);
            $params = array('shortname' => $name, 'fullname' => $name) + $params;
            $this->getDataGenerator()->create_course($params);
        }

        // We repeat the test for all prediction processors.
        $predictionsprocessor = \core_analytics\manager::get_predictions_processor($predictionsprocessorclass, false);
        if ($predictionsprocessor->is_ready() !== true) {
            $this->markTestSkipped('Skipping ' . $predictionsprocessorclass . ' as the predictor is not ready.');
        }

        set_config('predictionsprocessor', $predictionsprocessorclass, 'analytics');

        $results = $model->evaluate();

        // We check that the returned status includes at least $expectedcode code.
        foreach ($results as $timesplitting => $result) {
            $message = 'The returned status code ' . $result->status . ' should include ' . $expected[$timesplitting];
            $filtered = $result->status & $expected[$timesplitting];
            $this->assertEquals($expected[$timesplitting], $filtered, $message);
        }

        set_config('enabled_stores', '', 'tool_log');
        get_log_manager(true);
    }

    /**
     * test_read_indicator_calculations
     *
     * @return void
     */
    public function test_read_indicator_calculations() {
        global $DB;

        $this->resetAfterTest(true);

        $starttime = 123;
        $endtime = 321;
        $sampleorigin = 'whatever';

        $indicator = $this->getMockBuilder('test_indicator_max')->setMethods(['calculate_sample'])->getMock();
        $indicator->expects($this->never())->method('calculate_sample');

        $existingcalcs = array(111 => 1, 222 => 0.5);
        $sampleids = array(111 => 111, 222 => 222);
        list($values, $unused) = $indicator->calculate($sampleids, $sampleorigin, $starttime, $endtime, $existingcalcs);
    }

    /**
     * test_not_null_samples
     */
    public function test_not_null_samples() {
        $this->resetAfterTest(true);

        $classname = '\core\analytics\time_splitting\quarters';
        $timesplitting = \core_analytics\manager::get_time_splitting($classname);
        $timesplitting->set_analysable(new \core_analytics\site());

        $ranges = array(
            array('start' => 111, 'end' => 222, 'time' => 222),
            array('start' => 222, 'end' => 333, 'time' => 333)
        );
        $samples = array(123 => 123, 321 => 321);

        $indicator1 = $this->getMockBuilder('test_indicator_max')
            ->setMethods(['calculate_sample'])
            ->getMock();
        $indicator1->method('calculate_sample')
            ->willReturn(null);

        $indicator2 = \core_analytics\manager::get_indicator('test_indicator_min');

        // Samples with at least 1 not null value are returned.
        $params = array(
            $samples,
            'whatever',
            array($indicator1, $indicator2),
            $ranges
        );
        $dataset = phpunit_util::call_internal_method($timesplitting, 'calculate_indicators', $params, $classname);
        $this->assertArrayHasKey('123-0', $dataset);
        $this->assertArrayHasKey('123-1', $dataset);
        $this->assertArrayHasKey('321-0', $dataset);
        $this->assertArrayHasKey('321-1', $dataset);

        // Samples with only null values are not returned.
        $params = array(
            $samples,
            'whatever',
            array($indicator1),
            $ranges
        );
        $dataset = phpunit_util::call_internal_method($timesplitting, 'calculate_indicators', $params, $classname);
        $this->assertArrayNotHasKey('123-0', $dataset);
        $this->assertArrayNotHasKey('123-1', $dataset);
        $this->assertArrayNotHasKey('321-0', $dataset);
        $this->assertArrayNotHasKey('321-1', $dataset);
    }

    /**
     * provider_ml_test_evaluation
     *
     * @return array
     */
    public function provider_ml_test_evaluation() {

        $cases = array(
            'bad' => array(
                'modelquality' => 'random',
                'ncourses' => 50,
                'expectedresults' => array(
                    // The course duration is too much to be processed by in weekly basis.
                    '\core\analytics\time_splitting\weekly' => \core_analytics\model::NO_DATASET,
                    '\core\analytics\time_splitting\single_range' => \core_analytics\model::EVALUATE_LOW_SCORE,
                    '\core\analytics\time_splitting\quarters' => \core_analytics\model::EVALUATE_LOW_SCORE,
                )
            ),
            'good' => array(
                'modelquality' => 'perfect',
                'ncourses' => 50,
                'expectedresults' => array(
                    // The course duration is too much to be processed by in weekly basis.
                    '\core\analytics\time_splitting\weekly' => \core_analytics\model::NO_DATASET,
                    '\core\analytics\time_splitting\single_range' => \core_analytics\model::OK,
                    '\core\analytics\time_splitting\quarters' => \core_analytics\model::OK,
                )
            )
        );
        return $this->add_prediction_processors($cases);
    }

    /**
     * add_random_model
     *
     * @return \core_analytics\model
     */
    protected function add_random_model() {

        $target = \core_analytics\manager::get_target('test_target_shortname');
        $indicators = array('test_indicator_max', 'test_indicator_min', 'test_indicator_random');
        foreach ($indicators as $key => $indicator) {
            $indicators[$key] = \core_analytics\manager::get_indicator($indicator);
        }

        $model = \core_analytics\model::create($target, $indicators);

        // To load db defaults as well.
        return new \core_analytics\model($model->get_id());
    }

    /**
     * add_perfect_model
     *
     * @param string $targetclass
     * @return \core_analytics\model
     */
    protected function add_perfect_model($targetclass = 'test_target_shortname') {

        $target = \core_analytics\manager::get_target($targetclass);
        $indicators = array('test_indicator_max', 'test_indicator_min', 'test_indicator_fullname');
        foreach ($indicators as $key => $indicator) {
            $indicators[$key] = \core_analytics\manager::get_indicator($indicator);
        }

        $model = \core_analytics\model::create($target, $indicators);

        // To load db defaults as well.
        return new \core_analytics\model($model->get_id());
    }

    /**
     * add_prediction_processors
     *
     * @param array $cases
     * @return array
     */
    protected function add_prediction_processors($cases) {

        $return = array();

        // We need to test all system prediction processors.
        $predictionprocessors = \core_analytics\manager::get_all_prediction_processors();
        foreach ($predictionprocessors as $classfullname => $unused) {
            foreach ($cases as $key => $case) {
                $newkey = $key . '-' . $classfullname;
                $return[$newkey] = $case + array('predictionsprocessorclass' => $classfullname);
            }
        }

        return $return;
    }
}
