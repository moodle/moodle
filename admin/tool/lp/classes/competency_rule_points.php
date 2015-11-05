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
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp;
defined('MOODLE_INTERNAL') || die();

use lang_string;


/**
 * Competency rule points based class.
 *
 * This rule matches when related competencies contribute for a required number of points.
 *
 * @package    tool_lp
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
        $params['userid'] = $usercompetency->get_userid();
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
            if ($competency->id == $this->competency->get_id()) {
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
     * Validate the rule config.
     *
     * @return bool
     */
    public static function get_amd_module() {
        return 'tool_lp/competency_rule_points';
    }

    /**
     * The name of the rule.
     *
     * @return lang_string
     */
    public static function get_name() {
        return new lang_string('pointsrequiredaremet', 'tool_lp');
    }
}
