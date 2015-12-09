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
defined('MOODLE_INTERNAL') || die();

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
            'origtemplateid' => array(
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
     * Whether the current user can manage the plan.
     *
     * @return bool
     */
    public function can_manage() {
        if ($this->get_status() == self::STATUS_DRAFT) {
            return self::can_manage_user_draft($this->get_userid());
        }
        return self::can_manage_user($this->get_userid());
    }

    /**
     * Whether the current user can read the plan.
     *
     * @return bool
     */
    public function can_read() {
        if ($this->get_status() == self::STATUS_DRAFT) {
            return self::can_read_user_draft($this->get_userid());
        }
        return self::can_read_user($this->get_userid());
    }

    /**
     * Get the competencies in this plan.
     *
     * @return competency[]
     */
    public function get_competencies() {
        $competencies = array();
        if ($this->get_templateid()) {
            // Get the competencies from the template.
            $competencies = template_competency::list_competencies($this->get_templateid(), true);
        } else {
            // Get the competencies from the plan.
            $competencies = plan_competency::list_competencies($this->get_id());
        }
        return $competencies;
    }

    /**
     * Get the context in which the plan is attached.
     *
     * @return context_user
     */
    public function get_context() {
        return context_user::instance($this->get_userid());
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
     * Get the plan template.
     *
     * @return template|null
     */
    public function get_template() {
        $templateid = $this->get_templateid();
        if ($templateid === null) {
            return null;
        }
        return new template($templateid);
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

    /**
     * Validate the user ID.
     *
     * @param  int $value
     * @return true|lang_string
     */
    protected function validate_userid($value) {
        global $DB;

        // During create.
        if (!$this->get_id()) {

            // Check that the user exists. We do not need to do that on update because
            // the userid of a plan should never change.
            if (!$DB->record_exists('user', array('id' => $value))) {
                return new lang_string('invaliddata', 'error');
            }

        }

        return true;
    }

    /**
     * Can the current user manage a user's plan?
     *
     * @param  int $planuserid The user to whom the plan would belong.
     * @return bool
     */
    public static function can_manage_user($planuserid) {
        global $USER;
        $context = context_user::instance($planuserid);

        $capabilities = array('tool/lp:planmanage');
        if ($context->instanceid == $USER->id) {
            $capabilities[] = 'tool/lp:planmanageown';
        }

        return has_any_capability($capabilities, $context);
    }

    /**
     * Can the current user manage a user's draft plan?
     *
     * @param  int $planuserid The user to whom the plan would belong.
     * @return bool
     */
    public static function can_manage_user_draft($planuserid) {
        global $USER;
        $context = context_user::instance($planuserid);

        $capabilities = array('tool/lp:planmanagedraft');
        if ($context->instanceid == $USER->id) {
            $capabilities[] = 'tool/lp:planmanageowndraft';
        }

        return has_any_capability($capabilities, $context);
    }

    /**
     * Can the current user view a user's plan?
     *
     * @param  int $planuserid The user to whom the plan would belong.
     * @return bool
     */
    public static function can_read_user($planuserid) {
        global $USER;
        $context = context_user::instance($planuserid);

        $capabilities = array('tool/lp:planview');
        if ($context->instanceid == $USER->id) {
            $capabilities[] = 'tool/lp:planviewown';
        }

        return has_any_capability($capabilities, $context)
            || self::can_manage_user($planuserid);
    }

    /**
     * Can the current user view a user's draft plan?
     *
     * @param  int $planuserid The user to whom the plan would belong.
     * @return bool
     */
    public static function can_read_user_draft($planuserid) {
        global $USER;
        $context = context_user::instance($planuserid);

        $capabilities = array('tool/lp:planviewdraft');
        if ($context->instanceid == $USER->id) {
            $capabilities[] = 'tool/lp:planviewowndraft';
        }

        return has_any_capability($capabilities, $context)
            || self::can_manage_user_draft($planuserid);
    }

    /**
     * Get the recordset of the plans that are due and incomplete.
     *
     * @return \moodle_recordset
     */
    public static function get_recordset_for_due_and_incomplete() {
        global $DB;
        $sql = "duedate > 0 AND duedate < :now AND status != :status";
        $params = array('now' => time(), 'status' => self::STATUS_COMPLETE);
        return $DB->get_recordset_select(self::TABLE, $sql, $params);
    }

    /**
     * Return a list of status depending on capabilities.
     *
     * @param  int $userid The user to whom the plan would belong.
     * @return array
     */
    public static function get_status_list($userid) {
        $status = array();
        if (self::can_manage_user_draft($userid)) {
            $status[self::STATUS_DRAFT] = get_string('planstatusdraft', 'tool_lp');
        }
        if (self::can_manage_user($userid)) {
            $status[self::STATUS_ACTIVE] = get_string('planstatusactive', 'tool_lp');
        }
        return $status;
    }

    /**
     * Update from template.
     *
     * Bulk update a lot of plans from a template
     *
     * @param  template $template
     * @return bool
     */
    public static function update_multiple_from_template(template $template) {
        global $DB;
        if (!$template->is_valid()) {
            // As we will bypass this model's validation we rely on the template being validated.
            throw new coding_exception('The template must be validated before updating plans.');
        }

        $params = array(
            'templateid' => $template->get_id(),
            'status' => self::STATUS_COMPLETE,

            'name' => $template->get_shortname(),
            'description' => $template->get_description(),
            'descriptionformat' => $template->get_descriptionformat(),
            'duedate' => $template->get_duedate(),
        );

        $sql = "UPDATE {" . self::TABLE . "}
                   SET name = :name,
                       description = :description,
                       descriptionformat = :descriptionformat,
                       duedate = :duedate
                 WHERE templateid = :templateid
                   AND status != :status";

        return $DB->execute($sql, $params);
    }

    /**
     * Check if a template is associated to the plan.
     *
     * @return bool
     */
    public function is_based_on_template() {
        return $this->get_templateid() !== null;
    }

    /**
     * Check if plan can be edited.
     *
     * @return bool
     */
    public function can_be_edited() {
        return !$this->is_based_on_template() && $this->get_status() != self::STATUS_COMPLETE && $this->can_manage();
    }
}
