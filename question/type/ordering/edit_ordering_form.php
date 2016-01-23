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
 * Defines the editing form for the multiple choice question type.
 *
 * @package    qtype
 * @subpackage ordering
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** Prevent direct access to this script */
defined('MOODLE_INTERNAL') || die();

/** Include required files */
require_once($CFG->dirroot.'/question/type/ordering/question.php');

/**
 * Ordering editing form definition
 * (originally based on mutiple choice form)
 *
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering_edit_form extends question_edit_form {

    const NUM_ANS_ROWS    =  2;
    const NUM_ANS_COLS    = 60;
    const NUM_ANS_DEFAULT =  6;
    const NUM_ANS_MIN     =  3;
    const NUM_ANS_ADD     =  3;

    // this functionality is currently disabled
    // because it is not fully functional
    protected $use_editor_for_answers = true;

    /**
     * unique name for this question type
     */
    public function qtype() {
        return 'ordering';
    }

    /**
     * Add question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    public function definition_inner($mform) {

        // cache this plugins name
        $plugin = 'qtype_ordering';

        // layouttype
        $name = 'layouttype';
        $label = get_string($name, $plugin);
        $options = qtype_ordering_question::get_layout_types();
        $mform->addElement('select', $name, $label, $options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, 0);

        // selecttype
        $name = 'selecttype';
        $label = get_string($name, $plugin);
        $options = qtype_ordering_question::get_select_types();
        $mform->addElement('select', $name, $label, $options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, 0);

        // selectcount
        $name = 'selectcount';
        $label = get_string($name, $plugin);
        $options = array(0 => get_string('all'));
        for ($i=3; $i <= 20; $i++) {
            $options[$i] = $i;
        }
        $mform->addElement('select', $name, $label, $options);
        $mform->disabledIf($name, 'selecttype', 'eq', 0);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, 0);

        // gradingtype
        $name = 'gradingtype';
        $label = get_string($name, $plugin);
        $options = qtype_ordering_question::get_grading_types();
        $mform->addElement('select', $name, $label, $options);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, 0);

        // answers (=items)
        //     get_per_answer_fields()
        //     add_per_answer_fields()
        $elements = array();
        $options = array();

        $name = 'answerheader';
        $label = get_string($name, $plugin);
        $elements[] = $mform->createElement('header', $name, $label);
        $options[$name] = array('expanded' => true);

        $name = 'answer';
        if (isset($this->question->id)) {
            $elements[] = $mform->createElement('editor', $name, $label, $this->get_editor_attributes(), $this->get_editor_options());
            $elements[] = $mform->createElement('submit', $name.'removeeditor', get_string('removeeditor', $plugin), array('onclick' => 'skipClientValidation = true;'));
            //$elements[] = $mform->createElement('submit', $name.'removeitem', get_string('removeitem', $plugin));
        } else {
            $elements[] = $mform->createElement('textarea', $name, $label, $this->get_editor_attributes());
        }
        $options[$name] = array('type' => PARAM_RAW);

        $repeats = $this->get_answer_repeats($this->question);
        $label = get_string('addmoreanswers', $plugin, self::NUM_ANS_ADD); // button text
        $this->repeat_elements($elements, $repeats, $options, 'countanswers', 'addanswers', self::NUM_ANS_ADD, $label);

        if (optional_param('addanswers', 0, PARAM_RAW)) {
            $repeats += self::NUM_ANS_ADD;
        }

        // adjust HTML editor and removal buttons
        $this->adjust_html_editors($mform, $name, $repeats);

        // feedback
        $this->add_ordering_feedback_fields(true);

        // interactive
        $this->add_ordering_interactive_settings(true, true);
    }

    protected function get_answer_repeats($question) {
        if (isset($question->id)) {
            $repeats = count($question->options->answers);
        } else {
            $repeats = self::NUM_ANS_DEFAULT;
        }
        if ($repeats < self::NUM_ANS_MIN) {
            $repeats = self::NUM_ANS_MIN;
        }
        return $repeats;
    }

    /**
     * get_editor_attributes
     */
    protected function get_editor_attributes() {
        return array(
            'rows'  => self::NUM_ANS_ROWS,
            'cols'  => self::NUM_ANS_COLS
        );
    }

    /**
     * get_editor_options
     */
    protected function get_editor_options() {
        return array(
            'context'  => $this->context,
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'noclean'  => true
        );
    }

    /**
     * reset_editor_format
     */
    protected function reset_editor_format($editor, $format=FORMAT_MOODLE) {
        $value = $editor->getValue();
        $value['format'] = $format;
        $value = $editor->setValue($value);
        return $format;
    }

    /**
     * adjust_html_editors
     */
    protected function adjust_html_editors($mform, $name, $repeats) {

        // cache the number of formats supported
        // by the preferred editor for each format
        $count = array();

        if (isset($this->question->options->answers)) {
            $ids = array_keys($this->question->options->answers);
        } else {
            $ids = array();
        }

        for ($i=0; $i<$repeats; $i++) {

            $editor = $name.'['.$i.']';
            if ($mform->elementExists($editor)) {
                $editor = $mform->getElement($editor);

                if (isset($ids[$i])) {
                    $id = $ids[$i];
                } else {
                    $id = 0;
                }

                // the old/new name of the button to remove the HTML editor
                // old : the name of the button when added by repeat_elements
                // new : the simplified name of the button to satisfy
                //       "no_submit_button_pressed()" in lib/formslib.php
                $oldname = $name.'removeeditor['.$i.']';
                $newname = $name.'removeeditor_'.$i;

                // remove HTML editor, if necessary
                if (optional_param($newname, 0, PARAM_RAW)) {
                    $format = $this->reset_editor_format($editor);
                    $_POST['answer'][$i]['format'] = $format; // overwrite incoming data
                } else if ($id) {
                    $format = $this->question->options->answers[$id]->answerformat;
                } else {
                    $format = $this->reset_editor_format($editor);
                }

                // check we have a submit button - it should always be there !!
                if ($mform->elementExists($oldname)) {
                    if (! isset($count[$format])) {
                        $editor = editors_get_preferred_editor($format);
                        $count[$format] = $editor->get_supported_formats();
                        $count[$format] = count($count[$format]);
                    }
                    if ($count[$format] > 1) {
                        $mform->removeElement($oldname);
                    } else {
                        $submit = $mform->getElement($oldname);
                        $submit->setName($newname);
                    }
                    $mform->registerNoSubmitButton($newname);
                }
            }
        }
    }

    /**
     * data_preprocessing
     */
    public function data_preprocessing($question) {

        $question = parent::data_preprocessing($question);
        //$question = $this->data_preprocessing_answers($question, true);

        // feedback
        $question = $this->data_preprocessing_ordering_feedback($question);

        // answers and fractions
        $question->answer     = array();
        $question->fraction   = array();

        if (empty($question->options->answers)) {
            $answerids = array();
        } else {
            $answerids = array_keys($question->options->answers);
        }

        $repeats = $this->get_answer_repeats($question);
        for ($i=0; $i<$repeats; $i++) {

            if ($answerid = array_shift($answerids)) {
                $answer = $question->options->answers[$answerid];
            } else {
                $answer = (object)array('answer' => '',
                                        'answerformat' => FORMAT_MOODLE);
                $answerid = 0;
            }

            if (empty($question->id)) {
                $question->answer[$i] = $answer->answer;
            } else {
                $itemid = file_get_submitted_draft_itemid("answer[$i]");
                $format = $answer->answerformat;
                $text = file_prepare_draft_area($itemid, $this->context->id, 'question', 'answer',
                                                $answerid, $this->editoroptions, $answer->answer);
                $question->answer[$i] = array('text' => $text,
                                              'format' => $format,
                                              'itemid' => $itemid);
            }
            $question->fraction[$i] = ($i + 1);
        }

        // layouttype
        if (isset($question->options->layouttype)) {
            $question->layouttype = $question->options->layouttype;
        } else {
            $question->layouttype = qtype_ordering_question::LAYOUT_VERTICAL;
        }

        // selecttype
        if (isset($question->options->selecttype)) {
            $question->selecttype = $question->options->selecttype;
        } else {
            $question->selecttype = qtype_ordering_question::SELECT_ALL;
        }

        // selectcount
        if (isset($question->options->selectcount)) {
            $question->selectcount = $question->options->selectcount;
        } else {
            $question->selectcount = max(3, count($question->answer));
        }

        // gradingtype
        if (isset($question->options->gradingtype)) {
            $question->gradingtype = $question->options->gradingtype;
        } else {
            $question->gradingtype = qtype_ordering_question::GRADING_ABSOLUTE_POSITION;
        }

        return $question;
    }

    public function validation($data, $files) {
        $errors = array();
        $plugin = 'qtype_ordering';

        $answercount = 0;
        foreach ($data['answer'] as $answer){
            if (is_array($answer)) {
                $answer = $answer['text'];
            }
            if (trim($answer)=='') {
                continue; // skip empty answer
            }
            $answercount++;
        }

        switch ($answercount) {
            case 0: $errors['answer[0]'] = get_string('notenoughanswers', $plugin, 2);
            case 1: $errors['answer[1]'] = get_string('notenoughanswers', $plugin, 2);
        }

        return $errors;
    }

    protected function add_ordering_feedback_fields($shownumpartscorrect = false) {
        if (method_exists($this, 'add_combined_feedback_fields')) {
            // Moodle >= 2.1
            $this->add_combined_feedback_fields($shownumpartscorrect);
        } else {
            // Moodle 2.0
            $mform = $this->_form;
            $names = array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback');
            foreach ($names as $name) {
                $label = get_string($name, 'qtype_multichoice'); // borrow string from standard core
                $mform->addElement('editor', $name, $label, array('rows' => 10), $this->editoroptions);
                $mform->setType($name, PARAM_RAW);
            }
        }
    }

    protected function add_ordering_interactive_settings($clearwrong=false, $shownumpartscorrect=false) {
        if (method_exists($this, 'add_interactive_settings')) {
            // Moodle >= 2.1
            $this->add_interactive_settings($clearwrong, $shownumpartscorrect);
        }
    }

    protected function data_preprocessing_ordering_feedback($question, $shownumcorrect=false) {
        if (method_exists($this, 'data_preprocessing_combined_feedback')) {
            // Moodle >= 2.1
            $question = $this->data_preprocessing_combined_feedback($question, $shownumcorrect);
        } else {
            // Moodle 2.0
            $names = array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback');
            foreach ($names as $name) {
                $draftid = file_get_submitted_draft_itemid($name);

                if (isset($question->id)) {
                    $itemid = $question->id;
                } else {
                    $itemid = null;
                }

                if (isset($question->options->$name)) {
                    $text = $question->options->$name;
                } else {
                    $text = '';
                }

                $text = file_prepare_draft_area($draftid, $this->context->id, 'qtype_ordering',
                                                $name, $itemid, $this->editoroptions, $text);

                $format = $name.'format';
                if (isset($question->options->$format)) {
                    $format = $question->options->$format;
                } else {
                    $format = FORMAT_MOODLE;
                }

                $question->$name = array('text'   => $text,
                                         'format' => $format,
                                         'itemid' => $draftid);
            }
        }
        return $question;
    }

    /**
     * this javascript could be useful for inserting buttons
     * into the form once it has loaded in the browser
     * however this means that the buttons are not recognized
     * by the Moodle Form API
     */
    protected function unused_js() {
        $removeeditor = 'Remove HTML editor';
        $js = '';
        $js .= '<script type="text/javascript">'."\n";
        $js .= "//<![CDATA[\n";
        $js .= "    var formatname = new RegExp('answer\\\\[(\\\\d+)\\\\]\\\\[format\\\\]');\n";
        $js .= "    var inputs = document.getElementsByTagName('INPUT');\n";
        $js .= "    for (var i=0; i<inputs.length; i++) {\n";
        $js .= "        var input = inputs[i];\n";
        $js .= "        if (input.type && input.type=='hidden') {\n";
        $js .= "            var m = formatname.exec(input.name);\n";
        $js .= "            if (m && m.length) {\n";
        $js .= "                var submit = document.createElement('INPUT');\n";
        $js .= "                submit.type = 'submit';\n";
        $js .= "                submit.value = '$removeeditor';\n";
        $js .= "                submit.format = input;\n";
        $js .= "                submit.onclick = function() {\n";
        $js .= "                    skipClientValidation = true;\n";
        $js .= "                    this.format.value = 0;\n";
        $js .= "                };\n";
        $js .= "                input.parentNode.insertBefore(submit, input.nextSibling);\n";
        $js .= "            }\n";
        $js .= "        }\n";
        $js .= "    }\n";
        $js .= "//]]>\n";
        $js .= "</script>\n";
        $mform->addElement('html', $js);
    }
}
