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
 * This file contains the cohort membership badge award criteria type class
 *
 * @package    core
 * @subpackage badges
 * @copyright  2016 onwards Catalyst IT {@link https://www.catalyst.net.nz/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Eugene Venter <eugene@catalyst.net.nz>
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/cohort/lib.php');

/**
 * Badge award criteria -- award on cohort membership
 *
 */
class award_criteria_cohort extends award_criteria {

    /* @var int Criteria [BADGE_CRITERIA_TYPE_COHORT] */
    public $criteriatype = BADGE_CRITERIA_TYPE_COHORT;

    public $required_param = 'cohort';
    public $optional_params = array();

    /**
     * Get criteria details for displaying to users
     *
     * @return string
     */
    public function get_details($short = '') {
        global $DB, $OUTPUT;
        $output = array();
        foreach ($this->params as $p) {
            $cohortname = $DB->get_field('cohort', 'name', array('id' => $p['cohort']));
            if (!$cohortname) {
                $str = $OUTPUT->error_text(get_string('error:nosuchcohort', 'badges'));
            } else {
                $str = html_writer::tag('b', '"' . $cohortname . '"');
            }
            $output[] = $str;
        }

        if ($short) {
            return implode(', ', $output);
        } else {
            return html_writer::alist($output, array(), 'ul');
        }
    }


    /**
     * Add appropriate new criteria options to the form
     *
     */
    public function get_options(&$mform) {
        global $DB;
        $none = false;

        $mform->addElement('header', 'first_header', $this->get_title());
        $mform->addHelpButton('first_header', 'criteria_' . $this->criteriatype, 'badges');

        // Get cohorts
        $cohorts = $DB->get_records_menu('cohort', array(), 'name ASC', 'id, name');
        if (!empty($cohorts)) {
            $select = array();
            $selected = array();
            foreach ($cohorts as $cid => $cohortname) {
                $select[$cid] = format_string($cohortname, true);
            }

            if ($this->id !== 0) {
                $selected = array_keys($this->params);
            }
            $settings = array('multiple' => 'multiple', 'size' => 20, 'class' => 'selectcohort');
            $mform->addElement('select', 'cohort_cohorts', get_string('addcohort', 'badges'), $select, $settings);
            $mform->addRule('cohort_cohorts', get_string('requiredcohort', 'badges'), 'required');
            $mform->addHelpButton('cohort_cohorts', 'addcohort', 'badges');

            if ($this->id !== 0) {
                $mform->setDefault('cohort_cohorts', $selected);
            }
        } else {
            $mform->addElement('static', 'nocohorts', '', get_string('error:nocohorts', 'badges'));
            $none = true;
        }

        // Add aggregation.
        if (!$none) {
            $mform->addElement('header', 'aggregation', get_string('method', 'badges'));
            $agg = array();
            $agg[] =& $mform->createElement('radio', 'agg', '', get_string('allmethodcohort', 'badges'), 1);
            $agg[] =& $mform->createElement('radio', 'agg', '', get_string('anymethodcohort', 'badges'), 2);
            $mform->addGroup($agg, 'methodgr', '', array('<br/>'), false);
            if ($this->id !== 0) {
                $mform->setDefault('agg', $this->method);
            } else {
                $mform->setDefault('agg', BADGE_CRITERIA_AGGREGATION_ANY);
            }
        }

        return array($none, get_string('noparamstoadd', 'badges'));
    }

    /**
     * Save criteria records
     *
     * @param $params criteria params
     */
    public function save($params = array()) {
        $cohorts = $params['cohort_cohorts'];
        unset($params['cohort_cohorts']);
        foreach ($cohorts as $cohortid) {
            $params["cohort_{$cohortid}"] = $cohortid;
        }

        parent::save($params);
    }

    /**
     * Review this criteria and decide if it has been completed
     *
     * @return bool Whether criteria is complete
     */
    public function review($userid, $filtered = false) {
        global $DB;
        $overall = false;

        foreach ($this->params as $param) {
            $cohort = $DB->get_record('cohort', array('id' => $param['cohort']));

            // Extra check in case a cohort was deleted while badge is still active.
            if (!$cohort) {
                if ($this->method == BADGE_CRITERIA_AGGREGATION_ALL) {
                    return false;
                } else {
                    continue;
                }
            }

            if ($this->method == BADGE_CRITERIA_AGGREGATION_ALL) {
                if (cohort_is_member($cohort->id, $userid)) {
                    $overall = true;
                    continue;
                } else {
                    return false;
                }
            } else if ($this->method == BADGE_CRITERIA_AGGREGATION_ANY) {
                if (cohort_is_member($cohort->id, $userid)) {
                    return true;
                } else {
                    $overall = false;
                    continue;
                }
            }
        }

        return $overall;
    }

    /**
     * Checks criteria for any major problems.
     *
     * @return array A list containing status and an error message (if any).
     */
    public function validate() {
        global $DB;
        $params = array_keys($this->params);
        $method = ($this->method == BADGE_CRITERIA_AGGREGATION_ALL);
        $singleparam = (count($params) == 1);

        foreach ($params as $param) {
            // Perform check if there only one parameter with any type of aggregation,
            // Or there are more than one parameter with aggregation ALL.
            if (($singleparam || $method) && !$DB->record_exists('cohort', array('id' => $param))) {
                return array(false, get_string('error:invalidparamcohort', 'badges'));
            }
        }

        return array(true, '');
    }

    public function get_completed_criteria_sql() {
// TODO;

        return array($join, $where, $params);
    }
}
