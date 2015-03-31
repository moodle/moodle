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
 * Defines the \mod_quiz\structure class.
 *
 * @package   mod_quiz
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quiz;
defined('MOODLE_INTERNAL') || die();

/**
 * Quiz structure class.
 *
 * The structure of the quiz. That is, which questions it is built up
 * from. This is used on the Edit quiz page (edit.php) and also when
 * starting an attempt at the quiz (startattempt.php). Once an attempt
 * has been started, then the attempt holds the specific set of questions
 * that that student should answer, and we no longer use this class.
 *
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class structure {
    /** @var \quiz the quiz this is the structure of. */
    protected $quizobj = null;

    /**
     * @var \stdClass[] the questions in this quiz. Contains the row from the questions
     * table, with the data from the quiz_slots table added, and also question_categories.contextid.
     */
    protected $questions = array();

    /** @var \stdClass[] quiz_slots.id => the quiz_slots rows for this quiz, agumented by sectionid. */
    protected $slots = array();

    /** @var \stdClass[] quiz_slots.slot => the quiz_slots rows for this quiz, agumented by sectionid. */
    protected $slotsinorder = array();

    /**
     * @var \stdClass[] currently a dummy. Holds data that will match the
     * quiz_sections, once it exists.
     */
    protected $sections = array();

    /** @var bool caches the results of can_be_edited. */
    protected $canbeedited = null;

    /**
     * Create an instance of this class representing an empty quiz.
     * @return structure
     */
    public static function create() {
        return new self();
    }

    /**
     * Create an instance of this class representing the structure of a given quiz.
     * @param \quiz $quizobj the quiz.
     * @return structure
     */
    public static function create_for_quiz($quizobj) {
        $structure = self::create();
        $structure->quizobj = $quizobj;
        $structure->populate_structure($quizobj->get_quiz());
        return $structure;
    }

    /**
     * Whether there are any questions in the quiz.
     * @return bool true if there is at least one question in the quiz.
     */
    public function has_questions() {
        return !empty($this->questions);
    }

    /**
     * Get the number of questions in the quiz.
     * @return int the number of questions in the quiz.
     */
    public function get_question_count() {
        return count($this->questions);
    }

    /**
     * Get the information about the question with this id.
     * @param int $questionid The question id.
     * @return \stdClass the data from the questions table, augmented with
     * question_category.contextid, and the quiz_slots data for the question in this quiz.
     */
    public function get_question_by_id($questionid) {
        return $this->questions[$questionid];
    }

    /**
     * Get the information about the question in a given slot.
     * @param int $slotnumber the index of the slot in question.
     * @return \stdClass the data from the questions table, augmented with
     * question_category.contextid, and the quiz_slots data for the question in this quiz.
     */
    public function get_question_in_slot($slotnumber) {
        return $this->questions[$this->slotsinorder[$slotnumber]->questionid];
    }

    /**
     * Get the displayed question number (or 'i') for a given slot.
     * @param int $slotnumber the index of the slot in question.
     * @return string the question number ot display for this slot.
     */
    public function get_displayed_number_for_slot($slotnumber) {
        return $this->slotsinorder[$slotnumber]->displayednumber;
    }

    /**
     * Get the page a given slot is on.
     * @param int $slotnumber the index of the slot in question.
     * @return int the page number of the page that slot is on.
     */
    public function get_page_number_for_slot($slotnumber) {
        return $this->slotsinorder[$slotnumber]->page;
    }

    /**
     * Get the slot id of a given slot slot.
     * @param int $slotnumber the index of the slot in question.
     * @return int the page number of the page that slot is on.
     */
    public function get_slot_id_for_slot($slotnumber) {
        return $this->slotsinorder[$slotnumber]->id;
    }

    /**
     * Get the question type in a given slot.
     * @param int $slotnumber the index of the slot in question.
     * @return string the question type (e.g. multichoice).
     */
    public function get_question_type_for_slot($slotnumber) {
        return $this->questions[$this->slotsinorder[$slotnumber]->questionid]->qtype;
    }

    /**
     * Whether it would be possible, given the question types, etc. for the
     * question in the given slot to require that the previous question had been
     * answered before this one is displayed.
     * @param int $slotnumber the index of the slot in question.
     * @return bool can this question require the previous one.
     */
    public function can_question_depend_on_previous_slot($slotnumber) {
        return $slotnumber > 1 && $this->can_finish_during_the_attempt($slotnumber - 1);
    }

    /**
     * Whether it is possible for another question to depend on this one finishing.
     * Note that the answer is not exact, because of random questions, and sometimes
     * questions cannot be depended upon because of quiz options.
     * @param int $slotnumber the index of the slot in question.
     * @return bool can this question finish naturally during the attempt?
     */
    public function can_finish_during_the_attempt($slotnumber) {
        if ($this->quizobj->get_quiz()->shufflequestions ||
                $this->quizobj->get_navigation_method() == QUIZ_NAVMETHOD_SEQ) {
            return false;
        }

        if ($this->get_question_type_for_slot($slotnumber) == 'random') {
            return true;
        }

        if (isset($this->slotsinorder[$slotnumber]->canfinish)) {
            return $this->slotsinorder[$slotnumber]->canfinish;
        }

        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $this->quizobj->get_context());
        $tempslot = $quba->add_question(\question_bank::load_question(
                $this->slotsinorder[$slotnumber]->questionid));
        $quba->set_preferred_behaviour($this->quizobj->get_quiz()->preferredbehaviour);
        $quba->start_all_questions();

        $this->slotsinorder[$slotnumber]->canfinish = $quba->can_question_finish_during_attempt($tempslot);
        return $this->slotsinorder[$slotnumber]->canfinish;
    }

    /**
     * Whether it would be possible, given the question types, etc. for the
     * question in the given slot to require that the previous question had been
     * answered before this one is displayed.
     * @param int $slotnumber the index of the slot in question.
     * @return bool can this question require the previous one.
     */
    public function is_question_dependent_on_previous_slot($slotnumber) {
        return $this->slotsinorder[$slotnumber]->requireprevious;
    }

    /**
     * Is a particular question in this attempt a real question, or something like a description.
     * @param int $slotnumber the index of the slot in question.
     * @return bool whether that question is a real question.
     */
    public function is_real_question($slotnumber) {
        return $this->get_question_in_slot($slotnumber)->length != 0;
    }

    /**
     * Get the course id that the quiz belongs to.
     * @return int the course.id for the quiz.
     */
    public function get_courseid() {
        return $this->quizobj->get_courseid();
    }

    /**
     * Get the course module id of the quiz.
     * @return int the course_modules.id for the quiz.
     */
    public function get_cmid() {
        return $this->quizobj->get_cmid();
    }

    /**
     * Get id of the quiz.
     * @return int the quiz.id for the quiz.
     */
    public function get_quizid() {
        return $this->quizobj->get_quizid();
    }

    /**
     * Get the quiz object.
     * @return \stdClass the quiz settings row from the database.
     */
    public function get_quiz() {
        return $this->quizobj->get_quiz();
    }

    /**
     * Whether the question in the quiz are shuffled for each attempt.
     * @return bool true if the questions are shuffled.
     */
    public function is_shuffled() {
        return $this->quizobj->get_quiz()->shufflequestions;
    }

    /**
     * Quizzes can only be repaginated if they have not been attempted, the
     * questions are not shuffled, and there are two or more questions.
     * @return bool whether this quiz can be repaginated.
     */
    public function can_be_repaginated() {
        return !$this->is_shuffled() && $this->can_be_edited()
                && $this->get_question_count() >= 2;
    }

    /**
     * Quizzes can only be edited if they have not been attempted.
     * @return bool whether the quiz can be edited.
     */
    public function can_be_edited() {
        if ($this->canbeedited === null) {
            $this->canbeedited = !quiz_has_attempts($this->quizobj->get_quizid());
        }
        return $this->canbeedited;
    }

    /**
     * This quiz can only be edited if they have not been attempted.
     * Throw an exception if this is not the case.
     */
    public function check_can_be_edited() {
        if (!$this->can_be_edited()) {
            $reportlink = quiz_attempt_summary_link_to_reports($this->get_quiz(),
                    $this->quizobj->get_cm(), $this->quizobj->get_context());
            throw new \moodle_exception('cannoteditafterattempts', 'quiz',
                    new \moodle_url('/mod/quiz/edit.php', array('cmid' => $this->get_cmid())), $reportlink);
        }
    }

    /**
     * How many questions are allowed per page in the quiz.
     * This setting controls how frequently extra page-breaks should be inserted
     * automatically when questions are added to the quiz.
     * @return int the number of questions that should be on each page of the
     * quiz by default.
     */
    public function get_questions_per_page() {
        return $this->quizobj->get_quiz()->questionsperpage;
    }

    /**
     * Get quiz slots.
     * @return \stdClass[] the slots in this quiz.
     */
    public function get_slots() {
        return $this->slots;
    }

    /**
     * Is this slot the first one on its page?
     * @param int $slotnumber the index of the slot in question.
     * @return bool whether this slot the first one on its page.
     */
    public function is_first_slot_on_page($slotnumber) {
        if ($slotnumber == 1) {
            return true;
        }
        return $this->slotsinorder[$slotnumber]->page != $this->slotsinorder[$slotnumber - 1]->page;
    }

    /**
     * Is this slot the last one on its page?
     * @param int $slotnumber the index of the slot in question.
     * @return bool whether this slot the last one on its page.
     */
    public function is_last_slot_on_page($slotnumber) {
        if (!isset($this->slotsinorder[$slotnumber + 1])) {
            return true;
        }
        return $this->slotsinorder[$slotnumber]->page != $this->slotsinorder[$slotnumber + 1]->page;
    }

    /**
     * Is this slot the last one in the quiz?
     * @param int $slotnumber the index of the slot in question.
     * @return bool whether this slot the last one in the quiz.
     */
    public function is_last_slot_in_quiz($slotnumber) {
        end($this->slotsinorder);
        return $slotnumber == key($this->slotsinorder);
    }

    /**
     * Get the final slot in the quiz.
     * @return \stdClass the quiz_slots for for the final slot in the quiz.
     */
    public function get_last_slot() {
        return end($this->slotsinorder);
    }

    /**
     * Get a slot by it's id. Throws an exception if it is missing.
     * @param int $slotid the slot id.
     * @return \stdClass the requested quiz_slots row.
     */
    public function get_slot_by_id($slotid) {
        if (!array_key_exists($slotid, $this->slots)) {
            throw new \coding_exception('The \'slotid\' could not be found.');
        }
        return $this->slots[$slotid];
    }

    /**
     * Get all the slots in a section of the quiz.
     * @param int $sectionid the section id.
     * @return int[] slot numbers.
     */
    public function get_slots_in_section($sectionid) {
        $slots = array();
        foreach ($this->slotsinorder as $slot) {
            if ($slot->sectionid == $sectionid) {
                $slots[] = $slot->slot;
            }
        }
        return $slots;
    }

    /**
     * Get all the sections of the quiz.
     * @return \stdClass[] the sections in this quiz.
     */
    public function get_quiz_sections() {
        return $this->sections;
    }

    /**
     * Get the overall quiz grade formatted for display.
     * @return string the maximum grade for this quiz.
     */
    public function formatted_quiz_grade() {
        return quiz_format_grade($this->get_quiz(), $this->get_quiz()->grade);
    }

    /**
     * Get the maximum mark for a question, formatted for display.
     * @param int $slotnumber the index of the slot in question.
     * @return string the maximum mark for the question in this slot.
     */
    public function formatted_question_grade($slotnumber) {
        return quiz_format_question_grade($this->get_quiz(), $this->slotsinorder[$slotnumber]->maxmark);
    }

    /**
     * Get the number of decimal places for displyaing overall quiz grades or marks.
     * @return int the number of decimal places.
     */
    public function get_decimal_places_for_grades() {
        return $this->get_quiz()->decimalpoints;
    }

    /**
     * Get the number of decimal places for displyaing question marks.
     * @return int the number of decimal places.
     */
    public function get_decimal_places_for_question_marks() {
        return quiz_get_grade_format($this->get_quiz());
    }

    /**
     * Get any warnings to show at the top of the edit page.
     * @return string[] array of strings.
     */
    public function get_edit_page_warnings() {
        $warnings = array();

        if (quiz_has_attempts($this->quizobj->get_quizid())) {
            $reviewlink = quiz_attempt_summary_link_to_reports($this->quizobj->get_quiz(),
                    $this->quizobj->get_cm(), $this->quizobj->get_context());
            $warnings[] = get_string('cannoteditafterattempts', 'quiz', $reviewlink);
        }

        if ($this->is_shuffled()) {
            $updateurl = new \moodle_url('/course/mod.php',
                    array('return' => 'true', 'update' => $this->quizobj->get_cmid(), 'sesskey' => sesskey()));
            $updatelink = '<a href="'.$updateurl->out().'">' . get_string('updatethis', '',
                    get_string('modulename', 'quiz')) . '</a>';
            $warnings[] = get_string('shufflequestionsselected', 'quiz', $updatelink);
        }

        return $warnings;
    }

    /**
     * Get the date information about the current state of the quiz.
     * @return string[] array of two strings. First a short summary, then a longer
     * explanation of the current state, e.g. for a tool-tip.
     */
    public function get_dates_summary() {
        $timenow = time();
        $quiz = $this->quizobj->get_quiz();

        // Exact open and close dates for the tool-tip.
        $dates = array();
        if ($quiz->timeopen > 0) {
            if ($timenow > $quiz->timeopen) {
                $dates[] = get_string('quizopenedon', 'quiz', userdate($quiz->timeopen));
            } else {
                $dates[] = get_string('quizwillopen', 'quiz', userdate($quiz->timeopen));
            }
        }
        if ($quiz->timeclose > 0) {
            if ($timenow > $quiz->timeclose) {
                $dates[] = get_string('quizclosed', 'quiz', userdate($quiz->timeclose));
            } else {
                $dates[] = get_string('quizcloseson', 'quiz', userdate($quiz->timeclose));
            }
        }
        if (empty($dates)) {
            $dates[] = get_string('alwaysavailable', 'quiz');
        }
        $explanation = implode(', ', $dates);

        // Brief summary on the page.
        if ($timenow < $quiz->timeopen) {
            $currentstatus = get_string('quizisclosedwillopen', 'quiz',
                    userdate($quiz->timeopen, get_string('strftimedatetimeshort', 'langconfig')));
        } else if ($quiz->timeclose && $timenow <= $quiz->timeclose) {
            $currentstatus = get_string('quizisopenwillclose', 'quiz',
                    userdate($quiz->timeclose, get_string('strftimedatetimeshort', 'langconfig')));
        } else if ($quiz->timeclose && $timenow > $quiz->timeclose) {
            $currentstatus = get_string('quizisclosed', 'quiz');
        } else {
            $currentstatus = get_string('quizisopen', 'quiz');
        }

        return array($currentstatus, $explanation);
    }

    /**
     * Set up this class with the structure for a given quiz.
     * @param \stdClass $quiz the quiz settings.
     */
    public function populate_structure($quiz) {
        global $DB;

        $slots = $DB->get_records_sql("
                SELECT slot.id AS slotid, slot.slot, slot.questionid, slot.page, slot.maxmark,
                        slot.requireprevious, q.*, qc.contextid
                  FROM {quiz_slots} slot
                  LEFT JOIN {question} q ON q.id = slot.questionid
                  LEFT JOIN {question_categories} qc ON qc.id = q.category
                 WHERE slot.quizid = ?
              ORDER BY slot.slot", array($quiz->id));

        $slots = $this->populate_missing_questions($slots);

        $this->questions = array();
        $this->slots = array();
        $this->slotsinorder = array();
        foreach ($slots as $slotdata) {
            $this->questions[$slotdata->questionid] = $slotdata;

            $slot = new \stdClass();
            $slot->id = $slotdata->slotid;
            $slot->slot = $slotdata->slot;
            $slot->quizid = $quiz->id;
            $slot->page = $slotdata->page;
            $slot->questionid = $slotdata->questionid;
            $slot->maxmark = $slotdata->maxmark;
            $slot->requireprevious = $slotdata->requireprevious;

            $this->slots[$slot->id] = $slot;
            $this->slotsinorder[$slot->slot] = $slot;
        }

        $section = new \stdClass();
        $section->id = 1;
        $section->quizid = $quiz->id;
        $section->heading = '';
        $section->firstslot = 1;
        $section->shuffle = false;
        $this->sections = array(1 => $section);

        $this->populate_slots_with_sectionids();
        $this->populate_question_numbers();
    }

    /**
     * Used by populate. Make up fake data for any missing questions.
     * @param \stdClass[] $slots the data about the slots and questions in the quiz.
     * @return \stdClass[] updated $slots array.
     */
    protected function populate_missing_questions($slots) {
        // Address missing question types.
        foreach ($slots as $slot) {
            if ($slot->qtype === null) {
                // If the questiontype is missing change the question type.
                $slot->id = $slot->questionid;
                $slot->category = 0;
                $slot->qtype = 'missingtype';
                $slot->name = get_string('missingquestion', 'quiz');
                $slot->slot = $slot->slot;
                $slot->maxmark = 0;
                $slot->requireprevious = 0;
                $slot->questiontext = ' ';
                $slot->questiontextformat = FORMAT_HTML;
                $slot->length = 1;

            } else if (!\question_bank::qtype_exists($slot->qtype)) {
                $slot->qtype = 'missingtype';
            }
        }

        return $slots;
    }

    /**
     * Fill in the section ids for each slot.
     */
    public function populate_slots_with_sectionids() {
        $nextsection = reset($this->sections);
        foreach ($this->slotsinorder as $slot) {
            if ($slot->slot == $nextsection->firstslot) {
                $currentsectionid = $nextsection->id;
                $nextsection = next($this->sections);
                if (!$nextsection) {
                    $nextsection = new \stdClass();
                    $nextsection->firstslot = -1;
                }
            }

            $slot->sectionid = $currentsectionid;
        }
    }

    /**
     * Number the questions.
     */
    protected function populate_question_numbers() {
        $number = 1;
        foreach ($this->slots as $slot) {
            if ($this->questions[$slot->questionid]->length == 0) {
                $slot->displayednumber = get_string('infoshort', 'quiz');
            } else {
                $slot->displayednumber = $number;
                $number += 1;
            }
        }
    }

    /**
     * Move a slot from its current location to a new location.
     *
     * After callig this method, this class will be in an invalid state, and
     * should be discarded if you want to manipulate the structure further.
     *
     * @param int $idmove id of slot to be moved
     * @param int $idbefore id of slot to come before slot being moved
     * @param int $page new page number of slot being moved
     * @return void
     */
    public function move_slot($idmove, $idbefore, $page) {
        global $DB;

        $this->check_can_be_edited();

        $movingslot = $this->slots[$idmove];
        if (empty($movingslot)) {
            throw new moodle_exception('Bad slot ID ' . $idmove);
        }
        $movingslotnumber = (int) $movingslot->slot;

        // Empty target slot means move slot to first.
        if (empty($idbefore)) {
            $targetslotnumber = 0;
        } else {
            $targetslotnumber = (int) $this->slots[$idbefore]->slot;
        }

        // Work out how things are being moved.
        $slotreorder = array();
        if ($targetslotnumber > $movingslotnumber) {
            $slotreorder[$movingslotnumber] = $targetslotnumber;
            for ($i = $movingslotnumber; $i < $targetslotnumber; $i++) {
                $slotreorder[$i + 1] = $i;
            }
        } else if ($targetslotnumber < $movingslotnumber - 1) {
            $slotreorder[$movingslotnumber] = $targetslotnumber + 1;
            for ($i = $targetslotnumber + 1; $i < $movingslotnumber; $i++) {
                $slotreorder[$i] = $i + 1;
            }
        }

        $trans = $DB->start_delegated_transaction();

        // Slot has moved record new order.
        if ($slotreorder) {
            update_field_with_unique_index('quiz_slots', 'slot', $slotreorder,
                    array('quizid' => $this->get_quizid()));
        }

        // Page has changed. Record it.
        if (!$page) {
            $page = 1;
        }
        if ($movingslot->page != $page) {
            $DB->set_field('quiz_slots', 'page', $page,
                    array('id' => $movingslot->id));
        }

        $emptypages = $DB->get_fieldset_sql("
                SELECT DISTINCT page - 1
                  FROM {quiz_slots} slot
                 WHERE quizid = ?
                   AND page > 1
                   AND NOT EXISTS (SELECT 1 FROM {quiz_slots} WHERE quizid = ? AND page = slot.page - 1)
              ORDER BY page - 1 DESC
                ", array($this->get_quizid(), $this->get_quizid()));

        foreach ($emptypages as $page) {
            $DB->execute("
                    UPDATE {quiz_slots}
                       SET page = page - 1
                     WHERE quizid = ?
                       AND page > ?
                    ", array($this->get_quizid(), $page));
        }

        $trans->allow_commit();
    }

    /**
     * Refresh page numbering of quiz slots.
     * @param \stdClass $quiz the quiz object.
     * @param \stdClass[] $slots (optional) array of slot objects.
     * @return \stdClass[] array of slot objects.
     */
    public function refresh_page_numbers($quiz, $slots=array()) {
        global $DB;
        // Get slots ordered by page then slot.
        if (!count($slots)) {
            $slots = $DB->get_records('quiz_slots', array('quizid' => $quiz->id), 'slot, page');
        }

        // Loop slots. Start Page number at 1 and increment as required.
        $pagenumbers = array('new' => 0, 'old' => 0);

        foreach ($slots as $slot) {
            if ($slot->page !== $pagenumbers['old']) {
                $pagenumbers['old'] = $slot->page;
                ++$pagenumbers['new'];
            }

            if ($pagenumbers['new'] == $slot->page) {
                continue;
            }
            $slot->page = $pagenumbers['new'];
        }

        return $slots;
    }

    /**
     * Refresh page numbering of quiz slots and save to the database.
     * @param \stdClass $quiz the quiz object.
     * @return \stdClass[] array of slot objects.
     */
    public function refresh_page_numbers_and_update_db($quiz) {
        global $DB;
        $this->check_can_be_edited();

        $slots = $this->refresh_page_numbers($quiz);

        // Record new page order.
        foreach ($slots as $slot) {
            $DB->set_field('quiz_slots', 'page', $slot->page,
                    array('id' => $slot->id));
        }

        return $slots;
    }

    /**
     * Remove a slot from a quiz
     * @param \stdClass $quiz the quiz object.
     * @param int $slotnumber The number of the slot to be deleted.
     */
    public function remove_slot($quiz, $slotnumber) {
        global $DB;

        $this->check_can_be_edited();

        $slot = $DB->get_record('quiz_slots', array('quizid' => $quiz->id, 'slot' => $slotnumber));
        $maxslot = $DB->get_field_sql('SELECT MAX(slot) FROM {quiz_slots} WHERE quizid = ?', array($quiz->id));
        if (!$slot) {
            return;
        }

        $trans = $DB->start_delegated_transaction();
        $DB->delete_records('quiz_slots', array('id' => $slot->id));
        for ($i = $slot->slot + 1; $i <= $maxslot; $i++) {
            $DB->set_field('quiz_slots', 'slot', $i - 1,
                    array('quizid' => $quiz->id, 'slot' => $i));
        }

        $qtype = $DB->get_field('question', 'qtype', array('id' => $slot->questionid));
        if ($qtype === 'random') {
            // This function automatically checks if the question is in use, and won't delete if it is.
            question_delete_question($slot->questionid);
        }

        unset($this->questions[$slot->questionid]);

        $this->refresh_page_numbers_and_update_db($quiz);

        $trans->allow_commit();
    }

    /**
     * Change the max mark for a slot.
     *
     * Saves changes to the question grades in the quiz_slots table and any
     * corresponding question_attempts.
     * It does not update 'sumgrades' in the quiz table.
     *
     * @param \stdClass $slot row from the quiz_slots table.
     * @param float $maxmark the new maxmark.
     * @return bool true if the new grade is different from the old one.
     */
    public function update_slot_maxmark($slot, $maxmark) {
        global $DB;

        if (abs($maxmark - $slot->maxmark) < 1e-7) {
            // Grade has not changed. Nothing to do.
            return false;
        }

        $trans = $DB->start_delegated_transaction();
        $slot->maxmark = $maxmark;
        $DB->update_record('quiz_slots', $slot);
        \question_engine::set_max_mark_in_attempts(new \qubaids_for_quiz($slot->quizid),
                $slot->slot, $maxmark);
        $trans->allow_commit();

        return true;
    }

    /**
     * Set whether the question in a particular slot requires the previous one.
     * @param int $slotid id of slot.
     * @param bool $requireprevious if true, set this question to require the previous one.
     */
    public function update_question_dependency($slotid, $requireprevious) {
        global $DB;
        $DB->set_field('quiz_slots', 'requireprevious', $requireprevious, array('id' => $slotid));
    }

    /**
     * Add/Remove a pagebreak.
     *
     * Saves changes to the slot page relationship in the quiz_slots table and reorders the paging
     * for subsequent slots.
     *
     * @param \stdClass $quiz the quiz object.
     * @param int $slotid id of slot.
     * @param int $type repaginate::LINK or repaginate::UNLINK.
     * @return \stdClass[] array of slot objects.
     */
    public function update_page_break($quiz, $slotid, $type) {
        global $DB;

        $this->check_can_be_edited();

        $quizslots = $DB->get_records('quiz_slots', array('quizid' => $quiz->id), 'slot');
        $repaginate = new \mod_quiz\repaginate($quiz->id, $quizslots);
        $repaginate->repaginate_slots($quizslots[$slotid]->slot, $type);
        $slots = $this->refresh_page_numbers_and_update_db($quiz);

        return $slots;
    }
}
