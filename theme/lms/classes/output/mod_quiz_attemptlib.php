<?php

namespace theme_lms\output;

use stdClass;
use html_writer;
use html_table;
use question_display_options;

defined('MOODLE_INTERNAL') || die;

class mod_quiz_attemptlib extends \quiz_attempt_nav_panel
{
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
  public function get_question_buttons()
  {
    $buttons = array();
    foreach ($this->attemptobj->get_slots() as $slot) {
      $heading = $this->attemptobj->get_heading_before_slot($slot);
      if (!is_null($heading)) {
        $sections = $this->attemptobj->get_quizobj()->get_sections();
        if (!(empty($heading) && count($sections) == 1)) {
          $buttons[] = new quiz_nav_section_heading(format_string($heading));
        }
      }

      $qa = $this->attemptobj->get_question_attempt($slot);
      $showcorrectness = $this->options->correctness && $qa->has_marks();

      $button = new quiz_nav_question_button();
      $button->id = 'quiznavbutton' . $slot;
      $button->number = $this->attemptobj->get_question_number($slot);
      $button->stateclass = $qa->get_state_class($showcorrectness);
      $button->navmethod = $this->attemptobj->get_navigation_method();
      if (!$showcorrectness && $button->stateclass === 'notanswered') {
        $button->stateclass = 'complete';
      }
      $button->statestring = $this->get_state_string($qa, $showcorrectness);
      $button->page = $this->attemptobj->get_question_page($slot);
      $button->currentpage = $this->showall || $button->page == $this->page;
      $button->flagged = $qa->is_flagged();
      $button->url = $this->get_question_url($slot);
      if ($this->attemptobj->is_blocked_by_previous_question($slot)) {
        $button->url = null;
        $button->stateclass = 'blocked';
        $button->statestring = get_string('questiondependsonprevious', 'quiz');
      }
      $buttons[] = $button;
    }

    return $buttons;
  }
}