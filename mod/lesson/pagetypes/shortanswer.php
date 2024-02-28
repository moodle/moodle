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
 * Short answer
 *
 * @package mod_lesson
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

 /** Short answer question type */
define("LESSON_PAGE_SHORTANSWER",   "1");

class lesson_page_type_shortanswer extends lesson_page {

    protected $type = lesson_page::TYPE_QUESTION;
    protected $typeidstring = 'shortanswer';
    protected $typeid = LESSON_PAGE_SHORTANSWER;
    protected $string = null;

    public function get_typeid() {
        return $this->typeid;
    }
    public function get_typestring() {
        if ($this->string===null) {
            $this->string = get_string($this->typeidstring, 'lesson');
        }
        return $this->string;
    }
    public function get_idstring() {
        return $this->typeidstring;
    }
    public function display($renderer, $attempt) {
        global $USER, $CFG, $PAGE;
        $mform = new lesson_display_answer_form_shortanswer($CFG->wwwroot.'/mod/lesson/continue.php', array('contents'=>$this->get_contents(), 'lessonid'=>$this->lesson->id));
        $data = new stdClass;
        $data->id = $PAGE->cm->id;
        $data->pageid = $this->properties->id;
        if (isset($USER->modattempts[$this->lesson->id])) {
            $data->answer = s($attempt->useranswer);
        }
        $mform->set_data($data);

        // Trigger an event question viewed.
        $eventparams = array(
            'context' => context_module::instance($PAGE->cm->id),
            'objectid' => $this->properties->id,
            'other' => array(
                    'pagetype' => $this->get_typestring()
                )
            );

        $event = \mod_lesson\event\question_viewed::create($eventparams);
        $event->trigger();
        return $mform->display();
    }

    /**
     * Creates answers for this page type.
     *
     * @param  object $properties The answer properties.
     */
    public function create_answers($properties) {
        if (isset($properties->enableotheranswers) && $properties->enableotheranswers) {
            $properties->response_editor = array_values($properties->response_editor);
            $properties->jumpto = array_values($properties->jumpto);
            $properties->score = array_values($properties->score);
            $wrongresponse = end($properties->response_editor);
            $wrongkey = key($properties->response_editor);
            $properties->answer_editor[$wrongkey] = LESSON_OTHER_ANSWERS;
        }
        parent::create_answers($properties);
    }

    /**
     * Update the answers for this page type.
     *
     * @param  object $properties The answer properties.
     * @param  context $context The context for this module.
     * @param  int $maxbytes The maximum bytes for any uploades.
     */
    public function update($properties, $context = null, $maxbytes = null) {
        if ($properties->enableotheranswers) {
            $properties->response_editor = array_values($properties->response_editor);
            $properties->jumpto = array_values($properties->jumpto);
            $properties->score = array_values($properties->score);
            $wrongresponse = end($properties->response_editor);
            $wrongkey = key($properties->response_editor);
            $properties->answer_editor[$wrongkey] = LESSON_OTHER_ANSWERS;
        }
        parent::update($properties, $context, $maxbytes);
    }


