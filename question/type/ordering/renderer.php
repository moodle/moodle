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
        global $DB, $CFG;

        $question = $qa->get_question();

        if (! $ordering = $DB->get_record('question_ordering', array('question' => $question->id))) {
            return '';
        }
        if (empty($ordering->studentsee)) {
            $ordering->studentsee = 100;
        } else {
            $ordering->studentsee += 2;
        }

        if (! $answers = $DB->get_records('question_answers', array('question' => $question->id), '', '*', 0, $ordering->studentsee)) {
            return ''; // shouldn't happen !!
        }
        shuffle($answers);

        $result = '';
        $result .= html_writer::tag('script', '', array('type'=>'text/javascript', 'src'=>$CFG->wwwroot.'/question/type/ordering/jquery.js'));
        $result .= html_writer::tag('script', '', array('type'=>'text/javascript', 'src'=>$CFG->wwwroot.'/question/type/ordering/jquery-ui.js'));

        $style = "\n";
        $style .= "ul.sortable li {\n";
        $style .= "    position: relative;\n";
        $style .= "}\n";
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
        $result .= html_writer::tag('style', $style, array('type' => 'text/css'));

        $script = "\n";
        $script .= "$(function() {\n";
        $script .= "    $('#sortable').sortable({\n";
        $script .= "        update: function(event, ui) {\n";
        $script .= "            var ItemsOrder = $(this).sortable('toArray').toString();\n";
        $script .= "            $('#q".$question->id."').attr('value', ItemsOrder);\n";
        $script .= "        }\n";
        $script .= "    });\n";
        $script .= "    $('#sortable').disableSelection();\n";
        $script .= "});\n";
        $script .= "$(document).ready(function() {\n";
        $script .= "    var ItemsOrder = $('#sortable').sortable('toArray').toString();\n";
        $script .= "    $('#q".$question->id."').attr('value', ItemsOrder);\n";
        $script .= "});\n";
        $result .= html_writer::tag('script', $script, array('type' => 'text/javascript'));

        $result .= html_writer::tag('div', stripslashes($question->format_questiontext($qa)), array('class' => 'qtext'));
        $result .= html_writer::start_tag('div', array('class' => 'ablock'));
        $result .= html_writer::start_tag('div', array('class' => 'answer'));
        $result .= html_writer::start_tag('ul', array('class' => 'boxy', 'id' => 'sortable'));

        foreach ($answers as $answer) {
            // $answer->fraction holds the correct order - as a decimal ?!
            $id = 'ordering_item_'.$answer->id.'_'.intval($answer->fraction);
            $params = array('class' => 'ui-state-default', 'id' => $id);
            $result .= html_writer::tag('li', stripslashes($answer->answer), $params);
        }

        $result .= html_writer::end_tag('ul');
        $result .= html_writer::end_tag('div'); // answer
        $result .= html_writer::end_tag('div'); // ablock

        $result .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'q'.$question->id, 'id' => 'q'.$question->id));
        $result .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'answer', 'value' => '712'));

        $result .= html_writer::tag('div', '', array('style' => 'clear:both;'));

        $script = "\n";
        $script .= "function orderingTouchHandler(event) {\n";
        $script .= "    var touch = event.changedTouches[0];\n";
        $script .= "    switch (event.type) {\n";
        $script .= "        case 'touchstart': var type = 'mousedown'; break;\n";
        $script .= "        case 'touchmove': var type = 'mousemove'; event.preventDefault(); break;\n";
        $script .= "        case 'touchend': var type = 'mouseup'; break;\n";
        $script .= "        default: return;\n";
        $script .= "    }\n";
        $script .= "    var simulatedEvent = document.createEvent('MouseEvent');\n";
        $script .= "    //initMouseEvent(type, canBubble, cancelable, view, clickCount, screenX, screenY, clientX, clientY, ctrlKey, altKey, shiftKey, metaKey, button, relatedTarget);\n";
        $script .= "    simulatedEvent.initMouseEvent(type, true, true, window, 1, touch.screenX, touch.screenY, touch.clientX, touch.clientY, false, false, false, false, 0/*left*/, null);\n";
        $script .= "    touch.target.dispatchEvent(simulatedEvent);\n";
        $script .= "    event.preventDefault();\n";
        $script .= "}\n";
        $script .= "function orderingInit() {\n";
        $script .= "    var obj = document.getElementById('sortable');\n";
        $script .= "    if (obj) {\n";
        $script .= "        for (var i=0; i<obj.childNodes.length; i++) {\n";
        $script .= "            obj.childNodes.item(i).addEventListener('touchstart', orderingTouchHandler, false);\n";
        $script .= "            obj.childNodes.item(i).addEventListener('touchmove', orderingTouchHandler, false);\n";
        $script .= "            obj.childNodes.item(i).addEventListener('touchend', orderingTouchHandler, false);\n";
        $script .= "            obj.childNodes.item(i).addEventListener('touchcancel', orderingTouchHandler, false);\n";
        $script .= "        }\n";
        $script .= "    } else {\n";
        $script .= "        setTimeout(orderingInit, 500);\n"; // try again in 1/2 a second
        $script .= "    }\n";
        $script .= "}\n";
        $script .= "orderingInit();\n";
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
