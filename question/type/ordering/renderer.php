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
 * @copyright  2009 The Open University
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
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {
        global $DB, $CFG;
        
        $question = $qa->get_question();
        //$stemorder = $question->get_stem_order();
        //$response = $qa->get_last_qt_data();
        
        //print_r ($response);
        
        //echo "!7!";

        //$response = $qa->get_last_qt_var('answer', '');
        
        //print_r ($_REQUEST);
        /*
        

        $inputname = $qa->get_qt_field_name('answer');
        $trueattributes = array(
            'type' => 'radio',
            'name' => $inputname,
            'value' => 1,
            'id' => $inputname . 'true',
        );
        $falseattributes = array(
            'type' => 'radio',
            'name' => $inputname,
            'value' => 0,
            'id' => $inputname . 'false',
        );

        if ($options->readonly) {
            $trueattributes['disabled'] = 'disabled';
            $falseattributes['disabled'] = 'disabled';
        }
        */
        
        //print_r ($question);
        //echo "@@@@";
        //print_r ($options);

/*
        // Work out which radio button to select (if any)
        $truechecked = false;
        $falsechecked = false;
        $responsearray = array();
        if ($response) {
            $trueattributes['checked'] = 'checked';
            $truechecked = true;
            $responsearray = array('answer' => 1);
        } else if ($response !== '') {
            $falseattributes['checked'] = 'checked';
            $falsechecked = true;
            $responsearray = array('answer' => 1);
        }

        // Work out visual feedback for answer correctness.
        $trueclass = '';
        $falseclass = '';
        $truefeedbackimg = '';
        $falsefeedbackimg = '';
        if ($options->correctness) {
            if ($truechecked) {
                $trueclass = ' ' . $this->feedback_class((int) $question->rightanswer);
                $truefeedbackimg = $this->feedback_image((int) $question->rightanswer);
            } else if ($falsechecked) {
                $falseclass = ' ' . $this->feedback_class((int) (!$question->rightanswer));
                $falsefeedbackimg = $this->feedback_image((int) (!$question->rightanswer));
            }
        }

        $radiotrue = html_writer::empty_tag('input', $trueattributes) .
                html_writer::tag('label', get_string('true', 'qtype_ordering'),
                array('for' => $trueattributes['id']));
        $radiofalse = html_writer::empty_tag('input', $falseattributes) .
                html_writer::tag('label', get_string('false', 'qtype_ordering'),
                array('for' => $falseattributes['id']));

        $result = '';
        $result .= html_writer::tag('div', $question->format_questiontext($qa),
                array('class' => 'qtext'));

        $result .= html_writer::start_tag('div', array('class' => 'ablock'));
        $result .= html_writer::tag('div', get_string('selectone', 'qtype_ordering'),
                array('class' => 'prompt'));

        $result .= html_writer::start_tag('div', array('class' => 'answer'));
        $result .= html_writer::tag('div', $radiotrue . ' ' . $truefeedbackimg,
                array('class' => 'r0' . $trueclass));
        $result .= html_writer::tag('div', $radiofalse . ' ' . $falsefeedbackimg,
                array('class' => 'r1' . $falseclass));
        $result .= html_writer::end_tag('div'); // answer

        $result .= html_writer::end_tag('div'); // ablock

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error($responsearray),
                    array('class' => 'validationerror'));
        }
        */
        //print_r ($this);
        $data = $DB->get_record("question_ordering", array("question" => $question->id));
        if ($data->studentsee == 0) $data->studentsee = 100; else $data->studentsee += 2;
        
        //if ($CFG->dbtype == "mysql") $rand = 'RAND()'; else $rand = 'RANDOM()';
        
        //$answers = $DB->get_records_sql("SELECT * FROM {question_answers} WHERE question = ? ORDER BY ".$rand." LIMIT ?", array($question->id, $data->studentsee));
        
        //$answers = $DB->get_records_sql("SELECT * FROM {question_answers} WHERE question = ? ORDER BY id LIMIT ?", array($question->id, $data->studentsee));
        $answers = $DB->get_records("question_answers", array("question" => $question->id), '', '*', 0, $data->studentsee);
        shuffle($answers);
        
        $result = '';
        $result .= html_writer::tag('script', '', array('type'=>'text/javascript', 'src'=>$CFG->wwwroot.'/question/type/ordering/jquery.js'));
        $result .= html_writer::tag('script', '', array('type'=>'text/javascript', 'src'=>$CFG->wwwroot.'/question/type/ordering/jquery-ui.js'));
        $result .= html_writer::tag('style', 'ul.sortable li {
	position: relative;
}

ul.boxy {
	list-style-type: none;
	padding: 4px 4px 0 4px;
	margin: 0px;
	font-size: 13px;
	font-family: Arial, sans-serif;
	border: 1px solid #ccc;
	width: 360px;
	float: left;
	margin-left: 5px;
}
ul.boxy li {
cursor: move;
margin-bottom: 1px;
padding: 8px 2px;
border: 1px solid #CCC;
background-color: #EEE;
min-height: 20px;
border-image: initial;
list-style-type: none;
}
');

        $result .= html_writer::tag('script', '
	$(function() {
		$( "#sortable" ).sortable({
      update: function(event, ui) {
				var ItemsOrder = $(this).sortable(\'toArray\').toString();
				$(\'#q'.$question->id.'\').attr("value", ItemsOrder);
			}
		});
		$( "#sortable" ).disableSelection();
	});
	$(document).ready(function() {
		var ItemsOrder = $("#sortable").sortable(\'toArray\').toString();
		$(\'#q'.$question->id.'\').attr("value", ItemsOrder);
	});
');
        
        $result .= html_writer::tag('div', stripslashes($question->format_questiontext($qa)),
                array('class' => 'qtext'));
        $result .= html_writer::start_tag('div', array('class' => 'ablock'));
        $result .= html_writer::start_tag('div', array('class' => 'answer'));
        $result .= html_writer::start_tag('ul', array('class' => 'boxy', 'id' => 'sortable'));
        
        while (list($key,$value)=each($answers)) {
            list($fr) = explode(".", $value->fraction);
            $result .= html_writer::tag('li', stripslashes($value->answer),
                array('class' => 'ui-state-default', 'id' => 'ordering_item_'.$value->id.'_'.$fr));
        }
        
        $result .= html_writer::end_tag('ul');
        $result .= html_writer::end_tag('div'); // answer
        $result .= html_writer::end_tag('div'); // ablock
        
        $result .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'q'.$question->id, 'id' => 'q'.$question->id));
        $result .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'answer', 'value' => '712'));
                
        $result .= html_writer::tag('div', '', array('style' => 'clear:both;'));
        
        $result .= html_writer::tag('script', '

function touchHandler(event)
{
    var touches = event.changedTouches,
        first = touches[0],
        type = "";
        
    
    switch(event.type)
    {
        case "touchstart":
            type = "mousedown";
            break;
            
        case "touchmove":
            type="mousemove";        
            event.preventDefault();
            break;        
            
        case "touchend":
            type="mouseup";
            break;
            
        default:
            return;
    }
    
    var simulatedEvent = document.createEvent("MouseEvent");
    
    //initMouseEvent(type, canBubble, cancelable, view, clickCount, screenX, screenY, clientX, clientY, 
    //               ctrlKey, altKey, shiftKey, metaKey, button, relatedTarget);
    
    simulatedEvent.initMouseEvent(type, true, true, window, 1, first.screenX, first.screenY, first.clientX, first.clientY,
                                  false, false, false, false, 0/*left*/, null);
                                                                            
    first.target.dispatchEvent(simulatedEvent);
 
    event.preventDefault();
}
 
function init() {
    for(i=0;i<document.getElementById("sortable").childNodes.length;i++) {
      document.getElementById(\'sortable\').childNodes.item(i).addEventListener("touchstart", touchHandler, false);
      document.getElementById(\'sortable\').childNodes.item(i).addEventListener("touchmove", touchHandler, false);
      document.getElementById(\'sortable\').childNodes.item(i).addEventListener("touchend", touchHandler, false);
      document.getElementById(\'sortable\').childNodes.item(i).addEventListener("touchcancel", touchHandler, false);
    }

    
    var myInterval = window.setInterval(function (a,b) {
function touchHandler(event)
{
    var touches = event.changedTouches,
        first = touches[0],
        type = "";
        
    
    switch(event.type)
    {
        case "touchstart":
            type = "mousedown";
            break;
            
        case "touchmove":
            type="mousemove";        
            event.preventDefault();
            break;        
            
        case "touchend":
            type="mouseup";
            break;
            
        default:
            return;
    }
    
    var simulatedEvent = document.createEvent("MouseEvent");
    
    //initMouseEvent(type, canBubble, cancelable, view, clickCount, screenX, screenY, clientX, clientY, 
    //               ctrlKey, altKey, shiftKey, metaKey, button, relatedTarget);
    
    simulatedEvent.initMouseEvent(type, true, true, window, 1, first.screenX, first.screenY, first.clientX, first.clientY,
                                  false, false, false, false, 0, null);
                                                                            
    first.target.dispatchEvent(simulatedEvent);
 
    event.preventDefault();
}

      for(i=0;i<document.getElementById("sortable").childNodes.length;i++) {
        document.getElementById(\'sortable\').childNodes.item(i).addEventListener("touchstart", touchHandler, false);
        document.getElementById(\'sortable\').childNodes.item(i).addEventListener("touchmove", touchHandler, false);
        document.getElementById(\'sortable\').childNodes.item(i).addEventListener("touchend", touchHandler, false);
        document.getElementById(\'sortable\').childNodes.item(i).addEventListener("touchcancel", touchHandler, false);
      }
    },500);
    
}

init();

');
        
        return $result;
    }
/*
    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();
        $response = $qa->get_last_qt_var('answer', '');

        if ($response) {
            return $question->format_text($question->truefeedback, $question->truefeedbackformat,
                    $qa, 'question', 'answerfeedback', $question->trueanswerid);
        } else if ($response !== '') {
            return $question->format_text($question->falsefeedback, $question->falsefeedbackformat,
                    $qa, 'question', 'answerfeedback', $question->falseanswerid);
        }
    }
*/
    public function correct_response(question_attempt $qa) {
        global $DB;
        
        $question = $qa->get_question();
        
        $data = $DB->get_records("question_attempt_steps", array("questionattemptid" => $question->contextid), "id DESC");
        $data = current($data);
        
        if ($data->fraction >= 1) {
            $feedback = get_string('correctfeedback', 'qtype_ordering');
        } else if ($data->fraction > 0) {
            $feedback = get_string('partiallycorrectfeedback', 'qtype_ordering') . " ".round($data->fraction, 2);
        } else {
            $feedback = get_string('incorrectfeedback', 'qtype_ordering');
        }
        
        return  $feedback;
    }
}
