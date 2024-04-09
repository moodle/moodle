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

use cm_info;
use coding_exception;
use context;
use context_module;
use html_table;
use html_table_cell;
use html_writer;
use mod_quiz\access_manager;
use mod_quiz\form\preflight_check_form;
use mod_quiz\output\grades\grade_out_of;
use mod_quiz\question\display_options;
use mod_quiz\quiz_attempt;
use moodle_url;
use plugin_renderer_base;
use popup_action;
use question_display_options;
use mod_quiz\quiz_settings;
use renderable;
use single_button;
use stdClass;

/**
 * The main renderer for the quiz module.
 *
 * @package   mod_quiz
 * @category  output
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {
    /**
     * Builds the review page
     *
     * @param quiz_attempt $attemptobj an instance of quiz_attempt.
     * @param array $slots of slots to be displayed.
     * @param int $page the current page number
     * @param bool $showall whether to show entire attempt on one page.
     * @param bool $lastpage if true the current page is the last page.
     * @param display_options $displayoptions instance of display_options.
     * @param attempt_summary_information|array $summarydata summary information about the attempt.
     *      Passing an array is deprecated.
     * @return string HTML to display.
     */
    public function review_page(quiz_attempt $attemptobj, $slots, $page, $showall,
            $lastpage, display_options $displayoptions, $summarydata) {
        if (is_array($summarydata)) {
            debugging('Since Moodle 4.4, $summarydata passed to review_page should be a attempt_summary_information.',
                DEBUG_DEVELOPER);
            $summarydata = $this->filter_review_summary_table($summarydata, $page);
            $summarydata = attempt_summary_information::create_from_legacy_array($summarydata);
        }

        $output = '';
        $output .= $this->header();
        $output .= $this->review_attempt_summary($summarydata, $page);
        $output .= $this->review_form($page, $showall, $displayoptions,
                $this->questions($attemptobj, true, $slots, $page, $showall, $displayoptions),
                $attemptobj);

        $output .= $this->review_next_navigation($attemptobj, $page, $lastpage, $showall);
        $output .= $this->footer();
        return $output;
    }

    /**
     * Renders the review question pop-up.
     *
     * @param quiz_attempt $attemptobj an instance of quiz_attempt.
     * @param int $slot which question to display.
     * @param int $seq which step of the question attempt to show. null = latest.
     * @param display_options $displayoptions instance of display_options.
     * @param attempt_summary_information|array $summarydata summary information about the attempt.
     *      Passing an array is deprecated.
     * @return string HTML to display.
     */
    public function review_question_page(quiz_attempt $attemptobj, $slot, $seq,
            display_options $displayoptions, $summarydata) {
        if (is_array($summarydata)) {
            debugging('Since Moodle 4.4, $summarydata passed to review_question_page should be a attempt_summary_information.',
                DEBUG_DEVELOPER);
            $summarydata = attempt_summary_information::create_from_legacy_array($summarydata);
        }

        $output = '';
        $output .= $this->header();
        $output .= html_writer::div($this->render($summarydata), 'mb-3');

        if (!is_null($seq)) {
            $output .= $attemptobj->render_question_at_step($slot, $seq, true, $this);
        } else {
            $output .= $attemptobj->render_question($slot, true, $this);
        }

        $output .= $this->close_window_button();
        $output .= $this->footer();
        return $output;
    }

    /**
     * Renders the review question pop-up.
     *
     * @param quiz_attempt $attemptobj an instance of quiz_attempt.
     * @param string $message Why the review is not allowed.
     * @return string html to output.
     */
    public function review_question_not_allowed(quiz_attempt $attemptobj, $message) {
        $output = '';
        $output .= $this->header();
        $output .= $this->heading(format_string($attemptobj->get_quiz_name(), true,
                ["context" => $attemptobj->get_quizobj()->get_context()]));
        $output .= $this->notification($message);
        $output .= $this->close_window_button();
        $output .= $this->footer();
        return $output;
    }

    /**
     * A chance to filter the information before display.
     *
     * Moodle core uses this to display less infomrmation on pages after the first.
     * This is a separate method, becaus it is a useful hook where themes can overrid things.
     *
     * @param attempt_summary_information $summarydata the data that will be displayed. Modify as desired.
     * @param int $page contains the current page number
     */
    public function filter_review_attempt_summary(
        attempt_summary_information $summarydata,
        int $page
    ): void {
        if ($page > 0) {
            $summarydata->filter_keeping_only(['user', 'attemptlist']);
        }
    }

    /**
     * Outputs the overall summary of the attempt at the top of the review page.
     *
     * @param attempt_summary_information $summarydata contains row data for table.
     * @param int $page contains the current page number
     * @return string HTML to display.
     */
    public function review_attempt_summary(
        attempt_summary_information $summarydata,
        int $page
    ): string {
        $this->filter_review_attempt_summary($summarydata, $page);
        return html_writer::div($this->render($summarydata), 'mb-3');
    }

    /**
     * Filters the summarydata array.
     *
     * @param array $summarydata contains row data for table
     * @param int $page the current page number
     * @return array updated version of the $summarydata array.
     * @deprecated since Moodle 4.4. Replaced by filter_review_attempt_summary.
     */
    protected function filter_review_summary_table($summarydata, $page) {
        debugging('filter_review_summary_table() is deprecated. Replaced by filter_review_attempt_summary().', DEBUG_DEVELOPER);
        if ($page == 0) {
            return $summarydata;
        }

        // Only show some of summary table on subsequent pages.
        foreach ($summarydata as $key => $rowdata) {
            if (!in_array($key, ['user', 'attemptlist'])) {
                unset($summarydata[$key]);
            }
        }

        return $summarydata;
    }

    /**
     * Outputs the table containing data from summary data array
     *
     * @param array $summarydata contains row data for table
     * @param int $page contains the current page number
     * @return string HTML to display.
     * @deprecated since Moodle 4.4. Replaced by review_attempt_summary.
     */
    public function review_summary_table($summarydata, $page) {
        debugging('review_summary_table() is deprecated. Please use review_attempt_summary() instead.', DEBUG_DEVELOPER);
        $summarydata = $this->filter_review_summary_table($summarydata, $page);
        $this->render(attempt_summary_information::create_from_legacy_array($summarydata));
    }

    /**
     * Renders each question
     *
     * @param quiz_attempt $attemptobj instance of quiz_attempt
     * @param bool $reviewing
     * @param array $slots array of integers relating to questions
     * @param int $page current page number
     * @param bool $showall if true shows attempt on single page
     * @param display_options $displayoptions instance of display_options
     */
    public function questions(quiz_attempt $attemptobj, $reviewing, $slots, $page, $showall,
            display_options $displayoptions) {
        $output = '';
        foreach ($slots as $slot) {
            $output .= $attemptobj->render_question($slot, $reviewing, $this,
                    $attemptobj->review_url($slot, $page, $showall));
        }
        return $output;
    }

    /**
     * Renders the main bit of the review page.
     *
     * @param int $page current page number
     * @param bool $showall if true display attempt on one page
     * @param display_options $displayoptions instance of display_options
     * @param string $content the rendered display of each question
     * @param quiz_attempt $attemptobj instance of quiz_attempt
     * @return string HTML to display.
     */
    public function review_form($page, $showall, $displayoptions, $content, $attemptobj) {
        if ($displayoptions->flags != question_display_options::EDITABLE) {
            return $content;
        }

        $this->page->requires->js_init_call('M.mod_quiz.init_review_form', null, false,
                quiz_get_js_module());

        $output = '';
        $output .= html_writer::start_tag('form', ['action' => $attemptobj->review_url(null,
                $page, $showall), 'method' => 'post', 'class' => 'questionflagsaveform']);
        $output .= html_writer::start_tag('div');
        $output .= $content;
        $output .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey',
                'value' => sesskey()]);
        $output .= html_writer::start_tag('div', ['class' => 'submitbtns']);
        $output .= html_writer::empty_tag('input', ['type' => 'submit',
                'class' => 'questionflagsavebutton btn btn-secondary', 'name' => 'savingflags',
                'value' => get_string('saveflags', 'question')]);
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('form');

        return $output;
    }

    /**
     * Returns either a link or button.
     *
     * @param quiz_attempt $attemptobj instance of quiz_attempt
     */
    public function finish_review_link(quiz_attempt $attemptobj) {
        $url = $attemptobj->view_url();

        if ($attemptobj->get_access_manager(time())->attempt_must_be_in_popup()) {
            $this->page->requires->js_init_call('M.mod_quiz.secure_window.init_close_button',
                    [$url->out(false)], false, quiz_get_js_module());
            return html_writer::empty_tag('input', ['type' => 'button',
                    'value' => get_string('finishreview', 'quiz'),
                    'id' => 'secureclosebutton',
                    'class' => 'mod_quiz-next-nav btn btn-primary']);

        } else {
            return html_writer::link($url, get_string('finishreview', 'quiz'),
                    ['class' => 'mod_quiz-next-nav']);
        }
    }

    /**
     * Creates the navigation links/buttons at the bottom of the review attempt page.
     *
     * Note, the name of this function is no longer accurate, but when the design
     * changed, it was decided to keep the old name for backwards compatibility.
     *
     * @param quiz_attempt $attemptobj instance of quiz_attempt
     * @param int $page the current page
     * @param bool $lastpage if true current page is the last page
     * @param bool|null $showall if true, the URL will be to review the entire attempt on one page,
     *      and $page will be ignored. If null, a sensible default will be chosen.
     *
     * @return string HTML fragment.
     */
    public function review_next_navigation(quiz_attempt $attemptobj, $page, $lastpage, $showall = null) {
        $nav = '';
        if ($page > 0) {
            $nav .= link_arrow_left(get_string('navigateprevious', 'quiz'),
                    $attemptobj->review_url(null, $page - 1, $showall), false, 'mod_quiz-prev-nav');
        }
        if ($lastpage) {
            $nav .= $this->finish_review_link($attemptobj);
        } else {
            $nav .= link_arrow_right(get_string('navigatenext', 'quiz'),
                    $attemptobj->review_url(null, $page + 1, $showall), false, 'mod_quiz-next-nav');
        }
        return html_writer::tag('div', $nav, ['class' => 'submitbtns']);
    }

    /**
     * Return the HTML of the quiz timer.
     *
     * @param quiz_attempt $attemptobj instance of quiz_attempt
     * @param int $timenow timestamp to use as 'now'.
     * @return string HTML content.
     */
    public function countdown_timer(quiz_attempt $attemptobj, $timenow) {

        $timeleft = $attemptobj->get_time_left_display($timenow);
        if ($timeleft !== false) {
            $ispreview = $attemptobj->is_preview();
            $timerstartvalue = $timeleft;
            if (!$ispreview) {
                // Make sure the timer starts just above zero. If $timeleft was <= 0, then
                // this will just have the effect of causing the quiz to be submitted immediately.
                $timerstartvalue = max($timerstartvalue, 1);
            }
            $this->initialise_timer($timerstartvalue, $ispreview);
        }

        return $this->output->render_from_template('mod_quiz/timer', (object) []);
    }

    /**
     * Create a preview link
     *
     * @param moodle_url $url URL to restart the attempt.
     */
    public function restart_preview_button($url) {
        return $this->single_button($url, get_string('startnewpreview', 'quiz'));
    }

    /**
     * Outputs the navigation block panel
     *
     * @param navigation_panel_base $panel
     */
    public function navigation_panel(navigation_panel_base $panel) {

        $output = '';
        $userpicture = $panel->user_picture();
        if ($userpicture) {
            $fullname = fullname($userpicture->user);
            if ($userpicture->size) {
                $fullname = html_writer::div($fullname);
            }
            $output .= html_writer::tag('div', $this->render($userpicture) . $fullname,
                    ['id' => 'user-picture', 'class' => 'clearfix']);
        }
        $output .= $panel->render_before_button_bits($this);

        $bcc = $panel->get_button_container_class();
        $output .= html_writer::start_tag('div', ['class' => "qn_buttons clearfix $bcc"]);
        foreach ($panel->get_question_buttons() as $button) {
            $output .= $this->render($button);
        }
        $output .= html_writer::end_tag('div');

        $output .= html_writer::tag('div', $panel->render_end_bits($this),
                ['class' => 'othernav']);

        $this->page->requires->js_init_call('M.mod_quiz.nav.init', null, false,
                quiz_get_js_module());

        return $output;
    }

    /**
     * Display a quiz navigation button.
     *
     * @param navigation_question_button $button
     * @return string HTML fragment.
     */
    protected function render_navigation_question_button(navigation_question_button $button) {
        $classes = ['qnbutton', $button->stateclass, $button->navmethod, 'btn'];
        $extrainfo = [];

        if ($button->currentpage) {
            $classes[] = 'thispage';
            $extrainfo[] = get_string('onthispage', 'quiz');
        }

        // Flagged?
        if ($button->flagged) {
            $classes[] = 'flagged';
            $flaglabel = get_string('flagged', 'question');
        } else {
            $flaglabel = '';
        }
        $extrainfo[] = html_writer::tag('span', $flaglabel, ['class' => 'flagstate']);

        if ($button->isrealquestion) {
            $qnostring = 'questionnonav';
        } else {
            $qnostring = 'questionnonavinfo';
        }

        $tooltip = get_string('questionx', 'question', s($button->number)) . ' - ' . $button->statestring;

        $a = new stdClass();
        $a->number = s($button->number);
        $a->attributes = implode(' ', $extrainfo);
        $tagcontents = html_writer::tag('span', '', ['class' => 'thispageholder']) .
                html_writer::tag('span', '', ['class' => 'trafficlight']) .
                get_string($qnostring, 'quiz', $a);
        $tagattributes = ['class' => implode(' ', $classes), 'id' => $button->id,
                'title' => $tooltip, 'data-quiz-page' => $button->page];

        if ($button->url) {
            return html_writer::link($button->url, $tagcontents, $tagattributes);
        } else {
            return html_writer::tag('span', $tagcontents, $tagattributes);
        }
    }

    /**
     * Display a quiz navigation heading.
     *
     * @param navigation_section_heading $heading the heading.
     * @return string HTML fragment.
     */
    protected function render_navigation_section_heading(navigation_section_heading $heading) {
        if (empty($heading->heading)) {
            $headingtext = get_string('sectionnoname', 'quiz');
            $class = ' dimmed_text';
        } else {
            $headingtext = $heading->heading;
            $class = '';
        }
        return $this->heading($headingtext, 3, 'mod_quiz-section-heading' . $class);
    }

    /**
     * Renders a list of links the other attempts.
     *
     * @param links_to_other_attempts $links
     * @return string HTML fragment.
     */
    protected function render_links_to_other_attempts(
            links_to_other_attempts $links) {
        $attemptlinks = [];
        foreach ($links->links as $attempt => $url) {
            if (!$url) {
                $attemptlinks[] = html_writer::tag('strong', $attempt);
            } else {
                if ($url instanceof renderable) {
                    $attemptlinks[] = $this->render($url);
                } else {
                    $attemptlinks[] = html_writer::link($url, $attempt);
                }
            }
        }
        return implode(', ', $attemptlinks);
    }

    /**
     * Render a {@see grade_out_of}.
     *
     * Most of the logic is in methods of the grade_out_of class. However,
     * having this renderer method allows themes to override the default rendering.
     *
     * @param grade_out_of $grade
     * @return string HTML to output.
     */
    protected function render_grade_out_of(grade_out_of $grade): string {
        return get_string($grade->get_string_key(), 'quiz',
                $grade->style_formatted_values($grade->get_formatted_values()));
    }

    /**
     * Render the 'start attempt' page.
     *
     * The student gets here if their interaction with the preflight check
     * from fails in some way (e.g. they typed the wrong password).
     *
     * @param \mod_quiz\quiz_settings $quizobj
     * @param preflight_check_form $mform
     * @return string
     */
    public function start_attempt_page(quiz_settings $quizobj, preflight_check_form $mform) {
        $output = '';
        $output .= $this->header();
        $output .= $this->during_attempt_tertiary_nav($quizobj->view_url());
        $output .= $this->heading(format_string($quizobj->get_quiz_name(), true,
                ["context" => $quizobj->get_context()]));
        $output .= $this->quiz_intro($quizobj->get_quiz(), $quizobj->get_cm());
        $output .= $mform->render();
        $output .= $this->footer();
        return $output;
    }

    /**
     * Attempt Page
     *
     * @param quiz_attempt $attemptobj Instance of quiz_attempt
     * @param int $page Current page number
     * @param access_manager $accessmanager Instance of access_manager
     * @param array $messages An array of messages
     * @param array $slots Contains an array of integers that relate to questions
     * @param int $id The ID of an attempt
     * @param int $nextpage The number of the next page
     * @return string HTML to output.
     */
    public function attempt_page($attemptobj, $page, $accessmanager, $messages, $slots, $id,
            $nextpage) {
        $output = '';
        $output .= $this->header();
        $output .= $this->during_attempt_tertiary_nav($attemptobj->view_url());
        $output .= $this->quiz_notices($messages);
        $output .= $this->countdown_timer($attemptobj, time());
        $output .= $this->attempt_form($attemptobj, $page, $slots, $id, $nextpage);
        $output .= $this->footer();
        return $output;
    }

    /**
     * Render the tertiary navigation for pages during the attempt.
     *
     * @param string|moodle_url $quizviewurl url of the view.php page for this quiz.
     * @return string HTML to output.
     */
    public function during_attempt_tertiary_nav($quizviewurl): string {
        $output = '';
        $output .= html_writer::start_div('container-fluid tertiary-navigation');
        $output .= html_writer::start_div('row');
        $output .= html_writer::start_div('navitem');
        $output .= html_writer::link($quizviewurl, get_string('back'),
                ['class' => 'btn btn-secondary']);
        $output .= html_writer::end_div();
        $output .= html_writer::end_div();
        $output .= html_writer::end_div();
        return $output;
    }

    /**
     * Returns any notices.
     *
     * @param array $messages
     */
    public function quiz_notices($messages) {
        if (!$messages) {
            return '';
        }
        return $this->notification(
                html_writer::tag('p', get_string('accessnoticesheader', 'quiz')) . $this->access_messages($messages),
                'warning',
                false
        );
    }

    /**
     * Outputs the form for making an attempt
     *
     * @param quiz_attempt $attemptobj
     * @param int $page Current page number
     * @param array $slots Array of integers relating to questions
     * @param int $id ID of the attempt
     * @param int $nextpage Next page number
     */
    public function attempt_form($attemptobj, $page, $slots, $id, $nextpage) {
        $output = '';

        // Start the form.
        $output .= html_writer::start_tag('form',
                ['action' => new moodle_url($attemptobj->processattempt_url(),
                        ['cmid' => $attemptobj->get_cmid()]), 'method' => 'post',
                        'enctype' => 'multipart/form-data', 'accept-charset' => 'utf-8',
                        'id' => 'responseform']);
        $output .= html_writer::start_tag('div');

        // Print all the questions.
        foreach ($slots as $slot) {
            $output .= $attemptobj->render_question($slot, false, $this,
                    $attemptobj->attempt_url($slot, $page));
        }

        $navmethod = $attemptobj->get_quiz()->navmethod;
        $output .= $this->attempt_navigation_buttons($page, $attemptobj->is_last_page($page), $navmethod);

        // Some hidden fields to track what is going on.
        $output .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'attempt',
                'value' => $attemptobj->get_attemptid()]);
        $output .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'thispage',
                'value' => $page, 'id' => 'followingpage']);
        $output .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'nextpage',
                'value' => $nextpage]);
        $output .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'timeup',
                'value' => '0', 'id' => 'timeup']);
        $output .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey',
                'value' => sesskey()]);
        $output .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'mdlscrollto',
                'value' => '', 'id' => 'mdlscrollto']);

        // Add a hidden field with questionids. Do this at the end of the form, so
        // if you navigate before the form has finished loading, it does not wipe all
        // the student's answers.
        $output .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'slots',
                'value' => implode(',', $attemptobj->get_active_slots($page))]);

        // Finish the form.
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('form');

        $output .= $this->connection_warning();

        return $output;
    }

    /**
     * Display the prev/next buttons that go at the bottom of each page of the attempt.
     *
     * @param int $page the page number. Starts at 0 for the first page.
     * @param bool $lastpage is this the last page in the quiz?
     * @param string $navmethod Optional quiz attribute, 'free' (default) or 'sequential'
     * @return string HTML fragment.
     */
    protected function attempt_navigation_buttons($page, $lastpage, $navmethod = 'free') {
        $output = '';

        $output .= html_writer::start_tag('div', ['class' => 'submitbtns']);
        if ($page > 0 && $navmethod == 'free') {
            $output .= html_writer::empty_tag('input', ['type' => 'submit', 'name' => 'previous',
                    'value' => get_string('navigateprevious', 'quiz'), 'class' => 'mod_quiz-prev-nav btn btn-secondary',
                    'id' => 'mod_quiz-prev-nav']);
            $this->page->requires->js_call_amd('core_form/submit', 'init', ['mod_quiz-prev-nav']);
        }
        if ($lastpage) {
            $nextlabel = get_string('endtest', 'quiz');
        } else {
            $nextlabel = get_string('navigatenext', 'quiz');
        }
        $output .= html_writer::empty_tag('input', ['type' => 'submit', 'name' => 'next',
                'value' => $nextlabel, 'class' => 'mod_quiz-next-nav btn btn-primary', 'id' => 'mod_quiz-next-nav']);
        $output .= html_writer::end_tag('div');
        $this->page->requires->js_call_amd('core_form/submit', 'init', ['mod_quiz-next-nav']);

        return $output;
    }

    /**
     * Render a button which allows students to redo a question in the attempt.
     *
     * @param int $slot the number of the slot to generate the button for.
     * @param bool $disabled if true, output the button disabled.
     * @return string HTML fragment.
     */
    public function redo_question_button($slot, $disabled) {
        $attributes = ['type' => 'submit', 'name' => 'redoslot' . $slot,
                'value' => get_string('redoquestion', 'quiz'),
                'class' => 'mod_quiz-redo_question_button btn btn-secondary',
                'id' => 'redoslot' . $slot . '-submit',
                'data-savescrollposition' => 'true',
            ];
        if ($disabled) {
            $attributes['disabled'] = 'disabled';
        } else {
            $this->page->requires->js_call_amd('core_question/question_engine', 'initSubmitButton', [$attributes['id']]);
        }
        return html_writer::div(html_writer::empty_tag('input', $attributes));
    }

    /**
     * Initialise the JavaScript required to initialise the countdown timer.
     *
     * @param int $timerstartvalue time remaining, in seconds.
     * @param bool $ispreview true if this is a preview attempt.
     */
    public function initialise_timer($timerstartvalue, $ispreview) {
        $options = [$timerstartvalue, (bool) $ispreview];
        $this->page->requires->js_init_call('M.mod_quiz.timer.init', $options, false, quiz_get_js_module());
    }

    /**
     * Output a page with an optional message, and JavaScript code to close the
     * current window and redirect the parent window to a new URL.
     *
     * @param moodle_url $url the URL to redirect the parent window to.
     * @param string $message message to display before closing the window. (optional)
     * @return string HTML to output.
     */
    public function close_attempt_popup($url, $message = '') {
        $output = '';
        $output .= $this->header();
        $output .= $this->box_start();

        if ($message) {
            $output .= html_writer::tag('p', $message);
            $output .= html_writer::tag('p', get_string('windowclosing', 'quiz'));
            $delay = 5;
        } else {
            $output .= html_writer::tag('p', get_string('pleaseclose', 'quiz'));
            $delay = 0;
        }
        $this->page->requires->js_init_call('M.mod_quiz.secure_window.close',
                [$url, $delay], false, quiz_get_js_module());

        $output .= $this->box_end();
        $output .= $this->footer();
        return $output;
    }

    /**
     * Print each message in an array, surrounded by &lt;p>, &lt;/p> tags.
     *
     * @param array $messages the array of message strings.
     * @return string HTML to output.
     */
    public function access_messages($messages) {
        $output = '';
        foreach ($messages as $message) {
            $output .= html_writer::tag('p', $message, ['class' => 'text-left']);
        }
        return $output;
    }

    /*
     * Summary Page
     */
    /**
     * Create the summary page
     *
     * @param quiz_attempt $attemptobj
     * @param display_options $displayoptions
     */
    public function summary_page($attemptobj, $displayoptions) {
        $output = '';
        $output .= $this->header();
        $output .= $this->during_attempt_tertiary_nav($attemptobj->view_url());
        $output .= $this->heading(format_string($attemptobj->get_quiz_name()));
        $output .= $this->heading(get_string('summaryofattempt', 'quiz'), 3);
        $output .= $this->summary_table($attemptobj, $displayoptions);
        $output .= $this->summary_page_controls($attemptobj);
        $output .= $this->footer();
        return $output;
    }

    /**
     * Generates the table of summarydata
     *
     * @param quiz_attempt $attemptobj
     * @param display_options $displayoptions
     */
    public function summary_table($attemptobj, $displayoptions) {
        // Prepare the summary table header.
        $table = new html_table();
        $table->attributes['class'] = 'generaltable quizsummaryofattempt boxaligncenter';
        $table->head = [get_string('question', 'quiz'), get_string('status', 'quiz')];
        $table->align = ['left', 'left'];
        $table->size = ['', ''];
        $markscolumn = $displayoptions->marks >= question_display_options::MARK_AND_MAX;
        if ($markscolumn) {
            $table->head[] = get_string('marks', 'quiz');
            $table->align[] = 'left';
            $table->size[] = '';
        }
        $tablewidth = count($table->align);
        $table->data = [];

        // Get the summary info for each question.
        $slots = $attemptobj->get_slots();
        foreach ($slots as $slot) {
            // Add a section headings if we need one here.
            $heading = $attemptobj->get_heading_before_slot($slot);
            if ($heading !== null) {
                // There is a heading here.
                $rowclasses = 'quizsummaryheading';
                if ($heading) {
                    $heading = format_string($heading);
                } else {
                    if (count($attemptobj->get_quizobj()->get_sections()) > 1) {
                        // If this is the start of an unnamed section, and the quiz has more
                        // than one section, then add a default heading.
                        $heading = get_string('sectionnoname', 'quiz');
                        $rowclasses .= ' dimmed_text';
                    }
                }
                $cell = new html_table_cell(format_string($heading));
                $cell->header = true;
                $cell->colspan = $tablewidth;
                $table->data[] = [$cell];
                $table->rowclasses[] = $rowclasses;
            }

            // Don't display information items.
            if (!$attemptobj->is_real_question($slot)) {
                continue;
            }

            // Real question, show it.
            $flag = '';
            if ($attemptobj->is_question_flagged($slot)) {
                // Quiz has custom JS manipulating these image tags - so we can't use the pix_icon method here.
                $flag = html_writer::empty_tag('img', ['src' => $this->image_url('i/flagged'),
                        'alt' => get_string('flagged', 'question'), 'class' => 'questionflag icon-post']);
            }
            if ($attemptobj->can_navigate_to($slot)) {
                $row = [html_writer::link($attemptobj->attempt_url($slot),
                        $attemptobj->get_question_number($slot) . $flag),
                        $attemptobj->get_question_status($slot, $displayoptions->correctness)];
            } else {
                $row = [$attemptobj->get_question_number($slot) . $flag,
                        $attemptobj->get_question_status($slot, $displayoptions->correctness)];
            }
            if ($markscolumn) {
                $row[] = $attemptobj->get_question_mark($slot);
            }
            $table->data[] = $row;
            $table->rowclasses[] = 'quizsummary' . $slot . ' ' . $attemptobj->get_question_state_class(
                            $slot, $displayoptions->correctness);
        }

        // Print the summary table.
        return html_writer::table($table);
    }

    /**
     * Creates any controls the page should have.
     *
     * @param quiz_attempt $attemptobj
     */
    public function summary_page_controls($attemptobj) {
        $output = '';

        // Return to place button.
        if ($attemptobj->get_state() == quiz_attempt::IN_PROGRESS) {
            $button = new single_button(
                    new moodle_url($attemptobj->attempt_url(null, $attemptobj->get_currentpage())),
                    get_string('returnattempt', 'quiz'));
            $output .= $this->container($this->container($this->render($button),
                    'controls'), 'submitbtns mdl-align');
        }

        // Finish attempt button.
        $options = [
                'attempt' => $attemptobj->get_attemptid(),
                'finishattempt' => 1,
                'timeup' => 0,
                'slots' => '',
                'cmid' => $attemptobj->get_cmid(),
                'sesskey' => sesskey(),
        ];

        $button = new single_button(
                new moodle_url($attemptobj->processattempt_url(), $options),
                get_string('submitallandfinish', 'quiz'));
        $button->class = 'btn-finishattempt';
        $button->formid = 'frm-finishattempt';
        if ($attemptobj->get_state() == quiz_attempt::IN_PROGRESS) {
            $totalunanswered = 0;
            if ($attemptobj->get_quiz()->navmethod == 'free') {
                // Only count the unanswered question if the navigation method is set to free.
                $totalunanswered = $attemptobj->get_number_of_unanswered_questions();
            }
            $this->page->requires->js_call_amd('mod_quiz/submission_confirmation', 'init', [$totalunanswered]);
        }
        $button->type = \single_button::BUTTON_PRIMARY;

        $duedate = $attemptobj->get_due_date();
        $message = '';
        if ($attemptobj->get_state() == quiz_attempt::OVERDUE) {
            $message = get_string('overduemustbesubmittedby', 'quiz', userdate($duedate));

        } else {
            if ($duedate) {
                $message = get_string('mustbesubmittedby', 'quiz', userdate($duedate));
            }
        }

        $output .= $this->countdown_timer($attemptobj, time());
        $output .= $this->container($message . $this->container(
                        $this->render($button), 'controls'), 'submitbtns mdl-align');

        return $output;
    }

    /*
     * View Page
     */
    /**
     * Generates the view page
     *
     * @param stdClass $course the course settings row from the database.
     * @param stdClass $quiz the quiz settings row from the database.
     * @param stdClass $cm the course_module settings row from the database.
     * @param context_module $context the quiz context.
     * @param view_page $viewobj
     * @return string HTML to display
     */
    public function view_page($course, $quiz, $cm, $context, $viewobj) {
        $output = '';

        $output .= $this->view_page_tertiary_nav($viewobj);
        $output .= $this->view_information($quiz, $cm, $context, $viewobj->infomessages);
        $output .= $this->view_result_info($quiz, $context, $cm, $viewobj);
        $output .= $this->render($viewobj->attemptslist);
        $output .= $this->box($this->view_page_buttons($viewobj), 'quizattempt');
        return $output;
    }

    /**
     * Render the tertiary navigation for the view page.
     *
     * @param view_page $viewobj the information required to display the view page.
     * @return string HTML to output.
     */
    public function view_page_tertiary_nav(view_page $viewobj): string {
        $content = '';

        if ($viewobj->buttontext) {
            $attemptbtn = $this->start_attempt_button($viewobj->buttontext,
                    $viewobj->startattempturl, $viewobj->preflightcheckform,
                    $viewobj->popuprequired, $viewobj->popupoptions);
            $content .= $attemptbtn;
        }

        if ($viewobj->canedit && !$viewobj->quizhasquestions) {
            $content .= html_writer::link($viewobj->editurl, get_string('addquestion', 'quiz'),
                    ['class' => 'btn btn-secondary']);
        }

        if ($content) {
            return html_writer::div(html_writer::div($content, 'row'), 'container-fluid tertiary-navigation');
        } else {
            return '';
        }
    }

    /**
     * Work out, and render, whatever buttons, and surrounding info, should appear
     * at the end of the review page.
     *
     * @param view_page $viewobj the information required to display the view page.
     * @return string HTML to output.
     */
    public function view_page_buttons(view_page $viewobj) {
        $output = '';

        if (!$viewobj->quizhasquestions) {
            $output .= html_writer::div(
                    $this->notification(get_string('noquestions', 'quiz'), 'warning', false),
                    'text-left mb-3');
        }
        $output .= $this->access_messages($viewobj->preventmessages);

        if ($viewobj->showbacktocourse) {
            $output .= $this->single_button($viewobj->backtocourseurl,
                    get_string('backtocourse', 'quiz'), 'get',
                    ['class' => 'continuebutton']);
        }

        return $output;
    }

    /**
     * Generates the view attempt button
     *
     * @param string $buttontext the label to display on the button.
     * @param moodle_url $url The URL to POST to in order to start the attempt.
     * @param preflight_check_form|null $preflightcheckform deprecated.
     * @param bool $popuprequired whether the attempt needs to be opened in a pop-up.
     * @param array $popupoptions the options to use if we are opening a popup.
     * @return string HTML fragment.
     */
    public function start_attempt_button($buttontext, moodle_url $url,
            preflight_check_form $preflightcheckform = null,
            $popuprequired = false, $popupoptions = null) {

        $button = new single_button($url, $buttontext, 'post', single_button::BUTTON_PRIMARY);
        $button->class .= ' quizstartbuttondiv';
        if ($popuprequired) {
            $button->class .= ' quizsecuremoderequired';
        }

        $popupjsoptions = null;
        if ($popuprequired && $popupoptions) {
            $action = new popup_action('click', $url, 'popup', $popupoptions);
            $popupjsoptions = $action->get_js_options();
        }

        $this->page->requires->js_call_amd('mod_quiz/preflightcheck', 'init',
                ['.quizstartbuttondiv [type=submit]', get_string('startattempt', 'quiz'),
                        '#mod_quiz_preflight_form', $popupjsoptions]);

        return $this->render($button) . ($preflightcheckform ? $preflightcheckform->render() : '');
    }

    /**
     * Generate a message saying that this quiz has no questions, with a button to
     * go to the edit page, if the user has the right capability.
     *
     * @param bool $canedit can the current user edit the quiz?
     * @param moodle_url $editurl URL of the edit quiz page.
     * @return string HTML to output.
     *
     * @deprecated since Moodle 4.0 MDL-71915 - please do not use this function any more.
     */
    public function no_questions_message($canedit, $editurl) {
        debugging('no_questions_message() is deprecated, please use generate_no_questions_message() instead.', DEBUG_DEVELOPER);

        $output = html_writer::start_tag('div', ['class' => 'card text-center mb-3']);
        $output .= html_writer::start_tag('div', ['class' => 'card-body']);

        $output .= $this->notification(get_string('noquestions', 'quiz'), 'warning', false);
        if ($canedit) {
            $output .= $this->single_button($editurl, get_string('editquiz', 'quiz'), 'get');
        }
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        return $output;
    }

    /**
     * Outputs an error message for any guests accessing the quiz
     *
     * @param stdClass $course the course settings row from the database.
     * @param stdClass $quiz the quiz settings row from the database.
     * @param stdClass $cm the course_module settings row from the database.
     * @param context_module $context the quiz context.
     * @param array $messages Array containing any messages
     * @param view_page $viewobj
     */
    public function view_page_guest($course, $quiz, $cm, $context, $messages, $viewobj) {
        $output = '';
        $output .= $this->view_page_tertiary_nav($viewobj);
        $output .= $this->view_information($quiz, $cm, $context, $messages);
        $guestno = html_writer::tag('p', get_string('guestsno', 'quiz'));
        $liketologin = html_writer::tag('p', get_string('liketologin'));
        $referer = get_local_referer(false);
        $output .= $this->confirm($guestno . "\n\n" . $liketologin . "\n", get_login_url(), $referer);
        return $output;
    }

    /**
     * Outputs and error message for anyone who is not enrolled on the course.
     *
     * @param stdClass $course the course settings row from the database.
     * @param stdClass $quiz the quiz settings row from the database.
     * @param stdClass $cm the course_module settings row from the database.
     * @param context_module $context the quiz context.
     * @param array $messages Array containing any messages
     * @param view_page $viewobj
     */
    public function view_page_notenrolled($course, $quiz, $cm, $context, $messages, $viewobj) {
        global $CFG;
        $output = '';
        $output .= $this->view_page_tertiary_nav($viewobj);
        $output .= $this->view_information($quiz, $cm, $context, $messages);
        $youneedtoenrol = html_writer::tag('p', get_string('youneedtoenrol', 'quiz'));
        $button = html_writer::tag('p',
                $this->continue_button($CFG->wwwroot . '/course/view.php?id=' . $course->id));
        $output .= $this->box($youneedtoenrol . "\n\n" . $button . "\n", 'generalbox', 'notice');
        return $output;
    }

    /**
     * Output the page information
     *
     * @param stdClass $quiz the quiz settings.
     * @param cm_info|stdClass $cm the course_module object.
     * @param context $context the quiz context.
     * @param array $messages any access messages that should be described.
     * @param bool $quizhasquestions does quiz has questions added.
     * @return string HTML to output.
     */
    public function view_information($quiz, $cm, $context, $messages, bool $quizhasquestions = false) {
        $output = '';

        // Output any access messages.
        if ($messages) {
            $output .= $this->box($this->access_messages($messages), 'quizinfo');
        }

        // Show number of attempts summary to those who can view reports.
        if (has_capability('mod/quiz:viewreports', $context)) {
            if ($strattemptnum = $this->quiz_attempt_summary_link_to_reports($quiz, $cm,
                    $context)) {
                $output .= html_writer::tag('div', $strattemptnum,
                        ['class' => 'quizattemptcounts']);
            }
        }

        if (has_any_capability(['mod/quiz:manageoverrides', 'mod/quiz:viewoverrides'], $context)) {
            if ($overrideinfo = $this->quiz_override_summary_links($quiz, $cm)) {
                $output .= html_writer::tag('div', $overrideinfo, ['class' => 'quizattemptcounts']);
            }
        }

        return $output;
    }

    /**
     * Output the quiz intro.
     *
     * @param stdClass $quiz the quiz settings.
     * @param stdClass $cm the course_module object.
     * @return string HTML to output.
     */
    public function quiz_intro($quiz, $cm) {
        if (html_is_blank($quiz->intro)) {
            return '';
        }

        return $this->box(format_module_intro('quiz', $quiz, $cm->id), 'generalbox', 'intro');
    }

    /**
     * Generates the table heading.
     */
    public function view_table_heading() {
        return $this->heading(get_string('summaryofattempts', 'quiz'), 3);
    }

    /**
     * Generates the table of data
     *
     * @param stdClass $quiz the quiz settings.
     * @param context_module $context the quiz context.
     * @param view_page $viewobj
     * @deprecated Since 4.4 please use the {@see list_of_attempts} renderable instead.
     */
    public function view_table($quiz, $context, $viewobj) {
        debugging('view_table has been deprecated since 4.4 please use the list_of_attempts renderable instead.');
        if (!$viewobj->attempts) {
            return '';
        }

        // Prepare table header.
        $table = new html_table();
        $table->attributes['class'] = 'generaltable quizattemptsummary';
        $table->caption = get_string('summaryofattempts', 'quiz');
        $table->captionhide = true;
        $table->head = [];
        $table->align = [];
        $table->size = [];
        if ($viewobj->attemptcolumn) {
            $table->head[] = get_string('attemptnumber', 'quiz');
            $table->align[] = 'center';
            $table->size[] = '';
        }
        $table->head[] = get_string('attemptstate', 'quiz');
        $table->align[] = 'left';
        $table->size[] = '';
        if ($viewobj->markcolumn) {
            $table->head[] = get_string('marks', 'quiz') . ' / ' .
                    quiz_format_grade($quiz, $quiz->sumgrades);
            $table->align[] = 'center';
            $table->size[] = '';
        }
        if ($viewobj->gradecolumn) {
            $table->head[] = get_string('gradenoun') . ' / ' .
                    quiz_format_grade($quiz, $quiz->grade);
            $table->align[] = 'center';
            $table->size[] = '';
        }
        if ($viewobj->canreviewmine) {
            $table->head[] = get_string('review', 'quiz');
            $table->align[] = 'center';
            $table->size[] = '';
        }
        if ($viewobj->feedbackcolumn) {
            $table->head[] = get_string('feedback', 'quiz');
            $table->align[] = 'left';
            $table->size[] = '';
        }

        // One row for each attempt.
        foreach ($viewobj->attemptobjs as $attemptobj) {
            $attemptoptions = $attemptobj->get_display_options(true);
            $row = [];

            // Add the attempt number.
            if ($viewobj->attemptcolumn) {
                if ($attemptobj->is_preview()) {
                    $row[] = get_string('preview', 'quiz');
                } else {
                    $row[] = $attemptobj->get_attempt_number();
                }
            }

            $row[] = $this->attempt_state($attemptobj);

            if ($viewobj->markcolumn) {
                if ($attemptoptions->marks >= question_display_options::MARK_AND_MAX &&
                        $attemptobj->is_finished()) {
                    $row[] = quiz_format_grade($quiz, $attemptobj->get_sum_marks());
                } else {
                    $row[] = '';
                }
            }

            // Outside the if because we may be showing feedback but not grades.
            $attemptgrade = quiz_rescale_grade($attemptobj->get_sum_marks(), $quiz, false);

            if ($viewobj->gradecolumn) {
                if ($attemptoptions->marks >= question_display_options::MARK_AND_MAX &&
                        $attemptobj->is_finished()) {

                    // Highlight the highest grade if appropriate.
                    if ($viewobj->overallstats && !$attemptobj->is_preview()
                            && $viewobj->numattempts > 1 && !is_null($viewobj->mygrade)
                            && $attemptobj->get_state() == quiz_attempt::FINISHED
                            && $attemptgrade == $viewobj->mygrade
                            && $quiz->grademethod == QUIZ_GRADEHIGHEST) {
                        $table->rowclasses[$attemptobj->get_attempt_number()] = 'bestrow';
                    }

                    $row[] = quiz_format_grade($quiz, $attemptgrade);
                } else {
                    $row[] = '';
                }
            }

            if ($viewobj->canreviewmine) {
                $row[] = $viewobj->accessmanager->make_review_link($attemptobj->get_attempt(),
                        $attemptoptions, $this);
            }

            if ($viewobj->feedbackcolumn && $attemptobj->is_finished()) {
                if ($attemptoptions->overallfeedback) {
                    $row[] = quiz_feedback_for_grade($attemptgrade, $quiz, $context);
                } else {
                    $row[] = '';
                }
            }

            if ($attemptobj->is_preview()) {
                $table->data['preview'] = $row;
            } else {
                $table->data[$attemptobj->get_attempt_number()] = $row;
            }
        } // End of loop over attempts.

        $output = '';
        $output .= $this->view_table_heading();
        $output .= html_writer::table($table);
        return $output;
    }

    /**
     * Generate a brief textual description of the current state of an attempt.
     *
     * @param quiz_attempt $attemptobj the attempt
     * @return string the appropriate lang string to describe the state.
     */
    public function attempt_state($attemptobj) {
        switch ($attemptobj->get_state()) {
            case quiz_attempt::IN_PROGRESS:
                return get_string('stateinprogress', 'quiz');

            case quiz_attempt::OVERDUE:
                return get_string('stateoverdue', 'quiz') . html_writer::tag('span',
                                get_string('stateoverduedetails', 'quiz',
                                        userdate($attemptobj->get_due_date())),
                                ['class' => 'statedetails']);

            case quiz_attempt::FINISHED:
                return get_string('statefinished', 'quiz') . html_writer::tag('span',
                                get_string('statefinisheddetails', 'quiz',
                                        userdate($attemptobj->get_submitted_date())),
                                ['class' => 'statedetails']);

            case quiz_attempt::ABANDONED:
                return get_string('stateabandoned', 'quiz');

            default:
                throw new coding_exception('Unexpected attempt state');
        }
    }

    /**
     * Generates data pertaining to quiz results
     *
     * @param stdClass $quiz Array containing quiz data
     * @param context_module $context The quiz context.
     * @param stdClass|cm_info $cm The course module information.
     * @param view_page $viewobj
     * @return string HTML to display.
     */
    public function view_result_info($quiz, $context, $cm, $viewobj) {
        $output = '';
        if (!$viewobj->numattempts && !$viewobj->gradecolumn && is_null($viewobj->mygrade)) {
            return $output;
        }
        $resultinfo = '';

        if ($viewobj->overallstats) {
            if ($viewobj->moreattempts) {
                $a = new stdClass();
                $a->method = quiz_get_grading_option_name($quiz->grademethod);
                $a->mygrade = quiz_format_grade($quiz, $viewobj->mygrade);
                $a->quizgrade = quiz_format_grade($quiz, $quiz->grade);
                $resultinfo .= $this->heading(get_string('gradesofar', 'quiz', $a), 3);
            } else {
                $a = new stdClass();
                $a->grade = quiz_format_grade($quiz, $viewobj->mygrade);
                $a->maxgrade = quiz_format_grade($quiz, $quiz->grade);
                $a = get_string('outofshort', 'quiz', $a);
                $resultinfo .= $this->heading(get_string('yourfinalgradeis', 'quiz', $a), 3);
            }
        }

        if ($viewobj->mygradeoverridden) {

            $resultinfo .= html_writer::tag('p', get_string('overriddennotice', 'grades'),
                            ['class' => 'overriddennotice']) . "\n";
        }
        if ($viewobj->gradebookfeedback) {
            $resultinfo .= $this->heading(get_string('comment', 'quiz'), 3);
            $resultinfo .= html_writer::div($viewobj->gradebookfeedback, 'quizteacherfeedback') . "\n";
        }
        if ($viewobj->feedbackcolumn) {
            $resultinfo .= $this->heading(get_string('overallfeedback', 'quiz'), 3);
            $resultinfo .= html_writer::div(
                            quiz_feedback_for_grade($viewobj->mygrade, $quiz, $context),
                            'quizgradefeedback') . "\n";
        }

        if ($resultinfo) {
            $output .= $this->box($resultinfo, 'generalbox', 'feedback');
        }
        return $output;
    }

    /**
     * Output either a link to the review page for an attempt, or a button to
     * open the review in a popup window.
     *
     * @param moodle_url $url of the target page.
     * @param bool $reviewinpopup whether a pop-up is required.
     * @param array $popupoptions options to pass to the popup_action constructor.
     * @return string HTML to output.
     */
    public function review_link($url, $reviewinpopup, $popupoptions) {
        if ($reviewinpopup) {
            $button = new single_button($url, get_string('review', 'quiz'));
            $button->add_action(new popup_action('click', $url, 'quizpopup', $popupoptions));
            return $this->render($button);

        } else {
            return html_writer::link($url, get_string('review', 'quiz'),
                    ['title' => get_string('reviewthisattempt', 'quiz')]);
        }
    }

    /**
     * Displayed where there might normally be a review link, to explain why the
     * review is not available at this time.
     *
     * @param string $message optional message explaining why the review is not possible.
     * @return string HTML to output.
     */
    public function no_review_message($message) {
        return html_writer::nonempty_tag('span', $message,
                ['class' => 'noreviewmessage']);
    }

    /**
     * Returns the same as {@see quiz_num_attempt_summary()} but wrapped in a link to the quiz reports.
     *
     * @param stdClass $quiz the quiz object. Only $quiz->id is used at the moment.
     * @param stdClass $cm the cm object. Only $cm->course, $cm->groupmode and $cm->groupingid
     * fields are used at the moment.
     * @param context $context the quiz context.
     * @param bool $returnzero if false (default), when no attempts have been made '' is returned
     *      instead of 'Attempts: 0'.
     * @param int $currentgroup if there is a concept of current group where this method is being
     *      called (e.g. a report) pass it in here. Default 0 which means no current group.
     * @return string HTML fragment for the link.
     */
    public function quiz_attempt_summary_link_to_reports($quiz, $cm, $context,
            $returnzero = false, $currentgroup = 0) {
        global $CFG;
        $summary = quiz_num_attempt_summary($quiz, $cm, $returnzero, $currentgroup);
        if (!$summary) {
            return '';
        }

        require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php');
        $url = new moodle_url('/mod/quiz/report.php', [
                'id' => $cm->id, 'mode' => quiz_report_default_report($context)]);
        return html_writer::link($url, $summary);
    }

    /**
     * Render a summary of the number of group and user overrides, with corresponding links.
     *
     * @param stdClass $quiz the quiz settings.
     * @param cm_info|stdClass $cm the cm object.
     * @param int $currentgroup currently selected group, if there is one.
     * @return string HTML fragment for the link.
     */
    public function quiz_override_summary_links(stdClass $quiz, cm_info|stdClass $cm, $currentgroup = 0): string {

        $baseurl = new moodle_url('/mod/quiz/overrides.php', ['cmid' => $cm->id]);
        $counts = quiz_override_summary($quiz, $cm, $currentgroup);

        $links = [];
        if ($counts['group']) {
            $links[] = html_writer::link(new moodle_url($baseurl, ['mode' => 'group']),
                    get_string('overridessummarygroup', 'quiz', $counts['group']));
        }
        if ($counts['user']) {
            $links[] = html_writer::link(new moodle_url($baseurl, ['mode' => 'user']),
                    get_string('overridessummaryuser', 'quiz', $counts['user']));
        }

        if (!$links) {
            return '';
        }

        $links = implode(', ', $links);
        switch ($counts['mode']) {
            case 'onegroup':
                return get_string('overridessummarythisgroup', 'quiz', $links);

            case 'somegroups':
                return get_string('overridessummaryyourgroups', 'quiz', $links);

            case 'allgroups':
                return get_string('overridessummary', 'quiz', $links);

            default:
                throw new coding_exception('Unexpected mode ' . $counts['mode']);
        }
    }

    /**
     * Outputs a chart.
     *
     * @param \core\chart_base $chart The chart.
     * @param string $title The title to display above the graph.
     * @param array $attrs extra container html attributes.
     * @return string HTML of the graph.
     */
    public function chart(\core\chart_base $chart, $title, $attrs = []) {
        return $this->heading($title, 3) . html_writer::tag('div',
                        $this->render($chart), array_merge(['class' => 'graph'], $attrs));
    }

    /**
     * Output a graph, or a message saying that GD is required.
     *
     * @param moodle_url $url the URL of the graph.
     * @param string $title the title to display above the graph.
     * @return string HTML of the graph.
     */
    public function graph(moodle_url $url, $title) {
        $graph = html_writer::empty_tag('img', ['src' => $url, 'alt' => $title]);

        return $this->heading($title, 3) . html_writer::tag('div', $graph, ['class' => 'graph']);
    }

    /**
     * Output the connection warning messages, which are initially hidden, and
     * only revealed by JavaScript if necessary.
     */
    public function connection_warning() {
        $options = ['filter' => false, 'newlines' => false];
        $warning = format_text(get_string('connectionerror', 'quiz'), FORMAT_MARKDOWN, $options);
        $ok = format_text(get_string('connectionok', 'quiz'), FORMAT_MARKDOWN, $options);
        return html_writer::tag('div', $warning,
                        ['id' => 'connection-error', 'style' => 'display: none;', 'role' => 'alert']) .
                html_writer::tag('div', $ok, ['id' => 'connection-ok', 'style' => 'display: none;', 'role' => 'alert']);
    }

    /**
     * Deprecated version of render_links_to_other_attempts.
     *
     * @param links_to_other_attempts $links
     * @return string HTML fragment.
     * @deprecated since Moodle 4.2. Please use render_links_to_other_attempts instead.
     * @todo MDL-76612 Final deprecation in Moodle 4.6
     */
    protected function render_mod_quiz_links_to_other_attempts(links_to_other_attempts $links) {
        return $this->render_links_to_other_attempts($links);
    }

    /**
     * Deprecated version of render_navigation_question_button.
     *
     * @param navigation_question_button $button
     * @return string HTML fragment.
     * @deprecated since Moodle 4.2. Please use render_links_to_other_attempts instead.
     * @todo MDL-76612 Final deprecation in Moodle 4.6
     */
    protected function render_quiz_nav_question_button(navigation_question_button $button) {
        return $this->render_navigation_question_button($button);
    }

    /**
     * Deprecated version of render_navigation_section_heading.
     *
     * @param navigation_section_heading $heading the heading.
     * @return string HTML fragment.
     * @deprecated since Moodle 4.2. Please use render_links_to_other_attempts instead.
     * @todo MDL-76612 Final deprecation in Moodle 4.6
     */
    protected function render_quiz_nav_section_heading(navigation_section_heading $heading) {
        return $this->render_navigation_section_heading($heading);
    }
}
