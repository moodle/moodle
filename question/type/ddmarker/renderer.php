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
 * Drag-and-drop words into sentences question renderer class.
 *
 * @package    qtype
 * @subpackage ddmarker
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/ddimageortext/rendererbase.php');


/**
 * Generates the output for drag-and-drop words into sentences questions.
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

        $img = html_writer::empty_tag('img', array('src'=>$bgimage, 'class'=>'dropbackground'));
        $droparea = html_writer::tag('div', $img, array('class'=>'droparea'));

        $draghomes = '';
        $orderedgroup = $question->get_ordered_choices(1);
        $componentname = $question->qtype->plugin_name();
        $hiddenfields = '';
        foreach ($orderedgroup as $choiceno => $drag) {
            $classes = array('draghome',
                             "choice{$choiceno}");
            if ($drag->infinite) {
                $classes[] = 'infinite';
            }
            $targeticonhtml =
                $OUTPUT->pix_icon('crosshairs', '', $componentname, array('class'=> 'target'));

            $markertextattrs = array('class'=> 'markertext');
            $markertext = html_writer::tag('span', $drag->text, $markertextattrs);
            $draghomesattrs = array('class'=>join(' ', $classes));
            $draghomes .= html_writer::tag('span', $targeticonhtml.$markertext, $draghomesattrs);
            $hiddenfields .= $this->hidden_field_choice($qa, $choiceno);
        }

        $dragitemsclass = 'dragitems';
        if ($options->readonly) {
            $dragitemsclass .= ' readonly';
        }
        $dragitems = html_writer::tag('div', $draghomes, array('class'=> $dragitemsclass));
        $output .= html_writer::tag('div', $droparea.$dragitems, array('class'=>'ddarea'));
        $topnode = 'div#q'.$qa->get_slot();
        $params = array('inputids' => $question->places,
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
        $output .= html_writer::tag('div', $hiddenfields, array('class'=>'ddform'));
        return $output;
    }
    protected function hidden_field_choice(question_attempt $qa, $choiceno, $value = null) {
        $varname = 'c'.$choiceno;
        $classes = array('choices', 'choice'.$choiceno);
        list(,$html) = $this->hidden_field_for_qt_var($qa, $varname, null, $classes);
        return $html;
    }
}
