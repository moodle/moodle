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
 * Defines the renderer for the quiz module.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * The renderer for the quiz module.
 *
 * @copyright  2008 onwards Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_renderer extends plugin_renderer_base {
    public function review_page(quiz_attempt $attemptobj, $slots, $page, $showall,
                                $lastpage, mod_quiz_display_options $displayoptions,
                                $summarydata) {

        $output = '';
        $output .= $this->header();
        $output .= $this->review_summary_table($summarydata, $page);
        $output .= $this->review_form($page, $showall, $displayoptions,
                $this->questions($attemptobj, true, $slots, $page, $showall, $displayoptions),
                $attemptobj, $showall);

        $output .= $this->review_next_navigation($attemptobj, $page, $lastpage);
        $output .= $this->footer();
        return $output;
    }

    protected function filter_summary_table($summarydata, $page) {
        if ($page == 0) {
            return $summarydata;
        }

        // Only show some of summary table on subsequent pages.
        foreach ($summarydata as $key => $rowdata) {
            if (!in_array($key, array('user', 'attemptlist'))) {
                unset($summarydata[$key]);
            }
        }

        return $summarydata;
    }

    public function review_summary_table($summarydata, $page) {
                                         $summarydata = $this->filter_summary_table($summarydata,
                                         $page);
        if (empty($summarydata)) {
            return '';
        }

        $output = '';
        $output .= html_writer::start_tag('table', array(
                'class' => 'generaltable generalbox quizreviewsummary'));
        $output .= html_writer::start_tag('tbody');
        foreach ($summarydata as $rowdata) {
            if ($rowdata['title'] instanceof renderable) {
                $title = $this->render($rowdata['title']);
            } else {
                $title = $rowdata['title'];
            }

            if ($rowdata['content'] instanceof renderable) {
                $content = $this->render($rowdata['content']);
            } else {
                $content = $rowdata['content'];
            }

            $output .= html_writer::tag('tr',
                html_writer::tag('th', $title, array('class' => 'cell', 'scope' => 'row')) .
                        html_writer::tag('td', $content, array('class' => 'cell'))
            );
        }

        $output .= html_writer::end_tag('tbody');
        $output .= html_writer::end_tag('table');
        return $output;
    }

    public function questions(quiz_attempt $attemptobj, $reviewing, $slots, $page, $showall,
                              mod_quiz_display_options $displayoptions) {
        $output = '';
        foreach ($slots as $slot) {
            $output .= $attemptobj->render_question($slot, $reviewing,
                    $attemptobj->review_url($slot, $page, $showall));
        }
        return $output;
    }

    public function review_form($summarydata, $page, $displayoptions, $content, $attemptobj,
                                $showall) {
        if ($displayoptions->flags != question_display_options::EDITABLE) {
            return $content;
        }

        $this->page->requires->js_init_call('M.mod_quiz.init_review_form', null, false,
                quiz_get_js_module());

        $output = '';
        $output .= html_writer::start_tag('form', array('action' => $attemptobj->review_url(0,
                $page, $showall), 'method' => 'post', 'class' => 'questionflagsaveform'));
        $output .= html_writer::start_tag('div');
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey',
                'value' => sesskey()));
        $output .= html_writer::start_tag('div', array('class' => 'submitbtns'));
        $output .= html_writer::empty_tag('input', array('type' => 'submit',
                'class' => 'questionflagsavebutton', 'name' => 'savingflags',
                'value' => get_string('saveflags', 'question')));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('form');

        return $output;
    }

    public function finish_review_link($url) {
        if ($this->page->pagelayout == 'popup') {
            // In a 'secure' popup window.
            $this->page->requires->js_init_call('M.mod_quiz.secure_window.init_close_button',
                    array($url), quiz_get_js_module());
            return html_writer::empty_tag('input', array('type' => 'button',
                    'value' => get_string('finishreview', 'quiz'),
                    'id' => 'secureclosebutton'));
        } else {
            return html_writer::link($url, get_string('finishreview', 'quiz'));
        }
    }

    public function review_next_navigation(quiz_attempt $attemptobj, $page, $lastpage) {
        if ($lastpage) {
            $nav = $this->finish_review_link($attemptobj->view_url());
        } else {
            $nav = link_arrow_right(get_string('next'), $attemptobj->review_url(0, $page + 1));
        }
        return html_writer::tag('div', $nav, array('class' => 'submitbtns'));
    }

    /**
     * Return the HTML of the quiz timer.
     * @return string HTML content.
     */
    public function countdown_timer() {
        return html_writer::tag('div', get_string('timeleft', 'quiz') .
                html_writer::tag('span', '', array('id' => 'quiz-time-left')),
                array('id' => 'quiz-timer'));
    }

    public function restart_preview_button($url) {
        return $this->single_button($url, get_string('startnewpreview', 'quiz'));
    }

    public function navigation_panel(quiz_nav_panel_base $panel) {

        $output = '';
        $userpicture = $panel->user_picture();
        if ($userpicture) {
            $output .= html_writer::tag('div', $this->render($userpicture),
                    array('id' => 'user-picture', 'class' => 'clearfix'));
        }
        $output .= $panel->render_before_button_bits($this);

        $output = html_writer::start_tag('div', array('class' => 'qn_buttons'));
        foreach ($panel->get_question_buttons() as $button) {
            $output .= $this->render($button);
        }
        $output .= html_writer::end_tag('div');

        $output .= html_writer::tag('div', $panel->render_end_bits($this),
                array('class' => 'othernav'));

        $this->page->requires->js_init_call('M.mod_quiz.nav.init', null, false,
                quiz_get_js_module());

        return $output;
    }

    protected function render_quiz_nav_question_button(quiz_nav_question_button $button) {
        $classes = array('qnbutton', $button->stateclass);
        $attributes = array();

        if ($button->currentpage) {
            $classes[] = 'thispage';
            $attributes[] = get_string('onthispage', 'quiz');
        }

        $attributes[] = $button->statestring;

        // Flagged?
        if ($button->flagged) {
            $classes[] = 'flagged';
            $flaglabel = get_string('flagged', 'question');
        } else {
            $flaglabel = '';
        }
        $attributes[] = html_writer::tag('span', $flaglabel, array('class' => 'flagstate'));

        if (is_numeric($button->number)) {
            $qnostring = 'questionnonav';
        } else {
            $qnostring = 'questionnonavinfo';
        }

        $a = new stdClass();
        $a->number = $button->number;
        $a->attributes = implode(' ', $attributes);

        return html_writer::link($button->url,
                html_writer::tag('span', '', array('class' => 'thispageholder')) .
                html_writer::tag('span', '', array('class' => 'trafficlight')) .
                get_string($qnostring, 'quiz', $a),
                array('class' => implode(' ', $classes), 'id' => $button->id,
                        'title' => $button->statestring));
    }

    protected function render_mod_quiz_links_to_other_attempts(
            mod_quiz_links_to_other_attempts $links) {
        $attemptlinks = array();
        foreach ($links->links as $attempt => $url) {
            if ($url) {
                $attemptlinks[] = html_writer::link($url, $attempt);
            } else {
                $attemptlinks[] = html_writer::tag('strong', $attempt);
            }
        }
        return implode(', ', $attemptlinks);
    }

    /*
     * Attempt Page
     */
    public function attempt_page($attemptobj, $page, $accessmanager, $messages, $slots, $id,
            $nextpage) {
        $output = '';
        $output .= $this->quiz_notices($messages);
        $output .= $this->attempt_form($attemptobj, $page, $slots, $id, $nextpage);
        return $output;
    }

    private function quiz_notices($messages) {
        if (!$messages) {
            return '';
        }
        return $this->box($this->heading(get_string('accessnoticesheader', 'quiz'), 3) .
        $this->access_messages($messages), 'quizaccessnotices');
    }

    private function attempt_form($attemptobj, $page, $slots, $id, $nextpage) {
        $output = '';

        //Start Form
        $output .= html_writer::start_tag('form',
                array('action' => $attemptobj->processattempt_url(), 'method' => 'post',
                'enctype' => 'multipart/form-data', 'accept-charset' => 'utf-8',
                'id' => 'responseform'));
        $output .= html_writer::start_tag('div');

        // Print all the questions
        foreach ($slots as $slot) {
            $output .= $attemptobj->render_question($slot, false, $attemptobj->attempt_url($id,
                    $page));
        }

        $output .= html_writer::start_tag('div', array('class' => 'submitbtns'));
        $output .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'next',
                'value' => get_string('next')));
        $output .= html_writer::end_tag('div');

        // Some hidden fields to trach what is going on.
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'attempt',
                'value' => $attemptobj->get_attemptid()));
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'thispage',
                'value' => $page, 'id' => 'followingpage'));
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'nextpage',
                'value' => $nextpage));
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'timeup',
                'value' => '0', 'id' => 'timeup'));
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey',
                'value' => sesskey()));
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'scrollpos',
                'value' => '', 'id' => 'scrollpos'));

        // Add a hidden field with questionids. Do this at the end of the form, so
        // if you navigate before the form has finished loading, it does not wipe all
        // the student's answers.
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'slots',
                'value' => implode(',', $slots)));

        //Finish form
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('form');

        return $output;
    }
    
    /**
     * Print each message in an array, surrounded by &lt;p>, &lt;/p> tags.
     *
     * @param array $messages the array of message strings.
     * @param bool $return if true, return a string, instead of outputting.
     *
     * @return mixed, if $return is true, return the string that would have been output, otherwise
     * return null.
     */
    public function access_messages($messages) {
        $output = '';
        foreach ($messages as $message) {
            $output .= html_writer::tag('p', $message) . "\n";
        }
        return $output;
    }

    /*
     * Summary Page
     */
    public function summary_page($attemptobj, $displayoptions) {
        $output = '';
        $output .= $this->summary_table($attemptobj, $displayoptions);
        $output .= $this->summary_page_controls($attemptobj);
        return $output;
    }

    private function summary_table($attemptobj, $displayoptions) {
        // Prepare the summary table header
        $table = new html_table();
        $table->attributes['class'] = 'generaltable quizsummaryofattempt boxaligncenter';
        $table->head = array(get_string('question', 'quiz'), get_string('status', 'quiz'));
        $table->align = array('left', 'left');
        $table->size = array('', '');
        $markscolumn = $displayoptions->marks >= question_display_options::MARK_AND_MAX;
        if ($markscolumn) {
            $table->head[] = get_string('marks', 'quiz');
            $table->align[] = 'left';
            $table->size[] = '';
        }
        $table->data = array();

        // Get the summary info for each question.
        $slots = $attemptobj->get_slots();
        foreach ($slots as $slot) {
            if (!$attemptobj->is_real_question($slot)) {
                continue;
            }
            $flag = '';
            if ($attemptobj->is_question_flagged($slot)) {
                $flag = html_writer::empty_tag('img', array('src' => $this->pix_url('i/flagged'),
                        'alt' => get_string('flagged', 'question'), 'class' => 'questionflag'));
            }
            $row = array(html_writer::start_tag('a',
                    array('href' => $attemptobj->attempt_url($slot))).
                    $attemptobj->get_question_number($slot).$flag.html_writer::end_tag('a'),
                    $displayoptions->correctness);
            if ($markscolumn) {
                $row[] = $attemptobj->get_question_mark($slot);
            }
            $table->data[] = $row;
        }

        // Print the summary table.
        $output = html_writer::table($table);

        return $output;
    }

    private function summary_page_controls($attemptobj) {
        $output = '';
        // countdown timer
        $output .= $this->summary_get_timer($attemptobj);

        // Finish attempt button.
        $output .= $this->container_start('submitbtns mdl-align');
        $options = array(
            'attempt' => $attemptobj->get_attemptid(),
            'finishattempt' => 1,
            'timeup' => 0,
            'slots' => '',
            'sesskey' => sesskey(),
        );

        $button = new single_button(
                new moodle_url($attemptobj->processattempt_url(), $options),
                get_string('submitallandfinish', 'quiz'));
        $button->id = 'responseform';
        $button->add_confirm_action(get_string('confirmclose', 'quiz'));

        $output .= $this->container_start('controls');
        $output .= $this->render($button);
        $output .= $this->container_end();
        $output .= $this->container_end();

        return $output;
    }
    
    private function summary_get_timer($attemptobj){
        return $attemptobj->get_timer_html();
    }

    /*
     * View Page
     */
    public function view_page($course, $quiz, $cm, $context, $viewobj){
        $output = '';
        $output .= $this->view_information($quiz, $cm, $context, $viewobj);
        $output .= $this->view_table($quiz, $context, $viewobj);
        $output .= $this->view_best_score($viewobj);
        $output .= $this->view_result_info($quiz, $context, $cm, $viewobj);
        $output .= $this->view_attempt_button($course, $quiz, $cm, $context, $viewobj);
        return $output;
    }
    
    private function view_information($quiz, $cm, $context, $viewobj){
        global $CFG;
        $output = '';
        // Print quiz name and description
        $output .= $this->heading(format_string($quiz->name));
        if (trim(strip_tags($quiz->intro))) {
            $output .= $this->box(format_module_intro('quiz', $quiz, $cm->id), 'generalbox', 'intro');
        }

        // Display information about this quiz.
        $messages = $viewobj->accessmanager->describe_rules();
        if ($quiz->attempts != 1) {
            $messages[] = get_string('gradingmethod', 'quiz',
                    quiz_get_grading_option_name($quiz->grademethod));
        }
        $output .= $this->box_start('quizinfo');
        $this->access_messages($messages);
        $output .= $this->box_end();

        // Show number of attempts summary to those who can view reports.
        if (has_capability('mod/quiz:viewreports', $context)) {
            if ($strattemptnum = quiz_attempt_summary_link_to_reports($quiz, $cm, $context)) {
                $output .= '<div class="quizattemptcounts">' . $strattemptnum . "</div>\n";
            }
        }

        // Guests can't do a quiz, so offer them a choice of logging in or going back.
        if (isguestuser()) {
            echo $this->confirm('<p>' . get_string('guestsno', 'quiz') . "</p>\n\n<p>" .
                    get_string('liketologin') . "</p>\n", get_login_url(), get_referer(false));
            echo $this->footer();
            exit;
        }

        // If they are not enrolled in this course in a good enough role, tell them to enrol.
        if (!($viewobj->canattempt || $viewobj->canpreview || $viewobj->canreviewmine)) {
            echo $this->box('<p>' . get_string('youneedtoenrol', 'quiz') . "</p>\n\n<p>" .
                    $this->continue_button($CFG->wwwroot . '/course/view.php?id=' . $course->id) .
                    "</p>\n", 'generalbox', 'notice');
            echo $this->footer();
            exit;
        }
        return $output;
    }

    private function view_table($quiz, $context, $viewobj){
        $output = '';
        if ($viewobj->attempts) {
            $output .= $this->heading(get_string('summaryofattempts', 'quiz'));

            // Prepare table header
            $table = new html_table();
            $table->attributes['class'] = 'generaltable quizattemptsummary';
            $table->head = array();
            $table->align = array();
            $table->size = array();
            if ($viewobj->attemptcolumn) {
                $table->head[] = get_string('attemptnumber', 'quiz');
                $table->align[] = 'center';
                $table->size[] = '';
            }
            $table->head[] = get_string('timecompleted', 'quiz');
            $table->align[] = 'left';
            $table->size[] = '';
            if ($viewobj->markcolumn) {
                $table->head[] = get_string('marks', 'quiz') . ' / ' .
                        quiz_format_grade($quiz, $quiz->sumgrades);
                $table->align[] = 'center';
                $table->size[] = '';
            }
            if ($viewobj->gradecolumn) {
                $table->head[] = get_string('grade') . ' / ' .
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
            if (isset($quiz->showtimetaken)) {
                $table->head[] = get_string('timetaken', 'quiz');
                $table->align[] = 'left';
                $table->size[] = '';
            }

            // One row for each attempt
            foreach ($viewobj->attempts as $attempt) {
                $attemptoptions = quiz_get_review_options($quiz, $attempt, $context);
                $row = array();
        
                // Add the attempt number, making it a link, if appropriate.
                if ($viewobj->attemptcolumn) {
                    if ($attempt->preview) {
                        $row[] = get_string('preview', 'quiz');
                    } else {
                        $row[] = $attempt->attempt;
                    }
                }

                // prepare strings for time taken and date completed
                $timetaken = '';
                $datecompleted = '';
                if ($attempt->timefinish > 0) {
                    // attempt has finished
                    $timetaken = format_time($attempt->timefinish - $attempt->timestart);
                    $datecompleted = userdate($attempt->timefinish);
                } else if (!$quiz->timeclose || $viewobj->timenow < $quiz->timeclose) {
                    // The attempt is still in progress.
                    $timetaken = format_time($viewobj->timenow - $attempt->timestart);
                    $datecompleted = get_string('inprogress', 'quiz');
                } else {
                    $timetaken = format_time($quiz->timeclose - $attempt->timestart);
                    $datecompleted = userdate($quiz->timeclose);
                }
                $row[] = $datecompleted;

                if ($viewobj->markcolumn && $attempt->timefinish > 0) {
                    if ($attemptoptions->marks >= question_display_options::MARK_AND_MAX) {
                        $row[] = quiz_format_grade($quiz, $attempt->sumgrades);
                    } else {
                        $row[] = '';
                    }
                }

                // Ouside the if because we may be showing feedback but not grades.
                $attemptgrade = quiz_rescale_grade($attempt->sumgrades, $quiz, false);

                if ($viewobj->gradecolumn) {
                    if ($attemptoptions->marks >= question_display_options::MARK_AND_MAX &&
                            $attempt->timefinish > 0) {
                        $formattedgrade = quiz_format_grade($quiz, $attemptgrade);
                        // highlight the highest grade if appropriate
                        if ($viewobj->overallstats && !$attempt->preview && $viewobj->numattempts > 1 && !is_null($viewobj->mygrade) &&
                                $attemptgrade == $viewobj->mygrade && $quiz->grademethod == QUIZ_GRADEHIGHEST) {
                            $table->rowclasses[$attempt->attempt] = 'bestrow';
                        }
        
                        $row[] = $formattedgrade;
                    } else {
                        $row[] = '';
                    }
                }

                if ($viewobj->canreviewmine) {
                    $row[] = $viewobj->accessmanager->make_review_link($attempt, $viewobj->canpreview, $attemptoptions);
                }

                if ($viewobj->feedbackcolumn && $attempt->timefinish > 0) {
                    if ($attemptoptions->overallfeedback) {
                        $row[] = quiz_feedback_for_grade($attemptgrade, $quiz, $context, $cm);
                    } else {
                        $row[] = '';
                    }
                }

                if (isset($quiz->showtimetaken)) {
                    $row[] = $timetaken;
                }

                if ($attempt->preview) {
                    $table->data['preview'] = $row;
                } else {
                    $table->data[$attempt->attempt] = $row;
                }
            } // End of loop over attempts.
            $output .= html_writer::table($table);
        }
        return $output;
    }

    private function view_best_score($viewobj){
        $output = '';
        // Print information about the student's best score for this quiz if possible.
        if (!$viewobj->moreattempts) {
            $output .= $this->heading(get_string('nomoreattempts', 'quiz'));
        }
        return $output;
    }

    private function view_result_info($quiz, $context, $cm, $viewobj){
        $output = '';
        if ($viewobj->numattempts && $viewobj->gradecolumn && !is_null($viewobj->mygrade)) {
            $resultinfo = '';
        
            if ($viewobj->overallstats) {
                if ($viewobj->moreattempts) {
                    $a = new stdClass();
                    $a->method = quiz_get_grading_option_name($quiz->grademethod);
                    $a->mygrade = quiz_format_grade($quiz, $viewobj->mygrade);
                    $a->quizgrade = quiz_format_grade($quiz, $quiz->grade);
                    $resultinfo .= $this->heading(get_string('gradesofar', 'quiz', $a), 2, 'main');
                } else {
                    $a = new stdClass();
                    $a->grade = quiz_format_grade($quiz, $viewobj->mygrade);
                    $a->maxgrade = quiz_format_grade($quiz, $quiz->grade);
                    $a = get_string('outofshort', 'quiz', $a);
                    $resultinfo .= $this->heading(get_string('yourfinalgradeis', 'quiz', $a), 2, 'main');
                }
            }
        
            if ($viewobj->mygradeoverridden) {
                $resultinfo .= '<p class="overriddennotice">' .
                        get_string('overriddennotice', 'grades') . "</p>\n";
            }
            if ($viewobj->gradebookfeedback) {
                $resultinfo .= $this->heading(get_string('comment', 'quiz'), 3, 'main');
                $resultinfo .= '<p class="quizteacherfeedback">'.$viewobj->gradebookfeedback."</p>\n";
            }
            if ($viewobj->feedbackcolumn) {
                $resultinfo .= $this->heading(get_string('overallfeedback', 'quiz'), 3, 'main');
                $resultinfo .= '<p class="quizgradefeedback">' .
                        quiz_feedback_for_grade($viewobj->mygrade, $quiz, $context, $cm) . "</p>\n";
            }
        
            if ($resultinfo) {
                $output .= $this->box($resultinfo, 'generalbox', 'feedback');
            }
        }
        return $output;
    }

    private function view_attempt_button($course, $quiz, $cm, $context, $viewobj){
        $output = '';
        // Determine if we should be showing a start/continue attempt button,
        // or a button to go back to the course page.
        $output .= $this->box_start('quizattempt');
        $output .= 'TEST';
        $buttontext = ''; // This will be set something if as start/continue attempt button should appear.
        if (!quiz_clean_layout($quiz->questions, true)) {
            $output .= quiz_no_questions_message($quiz, $cm, $context);
            $buttontext = '';
        
        } else {
            if ($viewobj->unfinished) {
                if ($viewobj->canattempt) {
                    $buttontext = get_string('continueattemptquiz', 'quiz');
                } else if ($viewobj->canpreview) {
                    $buttontext = get_string('continuepreview', 'quiz');
                }
        
            } else {
                if ($viewobj->canattempt) {
                    $messages = $viewobj->accessmanager->prevent_new_attempt($viewobj->numattempts, $viewobj->lastfinishedattempt);
                    if ($messages) {
                        $this->access_messages($messages);
                    } else if ($viewobj->numattempts == 0) {
                        $buttontext = get_string('attemptquiznow', 'quiz');
                    } else {
                        $buttontext = get_string('reattemptquiz', 'quiz');
                    }
        
                } else if ($viewobj->canpreview) {
                    $buttontext = get_string('previewquiznow', 'quiz');
                }
            }
        
            // If, so far, we think a button should be printed, so check if they will be
            // allowed to access it.
            if ($buttontext) {
                if (!$viewobj->moreattempts) {
                    $buttontext = '';
                } else if ($viewobj->canattempt && $messages = $viewobj->accessmanager->prevent_access()) {
                    $this->access_messages($messages);
                    $buttontext = '';
                }
            }
        }
        
        // Now actually print the appropriate button.
        if ($buttontext) {
            $output .= $viewobj->accessmanager->print_start_attempt_button($viewobj->canpreview, $buttontext, $viewobj->unfinished);
        } else if ($buttontext === '') {
            $output .= $this->single_button(new moodle_url('/course/view.php', array('id' => $course->id)),
                    get_string('backtocourse', 'quiz'), 'get', array('class' => 'continuebutton'));
        }
        $output .= $this->box_end();

        return $output;
    }
}

class mod_quiz_links_to_other_attempts implements renderable {
    /**
     * @var array string attempt number => url, or null for the current attempt.
     */
    public $links = array();
}