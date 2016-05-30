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
        global $PAGE, $OUTPUT;

        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();

        $questiontext = $question->format_questiontext($qa);

        $output = html_writer::tag('div', $questiontext, array('class' => 'qtext'));

        $bgimage = self::get_url_for_image($qa, 'bgimage');

        $img = html_writer::empty_tag('img', array(
                'src' => $bgimage, 'class' => 'dropbackground',
                'alt' => get_string('dropbackground', 'qtype_ddmarker')));

        $droparea = html_writer::tag('div', $img, array('class' => 'droparea'));

        $draghomes = '';
        $orderedgroup = $question->get_ordered_choices(1);
        $componentname = $question->qtype->plugin_name();
        $hiddenfields = '';
        foreach ($orderedgroup as $choiceno => $drag) {
            $classes = array('draghome',
                             "choice{$choiceno}");
            if ($drag->infinite) {
                $classes[] = 'infinite';
            } else {
                $classes[] = 'dragno'.$drag->noofdrags;
            }
            $targeticonhtml =
                $OUTPUT->pix_icon('crosshairs', '', $componentname, array('class' => 'target'));

            $markertextattrs = array('class' => 'markertext');
            $markertext = html_writer::tag('span', $drag->text, $markertextattrs);
            $draghomesattrs = array('class' => join(' ', $classes));
            $draghomes .= html_writer::tag('span', $targeticonhtml . $markertext, $draghomesattrs);
            $hiddenfields .= $this->hidden_field_choice($qa, $choiceno, $drag->infinite, $drag->noofdrags);
        }

        $dragitemsclass = 'dragitems';
        if ($options->readonly) {
            $dragitemsclass .= ' readonly';
        }

        $dragitems = html_writer::tag('div', $draghomes, array('class' => $dragitemsclass));
        $dropzones = html_writer::tag('div', '', array('class' => 'dropzones'));
        $texts = html_writer::tag('div', '', array('class' => 'markertexts'));
        $output .= html_writer::tag('div',
                                    $droparea.$dragitems.$dropzones . $texts,
                                    array('class' => 'ddarea'));

        if ($question->showmisplaced && $qa->get_state()->is_finished()) {
            $visibledropzones = $question->get_drop_zones_without_hit($response);
        } else {
            $visibledropzones = array();
        }

        $topnode = 'div#q'.$qa->get_slot();
        $params = array('dropzones' => $visibledropzones,
                        'topnode' => $topnode,
                        'readonly' => $options->readonly);

        $PAGE->requires->yui_module('moodle-qtype_ddmarker-dd',
                                        'M.qtype_ddmarker.init_question',
                                        array($params));

        if ($qa->get_state() == question_state::$invalid) {
            $output .= html_writer::nonempty_tag('div',
                                        $question->get_validation_error($qa->get_last_qt_data()),
                                        array('class' => 'validationerror'));
        }

        if ($question->showmisplaced && $qa->get_state()->is_finished()) {
            $wrongparts = $question->get_drop_zones_without_hit($response);
            if (count($wrongparts) !== 0) {
                $wrongpartsstringspans = array();
                foreach ($wrongparts as $wrongpart) {
                    $wrongpartsstringspans[] = html_writer::nonempty_tag('span',
                                    $wrongpart->markertext, array('class' => 'wrongpart'));
                }
                $wrongpartsstring = join(', ', $wrongpartsstringspans);
                $output .= html_writer::nonempty_tag('span',
                                                    get_string('followingarewrongandhighlighted',
                                                                'qtype_ddmarker',
                                                                $wrongpartsstring),
                                                    array('class' => 'wrongparts'));
            }
        }

        $output .= html_writer::tag('div', $hiddenfields, array('class' => 'ddform'));
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
