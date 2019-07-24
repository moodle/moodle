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
 * Runs an analysis of the site.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Runs an analysis of the site.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class analysis {

    /**
     * @var \core_analytics\local\analyser\base
     */
    private $analyser;

    /**
     * @var bool Whether to calculate the target or not in this run.
     */
    private $includetarget;

    /**
     * @var \core_analytics\local\analysis\result
     */
    private $result;

    /**
     * @var \core\lock\lock
     */
    private $lock;

    /**
     * Constructor.
     *
     * @param \core_analytics\local\analyser\base   $analyser
     * @param bool                                  $includetarget Whether to calculate the target or not.
     * @param \core_analytics\local\analysis\result $result
     */
    public function __construct(\core_analytics\local\analyser\base $analyser, bool $includetarget,
            \core_analytics\local\analysis\result $result) {
        $this->analyser = $analyser;
        $this->includetarget = $includetarget;
        $this->result = $result;

        // We cache the first time analysables were analysed because time-splitting methods can depend on these info.
        self::fill_firstanalyses_cache($this->analyser->get_modelid());
    }

    /**
     * Runs the analysis.
     *
     * @return null
     */
    public function run() {

        $options = $this->analyser->get_options();

        // Time limit control.
        $modeltimelimit = intval(get_config('analytics', 'modeltimelimit'));

        if ($this->includetarget) {
            $action = 'training';
        } else {
            $action = 'prediction';
        }
        $analysables = $this->analyser->get_analysables_iterator($action);

        $processedanalysables = $this->get_processed_analysables();

        $inittime = microtime(true);
        foreach ($analysables as $analysable) {
            $processed = false;

            if (!$analysable) {
                continue;
            }

            $analysableresults = $this->process_analysable($analysable);
            if ($analysableresults) {
                $processed = $this->result->add_analysable_results($analysableresults);
                if (!$processed) {
                    $errors = array();
                    foreach ($analysableresults as $timesplittingid => $result) {
                        $str = '';
                        if (count($analysableresults) > 1) {
                            $str .= $timesplittingid . ': ';
                        }
                        $str .= $result->message;
                        $errors[] = $str;
                    }

                    $a = new \stdClass();
                    $a->analysableid = $analysable->get_name();
                    $a->errors = implode(', ', $errors);
                    $this->analyser->add_log(get_string('analysablenotused', 'analytics', $a));
                }
            }

            if (!$options['evaluation']) {

                if (empty($processedanalysables[$analysable->get_id()]) ||
                        $this->analyser->get_target()->always_update_analysis_time() || $processed) {
                    // We store the list of processed analysables even if the target does not always_update_analysis_time(),
                    // what always_update_analysis_time controls is the update of the data.
                    $this->update_analysable_analysed_time($processedanalysables, $analysable->get_id());
                }

                // Apply time limit.
                $timespent = microtime(true) - $inittime;
                if ($modeltimelimit <= $timespent) {
                    break;
                }
            }
        }

        // Force GC to clean up the indicator instances used during the last iteration.
        $this->analyser->instantiate_indicators();
    }

    /**
     * Get analysables that have been already processed.
     *
     * @return \stdClass[]
     */
    protected function get_processed_analysables(): array {
        global $DB;

        $params = array('modelid' => $this->analyser->get_modelid());
        $params['action'] = ($this->includetarget) ? 'training' : 'prediction';
        $select = 'modelid = :modelid and action = :action';

        // Weird select fields ordering for performance (analysableid key matching, analysableid is also unique by modelid).
        return $DB->get_records_select('analytics_used_analysables', $select,
            $params, 'timeanalysed DESC', 'analysableid, modelid, action, firstanalysis, timeanalysed, id AS primarykey');
    }

    /**
     * Processes an analysable
     *
     * This method returns the general analysable status, an array of files by time splitting method and
     * an error message if there is any problem.
     *
     * @param \core_analytics\analysable $analysable
     * @return \stdClass[] Results objects by time splitting method
     */
    public function process_analysable(\core_analytics\analysable $analysable): array {

        // Target instances scope is per-analysable (it can't be lower as calculations run once per
        // analysable, not time splitting method nor time range).
        $target = call_user_func(array($this->analyser->get_target(), 'instance'));

        // We need to check that the analysable is valid for the target even if we don't include targets
        // as we still need to discard invalid analysables for the target.
        $isvalidresult = $target->is_valid_analysable($analysable, $this->includetarget);
        if ($isvalidresult !== true) {
            $a = new \stdClass();
            $a->analysableid = $analysable->get_name();
            $a->result = $isvalidresult;
            $this->analyser->add_log(get_string('analysablenotvalidfortarget', 'analytics', $a));
            return array();
        }

        // Process all provided time splitting methods.
        $results = array();
        foreach ($this->analyser->get_timesplittings() as $timesplitting) {

            $cachedresult = $this->result->retrieve_cached_result($timesplitting, $analysable);
            if ($cachedresult) {
                $result = new \stdClass();
                $result->result = $cachedresult;
                $results[$timesplitting->get_id()] = $result;
                continue;
            }

            $results[$timesplitting->get_id()] = $this->process_time_splitting($timesplitting, $analysable, $target);
        }

        return $results;
    }

    /**
     * Processes the analysable samples using the provided time splitting method.
     *
     * @param \core_analytics\local\time_splitting\base $timesplitting
     * @param \core_analytics\analysable $analysable
     * @param \core_analytics\local\target\base $target
     * @return \stdClass Results object.
     */
    protected function process_time_splitting(\core_analytics\local\time_splitting\base $timesplitting,
            \core_analytics\analysable $analysable, \core_analytics\local\target\base $target): \stdClass {

        $options = $this->analyser->get_options();

        $result = new \stdClass();

        $timesplitting->set_modelid($this->analyser->get_modelid());
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
        list($sampleids, $samplesdata) = $this->analyser->get_all_samples($analysable);

        if (count($sampleids) === 0) {
            $result->status = \core_analytics\model::ANALYSABLE_REJECTED_TIME_SPLITTING_METHOD;
            $result->message = get_string('nodata', 'analytics');
            return $result;
        }

        if ($this->includetarget) {
            // All ranges are used when we are calculating data for training.
            $ranges = $timesplitting->get_training_ranges();
        } else {
            // The latest range that has not yet been used for prediction (it depends on the time range where we are right now).
            $ranges = $timesplitting->get_most_recent_prediction_range();
        }

        // There is no need to keep track of the evaluated samples and ranges as we always evaluate the whole dataset.
        if ($options['evaluation'] === false) {

            if (empty($ranges)) {
                $result->status = \core_analytics\model::ANALYSABLE_REJECTED_TIME_SPLITTING_METHOD;
                $result->message = get_string('noranges', 'analytics');
                return $result;
            }

            // We skip all samples that are already part of a training dataset, even if they have not been used for prediction.
            if (!$target::based_on_assumptions()) {
                // Targets based on assumptions can not be trained.
                $this->filter_out_train_samples($sampleids, $timesplitting);
            }

            if (count($sampleids) === 0) {
                $result->status = \core_analytics\model::ANALYSABLE_REJECTED_TIME_SPLITTING_METHOD;
                $result->message = get_string('nonewdata', 'analytics');
                return $result;
            }

            // Only when processing data for predictions.
            if (!$this->includetarget) {
                // We also filter out samples and ranges that have already been used for predictions.
                $predictsamplesrecord = $this->filter_out_prediction_samples_and_ranges($sampleids, $ranges, $timesplitting);
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

        // Flag the model + analysable + timesplitting as being analysed (prevent concurrent executions).
        if (!$this->init_analysable_analysis($timesplitting->get_id(), $analysable->get_id())) {
            // If this model + analysable + timesplitting combination is being analysed we skip this process.
            $result->status = \core_analytics\model::NO_DATASET;
            $result->message = get_string('analysisinprogress', 'analytics');
            return $result;
        }

        // Remove samples the target consider invalid.
        try {
            $target->add_sample_data($samplesdata);
            $target->filter_out_invalid_samples($sampleids, $analysable, $this->includetarget);
        } catch (\Throwable $e) {
            $this->finish_analysable_analysis();
            throw $e;
        }

        if (!$sampleids) {
            $result->status = \core_analytics\model::NO_DATASET;
            $result->message = get_string('novalidsamples', 'analytics');
            $this->finish_analysable_analysis();
            return $result;
        }

        try {
            // Instantiate empty indicators to ensure that no garbage is dragged from previous analyses.
            $indicators = $this->analyser->instantiate_indicators();
            foreach ($indicators as $key => $indicator) {
                // The analyser attaches the main entities the sample depends on and are provided to the
                // indicator to calculate the sample.
                $indicators[$key]->add_sample_data($samplesdata);
            }

            // Here we start the memory intensive process that will last until $data var is
            // unset (until the method is finished basically).
            $data = $this->calculate($timesplitting, $sampleids, $ranges, $target);
        } catch (\Throwable $e) {
            $this->finish_analysable_analysis();
            throw $e;
        }

        if (!$data) {
            $result->status = \core_analytics\model::ANALYSABLE_REJECTED_TIME_SPLITTING_METHOD;
            $result->message = get_string('novaliddata', 'analytics');
            $this->finish_analysable_analysis();
            return $result;
        }

        try {
            // No need to keep track of analysed stuff when evaluating.
            if ($options['evaluation'] === false) {
                // Save the samples that have been already analysed so they are not analysed again in future.

                if ($this->includetarget) {
                    $this->save_train_samples($sampleids, $timesplitting);
                } else {
                    // The variable $predictsamplesrecord will always be set as filter_out_prediction_samples_and_ranges
                    // will always be called before it (no evaluation mode and no includetarget).
                    $this->save_prediction_samples($sampleids, $ranges, $timesplitting, $predictsamplesrecord);
                }
            }

            // We need to pass all the analysis data.
            $formattedresult = $this->result->format_result($data, $target, $timesplitting, $analysable);

        } catch (\Throwable $e) {
            $this->finish_analysable_analysis();
            throw $e;
        }

        if (!$formattedresult) {
            $this->finish_analysable_analysis();
            throw new \moodle_exception('errorcannotwritedataset', 'analytics');
        }

        $result->status = \core_analytics\model::OK;
        $result->message = get_string('successfullyanalysed', 'analytics');
        $result->result = $formattedresult;

        // Flag the model + analysable + timesplitting as analysed.
        $this->finish_analysable_analysis();

        return $result;
    }

    /**
     * Calculates indicators and targets.
     *
     * @param \core_analytics\local\time_splitting\base $timesplitting
     * @param array $sampleids
     * @param array $ranges
     * @param \core_analytics\local\target\base $target
     * @return array|null
     */
    public function calculate(\core_analytics\local\time_splitting\base $timesplitting, array &$sampleids,
            array $ranges, \core_analytics\local\target\base $target): ?array {

        $calculatedtarget = null;
        if ($this->includetarget) {
            // We first calculate the target because analysable data may still be invalid or none
            // of the analysable samples may be valid.
            $calculatedtarget = $target->calculate($sampleids, $timesplitting->get_analysable());

            // We remove samples we can not calculate their target.
            $sampleids = array_filter($sampleids, function($sampleid) use ($calculatedtarget) {
                if (is_null($calculatedtarget[$sampleid])) {
                    return false;
                }
                return true;
            });
        }

        // No need to continue calculating if the target couldn't be calculated for any sample.
        if (empty($sampleids)) {
            return null;
        }

        $dataset = $this->calculate_indicators($timesplitting, $sampleids, $ranges);

        if (empty($dataset)) {
            return null;
        }

        // Now that we have the indicators in place we can add the time range indicators (and target if provided) to each of them.
        $this->fill_dataset($timesplitting, $dataset, $calculatedtarget);

        $this->add_context_metadata($timesplitting, $dataset, $target);

        if (!PHPUNIT_TEST && CLI_SCRIPT) {
            echo PHP_EOL;
        }

        return $dataset;
    }

    /**
     * Calculates indicators.
     *
     * @param \core_analytics\local\time_splitting\base $timesplitting
     * @param array $sampleids
     * @param array $ranges
     * @return array
     */
    protected function calculate_indicators(\core_analytics\local\time_splitting\base $timesplitting, array $sampleids,
            array $ranges): array {
        global $DB;

        $options = $this->analyser->get_options();

        $dataset = array();

        // Faster to run 1 db query per range.
        $existingcalculations = array();
        if ($timesplitting->cache_indicator_calculations()) {
            foreach ($ranges as $rangeindex => $range) {
                // Load existing calculations.
                $existingcalculations[$rangeindex] = \core_analytics\manager::get_indicator_calculations(
                    $timesplitting->get_analysable(), $range['start'], $range['end'], $this->analyser->get_samples_origin());
            }
        }

        // Here we store samples which calculations are not all null.
        $notnulls = array();

        // Fill the dataset samples with indicators data.
        $newcalculations = array();
        foreach ($this->analyser->get_indicators() as $indicator) {

            // Hook to allow indicators to store analysable-dependant data.
            $indicator->fill_per_analysable_caches($timesplitting->get_analysable());

            // Per-range calculations.
            foreach ($ranges as $rangeindex => $range) {

                // Indicator instances are per-range.
                $rangeindicator = clone $indicator;

                $prevcalculations = array();
                if (!empty($existingcalculations[$rangeindex][$rangeindicator->get_id()])) {
                    $prevcalculations = $existingcalculations[$rangeindex][$rangeindicator->get_id()];
                }

                // Calculate the indicator for each sample in this time range.
                list($samplesfeatures, $newindicatorcalculations, $indicatornotnulls) = $rangeindicator->calculate($sampleids,
                    $this->analyser->get_samples_origin(), $range['start'], $range['end'], $prevcalculations);

                // Free memory ASAP.
                unset($rangeindicator);
                gc_collect_cycles();
                gc_mem_caches();

                // Copy the features data to the dataset.
                foreach ($samplesfeatures as $analysersampleid => $features) {

                    $uniquesampleid = $timesplitting->append_rangeindex($analysersampleid, $rangeindex);

                    if (!isset($notnulls[$uniquesampleid]) && !empty($indicatornotnulls[$analysersampleid])) {
                        $notnulls[$uniquesampleid] = $uniquesampleid;
                    }

                    // Init the sample if it is still empty.
                    if (!isset($dataset[$uniquesampleid])) {
                        $dataset[$uniquesampleid] = array();
                    }

                    // Append the features indicator features at the end of the sample.
                    $dataset[$uniquesampleid] = array_merge($dataset[$uniquesampleid], $features);
                }

                if (!$options['evaluation'] && $timesplitting->cache_indicator_calculations()) {
                    $timecreated = time();
                    foreach ($newindicatorcalculations as $sampleid => $calculatedvalue) {
                        // Prepare the new calculations to be stored into DB.

                        $indcalc = new \stdClass();
                        $indcalc->contextid = $timesplitting->get_analysable()->get_context()->id;
                        $indcalc->starttime = $range['start'];
                        $indcalc->endtime = $range['end'];
                        $indcalc->sampleid = $sampleid;
                        $indcalc->sampleorigin = $this->analyser->get_samples_origin();
                        $indcalc->indicator = $indicator->get_id();
                        $indcalc->value = $calculatedvalue;
                        $indcalc->timecreated = $timecreated;
                        $newcalculations[] = $indcalc;
                    }
                }
            }

            if (!$options['evaluation'] && $timesplitting->cache_indicator_calculations()) {
                $batchsize = self::get_insert_batch_size();
                if (count($newcalculations) > $batchsize) {
                    // We don't want newcalculations array to grow too much as we already keep the
                    // system memory busy storing $dataset contents.

                    // Insert from the beginning.
                    $remaining = array_splice($newcalculations, $batchsize);

                    // Sorry mssql and oracle, this will be slow.
                    $DB->insert_records('analytics_indicator_calc', $newcalculations);
                    $newcalculations = $remaining;
                }
            }
        }

        if (!$options['evaluation'] && $timesplitting->cache_indicator_calculations() && $newcalculations) {
            // Insert the remaining records.
            $DB->insert_records('analytics_indicator_calc', $newcalculations);
        }

        // Delete rows where all calculations are null.
        // We still store the indicator calculation and we still store the sample id as
        // processed so we don't have to process this sample again, but we exclude it
        // from the dataset because it is not useful.
        $nulls = array_diff_key($dataset, $notnulls);
        foreach ($nulls as $uniqueid => $ignoredvalues) {
            unset($dataset[$uniqueid]);
        }

        return $dataset;
    }

    /**
     * Adds time range indicators and the target to each sample.
     *
     * This will identify the sample as belonging to a specific range.
     *
     * @param \core_analytics\local\time_splitting\base $timesplitting
     * @param array $dataset
     * @param array|null $calculatedtarget
     * @return null
     */
    protected function fill_dataset(\core_analytics\local\time_splitting\base $timesplitting,
            array &$dataset, ?array $calculatedtarget = null) {

        $nranges = count($timesplitting->get_distinct_ranges());

        foreach ($dataset as $uniquesampleid => $unmodified) {

            list($analysersampleid, $rangeindex) = $timesplitting->infer_sample_info($uniquesampleid);

            // No need to add range features if this time splitting method only defines one time range.
            if ($nranges > 1) {

                // 1 column for each range.
                $timeindicators = array_fill(0, $nranges, 0);

                $timeindicators[$rangeindex] = 1;

                $dataset[$uniquesampleid] = array_merge($timeindicators, $dataset[$uniquesampleid]);
            }

            if ($calculatedtarget) {
                // Add this sampleid's calculated target and the end.
                $dataset[$uniquesampleid][] = $calculatedtarget[$analysersampleid];

            } else {
                // Add this sampleid, it will be used to identify the prediction that comes back from
                // the predictions processor.
                array_unshift($dataset[$uniquesampleid], $uniquesampleid);
            }
        }
    }

    /**
     * Updates the analysable analysis time.
     *
     * @param array $processedanalysables
     * @param int $analysableid
     * @return null
     */
    protected function update_analysable_analysed_time(array $processedanalysables, int $analysableid) {
        global $DB;

        $now = time();

        if (!empty($processedanalysables[$analysableid])) {
            $obj = $processedanalysables[$analysableid];

            $obj->id = $obj->primarykey;
            unset($obj->primarykey);

            $obj->timeanalysed = $now;

            $DB->update_record('analytics_used_analysables', $obj);

        } else {

            $obj = new \stdClass();
            $obj->modelid = $this->analyser->get_modelid();
            $obj->action = ($this->includetarget) ? 'training' : 'prediction';
            $obj->analysableid = $analysableid;
            $obj->firstanalysis = $now;
            $obj->timeanalysed = $now;

            $obj->primarykey = $DB->insert_record('analytics_used_analysables', $obj);

            // Update the cache just in case it is used in the same request.
            $key = $this->analyser->get_modelid() . '_' . $analysableid;
            $cache = \cache::make('core', 'modelfirstanalyses');
            $cache->set($key, $now);
        }
    }

    /**
     * Fills a cache containing the first time each analysable in the provided model was analysed.
     *
     * @param int $modelid
     * @param int|null $analysableid
     * @return null
     */
    public static function fill_firstanalyses_cache(int $modelid, ?int $analysableid = null) {
        global $DB;

        // Using composed keys instead of cache $identifiers because of MDL-65358.
        $primarykey = $DB->sql_concat($modelid, "'_'", 'analysableid');
        $sql = "SELECT $primarykey AS id, MIN(firstanalysis) AS firstanalysis
                  FROM {analytics_used_analysables} aua
                 WHERE modelid = :modelid";
        $params = ['modelid' => $modelid];

        if ($analysableid) {
            $sql .= " AND analysableid = :analysableid";
            $params['analysableid'] = $analysableid;
        }

        $sql .= " GROUP BY modelid, analysableid ORDER BY analysableid";

        $firstanalyses = $DB->get_records_sql($sql, $params);
        if ($firstanalyses) {
            $cache = \cache::make('core', 'modelfirstanalyses');

            $firstanalyses = array_map(function($record) {
                return $record->firstanalysis;
            }, $firstanalyses);

            $cache->set_many($firstanalyses);
        }

        return $firstanalyses;
    }

    /**
     * Adds dataset context info.
     *
     * The final dataset document will look like this:
     * ----------------------------------------------------
     * metadata1,metadata2,metadata3,.....
     * value1, value2, value3,.....
     *
     * header1,header2,header3,header4,.....
     * stud1value1,stud1value2,stud1value3,stud1value4,.....
     * stud2value1,stud2value2,stud2value3,stud2value4,.....
     * .....
     * ----------------------------------------------------
     *
     * @param \core_analytics\local\time_splitting\base $timesplitting
     * @param array $dataset
     * @param \core_analytics\local\target\base $target
     * @return null
     */
    protected function add_context_metadata(\core_analytics\local\time_splitting\base $timesplitting, array &$dataset,
            \core_analytics\local\target\base $target) {
        $headers = $this->get_headers($timesplitting, $target);

        // This will also reset samples' dataset keys.
        array_unshift($dataset, $headers);
    }

    /**
     * Returns the headers for the csv file based on the indicators and the target.
     *
     * @param \core_analytics\local\time_splitting\base $timesplitting
     * @param \core_analytics\local\target\base $target
     * @return string[]
     */
    public function get_headers(\core_analytics\local\time_splitting\base $timesplitting,
            \core_analytics\local\target\base $target): array {
        // 3rd column will contain the indicator ids.
        $headers = array();

        if (!$this->includetarget) {
            // The first column is the sampleid.
            $headers[] = 'sampleid';
        }

        // We always have 1 column for each time splitting method range, it does not depend on how
        // many ranges we calculated.
        $ranges = $timesplitting->get_distinct_ranges();
        if (count($ranges) > 1) {
            foreach ($ranges as $rangeindex) {
                $headers[] = 'range/' . $rangeindex;
            }
        }

        // Model indicators.
        foreach ($this->analyser->get_indicators() as $indicator) {
            $headers = array_merge($headers, $indicator::get_feature_headers());
        }

        // The target as well.
        if ($this->includetarget) {
            $headers[] = $target->get_id();
        }

        return $headers;
    }

    /**
     * Filters out samples that have already been used for training.
     *
     * @param int[] $sampleids
     * @param \core_analytics\local\time_splitting\base $timesplitting
     * @return  null
     */
    protected function filter_out_train_samples(array &$sampleids, \core_analytics\local\time_splitting\base $timesplitting) {
        global $DB;

        $params = array('modelid' => $this->analyser->get_modelid(), 'analysableid' => $timesplitting->get_analysable()->get_id(),
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
     * @return  \stdClass|null The analytics_predict_samples record or null
     */
    protected function filter_out_prediction_samples_and_ranges(array &$sampleids, array &$ranges,
            \core_analytics\local\time_splitting\base $timesplitting) {

        if (count($ranges) > 1) {
            throw new \coding_exception('$ranges argument should only contain one range');
        }

        $rangeindex = key($ranges);
        $predictedrange = $this->get_predict_samples_record($timesplitting, $rangeindex);

        if (!$predictedrange) {
            // Nothing to filter out.
            return null;
        }

        $predictedrange->sampleids = json_decode($predictedrange->sampleids, true);
        $missingsamples = array_diff_key($sampleids, $predictedrange->sampleids);
        if (count($missingsamples) === 0) {
            // All samples already calculated.
            unset($ranges[$rangeindex]);
            return null;
        }

        // Replace the list of samples by the one excluding samples that already got predictions at this range.
        $sampleids = $missingsamples;

        return $predictedrange;
    }

    /**
     * Returns a predict samples record.
     *
     * @param  \core_analytics\local\time_splitting\base $timesplitting
     * @param  int                                       $rangeindex
     * @return \stdClass|false
     */
    private function get_predict_samples_record(\core_analytics\local\time_splitting\base $timesplitting, int $rangeindex) {
        global $DB;

        $params = array('modelid' => $this->analyser->get_modelid(), 'analysableid' => $timesplitting->get_analysable()->get_id(),
            'timesplitting' => $timesplitting->get_id(), 'rangeindex' => $rangeindex);
        $predictedrange = $DB->get_record('analytics_predict_samples', $params);

        return $predictedrange;
    }

    /**
     * Saves samples that have just been used for training.
     *
     * @param int[] $sampleids
     * @param \core_analytics\local\time_splitting\base $timesplitting
     * @return null
     */
    protected function save_train_samples(array $sampleids, \core_analytics\local\time_splitting\base $timesplitting) {
        global $DB;

        $trainingsamples = new \stdClass();
        $trainingsamples->modelid = $this->analyser->get_modelid();
        $trainingsamples->analysableid = $timesplitting->get_analysable()->get_id();
        $trainingsamples->timesplitting = $timesplitting->get_id();

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
     * @param \stdClass|null $predictsamplesrecord The existing record or null if there is no record yet.
     * @return null
     */
    protected function save_prediction_samples(array $sampleids, array $ranges,
            \core_analytics\local\time_splitting\base $timesplitting, ?\stdClass $predictsamplesrecord = null) {
        global $DB;

        if (count($ranges) > 1) {
            throw new \coding_exception('$ranges argument should only contain one range');
        }

        $rangeindex = key($ranges);

        if ($predictsamplesrecord) {
            // Append the new samples used for prediction.
            $predictsamplesrecord->sampleids = json_encode($predictsamplesrecord->sampleids + $sampleids);
            $predictsamplesrecord->timemodified = time();
            $DB->update_record('analytics_predict_samples', $predictsamplesrecord);
        } else {
            $predictsamplesrecord = (object)[
                'modelid' => $this->analyser->get_modelid(),
                'analysableid' => $timesplitting->get_analysable()->get_id(),
                'timesplitting' => $timesplitting->get_id(), 'rangeindex' => $rangeindex
            ];
            $predictsamplesrecord->sampleids = json_encode($sampleids);
            $predictsamplesrecord->timecreated = time();
            $predictsamplesrecord->timemodified = $predictsamplesrecord->timecreated;
            $DB->insert_record('analytics_predict_samples', $predictsamplesrecord);
        }
    }

    /**
     * Flags the analysable element as in-analysis and stores a lock for it.
     *
     * @param  string $timesplittingid
     * @param  int    $analysableid
     * @return bool Success or not
     */
    private function init_analysable_analysis(string $timesplittingid, int $analysableid) {

        // Do not include $this->includetarget as we don't want the same analysable to be analysed for training
        // and prediction at the same time.
        $lockkey = 'modelid:' . $this->analyser->get_modelid() . '-analysableid:' . $analysableid .
            '-timesplitting:' . self::clean_time_splitting_id($timesplittingid);

        // Large timeout as processes may be quite long.
        $lockfactory = \core\lock\lock_config::get_lock_factory('core_analytics');

        // If it is not ready in 10 secs skip this model + analysable + timesplittingmethod combination
        // it will attempt it again during next cron run.
        if (!$this->lock = $lockfactory->get_lock($lockkey, 10)) {
            return false;
        }
        return true;
    }


    /**
     * Remove all possibly problematic chars from the time splitting method id (id = its full class name).
     *
     * @param string $timesplittingid
     * @return string
     */
    public static function clean_time_splitting_id($timesplittingid) {
        $timesplittingid = str_replace('\\', '-', $timesplittingid);
        return clean_param($timesplittingid, PARAM_ALPHANUMEXT);
    }

    /**
     * Mark the currently analysed analysable+timesplitting as analysed.
     *
     * @return null
     */
    private function finish_analysable_analysis() {
        $this->lock->release();
    }

    /**
     * Returns the batch size used for insert_records.
     *
     * This method tries to find the best batch size without getting
     * into dml internals. Maximum 1000 records to save memory.
     *
     * @return int
     */
    private static function get_insert_batch_size(): int {
        global $DB;

        $dbconfig = $DB->export_dbconfig();

        // 500 is pgsql default so using 1000 is fine, no other db driver uses a hardcoded value.
        if (empty($dbconfig) || empty($dbconfig->dboptions) || empty($dbconfig->dboptions['bulkinsertsize'])) {
            return 1000;
        }

        $bulkinsert = $dbconfig->dboptions['bulkinsertsize'];
        if ($bulkinsert < 1000) {
            return $bulkinsert;
        }

        while ($bulkinsert > 1000) {
            $bulkinsert = round($bulkinsert / 2, 0);
        }

        return (int)$bulkinsert;
    }
}