    public function check_answer() {
        global $CFG;
        $result = parent::check_answer();

        $mform = new lesson_display_answer_form_shortanswer($CFG->wwwroot.'/mod/lesson/continue.php', array('contents'=>$this->get_contents()));
        $data = $mform->get_data();
        require_sesskey();

        $studentanswer = trim($data->answer);
        if ($studentanswer === '') {
            $result->noanswer = true;
            return $result;
        }

        $i=0;
        $answers = $this->get_answers();
        foreach ($answers as $answer) {
            $answer = parent::rewrite_answers_urls($answer, false);
            $i++;
            // Applying PARAM_TEXT as it is applied to the answer submitted by the user.
            $expectedanswer  = clean_param($answer->answer, PARAM_TEXT);
            $ismatch         = false;
            $markit          = false;
            $useregexp       = ($this->qoption);

            if ($useregexp) { //we are using 'normal analysis', which ignores case
                $ignorecase = '';
                if (substr($expectedanswer, -2) == '/i') {
                    $expectedanswer = substr($expectedanswer, 0, -2);
                    $ignorecase = 'i';
                }
            } else {
                $expectedanswer = str_replace('*', '%@@%@@%', $expectedanswer);
                $expectedanswer = preg_quote($expectedanswer, '/');
                $expectedanswer = str_replace('%@@%@@%', '.*', $expectedanswer);
            }
            // see if user typed in any of the correct answers
            if ((!$this->lesson->custom && $this->lesson->jumpto_is_correct($this->properties->id, $answer->jumpto)) or ($this->lesson->custom && $answer->score > 0) ) {
                if (!$useregexp) { // we are using 'normal analysis', which ignores case
                    if (preg_match('/^'.$expectedanswer.'$/i',$studentanswer)) {
                        $ismatch = true;
                    }
                } else {
                    if (preg_match('/^'.$expectedanswer.'$/'.$ignorecase,$studentanswer)) {
                        $ismatch = true;
                    }
                }
                if ($ismatch == true) {
                    $result->correctanswer = true;
                }
            } else {
               if (!$useregexp) { //we are using 'normal analysis'
                    // see if user typed in any of the wrong answers; don't worry about case
                    if (preg_match('/^'.$expectedanswer.'$/i',$studentanswer)) {
                        $ismatch = true;
                    }
                } else { // we are using regular expressions analysis
                    $startcode = substr($expectedanswer,0,2);
                    switch ($startcode){
                        //1- check for absence of required string in $studentanswer (coded by initial '--')
                        case "--":
                            $expectedanswer = substr($expectedanswer,2);
                            if (!preg_match('/^'.$expectedanswer.'$/'.$ignorecase,$studentanswer)) {
                                $ismatch = true;
                            }
                            break;
                        //2- check for code for marking wrong strings (coded by initial '++')
                        case "++":
                            $expectedanswer=substr($expectedanswer,2);
                            $markit = true;
                            //check for one or several matches
                            if (preg_match_all('/'.$expectedanswer.'/'.$ignorecase,$studentanswer, $matches)) {
                                $ismatch   = true;
                                $nb        = count($matches[0]);
                                $original  = array();
                                $marked    = array();
                                $fontStart = '<span class="incorrect matches">';
                                $fontEnd   = '</span>';
                                for ($i = 0; $i < $nb; $i++) {
                                    array_push($original,$matches[0][$i]);
                                    array_push($marked,$fontStart.$matches[0][$i].$fontEnd);
                                }
                                $studentanswer = str_replace($original, $marked, $studentanswer);
                            }
                            break;
                        //3- check for wrong answers belonging neither to -- nor to ++ categories
                        default:
                            if (preg_match('/^'.$expectedanswer.'$/'.$ignorecase,$studentanswer, $matches)) {
                                $ismatch = true;
                            }
                            break;
                    }
                    $result->correctanswer = false;
                }
            }
            if ($ismatch) {
                $result->newpageid = $answer->jumpto;
                $options = new stdClass();
                $options->para = false;
                $options->noclean = true;
                $result->response = format_text($answer->response, $answer->responseformat, $options);
                $result->answerid = $answer->id;
                break; // quit answer analysis immediately after a match has been found
            }
        }

        // We could check here to see if we have a wrong answer jump to use.
        if ($result->answerid == 0) {
            // Use the all other answers jump details if it is set up.
            $lastanswer = end($answers);
            // Double check that this is the @#wronganswer#@ answer.
            if (strpos($lastanswer->answer, LESSON_OTHER_ANSWERS) !== false) {
                $otheranswers = end($answers);
                $result->newpageid = $otheranswers->jumpto;
                $options = new stdClass();
                $options->para = false;
                $result->response = format_text($otheranswers->response, $otheranswers->responseformat, $options);
                // Does this also need to do the jumpto_is_correct?
                if ($this->lesson->custom) {
                    $result->correctanswer = ($otheranswers->score > 0);
                }
                $result->answerid = $otheranswers->id;
            }
        }

        $result->userresponse = $studentanswer;
        //clean student answer as it goes to output.
        $result->studentanswer = s($studentanswer);
        return $result;
    }

    public function option_description_string() {
        if ($this->properties->qoption) {
            return " - ".get_string("casesensitive", "lesson");
        }
        return parent::option_description_string();
    }

