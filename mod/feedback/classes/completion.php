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
 * Contains class mod_feedback_completion
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Collects information and methods about feedback completion (either complete.php or show_entries.php)
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_feedback_completion extends mod_feedback_structure {
    /** @var stdClass */
    protected $completed;
    /** @var stdClass */
    protected $completedtmp = null;
    /** @var stdClass[] */
    protected $valuestmp = null;
    /** @var stdClass[] */
    protected $values = null;
    /** @var bool */
    protected $iscompleted = false;
    /** @var mod_feedback_complete_form the form used for completing the feedback */
    protected $form = null;
    /** @var bool true when the feedback has been completed during the request */
    protected $justcompleted = false;
    /** @var int the next page the user should jump after processing the form */
    protected $jumpto = null;


    /**
     * Constructor
     *
     * @param stdClass $feedback feedback object
     * @param cm_info $cm course module object corresponding to the $feedback
     *     (at least one of $feedback or $cm is required)
     * @param int $courseid current course (for site feedbacks only)
     * @param bool $iscompleted has feedback been already completed? If yes either completedid or userid must be specified.
     * @param int $completedid id in the table feedback_completed, may be omitted if userid is specified
     *     but it is highly recommended because the same user may have multiple responses to the same feedback
     *     for different courses
     * @param int $nonanonymouseuserid - Return only anonymous results or specified user's results.
     *     If null only anonymous replies will be returned and the $completedid is mandatory.
     *     If specified only non-anonymous replies of $nonanonymouseuserid will be returned.
     * @param int $userid User id to use for all capability checks, etc. Set to 0 for current user (default).
     */
    public function __construct($feedback, $cm, $courseid, $iscompleted = false, $completedid = null,
                                $nonanonymouseuserid = null, $userid = 0) {
        global $DB;

        parent::__construct($feedback, $cm, $courseid, 0, $userid);
        // Make sure courseid is always set for site feedback.
        if ($this->feedback->course == SITEID && !$this->courseid) {
            $this->courseid = SITEID;
        }
        if ($iscompleted) {
            // Retrieve information about the completion.
            $this->iscompleted = true;
            $params = array('feedback' => $this->feedback->id);
            if (!$nonanonymouseuserid && !$completedid) {
                throw new coding_exception('Either $completedid or $nonanonymouseuserid must be specified for completed feedbacks');
            }
            if ($completedid) {
                $params['id'] = $completedid;
            }
            if ($nonanonymouseuserid) {
                // We must respect the anonymousity of the reply that the user saw when they were completing the feedback,
                // not the current state that may have been changed later by the teacher.
                $params['anonymous_response'] = FEEDBACK_ANONYMOUS_NO;
                $params['userid'] = $nonanonymouseuserid;
            }
            $this->completed = $DB->get_record('feedback_completed', $params, '*', MUST_EXIST);
            $this->courseid = $this->completed->courseid;
        }
    }

    /**
     * Returns a record from 'feedback_completed' table
     * @return stdClass
     */
    public function get_completed() {
        return $this->completed;
    }

    /**
     * Check if the feedback was just completed.
     *
     * @return bool true if the feedback was just completed.
     * @since  Moodle 3.3
     */
    public function just_completed() {
        return $this->justcompleted;
    }

    /**
     * Return the jumpto property.
     *
     * @return int the next page to jump.
     * @since  Moodle 3.3
     */
    public function get_jumpto() {
        return $this->jumpto;
    }

    /**
     * Returns the temporary completion record for the current user or guest session
     *
     * @return stdClass|false record from feedback_completedtmp or false if not found
     */
    public function get_current_completed_tmp() {
        global $DB, $USER;
        if ($this->completedtmp === null) {
            $params = array('feedback' => $this->get_feedback()->id);
            if ($courseid = $this->get_courseid()) {
                $params['courseid'] = $courseid;
            }
            if ((isloggedin() || $USER->id != $this->userid) && !isguestuser($this->userid)) {
                $params['userid'] = $this->userid;
            } else {
                $params['guestid'] = sesskey();
            }
            $this->completedtmp = $DB->get_record('feedback_completedtmp', $params);
        }
        return $this->completedtmp;
    }

    /**
     * Can the current user see the item, if dependency is met?
     *
     * @param stdClass $item
     * @return bool whether user can see item or not,
     *     true if there is no dependency or dependency is met,
     *     false if dependent question is visible or broken
     *        and further it is either not answered or the dependency is not met,
     *     null if dependency is broken.
     */
    protected function can_see_item($item) {
        if (empty($item->dependitem)) {
            return true;
        }
        if ($this->dependency_has_error($item)) {
            return null;
        }
        $allitems = $this->get_items();
        $ditem = $allitems[$item->dependitem];
        $itemobj = feedback_get_item_class($ditem->typ);
        if ($this->iscompleted) {
            $value = $this->get_values($ditem);
        } else {
            $value = $this->get_values_tmp($ditem);
        }
        if ($value === null) {
            // Cyclic dependencies are no problem here, since they will throw an dependency error above.
            if ($this->can_see_item($ditem) === false) {
                return false;
            }
            return null;
        }
        $check = $itemobj->compare_value($ditem, $value, $item->dependvalue) ? true : false;
        if ($check) {
            return $this->can_see_item($ditem);
        }
        return false;
    }

    /**
     * Dependency condition has an error
     * @param stdClass $item
     * @return bool
     */
    protected function dependency_has_error($item) {
        if (empty($item->dependitem)) {
            // No dependency - no error.
            return false;
        }
        $allitems = $this->get_items();
        if (!array_key_exists($item->dependitem, $allitems)) {
            // Looks like dependent item has been removed.
            return true;
        }
        $itemids = array_keys($allitems);
        $index1 = array_search($item->dependitem, $itemids);
        $index2 = array_search($item->id, $itemids);
        if ($index1 >= $index2) {
            // Dependent item is after the current item in the feedback.
            return true;
        }
        for ($i = $index1 + 1; $i < $index2; $i++) {
            if ($allitems[$itemids[$i]]->typ === 'pagebreak') {
                return false;
            }
        }
        // There are no page breaks between dependent items.
        return true;
    }

    /**
     * Returns a value stored for this item in the feedback (temporary or not, depending on the mode)
     * @param stdClass $item
     * @return string
     */
    public function get_item_value($item) {
        if ($this->iscompleted) {
            return $this->get_values($item);
        } else {
            return $this->get_values_tmp($item);
        }
    }

    /**
     * Retrieves responses from an unfinished attempt.
     *
     * @return array the responses (from the feedback_valuetmp table)
     * @since  Moodle 3.3
     */
    public function get_unfinished_responses() {
        global $DB;
        $responses = array();

        $completedtmp = $this->get_current_completed_tmp();
        if ($completedtmp) {
            $responses = $DB->get_records('feedback_valuetmp', ['completed' => $completedtmp->id]);
        }
        return $responses;
    }

    /**
     * Returns all temporary values for this feedback or just a value for an item
     * @param stdClass $item
     * @return array
     */
    protected function get_values_tmp($item = null) {
        global $DB;
        if ($this->valuestmp === null) {
            $this->valuestmp = array();
            $responses = $this->get_unfinished_responses();
            foreach ($responses as $r) {
                $this->valuestmp[$r->item] = $r->value;
            }
        }
        if ($item) {
            return array_key_exists($item->id, $this->valuestmp) ? $this->valuestmp[$item->id] : null;
        }
        return $this->valuestmp;
    }

    /**
     * Retrieves responses from an finished attempt.
     *
     * @return array the responses (from the feedback_value table)
     * @since  Moodle 3.3
     */
    public function get_finished_responses() {
        global $DB;
        $responses = array();

        if ($this->completed) {
            $responses = $DB->get_records('feedback_value', ['completed' => $this->completed->id]);
        }
        return $responses;
    }

    /**
     * Returns all completed values for this feedback or just a value for an item
     * @param stdClass $item
     * @return array
     */
    protected function get_values($item = null) {
        global $DB;
        if ($this->values === null) {
            $this->values = array();
            $responses = $this->get_finished_responses();
            foreach ($responses as $r) {
                $this->values[$r->item] = $r->value;
            }
        }
        if ($item) {
            return array_key_exists($item->id, $this->values) ? $this->values[$item->id] : null;
        }
        return $this->values;
    }

    /**
     * Splits the feedback items into pages
     *
     * Items that we definitely know at this stage as not applicable are excluded.
     * Items that are dependent on something that has not yet been answered are
     * still present, as well as items with broken dependencies.
     *
     * @return array array of arrays of items
     */
    public function get_pages() {
        $pages = [[]]; // The first page always exists.
        $items = $this->get_items();
        foreach ($items as $item) {
            if ($item->typ === 'pagebreak') {
                $pages[] = [];
            } else if ($this->can_see_item($item) !== false) {
                $pages[count($pages) - 1][] = $item;
            }
        }
        return $pages;
    }

    /**
     * Returns the last page that has items with the value (i.e. not label) which have been answered
     * as well as the first page that has items with the values that have not been answered.
     *
     * Either of the two return values may be null if there are no answered page or there are no
     * unanswered pages left respectively.
     *
     * Two pages may not be directly following each other because there may be empty pages
     * or pages with information texts only between them
     *
     * @return array array of two elements [$lastcompleted, $firstincompleted]
     */
    protected function get_last_completed_page() {
        $completed = [];
        $incompleted = [];
        $pages = $this->get_pages();
        foreach ($pages as $pageidx => $pageitems) {
            foreach ($pageitems as $item) {
                if ($item->hasvalue) {
                    if ($this->get_values_tmp($item) !== null) {
                        $completed[$pageidx] = true;
                    } else {
                        $incompleted[$pageidx] = true;
                    }
                }
            }
        }
        $completed = array_keys($completed);
        $incompleted = array_keys($incompleted);
        // If some page has both completed and incompleted items it is considered incompleted.
        $completed = array_diff($completed, $incompleted);
        // If the completed page follows an incompleted page, it does not count.
        $firstincompleted = $incompleted ? min($incompleted) : null;
        if ($firstincompleted !== null) {
            $completed = array_filter($completed, function($a) use ($firstincompleted) {
                return $a < $firstincompleted;
            });
        }
        $lastcompleted = $completed ? max($completed) : null;
        return [$lastcompleted, $firstincompleted];
    }

    /**
     * Get the next page for the feedback
     *
     * This is normally $gopage+1 but may be bigger if there are empty pages or
     * pages without visible questions.
     *
     * This method can only be called when questions on the current page are
     * already answered, otherwise it may be inaccurate.
     *
     * @param int $gopage current page
     * @param bool $strictcheck when gopage is the user-input value, make sure we do not jump over unanswered questions
     * @return int|null the index of the next page or null if this is the last page
     */
    public function get_next_page($gopage, $strictcheck = true) {
        if ($strictcheck) {
            list($lastcompleted, $firstincompleted) = $this->get_last_completed_page();
            if ($firstincompleted !== null && $firstincompleted <= $gopage) {
                return $firstincompleted;
            }
        }
        $pages = $this->get_pages();
        for ($pageidx = $gopage + 1; $pageidx < count($pages); $pageidx++) {
            if (!empty($pages[$pageidx])) {
                return $pageidx;
            }
        }
        // No further pages in the feedback have any visible items.
        return null;
    }

    /**
     * Get the previous page for the feedback
     *
     * This is normally $gopage-1 but may be smaller if there are empty pages or
     * pages without visible questions.
     *
     * @param int $gopage current page
     * @param bool $strictcheck when gopage is the user-input value, make sure we do not jump over unanswered questions
     * @return int|null the index of the next page or null if this is the first page with items
     */
    public function get_previous_page($gopage, $strictcheck = true) {
        if (!$gopage) {
            // If we are already on the first (0) page, there is definitely no previous page.
            return null;
        }
        $pages = $this->get_pages();
        $rv = null;
        // Iterate through previous pages and find the closest one that has any items on it.
        for ($pageidx = $gopage - 1; $pageidx >= 0; $pageidx--) {
            if (!empty($pages[$pageidx])) {
                $rv = $pageidx;
                break;
            }
        }
        if ($rv === null) {
            // We are on the very first page that has items.
            return null;
        }
        if ($rv > 0 && $strictcheck) {
            // Check if this page is actually not past than first incompleted page.
            list($lastcompleted, $firstincompleted) = $this->get_last_completed_page();
            if ($firstincompleted !== null && $firstincompleted < $rv) {
                return $firstincompleted;
            }
        }
        return $rv;
    }

    /**
     * Page index to resume the feedback
     *
     * When user abandones answering feedback and then comes back to it we should send him
     * to the first page after the last page he fully completed.
     * @return int
     */
    public function get_resume_page() {
        list($lastcompleted, $firstincompleted) = $this->get_last_completed_page();
        return $lastcompleted === null ? 0 : $this->get_next_page($lastcompleted, false);
    }

    /**
     * Creates a new record in the 'feedback_completedtmp' table for the current user/guest session
     *
     * @return stdClass record from feedback_completedtmp or false if not found
     */
    protected function create_current_completed_tmp() {
        global $DB, $USER;
        $record = (object)['feedback' => $this->feedback->id];
        if ($this->get_courseid()) {
            $record->courseid = $this->get_courseid();
        }
        if ((isloggedin() || $USER->id != $this->userid) && !isguestuser($this->userid)) {
            $record->userid = $this->userid;
        } else {
            $record->guestid = sesskey();
        }
        $record->timemodified = time();
        $record->anonymous_response = $this->feedback->anonymous;
        $id = $DB->insert_record('feedback_completedtmp', $record);
        $this->completedtmp = $DB->get_record('feedback_completedtmp', ['id' => $id]);
        $this->valuestmp = null;
        return $this->completedtmp;
    }

    /**
     * If user has already completed the feedback, create the temproray values from last completed attempt
     *
     * @return stdClass record from feedback_completedtmp or false if not found
     */
    public function create_completed_tmp_from_last_completed() {
        if (!$this->get_current_completed_tmp()) {
            $lastcompleted = $this->find_last_completed();
            if ($lastcompleted) {
                $this->completedtmp = feedback_set_tmp_values($lastcompleted);
            }
        }
        return $this->completedtmp;
    }

    /**
     * Saves unfinished response to the temporary table
     *
     * This is called when user proceeds to the next/previous page in the complete form
     * and also right after the form submit.
     * After the form submit the {@link save_response()} is called to
     * move response from temporary table to completion table.
     *
     * @param stdClass $data data from the form mod_feedback_complete_form
     */
    public function save_response_tmp($data) {
        global $DB;
        if (!$completedtmp = $this->get_current_completed_tmp()) {
            $completedtmp = $this->create_current_completed_tmp();
        } else {
            $currentime = time();
            $DB->update_record('feedback_completedtmp',
                    ['id' => $completedtmp->id, 'timemodified' => $currentime]);
            $completedtmp->timemodified = $currentime;
        }

        // Find all existing values.
        $existingvalues = $DB->get_records_menu('feedback_valuetmp',
                ['completed' => $completedtmp->id], '', 'item, id');

        // Loop through all feedback items and save the ones that are present in $data.
        $allitems = $this->get_items();
        foreach ($allitems as $item) {
            if (!$item->hasvalue) {
                continue;
            }
            $keyname = $item->typ . '_' . $item->id;
            if (!isset($data->$keyname)) {
                // This item is either on another page or dependency was not met - nothing to save.
                continue;
            }

            $newvalue = ['item' => $item->id, 'completed' => $completedtmp->id, 'course_id' => $completedtmp->courseid];

            // Convert the value to string that can be stored in 'feedback_valuetmp' or 'feedback_value'.
            $itemobj = feedback_get_item_class($item->typ);
            $newvalue['value'] = $itemobj->create_value($data->$keyname);

            // Update or insert the value in the 'feedback_valuetmp' table.
            if (array_key_exists($item->id, $existingvalues)) {
                $newvalue['id'] = $existingvalues[$item->id];
                $DB->update_record('feedback_valuetmp', $newvalue);
            } else {
                $DB->insert_record('feedback_valuetmp', $newvalue);
            }
        }

        // Reset valuestmp cache.
        $this->valuestmp = null;
    }

    /**
     * Saves the response
     *
     * The form data has already been stored in the temporary table in
     * {@link save_response_tmp()}. This function copies the values
     * from the temporary table to the completion table.
     * It is also responsible for sending email notifications when applicable.
     */
    public function save_response() {
        global $SESSION, $DB, $USER;

        $feedbackcompleted = $this->find_last_completed();
        $feedbackcompletedtmp = $this->get_current_completed_tmp();

        if (feedback_check_is_switchrole()) {
            // We do not actually save anything if the role is switched, just delete temporary values.
            $this->delete_completedtmp();
            return;
        }

        // Save values.
        $completedid = feedback_save_tmp_values($feedbackcompletedtmp, $feedbackcompleted);
        $this->completed = $DB->get_record('feedback_completed', array('id' => $completedid));

        // Send email.
        if ($this->feedback->anonymous == FEEDBACK_ANONYMOUS_NO) {
            feedback_send_email($this->cm, $this->feedback, $this->cm->get_course(), $this->userid, $this->completed);
        } else {
            feedback_send_email_anonym($this->cm, $this->feedback, $this->cm->get_course());
        }

        unset($SESSION->feedback->is_started);

        // Update completion state.
        $completion = new completion_info($this->cm->get_course());
        if ((isloggedin() || $USER->id != $this->userid) && $completion->is_enabled($this->cm) &&
                $this->cm->completion == COMPLETION_TRACKING_AUTOMATIC && $this->feedback->completionsubmit) {
            $completion->update_state($this->cm, COMPLETION_COMPLETE, $this->userid);
        }
    }

    /**
     * Deletes the temporary completed and all related temporary values
     */
    protected function delete_completedtmp() {
        global $DB;

        if ($completedtmp = $this->get_current_completed_tmp()) {
            $DB->delete_records('feedback_valuetmp', ['completed' => $completedtmp->id]);
            $DB->delete_records('feedback_completedtmp', ['id' => $completedtmp->id]);
            $this->completedtmp = null;
        }
    }

    /**
     * Retrieves the last completion record for the current user
     *
     * @return stdClass record from feedback_completed or false if not found
     */
    public function find_last_completed() {
        global $DB, $USER;
        if ((!isloggedin() && $USER->id == $this->userid) || isguestuser($this->userid)) {
            // Not possible to retrieve completed feedback for guests.
            return false;
        }
        if ($this->is_anonymous()) {
            // Not possible to retrieve completed anonymous feedback.
            return false;
        }
        $params = array('feedback' => $this->feedback->id,
            'userid' => $this->userid,
            'anonymous_response' => FEEDBACK_ANONYMOUS_NO
        );
        if ($this->get_courseid()) {
            $params['courseid'] = $this->get_courseid();
        }
        $this->completed = $DB->get_record('feedback_completed', $params);
        return $this->completed;
    }

    /**
     * Checks if user has capability to submit the feedback
     *
     * There is an exception for fully anonymous feedbacks when guests can complete
     * feedback without the proper capability.
     *
     * This should be followed by checking {@link can_submit()} because even if
     * user has capablity to complete, they may have already submitted feedback
     * and can not re-submit
     *
     * @return bool
     */
    public function can_complete() {
        global $CFG, $USER;

        $context = context_module::instance($this->cm->id);
        if (has_capability('mod/feedback:complete', $context, $this->userid)) {
            return true;
        }

        if (!empty($CFG->feedback_allowfullanonymous)
                    AND $this->feedback->course == SITEID
                    AND $this->feedback->anonymous == FEEDBACK_ANONYMOUS_YES
                    AND ((!isloggedin() && $USER->id == $this->userid) || isguestuser($this->userid))) {
            // Guests are allowed to complete fully anonymous feedback without having 'mod/feedback:complete' capability.
            return true;
        }

        return false;
    }

    /**
     * Checks if user is prevented from re-submission.
     *
     * This must be called after {@link can_complete()}
     *
     * @return bool
     */
    public function can_submit() {
        if ($this->get_feedback()->multiple_submit == 0 ) {
            if ($this->is_already_submitted()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Trigger module viewed event.
     *
     * @since Moodle 3.3
     */
    public function trigger_module_viewed() {
        $event = \mod_feedback\event\course_module_viewed::create_from_record($this->feedback, $this->cm, $this->cm->get_course());
        $event->trigger();
    }

    /**
     * Mark activity viewed for completion-tracking.
     *
     * @since Moodle 3.3
     */
    public function set_module_viewed() {
        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');

        $completion = new completion_info($this->cm->get_course());
        $completion->set_module_viewed($this->cm, $this->userid);
    }

    /**
     * Process a page jump via the mod_feedback_complete_form.
     *
     * This function initializes the form and process the submission.
     *
     * @param  int $gopage         the current page
     * @param  int $gopreviouspage if the user chose to go to the previous page
     * @return string the url to redirect the user (if any)
     * @since  Moodle 3.3
     */
    public function process_page($gopage, $gopreviouspage = false) {
        global $CFG, $PAGE, $SESSION;

        $urltogo = null;

        // Save the form for later during the request.
        $this->create_completed_tmp_from_last_completed();
        $this->form = new mod_feedback_complete_form(mod_feedback_complete_form::MODE_COMPLETE,
            $this, 'feedback_complete_form', array('gopage' => $gopage));

        if ($this->form->is_cancelled()) {
            // Form was cancelled - return to the course page.
            $urltogo = new moodle_url('/mod/feedback/view.php', ['id' => $this->get_cm()->id]);
        } else if ($this->form->is_submitted() &&
                ($this->form->is_validated() || $gopreviouspage)) {
            // Form was submitted (skip validation for "Previous page" button).
            $data = $this->form->get_submitted_data();
            if (!isset($SESSION->feedback->is_started) OR !$SESSION->feedback->is_started == true) {
                throw new \moodle_exception('error', '', $CFG->wwwroot.'/course/view.php?id='.$this->courseid);
            }
            $this->save_response_tmp($data);
            if (!empty($data->savevalues) || !empty($data->gonextpage)) {
                if (($nextpage = $this->get_next_page($gopage)) !== null) {
                    if ($PAGE->has_set_url()) {
                        $urltogo = new moodle_url($PAGE->url, array('gopage' => $nextpage));
                    }
                    $this->jumpto = $nextpage;
                } else {
                    $this->save_response();
                    if (!$this->get_feedback()->page_after_submit) {
                        \core\notification::success(get_string('entries_saved', 'feedback'));
                    }
                    $this->justcompleted = true;
                }
            } else if (!empty($gopreviouspage)) {
                $prevpage = intval($this->get_previous_page($gopage));
                if ($PAGE->has_set_url()) {
                    $urltogo = new moodle_url($PAGE->url, array('gopage' => $prevpage));
                }
                $this->jumpto = $prevpage;
            }
        }
        return $urltogo;
    }

    /**
     * Render the form with the questions.
     *
     * @return string the form rendered
     * @since Moodle 3.3
     */
    public function render_items() {
        global $SESSION;

        // Print the items.
        $SESSION->feedback->is_started = true;
        return $this->form->render();
    }
}