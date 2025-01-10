<?php

namespace theme_lms\output;

use block_contents;
use mod_quiz_display_options;
use stdClass;


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
//  public function get_question_buttons()
//  {
//    $buttons = array();
//    foreach ($this->attemptobj->get_slots() as $slot) {
//      $heading = $this->attemptobj->get_heading_before_slot($slot);
//      if (!is_null($heading)) {
//        $sections = $this->attemptobj->get_quizobj()->get_sections();
//        if (!(empty($heading) && count($sections) == 1)) {
//          $buttons[] = new quiz_nav_section_heading(format_string($heading));
//        }
//      }
//
//      $qa = $this->attemptobj->get_question_attempt($slot);
//      $showcorrectness = $this->options->correctness && $qa->has_marks();
//
//      $button = new quiz_nav_question_button();
//      $button->id = 'quiznavbutton' . $slot;
//      $button->number = $this->attemptobj->get_question_number($slot);
//      $button->stateclass = $qa->get_state_class($showcorrectness);
//      $button->navmethod = $this->attemptobj->get_navigation_method();
//      if (!$showcorrectness && $button->stateclass === 'notanswered') {
//        $button->stateclass = 'complete';
//      }
//      $button->statestring = $this->get_state_string($qa, $showcorrectness);
//      $button->page = $this->attemptobj->get_question_page($slot);
//      $button->currentpage = $this->showall || $button->page == $this->page;
//      $button->flagged = $qa->is_flagged();
//      $button->url = $this->get_question_url($slot);
//      if ($this->attemptobj->is_blocked_by_previous_question($slot)) {
//        $button->url = null;
//        $button->stateclass = 'blocked';
//        $button->statestring = get_string('questiondependsonprevious', 'quiz');
//      }
//      $buttons[] = $button;
//    }
//
//    return $buttons;
//  }
  public function get_display_options($reviewing) {
    if ($reviewing) {
      if (is_null($this->reviewoptions)) {
        $this->reviewoptions = quiz_get_review_options($this->get_quiz(),
          $this->attempt, $this->quizobj->get_context());
        if ($this->is_own_preview()) {
          // It should  always be possible for a teacher to review their
          // own preview irrespective of the review options settings.
          $this->reviewoptions->attempt = true;
        }
      }
      return $this->reviewoptions;

    } else {
      $options = mod_quiz_display_options::make_from_quiz($this->get_quiz(),
        mod_quiz_display_options::DURING);
      $options->flags = quiz_get_flag_option($this->attempt, $this->quizobj->get_context());
      return $options;
    }
  }
  public function get_navigation_panel(mod_quiz_renderer $output,
                                                         $panelclass, $page, $showall = false) {
    $panel = new $panelclass($this, $this->get_display_options(true), $page, $showall);

    $bc = new block_contents();
    $bc->attributes['id'] = 'mod_quiz_navblock';
    $bc->attributes['role'] = 'navigation';
    $bc->title = get_string('quiznavigation', 'quiz');
    $bc->content = $output->navigation_panel($panel);
    return $bc;
  }
}