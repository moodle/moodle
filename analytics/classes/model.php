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

    /**
     * All as expected.
     */
    const OK = 0;

    /**
     * There was a problem.
     */
    const GENERAL_ERROR = 1;

    /**
     * No dataset to analyse.
     */
    const NO_DATASET = 2;

    /**
     * Model with low prediction accuracy.
     */
    const LOW_SCORE = 4;

    /**
     * Not enough data to evaluate the model properly.
     */
    const NOT_ENOUGH_DATA = 8;

    /**
     * Invalid analysable for the time splitting method.
     */
    const ANALYSABLE_REJECTED_TIME_SPLITTING_METHOD = 4;

    /**
     * Invalid analysable for all time splitting methods.
     */
    const ANALYSABLE_STATUS_INVALID_FOR_RANGEPROCESSORS = 8;

    /**
     * Invalid analysable for the target
     */
    const ANALYSABLE_STATUS_INVALID_FOR_TARGET = 16;

    /**
     * Minimum score to consider a non-static prediction model as good.
     */
    const MIN_SCORE = 0.7;

    /**
     * Minimum prediction confidence (from 0 to 1) to accept a prediction as reliable enough.
     */
    const PREDICTION_MIN_SCORE = 0.6;

    /**
     * Maximum standard deviation between different evaluation repetitions to consider that evaluation results are stable.
     */
    const ACCEPTED_DEVIATION = 0.05;

    /**
     * Number of evaluation repetitions.
     */
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
     * @var \core_analytics\predictor
     */
    protected $predictionsprocessor = null;

    /**
     * @var \core_analytics\local\indicator\base[]
     */
    protected $indicators = null;

    /**
     * @var \context[]
     */
    protected $contexts = null;

    /**
     * Unique Model id created from site info and last model modification.
     *
     * @var string
     */
    protected $uniqueid = null;

    /**
     * Constructor.
     *
     * @param int|\stdClass $model
     * @return void
     */
    public function __construct($model) {
        global $DB;

        if (is_scalar($model)) {
            $model = $DB->get_record('analytics_models', array('id' => $model), '*', MUST_EXIST);
            if (!$model) {
                throw new \moodle_exception('errorunexistingmodel', 'analytics', '', $model);
            }
        }
        $this->model = $model;
    }

    /**
     * Quick safety check to discard site models which required components are not available anymore.
     *
     * @return bool
     */
    public function is_available() {
        $target = $this->get_target();
        if (!$target) {
            return false;
        }

        $classname = $target->get_analyser_class();
        if (!class_exists($classname)) {
            return false;
        }

        return true;
    }

    /**
     * Returns the model id.
     *
     * @return int
     */
    public function get_id() {
        return $this->model->id;
    }

    /**
     * Returns a plain \stdClass with the model data.
     *
     * @return \stdClass
     */
    public function get_model_obj() {
        return $this->model;
    }

    /**
     * Returns the model target.
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
     * Returns the model indicators.
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
            $this->init_analyser(array('notimesplitting' => true));
        }

        foreach ($indicators as $classname => $indicator) {
            if ($this->analyser->check_indicator_requirements($indicator) !== true) {
                unset($indicators[$classname]);
            }
        }
        return $indicators;
    }

    /**
     * Returns the model analyser (defined by the model target).
     *
     * @param array $options Default initialisation with no options.
     * @return \core_analytics\local\analyser\base
     */
    public function get_analyser($options = array()) {
        if ($this->analyser !== null) {
            return $this->analyser;
        }

        $this->init_analyser($options);

        return $this->analyser;
    }

    /**
     * Initialises the model analyser.
     *
     * @throws \coding_exception
     * @param array $options
     * @return void
     */
    protected function init_analyser($options = array()) {

        $target = $this->get_target();
        $indicators = $this->get_indicators();

        if (empty($target)) {
            throw new \moodle_exception('errornotarget', 'analytics');
        }

        $potentialtimesplittings = $this->get_potential_timesplittings();

        $timesplittings = array();
        if (empty($options['notimesplitting'])) {
            if (!empty($options['evaluation'])) {
                // The evaluation process will run using all available time splitting methods unless one is specified.
                if (!empty($options['timesplitting'])) {
                    $timesplitting = \core_analytics\manager::get_time_splitting($options['timesplitting']);

                    if (empty($potentialtimesplittings[$timesplitting->get_id()])) {
                        throw new \moodle_exception('errorcannotusetimesplitting', 'analytics');
                    }
                    $timesplittings = array($timesplitting->get_id() => $timesplitting);
                } else {
                    $timesplittingsforevaluation = \core_analytics\manager::get_time_splitting_methods_for_evaluation();

                    // They both have the same objects, using $potentialtimesplittings as its items are sorted.
                    $timesplittings = array_intersect_key($potentialtimesplittings, $timesplittingsforevaluation);
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
        }

        $classname = $target->get_analyser_class();
        if (!class_exists($classname)) {
            throw new \coding_exception($classname . ' class does not exists');
        }

        // Returns a \core_analytics\local\analyser\base class.
        $this->analyser = new $classname($this->model->id, $target, $indicators, $timesplittings, $options);
    }

    /**
     * Returns the model time splitting method.
     *
     * @return \core_analytics\local\time_splitting\base|false Returns false if no time splitting.
     */
    public function get_time_splitting() {
        if (empty($this->model->timesplitting)) {
            return false;
        }
        return \core_analytics\manager::get_time_splitting($this->model->timesplitting);
    }

    /**
     * Returns the time-splitting methods that can be used by this model.
     *
     * @return \core_analytics\local\time_splitting\base[]
     */
    public function get_potential_timesplittings() {

        $timesplittings = \core_analytics\manager::get_all_time_splittings();
        uasort($timesplittings, function($a, $b) {
            return strcasecmp($a->get_name(), $b->get_name());
        });

        foreach ($timesplittings as $key => $timesplitting) {
            if (!$this->get_target()->can_use_timesplitting($timesplitting)) {
                unset($timesplittings[$key]);
                continue;
            }
        }
        return $timesplittings;
    }

    /**
     * Creates a new model. Enables it if $timesplittingid is specified.
     *
     * @param \core_analytics\local\target\base $target
     * @param \core_analytics\local\indicator\base[] $indicators
     * @param string|false $timesplittingid The time splitting method id (its fully qualified class name)
     * @param string|null $processor The machine learning backend this model will use.
     * @return \core_analytics\model
     */
    public static function create(\core_analytics\local\target\base $target, array $indicators,
                                  $timesplittingid = false, $processor = null) {
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

        if ($target->based_on_assumptions()) {
            $modelobj->trained = 1;
        }

        if ($timesplittingid) {
            if (!\core_analytics\manager::is_valid($timesplittingid, '\core_analytics\local\time_splitting\base')) {
                throw new \moodle_exception('errorinvalidtimesplitting', 'analytics');
            }
            if (substr($timesplittingid, 0, 1) !== '\\') {
                throw new \moodle_exception('errorinvalidtimesplitting', 'analytics');
            }
            $modelobj->timesplitting = $timesplittingid;
        }

        if ($processor &&
                !manager::is_valid($processor, '\core_analytics\classifier') &&
                !manager::is_valid($processor, '\core_analytics\regressor')) {
            throw new \coding_exception('The provided predictions processor \\' . $processor . '\processor is not valid');
        } else {
            $modelobj->predictionsprocessor = $processor;
        }

        $id = $DB->insert_record('analytics_models', $modelobj);

        // Get db defaults.
        $modelobj = $DB->get_record('analytics_models', array('id' => $id), '*', MUST_EXIST);

        $model = new static($modelobj);

        return $model;
    }

    /**
     * Does this model exist?
     *
     * If no indicators are provided it considers any model with the provided
     * target a match.
     *
     * @param \core_analytics\local\target\base $target
     * @param \core_analytics\local\indicator\base[]|false $indicators
     * @return bool
     */
    public static function exists(\core_analytics\local\target\base $target, $indicators = false) {
        global $DB;

        $existingmodels = $DB->get_records('analytics_models', array('target' => $target->get_id()));

        if (!$existingmodels) {
            return false;
        }

        if (!$indicators && $existingmodels) {
            return true;
        }

        $indicatorids = array_keys($indicators);
        sort($indicatorids);

        foreach ($existingmodels as $modelobj) {
            $model = new \core_analytics\model($modelobj);
            $modelindicatorids = array_keys($model->get_indicators());
            sort($modelindicatorids);

            if ($indicatorids === $modelindicatorids) {
                return true;
            }
        }
        return false;
    }

    /**
     * Updates the model.
     *
     * @param int|bool $enabled
     * @param \core_analytics\local\indicator\base[]|false $indicators False to respect current indicators
     * @param string|false $timesplittingid False to respect current time splitting method
     * @param string|false $predictionsprocessor False to respect current predictors processor value
     * @param int[]|false $contextids List of context ids for this model. False to respect the current list of contexts.
     * @return void
     */
    public function update($enabled, $indicators = false, $timesplittingid = '', $predictionsprocessor = false,
            $contextids = false) {
        global $USER, $DB;

        \core_analytics\manager::check_can_manage_models();

        $now = time();

        if ($indicators !== false) {
            $indicatorclasses = self::indicator_classes($indicators);
            $indicatorsstr = json_encode($indicatorclasses);
        } else {
            // Respect current value.
            $indicatorsstr = $this->model->indicators;
        }

        if ($timesplittingid === false) {
            // Respect current value.
            $timesplittingid = $this->model->timesplitting;
        }

        if ($predictionsprocessor === false) {
            // Respect current value.
            $predictionsprocessor = $this->model->predictionsprocessor;
        }

        if ($contextids === false) {
            $contextsstr = $this->model->contextids;
        } else if (!$contextids) {
            $contextsstr = null;
        } else {
            $contextsstr = json_encode($contextids);

            // Reset the internal cache.
            $this->contexts = null;
        }

        if ($this->model->timesplitting !== $timesplittingid ||
                $this->model->indicators !== $indicatorsstr ||
                $this->model->predictionsprocessor !== $predictionsprocessor) {

            // Delete generated predictions before changing the model version.
            $this->clear();

            // It needs to be reset as the version changes.
            $this->uniqueid = null;
            $this->indicators = null;

            // We update the version of the model so different time splittings are not mixed up.
            $this->model->version = $now;

            // Reset trained flag.
            if (!$this->is_static()) {
                $this->model->trained = 0;
            }

        } else if ($this->model->enabled != $enabled) {
            // We purge the cached contexts with insights as some will not be visible anymore.
            $this->purge_insights_cache();
        }

        $this->model->enabled = intval($enabled);
        $this->model->indicators = $indicatorsstr;
        $this->model->timesplitting = $timesplittingid;
        $this->model->predictionsprocessor = $predictionsprocessor;
        $this->model->contextids = $contextsstr;
        $this->model->timemodified = $now;
        $this->model->usermodified = $USER->id;

        $DB->update_record('analytics_models', $this->model);
    }

    /**
     * Removes the model.
     *
     * @return void
     */
    public function delete() {
        global $DB;

        \core_analytics\manager::check_can_manage_models();

        $this->clear();

        // Method self::clear is already clearing the current model version.
        $predictor = $this->get_predictions_processor(false);
        if ($predictor->is_ready() !== true) {
            $predictorname = \core_analytics\manager::get_predictions_processor_name($predictor);
            debugging('Prediction processor ' . $predictorname . ' is not ready to be used. Model ' .
                $this->model->id . ' could not be deleted.');
        } else {
            $predictor->delete_output_dir($this->get_output_dir(array(), true), $this->get_unique_id());
        }

        $DB->delete_records('analytics_models', array('id' => $this->model->id));
        $DB->delete_records('analytics_models_log', array('modelid' => $this->model->id));
    }

    /**
     * Evaluates the model.
     *
     * This method gets the site contents (through the analyser) creates a .csv dataset
     * with them and evaluates the model prediction accuracy multiple times using the
     * machine learning backend. It returns an object where the model score is the average
     * prediction accuracy of all executed evaluations.
     *
     * @param array $options
     * @return \stdClass[]
     */
    public function evaluate($options = array()) {

        \core_analytics\manager::check_can_manage_models();

        if ($this->is_static()) {
            $this->get_analyser()->add_log(get_string('noevaluationbasedassumptions', 'analytics'));
            $result = new \stdClass();
            $result->status = self::NO_DATASET;
            return array($result);
        }

        $options['evaluation'] = true;

        if (empty($options['mode'])) {
            $options['mode'] = 'configuration';
        }

        switch ($options['mode']) {
            case 'trainedmodel':

                // We are only interested on the time splitting method used by the trained model.
                $options['timesplitting'] = $this->model->timesplitting;

                // Provide the trained model directory to the ML backend if that is what we want to evaluate.
                $trainedmodeldir = $this->get_output_dir(['execution']);
                break;
            case 'configuration':

                $trainedmodeldir = false;
                break;

            default:
                throw new \moodle_exception('errorunknownaction', 'analytics');
        }

        $this->init_analyser($options);

        if (empty($this->get_indicators())) {
            throw new \moodle_exception('errornoindicators', 'analytics');
        }

        $this->heavy_duty_mode();

        // Before get_labelled_data call so we get an early exception if it is not ready.
        $predictor = $this->get_predictions_processor();

        $datasets = $this->get_analyser()->get_labelled_data($this->get_contexts());

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
            if ($this->get_target()->is_linear()) {
                $predictorresult = $predictor->evaluate_regression($this->get_unique_id(), self::ACCEPTED_DEVIATION,
                    self::EVALUATION_ITERATIONS, $dataset, $outputdir, $trainedmodeldir);
            } else {
                $predictorresult = $predictor->evaluate_classification($this->get_unique_id(), self::ACCEPTED_DEVIATION,
                    self::EVALUATION_ITERATIONS, $dataset, $outputdir, $trainedmodeldir);
            }

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

            $result->logid = $this->log_result($timesplitting->get_id(), $result->score, $dir, $result->info, $options['mode']);

            $results[$timesplitting->get_id()] = $result;
        }

        return $results;
    }

    /**
     * Trains the model using the site contents.
     *
     * This method prepares a dataset from the site contents (through the analyser)
     * and passes it to the machine learning backends. Static models are skipped as
     * they do not require training.
     *
     * @return \stdClass
     */
    public function train() {

        \core_analytics\manager::check_can_manage_models();

        if ($this->is_static()) {
            $this->get_analyser()->add_log(get_string('notrainingbasedassumptions', 'analytics'));
            $result = new \stdClass();
            $result->status = self::OK;
            return $result;
        }

        if (!$this->is_enabled() || empty($this->model->timesplitting)) {
            throw new \moodle_exception('invalidtimesplitting', 'analytics', '', $this->model->id);
        }

        if (empty($this->get_indicators())) {
            throw new \moodle_exception('errornoindicators', 'analytics');
        }

        $this->heavy_duty_mode();

        // Before get_labelled_data call so we get an early exception if it is not writable.
        $outputdir = $this->get_output_dir(array('execution'));

        // Before get_labelled_data call so we get an early exception if it is not ready.
        $predictor = $this->get_predictions_processor();

        $datasets = $this->get_analyser()->get_labelled_data($this->get_contexts());

        // No training if no files have been provided.
        if (empty($datasets) || empty($datasets[$this->model->timesplitting])) {

            $result = new \stdClass();
            $result->status = self::NO_DATASET;
            $result->info = $this->get_analyser()->get_logs();
            return $result;
        }
        $samplesfile = $datasets[$this->model->timesplitting];

        // Train using the dataset.
        if ($this->get_target()->is_linear()) {
            $predictorresult = $predictor->train_regression($this->get_unique_id(), $samplesfile, $outputdir);
        } else {
            $predictorresult = $predictor->train_classification($this->get_unique_id(), $samplesfile, $outputdir);
        }

        $result = new \stdClass();
        $result->status = $predictorresult->status;
        $result->info = $predictorresult->info;

        if ($result->status !== self::OK) {
            return $result;
        }

        $this->flag_file_as_used($samplesfile, 'trained');

        // Mark the model as trained if it wasn't.
        if ($this->model->trained == false) {
            $this->mark_as_trained();
        }

        return $result;
    }

    /**
     * Get predictions from the site contents.
     *
     * It analyses the site contents (through analyser classes) looking for samples
     * ready to receive predictions. It generates a dataset with all samples ready to
     * get predictions and it passes it to the machine learning backends or to the
     * targets based on assumptions to get the predictions.
     *
     * @return \stdClass
     */
    public function predict() {
        global $DB;

        \core_analytics\manager::check_can_manage_models();

        if (!$this->is_enabled() || empty($this->model->timesplitting)) {
            throw new \moodle_exception('invalidtimesplitting', 'analytics', '', $this->model->id);
        }

        if (empty($this->get_indicators())) {
            throw new \moodle_exception('errornoindicators', 'analytics');
        }

        $this->heavy_duty_mode();

        // Before get_unlabelled_data call so we get an early exception if it is not writable.
        $outputdir = $this->get_output_dir(array('execution'));

        if (!$this->is_static()) {
            // Predictions using a machine learning backend.

            // Before get_unlabelled_data call so we get an early exception if it is not ready.
            $predictor = $this->get_predictions_processor();

            $samplesdata = $this->get_analyser()->get_unlabelled_data($this->get_contexts());

            // Get the prediction samples file.
            if (empty($samplesdata) || empty($samplesdata[$this->model->timesplitting])) {

                $result = new \stdClass();
                $result->status = self::NO_DATASET;
                $result->info = $this->get_analyser()->get_logs();
                return $result;
            }
            $samplesfile = $samplesdata[$this->model->timesplitting];

            // We need to throw an exception if we are trying to predict stuff that was already predicted.
            $params = array('modelid' => $this->model->id, 'action' => 'predicted', 'fileid' => $samplesfile->get_id());
            if ($predicted = $DB->get_record('analytics_used_files', $params)) {
                throw new \moodle_exception('erroralreadypredict', 'analytics', '', $samplesfile->get_id());
            }

            $indicatorcalculations = \core_analytics\dataset_manager::get_structured_data($samplesfile);

            // Estimation and classification processes run on the machine learning backend side.
            if ($this->get_target()->is_linear()) {
                $predictorresult = $predictor->estimate($this->get_unique_id(), $samplesfile, $outputdir);
            } else {
                $predictorresult = $predictor->classify($this->get_unique_id(), $samplesfile, $outputdir);
            }

            // Prepare the results object.
            $result = new \stdClass();
            $result->status = $predictorresult->status;
            $result->info = $predictorresult->info;
            $result->predictions = $this->format_predictor_predictions($predictorresult);

        } else {
            // Predictions based on assumptions.

            $indicatorcalculations = $this->get_analyser()->get_static_data($this->get_contexts());
            // Get the prediction samples file.
            if (empty($indicatorcalculations) || empty($indicatorcalculations[$this->model->timesplitting])) {

                $result = new \stdClass();
                $result->status = self::NO_DATASET;
                $result->info = $this->get_analyser()->get_logs();
                return $result;
            }

            // Same as reset($indicatorcalculations) as models based on assumptions only analyse 1 single
            // time-splitting method.
            $indicatorcalculations = $indicatorcalculations[$this->model->timesplitting];

            // Prepare the results object.
            $result = new \stdClass();
            $result->status = self::OK;
            $result->info = [];
            $result->predictions = $this->get_static_predictions($indicatorcalculations);
        }

        if ($result->status !== self::OK) {
            return $result;
        }

        if ($result->predictions) {
            list($samplecontexts, $predictionrecords) = $this->execute_prediction_callbacks($result->predictions,
                $indicatorcalculations);
        }

        if (!empty($samplecontexts) && $this->uses_insights()) {
            $this->trigger_insights($samplecontexts, $predictionrecords);
        }

        if (!$this->is_static()) {
            $this->flag_file_as_used($samplesfile, 'predicted');
        }

        return $result;
    }

    /**
     * Returns the model predictions processor.
     *
     * @param bool $checkisready
     * @return \core_analytics\predictor
     */
    public function get_predictions_processor($checkisready = true) {
        return manager::get_predictions_processor($this->model->predictionsprocessor, $checkisready);
    }

    /**
     * Formats the predictor results.
     *
     * @param array $predictorresult
     * @return array
     */
    private function format_predictor_predictions($predictorresult) {

        $predictions = array();
        if (!empty($predictorresult->predictions)) {
            foreach ($predictorresult->predictions as $sampleinfo) {

                // We parse each prediction.
                switch (count($sampleinfo)) {
                    case 1:
                        // For whatever reason the predictions processor could not process this sample, we
                        // skip it and do nothing with it.
                        debugging($this->model->id . ' model predictions processor could not process the sample with id ' .
                            $sampleinfo[0], DEBUG_DEVELOPER);
                        continue 2;
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
                $predictions[$uniquesampleid] = $predictiondata;
            }
        }
        return $predictions;
    }

    /**
     * Execute the prediction callbacks defined by the target.
     *
     * @param \stdClass[] $predictions
     * @param array $indicatorcalculations
     * @return array
     */
    protected function execute_prediction_callbacks(&$predictions, $indicatorcalculations) {

        // Here we will store all predictions' contexts, this will be used to limit which users will see those predictions.
        $samplecontexts = array();
        $records = array();

        foreach ($predictions as $uniquesampleid => $prediction) {

            // The unique sample id contains both the sampleid and the rangeindex.
            list($sampleid, $rangeindex) = $this->get_time_splitting()->infer_sample_info($uniquesampleid);
            if ($this->get_target()->triggers_callback($prediction->prediction, $prediction->predictionscore)) {

                // Prepare the record to store the predicted values.
                list($record, $samplecontext) = $this->prepare_prediction_record($sampleid, $rangeindex, $prediction->prediction,
                    $prediction->predictionscore, json_encode($indicatorcalculations[$uniquesampleid]));

                // We will later bulk-insert them all.
                $records[$uniquesampleid] = $record;

                // Also store all samples context to later generate insights or whatever action the target wants to perform.
                $samplecontexts[$samplecontext->id] = $samplecontext;

                $this->get_target()->prediction_callback($this->model->id, $sampleid, $rangeindex, $samplecontext,
                    $prediction->prediction, $prediction->predictionscore);
            }
        }

        if (!empty($records)) {
            $this->save_predictions($records);
        }

        return [$samplecontexts, $records];
    }

    /**
     * Generates insights and updates the cache.
     *
     * @param \context[] $samplecontexts
     * @param  \stdClass[] $predictionrecords
     * @return void
     */
    protected function trigger_insights($samplecontexts, $predictionrecords) {

        // Notify the target that all predictions have been processed.
        if ($this->get_analyser()::one_sample_per_analysable()) {

            // We need to do something unusual here. self::save_predictions uses the bulk-insert function (insert_records()) for
            // performance reasons and that function does not return us the inserted ids. We need to retrieve them from
            // the database, and we need to do it using one single database query (for performance reasons as well).
            $predictionrecords = $this->add_prediction_ids($predictionrecords);

            $samplesdata = $this->predictions_sample_data($predictionrecords);
            $samplesdata = $this->append_calculations_info($predictionrecords, $samplesdata);

            $predictions = array_map(function($predictionobj) use ($samplesdata) {
                $prediction = new \core_analytics\prediction($predictionobj, $samplesdata[$predictionobj->sampleid]);
                return $prediction;
            }, $predictionrecords);
        } else {
            $predictions = [];
        }

        $this->get_target()->generate_insight_notifications($this->model->id, $samplecontexts, $predictions);

        if ($this->get_target()->link_insights_report()) {

            // Update cache.
            foreach ($samplecontexts as $context) {
                \core_analytics\manager::cached_models_with_insights($context, $this->get_id());
            }
        }
    }

    /**
     * Get predictions from a static model.
     *
     * @param array $indicatorcalculations
     * @return \stdClass[]
     */
    protected function get_static_predictions(&$indicatorcalculations) {

        $headers = array_shift($indicatorcalculations);

        // Get rid of the sampleid header.
        array_shift($headers);

        // Group samples by analysable for \core_analytics\local\target::calculate.
        $analysables = array();
        // List all sampleids together.
        $sampleids = array();

        foreach ($indicatorcalculations as $uniquesampleid => $indicators) {

            // Get rid of the sampleid column.
            unset($indicators[0]);
            $indicators = array_combine($headers, $indicators);
            $indicatorcalculations[$uniquesampleid] = $indicators;

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
        list($sampleids, $samplesdata) = $this->get_samples($sampleids);

        // Calculate the targets.
        $predictions = array();
        foreach ($analysables as $analysableclass => $rangedata) {
            foreach ($rangedata as $rangeindex => $data) {

                // Attach samples data and calculated indicators data.
                $this->get_target()->clear_sample_data();
                $this->get_target()->add_sample_data($samplesdata);
                $this->get_target()->add_sample_data($data->indicatorsdata);

                // Append new elements (we can not get duplicates because sample-analysable relation is N-1).
                $timesplitting = $this->get_time_splitting();
                $timesplitting->set_modelid($this->get_id());
                $timesplitting->set_analysable($data->analysable);
                $range = $timesplitting->get_range_by_index($rangeindex);

                $this->get_target()->filter_out_invalid_samples($data->sampleids, $data->analysable, false);
                $calculations = $this->get_target()->calculate($data->sampleids, $data->analysable, $range['start'], $range['end']);

                // Missing $indicatorcalculations values in $calculations are caused by is_valid_sample. We need to remove
                // these $uniquesampleid from $indicatorcalculations because otherwise they will be stored as calculated
                // by self::save_prediction.
                $indicatorcalculations = array_filter($indicatorcalculations, function($indicators, $uniquesampleid)
                        use ($calculations, $rangeindex) {
                    list($sampleid, $indicatorsrangeindex) = $this->get_time_splitting()->infer_sample_info($uniquesampleid);
                    if ($rangeindex == $indicatorsrangeindex && !isset($calculations[$sampleid])) {
                        return false;
                    }
                    return true;
                }, ARRAY_FILTER_USE_BOTH);

                foreach ($calculations as $sampleid => $value) {

                    $uniquesampleid = $this->get_time_splitting()->append_rangeindex($sampleid, $rangeindex);

                    // Null means that the target couldn't calculate the sample, we also remove them from $indicatorcalculations.
                    if (is_null($calculations[$sampleid])) {
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
     * Stores the prediction in the database.
     *
     * @param int $sampleid
     * @param int $rangeindex
     * @param int $prediction
     * @param float $predictionscore
     * @param string $calculations
     * @return \context
     */
    protected function prepare_prediction_record($sampleid, $rangeindex, $prediction, $predictionscore, $calculations) {
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

        $analysable = $this->get_analyser()->get_sample_analysable($sampleid);
        $timesplitting = $this->get_time_splitting();
        $timesplitting->set_modelid($this->get_id());
        $timesplitting->set_analysable($analysable);
        $range = $timesplitting->get_range_by_index($rangeindex);
        if ($range) {
            $record->timestart = $range['start'];
            $record->timeend = $range['end'];
        }

        return array($record, $context);
    }

    /**
     * Save the prediction objects.
     *
     * @param \stdClass[] $records
     */
    protected function save_predictions($records) {
        global $DB;
        $DB->insert_records('analytics_predictions', $records);
    }

    /**
     * Enabled the model using the provided time splitting method.
     *
     * @param string|false $timesplittingid False to respect the current time splitting method.
     * @return void
     */
    public function enable($timesplittingid = false) {
        global $DB, $USER;

        $now = time();

        if ($timesplittingid && $timesplittingid !== $this->model->timesplitting) {

            if (!\core_analytics\manager::is_valid($timesplittingid, '\core_analytics\local\time_splitting\base')) {
                throw new \moodle_exception('errorinvalidtimesplitting', 'analytics');
            }

            if (substr($timesplittingid, 0, 1) !== '\\') {
                throw new \moodle_exception('errorinvalidtimesplitting', 'analytics');
            }

            // Delete generated predictions before changing the model version.
            $this->clear();

            // It needs to be reset as the version changes.
            $this->uniqueid = null;

            $this->model->timesplitting = $timesplittingid;
            $this->model->version = $now;

            // Reset trained flag.
            if (!$this->is_static()) {
                $this->model->trained = 0;
            }
        } else if (empty($this->model->timesplitting)) {
            // A valid timesplitting method needs to be supplied before a model can be enabled.
            throw new \moodle_exception('invalidtimesplitting', 'analytics', '', $this->model->id);

        }

        // Purge pages with insights as this may change things.
        if ($this->model->enabled != 1) {
            $this->purge_insights_cache();
        }

        $this->model->enabled = 1;
        $this->model->timemodified = $now;
        $this->model->usermodified = $USER->id;

        // We don't always update timemodified intentionally as we reserve it for target, indicators or timesplitting updates.
        $DB->update_record('analytics_models', $this->model);
    }

    /**
     * Is this a static model (as defined by the target)?.
     *
     * Static models are based on assumptions instead of in machine learning
     * backends results.
     *
     * @return bool
     */
    public function is_static() {
        return (bool)$this->get_target()->based_on_assumptions();
    }

    /**
     * Is this model enabled?
     *
     * @return bool
     */
    public function is_enabled() {
        return (bool)$this->model->enabled;
    }

    /**
     * Is this model already trained?
     *
     * @return bool
     */
    public function is_trained() {
        // Models which targets are based on assumptions do not need training.
        return (bool)$this->model->trained || $this->is_static();
    }

    /**
     * Marks the model as trained
     *
     * @return void
     */
    public function mark_as_trained() {
        global $DB;

        \core_analytics\manager::check_can_manage_models();

        $this->model->trained = 1;
        $DB->update_record('analytics_models', $this->model);
    }

    /**
     * Get the contexts with predictions.
     *
     * @param bool $skiphidden Skip hidden predictions
     * @return \stdClass[]
     */
    public function get_predictions_contexts($skiphidden = true) {
        global $DB, $USER;

        $sql = "SELECT DISTINCT ap.contextid FROM {analytics_predictions} ap
                  JOIN {context} ctx ON ctx.id = ap.contextid
                 WHERE ap.modelid = :modelid";
        $params = array('modelid' => $this->model->id);

        if ($skiphidden) {
            $sql .= " AND NOT EXISTS (
              SELECT 1
                FROM {analytics_prediction_actions} apa
               WHERE apa.predictionid = ap.id AND apa.userid = :userid AND
                     (apa.actionname = :fixed OR apa.actionname = :notuseful OR
                     apa.actionname = :useful OR apa.actionname = :notapplicable OR
                     apa.actionname = :incorrectlyflagged)
            )";
            $params['userid'] = $USER->id;
            $params['fixed'] = \core_analytics\prediction::ACTION_FIXED;
            $params['notuseful'] = \core_analytics\prediction::ACTION_NOT_USEFUL;
            $params['useful'] = \core_analytics\prediction::ACTION_USEFUL;
            $params['notapplicable'] = \core_analytics\prediction::ACTION_NOT_APPLICABLE;
            $params['incorrectlyflagged'] = \core_analytics\prediction::ACTION_INCORRECTLY_FLAGGED;
        }

        return $DB->get_records_sql($sql, $params);
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
        return $DB->record_exists('analytics_predict_samples',
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
     * @param bool $skiphidden Skip hidden predictions
     * @param int $page The page of results to fetch. False for all results.
     * @param int $perpage The max number of results to fetch. Ignored if $page is false.
     * @return array($total, \core_analytics\prediction[])
     */
    public function get_predictions(\context $context, $skiphidden = true, $page = false, $perpage = 100) {
        global $DB, $USER;

        \core_analytics\manager::check_can_list_insights($context);

        // Filters out previous predictions keeping only the last time range one.
        $sql = "SELECT ap.*
                  FROM {analytics_predictions} ap
                  JOIN (
                    SELECT sampleid, max(rangeindex) AS rangeindex
                      FROM {analytics_predictions}
                     WHERE modelid = :modelidsubap and contextid = :contextidsubap
                    GROUP BY sampleid
                  ) apsub
                  ON ap.sampleid = apsub.sampleid AND ap.rangeindex = apsub.rangeindex
                WHERE ap.modelid = :modelid and ap.contextid = :contextid";

        $params = array('modelid' => $this->model->id, 'contextid' => $context->id,
            'modelidsubap' => $this->model->id, 'contextidsubap' => $context->id);

        if ($skiphidden) {
            $sql .= " AND NOT EXISTS (
              SELECT 1
                FROM {analytics_prediction_actions} apa
               WHERE apa.predictionid = ap.id AND apa.userid = :userid AND
                     (apa.actionname = :fixed OR apa.actionname = :notuseful OR
                     apa.actionname = :useful OR apa.actionname = :notapplicable OR
                     apa.actionname = :incorrectlyflagged)
            )";
            $params['userid'] = $USER->id;
            $params['fixed'] = \core_analytics\prediction::ACTION_FIXED;
            $params['notuseful'] = \core_analytics\prediction::ACTION_NOT_USEFUL;
            $params['useful'] = \core_analytics\prediction::ACTION_USEFUL;
            $params['notapplicable'] = \core_analytics\prediction::ACTION_NOT_APPLICABLE;
            $params['incorrectlyflagged'] = \core_analytics\prediction::ACTION_INCORRECTLY_FLAGGED;
        }

        $sql .= " ORDER BY ap.timecreated DESC";
        if (!$predictions = $DB->get_records_sql($sql, $params)) {
            return array();
        }

        // Get predicted samples' ids.
        $sampleids = array_map(function($prediction) {
            return $prediction->sampleid;
        }, $predictions);

        list($unused, $samplesdata) = $this->get_samples($sampleids);

        $current = 0;

        if ($page !== false) {
            $offset = $page * $perpage;
            $limit = $offset + $perpage;
        }

        foreach ($predictions as $predictionid => $predictiondata) {

            $sampleid = $predictiondata->sampleid;

            // Filter out predictions which samples are not available anymore.
            if (empty($samplesdata[$sampleid])) {
                unset($predictions[$predictionid]);
                continue;
            }

            // Return paginated dataset - we cannot paginate in the DB because we post filter the list.
            if ($page === false || ($current >= $offset && $current < $limit)) {
                // Replace \stdClass object by \core_analytics\prediction objects.
                $prediction = new \core_analytics\prediction($predictiondata, $samplesdata[$sampleid]);
                $predictions[$predictionid] = $prediction;
            } else {
                unset($predictions[$predictionid]);
            }

            $current++;
        }

        if (empty($predictions)) {
            return array();
        }

        return [$current, $predictions];
    }

    /**
     * Returns the actions executed by users on the predictions.
     *
     * @param  \context|null $context
     * @return \moodle_recordset
     */
    public function get_prediction_actions(?\context $context): \moodle_recordset {
        global $DB;

        $sql = "SELECT apa.id, apa.predictionid, apa.userid, apa.actionname, apa.timecreated,
                       ap.contextid, ap.sampleid, ap.rangeindex, ap.prediction, ap.predictionscore
                  FROM {analytics_prediction_actions} apa
                  JOIN {analytics_predictions} ap ON ap.id = apa.predictionid
                 WHERE ap.modelid = :modelid";
        $params = ['modelid' => $this->model->id];

        if ($context) {
            $sql .= " AND ap.contextid = :contextid";
            $params['contextid'] = $context->id;
        }

        return $DB->get_recordset_sql($sql, $params);
    }

    /**
     * Returns the sample data of a prediction.
     *
     * @param \stdClass $predictionobj
     * @return array
     */
    public function prediction_sample_data($predictionobj) {

        list($unused, $samplesdata) = $this->get_samples(array($predictionobj->sampleid));

        if (empty($samplesdata[$predictionobj->sampleid])) {
            throw new \moodle_exception('errorsamplenotavailable', 'analytics');
        }

        return $samplesdata[$predictionobj->sampleid];
    }

    /**
     * Returns the samples data of the provided predictions.
     *
     * @param \stdClass[] $predictionrecords
     * @return array
     */
    public function predictions_sample_data(array $predictionrecords): array {

        $sampleids = [];
        foreach ($predictionrecords as $predictionobj) {
            $sampleids[] = $predictionobj->sampleid;
        }
        list($sampleids, $samplesdata) = $this->get_analyser()->get_samples($sampleids);

        return $samplesdata;
    }

    /**
     * Appends the calculation info to the samples data.
     *
     * @param   \stdClass[] $predictionrecords
     * @param   array $samplesdata
     * @return  array
     */
    public function append_calculations_info(array $predictionrecords, array $samplesdata): array {

        if ($extrainfo = calculation_info::pull_info($predictionrecords)) {
            foreach ($samplesdata as $sampleid => $data) {
                // The extra info come prefixed by extra: so we will not have overwrites here.
                $samplesdata[$sampleid] = $samplesdata[$sampleid] + $extrainfo[$sampleid];
            }
        }
        return $samplesdata;
    }

    /**
     * Returns the description of a sample
     *
     * @param \core_analytics\prediction $prediction
     * @return array 2 elements: list(string, \renderable)
     */
    public function prediction_sample_description(\core_analytics\prediction $prediction) {
        return $this->get_analyser()->sample_description($prediction->get_prediction_data()->sampleid,
            $prediction->get_prediction_data()->contextid, $prediction->get_sample_data());
    }

    /**
     * Returns the default output directory for prediction processors
     *
     * @return string
     */
    public static function default_output_dir(): string {
        global $CFG;

        return $CFG->dataroot . DIRECTORY_SEPARATOR . 'models';
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
     * @param bool $onlymodelid Preference over $subdirs
     * @return string
     */
    public function get_output_dir($subdirs = array(), $onlymodelid = false) {
        $subdirstr = '';
        foreach ($subdirs as $subdir) {
            $subdirstr .= DIRECTORY_SEPARATOR . $subdir;
        }

        $outputdir = get_config('analytics', 'modeloutputdir');
        if (empty($outputdir)) {
            // Apply default value.
            $outputdir = self::default_output_dir();
        }

        // Append model id.
        $outputdir .= DIRECTORY_SEPARATOR . $this->model->id;
        if (!$onlymodelid) {
            // Append version + subdirs.
            $outputdir .= DIRECTORY_SEPARATOR . $this->model->version . $subdirstr;
        }

        make_writable_directory($outputdir);

        return $outputdir;
    }

    /**
     * Returns a unique id for this model.
     *
     * This id should be unique for this site.
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
        $ids = array($CFG->wwwroot, $CFG->prefix, $this->model->id, $this->model->version);
        $this->uniqueid = sha1(implode('$$', $ids));

        return $this->uniqueid;
    }

    /**
     * Exports the model data for displaying it in a template.
     *
     * @param \renderer_base $output The renderer to use for exporting
     * @return \stdClass
     */
    public function export(\renderer_base $output) {

        \core_analytics\manager::check_can_manage_models();

        $data = clone $this->model;

        $data->modelname = format_string($this->get_name());
        $data->name = $this->inplace_editable_name()->export_for_template($output);
        $data->target = $this->get_target()->get_name();
        $data->targetclass = $this->get_target()->get_id();

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
     * Exports the model data to a zip file.
     *
     * @param string $zipfilename
     * @param bool $includeweights Include the model weights if available
     * @return string Zip file path
     */
    public function export_model(string $zipfilename, bool $includeweights = true): string {

        \core_analytics\manager::check_can_manage_models();

        $modelconfig = new model_config($this);
        return $modelconfig->export($zipfilename, $includeweights);
    }

    /**
     * Imports the provided model.
     *
     * Note that this method assumes that model_config::check_dependencies has already been called.
     *
     * @throws \moodle_exception
     * @param  string $zipfilepath Zip file path
     * @return \core_analytics\model
     */
    public static function import_model(string $zipfilepath): \core_analytics\model {

        \core_analytics\manager::check_can_manage_models();

        $modelconfig = new \core_analytics\model_config();
        return $modelconfig->import($zipfilepath);
    }

    /**
     * Can this model be exported?
     *
     * @return bool
     */
    public function can_export_configuration(): bool {

        if (empty($this->model->timesplitting)) {
            return false;
        }
        if (!$this->get_indicators()) {
            return false;
        }

        if ($this->is_static()) {
            return false;
        }

        return true;
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

        \core_analytics\manager::check_can_manage_models();

        return $DB->get_records('analytics_models_log', array('modelid' => $this->get_id()), 'timecreated DESC', '*',
            $limitfrom, $limitnum);
    }

    /**
     * Merges all training data files into one and returns it.
     *
     * @return \stored_file|false
     */
    public function get_training_data() {

        \core_analytics\manager::check_can_manage_models();

        $timesplittingid = $this->get_time_splitting()->get_id();
        return \core_analytics\dataset_manager::export_training_data($this->get_id(), $timesplittingid);
    }

    /**
     * Has the model been trained using data from this site?
     *
     * This method is useful to determine if a trained model can be evaluated as
     * we can not use the same data for training and for evaluation.
     *
     * @return bool
     */
    public function trained_locally(): bool {
        global $DB;

        if (!$this->is_trained() || $this->is_static()) {
            // Early exit.
            return false;
        }

        if ($DB->record_exists('analytics_train_samples', ['modelid' => $this->model->id])) {
            return true;
        }

        return false;
    }

    /**
     * Flag the provided file as used for training or prediction.
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
     * Log the evaluation results in the database.
     *
     * @param string $timesplittingid
     * @param float $score
     * @param string $dir
     * @param array $info
     * @param string $evaluationmode
     * @return int The inserted log id
     */
    protected function log_result($timesplittingid, $score, $dir = false, $info = false, $evaluationmode = 'configuration') {
        global $DB, $USER;

        $log = new \stdClass();
        $log->modelid = $this->get_id();
        $log->version = $this->model->version;
        $log->evaluationmode = $evaluationmode;
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
                    $indicator = '\\' . get_class($indicator);
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
    public function clear() {
        global $DB, $USER;

        \core_analytics\manager::check_can_manage_models();

        // Delete current model version stored stuff.
        $predictor = $this->get_predictions_processor(false);
        if ($predictor->is_ready() !== true) {
            $predictorname = \core_analytics\manager::get_predictions_processor_name($predictor);
            debugging('Prediction processor ' . $predictorname . ' is not ready to be used. Model ' .
                $this->model->id . ' could not be cleared.');
        } else {
            $predictor->clear_model($this->get_unique_id(), $this->get_output_dir());
        }

        $DB->delete_records_select('analytics_prediction_actions', "predictionid IN
            (SELECT id FROM {analytics_predictions} WHERE modelid = :modelid)", ['modelid' => $this->get_id()]);

        $DB->delete_records('analytics_predictions', array('modelid' => $this->model->id));
        $DB->delete_records('analytics_predict_samples', array('modelid' => $this->model->id));
        $DB->delete_records('analytics_train_samples', array('modelid' => $this->model->id));
        $DB->delete_records('analytics_used_files', array('modelid' => $this->model->id));
        $DB->delete_records('analytics_used_analysables', array('modelid' => $this->model->id));

        // Purge all generated files.
        \core_analytics\dataset_manager::clear_model_files($this->model->id);

        // We don't expect people to clear models regularly and the cost of filling the cache is
        // 1 db read per context.
        $this->purge_insights_cache();

        if (!$this->is_static()) {
            $this->model->trained = 0;
        }

        $this->model->timemodified = time();
        $this->model->usermodified = $USER->id;
        $DB->update_record('analytics_models', $this->model);
    }

    /**
     * Returns the name of the model.
     *
     * By default, models use their target's name as their own name. They can have their explicit name, too. In which
     * case, the explicit name is used instead of the default one.
     *
     * @return string|lang_string
     */
    public function get_name() {

        if (trim($this->model->name ?? '') === '') {
            return $this->get_target()->get_name();

        } else {
            return $this->model->name;
        }
    }

    /**
     * Renames the model to the given name.
     *
     * When given an empty string, the model falls back to using the associated target's name as its name.
     *
     * @param string $name The new name for the model, empty string for using the default name.
     */
    public function rename(string $name) {
        global $DB, $USER;

        $this->model->name = $name;
        $this->model->timemodified = time();
        $this->model->usermodified = $USER->id;

        $DB->update_record('analytics_models', $this->model);
    }

    /**
     * Returns an inplace editable element with the model's name.
     *
     * @return \core\output\inplace_editable
     */
    public function inplace_editable_name() {

        $displayname = format_string($this->get_name());

        return new \core\output\inplace_editable('core_analytics', 'modelname', $this->model->id,
            has_capability('moodle/analytics:managemodels', \context_system::instance()), $displayname, $this->model->name);
    }

    /**
     * Returns true if the time-splitting method used by this model is invalid for this model.
     * @return  bool
     */
    public function invalid_timesplitting_selected(): bool {
        $currenttimesplitting = $this->model->timesplitting;
        if (empty($currenttimesplitting)) {
            // Not set is different from invalid. This function is used to identify invalid
            // time-splittings.
            return false;
        }

        $potentialtimesplittings = $this->get_potential_timesplittings();
        if ($currenttimesplitting && empty($potentialtimesplittings[$currenttimesplitting])) {
            return true;
        }

        return false;
    }

    /**
     * Adds the id from {analytics_predictions} db table to the prediction \stdClass objects.
     *
     * @param  \stdClass[] $predictionrecords
     * @return \stdClass[] The prediction records including their ids in {analytics_predictions} db table.
     */
    private function add_prediction_ids($predictionrecords) {
        global $DB;

        $firstprediction = reset($predictionrecords);

        $contextids = array_map(function($predictionobj) {
            return $predictionobj->contextid;
        }, $predictionrecords);

        // Limited to 30000 records as a middle point between the ~65000 params limit in pgsql and the size limit for mysql which
        // can be increased if required up to a reasonable point.
        $chunks = array_chunk($contextids, 30000);
        foreach ($chunks as $contextidschunk) {
            list($contextsql, $contextparams) = $DB->get_in_or_equal($contextidschunk, SQL_PARAMS_NAMED);

            // We select the fields that will allow us to map ids to $predictionrecords. Given that we already filter by modelid
            // we have enough with sampleid and rangeindex. The reason is that the sampleid relation to a site is N - 1.
            $fields = 'id, sampleid, rangeindex';

            // We include the contextid and the timecreated filter to reduce the number of records in $dbpredictions. We can not
            // add as many OR conditions as records in $predictionrecords.
            $sql = "SELECT $fields
                      FROM {analytics_predictions}
                     WHERE modelid = :modelid
                           AND contextid $contextsql
                           AND timecreated >= :firsttimecreated";
            $params = $contextparams + ['modelid' => $this->model->id, 'firsttimecreated' => $firstprediction->timecreated];
            $dbpredictions = $DB->get_recordset_sql($sql, $params);
            foreach ($dbpredictions as $id => $dbprediction) {
                // The append_rangeindex implementation is the same regardless of the time splitting method in use.
                $uniqueid = $this->get_time_splitting()->append_rangeindex($dbprediction->sampleid, $dbprediction->rangeindex);
                $predictionrecords[$uniqueid]->id = $dbprediction->id;
            }
        }

        return $predictionrecords;
    }

    /**
     * Wrapper around analyser's get_samples to skip DB's max-number-of-params exception.
     *
     * @param  array  $sampleids
     * @return array
     */
    public function get_samples(array $sampleids): array {

        if (empty($sampleids)) {
            throw new \coding_exception('No sample ids provided');
        }

        $chunksize = count($sampleids);

        // We start with just 1 chunk, if it is too large for the db we split the list of sampleids in 2 and we
        // try again. We repeat this process until the chunk is small enough for the db engine to process. The
        // >= has been added in case there are other \dml_read_exceptions unrelated to the max number of params.
        while (empty($done) && $chunksize >= 1) {

            $chunks = array_chunk($sampleids, $chunksize);
            $allsampleids = [];
            $allsamplesdata = [];

            foreach ($chunks as $index => $chunk) {

                try {
                    list($chunksampleids, $chunksamplesdata) = $this->get_analyser()->get_samples($chunk);
                } catch (\dml_read_exception $e) {

                    // Reduce the chunksize, we use floor() so the $chunksize is always less than the previous $chunksize value.
                    $chunksize = floor($chunksize / 2);
                    break;
                }

                // We can sum as these two arrays are indexed by sampleid and there are no collisions.
                $allsampleids = $allsampleids + $chunksampleids;
                $allsamplesdata = $allsamplesdata + $chunksamplesdata;

                if ($index === count($chunks) - 1) {
                    // We successfully processed all the samples in all chunks, we are done.
                    $done = true;
                }
            }
        }

        if (empty($done)) {
            if (!empty($e)) {
                // Throw the last exception we caught, the \dml_read_exception we have been catching is unrelated to the max number
                // of param's exception.
                throw new \dml_read_exception($e);
            } else {
                throw new \coding_exception('We should never reach this point, there is a bug in ' .
                    'core_analytics\\model::get_samples\'s code');
            }
        }
        return [$allsampleids, $allsamplesdata];
    }

    /**
     * Contexts where this model should be active.
     *
     * @return \context[] Empty array if there are no context restrictions.
     */
    public function get_contexts() {
        if ($this->contexts !== null) {
            return $this->contexts;
        }

        if (!$this->model->contextids) {
            $this->contexts = [];
            return $this->contexts;
        }
        $contextids = json_decode($this->model->contextids);

        // We don't expect this list to be massive as contexts need to be selected manually using the edit model form.
        $this->contexts = array_map(function($contextid) {
            return \context::instance_by_id($contextid, IGNORE_MISSING);
        }, $contextids);

        return $this->contexts;
    }

    /**
     * Purges the insights cache.
     */
    private function purge_insights_cache() {
        $cache = \cache::make('core', 'contextwithinsights');
        $cache->purge();
    }

    /**
     * Increases system memory and time limits.
     *
     * @return void
     */
    private function heavy_duty_mode() {
        if (ini_get('memory_limit') != -1) {
            raise_memory_limit(MEMORY_HUGE);
        }
        \core_php_time_limit::raise();
    }
}
