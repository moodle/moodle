<?php
namespace theme_lms\output;
use stdClass;
use html_writer;
use html_table;
use question_display_options;
defined('MOODLE_INTERNAL') || die;

class mod_quiz_renderer extends \mod_quiz_renderer {
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
     * @param mod_quiz_view_object $viewobj
     * @return string HTML to display
     */
    public function view_page($course, $quiz, $cm, $context, $viewobj) {

        // print_object($viewobj);
        // die;
        // quiz_format_grade($quiz, $viewobj->mygrade)
        // quiz_format_grade($quiz, $quiz->grade)
        $output = '';

        $output .= $this->view_page_tertiary_nav($viewobj);
        $output .= $this->view_information($quiz, $cm, $context, $viewobj->infomessages);
        $output .= $this->card_grade($quiz, $viewobj);
        $output .= $this->view_table($quiz, $context, $viewobj);
        $output .= $this->view_result_info($quiz, $context, $cm, $viewobj);
        $output .= $this->box($this->view_page_buttons($viewobj), 'quizattempt');
        return $output;
    }


     /**
     * Generates the table of data
     *
     * @param array $quiz Array contining quiz data
     * @param int $context The page context ID
     * @param mod_quiz_view_object $viewobj
     */
    public function view_table($quiz, $context, $viewobj) {
        if (!$viewobj->attempts) {
            return '';
        }

        // Prepare table header.
        $table = new html_table();
        $table->attributes['class'] = 'generaltable quizattemptsummary';
        $table->head = array();
        $table->align = array();
        $table->size = array();
        $table->caption = get_string('summaryofattempts', 'quiz');
        $table->captionhide = true;
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
            $row = array();

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

            // Ouside the if because we may be showing feedback but not grades.
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

    function card_grade($quiz, $viewobj)
    {
        $mygrade = quiz_format_grade($quiz, $viewobj->mygrade);
        $quizgrade = quiz_format_grade($quiz, $quiz->grade);
        $cardGrade = html_writer::div(
            html_writer::div(
                html_writer::tag('h3', 'Đạt', ['class' => 'card-text, title-course']) .
                html_writer::tag('p', $mygrade . '%', ['class' => 'grade-percent text-lms']) .
                html_writer::tag('p', 'Tối thiểu 60%', ['class' => 'grade-min-percent'])
            , 'card-body')
        , 'card card-grade');

        $cardPoint = html_writer::div(
            html_writer::div(
                html_writer::tag('h3', 'Điểm số', ['class' => 'card-text, title-course']) .
                html_writer::tag('p', '97', ['class' => 'grade-percent text-lms']) .
                html_writer::tag('p', 'Tối đa 100 điểm', ['class' => 'grade-min-percent'])
            , 'card-body')
        , 'card card-grade');

        $cardAccuracy = html_writer::div(
            html_writer::div(
                html_writer::tag('h3', 'Độ chính xác', ['class' => 'card-text, title-course']) .
                html_writer::tag('p', '97%', ['class' => 'grade-percent text-lms'])
            , 'card-body')
        , 'card card-grade');

        $wrap = html_writer::div(
            $cardGrade . $cardPoint . $cardAccuracy
        , 'wrap-card');

        return $wrap;
    }
//  protected function render_quiz_nav_question_button(quiz_nav_question_button $button) {
//    $classes = array('qnbutton', $button->stateclass, $button->navmethod, 'btn');
//    $extrainfo = array();
//
//    if ($button->currentpage) {
//      $classes[] = 'thispage';
//      $extrainfo[] = get_string('onthispage', 'quiz');
//    }
//
//    // Flagged?
//    if ($button->flagged) {
//      $classes[] = 'flagged';
//      $flaglabel = get_string('flagged', 'question');
//    } else {
//      $flaglabel = '';
//    }
//    $extrainfo[] = html_writer::tag('span', $flaglabel, array('class' => 'flagstate'));
//
//    if (is_numeric($button->number)) {
//      $qnostring = 'questionnonav';
//    } else {
//      $qnostring = 'questionnonavinfo';
//    }
//
//    $a = new stdClass();
//    $a->number = $button->number;
//    $a->attributes = implode(' ', $extrainfo);
//    $tagcontents = html_writer::tag('span', '', array('class' => 'thispageholder')) .
//      get_string($qnostring, 'quiz', $a);
//    $tagattributes = array('class' => implode(' ', $classes), 'id' => $button->id,
//                           'title' => $button->statestring, 'data-quiz-page' => $button->page);
//
//    if ($button->url) {
//      return html_writer::link($button->url, $tagcontents, $tagattributes);
//    } else {
//      return html_writer::tag('span', $tagcontents, $tagattributes);
//    }
//  }

}