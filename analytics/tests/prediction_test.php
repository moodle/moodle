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

global $CFG;
require_once(__DIR__ . '/fixtures/test_indicator_max.php');
require_once(__DIR__ . '/fixtures/test_indicator_min.php');
require_once(__DIR__ . '/fixtures/test_indicator_null.php');
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
        // 2 predictions for each range.
        $this->assertEquals(2, $DB->count_records('analytics_predictions',
            array('modelid' => $model->get_id())));

        // No new generated records as there are no new courses available.
        $model->predict();
        $predictedranges = $DB->get_records('analytics_predict_samples', array('modelid' => $model->get_id()));
        $this->assertCount(2, $predictedranges);
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

        // Generate training data.
        $ncourses = 10;
        $this->generate_courses($ncourses);

        // We repeat the test for all prediction processors.
        $predictionsprocessor = \core_analytics\manager::get_predictions_processor($predictionsprocessorclass, false);
        if ($predictionsprocessor->is_ready() !== true) {
            $this->markTestSkipped('Skipping ' . $predictionsprocessorclass . ' as the predictor is not ready.');
        }

        $model = $this->add_perfect_model();
        $model->update(true, false, $timesplittingid, get_class($predictionsprocessor));

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
        // Check that analysable files for training are stored under labelled filearea.
        $fs = get_file_storage();
        $this->assertCount(1, $fs->get_directory_files(\context_system::instance()->id, 'analytics',
            \core_analytics\dataset_manager::LABELLED_FILEAREA, $model->get_id(), '/analysable/', true, false));
        $this->assertEmpty($fs->get_directory_files(\context_system::instance()->id, 'analytics',
            \core_analytics\dataset_manager::UNLABELLED_FILEAREA, $model->get_id(), '/analysable/', true, false));

        $params = [
            'startdate' => mktime(0, 0, 0, 10, 24, 2015),
            'enddate' => mktime(0, 0, 0, 2, 24, 2016),
        ];
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

        // Check that analysable files to get predictions are stored under unlabelled filearea.
        $this->assertCount(1, $fs->get_directory_files(\context_system::instance()->id, 'analytics',
            \core_analytics\dataset_manager::LABELLED_FILEAREA, $model->get_id(), '/analysable/', true, false));
        $this->assertCount(1, $fs->get_directory_files(\context_system::instance()->id, 'analytics',
            \core_analytics\dataset_manager::UNLABELLED_FILEAREA, $model->get_id(), '/analysable/', true, false));

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
        $this->assertCount(1, $fs->get_directory_files(\context_system::instance()->id, 'analytics',
            \core_analytics\dataset_manager::LABELLED_FILEAREA, $model->get_id(), '/analysable/', true, false));
        $this->assertCount(2, $fs->get_directory_files(\context_system::instance()->id, 'analytics',
            \core_analytics\dataset_manager::UNLABELLED_FILEAREA, $model->get_id(), '/analysable/', true, false));

        // New visible course (for training).
        $course5 = $this->getDataGenerator()->create_course(array('shortname' => 'aaa', 'fullname' => 'aa'));
        $course6 = $this->getDataGenerator()->create_course();
        $result = $model->train();
        $this->assertEquals(2, $DB->count_records('analytics_used_files',
            array('modelid' => $model->get_id(), 'action' => 'trained')));
        $this->assertCount(2, $fs->get_directory_files(\context_system::instance()->id, 'analytics',
            \core_analytics\dataset_manager::LABELLED_FILEAREA, $model->get_id(), '/analysable/', true, false));
        $this->assertCount(2, $fs->get_directory_files(\context_system::instance()->id, 'analytics',
            \core_analytics\dataset_manager::UNLABELLED_FILEAREA, $model->get_id(), '/analysable/', true, false));

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
     * test_ml_export_import
     *
     * @param string $predictionsprocessorclass The class name
     * @dataProvider provider_ml_processors
     */
    public function test_ml_export_import($predictionsprocessorclass) {

        $this->resetAfterTest(true);
        $this->setAdminuser();
        set_config('enabled_stores', 'logstore_standard', 'tool_log');

        // Generate training data.
        $ncourses = 10;
        $this->generate_courses($ncourses);

        // We repeat the test for all prediction processors.
        $predictionsprocessor = \core_analytics\manager::get_predictions_processor($predictionsprocessorclass, false);
        if ($predictionsprocessor->is_ready() !== true) {
            $this->markTestSkipped('Skipping ' . $predictionsprocessorclass . ' as the predictor is not ready.');
        }

        $model = $this->add_perfect_model();
        $model->update(true, false, '\core\analytics\time_splitting\quarters', get_class($predictionsprocessor));

        $model->train();
        $this->assertTrue($model->trained_locally());

        $this->generate_courses(10, ['visible' => 0]);

        $originalresults = $model->predict();

        $zipfilename = 'model-zip-' . microtime() . '.zip';
        $zipfilepath = $model->export_model($zipfilename);

        $modelconfig = new \core_analytics\model_config();
        list($modelconfig, $mlbackend) = $modelconfig->extract_import_contents($zipfilepath);
        $this->assertNotFalse($mlbackend);

        $importmodel = \core_analytics\model::import_model($zipfilepath);
        $importmodel->enable();

        // Now predict using the imported model without prior training.
        $importedmodelresults = $importmodel->predict();

        foreach ($originalresults->predictions as $sampleid => $prediction) {
            $this->assertEquals($importedmodelresults->predictions[$sampleid]->prediction, $prediction->prediction);
        }

        $this->assertFalse($importmodel->trained_locally());

        $zipfilename = 'model-zip-' . microtime() . '.zip';
        $zipfilepath = $model->export_model($zipfilename, false);

        $modelconfig = new \core_analytics\model_config();
        list($modelconfig, $mlbackend) = $modelconfig->extract_import_contents($zipfilepath);
        $this->assertFalse($mlbackend);

        set_config('enabled_stores', '', 'tool_log');
        get_log_manager(true);
    }

    /**
     * provider_ml_processors
     *
     * @return array
     */
    public function provider_ml_processors() {
        $cases = [
            'case' => [],
        ];

        // We need to test all system prediction processors.
        return $this->add_prediction_processors($cases);
    }
    /**
     * Test the system classifiers returns.
     *
     * This test checks that all mlbackend plugins in the system are able to return proper status codes
     * even under weird situations.
     *
     * @dataProvider provider_ml_classifiers_return
     * @param int $success
     * @param int $nsamples
     * @param int $classes
     * @param string $predictionsprocessorclass
     * @return void
     */
    public function test_ml_classifiers_return($success, $nsamples, $classes, $predictionsprocessorclass) {
        $this->resetAfterTest();

        $predictionsprocessor = \core_analytics\manager::get_predictions_processor($predictionsprocessorclass, false);
        if ($predictionsprocessor->is_ready() !== true) {
            $this->markTestSkipped('Skipping ' . $predictionsprocessorclass . ' as the predictor is not ready.');
        }

        if ($nsamples % count($classes) != 0) {
            throw new \coding_exception('The number of samples should be divisible by the number of classes');
        }
        $samplesperclass = $nsamples / count($classes);

        // Metadata (we pass 2 classes even if $classes only provides 1 class samples as we want to test
        // what the backend does in this case.
        $dataset = "nfeatures,targetclasses,targettype" . PHP_EOL;
        $dataset .= "3,\"[0,1]\",\"discrete\"" . PHP_EOL;

        // Headers.
        $dataset .= "feature1,feature2,feature3,target" . PHP_EOL;
        foreach ($classes as $class) {
            for ($i = 0; $i < $samplesperclass; $i++) {
                $dataset .= "1,0,1,$class" . PHP_EOL;
            }
        }

        $trainingfile = array(
            'contextid' => \context_system::instance()->id,
            'component' => 'analytics',
            'filearea' => 'labelled',
            'itemid' => 123,
            'filepath' => '/',
            'filename' => 'whocares.csv'
        );
        $fs = get_file_storage();
        $dataset = $fs->create_file_from_string($trainingfile, $dataset);

        // Training should work correctly if at least 1 sample of each class is included.
        $dir = make_request_directory();
        $result = $predictionsprocessor->train_classification('whatever', $dataset, $dir);

        switch ($success) {
            case 'yes':
                $this->assertEquals(\core_analytics\model::OK, $result->status);
                break;
            case 'no':
                $this->assertNotEquals(\core_analytics\model::OK, $result->status);
                break;
            case 'maybe':
            default:
                // We just check that an object is returned so we don't have an empty check,
                // what we really want to check is that an exception was not thrown.
                $this->assertInstanceOf(\stdClass::class, $result);
        }
    }

    /**
     * test_ml_classifiers_return provider
     *
     * We can not be very specific here as test_ml_classifiers_return only checks that
     * mlbackend plugins behave and expected and control properly backend errors even
     * under weird situations.
     *
     * @return array
     */
    public function provider_ml_classifiers_return() {
        // Using verbose options as the first argument for readability.
        $cases = array(
            '1-samples' => array('maybe', 1, [0]),
            '2-samples-same-class' => array('maybe', 2, [0]),
            '2-samples-different-classes' => array('yes', 2, [0, 1]),
            '4-samples-different-classes' => array('yes', 4, [0, 1])
        );

        // We need to test all system prediction processors.
        return $this->add_prediction_processors($cases);
    }

    /**
     * Basic test to check that prediction processors work as expected.
     *
     * @dataProvider provider_ml_test_evaluation_configuration
     * @param string $modelquality
     * @param int $ncourses
     * @param array $expected
     * @param string $predictionsprocessorclass
     * @return void
     */
    public function test_ml_evaluation_configuration($modelquality, $ncourses, $expected, $predictionsprocessorclass) {
        $this->resetAfterTest(true);
        $this->setAdminuser();
        set_config('enabled_stores', 'logstore_standard', 'tool_log');

        $sometimesplittings = '\core\analytics\time_splitting\single_range,' .
            '\core\analytics\time_splitting\quarters';
        set_config('defaulttimesplittingsevaluation', $sometimesplittings, 'analytics');

        if ($modelquality === 'perfect') {
            $model = $this->add_perfect_model();
        } else if ($modelquality === 'random') {
            $model = $this->add_random_model();
        } else {
            throw new \coding_exception('Only perfect and random accepted as $modelquality values');
        }

        // Generate training data.
        $this->generate_courses($ncourses);

        // We repeat the test for all prediction processors.
        $predictionsprocessor = \core_analytics\manager::get_predictions_processor($predictionsprocessorclass, false);
        if ($predictionsprocessor->is_ready() !== true) {
            $this->markTestSkipped('Skipping ' . $predictionsprocessorclass . ' as the predictor is not ready.');
        }

        $model->update(false, false, false, get_class($predictionsprocessor));
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
     * Tests the evaluation of already trained models.
     *
     * @dataProvider provider_ml_processors
     * @param  string $predictionsprocessorclass
     * @return null
     */
    public function test_ml_evaluation_trained_model($predictionsprocessorclass) {
        $this->resetAfterTest(true);
        $this->setAdminuser();
        set_config('enabled_stores', 'logstore_standard', 'tool_log');

        $model = $this->add_perfect_model();

        // Generate training data.
        $this->generate_courses(50);

        // We repeat the test for all prediction processors.
        $predictionsprocessor = \core_analytics\manager::get_predictions_processor($predictionsprocessorclass, false);
        if ($predictionsprocessor->is_ready() !== true) {
            $this->markTestSkipped('Skipping ' . $predictionsprocessorclass . ' as the predictor is not ready.');
        }

        $model->update(true, false, '\\core\\analytics\\time_splitting\\quarters', get_class($predictionsprocessor));
        $model->train();

        $zipfilename = 'model-zip-' . microtime() . '.zip';
        $zipfilepath = $model->export_model($zipfilename);
        $importmodel = \core_analytics\model::import_model($zipfilepath);

        $results = $importmodel->evaluate(['mode' => 'trainedmodel']);
        $this->assertEquals(0, $results['\\core\\analytics\\time_splitting\\quarters']->status);
        $this->assertEquals(1, $results['\\core\\analytics\\time_splitting\\quarters']->score);

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

        $existingcalcs = array(111 => 1, 222 => -1);
        $sampleids = array(111 => 111, 222 => 222);
        list($values, $unused) = $indicator->calculate($sampleids, $sampleorigin, $starttime, $endtime, $existingcalcs);
    }

    /**
     * test_not_null_samples
     */
    public function test_not_null_samples() {
        $this->resetAfterTest(true);

        $timesplitting = \core_analytics\manager::get_time_splitting('\core\analytics\time_splitting\quarters');
        $timesplitting->set_analysable(new \core_analytics\site());

        $ranges = array(
            array('start' => 111, 'end' => 222, 'time' => 222),
            array('start' => 222, 'end' => 333, 'time' => 333)
        );
        $samples = array(123 => 123, 321 => 321);

        $target = \core_analytics\manager::get_target('test_target_shortname');
        $indicators = array('test_indicator_null', 'test_indicator_min');
        foreach ($indicators as $key => $indicator) {
            $indicators[$key] = \core_analytics\manager::get_indicator($indicator);
        }
        $model = \core_analytics\model::create($target, $indicators, '\core\analytics\time_splitting\no_splitting');

        $analyser = $model->get_analyser();
        $result = new \core_analytics\local\analysis\result_array($model->get_id(), false, $analyser->get_options());
        $analysis = new \core_analytics\analysis($analyser, false, $result);

        // Samples with at least 1 not null value are returned.
        $params = array(
            $timesplitting,
            $samples,
            $ranges
        );
        $dataset = phpunit_util::call_internal_method($analysis, 'calculate_indicators', $params,
            '\core_analytics\analysis');
        $this->assertArrayHasKey('123-0', $dataset);
        $this->assertArrayHasKey('123-1', $dataset);
        $this->assertArrayHasKey('321-0', $dataset);
        $this->assertArrayHasKey('321-1', $dataset);


        $indicators = array('test_indicator_null');
        foreach ($indicators as $key => $indicator) {
            $indicators[$key] = \core_analytics\manager::get_indicator($indicator);
        }
        $model = \core_analytics\model::create($target, $indicators, '\core\analytics\time_splitting\no_splitting');

        $analyser = $model->get_analyser();
        $result = new \core_analytics\local\analysis\result_array($model->get_id(), false, $analyser->get_options());
        $analysis = new \core_analytics\analysis($analyser, false, $result);

        // Samples with only null values are not returned.
        $params = array(
            $timesplitting,
            $samples,
            $ranges
        );
        $dataset = phpunit_util::call_internal_method($analysis, 'calculate_indicators', $params,
            '\core_analytics\analysis');
        $this->assertArrayNotHasKey('123-0', $dataset);
        $this->assertArrayNotHasKey('123-1', $dataset);
        $this->assertArrayNotHasKey('321-0', $dataset);
        $this->assertArrayNotHasKey('321-1', $dataset);
    }

    /**
     * provider_ml_test_evaluation_configuration
     *
     * @return array
     */
    public function provider_ml_test_evaluation_configuration() {

        $cases = array(
            'bad' => array(
                'modelquality' => 'random',
                'ncourses' => 50,
                'expectedresults' => array(
                    '\core\analytics\time_splitting\single_range' => \core_analytics\model::LOW_SCORE,
                    '\core\analytics\time_splitting\quarters' => \core_analytics\model::LOW_SCORE,
                )
            ),
            'good' => array(
                'modelquality' => 'perfect',
                'ncourses' => 50,
                'expectedresults' => array(
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
     * Generates $ncourses courses
     *
     * @param  int $ncourses The number of courses to be generated.
     * @param  array $params Course params
     * @return null
     */
    protected function generate_courses($ncourses, array $params = []) {

        $params = $params + [
            'startdate' => mktime(0, 0, 0, 10, 24, 2015),
            'enddate' => mktime(0, 0, 0, 2, 24, 2016),
        ];

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
