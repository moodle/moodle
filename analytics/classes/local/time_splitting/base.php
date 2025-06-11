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
     * The model id.
     *
     * @var int
     */
    protected $modelid;

    /**
     * @var \core_analytics\analysable
     */
    protected $analysable;

    /**
     * @var array
     */
    protected $ranges = [];

    /**
     * Define the time splitting methods ranges.
     *
     * 'time' value defines when predictions are executed, their values will be compared with
     * the current time in ready_to_predict. The ranges should be sorted by 'time' in
     * ascending order.
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
    abstract public static function get_name(): \lang_string;

    /**
     * Returns the time splitting method id.
     *
     * @return string
     */
    public function get_id() {
        return '\\' . get_class($this);
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
     * Assigns the model id to this time-splitting method it case it needs it.
     *
     * @param int $modelid
     */
    public function set_modelid(int $modelid) {
        $this->modelid = $modelid;
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
     * Should we use this time range for training?
     *
     * @param array $range
     * @return bool
     */
    public function ready_to_train($range) {
        $now = time();
        if ($range['time'] <= $now && $range['end'] <= $now) {
            return true;
        }
        return false;
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
     * By default all ranges are for training.
     *
     * @return array
     */
    public function get_training_ranges() {
        return $this->ranges;
    }

    /**
     * Returns the distinct range indexes in this time splitting method.
     *
     * @return int[]
     */
    public function get_distinct_ranges() {
        if ($this->include_range_info_in_training_data()) {
            return array_keys($this->ranges);
        } else {
            return [0];
        }
    }

    /**
     * Returns the most recent range that can be used to predict.
     *
     * This method is only called when calculating predictions.
     *
     * @return array
     */
    public function get_most_recent_prediction_range() {

        $ranges = $this->get_all_ranges();

        // Opposite order as we are interested in the last range that can be used for prediction.
        krsort($ranges);

        // We already provided the analysable to the time splitting method, there is no need to feed it back.
        foreach ($ranges as $rangeindex => $range) {
            if ($this->ready_to_predict($range)) {
                // We need to maintain the same indexes.
                return array($rangeindex => $range);
            }
        }

        return array();
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
    final public function append_rangeindex($sampleid, $rangeindex) {
        return $sampleid . '-' . $rangeindex;
    }

    /**
     * Returns the sample id and the range index from a uniquesampleid.
     *
     * @param string $uniquesampleid
     * @return array array($sampleid, $rangeindex)
     */
    final public function infer_sample_info($uniquesampleid) {
        return explode('-', $uniquesampleid);
    }

    /**
     * Whether to include the range index in the training data or not.
     *
     * By default, we consider that the different time ranges included in a time splitting method may not be
     * compatible between them (i.e. the indicators calculated at the end of the course can easily
     * differ from indicators calculated at the beginning of the course). So we include the range index as
     * one of the variables that the machine learning backend uses to generate predictions.
     *
     * If the indicators calculated using the different time ranges available in this time splitting method
     * are comparable you can overwrite this method to return false.
     *
     * Note that:
     *  - This is only relevant for models whose predictions are not based on assumptions
     *    (i.e. the ones using a machine learning backend to generate predictions).
     *  - The ranges can only be included in the training data when
     *    we know the final number of ranges the time splitting method will have. E.g.
     *    We can not know the final number of ranges of a 'daily' time splitting method
     *    as we will have one new range every day.
     * @return bool
     */
    public function include_range_info_in_training_data() {
        return true;
    }

    /**
     * Whether to cache or not the indicator calculations.
     *
     * Indicator calculations are stored to be reused across models. The calculations
     * are indexed by the calculation start and end time, and these times depend on the
     * time-splitting method. You should overwrite this method and return false if the time
     * frames generated by your time-splitting method are unique and / or can hardly be
     * reused by further models.
     *
     * @return bool
     */
    public function cache_indicator_calculations(): bool {
        return true;
    }

    /**
     * Is this method valid to evaluate prediction models?
     *
     * @return bool
     */
    public function valid_for_evaluation(): bool {
        return true;
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
}
