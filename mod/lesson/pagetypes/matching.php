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
 * @package mod_lesson
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

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
        // don't shuffle answers (could be an option??)
        $getanswers = array_slice($this->get_answers(), 2);

        $answers = array();
        foreach ($getanswers as $getanswer) {
            $answers[$getanswer->id] = $getanswer;
        }

        $responses = array();
        foreach ($answers as $answer) {
            // get all the response
            if ($answer->response != null) {
                $responses[] = trim($answer->response);
            }
        }

        $responseoptions = array(''=>get_string('choosedots'));
        if (!empty($responses)) {
            shuffle($responses);
            foreach ($responses as  $response) {
                $responseoptions[htmlspecialchars($response)] = $response;
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
        global $DB, $PAGE;
        // now add the answers
        $newanswer = new stdClass;
        $newanswer->lessonid = $this->lesson->id;
        $newanswer->pageid = $this->properties->id;
        $newanswer->timecreated = $this->properties->timecreated;

        $cm = get_coursemodule_from_instance('lesson', $this->lesson->id, $this->lesson->course);
        $context = context_module::instance($cm->id);

        $answers = array();

        // need to add two to offset correct response and wrong response
        $this->lesson->maxanswers = $this->lesson->maxanswers + 2;
        for ($i = 0; $i < $this->lesson->maxanswers; $i++) {
            $answer = clone($newanswer);
            if (!empty($properties->answer_editor[$i]) && is_array($properties->answer_editor[$i])) {
                $answer->answer = $properties->answer_editor[$i]['text'];
                $answer->answerformat = $properties->answer_editor[$i]['format'];
            }
            if (!empty($properties->response_editor[$i])) {
                $answer->response = $properties->response_editor[$i];
                $answer->responseformat = 0;
            }

            if (isset($properties->jumpto[$i])) {
                $answer->jumpto = $properties->jumpto[$i];
            }
            if ($this->lesson->custom && isset($properties->score[$i])) {
                $answer->score = $properties->score[$i];
            }

            if (isset($answer->answer) && $answer->answer != '') {
                $answer->id = $DB->insert_record("lesson_answers", $answer);
                $this->save_answers_files($context, $PAGE->course->maxbytes,
                        $answer, $properties->answer_editor[$i]);
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
        global $CFG, $PAGE;

        $formattextdefoptions = new stdClass();
        $formattextdefoptions->noclean = true;
        $formattextdefoptions->para = false;

        $result = parent::check_answer();

        $mform = $this->make_answer_form();

        $data = $mform->get_data();
        require_sesskey();

        if (!$data) {
            redirect(new moodle_url('/mod/lesson/view.php', array('id'=>$PAGE->cm->id, 'pageid'=>$this->properties->id)));
        }

        $response = $data->response;
        $getanswers = $this->get_answers();
        foreach ($getanswers as $key => $answer) {
            $getanswers[$key] = parent::rewrite_answers_urls($answer);
        }

        $correct = array_shift($getanswers);
        $wrong   = array_shift($getanswers);

        $answers = array();
        foreach ($getanswers as $key => $answer) {
            if ($answer->answer !== '' or $answer->response !== '') {
                $answers[$answer->id] = $answer;
            }
        }

        // get the user's exact responses for record keeping
        $hits = 0;
        $userresponse = array();
        foreach ($response as $id => $value) {
            if ($value == '') {
                $result->noanswer = true;
                return $result;
            }
            $value = htmlspecialchars_decode($value);
            $userresponse[] = $value;
            // Make sure the user's answer exists in question's answer
            if (array_key_exists($id, $answers)) {
                $answer = $answers[$id];
                $result->studentanswer .= '<br />'.format_text($answer->answer, $answer->answerformat, $formattextdefoptions).' = '.$value;
                if (trim($answer->response) == trim($value)) {
                    $hits++;
                }
            }
        }

        $result->userresponse = implode(",", $userresponse);

        if ($hits == count($answers)) {
            $result->correctanswer = true;
            $result->response      = format_text($correct->answer, $correct->answerformat, $formattextdefoptions);
            $result->answerid      = $correct->id;
            $result->newpageid     = $correct->jumpto;
        } else {
            $result->correctanswer = false;
            $result->response      = format_text($wrong->answer, $wrong->answerformat, $formattextdefoptions);
            $result->answerid      = $wrong->id;
            $result->newpageid     = $wrong->jumpto;
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
            $answer = parent::rewrite_answers_urls($answer);
            if ($n < 2) {
                if ($answer->answer != null) {
                    $cells = array();
                    if ($n == 0) {
                        $cells[] = "<span class=\"label\">".get_string("correctresponse", "lesson").'</span>';
                    } else {
                        $cells[] = "<span class=\"label\">".get_string("wrongresponse", "lesson").'</span>';
                    }
                    $cells[] = format_text($answer->answer, $answer->answerformat, $options);
                    $table->data[] = new html_table_row($cells);
                }

                if ($n == 0) {
                    $cells = array();
                    $cells[] = '<span class="label">'.get_string("correctanswerscore", "lesson")."</span>: ";
                    $cells[] = $answer->score;
                    $table->data[] = new html_table_row($cells);

                    $cells = array();
                    $cells[] = '<span class="label">'.get_string("correctanswerjump", "lesson")."</span>: ";
                    $cells[] = $this->get_jump_name($answer->jumpto);
                    $table->data[] = new html_table_row($cells);
                } elseif ($n == 1) {
                    $cells = array();
                    $cells[] = '<span class="label">'.get_string("wronganswerscore", "lesson")."</span>: ";
                    $cells[] = $answer->score;
                    $table->data[] = new html_table_row($cells);

                    $cells = array();
                    $cells[] = '<span class="label">'.get_string("wronganswerjump", "lesson")."</span>: ";
                    $cells[] = $this->get_jump_name($answer->jumpto);
                    $table->data[] = new html_table_row($cells);
                }

                if ($n === 0){
                    $table->data[count($table->data)-1]->cells[0]->style = 'width:20%;';
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
                $cells[] = format_text($answer->answer, $answer->answerformat, $options);
                $table->data[] = new html_table_row($cells);

                $cells = array();
                $cells[] = '<span class="label">'.get_string("matchesanswer", "lesson")." $i</span>: ";
                $cells[] = format_text($answer->response, $answer->responseformat, $options);
                $table->data[] = new html_table_row($cells);
            }
            $i++;
        }
        return $table;
    }
    /**
     * Updates the page and its answers
     *
     * @global moodle_database $DB
     * @global moodle_page $PAGE
     * @param stdClass $properties
     * @return bool
     */
    public function update($properties, $context = null, $maxbytes = null) {
        global $DB, $PAGE;
        $answers  = $this->get_answers();
        $properties->id = $this->properties->id;
        $properties->lessonid = $this->lesson->id;
        $properties->timemodified = time();
        $properties = file_postupdate_standard_editor($properties, 'contents', array('noclean'=>true, 'maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$PAGE->course->maxbytes), context_module::instance($PAGE->cm->id), 'mod_lesson', 'page_contents', $properties->id);
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

            if (!empty($properties->answer_editor[$i]) && is_array($properties->answer_editor[$i])) {
                $this->answers[$i]->answer = $properties->answer_editor[$i]['text'];
                $this->answers[$i]->answerformat = $properties->answer_editor[$i]['format'];
            }
            if (!empty($properties->response_editor[$i])) {
                $this->answers[$i]->response = $properties->response_editor[$i];
                $this->answers[$i]->responseformat = 0;
            }

            if (isset($properties->jumpto[$i])) {
                $this->answers[$i]->jumpto = $properties->jumpto[$i];
            }
            if ($this->lesson->custom && isset($properties->score[$i])) {
                $this->answers[$i]->score = $properties->score[$i];
            }

            // we don't need to check for isset here because properties called it's own isset method.
            if ($this->answers[$i]->answer != '') {
                if (!isset($this->answers[$i]->id)) {
                    $this->answers[$i]->id =  $DB->insert_record("lesson_answers", $this->answers[$i]);
                } else {
                    $DB->update_record("lesson_answers", $this->answers[$i]->properties());
                }
                // Save files in answers and responses.
                $this->save_answers_files($context, $maxbytes, $this->answers[$i],
                        $properties->answer_editor[$i], $properties->response_editor[$i]);
            } else if ($i < 2) {
                if (!isset($this->answers[$i]->id)) {
                    $this->answers[$i]->id =  $DB->insert_record("lesson_answers", $this->answers[$i]);
                } else {
                    $DB->update_record("lesson_answers", $this->answers[$i]->properties());
                }

                // Save files in answers and responses.
                $this->save_answers_files( $context, $maxbytes, $this->answers[$i],
                        $properties->answer_editor[$i], $properties->response_editor[$i]);
            } else if (isset($this->answers[$i]->id)) {
                $DB->delete_records('lesson_answers', array('id'=>$this->answers[$i]->id));
                unset($this->answers[$i]);
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
            if ($n == 0 && $useranswer != null && $useranswer->correct) {
                if ($answer->response == null && $useranswer != null) {
                    $answerdata->response = get_string("thatsthecorrectanswer", "lesson");
                } else {
                    $answerdata->response = $answer->response;
                }
                if ($this->lesson->custom) {
                    $answerdata->score = get_string("pointsearned", "lesson").": ".$answer->score;
                } else {
                    $answerdata->score = get_string("receivedcredit", "lesson");
                }
            } elseif ($n == 1 && $useranswer != null && !$useranswer->correct) {
                if ($answer->response == null && $useranswer != null) {
                    $answerdata->response = get_string("thatsthewronganswer", "lesson");
                } else {
                    $answerdata->response = $answer->response;
                }
                if ($this->lesson->custom) {
                    $answerdata->score = get_string("pointsearned", "lesson").": ".$answer->score;
                } else {
                    $answerdata->score = get_string("didnotreceivecredit", "lesson");
                }
            } elseif ($n > 1) {
                $data = '<label class="accesshide" for="answer_' . $n . '">' . get_string('answer', 'lesson') . '</label>';
                $data .= strip_tags(format_string($answer->answer)) . ' ';
                if ($useranswer != null) {
                    $userresponse = explode(",", $useranswer->useranswer);
                    $data .= '<label class="accesshide" for="stu_answer_response_' . $n . '">' . get_string('matchesanswer', 'lesson') . '</label>';
                    $data .= "<select id=\"stu_answer_response_" . $n . "\" disabled=\"disabled\"><option selected=\"selected\">";
                    if (array_key_exists($i, $userresponse)) {
                        $data .= $userresponse[$i];
                    }
                    $data .= "</option></select>";
                } else {
                    $data .= '<label class="accesshide" for="answer_response_' . $n . '">' . get_string('matchesanswer', 'lesson') . '</label>';
                    $data .= "<select id=\"answer_response_" . $n . "\" disabled=\"disabled\"><option selected=\"selected\">".strip_tags(format_string($answer->response))."</option></select>";
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
                    $percent = '';
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
        // The jumps for matching question type are stored in the 1st and 2nd answer record.
        $jumps = array();
        if ($answers = $DB->get_records("lesson_answers", array("lessonid" => $this->lesson->id, "pageid" => $this->properties->id), 'id', '*', 0, 2)) {
            foreach ($answers as $answer) {
                $jumps[] = $this->get_jump_name($answer->jumpto);
            }
        } else {
            $jumps[] = $this->get_jump_name($this->properties->nextpageid);
        }
        return $jumps;
    }
}

class lesson_add_page_form_matching extends lesson_add_page_form_base {

    public $qtype = 'matching';
    public $qtypestring = 'matching';

    public function custom_definition() {

        $this->_form->addElement('header', 'correctresponse', get_string('correctresponse', 'lesson'));
        $this->_form->addElement('editor', 'answer_editor[0]', get_string('correctresponse', 'lesson'),
                array('rows' => '4', 'columns' => '80'),
                array('noclean' => true, 'maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes' => $this->_customdata['maxbytes']));
        $this->_form->setType('answer_editor[0]', PARAM_RAW);
        $this->_form->setDefault('answer_editor[0]', array('text' => '', 'format' => FORMAT_HTML));
        $this->add_jumpto(0, get_string('correctanswerjump','lesson'), LESSON_NEXTPAGE);
        $this->add_score(0, get_string("correctanswerscore", "lesson"), 1);

        $this->_form->addElement('header', 'wrongresponse', get_string('wrongresponse', 'lesson'));
        $this->_form->addElement('editor', 'answer_editor[1]', get_string('wrongresponse', 'lesson'),
                array('rows' => '4', 'columns' => '80'),
                array('noclean' => true, 'maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes' => $this->_customdata['maxbytes']));
        $this->_form->setType('answer_editor[1]', PARAM_RAW);
        $this->_form->setDefault('answer_editor[1]', array('text' => '', 'format' => FORMAT_HTML));

        $this->add_jumpto(1, get_string('wronganswerjump','lesson'), LESSON_THISPAGE);
        $this->add_score(1, get_string("wronganswerscore", "lesson"), 0);

        for ($i = 2; $i < $this->_customdata['lesson']->maxanswers+2; $i++) {
            $this->_form->addElement('header', 'matchingpair'.($i-1), get_string('matchingpair', 'lesson', $i-1));
            $this->add_answer($i, null, ($i < 4));
            $required = ($i < 4);
            $label = get_string('matchesanswer','lesson');
            $count = $i;
            $this->_form->addElement('text', 'response_editor['.$count.']', $label, array('size'=>'50'));
            $this->_form->setType('response_editor['.$count.']', PARAM_NOTAGS);
            $this->_form->setDefault('response_editor['.$count.']', '');
            if ($required) {
                $this->_form->addRule('response_editor['.$count.']', get_string('required'), 'required', null, 'client');
            }
        }
    }
}

class lesson_display_answer_form_matching extends moodleform {

    public function definition() {
        global $USER, $OUTPUT, $PAGE;
        $mform = $this->_form;
        $answers = $this->_customdata['answers'];
        $useranswers = $this->_customdata['useranswers'];
        $responseoptions = $this->_customdata['responseoptions'];
        $lessonid = $this->_customdata['lessonid'];
        $contents = $this->_customdata['contents'];

        $mform->addElement('header', 'pageheader');

        $mform->addElement('html', $OUTPUT->container($contents, 'contents'));

        $hasattempt = false;
        $disabled = '';
        if (isset($useranswers) && !empty($useranswers)) {
            $hasattempt = true;
            $disabled = array('disabled' => 'disabled');
        }

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
            if ($answer->response != null) {
                $responseid = 'response['.$answer->id.']';
                if ($hasattempt) {
                    $responseid = 'response_'.$answer->id;
                    $mform->addElement('hidden', 'response['.$answer->id.']', htmlspecialchars($useranswers[$i]));
                    // Temporary fixed until MDL-38885 gets integrated
                    $mform->setType('response', PARAM_TEXT);
                }
                $context = context_module::instance($PAGE->cm->id);
                $answer->answer = file_rewrite_pluginfile_urls($answer->answer, 'pluginfile.php', $context->id,
                        'mod_lesson', 'page_answers', $answer->id);
                $mform->addElement('select', $responseid, format_text($answer->answer,$answer->answerformat,$options), $responseoptions, $disabled);
                $mform->setType($responseid, PARAM_TEXT);
                if ($hasattempt) {
                    $mform->setDefault($responseid, htmlspecialchars(trim($useranswers[$i])));
                } else {
                    $mform->setDefault($responseid, 'answeroption');
                }
            }
            $mform->addElement('html', '</div>');
            $i++;
        }
        if ($hasattempt) {
            $this->add_action_buttons(null, get_string("nextpage", "lesson"));
        } else {
            $this->add_action_buttons(null, get_string("submit", "lesson"));
        }
    }

}