    public function display_answers(html_table $table) {
        $answers = $this->get_answers();
        $options = new stdClass;
        $options->noclean = true;
        $options->para = false;
        $i = 1;
        foreach ($answers as $answer) {
            $answer = parent::rewrite_answers_urls($answer, false);
            $cells = array();
            if ($this->lesson->custom && $answer->score > 0) {
                // if the score is > 0, then it is correct
                $cells[] = '<label class="correct">' . get_string('answer', 'lesson') . ' ' . $i . '</label>:';
            } else if ($this->lesson->custom) {
                $cells[] = '<label>' . get_string('answer', 'lesson') . ' ' . $i . '</label>:';
            } else if ($this->lesson->jumpto_is_correct($this->properties->id, $answer->jumpto)) {
                // underline correct answers
                $cells[] = '<span class="correct">' . get_string('answer', 'lesson') . ' ' . $i . '</span>:' . "\n";
            } else {
                $cells[] = '<label class="correct">' . get_string('answer', 'lesson') . ' ' . $i . '</label>:';
            }
            $cells[] = format_text($answer->answer, $answer->answerformat, $options);
            $table->data[] = new html_table_row($cells);

            $cells = array();
            $cells[] = '<label>' . get_string('response', 'lesson') . ' ' . $i . '</label>:';
            $cells[] = format_text($answer->response, $answer->responseformat, $options);
            $table->data[] = new html_table_row($cells);

            $cells = array();
            $cells[] = '<label>' . get_string('score', 'lesson') . '</label>:';
            $cells[] = $answer->score;
            $table->data[] = new html_table_row($cells);

            $cells = array();
            $cells[] = '<label>' . get_string('jump', 'lesson') . '</label>:';
            $cells[] = $this->get_jump_name($answer->jumpto);
            $table->data[] = new html_table_row($cells);
            if ($i === 1){
                $table->data[count($table->data)-1]->cells[0]->style = 'width:20%;';
            }
            $i++;
        }
        return $table;
    }
    public function stats(array &$pagestats, $tries) {
        $temp = $this->lesson->get_last_attempt($tries);
        if (isset($pagestats[$temp->pageid][$temp->useranswer])) {
            $pagestats[$temp->pageid][$temp->useranswer]++;
        } else {
            $pagestats[$temp->pageid][$temp->useranswer] = 1;
        }
        if (isset($pagestats[$temp->pageid]["total"])) {
            $pagestats[$temp->pageid]["total"]++;
        } else {
            $pagestats[$temp->pageid]["total"] = 1;
        }
        return true;
    }

    public function report_answers($answerpage, $answerdata, $useranswer, $pagestats, &$i, &$n) {
        global $PAGE;

        $answers = $this->get_answers();
        $formattextdefoptions = new stdClass;
        $formattextdefoptions->para = false;  //I'll use it widely in this page
        foreach ($answers as $answer) {
            $answer = parent::rewrite_answers_urls($answer, false);
            if ($useranswer == null && $i == 0) {
                // I have the $i == 0 because it is easier to blast through it all at once.
                if (isset($pagestats[$this->properties->id])) {
                    $stats = $pagestats[$this->properties->id];
                    $total = $stats["total"];
                    unset($stats["total"]);
                    foreach ($stats as $valentered => $ntimes) {
                        $data = '<input type="text" size="50" disabled="disabled" class="form-control" ' .
                                'readonly="readonly" value="'.s($valentered).'" />';
                        $percent = $ntimes / $total * 100;
                        $percent = round($percent, 2);
                        $percent .= "% ".get_string("enteredthis", "lesson");
                        $answerdata->answers[] = array($data, $percent);
                    }
                } else {
                    $answerdata->answers[] = array(get_string("nooneansweredthisquestion", "lesson"), " ");
                }
                $i++;
            } else if ($useranswer != null && ($answer->id == $useranswer->answerid || $answer == end($answers))) {
                 // get in here when what the user entered is not one of the answers
                $data = '<input type="text" size="50" disabled="disabled" class="form-control" ' .
                        'readonly="readonly" value="'.s($useranswer->useranswer).'">';
                if (isset($pagestats[$this->properties->id][$useranswer->useranswer])) {
                    $percent = $pagestats[$this->properties->id][$useranswer->useranswer] / $pagestats[$this->properties->id]["total"] * 100;
                    $percent = round($percent, 2);
                    $percent .= "% ".get_string("enteredthis", "lesson");
                } else {
                    $percent = get_string("nooneenteredthis", "lesson");
                }
                $answerdata->answers[] = array($data, $percent);

                if ($answer->id == $useranswer->answerid) {
                    if ($answer->response == null) {
                        if ($useranswer->correct) {
                            $answerdata->response = get_string("thatsthecorrectanswer", "lesson");
                        } else {
                            $answerdata->response = get_string("thatsthewronganswer", "lesson");
                        }
                    } else {
                        $answerdata->response = $answer->response;
                    }
                    if ($this->lesson->custom) {
                        $answerdata->score = get_string("pointsearned", "lesson").": ".$answer->score;
                    } elseif ($useranswer->correct) {
                        $answerdata->score = get_string("receivedcredit", "lesson");
                    } else {
                        $answerdata->score = get_string("didnotreceivecredit", "lesson");
                    }
                    // We have found the correct answer, do not process any more answers.
                    $answerpage->answerdata = $answerdata;
                    break;
                } else {
                    $answerdata->response = get_string("thatsthewronganswer", "lesson");
                    if ($this->lesson->custom) {
                        $answerdata->score = get_string("pointsearned", "lesson").": 0";
                    } else {
                        $answerdata->score = get_string("didnotreceivecredit", "lesson");
                    }
                }
            }
            $answerpage->answerdata = $answerdata;
        }
        return $answerpage;
    }

