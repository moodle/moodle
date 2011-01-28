<?php


/**
 * Elements embedded in question text editing form definition.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_gapselect_edit_form_base extends question_edit_form {

    /** @var array of HTML tags allowed in choices / drag boxes. */
    protected $allowedhtmltags = array(
        'sub',
        'sup',
        'b',
        'i',
        'em',
        'strong'
    );

    /** @var string regex to match HTML open tags. */
    private $htmltstarttagsandattributes = '/<\s*\w.*?>/';

    /** @var string regex to match HTML close tags or br. */
    private $htmltclosetags = '~<\s*/\s*\w\s*.*?>|<\s*br\s*>~';

    /** @var string regex to select text like [[cat]] (including the square brackets). */
    private $squareBracketsRegex = '/\[\[[^]]*?\]\]/';  

    private function get_html_tags($text) {
        $textarray = array();
        foreach ($this->allowedhtmltags as $htmltag) {
            $tagpair = "/<\s*\/?\s*$htmltag\s*.*?>/";
            preg_match_all($tagpair, $text, $textarray);
            if ($textarray[0]) {
                return $textarray[0];
            }
        }
        preg_match_all($this->htmltstarttagsandattributes, $text, $textarray);
        if ($textarray[0]) {
            $tag = htmlspecialchars($textarray[0][0]);
            $allowedtaglist = $this->get_list_of_printable_allowed_tags($this->allowedhtmltags);
            return $tag . " is not allowed (only $allowedtaglist and corresponsing closing tags are allowed)";
        }
        preg_match_all($this->htmltclosetags, $text, $textarray);
        if ($textarray[0]) {
            $tag = htmlspecialchars($textarray[0][0]);
            $allowedtaglist=$this->get_list_of_printable_allowed_tags($this->allowedhtmltags);
            return $tag . " is not allowed HTML tag! (only $allowedtaglist and corresponsing closing tags are allowed)";
        }
        return false;
    }

    private function get_list_of_printable_allowed_tags($allowedhtmltags) {
        $allowedtaglist = null;
        foreach ($allowedhtmltags as $htmltag) {
            $allowedtaglist .= htmlspecialchars('<'.$htmltag.'>') . ', ';
        }
        return $allowedtaglist;
    }

    /**
     * definition_inner adds all specific fields to the form.
     * @param object $mform (the form being built).
     */
    function definition_inner(&$mform) {
        global $CFG;

        //add the answer (choice) fields to the form
        $this->definition_answer_choice($mform);

        $this->add_combined_feedback_fields(true);
        $this->add_interactive_settings(true, true);
    }

    protected function definition_answer_choice(&$mform) {
        $mform->addElement('header', 'choicehdr',   get_string('choices', 'qtype_gapselect'));

        $mform->addElement('checkbox', 'shuffleanswers',  get_string('shuffle', 'quiz'));
        $mform->setDefault('shuffleanswers', 0);

        $textboxgroup = array();

        $grouparray = array();
        $grouparray[] =& $mform->createElement('text', 'answer', get_string('answer', 'qtype_gapselect'), array('size'=>30, 'class'=>'tweakcss'));
        $grouparray[] =& $mform->createElement('static', '', '',' '.get_string('group', 'qtype_gapselect').' ');

        $grouparray = $this->choice_group($mform, $grouparray);
        $textboxgroup[] = $mform->createElement('group','choices', 'Choice {no}',$grouparray);

        if (isset($this->question->options)) {
            $countanswers = count($this->question->options->answers);
        } else {
            $countanswers = 0;
        }

        if ($this->question->formoptions->repeatelements) {
            $defaultstartnumbers = QUESTION_NUMANS_START*2;
            $repeatsatstart = max($defaultstartnumbers, QUESTION_NUMANS_START, $countanswers + QUESTION_NUMANS_ADD);
        } else {
            $repeatsatstart = $countanswers;
        }

        $repeatedoptions = $this->repeated_options();
        $mform->setType('answer', PARAM_RAW);
        $this->repeat_elements($textboxgroup, $repeatsatstart, $repeatedoptions, 'noanswers', 'addanswers', QUESTION_NUMANS_ADD, get_string('addmorechoiceblanks', 'qtype_gapselect'));
    }



    public function set_data($question) {
        if (isset($question->options)) {
            $options = $question->options;
            $default_values = array();
            if (count($options->answers)) {
                $key = 0;
                foreach ($options->answers as $answer) {
                    $default_values['choices['.$key.'][answer]'] = $answer->answer;
                    $default_values += $this->default_values_from_feedback_field($answer->feedback, $key);
                    $key++;
                }
            }

            $default_values['shuffleanswers'] =  $question->options->shuffleanswers;
            $default_values['correctfeedback'] =  $question->options->correctfeedback;
            $default_values['partiallycorrectfeedback'] =  $question->options->partiallycorrectfeedback;
            $default_values['incorrectfeedback'] =  $question->options->incorrectfeedback;
            $default_values['shownumcorrect'] = $question->options->shownumcorrect;
            $question = (object)((array)$question + $default_values);
        }
        parent::set_data($question);
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $questiontext = $data['questiontext'];
        $choices = $data['choices'];

        //check the whether the slots are valid
        $errorsinquestiontext = $this->validate_slots($questiontext, $choices);
        if ($errorsinquestiontext) {
            $errors['questiontext'] = $errorsinquestiontext;
        }
        foreach ($choices as $key => $choice) {
            $answer = $choice['answer'];

            //check whether the html-tags are allowed tags
            $validtags = $this->get_html_tags($answer);
            if (is_array($validtags)) {
                continue;
            }
            if ($validtags) {
                $errors['choices['.$key.']'] = $validtags;
            }
        }
        return $errors;
    }

    private function validate_slots($questiontext, $choices) {
        $error = 'Please check the Question text: ';
        if (!$questiontext) {
            return $error . 'The question text is empty!';
        }

        $matches = array();
        preg_match_all($this->squareBracketsRegex, $questiontext, $matches);
        $slots = $matches[0];

        if (!$slots) {
            return $error . 'The question text is not in the correct format!';
        }

        $output = array();
        foreach ($slots as $slot) {
            // The 2 is for'[[' and 4 is for '[[]]'.
            $output[] = substr($slot, 2, (strlen($slot)-4));
        }

        $slots = $output;
        $found = false;
        foreach ($slots as $slot) {
            $found = false;
            foreach ($choices as $key => $choice) {
                if ($slot == $key + 1) {
                    if (!$choice['answer']) {
                        return " Please check Choices: The choice <b>$slot</b> empty.";
                    }
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return $error . "<b>$slot</b> was not found in Choices! (only the choice numbers that exist in choices are allowed to be used a place holders!";
            }
        }
        return false;
    }
    function qtype() {
        return '';
    }

    protected function default_values_from_feedback_field($feedback, $key) {
        $default_values = array();
        return $default_values;
    }

    protected function repeated_options() {
        $repeatedoptions = array();
        return $repeatedoptions;
    }
}