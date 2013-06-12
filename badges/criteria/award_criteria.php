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
 * Badge award criteria
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

/*
 * Role completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('BADGE_CRITERIA_TYPE_OVERALL', 0);

/*
 * Activity completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('BADGE_CRITERIA_TYPE_ACTIVITY', 1);

/*
 * Duration completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('BADGE_CRITERIA_TYPE_MANUAL', 2);

/*
 * Grade completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('BADGE_CRITERIA_TYPE_SOCIAL', 3);

/*
 * Course completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
*/
define('BADGE_CRITERIA_TYPE_COURSE', 4);

/*
 * Courseset completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('BADGE_CRITERIA_TYPE_COURSESET', 5);

/*
 * Course completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('BADGE_CRITERIA_TYPE_PROFILE', 6);

/*
 * Criteria type constant to class name mapping
 */
global $BADGE_CRITERIA_TYPES;
$BADGE_CRITERIA_TYPES = array(
    BADGE_CRITERIA_TYPE_OVERALL   => 'overall',
    BADGE_CRITERIA_TYPE_ACTIVITY  => 'activity',
    BADGE_CRITERIA_TYPE_MANUAL    => 'manual',
    BADGE_CRITERIA_TYPE_SOCIAL    => 'social',
    BADGE_CRITERIA_TYPE_COURSE    => 'course',
    BADGE_CRITERIA_TYPE_COURSESET => 'courseset',
    BADGE_CRITERIA_TYPE_PROFILE   => 'profile'
);

/**
 * Award criteria abstract definition
 *
 */
abstract class award_criteria {

    public $id;
    public $method;
    public $badgeid;
    public $params = array();

    /**
     * The base constructor
     *
     * @param array $params
     */
    public function __construct($params) {
        $this->id = isset($params['id']) ? $params['id'] : 0;
        $this->method = isset($params['method']) ? $params['method'] : BADGE_CRITERIA_AGGREGATION_ANY;
        $this->badgeid = $params['badgeid'];
        if (isset($params['id'])) {
            $this->params = $this->get_params($params['id']);
        }
    }

    /**
     * Factory method for creating criteria class object
     *
     * @param array $params associative arrays varname => value
     * @return award_criteria
     */
    public static function build($params) {
        global $CFG, $BADGE_CRITERIA_TYPES;

        if (!isset($params['criteriatype']) || !isset($BADGE_CRITERIA_TYPES[$params['criteriatype']])) {
            print_error('error:invalidcriteriatype', 'badges');
        }

        $class = 'award_criteria_' . $BADGE_CRITERIA_TYPES[$params['criteriatype']];
        require_once($CFG->dirroot . '/badges/criteria/' . $class . '.php');

        return new $class($params);
    }

    /**
     * Return criteria title
     *
     * @return string
     */
    public function get_title() {
        return get_string('criteria_' . $this->criteriatype, 'badges');
    }

    /**
     * Get criteria details for displaying to users
     *
     * @param string $short Print short version of criteria
     * @return string
     */
    abstract public function get_details($short = '');

    /**
     * Add appropriate criteria options to the form
     *
     */
    abstract public function get_options(&$mform);

