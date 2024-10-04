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
 * Drag-and-drop markers question renderer class.
 *
 * @package   qtype_ddmarker
 * @copyright 2012 The Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/rendererbase.php');
require_once($CFG->dirroot . '/question/type/ddimageortext/rendererbase.php');


/**
 * Generates the output for drag-and-drop markers questions.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_renderer extends qtype_ddtoimage_renderer_base {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();
        $componentname = $question->qtype->plugin_name();

        $questiontext = $question->format_questiontext($qa);

        $dropareaclass = 'droparea';
        $draghomesclass = 'draghomes';
        if ($options->readonly) {
            $dropareaclass .= ' readonly';
            $draghomesclass .= ' readonly';
        }

        $output = html_writer::div($questiontext, 'qtext');

        $output .= html_writer::start_div('ddarea');
        $output .= html_writer::start_div($dropareaclass);
        $output .= html_writer::img(self::get_url_for_image($qa, 'bgimage'), get_string('dropbackground', 'qtype_ddmarker'),
                ['class' => 'dropbackground img-fluid w-100']);

        $visibledropzones = [];
        if ($question->showmisplaced && $qa->get_state()->is_finished()) {
            $visibledropzones = $question->get_drop_zones_without_hit($response);
            if (count($visibledropzones) !== 0) {
                $wrongpartsstringspans = [];
                foreach ($visibledropzones as $visibledropzone) {
                    $visibledropzone->markertext = question_utils::format_question_fragment(
                        $visibledropzone->markertext, $this->page->context);
                    $wrongpartsstringspans[] = html_writer::span($visibledropzone->markertext, 'wrongpart');
                }
            }
        }
        $output .= html_writer::div('', 'dropzones', ['data-visibled-dropzones' => json_encode($visibledropzones)]);
        $output .= html_writer::div('', 'markertexts');

        $output .= html_writer::end_div();
        $output .= html_writer::start_div($draghomesclass);

        $orderedgroup = $question->get_ordered_choices(1);
        $hiddenfields = '';
        $dragitems = '';
        foreach ($orderedgroup as $choiceno => $drag) {
            $classes = ['marker', 'user-select-none', 'choice' . $choiceno];
            $attr = [];
            if ($drag->infinite) {
                $classes[] = 'infinite';
            } else {
                $classes[] = 'dragno' . $drag->noofdrags;
            }
            if (!$options->readonly) {
                $attr['tabindex'] = 0;
            }
            $dragoutput = html_writer::start_span(join(' ', $classes), $attr);
            $targeticonhtml = $this->output->image_icon('crosshairs', '', $componentname, ['class' => 'target']);
            $markertext = html_writer::span(question_utils::format_question_fragment($drag->text, $this->page->context),
                'markertext');
            $dragoutput .= $targeticonhtml . $markertext;
            $dragoutput .= html_writer::end_span();
            $dragitems .= $dragoutput;
            $hiddenfields .= $this->hidden_field_choice($qa, $choiceno, $drag->infinite, $drag->noofdrags);
        }
        $output .= $dragitems;
        $output .= html_writer::end_div();
        // Add extra hidden drag items so we can make sure the filter will be applied.
        $output .= html_writer::div($dragitems, 'dd-original d-none');
        $output .= html_writer::end_div();

        if ($qa->get_state() == question_state::$invalid) {
            $output .= html_writer::div($question->get_validation_error($qa->get_last_qt_data()), 'validationerror');
        }

        if (count($visibledropzones) !== 0) {
            $wrongpartsstring = join(', ', $wrongpartsstringspans);
            $output .= html_writer::span(get_string('followingarewrongandhighlighted', 'qtype_ddmarker', $wrongpartsstring),
                'wrongparts');
        }

        $output .= html_writer::div($hiddenfields, 'ddform');
        $this->page->requires->js_call_amd('qtype_ddmarker/question', 'init',
                [$qa->get_outer_question_div_unique_id(), $options->readonly]);

        return $output;
    }

    protected function hidden_field_choice(question_attempt $qa, $choiceno, $infinite, $noofdrags, $value = null) {
        $varname = 'c'.$choiceno;
        $classes = array('choices', 'choice'.$choiceno, 'noofdrags'.$noofdrags);
        if ($infinite) {
            $classes[] = 'infinite';
        }
        list(, $html) = $this->hidden_field_for_qt_var($qa, $varname, null, $classes);
        return $html;
    }

    protected function hint(question_attempt $qa, question_hint $hint) {
        $output = '';
        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();
        if ($hint->statewhichincorrect) {
            $wrongdrags = $question->get_wrong_drags($response);
            $wrongparts = array();
            foreach ($wrongdrags as $wrongdrag) {
                $wrongparts[] = html_writer::nonempty_tag('span',
                                                $wrongdrag, array('class' => 'wrongpart'));
            }
            $output .= html_writer::nonempty_tag('div',
                    get_string('followingarewrong', 'qtype_ddmarker', join(', ', $wrongparts)),
                    array('class' => 'wrongparts'));
        }
        $output .= parent::hint($qa, $hint);
        return $output;
    }
}
