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
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Badge award criteria -- award on competency completion
 *
 * @package    core
 * @subpackage badges
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class award_criteria_competency extends award_criteria {

    /* @var int Criteria [BADGE_CRITERIA_TYPE_COMPETENCY] */
    public $criteriatype = BADGE_CRITERIA_TYPE_COMPETENCY;

    public $required_param = 'competency';
    public $self_validation = true;
    public $optional_params = array('');

    /**
     * Get criteria details for displaying to users
     * @param string $short Print short version of criteria
     * @return string
     */
    public function get_details($short = '') {
        global $DB, $OUTPUT;
        $output = array();

        foreach ($this->params as $p) {
            $competency = new \core_competency\competency($p['competency']);
            if ($short) {
                $competency->set('description', '');
            }
            $summary = new \tool_lp\output\competency_summary($competency, $competency->get_framework(), !$short, !$short);
            $str = $OUTPUT->render($summary);
            $output[] = $str;
        }

        return '<dl><dd class="p-3 mb-2 bg-light text-dark border">' .
               implode('</dd><dd class="p-3 mb-2 bg-light text-dark border">', $output) .
               '</dd></dl>';
    }

    /**
     * Add appropriate new criteria options to the form
     * @param object $mform moodle form
     */
    public function get_options(&$mform) {
        global $DB, $PAGE;
        $none = false;
        $availablebadges = null;

        $mform->addElement('header', 'first_header', $this->get_title());
        $mform->addHelpButton('first_header', 'criteria_' . $this->criteriatype, 'badges');

        // Determine if this badge is a course badge or a site badge.
        $competencies = '';
        if (count($this->params)) {
            $competencies = implode(',', array_keys($this->params));
        }
        $mform->addElement('static', 'competenciesdescription', '', '<div data-region="competencies"></div>');
        $mform->addElement('hidden', 'competency', $competencies, ['data-action' => 'competencies']);

        $mform->setType('competency', PARAM_RAW);
        $badge = $DB->get_record('badge', array('id' => $this->badgeid));
        if ($badge->type == BADGE_TYPE_SITE) {
            $context = context_system::instance();
        } else if ($badge->type == BADGE_TYPE_COURSE) {
            $context = context_course::instance($badge->courseid);
        }
        $params = [$context->id];
        // Require some JS to select the competencies.
        $PAGE->requires->js_call_amd('core_badges/competency', 'init', $params);

        $mform->addElement('button', 'select_competencies', get_string('addcompetency', 'badges'), ['data-action' => 'select-competencies']);

        // Add aggregation.
        if (!$none) {
            $mform->addElement('header', 'aggregation', get_string('method', 'badges'));
            $agg = array();
            $agg[] =& $mform->createElement('radio', 'agg', '', get_string('allmethodcompetencies', 'badges'), 1);
            $agg[] =& $mform->createElement('radio', 'agg', '', get_string('anymethodcompetencies', 'badges'), 2);
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
        $competencies = explode(',', $params['competency']);
        unset($params['competency']);
        foreach ($competencies as $competencyid) {
            $params["competency_{$competencyid}"] = $competencyid;
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
        $competencyids = [];

        foreach ($this->params as $param) {
            $competencyids[] = $param['competency'];
        }

        $existing = [];
        $badge = $DB->get_record('badge', array('id' => $this->badgeid));
        if ($badge->type == BADGE_TYPE_SITE) {
            $existing = \core_competency\user_competency::get_multiple($userid, $competencyids);
        } else if ($badge->type == BADGE_TYPE_COURSE) {
            $existing = \core_competency\user_competency_course::get_multiple($userid, $badge->courseid, $competencyids);
        }

        foreach ($this->params as $param) {
            $found = false;
            $proficiency = false;
            foreach ($existing as $usercompetency) {
                if ($usercompetency->get('competencyid') == $param['competency']) {
                    $found = true;
                    $proficiency = $usercompetency->get('proficiency');
                }
            }

            if ($this->method == BADGE_CRITERIA_AGGREGATION_ALL) {
                if (!$proficiency) {
                    return false;
                }
            } else if ($this->method == BADGE_CRITERIA_AGGREGATION_ANY) {
                if ($proficiency) {
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
        global $DB;

        $join = '';
        $where = '';
        $params = [];
        $competencyids = [];

        $badge = $DB->get_record('badge', array('id' => $this->badgeid));

        if ($this->method == BADGE_CRITERIA_AGGREGATION_ANY) {
            // User has received ANY of the required competencies (we can use an in or equals list).
            foreach ($this->params as $param) {
                $competencyids[] = $param['competency'];
            }

            $where = ' AND uc2.competencyid ';
            list($sql, $params) = $DB->get_in_or_equal($competencyids, SQL_PARAMS_NAMED, 'usercomp');
            $where .= $sql;
            if ($badge->type == BADGE_TYPE_SITE) {
                $join = ' JOIN {competency_usercomp} uc2 ON uc2.userid = u.id';
            } else if ($badge->type == BADGE_TYPE_COURSE) {
                $join = ' JOIN {competency_usercompcourse} uc2 ON uc2.userid = u.id AND uc2.courseid = :competencycourseid ';
                $params['competencycourseid'] = $badge->courseid;
            }
            $where .= ' AND uc2.proficiency = :isproficient ';
            $params['isproficient'] = true;
            return array($join, $where, $params);
        } else {

            // User has received ALL of the required competencies (we have to join on each one).
            $joincount = 0;
            foreach ($this->params as $param) {
                $joincount++;
                $join .= ' JOIN {competency_usercomp} uc' . $joincount . ' ON uc' . $joincount . '.userid = u.id';
                $where .= ' AND uc' . $joincount . '.competencyid = :competencyindex' . $joincount;
                $params['competencyindex' . $joincount] = $param['competency'];

                $where .= ' AND uc' . $joincount . '.userid = u.id';
                $where .= ' AND uc' . $joincount . '.proficiency = :isproficient' . $joincount;
                $params['isproficient' . $joincount] = true;
            }

            return array($join, $where, $params);
        }
        return array($join, $where, $params);
    }
}