    /**
     * Add appropriate parameter elements to the criteria form
     *
     */
    public function config_options(&$mform, $param) {
        global $OUTPUT;
        $prefix = $this->required_param . '_';

        if ($param['error']) {
            $parameter[] =& $mform->createElement('advcheckbox', $prefix . $param['id'], '',
                    $OUTPUT->error_text($param['name']), null, array(0, $param['id']));
            $mform->addGroup($parameter, 'param_' . $prefix . $param['id'], '', array(' '), false);
        } else {
            $parameter[] =& $mform->createElement('advcheckbox', $prefix . $param['id'], '', $param['name'], null, array(0, $param['id']));
            $parameter[] =& $mform->createElement('static', 'break_start_' . $param['id'], null, '<div style="margin-left: 3em;">');

            if (in_array('grade', $this->optional_params)) {
                $parameter[] =& $mform->createElement('static', 'mgrade_' . $param['id'], null, get_string('mingrade', 'badges'));
                $parameter[] =& $mform->createElement('text', 'grade_' . $param['id'], '', array('size' => '5'));
                $mform->setType('grade_' . $param['id'], PARAM_INT);
            }

            if (in_array('bydate', $this->optional_params)) {
                $parameter[] =& $mform->createElement('static', 'complby_' . $param['id'], null, get_string('bydate', 'badges'));
                $parameter[] =& $mform->createElement('date_selector', 'bydate_' . $param['id'], "", array('optional' => true));
            }

            $parameter[] =& $mform->createElement('static', 'break_end_' . $param['id'], null, '</div>');
            $mform->addGroup($parameter, 'param_' . $prefix . $param['id'], '', array(' '), false);
            if (in_array('grade', $this->optional_params)) {
                $mform->addGroupRule('param_' . $prefix . $param['id'], array(
                    'grade_' . $param['id'] => array(array(get_string('err_numeric', 'form'), 'numeric', '', 'client'))));
            }
            $mform->disabledIf('bydate_' . $param['id'] . '[day]', 'bydate_' . $param['id'] . '[enabled]', 'notchecked');
            $mform->disabledIf('bydate_' . $param['id'] . '[month]', 'bydate_' . $param['id'] . '[enabled]', 'notchecked');
            $mform->disabledIf('bydate_' . $param['id'] . '[year]', 'bydate_' . $param['id'] . '[enabled]', 'notchecked');
            $mform->disabledIf('param_' . $prefix . $param['id'], $prefix . $param['id'], 'notchecked');
        }

        // Set default values.
        $mform->setDefault($prefix . $param['id'], $param['checked']);
        if (isset($param['bydate'])) {
            $mform->setDefault('bydate_' . $param['id'], $param['bydate']);
        }
        if (isset($param['grade'])) {
            $mform->setDefault('grade_' . $param['id'], $param['grade']);
        }
    }

    /**
     * Add appropriate criteria elements
     *
     * @param stdClass $data details of various criteria
     */
    public function config_form_criteria($data) {
        global $OUTPUT;
        $agg = $data->get_aggregation_methods();

        $editurl = new moodle_url('/badges/criteria_settings.php',
                array('badgeid' => $this->badgeid, 'edit' => true, 'type' => $this->criteriatype, 'crit' => $this->id));
        $deleteurl = new moodle_url('/badges/criteria_action.php',
                array('badgeid' => $this->badgeid, 'delete' => true, 'type' => $this->criteriatype));
        $editaction = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')), null, array('class' => 'criteria-action'));
        $deleteaction = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete')), null, array('class' => 'criteria-action'));

        echo $OUTPUT->box_start();
        if (!$data->is_locked() && !$data->is_active()) {
            echo $OUTPUT->box($deleteaction . $editaction, array('criteria-header'));
        }
        echo $OUTPUT->heading($this->get_title() . $OUTPUT->help_icon('criteria_' . $this->criteriatype, 'badges'), 3, 'main help');

        if (!empty($this->params)) {
            if (count($this->params) > 1) {
                echo $OUTPUT->box(get_string('criteria_descr_' . $this->criteriatype, 'badges',
                        strtoupper($agg[$data->get_aggregation_method($this->criteriatype)])), array('clearfix'));
            } else {
                echo $OUTPUT->box(get_string('criteria_descr_single_' . $this->criteriatype , 'badges'), array('clearfix'));
            }
            echo $OUTPUT->box($this->get_details(), array('clearfix'));
        }
        echo $OUTPUT->box_end();
    }

    /**
     * Review this criteria and decide if the user has completed
     *
     * @param int $userid User whose criteria completion needs to be reviewed.
     * @return bool Whether criteria is complete
     */
    abstract public function review($userid);

    /**
     * Mark this criteria as complete for a user
     *
     * @param int $userid User whose criteria is completed.
     */
    public function mark_complete($userid) {
        global $DB;
        $obj = array();
        $obj['critid'] = $this->id;
        $obj['userid'] = $userid;
        $obj['datemet'] = time();
        if (!$DB->record_exists('badge_criteria_met', array('critid' => $this->id, 'userid' => $userid))) {
            $DB->insert_record('badge_criteria_met', $obj);
        }
    }

