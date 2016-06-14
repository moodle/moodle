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
 * Template cohort persistent.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use lang_string;
use core_competency\template;

/**
 * Template cohort persistent.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_cohort extends persistent {

    const TABLE = 'competency_templatecohort';

    /** 2 hours of threshold to prevent expired plans **/
    const DUEDATE_THRESHOLD = 7200;

    /**
     * Return the custom definition of the properties of this model.
     *
     * @return array Where keys are the property names.
     */
    protected static function define_properties() {
        return array(
            'templateid' => array(
                'type' => PARAM_INT
            ),
            'cohortid' => array(
                'type' => PARAM_INT
            )
        );
    }

    /**
     * Validate the cohort ID.
     *
     * @param  int $value The cohort ID.
     * @return true|lang_string
     */
    protected function validate_cohortid($value) {
        global $DB;
        if (!$DB->record_exists('cohort', array('id' => $value))) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Validate the template ID.
     *
     * @param  int $value The template ID.
     * @return true|lang_string
     */
    protected function validate_templateid($value) {
        if (!template::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Return an array of user IDs for which the plans are missing.
     *
     * Plans are considered as missing when a member of a cohort does not have a plan created.
     * When the parameter $unlinkedaremissing is set to false, plans that were unlinked from
     * their template will be ignored so that we do not recreate unlinked plans endlessly.
     *
     * This method ignores the due date of the template.
     *
     * @param  int     $templateid The template ID.
     * @param  int     $cohortid The cohort ID.
     * @param  boolean $unlinkedaremissing When true, unlinked plans are considered as missing.
     * @return int[]   User IDs.
     */
    public static function get_missing_plans($templateid, $cohortid, $unlinkedaremissing = false) {
        global $DB;

        $skipsql = '';
        $skipparams = array();
        if (!$unlinkedaremissing) {
            $skipsql = 'OR p.origtemplateid = :origtemplateid';
            $skipparams = array('origtemplateid' => $templateid);
        }

        $sql = "SELECT cm.userid
                  FROM {cohort_members} cm
             LEFT JOIN {" . plan::TABLE . "} p
                    ON p.userid = cm.userid
                   AND (p.templateid = :templateid
                        $skipsql)
                 WHERE cm.cohortid = :cohortid
                   AND p.id IS NULL";
        $params = array('templateid' => $templateid, 'cohortid' => $cohortid) + $skipparams;

        return $DB->get_fieldset_sql($sql, $params);
    }

    /**
     * Get a relation.
     *
     * This does not perform any validation on the data passed. If the relation exists in the database
     * then it is loaded in a the model, if not then it is up to the developer to save the model.
     *
     * @param int $templateid
     * @param int $cohortid
     * @return template_cohort
     */
    public static function get_relation($templateid, $cohortid) {
        global $DB;

        $params = array(
            'templateid' => $templateid,
            'cohortid' => $cohortid
        );

        $relation = new static(null, (object) $params);
        if ($record = $DB->get_record(self::TABLE, $params)) {
            $relation->from_record($record);
        }

        return $relation;
    }

    /**
     * Get a relations by templateid.
     *
     * This does not perform any validation on the data passed. If the relation exists in the database
     * then it is loaded in a the model, if not then it is up to the developer to save the model.
     *
     * @param int $templateid
     * @return template_cohort[] array of template cohort
     */
    public static function get_relations_by_templateid($templateid) {
        global $DB;

        $params = array(
            'templateid' => $templateid
        );

        $relations = array();
        $records = $DB->get_records(self::TABLE, $params);
        foreach ($records as $record) {
            $relations[] = new template_cohort(0, $record);
        }

        return $relations;
    }

    /**
     * Return an array of templates persistent with their missing userids.
     *
     * Note that only cohorts associated with visible templates are considered,
     * as well as only templates with a due date in the future, or no due date.
     *
     * @param int $lastruntime  The last time the Cohort ssync task ran.
     * @param bool $unlinkedaremissing When true, unlinked plans are considered as missing.
     * @return array( array(
     *                   'template' => \core_competency\template,
     *                   'userids' => array
     *              ))
     */
    public static function get_all_missing_plans($lastruntime = 0, $unlinkedaremissing = false) {
        global $DB;

        $planwhereclause = " WHERE (p.id is NULL
                               AND (cm.timeadded >= :lastruntime1
                                OR tc.timecreated >= :lastruntime3
                                OR t.timemodified >= :lastruntime4))";

        if ($unlinkedaremissing) {
            $planwhereclause .= " OR (p.origtemplateid IS NOT NULL AND cm.timeadded < :lastruntime2)";
        }

        $sql = "SELECT " . $DB->sql_concat('cm.userid', 'tc.templateid') . " as uniqueid, cm.userid, t.*
                  FROM {cohort_members} cm
                  JOIN {" . self::TABLE . "} tc ON cm.cohortid = tc.cohortid
                  JOIN {" . template::TABLE . "} t
                    ON (tc.templateid = t.id AND t.visible = 1)
                   AND (t.duedate = 0 OR t.duedate > :time1)
             LEFT JOIN {" . plan::TABLE . "} p ON (cm.userid = p.userid AND (t.id = p.templateid OR t.id = p.origtemplateid))
                  $planwhereclause
              ORDER BY t.id";

        $params = array('time1' => time(), 'time2' => time(),
                        'lastruntime1' => $lastruntime, 'lastruntime2' => $lastruntime,
                        'lastruntime3' => $lastruntime, 'lastruntime4' => $lastruntime);
        $results = $DB->get_records_sql($sql, $params);

        $missingplans = array();
        foreach ($results as $usertemplate) {
            $userid = $usertemplate->userid;

            // Check if template already exist in the array.
            if (isset($missingplans[$usertemplate->id])) {
                $missingplans[$usertemplate->id]['userids'][] = $userid;
            } else {
                unset($usertemplate->userid);
                unset($usertemplate->uniqueid);
                $template = new template(0, $usertemplate);
                $missingplans[$template->get_id()]['template'] = $template;
                $missingplans[$template->get_id()]['userids'][] = $userid;
            }
        }
        return array_values($missingplans);
    }

}
