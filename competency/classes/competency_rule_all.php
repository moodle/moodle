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
 * Competency rule all.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use lang_string;

/**
 * Competency rule all class.
 *
 * This rule is considered matched when all the children of a competency are completed.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_rule_all extends competency_rule {

    /**
     * Whether or not the rule is matched.
     *
     * @param user_competency $usercompetency The user competency.
     * @return bool
     */
    public function matches(user_competency $usercompetency) {
        global $DB;

        // TODO Improve performance here, perhaps the caller could already provide records.
        $children = competency::get_records(array('parentid' => $this->competency->get_id()));

        if (empty($children)) {
            // Leaves are not compatible with this rule.
            return false;
        }

        $ids = array();
        foreach ($children as $child) {
            $ids[] = $child->get_id();
        }

        list($insql, $params) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
        $sql = "userid = :userid
            AND proficiency = :proficiency
            AND competencyid $insql";
        $params['userid'] = $usercompetency->get_userid();
        $params['proficiency'] = 1;

        // Is the user is marked as proficient in all children?
        return user_competency::count_records_select($sql, $params) === count($ids);
    }

    /**
     * Validate the rule config.
     *
     * @param string $value The value to validate.
     * @return bool
     */
    public function validate_config($value) {
        return $value === null;
    }

    /**
     * The name of the rule.
     *
     * @return lang_string
     */
    public static function get_name() {
        return new lang_string('allchildrenarecomplete', 'core_competency');
    }
}
