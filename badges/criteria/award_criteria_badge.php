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
 * This file contains the badge earned badge award criteria type class
 *
 * @package    core
 * @subpackage badges
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Badge award criteria -- award on badge completion
 *
 * @package    core
 * @subpackage badges
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class award_criteria_badge extends award_criteria {

    /* @var int Criteria [BADGE_CRITERIA_TYPE_BADGE] */
    public $criteriatype = BADGE_CRITERIA_TYPE_BADGE;

    public $required_param = 'badge';
    public $optional_params = array();

    /**
     * Get criteria details for displaying to users
     * @param string $short Print short version of criteria
     * @return string
     */
    public function get_details($short = '') {
        global $DB, $OUTPUT;
        $output = array();
        foreach ($this->params as $p) {
            $badgename = $DB->get_field('badge', 'name', array('id' => $p['badge']));
            if (!$badgename) {
                $str = $OUTPUT->error_text(get_string('error:nosuchbadge', 'badges'));
            } else {
                $str = html_writer::tag('b', '"' . $badgename . '"');
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
     * @param object $mform moodle form
     */
    public function get_options(&$mform) {
        global $DB;
        $none = false;
        $availablebadges = null;

        $mform->addElement('header', 'first_header', $this->get_title());
        $mform->addHelpButton('first_header', 'criteria_' . $this->criteriatype, 'badges');

        // Determine if this badge is a course badge or a site badge.
        $thisbadge = $DB->get_record('badge', array('id' => $this->badgeid));

        if ($thisbadge->type == BADGE_TYPE_SITE) {
            // Only list site badges that are enabled.
            $select = " type = :site AND (status = :status1 OR status = :status2)";
            $params = array('site' => BADGE_TYPE_SITE,
                            'status1' => BADGE_STATUS_ACTIVE,
                            'status2' => BADGE_STATUS_ACTIVE_LOCKED);
            $availablebadges = $DB->get_records_select_menu('badge', $select, $params, 'name ASC', 'id, name');

        } else if ($thisbadge->type == BADGE_TYPE_COURSE) {
            // List both site badges and course badges belonging to this course.
            $select = " (type = :site OR (type = :course AND courseid = :courseid)) AND (status = :status1 OR status = :status2)";
            $params = array('site' => BADGE_TYPE_SITE,
                            'course' => BADGE_TYPE_COURSE,
                            'courseid' => $thisbadge->courseid,
                            'status1' => BADGE_STATUS_ACTIVE,
                            'status2' => BADGE_STATUS_ACTIVE_LOCKED);
            $availablebadges = $DB->get_records_select_menu('badge', $select, $params, 'name ASC', 'id, name');
        }
        if (!empty($availablebadges)) {
            $select = array();
            $selected = array();
            foreach ($availablebadges as $bid => $badgename) {
                if ($bid != $this->badgeid) {
                    // Do not let it use itself as criteria.
                    $select[$bid] = format_string($badgename, true);
                }
            }

            if ($this->id !== 0) {
                $selected = array_keys($this->params);
            }
            $settings = array('multiple' => 'multiple', 'size' => 20, 'class' => 'selectbadge', 'required' => 'required');
            $mform->addElement('select', 'badge_badges', get_string('addbadge', 'badges'), $select, $settings);
            $mform->addRule('badge_badges', get_string('requiredbadge', 'badges'), 'required');
            $mform->addHelpButton('badge_badges', 'addbadge', 'badges');

            if ($this->id !== 0) {
                $mform->setDefault('badge_badges', $selected);
            }
        } else {
            $mform->addElement('static', 'nobadges', '', get_string('error:nobadges', 'badges'));
            $none = true;
        }

        // Add aggregation.
        if (!$none) {
            $mform->addElement('header', 'aggregation', get_string('method', 'badges'));
            $agg = array();
            $agg[] =& $mform->createElement('radio', 'agg', '', get_string('allmethodbadges', 'badges'), 1);
            $agg[] =& $mform->createElement('radio', 'agg', '', get_string('anymethodbadges', 'badges'), 2);
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
     * @param array $params Values from the form or any other array.
     */
    public function save($params = array()) {
        $badges = $params['badge_badges'];
        unset($params['badge_badges']);
        foreach ($badges as $badgeid) {
            $params["badge_{$badgeid}"] = $badgeid;
        }

        parent::save($params);
    }

    /**
     * Review this criteria and decide if it has been completed
     *
     * @param int $userid User whose criteria completion needs to be reviewed.
     * @param bool $filtered An additional parameter indicating that user list
     *        has been reduced and some expensive checks can be skipped.
     *
     * @return bool Whether criteria is complete.
     */
    public function review($userid, $filtered = false) {

        global $DB;
        $overall = false;

        foreach ($this->params as $param) {
            $badge = $DB->get_record('badge', array('id' => $param['badge']));
            // See if the user has earned this badge.
            $awarded = $DB->get_record('badge_issued', array('badgeid' => $param['badge'], 'userid' => $userid));

            // Extra check in case a badge was deleted while this badge is still active.
            if (!$badge) {
                if ($this->method == BADGE_CRITERIA_AGGREGATION_ALL) {
                    return false;
                } else {
                    continue;
                }
            }

            if ($this->method == BADGE_CRITERIA_AGGREGATION_ALL) {

                if ($awarded) {
                    $overall = true;
                    continue;
                } else {
                    return false;
                }
            } else if ($this->method == BADGE_CRITERIA_AGGREGATION_ANY) {
                if ($awarded) {
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

            if (($singleparam || $method) && !$DB->record_exists('badge', array('id' => $param))) {
                return array(false, get_string('error:invalidparambadge', 'badges'));
            }
        }

        return array(true, '');
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
            // User has received ANY of the required badges.
            $join = " LEFT JOIN {badge_issued} bi2 ON bi2.userid = u.id";
            $i = 0;
            foreach ($this->params as $param) {
                if ($i == 0) {
                    $where .= ' bi2.badgeid = :badgeid'.$i;
                } else {
                    $where .= ' OR bi2.badgeid = :badgeid'.$i;
                }
                $params['badgeid'.$i] = $param['badge'];
                $i++;
            }
            // MDL-66032 Do not create expression if there are no badges in criteria.
            if (!empty($where)) {
                $where = ' AND (' . $where . ') ';
            }
            return array($join, $where, $params);
        } else {
            // User has received ALL of the required badges.
            $join = " LEFT JOIN {badge_issued} bi2 ON bi2.userid = u.id";
            $i = 0;
            foreach ($this->params as $param) {
                $i++;
                $where = ' AND bi2.badgeid = :badgeid'.$i;
                $params['badgeid'.$i] = $param['badge'];
            }
            return array($join, $where, $params);
        }
    }
}
