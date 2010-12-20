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
 * Renderer for outputting parts of a question belonging to the immediate
 * feedback with CBM behaviour.
 *
 * @package qbehaviour_immediatecbm
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../deferredcbm/renderer.php');


class qbehaviour_immediatecbm_renderer extends qbehaviour_deferredcbm_renderer {
    public function controls(question_attempt $qa, question_display_options $options) {
        $output = parent::controls($qa, $options);
        if ($qa->get_state() == question_state::$invalid && !$qa->get_last_step()->has_behaviour_var('certainty')) {
            $output .= html_writer::tag('div',
                    get_string('pleaseselectacertainty', 'qbehaviour_immediatecbm'),
                    array('class' => 'validationerror'));
        }
        $output .= $this->submit_button($qa, $options);
        return $output;
    }
}
