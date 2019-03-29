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

    /* @var int The criteria type */
    public $criteriatype = BADGE_CRITERIA_TYPE_COMPETENCY;
    /* @var string a required param */
    public $required_param = 'competency';
    /* @var array no optional params */
    public $optional_params = [];


    /**
     * Get criteria details for displaying to users
     * @param string $short Print short version of criteria
     * @return string
     */
    public function get_details($short = '') {
        $output = array();

        foreach ($this->params as $p) {
            $competency = new \core_competency\competency($p['competency']);
            if ($short) {
                $competency->set('description', '');
            }
            // Render the competency even if competencies are not currently enabled.
            \core_competency\api::skip_enabled();
            if ($pluginsfunction = get_plugins_with_function('render_competency_summary')) {
                foreach ($pluginsfunction as $plugintype => $plugins) {
                    foreach ($plugins as $pluginfunction) {
                        $output[] = $pluginfunction($competency, $competency->get_framework(), false, false, true);
                    }
                }
            }
            \core_competency\api::check_enabled();
        }

        return '<dl><dd class="p-3 mb-2 bg-light text-dark border">' .
               implode('</dd><dd class="p-3 mb-2 bg-light text-dark border">', $output) .
               '</dd></dl>';
    }

    /**
     * Add appropriate new criteria options to the form
     * @param object $mform moodle form
     * @return array First item is a boolean to indicate an error and the second is the error message.
     */
    public function get_options(&$mform) {
        global $DB;
        $none = false;
        $availablebadges = null;

        $mform->addElement('header', 'first_header', $this->get_title());
        $mform->addHelpButton('first_header', 'criteria_' . $this->criteriatype, 'badges');

        // Determine if this badge is a course badge or a site badge.
        $competencies = '';
        if (count($this->params)) {
            $competencies = implode(',', array_keys($this->params));
        }
        $badge = $DB->get_record('badge', array('id' => $this->badgeid));
        $context = null;
        $courseid = 0;

        if ($badge->type == BADGE_TYPE_SITE) {
            $context = context_system::instance();
            $courseid = SITEID;
        } else if ($badge->type == BADGE_TYPE_COURSE) {
            $context = context_course::instance($badge->courseid);
            $courseid = $badge->courseid;
        }
        if ($pluginsfunction = get_plugins_with_function('competency_picker')) {
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $pluginfunction) {
                    $output[] = $pluginfunction($mform, $courseid, $context, 'competency_competencies');
                }
            }
        }
        $mform->getElement('competency_competencies')->setValue($competencies);
        $mform->addRule('competency_competencies', get_string('requiredcompetency', 'badges'), 'required');

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
        $competencies = $params['competency_competencies'];
        unset($params['competency_competencies']);
        if (is_string($competencies)) {
            $competencies = explode(',', $competencies);
        }
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

        if (!self::is_enabled()) {
            return false;
        }
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
            $proficiency = false;
            foreach ($existing as $usercompetency) {
                if ($usercompetency->get('competencyid') == $param['competency']) {
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

        if (!self::is_enabled()) {
            return array($join, $where, $params);
        }

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

        }
        return array($join, $where, $params);
    }

    /**
     * Hide this criteria when competencies are disabled.
     *
     * @return boolean
     */
    public static function is_enabled() {
        return \core_competency\api::is_enabled();
    }

    /**
     * Check if any badge has records for competencies.
     *
     * @param array $competencyids Array of competencies ids.
     * @return boolean Return true if competencies were found in any badge.
     */
    public static function has_records_for_competencies($competencyids) {
        global $DB;
        list($insql, $params) = $DB->get_in_or_equal($competencyids, SQL_PARAMS_NAMED);
        $sql = "SELECT DISTINCT bc.badgeid
                    FROM {badge_criteria} bc
                    JOIN {badge_criteria_param} bcp ON bc.id = bcp.critid
                    WHERE bc.criteriatype = :criteriatype AND value $insql";
        $params['criteriatype'] = BADGE_CRITERIA_TYPE_COMPETENCY;

        return self::record_exists_sql($sql, $params);
    }
}
