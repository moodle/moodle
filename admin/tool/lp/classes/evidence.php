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
 * Evidence persistent file.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use lang_string;
use stdClass;

/**
 * Evidence persistent class.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class evidence extends persistent {

    const TABLE = 'tool_lp_evidence';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'usercompetencyid' => array(
                'type' => PARAM_INT
            ),
            'descidentifier' => array(
                'type' => PARAM_STRINGID
            ),
            'desccomponent' => array(
                'type' => PARAM_COMPONENT
            ),
            'desca' => array(
                'type' => PARAM_RAW,
                'default' => null,
                'null' => NULL_ALLOWED
            ),
            'url' => array(
                'type' => PARAM_URL,
                'default' => null,
                'null' => NULL_ALLOWED
            ),
            'grade' => array(
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            )
        );
    }

    /**
     * Convenience method to get the description $a.
     *
     * @return mixed
     */
    public function get_desca() {
        $value = $this->get('desca');
        if ($value !== null) {
            $value = json_decode($value);
        }
        return $value;
    }

    /**
     * Convenience method to get the description.
     *
     * @return lang_string
     */
    public function get_description() {
        return new lang_string($this->get('descidentifier'), $this->get('desccomponent'), $this->get_desca());
    }

    /**
     * Convenience method to set the description $a.
     *
     * @param mixed $value
     * @return mixed
     */
    public function set_desca($value) {
        if ($value !== null) {
            if (!is_scalar($value) && !is_array($value) && !($value instanceof stdClass)) {
                throw new coding_exception('$a format not supported.');
            }
            $value = json_encode($value);
        }
        $this->set('desca', $value);
    }

    /**
     * Validate the description $a.
     *
     * @param string $value
     * @return true|lang_string
     */
    protected function validate_desca($value) {
        if ($value === null) {
            return true;
        }

        $desc = json_decode($value);
        if ($desc === null && json_last_error() !== JSON_ERROR_NONE) {
            return new lang_string('invaliddata', 'tool_lp');
        }

        return true;
    }

    /**
     * Validate the description identifier.
     *
     * Only validate string existence during create. If the string is removed later on we should
     * not prevent this model from being updated. Alternatively we could check if the string has
     * changed before performing the check but this overhead is not required for now.
     * An evidence should usually never be updated anyway.
     *
     * @param  string $value
     * @return true|lang_string
     */
    protected function validate_descidentifier($value) {
        if (!$this->get_id() && !get_string_manager()->string_exists($value, $this->get('desccomponent'))) {
            return new lang_string('invalidevidencedesc', 'tool_lp');
        }

        return true;
    }

    /**
     * Validate the grade.
     *
     * @param int $value The value.
     * @return true|lang_string
     */
    protected function validate_grade($value) {
        if ($value !== null && $value <= 0) {
            return new lang_string('invalidgrade', 'tool_lp');
        }

        return true;
    }

    /**
     * Validate the user competency.
     *
     * @param  int $value
     * @return true|lang_string
     */
    protected function validate_usercompetencyid($value) {
        if (!user_competency::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

}
