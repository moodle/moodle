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
 * Analysers base class.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\analyser;

defined('MOODLE_INTERNAL') || die();

/**
 * Analysers base class.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {

    /**
     * @var int
     */
    protected $modelid;

    /**
     * The model target.
     *
     * @var \core_analytics\local\target\base
     */
    protected $target;

    /**
     * A $this->$target copy loaded with the ongoing analysis analysable.
     *
     * @var \core_analytics\local\target\base
     */
    protected $analysabletarget;

    /**
     * The model indicators.
     *
     * @var \core_analytics\local\indicator\base[]
     */
    protected $indicators;

    /**
     * Time splitting methods to use.
     *
     * Multiple time splitting methods during evaluation and 1 single
     * time splitting method once the model is enabled.
     *
     * @var \core_analytics\local\time_splitting\base[]
     */
    protected $timesplittings;

    /**
     * Execution options.
     *
     * @var array
     */
    protected $options;

    /**
     * Simple log array.
     *
     * @var string[]
     */
    protected $log;

    /**
     * Constructor method.
     *
     * @param int $modelid
     * @param \core_analytics\local\target\base $target
     * @param \core_analytics\local\indicator\base[] $indicators
     * @param \core_analytics\local\time_splitting\base[] $timesplittings
     * @param array $options
     * @return void
     */
    public function __construct($modelid, \core_analytics\local\target\base $target, $indicators, $timesplittings, $options) {
        $this->modelid = $modelid;
        $this->target = $target;
        $this->indicators = $indicators;
        $this->timesplittings = $timesplittings;

        if (empty($options['evaluation'])) {
            $options['evaluation'] = false;
        }
        $this->options = $options;

        // Checks if the analyser satisfies the indicators requirements.
        $this->check_indicators_requirements();

        $this->log = array();
    }

    /**
     * Returns the list of analysable elements available on the site.
     *
     * \core_analytics\local\analyser\by_course and \core_analytics\local\analyser\sitewide are implementing
     * this method returning site courses (by_course) and the whole system (sitewide) as analysables.
     *
     * @return \core_analytics\analysable[] Array of analysable elements using the analysable id as array key.
     */
    abstract public function get_analysables();

    /**
     * This function returns this analysable list of samples.
     *
     * @param \core_analytics\analysable $analysable
     * @return array array[0] = int[] (sampleids) and array[1] = array (samplesdata)
     */
    abstract protected function get_all_samples(\core_analytics\analysable $analysable);

    /**
     * This function returns the samples data from a list of sample ids.
     *
     * @param int[] $sampleids
     * @return array array[0] = int[] (sampleids) and array[1] = array (samplesdata)
     */
    abstract public function get_samples($sampleids);

    /**
     * Returns the analysable of a sample.
     *
     * @param int $sampleid
     * @return \core_analytics\analysable
     */
    abstract public function get_sample_analysable($sampleid);

    /**
     * Returns the sample's origin in moodle database.
     *
     * @return string
     */
    abstract public function get_samples_origin();

    /**
     * Returns the context of a sample.
     *
     * moodle/analytics:listinsights will be required at this level to access the sample predictions.
     *
     * @param int $sampleid
     * @return \context
     */
    abstract public function sample_access_context($sampleid);

    /**
     * Describes a sample with a description summary and a \renderable (an image for example)
     *
     * @param int $sampleid
     * @param int $contextid
     * @param array $sampledata
     * @return array array(string, \renderable)
     */
    abstract public function sample_description($sampleid, $contextid, $sampledata);

    /**
     * Main analyser method which processes the site analysables.
     *
     * @param bool $includetarget
     * @return \stored_file[]
     */
    public function get_analysable_data($includetarget) {
        global $DB;

        // Time limit control.
        $modeltimelimit = intval(get_config('analytics', 'modeltimelimit'));

        $filesbytimesplitting = array();

        list($analysables, $processedanalysables) = $this->get_sorted_analysables($includetarget);

        $inittime = time();
        foreach ($analysables as $key => $analysable) {

            $files = $this->process_analysable($analysable, $includetarget);

            // Later we will need to aggregate data by time splitting method.
            foreach ($files as $timesplittingid => $file) {
                $filesbytimesplitting[$timesplittingid][] = $file;
            }

            $this->update_analysable_analysed_time($processedanalysables, $analysable->get_id(), $includetarget);

            // Apply time limit.
            if (!$this->options['evaluation']) {
                $timespent = time() - $inittime;
                if ($modeltimelimit <= $timespent) {
                    break;
                }
            }

            unset($analysables[$key]);
        }

        if ($this->options['evaluation'] === false) {
            // Look for previous training and prediction files we generated and couldn't be used
            // by machine learning backends because they weren't big enough.

            $pendingfiles = \core_analytics\dataset_manager::get_pending_files($this->modelid, $includetarget,
                array_keys($filesbytimesplitting));
            foreach ($pendingfiles as $timesplittingid => $files) {
                foreach ($files as $file) {
                    $filesbytimesplitting[$timesplittingid][] = $file;
                }
            }
        }

        // We join the datasets by time splitting method.
        $timesplittingfiles = $this->merge_analysable_files($filesbytimesplitting, $includetarget);

        if (!empty($pendingfiles)) {
            // We must remove them now as they are already part of another dataset.
            foreach ($pendingfiles as $timesplittingid => $files) {
                foreach ($files as $file) {
                    $file->delete();
                }
            }
        }

        return $timesplittingfiles;
    }

    /**
     * Samples data this analyser provides.
     *
     * @return string[]
     */
    protected function provided_sample_data() {
        return array($this->get_samples_origin());
    }

    /**
     * Returns labelled data (training and evaluation).
     *
     * @return array
     */
    public function get_labelled_data() {
        return $this->get_analysable_data(true);
    }

    /**
     * Returns unlabelled data (prediction).
     *
     * @return array
     */
    public function get_unlabelled_data() {
        return $this->get_analysable_data(false);
    }

    /**
     * Checks if the analyser satisfies all the model indicators requirements.
     *
     * @throws \core_analytics\requirements_exception
     * @return void
     */
    protected function check_indicators_requirements() {

        foreach ($this->indicators as $indicator) {
            $missingrequired = $this->check_indicator_requirements($indicator);
            if ($missingrequired !== true) {
                throw new \core_analytics\requirements_exception(get_class($indicator) . ' indicator requires ' .
                    json_encode($missingrequired) . ' sample data which is not provided by ' . get_class($this));
            }
        }
    }

    /**
     * Merges analysable dataset files into 1.
     *
     * @param array $filesbytimesplitting
     * @param bool $includetarget
     * @return \stored_file[]
     */
    protected function merge_analysable_files($filesbytimesplitting, $includetarget) {

        $timesplittingfiles = array();
        foreach ($filesbytimesplitting as $timesplittingid => $files) {

            if ($this->options['evaluation'] === true) {
                // Delete the previous copy. Only when evaluating.
                \core_analytics\dataset_manager::delete_previous_evaluation_file($this->modelid, $timesplittingid);
            }

            // Merge all course files into one.
            if ($includetarget) {
                $filearea = \core_analytics\dataset_manager::LABELLED_FILEAREA;
            } else {
                $filearea = \core_analytics\dataset_manager::UNLABELLED_FILEAREA;
            }
            $timesplittingfiles[$timesplittingid] = \core_analytics\dataset_manager::merge_datasets($files,
                $this->modelid, $timesplittingid, $filearea, $this->options['evaluation']);
        }

        return $timesplittingfiles;
    }

    /**
     * Checks that this analyser satisfies the provided indicator requirements.
     *
     * @param \core_analytics\local\indicator\base $indicator
     * @return true|string[] True if all good, missing requirements list otherwise
     */
    public function check_indicator_requirements(\core_analytics\local\indicator\base $indicator) {

        $providedsampledata = $this->provided_sample_data();

        $requiredsampledata = $indicator::required_sample_data();
        if (empty($requiredsampledata)) {
            // The indicator does not need any sample data.
            return true;
        }
        $missingrequired = array_diff($requiredsampledata, $providedsampledata);

        if (empty($missingrequired)) {
            return true;
        }

        return $missingrequired;
    }

    /**
     * Processes an analysable
     *
     * This method returns the general analysable status, an array of files by time splitting method and
     * an error message if there is any problem.
     *
     * @param \core_analytics\analysable $analysable
     * @param bool $includetarget
     * @return \stored_file[] Files by time splitting method
     */
    public function process_analysable($analysable, $includetarget) {

        // Default returns.
        $files = array();
        $message = null;

        // Target instances scope is per-analysable (it can't be lower as calculations run once per
        // analysable, not time splitting method nor time range).
        $this->analysabletarget = call_user_func(array($this->target, 'instance'));

        // We need to check that the analysable is valid for the target even if we don't include targets
        // as we still need to discard invalid analysables for the target.
        $result = $this->analysabletarget->is_valid_analysable($analysable, $includetarget);
        if ($result !== true) {
            $a = new \stdClass();
            $a->analysableid = $analysable->get_name();
            $a->result = $result;
            $this->add_log(get_string('analysablenotvalidfortarget', 'analytics', $a));
            return array();
        }

        // Process all provided time splitting methods.
        $results = array();
        foreach ($this->timesplittings as $timesplitting) {

            // For evaluation purposes we don't need to be that strict about how updated the data is,
            // if this analyser was analysed less that 1 week ago we skip generating a new one. This
            // helps scale the evaluation process as sites with tons of courses may a lot of time to
            // complete an evaluation.
            if (!empty($this->options['evaluation']) && !empty($this->options['reuseprevanalysed'])) {

                $previousanalysis = \core_analytics\dataset_manager::get_evaluation_analysable_file($this->modelid,
                    $analysable->get_id(), $timesplitting->get_id());
                // 1 week is a partly random time interval, no need to worry about DST.
                $boundary = time() - WEEKSECS;
                if ($previousanalysis && $previousanalysis->get_timecreated() > $boundary) {
                    // Recover the previous analysed file and avoid generating a new one.

                    // Don't bother filling a result object as it is only useful when there are no files generated.
                    $files[$timesplitting->get_id()] = $previousanalysis;
                    continue;
                }
            }

            $result = $this->process_time_splitting($timesplitting, $analysable, $includetarget);

            if (!empty($result->file)) {
                $files[$timesplitting->get_id()] = $result->file;
            }
            $results[] = $result;
        }

        if (empty($files)) {
            $errors = array();
            foreach ($results as $timesplittingid => $result) {
                $errors[] = $timesplittingid . ': ' . $result->message;
            }

            $a = new \stdClass();
            $a->analysableid = $analysable->get_name();
            $a->errors = implode(', ', $errors);
            $this->add_log(get_string('analysablenotused', 'analytics', $a));
        }

        return $files;
    }

    /**
     * Adds a register to the analysis log.
     *
     * @param string $string
     * @return void
     */
    public function add_log($string) {
        $this->log[] = $string;
    }

    /**
     * Returns the analysis logs.
     *
     * @return string[]
     */
    public function get_logs() {
        return $this->log;
    }

    /**
     * Whether the plugin needs user data clearing or not.
     *
     * This is related to privacy. Override this method if your analyser samples have any relation
     * to the 'user' database entity. We need to clean the site from all user-related data if a user
     * request their data to be deleted from the system. A static::provided_sample_data returning 'user'
     * is an indicator that you should be returning true.
     *
     * @return bool
     */
    public function processes_user_data() {
        return false;
    }

    /**
     * SQL JOIN from a sample to users table.
     *
     * This function should be defined if static::processes_user_data returns true and it is related to analytics API
     * privacy API implementation. It allows the analytics API to identify data associated to users that needs to be
     * deleted or exported.
     *
     * This function receives the alias of a table with a 'sampleid' field and it should return a SQL join
     * with static::get_samples_origin and with 'user' table. Note that:
     * - The function caller expects the returned 'user' table to be aliased as 'u' (defacto standard in moodle).
     * - You can join with other tables if your samples origin table does not contain a 'userid' field (if that would be
     *   a requirement this solution would be automated for you) you can't though use the following
     *   aliases: 'ap', 'apa', 'aic' and 'am'.
     *
     * Some examples:
     *
     * static::get_samples_origin() === 'user':
     *   JOIN {user} u ON {$sampletablealias}.sampleid = u.id
     *
     * static::get_samples_origin() === 'role_assignments':
     *   JOIN {role_assignments} ra ON {$sampletablealias}.sampleid = ra.userid JOIN {user} u ON u.id = ra.userid
     *
     * static::get_samples_origin() === 'user_enrolments':
     *   JOIN {user_enrolments} ue ON {$sampletablealias}.sampleid = ue.userid JOIN {user} u ON u.id = ue.userid
     *
     * @throws \coding_exception
     * @param string $sampletablealias The alias of the table with a sampleid field that will join with this SQL string
     * @return string
     */
    public function join_sample_user($sampletablealias) {
        throw new \coding_exception('This method should be implemented if static::processes_user_data returns true.');
    }

    /**
     * Processes the analysable samples using the provided time splitting method.
     *
     * @param \core_analytics\local\time_splitting\base $timesplitting
     * @param \core_analytics\analysable $analysable
     * @param bool $includetarget
     * @return \stdClass Results object.
     */
    protected function process_time_splitting($timesplitting, $analysable, $includetarget = false) {

        $result = new \stdClass();

        if (!$timesplitting->is_valid_analysable($analysable)) {
            $result->status = \core_analytics\model::ANALYSABLE_REJECTED_TIME_SPLITTING_METHOD;
            $result->message = get_string('invalidanalysablefortimesplitting', 'analytics',
                $timesplitting->get_name());
            return $result;
        }
        $timesplitting->set_analysable($analysable);

        if (CLI_SCRIPT && !PHPUNIT_TEST) {
            mtrace('Analysing id "' . $analysable->get_id() . '" with "' . $timesplitting->get_name() .
                '" time splitting method...');
        }

        // What is a sample is defined by the analyser, it can be an enrolment, a course, a user, a question
        // attempt... it is on what we will base indicators calculations.
        list($sampleids, $samplesdata) = $this->get_all_samples($analysable);

        if (count($sampleids) === 0) {
            $result->status = \core_analytics\model::ANALYSABLE_REJECTED_TIME_SPLITTING_METHOD;
            $result->message = get_string('nodata', 'analytics');
            return $result;
        }

        if ($includetarget) {
            // All ranges are used when we are calculating data for training.
            $ranges = $timesplitting->get_all_ranges();
        } else {
            // The latest range that has not yet been used for prediction (it depends on the time range where we are right now).
            $ranges = $this->get_most_recent_prediction_range($timesplitting);
        }

        // There is no need to keep track of the evaluated samples and ranges as we always evaluate the whole dataset.
        if ($this->options['evaluation'] === false) {

            if (empty($ranges)) {
                $result->status = \core_analytics\model::ANALYSABLE_REJECTED_TIME_SPLITTING_METHOD;
                $result->message = get_string('noranges', 'analytics');
                return $result;
            }

            // We skip all samples that are already part of a training dataset, even if they have not been used for prediction.
            $this->filter_out_train_samples($sampleids, $timesplitting);

            if (count($sampleids) === 0) {
                $result->status = \core_analytics\model::ANALYSABLE_REJECTED_TIME_SPLITTING_METHOD;
                $result->message = get_string('nonewdata', 'analytics');
                return $result;
            }

            // Only when processing data for predictions.
            if (!$includetarget) {
                // We also filter out samples and ranges that have already been used for predictions.
                $this->filter_out_prediction_samples_and_ranges($sampleids, $ranges, $timesplitting);
            }

            if (count($sampleids) === 0) {
                $result->status = \core_analytics\model::ANALYSABLE_REJECTED_TIME_SPLITTING_METHOD;
                $result->message = get_string('nonewdata', 'analytics');
                return $result;
            }

            if (count($ranges) === 0) {
                $result->status = \core_analytics\model::ANALYSABLE_REJECTED_TIME_SPLITTING_METHOD;
                $result->message = get_string('nonewranges', 'analytics');
                return $result;
            }
        }

        if (!empty($includetarget)) {
            $filearea = \core_analytics\dataset_manager::LABELLED_FILEAREA;
        } else {
            $filearea = \core_analytics\dataset_manager::UNLABELLED_FILEAREA;
        }
        $dataset = new \core_analytics\dataset_manager($this->modelid, $analysable->get_id(), $timesplitting->get_id(),
            $filearea, $this->options['evaluation']);

        // Flag the model + analysable + timesplitting as being analysed (prevent concurrent executions).
        if (!$dataset->init_process()) {
            // If this model + analysable + timesplitting combination is being analysed we skip this process.
            $result->status = \core_analytics\model::NO_DATASET;
            $result->message = get_string('analysisinprogress', 'analytics');
            return $result;
        }

        try {
            // Remove samples the target consider invalid.
            $this->analysabletarget->add_sample_data($samplesdata);
            $this->analysabletarget->filter_out_invalid_samples($sampleids, $analysable, $includetarget);
        } catch (\Throwable $e) {
            $dataset->close_process();
            throw $e;
        }

        if (!$sampleids) {
            $result->status = \core_analytics\model::NO_DATASET;
            $result->message = get_string('novalidsamples', 'analytics');
            $dataset->close_process();
            return $result;
        }

        try {
            foreach ($this->indicators as $key => $indicator) {
                // The analyser attaches the main entities the sample depends on and are provided to the
                // indicator to calculate the sample.
                $this->indicators[$key]->add_sample_data($samplesdata);
            }

            // Here we start the memory intensive process that will last until $data var is
            // unset (until the method is finished basically).
            if ($includetarget) {
                $data = $timesplitting->calculate($sampleids, $this->get_samples_origin(), $this->indicators, $ranges,
                    $this->analysabletarget);
            } else {
                $data = $timesplitting->calculate($sampleids, $this->get_samples_origin(), $this->indicators, $ranges);
            }
        } catch (\Throwable $e) {
            $dataset->close_process();
            throw $e;
        }

        if (!$data) {
            $result->status = \core_analytics\model::ANALYSABLE_REJECTED_TIME_SPLITTING_METHOD;
            $result->message = get_string('novaliddata', 'analytics');
            $dataset->close_process();
            return $result;
        }

        try {
            // Add extra metadata.
            $this->add_model_metadata($data);

            // Write all calculated data to a file.
            $file = $dataset->store($data);
        } catch (\Throwable $e) {
            $dataset->close_process();
            throw $e;
        }

        // Flag the model + analysable + timesplitting as analysed.
        $dataset->close_process();

        // No need to keep track of analysed stuff when evaluating.
        if ($this->options['evaluation'] === false) {
            // Save the samples that have been already analysed so they are not analysed again in future.

            if ($includetarget) {
                $this->save_train_samples($sampleids, $timesplitting, $file);
            } else {
                $this->save_prediction_samples($sampleids, $ranges, $timesplitting);
            }
        }

        $result->status = \core_analytics\model::OK;
        $result->message = get_string('successfullyanalysed', 'analytics');
        $result->file = $file;
        return $result;
    }

    /**
     * Returns the most recent range that can be used to predict.
     *
     * @param \core_analytics\local\time_splitting\base $timesplitting
     * @return array
     */
    protected function get_most_recent_prediction_range($timesplitting) {

        $now = time();
        $ranges = $timesplitting->get_all_ranges();

        // Opposite order as we are interested in the last range that can be used for prediction.
        krsort($ranges);

        // We already provided the analysable to the time splitting method, there is no need to feed it back.
        foreach ($ranges as $rangeindex => $range) {
            if ($timesplitting->ready_to_predict($range)) {
                // We need to maintain the same indexes.
                return array($rangeindex => $range);
            }
        }

        return array();
    }

    /**
     * Filters out samples that have already been used for training.
     *
     * @param int[] $sampleids
     * @param \core_analytics\local\time_splitting\base $timesplitting
     */
    protected function filter_out_train_samples(&$sampleids, $timesplitting) {
        global $DB;

        $params = array('modelid' => $this->modelid, 'analysableid' => $timesplitting->get_analysable()->get_id(),
            'timesplitting' => $timesplitting->get_id());

        $trainingsamples = $DB->get_records('analytics_train_samples', $params);

        // Skip each file trained samples.
        foreach ($trainingsamples as $trainingfile) {

            $usedsamples = json_decode($trainingfile->sampleids, true);

            if (!empty($usedsamples)) {
                // Reset $sampleids to $sampleids minus this file's $usedsamples.
                $sampleids = array_diff_key($sampleids, $usedsamples);
            }
        }
    }

    /**
     * Filters out samples that have already been used for prediction.
     *
     * @param int[] $sampleids
     * @param array $ranges
     * @param \core_analytics\local\time_splitting\base $timesplitting
     */
    protected function filter_out_prediction_samples_and_ranges(&$sampleids, &$ranges, $timesplitting) {
        global $DB;

        if (count($ranges) > 1) {
            throw new \coding_exception('$ranges argument should only contain one range');
        }

        $rangeindex = key($ranges);

        $params = array('modelid' => $this->modelid, 'analysableid' => $timesplitting->get_analysable()->get_id(),
            'timesplitting' => $timesplitting->get_id(), 'rangeindex' => $rangeindex);
        $predictedrange = $DB->get_record('analytics_predict_samples', $params);

        if (!$predictedrange) {
            // Nothing to filter out.
            return;
        }

        $predictedrange->sampleids = json_decode($predictedrange->sampleids, true);
        $missingsamples = array_diff_key($sampleids, $predictedrange->sampleids);
        if (count($missingsamples) === 0) {
            // All samples already calculated.
            unset($ranges[$rangeindex]);
            return;
        }

        // Replace the list of samples by the one excluding samples that already got predictions at this range.
        $sampleids = $missingsamples;
    }

    /**
     * Saves samples that have just been used for training.
     *
     * @param int[] $sampleids
     * @param \core_analytics\local\time_splitting\base $timesplitting
     * @param \stored_file $file
     * @return void
     */
    protected function save_train_samples($sampleids, $timesplitting, $file) {
        global $DB;

        $trainingsamples = new \stdClass();
        $trainingsamples->modelid = $this->modelid;
        $trainingsamples->analysableid = $timesplitting->get_analysable()->get_id();
        $trainingsamples->timesplitting = $timesplitting->get_id();
        $trainingsamples->fileid = $file->get_id();

        $trainingsamples->sampleids = json_encode($sampleids);
        $trainingsamples->timecreated = time();

        $DB->insert_record('analytics_train_samples', $trainingsamples);
    }

    /**
     * Saves samples that have just been used for prediction.
     *
     * @param int[] $sampleids
     * @param array $ranges
     * @param \core_analytics\local\time_splitting\base $timesplitting
     * @return void
     */
    protected function save_prediction_samples($sampleids, $ranges, $timesplitting) {
        global $DB;

        if (count($ranges) > 1) {
            throw new \coding_exception('$ranges argument should only contain one range');
        }

        $rangeindex = key($ranges);

        $params = array('modelid' => $this->modelid, 'analysableid' => $timesplitting->get_analysable()->get_id(),
            'timesplitting' => $timesplitting->get_id(), 'rangeindex' => $rangeindex);
        if ($predictionrange = $DB->get_record('analytics_predict_samples', $params)) {
            // Append the new samples used for prediction.
            $prevsamples = json_decode($predictionrange->sampleids, true);
            $predictionrange->sampleids = json_encode($prevsamples + $sampleids);
            $predictionrange->timemodified = time();
            $DB->update_record('analytics_predict_samples', $predictionrange);
        } else {
            $predictionrange = (object)$params;
            $predictionrange->sampleids = json_encode($sampleids);
            $predictionrange->timecreated = time();
            $predictionrange->timemodified = $predictionrange->timecreated;
            $DB->insert_record('analytics_predict_samples', $predictionrange);
        }
    }

    /**
     * Adds target metadata to the dataset.
     *
     * @param array $data
     * @return void
     */
    protected function add_model_metadata(&$data) {
        global $CFG;

        $metadata = array(
            'moodleversion' => $CFG->version,
            'targetcolumn' => $this->analysabletarget->get_id()
        );
        if ($this->analysabletarget->is_linear()) {
            $metadata['targettype'] = 'linear';
            $metadata['targetmin'] = $this->analysabletarget::get_min_value();
            $metadata['targetmax'] = $this->analysabletarget::get_max_value();
        } else {
            $metadata['targettype'] = 'discrete';
            $metadata['targetclasses'] = json_encode($this->analysabletarget::get_classes());
        }

        foreach ($metadata as $varname => $value) {
            $data[0][] = $varname;
            $data[1][] = $value;
        }
    }

    /**
     * Returns the list of analysables sorted in processing priority order.
     *
     * It will first return analysables that have never been analysed before
     * and it will continue with the ones we have already seen by timeanalysed DESC
     * order.
     *
     * @param bool $includetarget
     * @return array(0 => \core_analytics\analysable[], 1 => \stdClass[])
     */
    protected function get_sorted_analysables($includetarget) {

        $analysables = $this->get_analysables();

        // Get the list of analysables that have been already processed.
        $processedanalysables = $this->get_processed_analysables($includetarget);

        // We want to start processing analysables we have not yet processed and later continue
        // with analysables that we already processed.
        $unseen = array_diff_key($analysables, $processedanalysables);

        // Var $processed first as we want to respect its timeanalysed DESC order so analysables that
        // have recently been processed are on the bottom of the stack.
        $seen = array_intersect_key($processedanalysables, $analysables);
        array_walk($seen, function(&$value, $analysableid) use ($analysables) {
            // We replace the analytics_used_analysables record by the analysable object.
            $value = $analysables[$analysableid];
        });

        return array($unseen + $seen, $processedanalysables);
    }

    /**
     * Get analysables that have been already processed.
     *
     * @param bool $includetarget
     * @return \stdClass[]
     */
    protected function get_processed_analysables($includetarget) {
        global $DB;

        $params = array('modelid' => $this->modelid);
        $params['action'] = ($includetarget) ? 'training' : 'prediction';
        $select = 'modelid = :modelid and action = :action';

        // Weird select fields ordering for performance (analysableid key matching, analysableid is also unique by modelid).
        return $DB->get_records_select('analytics_used_analysables', $select,
            $params, 'timeanalysed DESC', 'analysableid, modelid, action, timeanalysed, id AS primarykey');
    }

    /**
     * Updates the analysable analysis time.
     *
     * @param array $processedanalysables
     * @param int $analysableid
     * @param bool $includetarget
     * @return null
     */
    protected function update_analysable_analysed_time($processedanalysables, $analysableid, $includetarget) {
        global $DB;

        if (!empty($processedanalysables[$analysableid])) {
            $obj = $processedanalysables[$analysableid];

            $obj->id = $obj->primarykey;
            unset($obj->primarykey);

            $obj->timeanalysed = time();
            $DB->update_record('analytics_used_analysables', $obj);

        } else {

            $obj = new \stdClass();
            $obj->modelid = $this->modelid;
            $obj->action = ($includetarget) ? 'training' : 'prediction';
            $obj->analysableid = $analysableid;
            $obj->timeanalysed = time();

            $DB->insert_record('analytics_used_analysables', $obj);
        }
    }
}
