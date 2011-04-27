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
                $this->questions($attemptobj, true, $slots, $page, $showall, $displayoptions));

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

    public function review_form($summarydata, $page, $displayoptions, $content) {
        if ($displayoptions->flags != question_display_options::EDITABLE) {
            return $content;
        }

        $this->page->requires->js_init_call('M.mod_quiz.init_review_form', null, false,
                quiz_get_js_module());

        // TODO fix this to use html_writer.
        $output = '';
        $output .= '<form action="' . $attemptobj->review_url(0, $page, $showall) .
                '" method="post" class="questionflagsaveform"><div>';
        $output .= '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
        $output .= $content;
        $output .= '<div class="submitbtns">' . "\n" .
                '<input type="submit" class="questionflagsavebutton" name="savingflags" value="' .
                get_string('saveflags', 'question') . '" />' .
                "</div>\n" .
                "\n</div></form>\n";

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
                                                              mod_quiz_links_to_other_attempts
                                                              $links) {
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
        $output .= $this->quiz_notices($attemptobj, $accessmanager, $messages);
        $output .= $this->attempt_form($attemptobj, $page, $slots, $id, $nextpage);
        return $output;
    }

    private function quiz_notices($attemptobj, $accessmanager, $messages) {
        if ($attemptobj->is_preview_user() && $messages) {
            // Inform teachers of any restrictions that would apply to students at this point.
            $output = $this->box_start('quizaccessnotices');
            $output .= $this->heading(get_string('accessnoticesheader', 'quiz'), 3);
            $accessmanager->print_messages($messages);
            $output .= $this->box_end();

            return $output;
        }
    }

    private function attempt_form($attemptobj, $page, $slots, $id, $nextpage) {
        // Start the form
        //TODO: Convert all html to html:writer
        $output = '';
        $output .= '<form id="responseform" method="post" action="'.
                s($attemptobj->processattempt_url()).
                '" enctype="multipart/form-data" accept-charset="utf-8">'. "\n";
        $output .= '<div>';

        // Print all the questions
        foreach ($slots as $slot) {
            $output .= $attemptobj->render_question($slot, false, $attemptobj->attempt_url($id,
                    $page));
        }

        // Print a link to the next page.
        $output .= '<div class="submitbtns">';
        $output .= '<input type="submit" name="next" value="' . get_string('next') . '" />';
        $output .= "</div>";

        // Some hidden fields to trach what is going on.
        $output .= '<input type="hidden" name="attempt" value="' . $attemptobj->get_attemptid() .
                '" />';
        $output .= '<input type="hidden" name="thispage" id="followingpage" value="' . $page .
                '" />';
        $output .= '<input type="hidden" name="nextpage" value="' . $nextpage . '" />';
        $output .= '<input type="hidden" name="timeup" id="timeup" value="0" />';
        $output .= '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
        $output .= '<input type="hidden" name="scrollpos" id="scrollpos" value="" />';

        // Add a hidden field with questionids. Do this at the end of the form, so
        // if you navigate before the form has finished loading, it does not wipe all
        // the student's answers.
        $output .= '<input type="hidden" name="slots" value="' .
                implode(',', $slots) . "\" />\n";

        // Finish the form
        $output .= '</div>';
        $output .= "</form>\n";

        return $output;
    }

    public function print_message($attemptobj, $accessmanager, $messages) {
        print_error('attempterror', 'quiz', $attemptobj->view_url(),
                $accessmanager->print_messages($messages, true));
    }
}

class mod_quiz_links_to_other_attempts implements renderable {
    /**
     * @var array string attempt number => url, or null for the current attempt.
     */
    public $links = array();
}