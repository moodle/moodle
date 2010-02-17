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
 * Matching
 *
 * @package   lesson
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

/** Matching question type */
define("LESSON_PAGE_MATCHING",      "5");

class lesson_page_type_matching extends lesson_page {

    protected $type = lesson_page::TYPE_QUESTION;
    protected $typeid = LESSON_PAGE_MATCHING;
    protected $typeidstring = 'matching';
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
        $mform = $this->make_answer_form($attempt);
        $data = new stdClass;
        $data->id = $PAGE->cm->id;
        $data->pageid = $this->properties->id;
        $mform->set_data($data);
        return $mform->display();
    }

    protected function make_answer_form($attempt=null) {
        global $USER, $CFG;
        // don't suffle answers (could be an option??)
        $answers = array_slice($this->get_answers(), 2);
        $responses = array();
        foreach ($answers as $answer) {
            // get all the response
            if ($answer->response != NULL) {
                $responses[] = trim($answer->response);
            }
        }

        $responseoptions = array();
        if (!empty($responses)) {
            shuffle($responses);
            $responses = array_unique($responses);
            foreach ($responses as $response) {
                $responseoptions[htmlspecialchars(trim($response))] = $response;
            }
        }
        if (isset($USER->modattempts[$this->lesson->id]) && !empty($attempt->useranswer)) {
            $useranswers = explode(',', $attempt->useranswer);
            $t = 0;
        } else {
            $useranswers = array();
        }

        $action = $CFG->wwwroot.'/mod/lesson/continue.php';
        $params = array('answers'=>$answers, 'useranswers'=>$useranswers, 'responseoptions'=>$responseoptions, 'lessonid'=>$this->lesson->id, 'contents'=>$this->get_contents());
        $mform = new lesson_display_answer_form_matching($action, $params);
        return $mform;
    }

    public function create_answers($properties) {
        global $DB;
        // now add the answers
        $newanswer = new stdClass;
        $newanswer->lessonid = $this->lesson->id;
        $newanswer->pageid = $this->properties->id;
        $newanswer->timecreated = $this->properties->timecreated;

        $answers = array();

        // need to add two to offset correct response and wrong response
        $this->lesson->maxanswers = $this->lesson->maxanswers + 2;
        for ($i = 0; $i < $this->lesson->maxanswers; $i++) {
            $answer = clone($newanswer);
            if (!empty($properties->answer[$i])) {
                $answer->answer = format_text($properties->answer[$i], FORMAT_PLAIN);
                if (isset($properties->response[$i])) {
                    $answer->response = format_text($properties->response[$i], FORMAT_PLAIN);
                }
                if (isset($properties->jumpto[$i])) {
                    $answer->jumpto = $properties->jumpto[$i];
                }
                if ($this->lesson->custom && isset($properties->score[$i])) {
                    $answer->score = $properties->score[$i];
                }
                $answer->id = $DB->insert_record("lesson_answers", $answer);
                $answers[$answer->id] = new lesson_page_answer($answer);
            } else if ($i < 2) {
                $answer->id = $DB->insert_record("lesson_answers", $answer);
                $answers[$answer->id] = new lesson_page_answer($answer);
            } else {
                break;
            }
        }
        $this->answers = $answers;
        return $answers;
    }

    public function check_answer() {
        global $CFG;
        $result = parent::check_answer();

        $mform = $this->make_answer_form();

        $data = $mform->get_data();
        require_sesskey();

        if (!$data) {
            redirect(new moodle_url('/mod/lesson/view.php', array('id'=>$PAGE->cm->id, 'pageid'=>$this->properties->id)));
        }

        $response = $data->response;
        if (!is_array($response)) {
            $result->noanswer = true;
            return $result;
        }
        $answers = $this->get_answers();

        $ncorrect = 0;
        $i = 0;
        foreach ($answers as $answer) {
            if ($i < 2) {
                // ignore first two answers, they are correct response
                // and wrong response
                $i++;
                continue;
            }
            if ($answer->response == $response[$answer->id]) {
                $ncorrect++;
            }
            if ($i == 2) {
                $correctpageid = $answer->jumpto;
                $correctanswerid = $answer->id;
            }
            if ($i == 3) {
                $wrongpageid = $answer->jumpto;
                $wronganswerid = $answer->id;
            }
            $i++;
        }
        // get he users exact responses for record keeping
        $userresponse = array();
        foreach ($response as $key => $value) {
            foreach($answers as $answer) {
                if ($value == $answer->response) {
                    $userresponse[] = $answer->id;
                }
                if ((int)$answer->id === (int)$key) {
                    $result->studentanswer .= '<br />'.$answer->answer.' = '.$value;
                }
            }
        }
        $result->userresponse = implode(",", $userresponse);

        if ($ncorrect == count($answers)-2) {  // dont count correct/wrong responses in the total.
            foreach ($answers as $answer) {
                if ($answer->response == NULL && $answer->answer != NULL) {
                    $result->response = $answer->answer;
                    break;
                }
            }
            if (isset($correctpageid)) {
                $result->newpageid = $correctpageid;
            }
            if (isset($correctanswerid)) {
                $result->answerid = $correctanswerid;
            }
            $result->correctanswer = true;
        } else {
            $t = 0;
            foreach ($answers as $answer) {
                if ($answer->response == NULL && $answer->answer != NULL) {
                    if ($t == 1) {
                        $result->response = $answer->answer;
                        break;
                    }
                    $t++;
                }
            }
            if (isset($wrongpageid)) {
                $result->newpageid = $wrongpageid;
            }
            if (isset($wronganswerid)) {
                $result->answerid = $wronganswerid;
            }
        }
        return $result;
    }

    public function option_description_string() {
        return get_string("firstanswershould", "lesson");
    }

    public function display_answers(html_table $table) {
        $answers = $this->get_answers();
        $options = new stdClass;
        $options->noclean = true;
        $options->para = false;
        $i = 1;
        $n = 0;

        foreach ($answers as $answer) {
            if ($n < 2) {
                if ($answer->answer != NULL) {
                    $cells = array();
                    if ($n == 0) {
                        $cells[] = "<span class=\"label\">".get_string("correctresponse", "lesson").'</span>';
                    } else {
                        $cells[] = "<span class=\"label\">".get_string("wrongresponse", "lesson").'</span>';
                    }
                    $cells[] = format_text($answer->answer, FORMAT_MOODLE, $options);
                    $table->data[] = new html_table_row($cells);
                }
                $n++;
                $i--;
            } else {
                $cells = array();
                if ($this->lesson->custom && $answer->score > 0) {
                    // if the score is > 0, then it is correct
                    $cells[] = '<span class="labelcorrect">'.get_string("answer", "lesson")." $i</span>: \n";
                } else if ($this->lesson->custom) {
                    $cells[] = '<span class="label">'.get_string("answer", "lesson")." $i</span>: \n";
                } else if ($this->lesson->jumpto_is_correct($this->properties->id, $answer->jumpto)) {
                    $cells[] = '<span class="labelcorrect">'.get_string("answer", "lesson")." $i</span>: \n";
                } else {
                    $cells[] = '<span class="label">'.get_string("answer", "lesson")." $i</span>: \n";
                }
                $cells[] = format_text($answer->answer, FORMAT_MOODLE, $options);
                $table->data[] = new html_table_row($cells);

                $cells = array();
                $cells[] = '<span class="label">'.get_string("matchesanswer", "lesson")." $i</span>: ";
                $cells[] = format_text($answer->response, FORMAT_MOODLE, $options);
                $table->data[] = new html_table_row($cells);
            }

            if ($i == 1) {
                $cells = array();
                $cells[] = '<span class="label">'.get_string("correctanswerscore", "lesson")." $i</span>: ";
                $cells[] = $answer->score;
                $table->data[] = new html_table_row($cells);

                $cells = array();
                $cells[] = '<span class="label">'.get_string("correctanswerjump", "lesson")." $i</span>: ";
                $cells[] = $this->get_jump_name($answer->jumpto);
                $table->data[] = new html_table_row($cells);
            } elseif ($i == 2) {
                $cells = array();
                $cells[] = '<span class="label">'.get_string("wronganswerscore", "lesson")." $i</span>: ";
                $cells[] = $answer->score;
                $table->data[] = new html_table_row($cells);

                $cells = array();
                $cells[] = '<span class="label">'.get_string("wronganswerjump", "lesson")." $i</span>: ";
                $cells[] = $this->get_jump_name($answer->jumpto);
                $table->data[] = new html_table_row($cells);
            }

            if ($i === 1){
                $table->data[count($table->data)-1]->cells[0]->style = 'width:20%;';
            }

            $i++;
        }
        return $table;
    }
    public function update($properties) {
        global $DB, $PAGE;
        $answers  = $this->get_answers();
        $properties->id = $this->properties->id;
        $properties->lessonid = $this->lesson->id;
        $properties = file_postupdate_standard_editor($properties, 'contents', array('noclean'=>true, 'maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$PAGE->course->maxbytes), get_context_instance(CONTEXT_MODULE, $PAGE->cm->id), 'lesson_page_contents', $properties->id);
        $DB->update_record("lesson_pages", $properties);

        // need to add two to offset correct response and wrong response
        $this->lesson->maxanswers += 2;
        for ($i = 0; $i < $this->lesson->maxanswers; $i++) {
            if (!array_key_exists($i, $this->answers)) {
                $this->answers[$i] = new stdClass;
                $this->answers[$i]->lessonid = $this->lesson->id;
                $this->answers[$i]->pageid = $this->id;
                $this->answers[$i]->timecreated = $this->timecreated;
            }
            if (!empty($properties->answer[$i])) {
                $this->answers[$i]->answer = format_text($properties->answer[$i], FORMAT_PLAIN);
                if (isset($properties->response[$i])) {
                    $this->answers[$i]->response = format_text($properties->response[$i], FORMAT_PLAIN);
                }
                if (isset($properties->jumpto[$i])) {
                    $this->answers[$i]->jumpto = $properties->jumpto[$i];
                }
                if ($this->lesson->custom && isset($properties->score[$i])) {
                    $this->answers[$i]->score = $properties->score[$i];
                }
                if (!isset($this->answers[$i]->id)) {
                    $this->answers[$i]->id =  $DB->insert_record("lesson_answers", $this->answers[$i]);
                } else {
                    $DB->update_record("lesson_answers", $this->answers[$i]->properties());
                }

            } else if ($i < 2) {
                if (!isset($this->answers[$i]->id)) {
                    $this->answers[$i]->id =  $DB->insert_record("lesson_answers", $this->answers[$i]);
                } else {
                    $DB->update_record("lesson_answers", $this->answers[$i]->properties());
                }

            } else {
                break;
            }
        }
        return true;
    }
    public function stats(array &$pagestats, $tries) {
        if(count($tries) > $this->lesson->maxattempts) { // if there are more tries than the max that is allowed, grab the last "legal" attempt
            $temp = $tries[$this->lesson->maxattempts - 1];
        } else {
            // else, user attempted the question less than the max, so grab the last one
            $temp = end($tries);
        }
        if ($temp->correct) {
            if (isset($pagestats[$temp->pageid]["correct"])) {
                $pagestats[$temp->pageid]["correct"]++;
            } else {
                $pagestats[$temp->pageid]["correct"] = 1;
            }
        }
        if (isset($pagestats[$temp->pageid]["total"])) {
            $pagestats[$temp->pageid]["total"]++;
        } else {
            $pagestats[$temp->pageid]["total"] = 1;
        }
        return true;
    }
    public function report_answers($answerpage, $answerdata, $useranswer, $pagestats, &$i, &$n) {
        $answers = array();
        foreach ($this->get_answers() as $answer) {
            $answers[$answer->id] = $answer;
        }
        $formattextdefoptions = new stdClass;
        $formattextdefoptions->para = false;  //I'll use it widely in this page
        foreach ($answers as $answer) {
            if ($n == 0 && $useranswer != NULL && $useranswer->correct) {
                if ($answer->response == NULL && $useranswer != NULL) {
                    $answerdata->response = get_string("thatsthecorrectanswer", "lesson");
                } else {
                    $answerdata->response = $answer->response;
                }
            } elseif ($n == 1 && $useranswer != NULL && !$useranswer->correct) {
                if ($answer->response == NULL && $useranswer != NULL) {
                    $answerdata->response = get_string("thatsthewronganswer", "lesson");
                } else {
                    $answerdata->response = $answer->response;
                }
            } elseif ($n > 1) {
                if ($n == 2 && $useranswer != NULL && $useranswer->correct) {
                    if ($this->lesson->custom) {
                        $answerdata->score = get_string("pointsearned", "lesson").": ".$answer->score;
                    } else {
                        $answerdata->score = get_string("receivedcredit", "lesson");
                    }
                } elseif ($n == 3 && $useranswer != NULL && !$useranswer->correct) {
                    if ($this->lesson->custom) {
                        $answerdata->score = get_string("pointsearned", "lesson").": ".$answer->score;
                    } else {
                        $answerdata->score = get_string("didnotreceivecredit", "lesson");
                    }
                }
                $data = "<select disabled=\"disabled\"><option selected=\"selected\">".strip_tags(format_string($answer->answer))."</option></select>";
                if ($useranswer != NULL) {
                    $userresponse = explode(",", $useranswer->useranswer);
                    $data .= "<select disabled=\"disabled\"><option selected=\"selected\">".strip_tags(format_string($answers[$userresponse[$i]]->response))."</option></select>";
                } else {
                    $data .= "<select disabled=\"disabled\"><option selected=\"selected\">".strip_tags(format_string($answer->response))."</option></select>";
                }

                if ($n == 2) {
                    if (isset($pagestats[$this->properties->id])) {
                        if (!array_key_exists('correct', $pagestats[$this->properties->id])) {
                            $pagestats[$this->properties->id]["correct"] = 0;
                        }
                        $percent = $pagestats[$this->properties->id]["correct"] / $pagestats[$this->properties->id]["total"] * 100;
                        $percent = round($percent, 2);
                        $percent .= "% ".get_string("answeredcorrectly", "lesson");
                    } else {
                        $percent = get_string("nooneansweredthisquestion", "lesson");
                    }
                } else {
                    $percent = "";
                }

                $answerdata->answers[] = array($data, $percent);
                $i++;
            }
            $n++;
            $answerpage->answerdata = $answerdata;
        }
        return $answerpage;
    }
    public function get_jumps() {
        global $DB;
        // The jumps for matching question type is stored
        // in the 3rd and 4rth answer record.
        $jumps = array();
        $params = array ("lessonid" => $this->lesson->id, "pageid" => $this->properties->id);
        if ($answers = $DB->get_records_select("lesson_answers", "lessonid = :lessonid and pageid = :pageid", $params, 'id', '*', '2', '2')) {
            foreach ($answers as $answer) {
                $jumps[] = $this->get_jump_name($answer->jumpto);
            }
        }
        return $jumps;
    }
}

class lesson_add_page_form_matching extends lesson_add_page_form_base {

    public $qtype = 'matching';
    public $qtypestring = 'matching';

    public function custom_definition() {

        $this->_form->addElement('header', 'correctresponse', get_string('correctresponse', 'lesson'));
        $this->add_textarea('answer', 0, get_string('correctresponse', 'lesson'));
        $this->add_jumpto(2, get_string('correctanswerjump','lesson'));
        $this->add_score(2, get_string("correctanswerscore", "lesson"));

        $this->_form->addElement('header', 'wrongresponse', get_string('wrongresponse', 'lesson'));
        $this->add_textarea('answer', 1, get_string('wrongresponse', 'lesson'));
        $this->add_jumpto(3, get_string('wronganswerjump','lesson'));
        $this->add_score(3, get_string("wronganswerscore", "lesson"));

        for ($i = 2; $i < $this->_customdata['lesson']->maxanswers+2; $i++) {
            $this->_form->addElement('header', 'matchingpair'.($i-1), get_string('matchingpair', 'lesson', $i-1));
            $this->add_answer($i);
            $this->add_response($i, get_string('matchesanswer','lesson'));
        }
    }
}


class lesson_display_answer_form_matching extends moodleform {

    public function definition() {
        global $USER, $OUTPUT;
        $mform = $this->_form;
        $answers = $this->_customdata['answers'];
        $useranswers = $this->_customdata['useranswers'];
        $responseoptions = $this->_customdata['responseoptions'];
        $lessonid = $this->_customdata['lessonid'];
        $contents = $this->_customdata['contents'];

        $mform->addElement('header', 'pageheader', $OUTPUT->box($contents, 'contents'));

        $options = new stdClass;
        $options->para = false;
        $options->noclean = true;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'pageid');
        $mform->setType('pageid', PARAM_INT);

        $i = 0;
        foreach ($answers as $answer) {
            $mform->addElement('html', '<div class="answeroption">');
            if ($answer->response != NULL) {
                $mform->addElement('select', 'response['.$answer->id.']', format_text($answer->answer,FORMAT_MOODLE,$options), $responseoptions);
                $mform->setType('response['.$answer->id.']', PARAM_TEXT);
                if (isset($USER->modattempts[$lessonid])) {
                    $mform->setDefault('response['.$answer->id.']', htmlspecialchars(trim($answers[$useranswers[$t]]->response)));
                } else {
                    $mform->setDefault('response['.$answer->id.']', 'answeroption');
                }
            }
            $mform->addElement('html', '</div>');
            $i++;
        }

        $this->add_action_buttons(null, get_string("pleasematchtheabovepairs", "lesson"));
    }

}