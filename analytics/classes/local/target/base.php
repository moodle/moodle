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
 * Abstract base target.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\target;

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract base target.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base extends \core_analytics\calculable {

    /**
     * This target have linear or discrete values.
     *
     * @return bool
     */
    abstract public function is_linear();

    /**
     * Returns the analyser class that should be used along with this target.
     *
     * @return string The full class name as a string
     */
    abstract public function get_analyser_class();

    /**
     * Allows the target to verify that the analysable is a good candidate.
     *
     * This method can be used as a quick way to discard invalid analysables.
     * e.g. Imagine that your analysable don't have students and you need them.
     *
     * @param \core_analytics\analysable $analysable
     * @param bool $fortraining
     * @return true|string
     */
    abstract public function is_valid_analysable(\core_analytics\analysable $analysable, $fortraining = true);

    /**
     * Is this sample from the $analysable valid?
     *
     * @param int $sampleid
     * @param \core_analytics\analysable $analysable
     * @param bool $fortraining
     * @return bool
     */
    abstract public function is_valid_sample($sampleid, \core_analytics\analysable $analysable, $fortraining = true);

    /**
     * Calculates this target for the provided samples.
     *
     * In case there are no values to return or the provided sample is not applicable just return null.
     *
     * @param int $sampleid
     * @param \core_analytics\analysable $analysable
     * @param int|false $starttime Limit calculations to start time
     * @param int|false $endtime Limit calculations to end time
     * @return float|null
     */
    abstract protected function calculate_sample($sampleid, \core_analytics\analysable $analysable, $starttime = false, $endtime = false);

    /**
     * Is this target generating insights?
     *
     * Defaults to true.
     *
     * @return bool
     */
    public static function uses_insights() {
        return true;
    }

    /**
     * Based on facts (processed by machine learning backends) by default.
     *
     * @return bool
     */
    public static function based_on_assumptions() {
        return false;
    }

    /**
     * Update the last analysis time on analysable processed or always.
     *
     * If you overwrite this method to return false the last analysis time
     * will only be recorded in DB when the element successfully analysed. You can
     * safely return false for lightweight targets.
     *
     * @return bool
     */
    public function always_update_analysis_time(): bool {
        return true;
    }

    /**
     * Suggested actions for a user.
     *
     * @param \core_analytics\prediction $prediction
     * @param bool $includedetailsaction
     * @param bool $isinsightuser
     * @return \core_analytics\prediction_action[]
     */
    public function prediction_actions(\core_analytics\prediction $prediction, $includedetailsaction = false,
            $isinsightuser = false) {
        global $PAGE;

        $predictionid = $prediction->get_prediction_data()->id;
        $contextid = $prediction->get_prediction_data()->contextid;
        $modelid = $prediction->get_prediction_data()->modelid;

        $PAGE->requires->js_call_amd('report_insights/actions', 'init', array($predictionid, $contextid, $modelid));

        $actions = array();

        if ($includedetailsaction) {

            $predictionurl = new \moodle_url('/report/insights/prediction.php', array('id' => $predictionid));
            $detailstext = $this->get_view_details_text();

            $actions[] = new \core_analytics\prediction_action(\core_analytics\prediction::ACTION_PREDICTION_DETAILS, $prediction,
                $predictionurl, new \pix_icon('t/preview', $detailstext),
                $detailstext);
        }

        // Flag as fixed / solved.
        $fixedattrs = array(
            'data-prediction-id' => $predictionid,
            'data-prediction-methodname' => 'report_insights_set_fixed_prediction'
        );
        $actions[] = new \core_analytics\prediction_action(\core_analytics\prediction::ACTION_FIXED,
            $prediction, new \moodle_url(''), new \pix_icon('t/check', get_string('fixedack', 'analytics')),
            get_string('fixedack', 'analytics'), false, $fixedattrs);

        // Flag as not useful.
        $notusefulattrs = array(
            'data-prediction-id' => $predictionid,
            'data-prediction-methodname' => 'report_insights_set_notuseful_prediction'
        );
        $actions[] = new \core_analytics\prediction_action(\core_analytics\prediction::ACTION_NOT_USEFUL,
            $prediction, new \moodle_url(''), new \pix_icon('t/delete', get_string('notuseful', 'analytics')),
            get_string('notuseful', 'analytics'), false, $notusefulattrs);

        return $actions;
    }

    /**
     * Returns the view details link text.
     * @return string
     */
    private function get_view_details_text() {
        if ($this->based_on_assumptions()) {
            $analyserclass = $this->get_analyser_class();
            if ($analyserclass::one_sample_per_analysable()) {
                $detailstext = get_string('viewinsightdetails', 'analytics');
            } else {
                $detailstext = get_string('viewdetails', 'analytics');
            }
        } else {
            $detailstext = get_string('viewprediction', 'analytics');
        }

        return $detailstext;
    }

    /**
     * Callback to execute once a prediction has been returned from the predictions processor.
     *
     * Note that the analytics_predictions db record is not yet inserted.
     *
     * @param int $modelid
     * @param int $sampleid
     * @param int $rangeindex
     * @param \context $samplecontext
     * @param float|int $prediction
     * @param float $predictionscore
     * @return void
     */
    public function prediction_callback($modelid, $sampleid, $rangeindex, \context $samplecontext, $prediction, $predictionscore) {
        return;
    }

    /**
     * Generates insights notifications
     *
     * @param int $modelid
     * @param \context[] $samplecontexts
     * @param  \core_analytics\prediction[] $predictions
     * @return void
     */
    public function generate_insight_notifications($modelid, $samplecontexts, array $predictions = []) {
        // Delegate the processing of insights to the insights_generator.
        $insightsgenerator = new \core_analytics\insights_generator($modelid, $this);
        $insightsgenerator->generate($samplecontexts, $predictions);
    }

    /**
     * Returns the list of users that will receive insights notifications.
     *
     * Feel free to overwrite if you need to but keep in mind that moodle/analytics:listinsights
     * or moodle/analytics:listowninsights capability is required to access the list of insights.
     *
     * @param \context $context
     * @return array
     */
    public function get_insights_users(\context $context) {
        if ($context->contextlevel === CONTEXT_USER) {
            $users = [$context->instanceid => \core_user::get_user($context->instanceid)];
        } else if ($context->contextlevel >= CONTEXT_COURSE) {
            // At course level or below only enrolled users although this is not ideal for
            // teachers assigned at category level.
            $users = get_enrolled_users($context, 'moodle/analytics:listinsights');
        } else {
            $users = get_users_by_capability($context, 'moodle/analytics:listinsights');
        }
        return $users;
    }

    /**
     * URL to the insight.
     *
     * @param  int $modelid
     * @param  \context $context
     * @return \moodle_url
     */
    public function get_insight_context_url($modelid, $context) {
        return new \moodle_url('/report/insights/insights.php?modelid=' . $modelid . '&contextid=' . $context->id);
    }

    /**
     * The insight notification subject.
     *
     * This is just a default message, you should overwrite it for a custom insight message.
     *
     * @param  int $modelid
     * @param  \context $context
     * @return string
     */
    public function get_insight_subject(int $modelid, \context $context) {
        return get_string('insightmessagesubject', 'analytics', $context->get_context_name());
    }

    /**
     * Returns an instance of the child class.
     *
     * Useful to reset cached data.
     *
     * @return \core_analytics\base\target
     */
    public static function instance() {
        return new static();
    }

    /**
     * Defines a boundary to ignore predictions below the specified prediction score.
     *
     * Value should go from 0 to 1.
     *
     * @return float
     */
    protected function min_prediction_score() {
        // The default minimum discards predictions with a low score.
        return \core_analytics\model::PREDICTION_MIN_SCORE;
    }

    /**
     * This method determines if a prediction is interesing for the model or not.
     *
     * @param mixed $predictedvalue
     * @param float $predictionscore
     * @return bool
     */
    public function triggers_callback($predictedvalue, $predictionscore) {

        $minscore = floatval($this->min_prediction_score());
        if ($minscore < 0) {
            debugging(get_class($this) . ' minimum prediction score is below 0, please update it to a value between 0 and 1.');
        } else if ($minscore > 1) {
            debugging(get_class($this) . ' minimum prediction score is above 1, please update it to a value between 0 and 1.');
        }

        // We need to consider that targets may not have a min score.
        if (!empty($minscore) && floatval($predictionscore) < $minscore) {
            return false;
        }

        return true;
    }

    /**
     * Calculates the target.
     *
     * Returns an array of values which size matches $sampleids size.
     *
     * Rows with null values will be skipped as invalid by time splitting methods.
     *
     * @param array $sampleids
     * @param \core_analytics\analysable $analysable
     * @param int $starttime
     * @param int $endtime
     * @return array The format to follow is [userid] = scalar|null
     */
    public function calculate($sampleids, \core_analytics\analysable $analysable, $starttime = false, $endtime = false) {

        if (!PHPUNIT_TEST && CLI_SCRIPT) {
            echo '.';
        }

        $calculations = [];
        foreach ($sampleids as $sampleid => $unusedsampleid) {

            // No time limits when calculating the target to train models.
            $calculatedvalue = $this->calculate_sample($sampleid, $analysable, $starttime, $endtime);

            if (!is_null($calculatedvalue)) {
                if ($this->is_linear() &&
                        ($calculatedvalue > static::get_max_value() || $calculatedvalue < static::get_min_value())) {
                    throw new \coding_exception('Calculated values should be higher than ' . static::get_min_value() .
                        ' and lower than ' . static::get_max_value() . '. ' . $calculatedvalue . ' received');
                } else if (!$this->is_linear() && static::is_a_class($calculatedvalue) === false) {
                    throw new \coding_exception('Calculated values should be one of the target classes (' .
                        json_encode(static::get_classes()) . '). ' . $calculatedvalue . ' received');
                }
            }
            $calculations[$sampleid] = $calculatedvalue;
        }
        return $calculations;
    }

    /**
     * Filters out invalid samples for training.
     *
     * @param int[] $sampleids
     * @param \core_analytics\analysable $analysable
     * @param bool $fortraining
     * @return void
     */
    public function filter_out_invalid_samples(&$sampleids, \core_analytics\analysable $analysable, $fortraining = true) {
        foreach ($sampleids as $sampleid => $unusedsampleid) {
            if (!$this->is_valid_sample($sampleid, $analysable, $fortraining)) {
                // Skip it and remove the sample from the list of calculated samples.
                unset($sampleids[$sampleid]);
            }
        }
    }
}
