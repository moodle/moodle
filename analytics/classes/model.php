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
 * Prediction model representation.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Prediction model representation.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class model {

    const OK = 0;
    const GENERAL_ERROR = 1;
    const NO_DATASET = 2;

    const EVALUATE_LOW_SCORE = 4;
    const EVALUATE_NOT_ENOUGH_DATA = 8;

    const ANALYSE_INPROGRESS = 2;
    const ANALYSE_REJECTED_RANGE_PROCESSOR = 4;
    const ANALYSABLE_STATUS_INVALID_FOR_RANGEPROCESSORS = 8;
    const ANALYSABLE_STATUS_INVALID_FOR_TARGET = 16;

    const MIN_SCORE = 0.7;
    const ACCEPTED_DEVIATION = 0.05;
    const EVALUATION_ITERATIONS = 10;

    /**
     * @var \stdClass
     */
    protected $model = null;

    /**
     * @var \core_analytics\local\analyser\base
     */
    protected $analyser = null;

    /**
     * @var \core_analytics\local\target\base
     */
    protected $target = null;

    /**
     * @var \core_analytics\local\indicator\base[]
     */
    protected $indicators = null;

    /**
     * Unique Model id created from site info and last model modification.
     *
     * @var string
     */
    protected $uniqueid = null;

    /**
     * __construct
     *
     * @param int|stdClass $model
     * @return void
     */
    public function __construct($model) {
        global $DB;

        if (is_scalar($model)) {
            $model = $DB->get_record('analytics_models', array('id' => $model));
            if (!$model) {
                throw new \moodle_exception('errorunexistingmodel', 'analytics', '', $model);
            }
        }
        $this->model = $model;
    }

    /**
     * get_id
     *
     * @return int
     */
    public function get_id() {
        return $this->model->id;
    }

    /**
     * get_model_obj
     *
     * @return \stdClass
     */
    public function get_model_obj() {
        return $this->model;
    }

    /**
     * get_target
     *
     * @return \core_analytics\local\target\base
     */
    public function get_target() {
        if ($this->target !== null) {
            return $this->target;
        }
        $instance = \core_analytics\manager::get_target($this->model->target);
        $this->target = $instance;

        return $this->target;
    }

    /**
     * get_indicators
     *
     * @return \core_analytics\local\indicator\base[]
     */
    public function get_indicators() {
        if ($this->indicators !== null) {
            return $this->indicators;
        }

        $fullclassnames = json_decode($this->model->indicators);

        if (!is_array($fullclassnames)) {
            throw new \coding_exception('Model ' . $this->model->id . ' indicators can not be read');
        }

        $this->indicators = array();
        foreach ($fullclassnames as $fullclassname) {
            $instance = \core_analytics\manager::get_indicator($fullclassname);
            if ($instance) {
                $this->indicators[$fullclassname] = $instance;
            } else {
                debugging('Can\'t load ' . $fullclassname . ' indicator', DEBUG_DEVELOPER);
            }
        }

        return $this->indicators;
    }

    /**
     * Returns the list of indicators that could potentially be used by the model target.
     *
     * It includes the indicators that are part of the model.
     *
     * @return \core_analytics\local\indicator\base[]
     */
    public function get_potential_indicators() {

        $indicators = \core_analytics\manager::get_all_indicators();

        if (empty($this->analyser)) {
            $this->init_analyser(array('evaluation' => true));
        }

        foreach ($indicators as $classname => $indicator) {
            if ($this->analyser->check_indicator_requirements($indicator) !== true) {
                unset($indicators[$classname]);
            }
        }
        return $indicators;
    }

    /**
     * get_analyser
     *
     * @return \core_analytics\local\analyser\base
     */
    public function get_analyser() {
        if ($this->analyser !== null) {
            return $this->analyser;
        }

        // Default initialisation with no options.
        $this->init_analyser();

        return $this->analyser;
    }

    /**
     * init_analyser
     *
     * @param array $options
     * @return void
     */
    protected function init_analyser($options = array()) {

        $target = $this->get_target();
        $indicators = $this->get_indicators();

        if (empty($target)) {
            throw new \moodle_exception('errornotarget', 'analytics');
        }

        if (!empty($options['evaluation'])) {
            // The evaluation process will run using all available time splitting methods unless one is specified.
            if (!empty($options['timesplitting'])) {
                $timesplitting = \core_analytics\manager::get_time_splitting($options['timesplitting']);
                $timesplittings = array($timesplitting->get_id() => $timesplitting);
            } else {
                $timesplittings = \core_analytics\manager::get_enabled_time_splitting_methods();
            }
        } else {

            if (empty($this->model->timesplitting)) {
                throw new \moodle_exception('invalidtimesplitting', 'analytics', '', $this->model->id);
            }

            // Returned as an array as all actions (evaluation, training and prediction) go through the same process.
            $timesplittings = array($this->model->timesplitting => $this->get_time_splitting());
        }

        if (empty($timesplittings)) {
            throw new \moodle_exception('errornotimesplittings', 'analytics');
        }

        $classname = $target->get_analyser_class();
        if (!class_exists($classname)) {
            throw \coding_exception($classname . ' class does not exists');
        }

        // Returns a \core_analytics\local\analyser\base class.
        $this->analyser = new $classname($this->model->id, $target, $indicators, $timesplittings, $options);
    }

    /**
     * get_time_splitting
     *
     * @return \core_analytics\local\time_splitting\base
     */
    public function get_time_splitting() {
        if (empty($this->model->timesplitting)) {
            return false;
        }
        return \core_analytics\manager::get_time_splitting($this->model->timesplitting);
    }

    /**
     * Creates a new model. Enables it if $timesplittingid is specified.
     *
     * @param \core_analytics\local\target\base $target
     * @param \core_analytics\local\indicator\base[] $indicators
     * @param string $timesplittingid The time splitting method id (its fully qualified class name)
     * @return \core_analytics\model
     */
    public static function create(\core_analytics\local\target\base $target, array $indicators, $timesplittingid = false) {
        global $USER, $DB;

        $indicatorclasses = self::indicator_classes($indicators);

        $now = time();

        $modelobj = new \stdClass();
        $modelobj->target = $target->get_id();
        $modelobj->indicators = json_encode($indicatorclasses);
        $modelobj->version = $now;
        $modelobj->timecreated = $now;
        $modelobj->timemodified = $now;
        $modelobj->usermodified = $USER->id;

        $id = $DB->insert_record('analytics_models', $modelobj);

        // Get db defaults.
        $modelobj = $DB->get_record('analytics_models', array('id' => $id), '*', MUST_EXIST);

        $model = new static($modelobj);

        if ($timesplittingid) {
            $model->enable($timesplittingid);
        }

        if ($model->is_static()) {
            $model->mark_as_trained();
        }

        return $model;
    }

    /**
     * update
     *
     * @param int|bool $enabled
     * @param \core_analytics\local\indicator\base[] $indicators
     * @param string $timesplittingid
     * @return void
     */
    public function update($enabled, $indicators, $timesplittingid = '') {
        global $USER, $DB;

        $now = time();

        $indicatorclasses = self::indicator_classes($indicators);

        $indicatorsstr = json_encode($indicatorclasses);
        if ($this->model->timesplitting !== $timesplittingid ||
                $this->model->indicators !== $indicatorsstr) {
            // We update the version of the model so different time splittings are not mixed up.
            $this->model->version = $now;

            // Delete generated predictions.
            $this->clear_model();

            // Purge all generated files.
            \core_analytics\dataset_manager::clear_model_files($this->model->id);

            // Reset trained flag.
            $this->model->trained = 0;
        }
        $this->model->enabled = intval($enabled);
        $this->model->indicators = $indicatorsstr;
        $this->model->timesplitting = $timesplittingid;
        $this->model->timemodified = $now;
        $this->model->usermodified = $USER->id;

        $DB->update_record('analytics_models', $this->model);

        // It needs to be reset (just in case, we may already used it).
        $this->uniqueid = null;
    }

    /**
     * Removes the model.
     *
     * @return void
     */
    public function delete() {
        global $DB;
        $this->clear_model();
        $DB->delete_records('analytics_models', array('id' => $this->model->id));
    }

    /**
     * Evaluates the model datasets.
     *
     * Model datasets should already be available in Moodle's filesystem.
     *
     * @param array $options
     * @return \stdClass[]
     */
    public function evaluate($options = array()) {

        if ($this->is_static()) {
            $this->get_analyser()->add_log(get_string('noevaluationbasedassumptions', 'analytics'));
            $result = new \stdClass();
            $result->status = self::OK;
            return $result;
        }

        // Increase memory limit.
        $this->increase_memory();

        $options['evaluation'] = true;
        $this->init_analyser($options);

        if (empty($this->get_indicators())) {
            throw new \moodle_exception('errornoindicators', 'analytics');
        }

        // Before get_labelled_data call so we get an early exception if it is not ready.
        $predictor = \core_analytics\manager::get_predictions_processor();

        $datasets = $this->get_analyser()->get_labelled_data();

        // No datasets generated.
        if (empty($datasets)) {
            $result = new \stdClass();
            $result->status = self::NO_DATASET;
            $result->info = $this->get_analyser()->get_logs();
            return array($result);
        }

        if (!PHPUNIT_TEST && CLI_SCRIPT) {
            echo PHP_EOL . get_string('processingsitecontents', 'analytics') . PHP_EOL;
        }

        $results = array();
        foreach ($datasets as $timesplittingid => $dataset) {

            $timesplitting = \core_analytics\manager::get_time_splitting($timesplittingid);

            $result = new \stdClass();

            $dashestimesplittingid = str_replace('\\', '', $timesplittingid);
            $outputdir = $this->get_output_dir(array('evaluation', $dashestimesplittingid));

            // Evaluate the dataset, the deviation we accept in the results depends on the amount of iterations.
            $predictorresult = $predictor->evaluate($this->model->id, self::ACCEPTED_DEVIATION,
                self::EVALUATION_ITERATIONS, $dataset, $outputdir);

            $result->status = $predictorresult->status;
            $result->info = $predictorresult->info;

            if (isset($predictorresult->score)) {
                $result->score = $predictorresult->score;
            } else {
                // Prediction processors may return an error, default to 0 score in that case.
                $result->score = 0;
            }

            $dir = false;
            if (!empty($predictorresult->dir)) {
                $dir = $predictorresult->dir;
            }

            $result->logid = $this->log_result($timesplitting->get_id(), $result->score, $dir, $result->info);

            $results[$timesplitting->get_id()] = $result;
        }

        return $results;
    }

    /**
     * train
     *
     * @return \stdClass
     */
    public function train() {
        global $DB;

        if ($this->is_static()) {
            $this->get_analyser()->add_log(get_string('notrainingbasedassumptions', 'analytics'));
            $result = new \stdClass();
            $result->status = self::OK;
            return $result;
        }

        // Increase memory limit.
        $this->increase_memory();

        if (!$this->is_enabled() || empty($this->model->timesplitting)) {
            throw new \moodle_exception('invalidtimesplitting', 'analytics', '', $this->model->id);
        }

        if (empty($this->get_indicators())) {
            throw new \moodle_exception('errornoindicators', 'analytics');
        }

        // Before get_labelled_data call so we get an early exception if it is not writable.
        $outputdir = $this->get_output_dir(array('execution'));

        // Before get_labelled_data call so we get an early exception if it is not ready.
        $predictor = \core_analytics\manager::get_predictions_processor();

        $datasets = $this->get_analyser()->get_labelled_data();

        // No training if no files have been provided.
        if (empty($datasets) || empty($datasets[$this->model->timesplitting])) {

            $result = new \stdClass();
            $result->status = self::NO_DATASET;
            $result->info = $this->get_analyser()->get_logs();
            return $result;
        }
        $samplesfile = $datasets[$this->model->timesplitting];

        // Train using the dataset.
        $predictorresult = $predictor->train($this->get_unique_id(), $samplesfile, $outputdir);

        $result = new \stdClass();
        $result->status = $predictorresult->status;
        $result->info = $predictorresult->info;

        $this->flag_file_as_used($samplesfile, 'trained');

        // Mark the model as trained if it wasn't.
        if ($this->model->trained == false) {
            $this->mark_as_trained();
        }

        return $result;
    }

    /**
     * predict
     *
     * @return \stdClass
     */
    public function predict() {
        global $DB;

        // Increase memory limit.
        $this->increase_memory();

        if (!$this->is_enabled() || empty($this->model->timesplitting)) {
            throw new \moodle_exception('invalidtimesplitting', 'analytics', '', $this->model->id);
        }

        if (empty($this->get_indicators())) {
            throw new \moodle_exception('errornoindicators', 'analytics');
        }

        // Before get_unlabelled_data call so we get an early exception if it is not writable.
        $outputdir = $this->get_output_dir(array('execution'));

        // Before get_unlabelled_data call so we get an early exception if it is not ready.
        if (!$this->is_static()) {
            $predictor = \core_analytics\manager::get_predictions_processor();
        }

        $samplesdata = $this->get_analyser()->get_unlabelled_data();

        // Get the prediction samples file.
        if (empty($samplesdata) || empty($samplesdata[$this->model->timesplitting])) {

            $result = new \stdClass();
            $result->status = self::NO_DATASET;
            $result->info = $this->get_analyser()->get_logs();
            return $result;
        }
        $samplesfile = $samplesdata[$this->model->timesplitting];

        // We need to throw an exception if we are trying to predict stuff that was already predicted.
        $params = array('modelid' => $this->model->id, 'fileid' => $samplesfile->get_id(), 'action' => 'predicted');
        if ($predicted = $DB->get_record('analytics_used_files', $params)) {
            throw new \moodle_exception('erroralreadypredict', 'analytics', '', $samplesfile->get_id());
        }

        $indicatorcalculations = \core_analytics\dataset_manager::get_structured_data($samplesfile);

        // Prepare the results object.
        $result = new \stdClass();

        if ($this->is_static()) {
            // Prediction based on assumptions.
            $result->status = \core_analytics\model::OK;
            $result->info = [];
            $result->predictions = $this->get_static_predictions($indicatorcalculations);

        } else {
            // Defer the prediction to the machine learning backend.
            $predictorresult = $predictor->predict($this->get_unique_id(), $samplesfile, $outputdir);

            $result->status = $predictorresult->status;
            $result->info = $predictorresult->info;
            $result->predictions = array();
            if ($predictorresult->predictions) {
                foreach ($predictorresult->predictions as $sampleinfo) {

                    // We parse each prediction
                    switch (count($sampleinfo)) {
                        case 1:
                            // For whatever reason the predictions processor could not process this sample, we
                            // skip it and do nothing with it.
                            debugging($this->model->id . ' model predictions processor could not process the sample with id ' .
                                $sampleinfo[0], DEBUG_DEVELOPER);
                            continue;
                        case 2:
                            // Prediction processors that do not return a prediction score will have the maximum prediction
                            // score.
                            list($uniquesampleid, $prediction) = $sampleinfo;
                            $predictionscore = 1;
                            break;
                        case 3:
                            list($uniquesampleid, $prediction, $predictionscore) = $sampleinfo;
                            break;
                        default:
                            break;
                    }
                    $predictiondata = (object)['prediction' => $prediction, 'predictionscore' => $predictionscore];
                    $result->predictions[$uniquesampleid] = $predictiondata;
                }
            }
        }

        // Here we will store all predictions' contexts, this will be used to limit which users will see those predictions.
        $samplecontexts = array();

        if ($result->predictions) {
            foreach ($result->predictions as $uniquesampleid => $prediction) {

                if ($this->get_target()->triggers_callback($prediction->prediction, $prediction->predictionscore)) {

                    // The unique sample id contains both the sampleid and the rangeindex.
                    list($sampleid, $rangeindex) = $this->get_time_splitting()->infer_sample_info($uniquesampleid);

                    // Store the predicted values.
                    $samplecontext = $this->save_prediction($sampleid, $rangeindex, $prediction->prediction, $prediction->predictionscore,
                        json_encode($indicatorcalculations[$uniquesampleid]));

                    // Also store all samples context to later generate insights or whatever action the target wants to perform.
                    $samplecontexts[$samplecontext->id] = $samplecontext;

                    $this->get_target()->prediction_callback($this->model->id, $sampleid, $rangeindex, $samplecontext,
                        $prediction->prediction, $prediction->predictionscore);
                }
            }
        }

        if (!empty($samplecontexts)) {
            // Notify the target that all predictions have been processed.
            $this->get_target()->generate_insights($this->model->id, $samplecontexts);

            // Aggressive invalidation, the cost of filling up the cache is not high.
            $cache = \cache::make('core', 'modelswithpredictions');
            foreach ($samplecontexts as $context) {
                $cache->delete($context->id);
            }
        }

        $this->flag_file_as_used($samplesfile, 'predicted');

        return $result;
    }

    /**
     * get_static_predictions
     *
     * @param array $indicatorcalculations
     * @return \stdClass[]
     */
    protected function get_static_predictions(&$indicatorcalculations) {

        // Group samples by analysable for \core_analytics\local\target::calculate.
        $analysables = array();
        // List all sampleids together.
        $sampleids = array();

        foreach ($indicatorcalculations as $uniquesampleid => $indicators) {
            list($sampleid, $rangeindex) = $this->get_time_splitting()->infer_sample_info($uniquesampleid);

            $analysable = $this->get_analyser()->get_sample_analysable($sampleid);
            $analysableclass = get_class($analysable);
            if (empty($analysables[$analysableclass])) {
                $analysables[$analysableclass] = array();
            }
            if (empty($analysables[$analysableclass][$rangeindex])) {
                $analysables[$analysableclass][$rangeindex] = (object)[
                    'analysable' => $analysable,
                    'indicatorsdata' => array(),
                    'sampleids' => array()
                ];
            }
            // Using the sampleid as a key so we can easily merge indicators data later.
            $analysables[$analysableclass][$rangeindex]->indicatorsdata[$sampleid] = $indicators;
            // We could use indicatorsdata keys but the amount of redundant data is not that big and leaves code below cleaner.
            $analysables[$analysableclass][$rangeindex]->sampleids[$sampleid] = $sampleid;

            // Accumulate sample ids to get all their associated data in 1 single db query (analyser::get_samples).
            $sampleids[$sampleid] = $sampleid;
        }

        // Get all samples data.
        list($sampleids, $samplesdata) = $this->get_analyser()->get_samples($sampleids);

        // Calculate the targets.
        $calculations = array();
        foreach ($analysables as $analysableclass => $rangedata) {
            foreach ($rangedata as $rangeindex => $data) {

                // Attach samples data and calculated indicators data.
                $this->get_target()->clear_sample_data();
                $this->get_target()->add_sample_data($samplesdata);
                $this->get_target()->add_sample_data($data->indicatorsdata);

                // Append new elements (we can not get duplicated because sample-analysable relation is N-1).
                $range = $this->get_time_splitting()->get_range_by_index($rangeindex);
                $calculations = $this->get_target()->calculate($data->sampleids, $data->analysable, $range['start'], $range['end']);

                // Missing $indicatorcalculations values in $calculations are caused by is_valid_sample. We need to remove
                // these $uniquesampleid from $indicatorcalculations because otherwise they will be stored as calculated
                // by self::save_prediction.
                $indicatorcalculations = array_filter($indicatorcalculations, function($indicators, $uniquesampleid) use ($calculations) {
                    list($sampleid, $rangeindex) = $this->get_time_splitting()->infer_sample_info($uniquesampleid);
                    if (!isset($calculations[$sampleid])) {
                        debugging($uniquesampleid . ' discarded by is_valid_sample');
                        return false;
                    }
                    return true;
                }, ARRAY_FILTER_USE_BOTH);

                foreach ($calculations as $sampleid => $value) {

                    $uniquesampleid = $this->get_time_splitting()->append_rangeindex($sampleid, $rangeindex);

                    // Null means that the target couldn't calculate the sample, we also remove them from $indicatorcalculations.
                    if (is_null($calculations[$sampleid])) {
                        debugging($uniquesampleid . ' discarded by is_valid_sample');
                        unset($indicatorcalculations[$uniquesampleid]);
                        continue;
                    }

                    // Even if static predictions are based on assumptions we flag them as 100% because they are 100%
                    // true according to what the developer defined.
                    $predictions[$uniquesampleid] = (object)['prediction' => $value, 'predictionscore' => 1];
                }
            }
        }
        return $predictions;
    }

    /**
     * save_prediction
     *
     * @param int $sampleid
     * @param int $rangeindex
     * @param int $prediction
     * @param float $predictionscore
     * @param string $calculations
     * @return \context
     */
    protected function save_prediction($sampleid, $rangeindex, $prediction, $predictionscore, $calculations) {
        global $DB;

        $context = $this->get_analyser()->sample_access_context($sampleid);

        $record = new \stdClass();
        $record->modelid = $this->model->id;
        $record->contextid = $context->id;
        $record->sampleid = $sampleid;
        $record->rangeindex = $rangeindex;
        $record->prediction = $prediction;
        $record->predictionscore = $predictionscore;
        $record->calculations = $calculations;
        $record->timecreated = time();
        $DB->insert_record('analytics_predictions', $record);

        return $context;
    }

    /**
     * enable
     *
     * @param string $timesplittingid
     * @return void
     */
    public function enable($timesplittingid = false) {
        global $DB;

        $now = time();

        if ($timesplittingid && $timesplittingid !== $this->model->timesplitting) {

            if (!\core_analytics\manager::is_valid($timesplittingid, '\core_analytics\local\time_splitting\base')) {
                throw new \moodle_exception('errorinvalidtimesplitting', 'analytics');
            }

            if (substr($timesplittingid, 0, 1) !== '\\') {
                throw new \moodle_exception('errorinvalidtimesplitting', 'analytics');
            }

            $this->model->timesplitting = $timesplittingid;
            $this->model->version = $now;
        }
        $this->model->enabled = 1;
        $this->model->timemodified = $now;

        // We don't always update timemodified intentionally as we reserve it for target, indicators or timesplitting updates.
        $DB->update_record('analytics_models', $this->model);

        // It needs to be reset (just in case, we may already used it).
        $this->uniqueid = null;
    }

    /**
     * is_static
     *
     * @return bool
     */
    public function is_static() {
        return (bool)$this->get_target()->based_on_assumptions();
    }

    /**
     * is_enabled
     *
     * @return bool
     */
    public function is_enabled() {
        return (bool)$this->model->enabled;
    }

    /**
     * is_trained
     *
     * @return bool
     */
    public function is_trained() {
        // Models which targets are based on assumptions do not need training.
        return (bool)$this->model->trained || $this->is_static();
    }

    /**
     * mark_as_trained
     *
     * @return void
     */
    public function mark_as_trained() {
        global $DB;

        $this->model->trained = 1;
        $DB->update_record('analytics_models', $this->model);
    }

    /**
     * get_predictions_contexts
     *
     * @return \stdClass[]
     */
    public function get_predictions_contexts() {
        global $DB;

        $sql = "SELECT DISTINCT contextid FROM {analytics_predictions} WHERE modelid = ?";
        return $DB->get_records_sql($sql, array($this->model->id));
    }

    /**
     * Has this model generated predictions?
     *
     * We don't check analytics_predictions table because targets have the ability to
     * ignore some predicted values, if that is the case predictions are not even stored
     * in db.
     *
     * @return bool
     */
    public function any_prediction_obtained() {
        global $DB;
        return $DB->record_exists('analytics_predict_ranges',
            array('modelid' => $this->model->id, 'timesplitting' => $this->model->timesplitting));
    }

    /**
     * Whether this model generates insights or not (defined by the model's target).
     *
     * @return bool
     */
    public function uses_insights() {
        $target = $this->get_target();
        return $target::uses_insights();
    }

    /**
     * Whether predictions exist for this context.
     *
     * @param \context $context
     * @return bool
     */
    public function predictions_exist(\context $context) {
        global $DB;

        // Filters out previous predictions keeping only the last time range one.
        $select = "modelid = :modelid AND contextid = :contextid";
        $params = array('modelid' => $this->model->id, 'contextid' => $context->id);
        return $DB->record_exists_select('analytics_predictions', $select, $params);
    }

    /**
     * Gets the predictions for this context.
     *
     * @param \context $context
     * @return \core_analytics\prediction[]
     */
    public function get_predictions(\context $context) {
        global $DB;

        // Filters out previous predictions keeping only the last time range one.
        $sql = "SELECT tip.*
                  FROM {analytics_predictions} tip
                  JOIN (
                    SELECT sampleid, max(rangeindex) AS rangeindex
                      FROM {analytics_predictions}
                     WHERE modelid = ? and contextid = ?
                    GROUP BY sampleid
                  ) tipsub
                  ON tip.sampleid = tipsub.sampleid AND tip.rangeindex = tipsub.rangeindex
                 WHERE tip.modelid = ? and tip.contextid = ?";
        $params = array($this->model->id, $context->id, $this->model->id, $context->id);
        if (!$predictions = $DB->get_records_sql($sql, $params)) {
            return array();
        }

        // Get predicted samples' ids.
        $sampleids = array_map(function($prediction) {
            return $prediction->sampleid;
        }, $predictions);

        list($unused, $samplesdata) = $this->get_analyser()->get_samples($sampleids);

        // Add samples data as part of each prediction.
        foreach ($predictions as $predictionid => $predictiondata) {

            $sampleid = $predictiondata->sampleid;

            // Filter out predictions which samples are not available anymore.
            if (empty($samplesdata[$sampleid])) {
                unset($predictions[$predictionid]);
                continue;
            }

            // Replace stdClass object by \core_analytics\prediction objects.
            $prediction = new \core_analytics\prediction($predictiondata, $samplesdata[$sampleid]);

            $predictions[$predictionid] = $prediction;
        }

        return $predictions;
    }

    /**
     * prediction_sample_data
     *
     * @param \stdClass $predictionobj
     * @return array
     */
    public function prediction_sample_data($predictionobj) {

        list($unused, $samplesdata) = $this->get_analyser()->get_samples(array($predictionobj->sampleid));

        if (empty($samplesdata[$predictionobj->sampleid])) {
            throw new \moodle_exception('errorsamplenotavailable', 'analytics');
        }

        return $samplesdata[$predictionobj->sampleid];
    }

    /**
     * prediction_sample_description
     *
     * @param \core_analytics\prediction $prediction
     * @return array 2 elements: list(string, \renderable)
     */
    public function prediction_sample_description(\core_analytics\prediction $prediction) {
        return $this->get_analyser()->sample_description($prediction->get_prediction_data()->sampleid,
            $prediction->get_prediction_data()->contextid, $prediction->get_sample_data());
    }

    /**
     * Returns the output directory for prediction processors.
     *
     * Directory structure as follows:
     * - Evaluation runs:
     *   models/$model->id/$model->version/evaluation/$model->timesplitting
     * - Training  & prediction runs:
     *   models/$model->id/$model->version/execution
     *
     * @param array $subdirs
     * @return string
     */
    protected function get_output_dir($subdirs = array()) {
        global $CFG;

        $subdirstr = '';
        foreach ($subdirs as $subdir) {
            $subdirstr .= DIRECTORY_SEPARATOR . $subdir;
        }

        $outputdir = get_config('analytics', 'modeloutputdir');
        if (empty($outputdir)) {
            // Apply default value.
            $outputdir = rtrim($CFG->dataroot, '/') . DIRECTORY_SEPARATOR . 'models';
        }

        // Append model id and version + subdirs.
        $outputdir .= DIRECTORY_SEPARATOR . $this->model->id . DIRECTORY_SEPARATOR . $this->model->version . $subdirstr;

        make_writable_directory($outputdir);

        return $outputdir;
    }

    /**
     * get_unique_id
     *
     * @return string
     */
    public function get_unique_id() {
        global $CFG;

        if (!is_null($this->uniqueid)) {
            return $this->uniqueid;
        }

        // Generate a unique id for this site, this model and this time splitting method, considering the last time
        // that the model target and indicators were updated.
        $ids = array($CFG->wwwroot, $CFG->dirroot, $CFG->prefix, $this->model->id, $this->model->version);
        $this->uniqueid = sha1(implode('$$', $ids));

        return $this->uniqueid;
    }

    /**
     * Exports the model data.
     *
     * @return \stdClass
     */
    public function export() {
        $data = clone $this->model;
        $data->target = $this->get_target()->get_name();

        if ($timesplitting = $this->get_time_splitting()) {
            $data->timesplitting = $timesplitting->get_name();
        }

        $data->indicators = array();
        foreach ($this->get_indicators() as $indicator) {
            $data->indicators[] = $indicator->get_name();
        }
        return $data;
    }

    /**
     * Returns the model logs data.
     *
     * @param int $limitfrom
     * @param int $limitnum
     * @return \stdClass[]
     */
    public function get_logs($limitfrom = 0, $limitnum = 0) {
        global $DB;
        return $DB->get_records('analytics_models_log', array('modelid' => $this->get_id()), 'timecreated DESC', '*',
            $limitfrom, $limitnum);
    }

    /**
     * flag_file_as_used
     *
     * @param \stored_file $file
     * @param string $action
     * @return void
     */
    protected function flag_file_as_used(\stored_file $file, $action) {
        global $DB;

        $usedfile = new \stdClass();
        $usedfile->modelid = $this->model->id;
        $usedfile->fileid = $file->get_id();
        $usedfile->action = $action;
        $usedfile->time = time();
        $DB->insert_record('analytics_used_files', $usedfile);
    }

    /**
     * log_result
     *
     * @param string $timesplittingid
     * @param float $score
     * @param string $dir
     * @param array $info
     * @return int The inserted log id
     */
    protected function log_result($timesplittingid, $score, $dir = false, $info = false) {
        global $DB, $USER;

        $log = new \stdClass();
        $log->modelid = $this->get_id();
        $log->version = $this->model->version;
        $log->target = $this->model->target;
        $log->indicators = $this->model->indicators;
        $log->timesplitting = $timesplittingid;
        $log->dir = $dir;
        if ($info) {
            // Ensure it is not an associative array.
            $log->info = json_encode(array_values($info));
        }
        $log->score = $score;
        $log->timecreated = time();
        $log->usermodified = $USER->id;

        return $DB->insert_record('analytics_models_log', $log);
    }

    /**
     * Utility method to return indicator class names from a list of indicator objects
     *
     * @param \core_analytics\local\indicator\base[] $indicators
     * @return string[]
     */
    private static function indicator_classes($indicators) {

        // What we want to check and store are the indicator classes not the keys.
        $indicatorclasses = array();
        foreach ($indicators as $indicator) {
            if (!\core_analytics\manager::is_valid($indicator, '\core_analytics\local\indicator\base')) {
                if (!is_object($indicator) && !is_scalar($indicator)) {
                    $indicator = strval($indicator);
                } else if (is_object($indicator)) {
                    $indicator = get_class($indicator);
                }
                throw new \moodle_exception('errorinvalidindicator', 'analytics', '', $indicator);
            }
            $indicatorclasses[] = $indicator->get_id();
        }

        return $indicatorclasses;
    }

    /**
     * Clears the model training and prediction data.
     *
     * Executed after updating model critical elements like the time splitting method
     * or the indicators.
     *
     * @return void
     */
    private function clear_model() {
        global $DB;

        $DB->delete_records('analytics_predict_ranges', array('modelid' => $this->model->id));
        $DB->delete_records('analytics_predictions', array('modelid' => $this->model->id));
        $DB->delete_records('analytics_train_samples', array('modelid' => $this->model->id));
        $DB->delete_records('analytics_used_files', array('modelid' => $this->model->id));

        $cache = \cache::make('core', 'modelswithpredictions');
        $result = $cache->purge();
    }

    private function increase_memory() {
        if (ini_get('memory_limit') != -1) {
            raise_memory_limit(MEMORY_HUGE);
        }
    }

}
