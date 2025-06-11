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

namespace mod_quiz\output;

use mod_quiz\quiz_attempt;
use moodle_url;
use question_attempt;
use question_display_options;
use question_state;
use renderable;
use user_picture;

/**
 * Represents the navigation panel, and builds a {@see block_contents} to allow it to be output.
 *
 * This class is not currently renderable or templatable, but it probably should be in the future,
 * which is why it is already in the output namespace.
 *
 * @package   mod_quiz
 * @category  output
 * @copyright 2008 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class navigation_panel_base {
    /** @var quiz_attempt */
    protected $attemptobj;
    /** @var question_display_options */
    protected $options;
    /** @var integer */
    protected $page;
    /** @var boolean */
    protected $showall;

    /**
     * Constructor.
     *
     * @param quiz_attempt $attemptobj construct the panel for this attempt.
     * @param question_display_options $options display options in force.
     * @param int $page which page of the quiz attempt is being shown, -1 if all.
     * @param bool $showall whether all pages are being shown at once.
     */
    public function __construct(quiz_attempt $attemptobj,
            question_display_options $options, $page, $showall) {
        $this->attemptobj = $attemptobj;
        $this->options = $options;
        $this->page = $page;
        $this->showall = $showall;
    }

    /**
     * Get the buttons and section headings to go in the quiz navigation block.
     *
     * @return renderable[] the buttons, possibly interleaved with section headings.
     */
    public function get_question_buttons() {
        $buttons = [];
        foreach ($this->attemptobj->get_slots() as $slot) {
            $heading = $this->attemptobj->get_heading_before_slot($slot);
            if (!is_null($heading)) {
                $sections = $this->attemptobj->get_quizobj()->get_sections();
                if (!(empty($heading) && count($sections) == 1)) {
                    $buttons[] = new navigation_section_heading(format_string($heading));
                }
            }

            $qa = $this->attemptobj->get_question_attempt($slot);
            $showcorrectness = $this->options->correctness && $qa->has_marks();

            $button = new navigation_question_button();
            $button->id          = 'quiznavbutton' . $slot;
            $button->isrealquestion = $this->attemptobj->is_real_question($slot);
            $button->number      = $this->attemptobj->get_question_number($slot);
            $button->stateclass  = $qa->get_state_class($showcorrectness);
            $button->navmethod   = $this->attemptobj->get_navigation_method();
            if (!$showcorrectness && $button->stateclass === 'notanswered') {
                $button->stateclass = 'complete';
            }
            $button->statestring = $this->get_state_string($qa, $showcorrectness);
            $button->page        = $this->attemptobj->get_question_page($slot);
            $button->currentpage = $this->showall || $button->page == $this->page;
            $button->flagged     = $qa->is_flagged();
            $button->url         = $this->get_question_url($slot);
            if ($this->attemptobj->is_blocked_by_previous_question($slot)) {
                $button->url = null;
                $button->stateclass = 'blocked';
                $button->statestring = get_string('questiondependsonprevious', 'quiz');
            }
            $buttons[] = $button;
        }

        return $buttons;
    }

    /**
     * Get the human-readable description of the current state of a particular question.
     *
     * @param question_attempt $qa the attempt at the question of interest.
     * @param bool $showcorrectness whether the current use is allowed to see if they have got the question right.
     * @return string Human-readable description of the state.
     */
    protected function get_state_string(question_attempt $qa, $showcorrectness) {
        if ($qa->get_question(false)->length > 0) {
            return $qa->get_state_string($showcorrectness);
        }

        // Special case handling for 'information' items.
        if ($qa->get_state() == question_state::$todo) {
            return get_string('notyetviewed', 'quiz');
        } else {
            return get_string('viewed', 'quiz');
        }
    }

    /**
     * Hook for subclasses to override to do output above the question buttons.
     *
     * @param renderer $output the quiz renderer to use.
     * @return string HTML to output.
     */
    public function render_before_button_bits(renderer $output) {
        return '';
    }

    /**
     * Hook that subclasses must override to do output after the question buttons.
     *
     * @param renderer $output the quiz renderer to use.
     * @return string HTML to output.
     */
    abstract public function render_end_bits(renderer $output);

    /**
     * Render the restart preview button.
     *
     * @param renderer $output the quiz renderer to use.
     * @return string HTML to output.
     */
    protected function render_restart_preview_link($output) {
        if (!$this->attemptobj->is_own_preview()) {
            return '';
        }
        return $output->restart_preview_button(new moodle_url(
                $this->attemptobj->start_attempt_url(), ['forcenew' => true]));
    }

    /**
     * Get the URL to navigate to a particular question.
     *
     * @param int $slot slot number, to identify the question.
     * @return moodle_url|null URL if the user can navigate there, or null if they cannot.
     */
    abstract protected function get_question_url($slot);

    /**
     * Get the user picture which should be displayed, if required.
     *
     * @return user_picture|null
     */
    public function user_picture() {
        global $DB, $PAGE;
        if ($this->attemptobj->get_quiz()->showuserpicture == QUIZ_SHOWIMAGE_NONE) {
            return null;
        }
        $user = $DB->get_record('user', ['id' => $this->attemptobj->get_userid()]);
        $userpicture = new user_picture($user);
        $userpicture->courseid = $this->attemptobj->get_courseid();
        if ($PAGE->pagelayout === 'secure') {
            $userpicture->link = false;
        }
        if ($this->attemptobj->get_quiz()->showuserpicture == QUIZ_SHOWIMAGE_LARGE) {
            $userpicture->size = true;
        }
        return $userpicture;
    }

    /**
     * Return 'allquestionsononepage' as CSS class name when $showall is set,
     * otherwise, return 'multipages' as CSS class name.
     *
     * @return string, CSS class name
     */
    public function get_button_container_class() {
        // Quiz navigation is set on 'Show all questions on one page'.
        if ($this->showall) {
            return 'allquestionsononepage';
        }
        // Quiz navigation is set on 'Show one page at a time'.
        return 'multipages';
    }
}
