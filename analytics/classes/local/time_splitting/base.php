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
 * Base time splitting method.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\time_splitting;

defined('MOODLE_INTERNAL') || die();

/**
 * Base time splitting method.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {

    /**
     * @var string
     */
    protected $id;

    /**
     * @var \core_analytics\analysable
     */
    protected $analysable;


    /**
     * @var int[]
     */
    protected $sampleids;

    /**
     * @var string
     */
    protected $samplesorigin;

    /**
     * @var array
     */
    protected $ranges = [];

    /**
     * @var \core_analytics\local\indicator\base
     */
    protected static $indicators = [];

    /**
     * @var bool
     */
    protected $evaluation = false;

    /**
     * Define the time splitting methods ranges.
     *
     * 'time' value defines when predictions are executed, their values will be compared with
     * the current time in ready_to_predict
     *
     * @return array('start' => time(), 'end' => time(), 'time' => time())
     */
    abstract protected function define_ranges();

    /**
     * Returns a lang_string object representing the name for the time splitting method.
     *
     * Used as column identificator.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return \lang_string
     */
    public static abstract function get_name() : \lang_string;

    /**
     * Returns the time splitting method id.
     *
     * @return string
     */
    public function get_id() {
        return '\\' . get_class($this);
    }

    /**
     * Returns current evaluation value.
     *
     * @return bool
     */
    public function is_evaluating() {
        return $this->evaluation;
    }

    /**
     * Sets the evaluation flag.
     *
     * @param bool $evaluation
     */
    public function set_evaluating($evaluation) {
        $this->evaluation = (bool)$evaluation;
    }

    /**
     * Assigns the analysable and updates the time ranges according to the analysable start and end dates.
     *
     * @param \core_analytics\analysable $analysable
     * @return void
     */
    public function set_analysable(\core_analytics\analysable $analysable) {
        $this->analysable = $analysable;
        $this->ranges = $this->define_ranges();
        $this->validate_ranges();
    }

    /**
     * get_analysable
     *
     * @return \core_analytics\analysable
     */
    public function get_analysable() {
        return $this->analysable;
    }

    /**
     * Returns whether the course can be processed by this time splitting method or not.
     *
     * @param \core_analytics\analysable $analysable
     * @return bool
     */
    public function is_valid_analysable(\core_analytics\analysable $analysable) {
        return true;
    }

    /**
     * Should we predict this time range now?
     *
     * @param array $range
     * @return bool
     */
    public function ready_to_predict($range) {
        if ($range['time'] <= time()) {
            return true;
        }
        return false;
    }

    /**
     * Calculates indicators and targets.
     *
     * @param array $sampleids
     * @param string $samplesorigin
     * @param \core_analytics\local\indicator\base[] $indicators
     * @param array $ranges
     * @param \core_analytics\local\target\base $target
     * @return array|bool
     */
    public function calculate(&$sampleids, $samplesorigin, $indicators, $ranges, $target = false) {

        $calculatedtarget = false;
        if ($target) {
            // We first calculate the target because analysable data may still be invalid or none
            // of the analysable samples may be valid ($sampleids is also passed by reference).
            $calculatedtarget = $target->calculate($sampleids, $this->analysable);

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
            return false;
        }

        $dataset = $this->calculate_indicators($sampleids, $samplesorigin, $indicators, $ranges);

        if (empty($dataset)) {
            return false;
        }

        // Now that we have the indicators in place we can add the time range indicators (and target if provided) to each of them.
        $this->fill_dataset($dataset, $calculatedtarget);

        $this->add_metadata($dataset, $indicators, $target);

        if (!PHPUNIT_TEST && CLI_SCRIPT) {
            echo PHP_EOL;
        }

        return $dataset;
    }

    /**
     * Calculates indicators.
     *
     * @param array $sampleids
     * @param string $samplesorigin
     * @param \core_analytics\local\indicator\base[] $indicators
     * @param array $ranges
     * @return array
     */
    protected function calculate_indicators($sampleids, $samplesorigin, $indicators, $ranges) {
        global $DB;

        $dataset = array();

        // Faster to run 1 db query per range.
        $existingcalculations = array();
        foreach ($ranges as $rangeindex => $range) {
            // Load existing calculations.
            $existingcalculations[$rangeindex] = \core_analytics\manager::get_indicator_calculations($this->analysable,
                $range['start'], $range['end'], $samplesorigin);
        }

        // Here we store samples which calculations are not all null.
        $notnulls = array();

        // Fill the dataset samples with indicators data.
        $newcalculations = array();
        foreach ($indicators as $indicator) {

            // Hook to allow indicators to store analysable-dependant data.
            $indicator->fill_per_analysable_caches($this->analysable);

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
                    $samplesorigin, $range['start'], $range['end'], $prevcalculations);

                // Copy the features data to the dataset.
                foreach ($samplesfeatures as $analysersampleid => $features) {

                    $uniquesampleid = $this->append_rangeindex($analysersampleid, $rangeindex);

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

                if (!$this->is_evaluating()) {
                    $timecreated = time();
                    foreach ($newindicatorcalculations as $sampleid => $calculatedvalue) {
                        // Prepare the new calculations to be stored into DB.

                        $indcalc = new \stdClass();
                        $indcalc->contextid = $this->analysable->get_context()->id;
                        $indcalc->starttime = $range['start'];
                        $indcalc->endtime = $range['end'];
                        $indcalc->sampleid = $sampleid;
                        $indcalc->sampleorigin = $samplesorigin;
                        $indcalc->indicator = $rangeindicator->get_id();
                        $indcalc->value = $calculatedvalue;
                        $indcalc->timecreated = $timecreated;
                        $newcalculations[] = $indcalc;
                    }
                }
            }

            if (!$this->is_evaluating()) {
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

        if (!$this->is_evaluating() && $newcalculations) {
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
     * @param array $dataset
     * @param array $calculatedtarget
     * @return void
     */
    protected function fill_dataset(&$dataset, $calculatedtarget = false) {

        $nranges = count($this->get_all_ranges());

        foreach ($dataset as $uniquesampleid => $unmodified) {

            list($analysersampleid, $rangeindex) = $this->infer_sample_info($uniquesampleid);

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
     * Adds dataset context info.
     *
     * The final dataset document will look like this:
     * ----------------------------------------------------
     * metadata1,metadata2,metadata3,.....
     * value1, value2, value3,.....
     *
     * indicator1,indicator2,indicator3,indicator4,.....
     * stud1value1,stud1value2,stud1value3,stud1value4,.....
     * stud2value1,stud2value2,stud2value3,stud2value4,.....
     * .....
     * ----------------------------------------------------
     *
     * @param array $dataset
     * @param \core_analytics\local\indicator\base[] $indicators
     * @param \core_analytics\local\target\base|false $target
     * @return void
     */
    protected function add_metadata(&$dataset, $indicators, $target = false) {

        $metadata = array(
            'timesplitting' => $this->get_id(),
            // If no target the first column is the sampleid, if target the last column is the target.
            // This will need to be updated when we support unsupervised learning models.
            'nfeatures' => count(current($dataset)) - 1
        );

        // The first 2 samples will be used to store metadata about the dataset.
        $metadatacolumns = [];
        $metadatavalues = [];
        foreach ($metadata as $key => $value) {
            $metadatacolumns[] = $key;
            $metadatavalues[] = $value;
        }

        $headers = $this->get_headers($indicators, $target);

        // This will also reset samples' dataset keys.
        array_unshift($dataset, $metadatacolumns, $metadatavalues, $headers);
    }

    /**
     * Returns the ranges used by this time splitting method.
     *
     * @return array
     */
    public function get_all_ranges() {
        return $this->ranges;
    }

    /**
     * Returns range data by its index.
     *
     * @param int $rangeindex
     * @return array|false Range data or false if the index is not part of the existing ranges.
     */
    public function get_range_by_index($rangeindex) {
        if (!isset($this->ranges[$rangeindex])) {
            return false;
        }
        return $this->ranges[$rangeindex];
    }

    /**
     * Generates a unique sample id (sample in a range index).
     *
     * @param int $sampleid
     * @param int $rangeindex
     * @return string
     */
    public function append_rangeindex($sampleid, $rangeindex) {
        return $sampleid . '-' . $rangeindex;
    }

    /**
     * Returns the sample id and the range index from a uniquesampleid.
     *
     * @param string $uniquesampleid
     * @return array array($sampleid, $rangeindex)
     */
    public function infer_sample_info($uniquesampleid) {
        return explode('-', $uniquesampleid);
    }

    /**
     * Returns the headers for the csv file based on the indicators and the target.
     *
     * @param \core_analytics\local\indicator\base[] $indicators
     * @param \core_analytics\local\target\base|false $target
     * @return string[]
     */
    protected function get_headers($indicators, $target = false) {
        // 3rd column will contain the indicator ids.
        $headers = array();

        if (!$target) {
            // The first column is the sampleid.
            $headers[] = 'sampleid';
        }

        // We always have 1 column for each time splitting method range, it does not depend on how
        // many ranges we calculated.
        $ranges = $this->get_all_ranges();
        if (count($ranges) > 1) {
            foreach ($ranges as $rangeindex => $range) {
                $headers[] = 'range/' . $rangeindex;
            }
        }

        // Model indicators.
        foreach ($indicators as $indicator) {
            $headers = array_merge($headers, $indicator::get_feature_headers());
        }

        // The target as well.
        if ($target) {
            $headers[] = $target->get_id();
        }

        return $headers;
    }

    /**
     * Validates the time splitting method ranges.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_ranges() {
        foreach ($this->ranges as $key => $range) {
            if (!isset($this->ranges[$key]['start']) || !isset($this->ranges[$key]['end']) ||
                    !isset($this->ranges[$key]['time'])) {
                throw new \coding_exception($this->get_id() . ' time splitting method "' . $key .
                    '" range is not fully defined. We need a start timestamp and an end timestamp.');
            }
        }
    }

    /**
     * Returns the batch size used for insert_records.
     *
     * This method tries to find the best batch size without getting
     * into dml internals. Maximum 1000 records to save memory.
     *
     * @return int
     */
    private static function get_insert_batch_size() {
        global $DB;

        // 500 is pgsql default so using 1000 is fine, no other db driver uses a hardcoded value.
        if (empty($DB->dboptions['bulkinsertsize'])) {
            return 1000;
        }

        $bulkinsert = $DB->dboptions['bulkinsertsize'];
        if ($bulkinsert < 1000) {
            return $bulkinsert;
        }

        while ($bulkinsert > 1000) {
            $bulkinsert = round($bulkinsert / 2, 0);
        }

        return (int)$bulkinsert;
    }
}