    /**
     * Return criteria parameters
     *
     * @param int $critid Criterion ID
     * @return array
     */
    public function get_params($cid) {
        global $DB;
        $params = array();

        $records = $DB->get_records('badge_criteria_param', array('critid' => $cid));
        foreach ($records as $rec) {
            $arr = explode('_', $rec->name);
            $params[$arr[1]][$arr[0]] = $rec->value;
        }

        return $params;
    }

    /**
     * Delete this criterion
     *
     */
    public function delete() {
        global $DB;

        // Remove any records if it has already been met.
        $DB->delete_records('badge_criteria_met', array('critid' => $this->id));

        // Remove all parameters records.
        $DB->delete_records('badge_criteria_param', array('critid' => $this->id));

        // Finally remove criterion itself.
        $DB->delete_records('badge_criteria', array('id' => $this->id));
    }

    /**
     * Saves intial criteria records with required parameters set up.
     */
    public function save($params = array()) {
        global $DB;
        $fordb = new stdClass();
        $fordb->criteriatype = $this->criteriatype;
        $fordb->method = isset($params->agg) ? $params->agg : $params['agg'];
        $fordb->badgeid = $this->badgeid;
        $t = $DB->start_delegated_transaction();

        // Unset unnecessary parameters supplied with form.
        if (isset($params->agg)) {
            unset($params->agg);
        } else {
            unset($params['agg']);
        }
        unset($params->submitbutton);
        $params = array_filter((array)$params);

        if ($this->id !== 0) {
            $cid = $this->id;

            // Update criteria before doing anything with parameters.
            $fordb->id = $cid;
            $DB->update_record('badge_criteria', $fordb, true);

            $existing = $DB->get_fieldset_select('badge_criteria_param', 'name', 'critid = ?', array($cid));
            $todelete = array_diff($existing, array_keys($params));

            if (!empty($todelete)) {
                // A workaround to add some disabled elements that are still being submitted from the form.
                foreach ($todelete as $del) {
                    $name = explode('_', $del);
                    if ($name[0] == $this->required_param) {
                        foreach ($this->optional_params as $opt) {
                            $todelete[] = $opt . '_' . $name[1];
                        }
                    }
                }
                $todelete = array_unique($todelete);
                list($sql, $sqlparams) = $DB->get_in_or_equal($todelete, SQL_PARAMS_NAMED, 'd', true);
                $sqlparams = array_merge(array('critid' => $cid), $sqlparams);
                $DB->delete_records_select('badge_criteria_param', 'critid = :critid AND name ' . $sql, $sqlparams);
            }

            foreach ($params as $key => $value) {
                if (in_array($key, $existing)) {
                    $updp = $DB->get_record('badge_criteria_param', array('name' => $key, 'critid' => $cid));
                    $updp->value = $value;
                    $DB->update_record('badge_criteria_param', $updp, true);
                } else {
                    $newp = new stdClass();
                    $newp->critid = $cid;
                    $newp->name = $key;
                    $newp->value = $value;
                    $DB->insert_record('badge_criteria_param', $newp);
                }
            }
        } else {
            $cid = $DB->insert_record('badge_criteria', $fordb, true);
            if ($cid) {
                foreach ($params as $key => $value) {
                    $newp = new stdClass();
                    $newp->critid = $cid;
                    $newp->name = $key;
                    $newp->value = $value;
                    $DB->insert_record('badge_criteria_param', $newp, false, true);
                }
            }
         }
         $t->allow_commit();
    }

    /**
     * Saves intial criteria records with required parameters set up.
     */
    public function make_clone($newbadgeid) {
        global $DB;

        $fordb = new stdClass();
        $fordb->criteriatype = $this->criteriatype;
        $fordb->method = $this->method;
        $fordb->badgeid = $newbadgeid;
        if (($newcrit = $DB->insert_record('badge_criteria', $fordb, true)) && isset($this->params)) {
            foreach ($this->params as $k => $param) {
                foreach ($param as $key => $value) {
                    $paramdb = new stdClass();
                    $paramdb->critid = $newcrit;
                    $paramdb->name = $key . '_' . $k;
                    $paramdb->value = $value;
                    $DB->insert_record('badge_criteria_param', $paramdb);
                }
            }
        }
    }
}
