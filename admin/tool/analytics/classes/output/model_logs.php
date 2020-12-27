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
 * Model logs table class.
 *
 * @package    tool_analytics
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_analytics\output;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/tablelib.php');

/**
 * Model logs table class.
 *
 * @package    tool_analytics
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class model_logs extends \table_sql {

    /**
     * @var \core_analytics\model
     */
    protected $model = null;

    /**
     * @var string|false
     */
    protected $evaluationmode = false;

    /**
     * Sets up the table_log parameters.
     *
     * @param string $uniqueid unique id of form.
     * @param \core_analytics\model $model
     */
    public function __construct($uniqueid, $model) {
        global $PAGE;

        parent::__construct($uniqueid);

        $this->model = $model;

        $this->set_attribute('class', 'modellog generaltable generalbox');
        $this->set_attribute('aria-live', 'polite');

        $this->define_columns(array('time', 'version', 'evaluationmode', 'indicators', 'timesplitting',
            'accuracy', 'info', 'usermodified'));
        $this->define_headers(array(
            get_string('time'),
            get_string('version'),
            get_string('evaluationmode', 'tool_analytics'),
            get_string('indicators', 'tool_analytics'),
            get_string('timesplittingmethod', 'analytics'),
            get_string('accuracy', 'tool_analytics'),
            get_string('info', 'tool_analytics'),
            get_string('fullnameuser'),
        ));

        $evaluationmodehelp = new \help_icon('evaluationmode', 'tool_analytics');
        $this->define_help_for_headers([null, null, $evaluationmodehelp, null, null, null, null, null]);

        $this->pageable(true);
        $this->collapsible(false);
        $this->sortable(false);
        $this->is_downloadable(false);

        $this->evaluationmode = optional_param('evaluationmode', false, PARAM_ALPHANUM);
        if ($this->evaluationmode && $this->evaluationmode != 'configuration' && $this->evaluationmode != 'trainedmodel') {
            $this->evaluationmode = '';
        }

        $this->define_baseurl($PAGE->url);
    }

    /**
     * Generate the version column.
     *
     * @param \stdClass $log log data.
     * @return string HTML for the version column
     */
    public function col_version($log) {
        $recenttimestr = get_string('strftimerecent', 'core_langconfig');
        return userdate($log->version, $recenttimestr);
    }

    /**
     * Generate the evaluation mode column.
     *
     * @param \stdClass $log log data.
     * @return string HTML for the evaluationmode column
     */
    public function col_evaluationmode($log) {
        return get_string('evaluationmodecol' . $log->evaluationmode, 'tool_analytics');
    }
    /**
     * Generate the time column.
     *
     * @param \stdClass $log log data.
     * @return string HTML for the time column
     */
    public function col_time($log) {
        $recenttimestr = get_string('strftimerecent', 'core_langconfig');
        return userdate($log->timecreated, $recenttimestr);
    }

    /**
     * Generate the indicators column.
     *
     * @param \stdClass $log log data.
     * @return string HTML for the indicators column
     */
    public function col_indicators($log) {
        $indicatorclasses = json_decode($log->indicators);
        $indicators = array();
        foreach ($indicatorclasses as $indicatorclass) {
            $indicator = \core_analytics\manager::get_indicator($indicatorclass);
            if ($indicator) {
                $indicators[] = $indicator->get_name();
            } else {
                debugging('Can\'t load ' . $indicatorclass . ' indicator', DEBUG_DEVELOPER);
            }
        }
        return '<ul><li>' . implode('</li><li>', $indicators) . '</li></ul>';
    }

    /**
     * Generate the context column.
     *
     * @param \stdClass $log log data.
     * @return string HTML for the context column
     */
    public function col_timesplitting($log) {
        $timesplitting = \core_analytics\manager::get_time_splitting($log->timesplitting);
        return $timesplitting->get_name();
    }

    /**
     * Generate the accuracy column.
     *
     * @param \stdClass $log log data.
     * @return string HTML for the accuracy column
     */
    public function col_accuracy($log) {
        return strval(round($log->score * 100, 2)) . '%';
    }

    /**
     * Generate the info column.
     *
     * @param \stdClass $log log data.
     * @return string HTML for the score column
     */
    public function col_info($log) {
        global $PAGE;

        if (empty($log->info) && empty($log->dir)) {
            return '';
        }

        $info = array();
        if (!empty($log->info)) {
            $info = json_decode($log->info);
        }
        if (!empty($log->dir)) {
            $info[] = get_string('predictorresultsin', 'tool_analytics', $log->dir);
        }
        $PAGE->requires->js_call_amd('tool_analytics/log_info', 'loadInfo', array($log->id, $info));
        return \html_writer::link('#', get_string('view'), array('data-model-log-id' => $log->id));
    }

    /**
     * Generate the usermodified column.
     *
     * @param \stdClass $log log data.
     * @return string HTML for the usermodified column
     */
    public function col_usermodified($log) {
        $user = \core_user::get_user($log->usermodified);
        return fullname($user);
    }

    /**
     * Query the logs table. Store results in the object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        $total = count($this->model->get_logs());
        $this->pagesize($pagesize, $total);
        $this->rawdata = $this->model->get_logs($this->get_page_start(), $this->get_page_size());

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }
}
