<?php
namespace theme_lms\output;
use html_writer;

defined('MOODLE_INTERNAL') || die();
class mod_quiz_quiz_attempt_nav_panel extends \quiz_attempt_nav_panel {
    public function render_end_bits(\mod_quiz_renderer $output) {
        if ($this->page == -1) {
            // Don't link from the summary page to itself.
            return '';
        }
        return html_writer::link($this->attemptobj->summary_url(),
                get_string('endtest', 'quiz'), array('class' => 'endtestlink aalink btn btn-danger')) .
                $this->render_restart_preview_link($output);
    }
}
