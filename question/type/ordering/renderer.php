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
 * True-false question renderer class.
 *
 * @package    qtype
 * @subpackage ordering
 * @copyright  2013 Gordon Bateson (gordonbateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Generates the output for true-false questions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering_renderer extends qtype_renderer {

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $CFG, $DB;
        static $addStyle = true;
        static $addScript = true;

        $question = $qa->get_question();

        if (! $options = $DB->get_record('question_ordering', array('question' => $question->id))) {
            return '';
        }

        if (! $answers = $DB->get_records('question_answers', array('question' => $question->id), '', '*')) {
            return ''; // shouldn't happen !!
        }

        if ($options->studentsee==0) { // all items
            $options->studentsee = count($answers);
        } else {
            // a nasty hack so that "studentsee" is the same
            // as what is displayed by edit_ordering_form.php
            $options->studentsee += 2;
        }

        switch ($options->logical) {

            case 0: // all
                $answerids = array_keys($answers);
                break;

            case 1: // random subset
                $answerids = array_rand($answers, $options->studentsee);
                break;

            case 2: // contiguous subset
                if (count($answers) > $options->studentsee) {
                    $offset = mt_rand(0, count($answers) - $options->studentsee);
                    $answers = array_slice($answers, $offset, $options->studentsee, true);
                }
                $answerids = array_keys($answers);
                break;
        }
        shuffle($answerids);

        $result = '';
        $result .= html_writer::tag('script', '', array('type'=>'text/javascript', 'src'=>$CFG->wwwroot.'/question/type/ordering/js/jquery.js'));
        $result .= html_writer::tag('script', '', array('type'=>'text/javascript', 'src'=>$CFG->wwwroot.'/question/type/ordering/js/jquery-ui.js'));

        $style = "\n";
        $style .= "ul.sortable".$question->id." li {\n";
        $style .= "    position: relative;\n";
        $style .= "}\n";
        if ($addStyle) {
            $addStyle = false; // only add style once
            $style .= "ul.boxy {\n";
            $style .= "    border: 1px solid #ccc;\n";
            $style .= "    float: left;\n";
            $style .= "    font-family: Arial, sans-serif;\n";
            $style .= "    font-size: 13px;\n";
            $style .= "    list-style-type: none;\n";
            $style .= "    margin: 0px;\n";
            $style .= "    margin-left: 5px;\n";
            $style .= "    padding: 4px 4px 0 4px;\n";
            $style .= "    width: 360px;\n";
            $style .= "}\n";
            $style .= "ul.boxy li {\n";
            $style .= "    background-color: #eeeeee;\n";
            $style .= "    border: 1px solid #cccccc;\n";
            $style .= "    border-image: initial;\n";
            $style .= "    cursor: move;\n";
            $style .= "    list-style-type: none;\n";
            $style .= "    margin-bottom: 1px;\n";
            $style .= "    min-height: 20px;\n";
            $style .= "    padding: 8px 2px;\n";
            $style .= "}\n";
        }
        $result .= html_writer::tag('style', $style, array('type' => 'text/css'));

        $script = "\n";
        $script .= "//<![CDATA[\n";
        $script .= "$(function() {\n";
        $script .= "    $('#sortable".$question->id."').sortable({\n";
        $script .= "        update: function(event, ui) {\n";
        $script .= "            var ItemsOrder = $(this).sortable('toArray').toString();\n";
        $script .= "            $('#q".$question->id."').attr('value', ItemsOrder);\n";
        $script .= "        }\n";
        $script .= "    });\n";
        $script .= "    $('#sortable".$question->id."').disableSelection();\n";
        $script .= "});\n";
        $script .= "$(document).ready(function() {\n";
        $script .= "    var ItemsOrder = $('#sortable".$question->id."').sortable('toArray').toString();\n";
        $script .= "    $('#q".$question->id."').attr('value', ItemsOrder);\n";
        $script .= "});\n";
        $script .= "//]]>\n";
        $result .= html_writer::tag('script', $script, array('type' => 'text/javascript'));

        $result .= html_writer::tag('div', stripslashes($question->format_questiontext($qa)), array('class' => 'qtext'));
        $result .= html_writer::start_tag('div', array('class' => 'ablock'));
        $result .= html_writer::start_tag('div', array('class' => 'answer'));
        $result .= html_writer::start_tag('ul', array('class' => 'boxy', 'id' => 'sortable'.$question->id));

        // a salt (=random string) to help disguise answer ids
        if (isset($CFG->passwordsaltmain)) {
            $salt = $CFG->passwordsaltmain;
        } else {
            $salt = complex_random_string();
        }

        // generate ordering items
        foreach ($answerids as $i => $answerid) {
            // the original "id" revealed the correct order of the answers
            // because $answer->fraction holds the correct order number
            // $id = 'ordering_item_'.$answerid.'_'.intval($answers[$answerid]->fraction);
            $id = 'ordering_item_'.md5($salt.$answers[$answerid]->answer);
            $params = array('class' => 'ui-state-default', 'id' => $id);
            $result .= html_writer::tag('li', $answers[$answerid]->answer, $params);
        }

        $result .= html_writer::end_tag('ul');
        $result .= html_writer::end_tag('div'); // answer
        $result .= html_writer::end_tag('div'); // ablock

        $result .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'q'.$question->id, 'id' => 'q'.$question->id, 'value' => '9'));
        $result .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'answer', 'value' => ''));

        $result .= html_writer::tag('div', '', array('style' => 'clear:both;'));

        $script = "\n";
        $script .= "//<![CDATA[\n";
        if ($addScript) {
            $addScript = false; // only add these functions once
            $script .= "function orderingTouchHandler(event) {\n";
            $script .= "    var touch = event.changedTouches[0];\n";
            $script .= "    switch (event.type) {\n";
            $script .= "        case 'touchstart': var type = 'mousedown'; break;\n";
            $script .= "        case 'touchmove': var type = 'mousemove'; event.preventDefault(); break;\n";
            $script .= "        case 'touchend': var type = 'mouseup'; break;\n";
            $script .= "        default: return;\n";
            $script .= "    }\n";
            $script .= "    var simulatedEvent = document.createEvent('MouseEvent');\n";
            $script .= "    // initMouseEvent(type, canBubble, cancelable, view, clickCount, screenX, screenY, clientX, clientY, ctrlKey, altKey, shiftKey, metaKey, button, relatedTarget)\n";
            $script .= "    simulatedEvent.initMouseEvent(type, true, true, window, 1, touch.screenX, touch.screenY, touch.clientX, touch.clientY, false, false, false, false, 0, null);\n";
            $script .= "    touch.target.dispatchEvent(simulatedEvent);\n";
            $script .= "    event.preventDefault();\n";
            $script .= "}\n";
            $script .= "function orderingInit(sortableid) {\n";
            $script .= "    var obj = document.getElementById(sortableid);\n";
            $script .= "    if (obj) {\n";
            $script .= "        for (var i=0; i<obj.childNodes.length; i++) {\n";
            $script .= "            obj.childNodes.item(i).addEventListener('touchstart', orderingTouchHandler, false);\n";
            $script .= "            obj.childNodes.item(i).addEventListener('touchmove', orderingTouchHandler, false);\n";
            $script .= "            obj.childNodes.item(i).addEventListener('touchend', orderingTouchHandler, false);\n";
            $script .= "            obj.childNodes.item(i).addEventListener('touchcancel', orderingTouchHandler, false);\n";
            $script .= "        }\n";
            $script .= "        obj = null;\n";
            $script .= "    } else {\n";
            $script .= "        // try again in 1/2 a second - shouldn't be necessary !!\n";
            $script .= "        setTimeout(new Function('orderingInit(".'"'."'+sortableid+'".'"'.")'), 500);\n";
            $script .= "    }\n";
            $script .= "}\n";
        }
        $script .= "orderingInit('sortable".$question->id."');\n";
        $script .= "//]]>\n";
        $result .= html_writer::tag('script', $script, array('type' => 'text/javascript'));

        return $result;
    }

    public function correct_response(question_attempt $qa) {
        global $DB;

        $question = $qa->get_question();

        if (! $step = $DB->get_records('question_attempt_steps', array('questionattemptid' => $question->contextid), 'id DESC')) {
            return ''; // shouldn't happen !!
        }
        $step = current($step); // first one

        if ($step->fraction >= 1) {
            $feedback = get_string('correctfeedback', 'qtype_ordering');
        } else if ($step->fraction > 0) {
            $feedback = get_string('partiallycorrectfeedback', 'qtype_ordering').' '.round($step->fraction, 2);
        } else {
            $feedback = get_string('incorrectfeedback', 'qtype_ordering');
        }

        return  $feedback;
    }
}
