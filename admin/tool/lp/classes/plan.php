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
 * Class for plans persistence.
 *
 * @package    tool_lp
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

use context_user;
use dml_missing_record_exception;
use lang_string;

/**
 * Class for loading/storing plans from the DB.
 *
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan extends persistent {

    const TABLE = 'tool_lp_plan';

    /** Draft status */
    const STATUS_DRAFT = 0;

    /** Active status */
    const STATUS_ACTIVE = 1;

    /** Complete status */
    const STATUS_COMPLETE = 2;

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'name' => array(
                'type' => PARAM_TEXT,
            ),
            'description' => array(
                'type' => PARAM_TEXT,
                'default' => ''
            ),
            'descriptionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_HTML,
            ),
            'userid' => array(
                'type' => PARAM_INT,
            ),
            'templateid' => array(
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'status' => array(
                'choices' => array(self::STATUS_DRAFT, self::STATUS_COMPLETE, self::STATUS_ACTIVE),
                'type' => PARAM_INT,
                'default' => self::STATUS_DRAFT,
            ),
            'duedate' => array(
                'type' => PARAM_INT,
                'default' => 0,
            ),
        );
    }

    /**
     * Whether the current user can update the learning plan.
     *
     * @return bool|null
     */
    public function can_update() {
        global $USER;

        // Null if the record has not been filled.
        if (!$userid = $this->get_userid()) {
            return null;
        }

        $context = context_user::instance($userid);

        // Not all users can edit all plans, the template should know about it.
        if (has_capability('tool/lp:planmanageall', $context) ||
                has_capability('tool/lp:planmanageown', $context)) {
            return true;
        }

        // The user that created the template can also edit it if he was the last one that modified it. But
        // can't do it if it is already completed.
        if ($USER->id == $userid && $this->get_usermodified() == $USER->id && $this->get_status() != self::STATUS_COMPLETE) {
            return true;
        }

        return false;
    }

    /**
     * Human readable status name.
     *
     * @return string
     */
    public function get_statusname() {

        $status = $this->get_status();

        switch ($status) {
            case self::STATUS_DRAFT:
                $strname = 'draft';
                break;
            case self::STATUS_ACTIVE:
                $strname = 'active';
                break;
            case self::STATUS_COMPLETE:
                $strname = 'complete';
                break;
            default:
                throw new \moodle_exception('errorplanstatus', 'tool_lp', '', $status);
                break;
        }

        return get_string('planstatus' . $strname, 'tool_lp');
    }

    /**
     * Validate the template ID.
     *
     * @param mixed $value The value.
     * @return true|lang_string
     */
    protected function validate_templateid($value) {

        // Checks that the template exists.
        if (!empty($value) && !template::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }

        return true;
    }

}
