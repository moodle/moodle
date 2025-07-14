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
 * Representation of a prediction.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Representation of a prediction.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class prediction {

    /**
     * Prediction details (one of the default prediction actions)
     */
    const ACTION_PREDICTION_DETAILS = 'predictiondetails';

    /**
     * Prediction useful (one of the default prediction actions)
     */
    const ACTION_USEFUL = 'useful';

    /**
     * Prediction not useful (one of the default prediction actions)
     */
    const ACTION_NOT_USEFUL = 'notuseful';

    /**
     * Prediction already fixed (one of the default prediction actions)
     */
    const ACTION_FIXED = 'fixed';

    /**
     * Prediction not applicable.
     */
    const ACTION_NOT_APPLICABLE = 'notapplicable';

    /**
     * Prediction incorrectly flagged.
     */
    const ACTION_INCORRECTLY_FLAGGED = 'incorrectlyflagged';

    /**
     * @var \stdClass
     */
    private $prediction;

    /**
     * @var array
     */
    private $sampledata;

    /**
     * @var array
     */
    private $calculations = array();

    /**
     * Constructor
     *
     * @param \stdClass|int $prediction
     * @param array $sampledata
     * @return void
     */
    public function __construct($prediction, $sampledata) {
        global $DB;

        if (is_scalar($prediction)) {
            $prediction = $DB->get_record('analytics_predictions', array('id' => $prediction), '*', MUST_EXIST);
        }
        $this->prediction = $prediction;

        $this->sampledata = $sampledata;

        $this->format_calculations();
    }

    /**
     * Get prediction object data.
     *
     * @return \stdClass
     */
    public function get_prediction_data() {
        return $this->prediction;
    }

    /**
     * Get prediction sample data.
     *
     * @return array
     */
    public function get_sample_data() {
        return $this->sampledata;
    }

    /**
     * Gets the prediction calculations
     *
     * @return array
     */
    public function get_calculations() {
        return $this->calculations;
    }

    /**
     * Stores the executed action.

     * Prediction instances should be retrieved using \core_analytics\manager::get_prediction,
     * It is the caller responsability to check that the user can see the prediction.
     *
     * @param string $actionname
     * @param \core_analytics\local\target\base $target
     */
    public function action_executed($actionname, \core_analytics\local\target\base $target) {
        global $USER, $DB;

        $context = \context::instance_by_id($this->get_prediction_data()->contextid, IGNORE_MISSING);
        if (!$context) {
            throw new \moodle_exception('errorpredictioncontextnotavailable', 'analytics');
        }

        // Check that the provided action exists.
        $actions = $target->prediction_actions($this, true);
        foreach ($actions as $action) {
            if ($action->get_action_name() === $actionname) {
                $found = true;
            }
        }
        $bulkactions = $target->bulk_actions([$this]);
        foreach ($bulkactions as $action) {
            if ($action->get_action_name() === $actionname) {
                $found = true;
            }
        }
        if (empty($found)) {
            throw new \moodle_exception('errorunknownaction', 'analytics');
        }

        $predictionid = $this->get_prediction_data()->id;

        $action = new \stdClass();
        $action->predictionid = $predictionid;
        $action->userid = $USER->id;
        $action->actionname = $actionname;
        $action->timecreated = time();
        $DB->insert_record('analytics_prediction_actions', $action);

        $eventdata = array (
            'context' => $context,
            'objectid' => $predictionid,
            'other' => array('actionname' => $actionname)
        );
        \core\event\prediction_action_started::create($eventdata)->trigger();
    }

    /**
     * Get the executed actions.
     *
     * Actions could be filtered by actionname.
     *
     * @param array $actionnamefilter Limit the results obtained to this list of action names.
     * @param int $userid the user id. Current user by default.
     * @return array of actions.
     */
    public function get_executed_actions(?array $actionnamefilter = null, int $userid = 0): array {
        global $USER, $DB;

        $conditions[] = "predictionid = :predictionid";
        $params['predictionid'] = $this->get_prediction_data()->id;
        if (!$userid) {
            $userid = $USER->id;
        }
        $conditions[] = "userid = :userid";
        $params['userid'] = $userid;
        if ($actionnamefilter) {
            list($actionsql, $actionparams) = $DB->get_in_or_equal($actionnamefilter, SQL_PARAMS_NAMED);
            $conditions[] = "actionname $actionsql";
            $params = $params + $actionparams;
        }
        return $DB->get_records_select('analytics_prediction_actions', implode(' AND ', $conditions), $params);
    }

    /**
     * format_calculations
     *
     * @return \stdClass[]
     */
    private function format_calculations() {

        $calculations = json_decode($this->prediction->calculations, true);

        foreach ($calculations as $featurename => $value) {

            list($indicatorclass, $subtype) = $this->parse_feature_name($featurename);

            if ($indicatorclass === 'range') {
                // Time range indicators don't belong to any indicator class, we don't store them.
                continue;
            } else if (!\core_analytics\manager::is_valid($indicatorclass, '\core_analytics\local\indicator\base')) {
                throw new \moodle_exception('errorpredictionformat', 'analytics');
            }

            $this->calculations[$featurename] = new \stdClass();
            $this->calculations[$featurename]->subtype = $subtype;
            $this->calculations[$featurename]->indicator = \core_analytics\manager::get_indicator($indicatorclass);
            $this->calculations[$featurename]->value = $value;
        }
    }

    /**
     * parse_feature_name
     *
     * @param string $featurename
     * @return string[]
     */
    private function parse_feature_name($featurename) {

        $indicatorclass = $featurename;
        $subtype = false;

        // Some indicator result in more than 1 feature, we need to see which feature are we dealing with.
        $separatorpos = strpos($featurename, '/');
        if ($separatorpos !== false) {
            $subtype = substr($featurename, ($separatorpos + 1));
            $indicatorclass = substr($featurename, 0, $separatorpos);
        }

        return array($indicatorclass, $subtype);
    }
}
