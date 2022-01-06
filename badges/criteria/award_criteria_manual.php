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
 * This file contains the manual badge award criteria type class
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Manual badge award criteria
 *
 */
class award_criteria_manual extends award_criteria {

    /* @var int Criteria [BADGE_CRITERIA_TYPE_MANUAL] */
    public $criteriatype = BADGE_CRITERIA_TYPE_MANUAL;

    public $required_param = 'role';
    public $optional_params = array();

    /**
     * Gets role name.
     * If no such role exists this function returns null.
     *
     * @return string|null
     */
    private function get_role_name($rid) {
        global $DB, $PAGE;
        $rec = $DB->get_record('role', array('id' => $rid));

        if ($rec) {
            return role_get_name($rec, $PAGE->context, ROLENAME_BOTH);
        } else {
            return null;
        }
    }

    /**
     * Add appropriate new criteria options to the form
     *
     */
    public function get_options(&$mform) {
        global $PAGE;
        $options = '';
        $none = true;

        $roles = get_roles_with_capability('moodle/badges:awardbadge', CAP_ALLOW, $PAGE->context);
        $visibleroles = get_viewable_roles($PAGE->context);
        $roleids = array_map(function($o) {
            return $o->id;
        }, $roles);
        $existing = array();
        $missing = array();

        if ($this->id !== 0) {
            $existing = array_keys($this->params);
            $missing = array_diff($existing, $roleids);
        }

        if (!empty($missing)) {
            $mform->addElement('header', 'category_errors', get_string('criterror', 'badges'));
            $mform->addHelpButton('category_errors', 'criterror', 'badges');
            foreach ($missing as $m) {
                $this->config_options($mform, array('id' => $m, 'checked' => true, 'name' => get_string('error:nosuchrole', 'badges'), 'error' => true));
                $none = false;
            }
        }

        if (!empty($roleids)) {
            $mform->addElement('header', 'first_header', $this->get_title());
            $mform->addHelpButton('first_header', 'criteria_' . $this->criteriatype, 'badges');
            foreach ($roleids as $rid) {
                if (!key_exists($rid, $visibleroles)) {
                    continue;
                }
                $checked = false;
                if (in_array($rid, $existing)) {
                    $checked = true;
                }
                $this->config_options($mform, array('id' => $rid, 'checked' => $checked, 'name' => self::get_role_name($rid), 'error' => false));
                $none = false;
            }
        }

        // Add aggregation.
        if (!$none) {
            $mform->addElement('header', 'aggregation', get_string('method', 'badges'));
            $agg = array();
            $agg[] =& $mform->createElement('radio', 'agg', '', get_string('allmethodmanual', 'badges'), 1);
            $agg[] =& $mform->createElement('static', 'none_break', null, '<br/>');
            $agg[] =& $mform->createElement('radio', 'agg', '', get_string('anymethodmanual', 'badges'), 2);
            $mform->addGroup($agg, 'methodgr', '', array(' '), false);
            if ($this->id !== 0) {
                $mform->setDefault('agg', $this->method);
            } else {
                $mform->setDefault('agg', BADGE_CRITERIA_AGGREGATION_ANY);
            }
        }

        return array($none, get_string('noparamstoadd', 'badges'));
    }

    /**
     * Get criteria details for displaying to users
     *
     * @return string
     */
    public function get_details($short = '') {
        global $OUTPUT;
        $output = array();
        foreach ($this->params as $p) {
            $str = self::get_role_name($p['role']);
            if (!$str) {
                $output[] = $OUTPUT->error_text(get_string('error:nosuchrole', 'badges'));
            } else {
                $output[] = $str;
            }
        }

        if ($short) {
            return implode(', ', $output);
        } else {
            return html_writer::alist($output, array(), 'ul');
        }
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
        global $DB;

        // Roles should always have a parameter.
        if (empty($this->params)) {
            return false;
        }

        // Users were already filtered by criteria completion.
        if ($filtered) {
            return true;
        }

        $overall = false;
        foreach ($this->params as $param) {
            $crit = $DB->get_record('badge_manual_award', array('issuerrole' => $param['role'], 'recipientid' => $userid, 'badgeid' => $this->badgeid));
            if ($this->method == BADGE_CRITERIA_AGGREGATION_ALL) {
                if (!$crit) {
                    return false;
                } else {
                    $overall = true;
                    continue;
                }
            } else {
                if (!$crit) {
                    $overall = false;
                    continue;
                } else {
                    return true;
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
        $join = '';
        $where = '';
        $params = array();

        if ($this->method == BADGE_CRITERIA_AGGREGATION_ANY) {
            foreach ($this->params as $param) {
                $roledata[] = " bma.issuerrole = :issuerrole{$param['role']} ";
                $params["issuerrole{$param['role']}"] = $param['role'];
            }
            if (!empty($roledata)) {
                $extraon = implode(' OR ', $roledata);
                $join = " JOIN {badge_manual_award} bma ON bma.recipientid = u.id
                          AND bma.badgeid = :badgeid{$this->badgeid} AND ({$extraon})";
                $params["badgeid{$this->badgeid}"] = $this->badgeid;
            }
            return array($join, $where, $params);
        } else {
            foreach ($this->params as $param) {
                $roledata[] = " bma.issuerrole = :issuerrole{$param['role']} ";
                $params["issuerrole{$param['role']}"] = $param['role'];
            }
            if (!empty($roledata)) {
                $extraon = implode(' AND ', $roledata);
                $join = " JOIN {badge_manual_award} bma ON bma.recipientid = u.id
                          AND bma.badgeid = :badgeid{$this->badgeid} AND ({$extraon})";
                $params["badgeid{$this->badgeid}"] = $this->badgeid;
            }
            return array($join, $where, $params);
        }
    }

    /**
     * Delete this criterion
     *
     */
    public function delete() {
        global $DB;

        // Remove any records of manual award.
        $DB->delete_records('badge_manual_award', array('badgeid' => $this->badgeid));

        parent::delete();
    }
}
