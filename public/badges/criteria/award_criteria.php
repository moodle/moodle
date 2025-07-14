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
 * Badge completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('BADGE_CRITERIA_TYPE_BADGE', 7);

/*
 * Cohort criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('BADGE_CRITERIA_TYPE_COHORT', 8);

/*
 * Competency criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('BADGE_CRITERIA_TYPE_COMPETENCY', 9);

/**
 * Award criteria abstract definition
 *
 */
abstract class award_criteria {

    /**
     * ID of the criterion.
     * @var integer
     */
    public $id;

    /**
     * Aggregation method [BADGE_CRITERIA_AGGREGATION_ANY, BADGE_CRITERIA_AGGREGATION_ALL].
     * @var integer
     */
    public $method;

    /**
     * ID of a badge this criterion belongs to.
     * @var integer
     */
    public $badgeid;

    /**
     * Criterion HTML/plain text description.
     * @var string
     */
    public $description;

    /**
     * Format of the criterion description.
     * @var integer
     */
    public $descriptionformat;

    /**
     * Any additional parameters.
     * @var array
     */
    public $params = array();

    /**
     * Criteria type.
     * @var string
     */
    public $criteriatype;

    /**
     * Required parameters.
     * @var string
     */
    public $required_param = '';

    /**
     * Optional parameters.
     * @var array
     */
    public $optional_params = [];

    /**
     * The base constructor
     *
     * @param array $params
     */
    public function __construct($params) {
        $this->id = isset($params['id']) ? $params['id'] : 0;
        $this->method = isset($params['method']) ? $params['method'] : BADGE_CRITERIA_AGGREGATION_ANY;
        $this->badgeid = $params['badgeid'];
        $this->description = isset($params['description']) ? $params['description'] : '';
        $this->descriptionformat = isset($params['descriptionformat']) ? $params['descriptionformat'] : FORMAT_HTML;
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
        global $CFG;

        require_once($CFG->libdir . '/badgeslib.php');

        $types = badges_list_criteria(false);

        if (!isset($params['criteriatype']) || !isset($types[$params['criteriatype']])) {
            throw new \moodle_exception('error:invalidcriteriatype', 'badges');
        }

        $class = 'award_criteria_' . $types[$params['criteriatype']];
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
            $parameter[] =& $mform->createElement('static', 'break_start_' . $param['id'], null,
                '<div class="ms-3 mt-1 w-100 align-items-center">');

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

        if (!empty($this->description)) {
            $badge = new badge($this->badgeid);
            echo $OUTPUT->box(
                format_text($this->description, $this->descriptionformat, array('context' => $badge->get_context())),
                'criteria-description'
                );
        }

        if (!empty($this->params)) {
            if (count($this->params) > 1) {
                echo $OUTPUT->box(get_string('criteria_descr_' . $this->criteriatype, 'badges',
                        core_text::strtoupper($agg[$data->get_aggregation_method($this->criteriatype)])), array('clearfix'));
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
     * @param bool $filtered An additional parameter indicating that user list
     *        has been reduced and some expensive checks can be skipped.
     *
     * @return bool Whether criteria is complete
     */
    abstract public function review($userid, $filtered = false);

    /**
     * Returns array with sql code and parameters returning all ids
     * of users who meet this particular criterion.
     *
     * @return array list($join, $where, $params)
     */
    abstract public function get_completed_criteria_sql();

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
        global $DB, $PAGE;

        // Remove any records if it has already been met.
        $DB->delete_records('badge_criteria_met', array('critid' => $this->id));

        // Remove all parameters records.
        $DB->delete_records('badge_criteria_param', array('critid' => $this->id));

        // Finally remove criterion itself.
        $DB->delete_records('badge_criteria', array('id' => $this->id));

        // Trigger event, badge criteria deleted.
        $eventparams = array('objectid' => $this->id,
            'context' => $PAGE->context,
            'other' => array('badgeid' => $this->badgeid));
        $event = \core\event\badge_criteria_deleted::create($eventparams);
        $event->trigger();
    }

    /**
     * Saves intial criteria records with required parameters set up.
     *
     * @param array $params Values from the form or any other array.
     */
    public function save($params = array()) {
        global $DB, $PAGE;

        // Figure out criteria description.
        // If it is coming from the form editor, it is an array(text, format).
        $description = '';
        $descriptionformat = FORMAT_HTML;
        if (isset($params['description']['text'])) {
            $description = $params['description']['text'];
            $descriptionformat = $params['description']['format'];
        } else if (isset($params['description'])) {
            $description = $params['description'];
        }

        $fordb = new stdClass();
        $fordb->criteriatype = $this->criteriatype;
        $fordb->method = isset($params['agg']) ? $params['agg'] : BADGE_CRITERIA_AGGREGATION_ALL;
        $fordb->badgeid = $this->badgeid;
        $fordb->description = $description;
        $fordb->descriptionformat = $descriptionformat;
        $t = $DB->start_delegated_transaction();

        // Pick only params that are required by this criterion.
        // Filter out empty values first.
        $params = array_filter($params);
        // Find out which param matches optional and required ones.
        $match = array_merge($this->optional_params, array($this->required_param));
        $regex = implode('|', array_map(function($a) {
            return $a . "_";
        }, $match));
        $requiredkeys = preg_grep('/^(' . $regex . ').*$/', array_keys($params));

        if ($this->id !== 0) {
            $cid = $this->id;

            // Update criteria before doing anything with parameters.
            $fordb->id = $cid;
            $DB->update_record('badge_criteria', $fordb, true);

            // Trigger event: badge_criteria_updated.
            $eventparams = array('objectid' => $this->id,
                'context' => $PAGE->context,
                'other' => array('badgeid' => $this->badgeid));
            $event = \core\event\badge_criteria_updated::create($eventparams);
            $event->trigger();

            $existing = $DB->get_fieldset_select('badge_criteria_param', 'name', 'critid = ?', array($cid));
            $todelete = array_diff($existing, $requiredkeys);

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

            foreach ($requiredkeys as $key) {
                if (in_array($key, $existing)) {
                    $updp = $DB->get_record('badge_criteria_param', array('name' => $key, 'critid' => $cid));
                    $updp->value = $params[$key];
                    $DB->update_record('badge_criteria_param', $updp, true);
                } else {
                    $newp = new stdClass();
                    $newp->critid = $cid;
                    $newp->name = $key;
                    $newp->value = $params[$key];
                    $DB->insert_record('badge_criteria_param', $newp);
                }
            }
        } else {
            $cid = $DB->insert_record('badge_criteria', $fordb, true);
            if ($cid) {
                foreach ($requiredkeys as $key) {
                    $newp = new stdClass();
                    $newp->critid = $cid;
                    $newp->name = $key;
                    $newp->value = $params[$key];
                    $DB->insert_record('badge_criteria_param', $newp, false, true);
                }
            }
            // Trigger event: badge_criteria_created.
            $eventparams = array('objectid' => $this->id,
                'context' => $PAGE->context,
                'other' => array('badgeid' => $this->badgeid));
            $event = \core\event\badge_criteria_created::create($eventparams);
            $event->trigger();
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
        $fordb->description = $this->description;
        $fordb->descriptionformat = $this->descriptionformat;
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

    /**
     * Allow some specific criteria types to be disabled based on config.
     *
     * @return boolean
     */
    public static function is_enabled() {
        return true;
    }
}
