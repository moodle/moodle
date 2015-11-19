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
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp;
defined('MOODLE_INTERNAL') || die();

use lang_string;

/**
 * Template cohort persistent.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_cohort extends persistent {

    const TABLE = 'tool_lp_template_cohort';

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
        global $DB;
        if (!template::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
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

}
