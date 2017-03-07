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
 * @package    core_competency
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use comment;
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

    const TABLE = 'competency_plan';

    /** Draft status. */
    const STATUS_DRAFT = 0;

    /** Active status. */
    const STATUS_ACTIVE = 1;

    /** Complete status. */
    const STATUS_COMPLETE = 2;

    /** Waiting for review. */
    const STATUS_WAITING_FOR_REVIEW = 3;

    /** In review. */
    const STATUS_IN_REVIEW = 4;

    /** 10 minutes threshold **/
    const DUEDATE_THRESHOLD = 600;

    /** @var plan object before update. */
    protected $beforeupdate = null;

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
                'type' => PARAM_CLEANHTML,
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
                'choices' => array(self::STATUS_DRAFT, self::STATUS_COMPLETE, self::STATUS_ACTIVE,
                    self::STATUS_WAITING_FOR_REVIEW, self::STATUS_IN_REVIEW),
                'type' => PARAM_INT,
                'default' => self::STATUS_DRAFT,
            ),
            'duedate' => array(
                'type' => PARAM_INT,
                'default' => 0,
            ),
            'reviewerid' => array(
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            )
        );
    }

    /**
     * Hook to execute before validate.
     *
     * @return void
     */
    protected function before_validate() {
        $this->beforeupdate = null;

        // During update.
        if ($this->get_id()) {
            $this->beforeupdate = new self($this->get_id());
        }
    }

    /**
     * Whether the current user can comment on this plan.
     *
     * @return bool
     */
    public function can_comment() {
        return static::can_comment_user($this->get_userid());
    }

    /**
     * Whether the current user can manage the plan.
     *
     * @return bool
     */
    public function can_manage() {
        if ($this->is_draft()) {
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
        if ($this->is_draft()) {
            return self::can_read_user_draft($this->get_userid());
        }
        return self::can_read_user($this->get_userid());
    }

    /**
     * Whether the current user can read comments on this plan.
     *
     * @return bool
     */
    public function can_read_comments() {
        return $this->can_read();
    }

    /**
     * Whether the current user can request a review of the plan.
     *
     * @return bool
     */
    public function can_request_review() {
        return self::can_request_review_user($this->get_userid());
    }

    /**
     * Whether the current user can review the plan.
     *
     * @return bool
     */
    public function can_review() {
        return self::can_review_user($this->get_userid());
    }

    /**
     * Get the comment object.
     *
     * @return comment
     */
    public function get_comment_object() {
        global $CFG;
        require_once($CFG->dirroot . '/comment/lib.php');

        if (!$this->get_id()) {
            throw new \coding_exception('The plan must exist.');
        }

        $comment = new comment((object) array(
            'client_id' => 'plancommentarea' . $this->get_id(),
            'context' => $this->get_context(),
            'component' => 'competency',    // This cannot be named 'core_competency'.
            'itemid' => $this->get_id(),
            'area' => 'plan',
            'showcount' => true,
        ));
        $comment->set_fullwidth(true);
        return $comment;
    }

    /**
     * Get the competencies in this plan.
     *
     * @return competency[]
     */
    public function get_competencies() {
        $competencies = array();

        if ($this->get_status() == self::STATUS_COMPLETE) {
            // Get the competencies from the archive of the plan.
            $competencies = user_competency_plan::list_competencies($this->get_id(), $this->get_userid());
        } else if ($this->is_based_on_template()) {
            // Get the competencies from the template.
            $competencies = template_competency::list_competencies($this->get_templateid());
        } else {
            // Get the competencies from the plan.
            $competencies = plan_competency::list_competencies($this->get_id());
        }

        return $competencies;
    }

    /**
     * Get a single competency from this plan.
     *
     * This will throw an exception if the competency does not belong to the plan.
     *
     * @param int $competencyid The competency ID.
     * @return competency
     */
    public function get_competency($competencyid) {
        $competency = null;

        if ($this->get_status() == self::STATUS_COMPLETE) {
            // Get the competency from the archive of the plan.
            $competency = user_competency_plan::get_competency_by_planid($this->get_id(), $competencyid);
        } else if ($this->is_based_on_template()) {
            // Get the competency from the template.
            $competency = template_competency::get_competency($this->get_templateid(), $competencyid);
        } else {
            // Get the competency from the plan.
            $competency = plan_competency::get_competency($this->get_id(), $competencyid);
        }
        return $competency;
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
            case self::STATUS_IN_REVIEW:
                $strname = 'inreview';
                break;
            case self::STATUS_WAITING_FOR_REVIEW:
                $strname = 'waitingforreview';
                break;
            case self::STATUS_ACTIVE:
                $strname = 'active';
                break;
            case self::STATUS_COMPLETE:
                $strname = 'complete';
                break;
            default:
                throw new \moodle_exception('errorplanstatus', 'core_competency', '', $status);
                break;
        }

        return get_string('planstatus' . $strname, 'core_competency');
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
     * Is the plan in draft mode?
     *
     * This method is convenient to know if the plan is a draft because whilst a draft
     * is being reviewed its status is not "draft" any more, but it still is a draft nonetheless.
     *
     * @return boolean
     */
    public function is_draft() {
        return in_array($this->get_status(), static::get_draft_statuses());
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
     * Can the current user comment on a user's plan?
     *
     * @param int $planuserid The user ID the plan belongs to.
     * @return bool
     */
    public static function can_comment_user($planuserid) {
        global $USER;

        $capabilities = array('moodle/competency:plancomment');
        if ($USER->id == $planuserid) {
            $capabilities[] = 'moodle/competency:plancommentown';
        }

        return has_any_capability($capabilities, context_user::instance($planuserid));
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

        $capabilities = array('moodle/competency:planmanage');
        if ($context->instanceid == $USER->id) {
            $capabilities[] = 'moodle/competency:planmanageown';
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

        $capabilities = array('moodle/competency:planmanagedraft');
        if ($context->instanceid == $USER->id) {
            $capabilities[] = 'moodle/competency:planmanageowndraft';
        }

        return has_any_capability($capabilities, $context);
    }

    /**
     * Can the current user read the comments on a user's plan?
     *
     * @param int $planuserid The user ID the plan belongs to.
     * @return bool
     */
    public static function can_read_comments_user($planuserid) {
        // Everyone who can read the plan can read the comments.
        return static::can_read_user($planuserid);
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

        $capabilities = array('moodle/competency:planview');
        if ($context->instanceid == $USER->id) {
            $capabilities[] = 'moodle/competency:planviewown';
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

        $capabilities = array('moodle/competency:planviewdraft');
        if ($context->instanceid == $USER->id) {
            $capabilities[] = 'moodle/competency:planviewowndraft';
        }

        return has_any_capability($capabilities, $context)
            || self::can_manage_user_draft($planuserid);
    }

    /**
     * Can the current user request the draft to be reviewed.
     *
     * @param  int $planuserid The user to whom the plan would belong.
     * @return bool
     */
    public static function can_request_review_user($planuserid) {
        global $USER;

        $capabilities = array('moodle/competency:planrequestreview');
        if ($USER->id == $planuserid) {
            $capabilities[] = 'moodle/competency:planrequestreviewown';
        }

        return has_any_capability($capabilities, context_user::instance($planuserid));
    }

    /**
     * Can the current user review the plan.
     *
     * This means being able to send the plan from draft to active, and vice versa.
     *
     * @param  int $planuserid The user to whom the plan would belong.
     * @return bool
     */
    public static function can_review_user($planuserid) {
        return has_capability('moodle/competency:planreview', context_user::instance($planuserid))
            || self::can_manage_user($planuserid);
    }

    /**
     * Get the plans of a user containing a specific competency.
     *
     * @param  int $userid       The user ID.
     * @param  int $competencyid The competency ID.
     * @return plans[]
     */
    public static function get_by_user_and_competency($userid, $competencyid) {
        global $DB;

        $sql = 'SELECT p.*
                  FROM {' . self::TABLE . '} p
             LEFT JOIN {' . plan_competency::TABLE . '} pc
                    ON pc.planid = p.id
                   AND pc.competencyid = :competencyid1
             LEFT JOIN {' . user_competency_plan::TABLE . '} ucp
                    ON ucp.planid = p.id
                   AND ucp.competencyid = :competencyid2
             LEFT JOIN {' . template_competency::TABLE . '} tc
                    ON tc.templateid = p.templateid
                   AND tc.competencyid = :competencyid3
                 WHERE p.userid = :userid
                   AND (pc.id IS NOT NULL
                    OR ucp.id IS NOT NULL
                    OR tc.id IS NOT NULL)
              ORDER BY p.id ASC';

        $params = array(
            'competencyid1' => $competencyid,
            'competencyid2' => $competencyid,
            'competencyid3' => $competencyid,
            'userid' => $userid
        );

        $plans = array();
        $records = $DB->get_records_sql($sql, $params);
        foreach ($records as $record) {
            $plans[$record->id] = new plan(0, $record);
        }

        return $plans;
    }

    /**
     * Get the list of draft statuses.
     *
     * @return array Contains the status constants.
     */
    public static function get_draft_statuses() {
        return array(self::STATUS_DRAFT, self::STATUS_WAITING_FOR_REVIEW, self::STATUS_IN_REVIEW);
    }

    /**
     * Get the recordset of the plans that are due, incomplete and not draft.
     *
     * @return \moodle_recordset
     */
    public static function get_recordset_for_due_and_incomplete() {
        global $DB;
        $sql = "duedate > 0 AND duedate < :now AND status = :status";
        $params = array('now' => time(), 'status' => self::STATUS_ACTIVE);
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
            $status[self::STATUS_DRAFT] = get_string('planstatusdraft', 'core_competency');
        }
        if (self::can_manage_user($userid)) {
            $status[self::STATUS_ACTIVE] = get_string('planstatusactive', 'core_competency');
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
            throw new \coding_exception('The template must be validated before updating plans.');
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

    /**
     * Validate the due date.
     * When setting a due date it must not exceed the DUEDATE_THRESHOLD.
     *
     * @param  int $value The due date.
     * @return bool|lang_string
     */
    protected function validate_duedate($value) {

        // We do not check duedate when plan is draft, complete, unset, or based on a template.
        if ($this->is_based_on_template()
                || $this->is_draft()
                || $this->get_status() == self::STATUS_COMPLETE
                || empty($value)) {
            return true;
        }

        // During update.
        if ($this->get_id()) {
            $before = $this->beforeupdate->get_duedate();
            $beforestatus = $this->beforeupdate->get_status();

            // The value has not changed, then it's always OK. Though if we're going
            // from draft to active it has to has to be validated.
            if ($before == $value && !in_array($beforestatus, self::get_draft_statuses())) {
                return true;
            }
        }

        if ($value <= time()) {
            // We cannot set the date in the past.
            return new lang_string('errorcannotsetduedateinthepast', 'core_competency');
        }

        if ($value <= time() + self::DUEDATE_THRESHOLD) {
            // We cannot set the date too soon, but we can leave it empty.
            return new lang_string('errorcannotsetduedatetoosoon', 'core_competency');
        }

        return true;
    }

    /**
     * Checks if a template has user plan records.
     *
     * @param  int $templateid The template ID
     * @return boolean
     */
    public static function has_records_for_template($templateid) {
        return self::record_exists_select('templateid = ?', array($templateid));
    }

    /**
     * Count the number of plans for a template, optionally filtering by status.
     *
     * @param  int $templateid The template ID
     * @param  int $status The plan status. 0 means all statuses.
     * @return int
     */
    public static function count_records_for_template($templateid, $status) {
        $filters = array('templateid' => $templateid);
        if ($status > 0) {
            $filters['status'] = $status;
        }
        return self::count_records($filters);
    }

    /**
     * Get the plans for a template, optionally filtering by status.
     *
     * @param  int $templateid The template ID
     * @param  int $status The plan status. 0 means all statuses.
     * @param  int $skip The number of plans to skip
     * @param  int $limit The max number of plans to return
     * @return int
     */
    public static function get_records_for_template($templateid, $status = 0, $skip = 0, $limit = 100) {
        $filters = array('templateid' => $templateid);
        if ($status > 0) {
            $filters['status'] = $status;
        }
        return self::get_records($filters, $skip, $limit);
    }
}
