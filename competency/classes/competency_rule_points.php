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
 * Competency rule points based.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use lang_string;


/**
 * Competency rule points based class.
 *
 * This rule matches when related competencies contribute for a required number of points.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_rule_points extends competency_rule {

    /**
     * Get the rule config.
     *
     * @return mixed
     */
    protected function get_config() {
        $config = parent::get_config();
        return json_decode($config);
    }

    /**
     * Whether or not the rule is matched.
     *
     * @param user_competency $usercompetency The user competency.
     * @return bool
     */
    public function matches(user_competency $usercompetency) {
        global $DB;

        $config = $this->get_config();
        $pointsrequired = $config->base->points;

        // Index by competency ID and extract required.
        $compsrules = array();
        $requiredids = array();
        foreach ($config->competencies as $comp) {
            $compsrules[$comp->id] = $comp;
            if ($comp->required) {
                $requiredids[$comp->id] = $comp->id;
            }
        }

        // Find all the user competency records.
        list($insql, $params) = $DB->get_in_or_equal(array_keys($compsrules), SQL_PARAMS_NAMED);
        $sql = "userid = :userid
            AND proficiency = :proficiency
            AND competencyid $insql";
        $params['userid'] = $usercompetency->get('userid');
        $params['proficiency'] = 1;
        $ucs = user_competency::get_records_select($sql, $params, '', 'competencyid');

        // Check that all the required are found.
        if (!empty($requiredids)) {
            $unmetrequired = array_diff_key($requiredids, $ucs);
            if (!empty($unmetrequired)) {
                return false;
            }
        }

        // Check that we have enough points.
        $points = 0;
        foreach ($compsrules as $compid => $comp) {
            if (array_key_exists($compid, $ucs)) {
                $points += $comp->points;
            }
        }

        return $points >= $pointsrequired;
    }

    /**
     * Validate the rule config.
     *
     * @param string $value The value to validate.
     * @return bool
     */
    public function validate_config($value) {
        $compids = array();
        $config = json_decode($value);
        if ($config === null || !isset($config->base) || !isset($config->competencies)) {
            return false;
        }

        if (!isset($config->base->points)) {
            return false;
        }

        try {
            $requiredpoints = validate_param($config->base->points, PARAM_INT);
        } catch (\invalid_parameter_exception $e) {
            return false;
        }

        if ($requiredpoints < 1) {
            return false;
        }

        $totalpoints = 0;

        // Validate the competency info.
        foreach ($config->competencies as $competency) {

            // Cannot include self.
            if ($competency->id == $this->competency->get('id')) {
                return false;
            }

            // Check for duplicates.
            if (in_array($competency->id, $compids)) {
                return false;
            }

            // Check for required fields.
            if (!isset($competency->id)
                    || !isset($competency->points)
                    || !isset($competency->required)) {
                return false;
            }

            // Validate the parameters.
            try {
                validate_param($competency->id, PARAM_INT);
                $points = validate_param($competency->points, PARAM_INT);
                validate_param($competency->required, PARAM_BOOL);
            } catch (\invalid_parameter_exception $e) {
                return false;
            }

            $totalpoints += $points;
            if ($points < 0) {
                return false;
            }

            $compids[] = $competency->id;
        }

        // No competencies, that's strange.
        if (empty($compids)) {
            return false;
        }

        // Impossible to reach the points required.
        if ($requiredpoints > $totalpoints) {
            return false;
        }

        // Check that all the competencies are children of the competency.
        // We may want to relax this check at a later stage if we want to allow competencies
        // to be linked throughout the whole framework.
        return $this->competency->is_parent_of($compids);
    }

    /**
     * The name of the rule.
     *
     * @return lang_string
     */
    public static function get_name() {
        return new lang_string('pointsrequiredaremet', 'core_competency');
    }

    /**
     * Migrate rule config when duplicate competency based on mapping competencies ids.
     *
     * @param string $config the config rule of a competency
     * @param array $mappings array that match the old competency ids with the new competencies
     * @return string
     */
    public static function migrate_config($config, $mappings) {
        $ruleconfig = json_decode($config, true);
        if (is_array($ruleconfig)) {
            foreach ($ruleconfig['competencies'] as $key => $rulecomp) {
                $rulecmpid = $rulecomp['id'];
                if (array_key_exists($rulecmpid, $mappings)) {
                    $ruleconfig['competencies'][$key]['id'] = $mappings[$rulecmpid]->get('id');
                } else {
                    throw new coding_exception("The competency id is not found in the matchids.");
                }
            }
        } else {
            throw new coding_exception("Invalid JSON config rule.");
        }

        return json_encode($ruleconfig);
    }
}
