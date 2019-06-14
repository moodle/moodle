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
 * Renderer for outputting parts of a question belonging to the interactive
 * behaviour.
 *
 * @package    qbehaviour
 * @subpackage interactive
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Interactive behaviour renderer.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_interactive_renderer extends qbehaviour_renderer {
    public function controls(question_attempt $qa, question_display_options $options) {
        if ($options->readonly === qbehaviour_interactive::TRY_AGAIN_VISIBLE ||
                $options->readonly === qbehaviour_interactive::TRY_AGAIN_VISIBLE_READONLY) {
            // We are in the try again state, so no submit button.
            return '';
        }
        return $this->submit_button($qa, $options);
    }

    public function feedback(question_attempt $qa, question_display_options $options) {
        // Show the Try again button if we are in try-again state.
        if (!$qa->get_state()->is_active() ||
                ($options->readonly !== qbehaviour_interactive::TRY_AGAIN_VISIBLE &&
                        $options->readonly !== qbehaviour_interactive::TRY_AGAIN_VISIBLE_READONLY)) {
            return '';
        }

        $attributes = array(
            'type' => 'submit',
            'id' => $qa->get_behaviour_field_name('tryagain'),
            'name' => $qa->get_behaviour_field_name('tryagain'),
            'value' => get_string('tryagain', 'qbehaviour_interactive'),
            'class' => 'submit btn',
        );
        if ($options->readonly === qbehaviour_interactive::TRY_AGAIN_VISIBLE_READONLY) {
            // This means the question really was rendered with read-only option.
            $attributes['disabled'] = 'disabled';
        }
        $output = html_writer::empty_tag('input', $attributes);
        if (empty($attributes['disabled'])) {
            $this->page->requires->js_init_call('M.core_question_engine.init_submit_button',
                    array($attributes['id'], $qa->get_slot()));
        }
        return $output;
    }
}