    /**
     * Make updates to the form data if required. In this case to put the all other answer data into the write section of the form.
     *
     * @param stdClass $data The form data to update.
     * @return stdClass The updated fom data.
     */
    public function update_form_data(stdClass $data): stdClass {
        $answercount = count($this->get_answers());
        // Check for other answer entry.
        $lastanswer = $data->{'answer_editor[' . ($answercount - 1) . ']'};
        if (strpos($lastanswer, LESSON_OTHER_ANSWERS) !== false) {
            $data->{'answer_editor[' . ($this->lesson->maxanswers + 1) . ']'} =
                    $data->{'answer_editor[' . ($answercount - 1) . ']'};
            $data->{'response_editor[' . ($this->lesson->maxanswers + 1) . ']'} =
                    $data->{'response_editor[' . ($answercount - 1) . ']'};
            $data->{'jumpto[' . ($this->lesson->maxanswers + 1) . ']'} = $data->{'jumpto[' . ($answercount - 1) . ']'};
            $data->{'score[' . ($this->lesson->maxanswers + 1) . ']'} = $data->{'score[' . ($answercount - 1) . ']'};
            $data->enableotheranswers = true;
            // Unset the old values.
            unset($data->{'answer_editor[' . ($answercount - 1) . ']'});
            unset($data->{'response_editor[' . ($answercount - 1) . ']'});
            unset($data->{'jumpto[' . ($answercount - 1) . ']'});
            unset($data->{'score[' . ($answercount - 1) . ']'});
        }
        return $data;
    }
}


class lesson_add_page_form_shortanswer extends lesson_add_page_form_base {
    public $qtype = 'shortanswer';
    public $qtypestring = 'shortanswer';
    protected $answerformat = '';
    protected $responseformat = LESSON_ANSWER_HTML;

    public function custom_definition() {

        $this->_form->addElement('checkbox', 'qoption', get_string('options', 'lesson'), get_string('casesensitive', 'lesson')); //oh my, this is a regex option!
        $this->_form->setDefault('qoption', 0);
        $this->_form->addHelpButton('qoption', 'casesensitive', 'lesson');

        $answercount = $this->_customdata['lesson']->maxanswers;
        for ($i = 0; $i < $answercount; $i++) {
            $this->_form->addElement('header', 'answertitle'.$i, get_string('answer').' '.($i+1));
            // Only first answer is required.
            $this->add_answer($i, null, ($i < 1));
            $this->add_response($i);
            $this->add_jumpto($i, null, ($i == 0 ? LESSON_NEXTPAGE : LESSON_THISPAGE));
            $this->add_score($i, null, ($i===0)?1:0);
        }

        // Other answer jump.
        $this->_form->addElement('header', 'wronganswer', get_string('allotheranswers', 'lesson'));
        $newcount = $answercount + 1;
        $this->_form->addElement('advcheckbox', 'enableotheranswers', get_string('enabled', 'lesson'));
        $this->add_response($newcount);
        $this->add_jumpto($newcount, get_string('allotheranswersjump', 'lesson'), LESSON_NEXTPAGE);
        $this->add_score($newcount, get_string('allotheranswersscore', 'lesson'), 0);
    }
}

class lesson_display_answer_form_shortanswer extends moodleform {

    public function definition() {
        global $OUTPUT, $USER;
        $mform = $this->_form;
        $contents = $this->_customdata['contents'];

        $hasattempt = false;
        $attrs = array('size'=>'50', 'maxlength'=>'200');
        if (isset($this->_customdata['lessonid'])) {
            $lessonid = $this->_customdata['lessonid'];
            if (isset($USER->modattempts[$lessonid]->useranswer)) {
                $attrs['readonly'] = 'readonly';
                $hasattempt = true;
            }
        }

        $placeholder = false;
        if (preg_match('/_____+/', $contents, $matches)) {
            $placeholder = $matches[0];
            $contentsparts = explode( $placeholder, $contents, 2);
            $attrs['size'] = round(strlen($placeholder) * 1.1);
        }

        // Disable shortforms.
        $mform->setDisableShortforms();

        $mform->addElement('header', 'pageheader');
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'pageid');
        $mform->setType('pageid', PARAM_INT);

        if ($placeholder) {
            $contentsgroup = array();
            $contentsgroup[] = $mform->createElement('static', '', '', $contentsparts[0]);
            $contentsgroup[] = $mform->createElement('text', 'answer', '', $attrs);
            $contentsgroup[] = $mform->createElement('static', '', '', $contentsparts[1]);
            $mform->addGroup($contentsgroup, '', '', '', false);
        } else {
            $mform->addElement('html', $OUTPUT->container($contents, 'contents'));
            $mform->addElement('text', 'answer', get_string('youranswer', 'lesson'), $attrs);

        }
        $mform->setType('answer', PARAM_TEXT);

        if ($hasattempt) {
            $this->add_action_buttons(null, get_string("nextpage", "lesson"));
        } else {
            $this->add_action_buttons(null, get_string("submit", "lesson"));
        }
    }

}
