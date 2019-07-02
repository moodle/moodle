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
     * @todo MDL-65284 This will be removed in Moodle 4.1
     * @deprecated
     * @see get_analysables_iterator
     * @throws  \coding_exception
     * @return \core_analytics\analysable[] Array of analysable elements using the analysable id as array key.
     */
    public function get_analysables() {
        // This function should only be called from get_analysables_iterator and we keep it here until Moodle 4.1
        // for backwards compatibility.
        throw new \coding_exception('This method is deprecated in favour of get_analysables_iterator.');
    }

    /**
     * Returns the list of analysable elements available on the site.
     *
     * A relatively complex SQL query should be set so that we take into account which analysable elements
     * have already been processed and the order in which they have been processed. Helper methods are available
     * to ease to implementation of get_analysables_iterator: get_iterator_sql and order_sql.
     *
     * @param string|null $action 'prediction', 'training' or null if no specific action needed.
     * @return \Iterator
     */
    public function get_analysables_iterator(?string $action = null) {

        debugging('Please overwrite get_analysables_iterator with your own implementation, we only keep this default
            implementation for backwards compatibility purposes with get_analysables(). note that $action param will
            be ignored so the analysable elements will be processed using get_analysables order, regardless of the
            last time they were processed.');

        return new \ArrayIterator($this->get_analysables());
    }

    /**
     * This function returns this analysable list of samples.
     *
     * @param \core_analytics\analysable $analysable
     * @return array array[0] = int[] (sampleids) and array[1] = array (samplesdata)
     */
    abstract public function get_all_samples(\core_analytics\analysable $analysable);

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
     * Model id getter.
     * @return int
     */
    public function get_modelid(): int {
        return $this->modelid;
    }

    /**
     * Options getter.
     * @return array
     */
    public function get_options(): array {
        return $this->options;
    }

    /**
     * Returns the analysed target.
     *
     * @return \core_analytics\local\target\base
     */
    public function get_target(): \core_analytics\local\target\base {
        return $this->target;
    }

    /**
     * Getter for time splittings.
     *
     * @return \core_analytics\local\time_splitting\base
     */
    public function get_timesplittings(): array {
        return $this->timesplittings;
    }

    /**
     * Getter for indicators.
     *
     * @return \core_analytics\local\indicator\base
     */
    public function get_indicators(): array {
        return $this->indicators;
    }

    /**
     * Instantiate the indicators.
     *
     * @return \core_analytics\local\indicator\base[]
     */
    public function instantiate_indicators() {
        foreach ($this->indicators as $key => $indicator) {
            $this->indicators[$key] = call_user_func(array($indicator, 'instance'));
        }
        return $this->indicators;
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
     * @return \stored_file[]
     */
    public function get_labelled_data() {
        // Delegates all processing to the analysis.
        $result = new \core_analytics\local\analysis\result_file($this->get_modelid(), true, $this->get_options());
        $analysis = new \core_analytics\analysis($this, true, $result);
        $analysis->run();
        return $result->get();
    }

    /**
     * Returns unlabelled data (prediction).
     *
     * @return \stored_file[]
     */
    public function get_unlabelled_data() {
        // Delegates all processing to the analysis.
        $result = new \core_analytics\local\analysis\result_file($this->get_modelid(), false, $this->get_options());
        $analysis = new \core_analytics\analysis($this, false, $result);
        $analysis->run();
        return $result->get();
    }

    /**
     * Returns indicator calculations as an array.
     * @return array
     */
    public function get_static_data() {
        // Delegates all processing to the analysis.
        $result = new \core_analytics\local\analysis\result_array($this->get_modelid(), false, $this->get_options());
        $analysis = new \core_analytics\analysis($this, false, $result);
        $analysis->run();
        return $result->get();
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
     * Do this analyser's analysables have 1 single sample each?
     *
     * Overwrite and return true if your analysables only have
     * one sample. The insights generated by models using this
     * analyser will then include the suggested actions in the
     * notification.
     *
     * @return bool
     */
    public static function one_sample_per_analysable() {
        return false;
    }

    /**
     * Get the sql of a default implementation of the iterator.
     *
     * This method only works for analysers that return analysable elements which ids map to a context instance ids.
     *
     * @param  string      $tablename    The name of the table
     * @param  int         $contextlevel The context level of the analysable
     * @param  string|null $action
     * @param  string|null $tablealias   The table alias
     * @return array                     [0] => sql and [1] => params array
     */
    protected function get_iterator_sql(string $tablename, int $contextlevel, ?string $action = null, ?string $tablealias = null) {

        if (!$tablealias) {
            $tablealias = 'analysable';
        }

        $params = ['contextlevel' => $contextlevel, 'modelid' => $this->get_modelid()];
        $select = $tablealias . '.*, ' . \context_helper::get_preload_record_columns_sql('ctx');

        // We add the action filter on ON instead of on WHERE because otherwise records are not returned if there are existing
        // records for another action or model.
        $usedanalysablesjoin = ' LEFT JOIN {analytics_used_analysables} aua ON ' . $tablealias . '.id = aua.analysableid AND ' .
            '(aua.modelid = :modelid OR aua.modelid IS NULL)';

        if ($action) {
            $usedanalysablesjoin .= " AND aua.action = :action";
            $params = $params + ['action' => $action];
        }

        // Adding the 1 = 1 just to have the WHERE part so that all further conditions added by callers can be
        // appended to $sql with and ' AND'.
        $sql = 'SELECT ' . $select . '
                  FROM {' . $tablename . '} ' . $tablealias . '
                  ' . $usedanalysablesjoin . '
                  JOIN {context} ctx ON (ctx.contextlevel = :contextlevel AND ctx.instanceid = ' . $tablealias . '.id)
                  WHERE 1 = 1';

        return [$sql, $params];
    }

    /**
     * Returns the order by clause.
     *
     * @param  string|null $fieldname  The field name
     * @param  string      $order      'ASC' or 'DESC'
     * @param  string|null $tablealias The table alias of the field
     * @return string
     */
    protected function order_sql(?string $fieldname = null, string $order = 'ASC', ?string $tablealias = null) {

        if (!$tablealias) {
            $tablealias = 'analysable';
        }

        if ($order != 'ASC' && $order != 'DESC') {
            throw new \coding_exception('The order can only be ASC or DESC');
        }

        $ordersql = ' ORDER BY (CASE WHEN aua.timeanalysed IS NULL THEN 0 ELSE aua.timeanalysed END) ASC';
        if ($fieldname) {
            $ordersql .= ', ' . $tablealias . '.' . $fieldname .' ' . $order;
        }

        return $ordersql;
    }
}
