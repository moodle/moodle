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
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use context;
use context_user;
use lang_string;
use moodle_exception;
use stdClass;

/**
 * Evidence persistent class.
 *
 * @package    core_competency
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class evidence extends persistent {

    const TABLE = 'competency_evidence';

    /** Action logging. */
    const ACTION_LOG = 0;
    /** Action rating a competency when no rating is set. */
    const ACTION_COMPLETE = 2;
    /** Action rating a competency. */
    const ACTION_OVERRIDE = 3;

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
            'contextid' => array(
                'type' => PARAM_INT
            ),
            'action' => array(
                'type' => PARAM_INT,
                'choices' => array(self::ACTION_LOG, self::ACTION_COMPLETE, self::ACTION_OVERRIDE)
            ),
            'actionuserid' => array(
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
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
            ),
            'note' => array(
                'type' => PARAM_NOTAGS,
                'default' => null,
                'null' => NULL_ALLOWED
            )
        );
    }

    /**
     * Return the competency linked to this.
     *
     * @return competency
     */
    public function get_competency() {
        return user_competency::get_competency_by_usercompetencyid($this->get_usercompetencyid());
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
     * Convenience method handling moodle_urls.
     *
     * @param null|string|moodle_url $url The URL.
     */
    public function set_url($url) {
        if ($url instanceof \moodle_url) {
            $url = $url->out(false);
        }
        $this->set('url', $url);
    }

    /**
     * Validate the action user ID.
     *
     * @param  int $value A user ID.
     * @return true|lang_string
     */
    protected function validate_actionuserid($value) {
        if ($value !== null && !\core_user::is_real_user($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Validate the context ID.
     *
     * @param  int $value
     * @return true|lang_string
     */
    protected function validate_contextid($value) {
        try {
            context::instance_by_id($value);
        } catch (moodle_exception $e) {
            // That does not look good...
            return new lang_string('invaliddata', 'error');
        }
        return true;
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
            return new lang_string('invaliddata', 'error');
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
            return new lang_string('invalidevidencedesc', 'core_competency');
        }

        return true;
    }

    /**
     * Validate the grade.
     *
     * For performance reason we do not validate that the grade is a valid item of the
     * scale associated with the competency or framework.
     *
     * @param int $value The value.
     * @return true|lang_string
     */
    protected function validate_grade($value) {
        if ($value !== null && $value <= 0) {
            return new lang_string('invalidgrade', 'core_competency');
        }

        $action = $this->get('action');
        if ($value === null && $action == self::ACTION_COMPLETE) {
            return new lang_string('invalidgrade', 'core_competency');

        } else if ($value !== null && $action == self::ACTION_LOG) {
            return new lang_string('invalidgrade', 'core_competency');
        }

        if ($value !== null) {
            // TODO MDL-52243 Use a core method to validate the grade_scale item.
            // Check if grade exist in the scale item values.
            $competency = $this->get_competency();
            if (!array_key_exists($value - 1, $competency->get_scale()->scale_items)) {
                return new lang_string('invalidgrade', 'core_competency');
            }
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

    /**
     * Whether the current user can delete an evidence in the context of a user.
     *
     * @param int $userid The user ID the evidence belongs to.
     * @return bool
     */
    public static function can_delete_user($userid) {
        return has_capability('moodle/competency:evidencedelete', context_user::instance($userid));
    }

}
