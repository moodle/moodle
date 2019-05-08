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
 * Essay
 *
 * @package mod_lesson
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

/** Essay question type */
define("LESSON_PAGE_ESSAY", "10");

class lesson_page_type_essay extends lesson_page {

    protected $type = lesson_page::TYPE_QUESTION;
    protected $typeidstring = 'essay';
    protected $typeid = LESSON_PAGE_ESSAY;
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

    /**
     * Unserialize attempt useranswer and add missing responseformat if needed
     * for compatibility with old records.
     *
     * @param string $useranswer serialized object
     * @return object
     */
    static public function extract_useranswer($useranswer) {
        $essayinfo = unserialize($useranswer);
        if (!isset($essayinfo->responseformat)) {
            $essayinfo->response = text_to_html($essayinfo->response, false, false);
            $essayinfo->responseformat = FORMAT_HTML;
        }
        return $essayinfo;
    }

    public function display($renderer, $attempt) {
        global $PAGE, $CFG, $USER;

        $context = context_module::instance($PAGE->cm->id);
        $options = array(
            'contents' => $this->get_contents(),
            'lessonid' => $this->lesson->id,
            'attemptid' => $attempt ? $attempt->id : null,
            'editoroptions' => array(
                'maxbytes' => $PAGE->course->maxbytes,
                'context' => $context,
                'noclean' => true,
                'maxfiles' => EDITOR_UNLIMITED_FILES,
                'enable_filemanagement' => false
            )
        );
        $mform = new lesson_display_answer_form_essay($CFG->wwwroot.'/mod/lesson/continue.php', $options);

        $data = new stdClass;
        $data->id = $PAGE->cm->id;
        $data->pageid = $this->properties->id;
        if (isset($USER->modattempts[$this->lesson->id])) {
            $essayinfo = self::extract_useranswer($attempt->useranswer);
            $data->answer = $essayinfo->answer;
        }

        $data = file_prepare_standard_editor($data, 'answer', $options['editoroptions'],
            $context, 'mod_lesson', 'essay_answers');
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
    public function create_answers($properties) {
        global $DB;
        // now add the answers
        $newanswer = new stdClass;
        $newanswer->lessonid = $this->lesson->id;
        $newanswer->pageid = $this->properties->id;
        $newanswer->timecreated = $this->properties->timecreated;

        if (isset($properties->jumpto[0])) {
            $newanswer->jumpto = $properties->jumpto[0];
        }
        if (isset($properties->score[0])) {
            $newanswer->score = $properties->score[0];
        }
        $newanswer->id = $DB->insert_record("lesson_answers", $newanswer);
        $answers = array($newanswer->id => new lesson_page_answer($newanswer));
        $this->answers = $answers;
        return $answers;
    }

    /**
     * Overridden function
     *
     * @param object $attempt
     * @param object $result
     * @return array
     */
    public function on_after_write_attempt($attempt, $result) {
        global $PAGE;

        if ($formdata = $result->postdata) {
            // Save any linked files if we are using an editor.
            $editoroptions = array(
                'maxbytes' => $PAGE->course->maxbytes,
                'context' => context_module::instance($PAGE->cm->id),
                'noclean' => true, 'maxfiles' => EDITOR_UNLIMITED_FILES,
                'enable_filemanagement' => false,
            );

            $formdata = file_postupdate_standard_editor($formdata, 'answer', $editoroptions,
                $editoroptions['context'], 'mod_lesson', 'essay_answers', $attempt->id);

            // Update the student response to have the modified link.
            $useranswer = unserialize($attempt->useranswer);
            $useranswer->answer = $formdata->answer;
            $useranswer->answerformat = $formdata->answerformat;
            $attempt->useranswer = serialize($useranswer);

            $result->studentanswer = $formdata->answer;
            $result->studentanswerformat = $formdata->answerformat;
            return [$attempt, $result];
        }

        return parent::on_after_write_attempt($attempt, $result);
    }

    /**
     * Custom formats the answer to display
     *
     * @param string $answer
     * @param context $context
     * @param int $answerformat
     * @param array $options Optional param for additional options.
     * @return string Returns formatted string
     */
    public function format_answer($answer, $context, $answerformat, $options = []) {
        $answer = file_rewrite_pluginfile_urls($answer, 'pluginfile.php', $context->id,
            'mod_lesson', 'essay_answers', $options->attemptid);
        return parent::format_answer($answer, $context, $answerformat, $options);
    }

    public function check_answer() {
        global $PAGE, $CFG;
        $result = parent::check_answer();
        $result->isessayquestion = true;
        $context = context_module::instance($PAGE->cm->id);
        $options = array(
            'contents' => $this->get_contents(),
            'editoroptions' => array(
                'maxbytes' => $PAGE->course->maxbytes,
                'context' => $context,
                'noclean' => true,
                'maxfiles' => EDITOR_UNLIMITED_FILES,
                'enable_filemanagement' => false,
            )
        );
        $mform = new lesson_display_answer_form_essay($CFG->wwwroot.'/mod/lesson/continue.php', $options);
        $data = $mform->get_data();
        require_sesskey();

        if (!$data) {
            $result->inmediatejump = true;
            $result->newpageid = $this->properties->id;
            return $result;
        }

        if (is_array($data->answer_editor) && strlen($data->answer_editor['text'])) {
            $studentanswer = $data->answer_editor['text']; // Will be reset later.
            $studentanswerformat = $data->answer_editor['format']; // Will be reset later.
        } else {
            $studentanswer = isset($data->answer) ? $data->answer : '';
            $studentanswerformat = FORMAT_HTML;
        }

        if (trim($studentanswer) === '') {
            $result->noanswer = true;
            return $result;
        }

        $answers = $this->get_answers();
        foreach ($answers as $answer) {
            $result->answerid = $answer->id;
            $result->newpageid = $answer->jumpto;
        }

        $userresponse = new stdClass;
        $userresponse->sent=0;
        $userresponse->graded = 0;
        $userresponse->score = 0;
        $userresponse->answer = $studentanswer;
        $userresponse->answerformat = $studentanswerformat;
        $userresponse->response = '';
        $userresponse->responseformat = FORMAT_HTML;
        $result->userresponse = serialize($userresponse);
        $result->studentanswerformat = $studentanswerformat;
        $result->studentanswer = $studentanswer;
        $result->postdata = $data;
        return $result;
    }
    public function update($properties, $context = null, $maxbytes = null) {
        global $DB, $PAGE;
        $answers  = $this->get_answers();
        $properties->id = $this->properties->id;
        $properties->lessonid = $this->lesson->id;
        $properties->timemodified = time();
        $properties = file_postupdate_standard_editor($properties, 'contents', array('noclean'=>true, 'maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$PAGE->course->maxbytes), context_module::instance($PAGE->cm->id), 'mod_lesson', 'page_contents', $properties->id);
        $DB->update_record("lesson_pages", $properties);

        // Trigger an event: page updated.
        \mod_lesson\event\page_updated::create_from_lesson_page($this, $context)->trigger();

        if (!array_key_exists(0, $this->answers)) {
            $this->answers[0] = new stdClass;
            $this->answers[0]->lessonid = $this->lesson->id;
            $this->answers[0]->pageid = $this->id;
            $this->answers[0]->timecreated = $this->timecreated;
        }
        if (isset($properties->jumpto[0])) {
            $this->answers[0]->jumpto = $properties->jumpto[0];
        }
        if (isset($properties->score[0])) {
            $this->answers[0]->score = $properties->score[0];
        }
        if (!isset($this->answers[0]->id)) {
            $this->answers[0]->id =  $DB->insert_record("lesson_answers", $this->answers[0]);
        } else {
            $DB->update_record("lesson_answers", $this->answers[0]->properties());
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
        $essayinfo = self::extract_useranswer($temp->useranswer);
        if ($essayinfo->graded) {
            if (isset($pagestats[$temp->pageid])) {
                $essaystats = $pagestats[$temp->pageid];
                $essaystats->totalscore += $essayinfo->score;
                $essaystats->total++;
                $pagestats[$temp->pageid] = $essaystats;
            } else {
                $essaystats = new stdClass();
                $essaystats->totalscore = $essayinfo->score;
                $essaystats->total = 1;
                $pagestats[$temp->pageid] = $essaystats;
            }
        }
        return true;
    }
    public function report_answers($answerpage, $answerdata, $useranswer, $pagestats, &$i, &$n) {
        global $PAGE, $DB;

        $formattextdefoptions = new stdClass();
        $formattextdefoptions->noclean = true;
        $formattextdefoptions->para = false;
        $formattextdefoptions->context = $answerpage->context;
        $answers = $this->get_answers();
        $context = context_module::instance($PAGE->cm->id);
        foreach ($answers as $answer) {
            $hasattempts = $DB->record_exists('lesson_attempts', ['answerid' => $answer->id]);
            if ($useranswer != null) {
                $essayinfo = self::extract_useranswer($useranswer->useranswer);
                $essayinfo->answer = file_rewrite_pluginfile_urls($essayinfo->answer, 'pluginfile.php',
                    $context->id, 'mod_lesson', 'essay_answers', $useranswer->id);

                if ($essayinfo->response == null) {
                    $answerdata->response = get_string("nocommentyet", "lesson");
                } else {
                    $essayinfo->response = file_rewrite_pluginfile_urls($essayinfo->response, 'pluginfile.php',
                            $answerpage->context->id, 'mod_lesson', 'essay_responses', $useranswer->id);
                    $answerdata->response  = format_text($essayinfo->response, $essayinfo->responseformat, $formattextdefoptions);
                }
                if (isset($pagestats[$this->properties->id])) {
                    $percent = $pagestats[$this->properties->id]->totalscore / $pagestats[$this->properties->id]->total * 100;
                    $percent = round($percent, 2);
                    $percent = get_string("averagescore", "lesson").": ". $percent ."%";
                } else {
                    // dont think this should ever be reached....
                    $percent = get_string("nooneansweredthisquestion", "lesson");
                }
                if ($essayinfo->graded) {
                    if ($this->lesson->custom) {
                        $answerdata->score = get_string("pointsearned", "lesson").": " . $essayinfo->score;
                    } elseif ($essayinfo->score) {
                        $answerdata->score = get_string("receivedcredit", "lesson");
                    } else {
                        $answerdata->score = get_string("didnotreceivecredit", "lesson");
                    }
                } else {
                    $answerdata->score = get_string("havenotgradedyet", "lesson");
                }
            } else {
                $essayinfo = new stdClass();
                if ($hasattempts && has_capability('mod/lesson:grade', $answerpage->context)) {
                    $essayinfo->answer = html_writer::link(new moodle_url("/mod/lesson/essay.php",
                        ['id' => $PAGE->cm->id]), get_string("viewessayanswers", "lesson"));
                } else {
                    $essayinfo->answer = "";
                }
                $essayinfo->answerformat = null;
            }

            // The essay question has been graded.
            if (isset($pagestats[$this->properties->id])) {
                $avescore = $pagestats[$this->properties->id]->totalscore / $pagestats[$this->properties->id]->total;
                $avescore = round($avescore, 2);
                $avescore = get_string("averagescore", "lesson").": ". $avescore ;
            } else {
                $avescore = $hasattempts ? get_string("essaynotgradedyet", "lesson") :
                        get_string("nooneansweredthisquestion", "lesson");
            }
            // This is the student's answer so it should be cleaned.
            $answerdata->answers[] = array(format_text($essayinfo->answer, $essayinfo->answerformat,
                    array('para' => true, 'context' => $answerpage->context)), $avescore);
            $answerpage->answerdata = $answerdata;
        }
        return $answerpage;
    }
    public function is_unanswered($nretakes) {
        global $DB, $USER;
        if (!$DB->count_records("lesson_attempts", array('pageid'=>$this->properties->id, 'userid'=>$USER->id, 'retry'=>$nretakes))) {
            return true;
        }
        return false;
    }
    public function requires_manual_grading() {
        return true;
    }
    public function get_earnedscore($answers, $attempt) {
        $essayinfo = self::extract_useranswer($attempt->useranswer);
        return $essayinfo->score;
    }
}

class lesson_add_page_form_essay extends lesson_add_page_form_base {

    public $qtype = 'essay';
    public $qtypestring = 'essay';

    public function custom_definition() {

        $this->add_jumpto(0);
        $this->add_score(0, null, 1);

    }
}

class lesson_display_answer_form_essay extends moodleform {

    public function definition() {
        global $USER, $OUTPUT;
        $mform = $this->_form;
        $contents = $this->_customdata['contents'];
        $editoroptions = $this->_customdata['editoroptions'];

        $hasattempt = false;
        $attrs = '';
        $useranswer = '';
        $useranswerraw = '';
        if (isset($this->_customdata['lessonid'])) {
            $lessonid = $this->_customdata['lessonid'];
            if (isset($USER->modattempts[$lessonid]->useranswer) && !empty($USER->modattempts[$lessonid]->useranswer)) {
                $attrs = array('disabled' => 'disabled');
                $hasattempt = true;
                $useranswertemp = lesson_page_type_essay::extract_useranswer($USER->modattempts[$lessonid]->useranswer);
                $useranswer = htmlspecialchars_decode($useranswertemp->answer, ENT_QUOTES);
                $useranswerraw = $useranswertemp->answer;
            }
        }

        // Disable shortforms.
        $mform->setDisableShortforms();

        $mform->addElement('header', 'pageheader');

        $mform->addElement('html', $OUTPUT->container($contents, 'contents'));

        $options = new stdClass;
        $options->para = false;
        $options->noclean = true;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'pageid');
        $mform->setType('pageid', PARAM_INT);

        if ($hasattempt) {
            $mform->addElement('hidden', 'answer', $useranswerraw);
            $mform->setType('answer', PARAM_RAW);
            $mform->addElement('html', $OUTPUT->container(get_string('youranswer', 'lesson'), 'youranswer'));
            $useranswer = file_rewrite_pluginfile_urls($useranswer, 'pluginfile.php', $editoroptions['context']->id,
                'mod_lesson', 'essay_answers', $this->_customdata['attemptid']);
            $mform->addElement('html', $OUTPUT->container($useranswer, 'reviewessay'));
            $this->add_action_buttons(null, get_string("nextpage", "lesson"));
        } else {
            $mform->addElement('editor', 'answer_editor', get_string('youranswer', 'lesson'), null, $editoroptions);
            $mform->setType('answer_editor', PARAM_RAW);
            $this->add_action_buttons(null, get_string("submit", "lesson"));
        }
    }
}
