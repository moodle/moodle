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

use html_writer;

/**
 * Specialisation of {@see navigation_panel_base} for the attempt quiz page.
 *
 * This class is not currently renderable or templatable, but it probably should be in the future,
 * which is why it is already in the output namespace.
 *
 * @package   mod_quiz
 * @category  output
 * @copyright 2008 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class navigation_panel_attempt extends navigation_panel_base {
    public function get_question_url($slot) {
        if ($this->attemptobj->can_navigate_to($slot)) {
            return $this->attemptobj->attempt_url($slot, -1, $this->page);
        } else {
            return null;
        }
    }

    public function render_before_button_bits(renderer $output) {
        return html_writer::tag('div', get_string('navnojswarning', 'quiz'),
                ['id' => 'quiznojswarning']);
    }

    public function render_end_bits(renderer $output) {
        if ($this->page == -1) {
            // Don't link from the summary page to itself.
            return '';
        }

        // We create a hidden div with an information message in order for the student
        // to known when their answers have been auto-saved.
        $html = html_writer::div(get_string('lastautosave', 'quiz', '-'), 'autosave_info', ['hidden' => 'hidden']);

        $html .= html_writer::link($this->attemptobj->summary_url(),
                get_string('endtest', 'quiz'), ['class' => 'endtestlink aalink']) .
                $this->render_restart_preview_link($output);

        return $html;
    }
}
