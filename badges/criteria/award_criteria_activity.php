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
 * This file contains the activity badge award criteria type class
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/completionlib.php');

/**
 * Badge award criteria -- award on activity completion
 *
 */
class award_criteria_activity extends award_criteria {

    /* @var int Criteria [BADGE_CRITERIA_TYPE_ACTIVITY] */
    public $criteriatype = BADGE_CRITERIA_TYPE_ACTIVITY;

    private $courseid;
    private $course;

    public $required_param = 'module';
    public $optional_params = array('bydate');

    public function __construct($record) {
        global $DB;
        parent::__construct($record);

        $this->course = $DB->get_record_sql('SELECT c.id, c.enablecompletion, c.cacherev, c.startdate
                        FROM {badge} b LEFT JOIN {course} c ON b.courseid = c.id
                        WHERE b.id = :badgeid ', array('badgeid' => $this->badgeid), MUST_EXIST);

        // If the course doesn't exist but we're sure the badge does (thanks to the LEFT JOIN), then use the site as the course.
        if (empty($this->course->id)) {
            $this->course = get_course(SITEID);
        }
        $this->courseid = $this->course->id;
    }

    /**
     * Gets the module instance from the database and returns it.
     * If no module instance exists this function returns false.
     *
     * @return stdClass|bool
     */
    private function get_mod_instance($cmid) {
        global $DB;
        $rec = $DB->get_record_sql("SELECT md.name
                               FROM {course_modules} cm,
                                    {modules} md
                               WHERE cm.id = ? AND
                                     md.id = cm.module", array($cmid));

        if ($rec) {
            return get_coursemodule_from_id($rec->name, $cmid);
        } else {
            return null;
        }
    }

    /**
     * Get criteria description for displaying to users
     *
     * @return string
     */
    public function get_details($short = '') {
        global $DB, $OUTPUT;
        $output = array();
        foreach ($this->params as $p) {
            $mod = self::get_mod_instance($p['module']);
            if (!$mod) {
                $str = $OUTPUT->error_text(get_string('error:nosuchmod', 'badges'));
            } else {
                $str = html_writer::tag('b', '"' . get_string('modulename', $mod->modname) . ' - ' . $mod->name . '"');
                if (isset($p['bydate'])) {
                    $str .= get_string('criteria_descr_bydate', 'badges', userdate($p['bydate'], get_string('strftimedate', 'core_langconfig')));
                }
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
        $none = true;
        $existing = array();
        $missing = array();

        $course = $this->course;
        $info = new completion_info($course);
        $mods = $info->get_activities();
        $mids = array_keys($mods);

        if ($this->id !== 0) {
            $existing = array_keys($this->params);
            $missing = array_diff($existing, $mids);
        }

        if (!empty($missing)) {
            $mform->addElement('header', 'category_errors', get_string('criterror', 'badges'));
            $mform->addHelpButton('category_errors', 'criterror', 'badges');
            foreach ($missing as $m) {
                $this->config_options($mform, array('id' => $m, 'checked' => true,
                        'name' => get_string('error:nosuchmod', 'badges'), 'error' => true));
                $none = false;
            }
        }

        if (!empty($mods)) {
            $mform->addElement('header', 'first_header', $this->get_title());
            foreach ($mods as $mod) {
                $checked = false;
                if (in_array($mod->id, $existing)) {
                    $checked = true;
                }
                $param = array('id' => $mod->id,
                        'checked' => $checked,
                        'name' => get_string('modulename', $mod->modname) . ' - ' . $mod->name,
                        'error' => false
                        );

                if ($this->id !== 0 && isset($this->params[$mod->id]['bydate'])) {
                    $param['bydate'] = $this->params[$mod->id]['bydate'];
                }

                if ($this->id !== 0 && isset($this->params[$mod->id]['grade'])) {
                    $param['grade'] = $this->params[$mod->id]['grade'];
                }

                $this->config_options($mform, $param);
                $none = false;
            }
        }

        // Add aggregation.
        if (!$none) {
            $mform->addElement('header', 'aggregation', get_string('method', 'badges'));
            $agg = array();
            $agg[] =& $mform->createElement('radio', 'agg', '', get_string('allmethodactivity', 'badges'), 1);
            $agg[] =& $mform->createElement('radio', 'agg', '', get_string('anymethodactivity', 'badges'), 2);
            $mform->addGroup($agg, 'methodgr', '', array('<br/>'), false);
            if ($this->id !== 0) {
                $mform->setDefault('agg', $this->method);
            } else {
                $mform->setDefault('agg', BADGE_CRITERIA_AGGREGATION_ANY);
            }
        }

        return array($none, get_string('error:noactivities', 'badges'));
    }

    /**
     * Review this criteria and decide if it has been completed
     *
     * @param int $userid User whose criteria completion needs to be reviewed.
     * @param bool $filtered An additional parameter indicating that user list
     *        has been reduced and some expensive checks can be skipped.
     *
     * @return bool Whether criteria is complete
     */
    public function review($userid, $filtered = false) {
        $completionstates = array(COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS, COMPLETION_COMPLETE_FAIL);

        if ($this->course->startdate > time()) {
            return false;
        }

        $info = new completion_info($this->course);

        $overall = false;
        foreach ($this->params as $param) {
            $cm = new stdClass();
            $cm->id = $param['module'];

            $data = $info->get_data($cm, false, $userid);
            $check_date = true;

            if (isset($param['bydate'])) {
                $date = $data->timemodified;
                $check_date = ($date <= $param['bydate']);
            }

            if ($this->method == BADGE_CRITERIA_AGGREGATION_ALL) {
                if (in_array($data->completionstate, $completionstates) && $check_date) {
                    $overall = true;
                    continue;
                } else {
                    return false;
                }
            } else {
                if (in_array($data->completionstate, $completionstates) && $check_date) {
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
     * Returns array with sql code and parameters returning all ids
     * of users who meet this particular criterion.
     *
     * @return array list($join, $where, $params)
     */
    public function get_completed_criteria_sql() {
        global $DB;
        $join = '';
        $where = '';
        $params = array();

        if ($this->method == BADGE_CRITERIA_AGGREGATION_ANY) {
            foreach ($this->params as $param) {
                $moduledata[] = " cmc.coursemoduleid = :completedmodule{$param['module']} ";
                $params["completedmodule{$param['module']}"] = $param['module'];
            }
            if (!empty($moduledata)) {
                $extraon = implode(' OR ', $moduledata);
                $join = " JOIN {course_modules_completion} cmc ON cmc.userid = u.id AND
                          ( cmc.completionstate = :completionfail OR
                          cmc.completionstate = :completionpass OR
                          cmc.completionstate = :completioncomplete ) AND ({$extraon})";
                $params["completionpass"] = COMPLETION_COMPLETE_PASS;
                $params["completionfail"] = COMPLETION_COMPLETE_FAIL;
                $params["completioncomplete"] = COMPLETION_COMPLETE;
            }
            return array($join, $where, $params);
        } else {
            // Get all cmids of modules related to the criteria.
            $cmids = array_map(fn ($x) => $x['module'], $this->params);
            list($cmcmodulessql, $paramscmc) = $DB->get_in_or_equal($cmids, SQL_PARAMS_NAMED);

            // Create a sql query to get all users who have worked on these course modules.
            $sql = "SELECT DISTINCT userid FROM {course_modules_completion} cmc "
            . "WHERE coursemoduleid " . $cmcmodulessql . " AND "
            . "( cmc.completionstate IN ( :completionpass, :completionfail, :completioncomplete ) )";
            $paramscmcs = [
                'completionpass' => COMPLETION_COMPLETE_PASS,
                'completionfail' => COMPLETION_COMPLETE_FAIL,
                'completioncomplete' => COMPLETION_COMPLETE
            ];
            $paramscmc = array_merge($paramscmc, $paramscmcs);
            $userids = $DB->get_records_sql($sql, $paramscmc);

            // Now check each user if the user has a completion of each module.
            $useridsbadgeable = array_keys(array_filter(
                $userids,
                function ($user) use ($cmcmodulessql, $paramscmc, $cmids) {
                    global $DB;
                    $params = array_merge($paramscmc, ['userid' => $user->userid]);
                    $select = "coursemoduleid " . $cmcmodulessql . " AND userid = :userid";
                    $cmidsuser = $DB->get_fieldset_select('course_modules_completion', 'coursemoduleid', $select, $params);
                    return empty(array_diff($cmidsuser, $cmids));
                }
            ));

            // Finally create a where statement (if neccessary) with all userids who are allowed to get the badge.
            // This list also includes all users who have previously received the badge. These are filtered out in the badge.php.
            $join = "";
            $where = "";
            if (!empty($useridsbadgeable)) {
                list($wherepart, $params) = $DB->get_in_or_equal($useridsbadgeable, SQL_PARAMS_NAMED);
                $where = " AND u.id " . $wherepart;
            }
            return array($join, $where, $params);
        }
    }
}
