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
 * This file contains the parent class for rate question types.
 *
 * @author Mike Churchward
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questiontypes
 */

namespace mod_questionnaire\question;
defined('MOODLE_INTERNAL') || die();
use \html_writer;

class rate extends base {

    /**
     * Constructor. Use to set any default properties.
     *
     */
    public function __construct($id = 0, $question = null, $context = null, $params = array()) {
        $this->length = 5;
        return parent::__construct($id, $question, $context, $params);
    }

    protected function responseclass() {
        return '\\mod_questionnaire\\response\\rank';
    }

    public function helpname() {
        return 'ratescale';
    }

    /**
     * Return true if the question has choices.
     */
    public function has_choices() {
        return true;
    }

    /**
     * Override and return a form template if provided. Output of question_survey_display is iterpreted based on this.
     * @return boolean | string
     */
    public function question_template() {
        return 'mod_questionnaire/question_rate';
    }

    /**
     * Override and return a response template if provided. Output of response_survey_display is iterpreted based on this.
     * @return boolean | string
     */
    public function response_template() {
        return 'mod_questionnaire/response_rate';
    }

    /**
     * True if question type supports feedback options. False by default.
     */
    public function supports_feedback() {
        return true;
    }

    /**
     * True if the question supports feedback and has valid settings for feedback. Override if the default logic is not enough.
     */
    public function valid_feedback() {
        return parent::valid_feedback() && (($this->precise == 0) || ($this->precise == 3));
    }

    /**
     * Get the maximum score possible for feedback if appropriate. Override if default behaviour is not correct.
     * @return int | boolean
     */
    public function get_feedback_maxscore() {
        if ($this->valid_feedback()) {
            $maxscore = 0;
            $nbchoices = 0;
            foreach ($this->choices as $choice) {
                if (isset($choice->value) && ($choice->value != null)) {
                    if ($choice->value > $maxscore) {
                        $maxscore = $choice->value;
                    }
                } else {
                    $nbchoices++;
                }
            }
            // The maximum score needs to be multiplied by the number of items to rate.
            $maxscore = $maxscore * $nbchoices;
        } else {
            $maxscore = false;
        }
        return $maxscore;
    }

    /**
     * Return the context tags for the check question template.
     * @param object $data
     * @param string $descendantdata
     * @param boolean $blankquestionnaire
     * @return object The check question context tags.
     *
     */
    protected function question_survey_display($data, $descendantsdata, $blankquestionnaire=false) {
        $choicetags = new \stdClass();
        $choicetags->qelements = [];

        $disabled = '';
        if ($blankquestionnaire) {
            $disabled = ' disabled="disabled"';
        }
        if (!empty($data) && ( !isset($data->{'q'.$this->id}) || !is_array($data->{'q'.$this->id}) ) ) {
            $data->{'q'.$this->id} = array();
        }

        $isna = $this->precise == 1;
        $osgood = $this->precise == 3;

        // Check if rate question has one line only to display full width columns of choices.
        $nocontent = false;
        $nameddegrees = 0;
        $n = array();
        $v = array();
        $mods = array();
        $maxndlen = 0;
        foreach ($this->choices as $cid => $choice) {
            $content = $choice->content;
            if (!$nocontent && $content == '') {
                $nocontent = true;
            }
            // Check for number from 1 to 3 digits, followed by the equal sign = (to accomodate named degrees).
            if (preg_match("/^([0-9]{1,3})=(.*)$/", $content, $ndd)) {
                $n[$nameddegrees] = format_text($ndd[2], FORMAT_HTML, ['noclean' => true]);
                if (strlen($n[$nameddegrees]) > $maxndlen) {
                    $maxndlen = strlen($n[$nameddegrees]);
                }
                $v[$nameddegrees] = $ndd[1];
                $this->choices[$cid] = '';
                $nameddegrees++;
            } else {
                // Something wrong here. $choice->content is being set, but it will never be used. This code exists as far back as
                // 2.0.
                $contents = questionnaire_choice_values($content);
                if ($contents->modname) {
                    $choice->content = $contents->text;
                }
            }
        }

        // The 0.1% right margin is needed to avoid the horizontal scrollbar in Chrome!
        // A one-line rate question (no content) does not need to span more than 50%.
        $width = $nocontent ? "50%" : "99.9%";
        $choicetags->qelements['twidth'] = $width;
        $choicetags->qelements['headerrow'] = [];
        // If Osgood, adjust central columns to width of named degrees if any.
        if ($osgood) {
            if ($maxndlen < 4) {
                $width = '45%';
            } else if ($maxndlen < 13) {
                $width = '40%';
            } else {
                $width = '30%';
            }
            $nn = 100 - ($width * 2);
            $colwidth = ($nn / $this->length).'%';
            $textalign = 'right';
        } else if ($nocontent) {
            $width = '0%';
            $colwidth = (100 / $this->length).'%';
            $textalign = 'right';
        } else {
            $width = '59%';
            $colwidth = (40 / $this->length).'%';
            $textalign = 'left';
        }

        $choicetags->qelements['headerrow']['col1width'] = $width;

        if ($isna) {
            $na = get_string('notapplicable', 'questionnaire');
        } else {
            $na = '';
        }
        if ($this->precise == 2) {
            $order = 'other_rate_uncheck(name, value)';
        } else {
            $order = '';
        }

        if ($this->precise != 2) {
            $nbchoices = count($this->choices) - $nameddegrees;
        } else { // If "No duplicate choices", can restrict nbchoices to number of rate items specified.
            $nbchoices = $this->length;
        }

        // Display empty td for Not yet answered column.
        if ($nbchoices > 1 && $this->precise != 2 && !$blankquestionnaire) {
            $choicetags->qelements['headerrow']['colnya'] = true;
        }

        $collabel = [];
        for ($j = 0; $j < $this->length; $j++) {
            $col = [];
            if (isset($n[$j])) {
                $str = $n[$j];
                $val = $v[$j];
            } else {
                $str = $j + 1;
                $val = $j + 1;
            }
            if ($blankquestionnaire) {
                $val = '<br />('.$val.')';
            } else {
                $val = '';
            }
            $col['colwidth'] = $colwidth;
            $col['coltext'] = $str.$val;
            $collabel[$j] = $col['coltext'];
            $choicetags->qelements['headerrow']['cols'][] = $col;
        }
        if ($na) {
            $choicetags->qelements['headerrow']['cols'][] = ['colwidth' => $colwidth, 'coltext' => $na];
            $collabel[$j] = $na;
        }

        $num = 0;
        foreach ($this->choices as $cid => $choice) {
            $str = 'q'."{$this->id}_$cid";
            $num += (isset($data->$str) && ($data->$str != -999));
        }

        $notcomplete = false;
        if ( ($num != $nbchoices) && ($num != 0) ) {
            $this->add_notification(get_string('checkallradiobuttons', 'questionnaire', $nbchoices));
            $notcomplete = true;
        }

        $row = 0;
        $choicetags->qelements['rows'] = [];
        foreach ($this->choices as $cid => $choice) {
            $cols = [];
            if (isset($choice->content)) {
                $row++;
                $str = 'q'."{$this->id}_$cid";
                $content = $choice->content;
                if ($osgood) {
                    list($content, $contentright) = array_merge(preg_split('/[|]/', $content), array(' '));
                }
                $cols[] = ['colstyle' => 'text-align: '.$textalign.';',
                           'coltext' => format_text($content, FORMAT_HTML, ['noclean' => true]).'&nbsp;'];

                $bg = 'c0 raterow';
                if ($nbchoices > 1 && $this->precise != 2  && !$blankquestionnaire) {
                    $checked = ' checked="checked"';
                    $completeclass = 'notanswered';
                    $title = '';
                    if ($notcomplete && isset($data->$str) && ($data->$str == -999)) {
                        $completeclass = 'notcompleted';
                        $title = get_string('pleasecomplete', 'questionnaire');
                    }
                    // Set value of notanswered button to -999 in order to eliminate it from form submit later on.
                    $colinput = ['name' => $str, 'value' => -999];
                    if (!empty($checked)) {
                        $colinput['checked'] = true;
                    }
                    if (!empty($order)) {
                        $colinput['onclick'] = $order;
                    }
                    $cols[] = ['colstyle' => 'width:1%;', 'colclass' => $completeclass, 'coltitle' => $title,
                        'colinput' => $colinput];
                }
                for ($j = 0; $j < $this->length + $isna; $j++) {
                    $col = [];
                    $checked = ((isset($data->$str) && ($j == $data->$str ||
                                 $j == $this->length && $data->$str == -1)) ? ' checked="checked"' : '');
                    $checked = '';
                    if (isset($data->$str) && ($j == $data->$str || $j == $this->length && $data->$str == -1)) {
                        $checked = ' checked="checked"';
                    }
                    $col['colstyle'] = 'text-align:center';
                    $col['colclass'] = $bg;
                    $i = $j + 1;
                    $col['colhiddentext'] = get_string('option', 'questionnaire', $i);
                    // If isna column then set na choice to -1 value.
                    $value = ($j < $this->length ? $j : - 1);
                    $col['colinput']['name'] = $str;
                    $col['colinput']['value'] = $value;
                    $col['colinput']['id'] = $str.'_'.$value;
                    if (!empty($checked)) {
                        $col['colinput']['checked'] = true;
                    }
                    if (!empty($disabled)) {
                        $col['colinput']['disabled'] = true;
                    }
                    if (!empty($order)) {
                        $col['colinput']['onclick'] = $order;
                    }
                    $col['colinput']['label'] = 'Choice '.$collabel[$j].' for row '.format_text($content, FORMAT_PLAIN);
                    if ($bg == 'c0 raterow') {
                        $bg = 'c1 raterow';
                    } else {
                        $bg = 'c0 raterow';
                    }
                    $cols[] = $col;
                }
                if ($osgood) {
                    $cols[] = ['coltext' => '&nbsp;'.format_text($contentright, FORMAT_HTML, ['noclean' => true])];
                }
                $choicetags->qelements['rows'][] = ['cols' => $cols];
            }
        }

        return $choicetags;
    }

    /**
     * Return the context tags for the rate response template.
     * @param object $data
     * @return object The rate question response context tags.
     *
     */
    protected function response_survey_display($data) {
        static $uniquetag = 0;  // To make sure all radios have unique names.

        $resptags = new \stdClass();
        $resptags->headers = [];
        $resptags->rows = [];

        if (!isset($data->{'q'.$this->id}) || !is_array($data->{'q'.$this->id})) {
            $data->{'q'.$this->id} = array();
        }
        // Check if rate question has one line only to display full width columns of choices.
        $nocontent = false;
        foreach ($this->choices as $cid => $choice) {
            $content = $choice->content;
            if ($choice->content == '') {
                $nocontent = true;
                break;
            }
        }
        $resptags->twidth = $nocontent ? "50%" : "99.9%";

        $osgood = $this->precise == 3;
        $bg = 'c0';
        $nameddegrees = 0;
        $cidnamed = array();
        $n = array();
        // Max length of potential named degree in column head.
        $maxndlen = 0;
        foreach ($this->choices as $cid => $choice) {
            $content = $choice->content;
            if (preg_match("/^[0-9]{1,3}=/", $content, $ndd)) {
                $ndd = format_text(substr($content, strlen($ndd[0])), FORMAT_HTML, ['noclean' => true]);
                $n[$nameddegrees] = $ndd;
                if (strlen($ndd) > $maxndlen) {
                    $maxndlen = strlen($ndd);
                }
                $cidnamed[$cid] = true;
                $nameddegrees++;
            }
        }
        if ($osgood) {
            $resptags->osgood = 1;
            if ($maxndlen < 4) {
                $sidecolwidth = '45%';
            } else if ($maxndlen < 13) {
                $sidecolwidth = '40%';
            } else {
                $sidecolwidth = '30%';
            }
            $nn = 100 - ($sidecolwidth * 2);
            $resptags->sidecolwidth = $sidecolwidth;
            $resptags->colwidth = ($nn / $this->length).'%';
            $resptags->textalign = 'right';
        } else {
            $resptags->sidecolwidth = '49%';
            $resptags->colwidth = (50 / $this->length).'%';
            $resptags->textalign = 'left';
        }
        for ($j = 0; $j < $this->length; $j++) {
            $cellobj = new \stdClass();
            $cellobj->bg = $bg;
            if (isset($n[$j])) {
                $cellobj->str = $n[$j];
            } else {
                $cellobj->str = $j + 1;
            }
            if ($bg == 'c0') {
                $bg = 'c1';
            } else {
                $bg = 'c0';
            }
            $resptags->headers[] = $cellobj;
        }
        if ($this->precise == 1) {
            $cellobj = new \stdClass();
            $cellobj->bg = $bg;
            $cellobj->str = get_string('notapplicable', 'questionnaire');
            $resptags->headers[] = $cellobj;
        }

        foreach ($this->choices as $cid => $choice) {
            $rowobj = new \stdClass();
            // Do not print column names if named column exist.
            if (!array_key_exists($cid, $cidnamed)) {
                $str = 'q'."{$this->id}_$cid";
                $content = $choice->content;
                $contents = questionnaire_choice_values($content);
                if ($contents->modname) {
                    $content = $contents->text;
                }
                if ($osgood) {
                    list($content, $contentright) = array_merge(preg_split('/[|]/', $content), array(' '));
                }
                $rowobj->content = format_text($content, FORMAT_HTML, ['noclean' => true]).'&nbsp;';
                $bg = 'c0';
                $cols = [];
                for ($j = 0; $j < $this->length; $j++) {
                    $cellobj = new \stdClass();
                    if (isset($data->$str) && ($j == $data->$str)) {
                        $cellobj->checked = 1;
                    }
                    $cellobj->str = $str.$j.$uniquetag++;
                    $cellobj->bg = $bg;
                    // N/A column checked.
                    $checkedna = (isset($data->$str) && ($data->$str == -1));
                    if ($bg == 'c0') {
                        $bg = 'c1';
                    } else {
                        $bg = 'c0';
                    }
                    $cols[] = $cellobj;
                }
                if ($this->precise == 1) { // N/A column.
                    $cellobj = new \stdClass();
                    if ($checkedna) {
                        $cellobj->checked = 1;
                    }
                    $cellobj->str = $str.$j.$uniquetag++.'na';
                    $cellobj->bg = $bg;
                    $cols[] = $cellobj;
                }
                $rowobj->cols = $cols;
                if ($osgood) {
                    $rowobj->osgoodstr = '&nbsp;'.format_text($contentright, FORMAT_HTML, ['noclean' => true]);
                }
                $resptags->rows[] = $rowobj;
            }
        }
        return $resptags;
    }

    /**
     * Check question's form data for complete response.
     *
     * @param object $responsedata The data entered into the response.
     * @return boolean
     *
     */
    public function response_complete($responsedata) {
        $num = 0;
        $nbchoices = count($this->choices);
        $na = get_string('notapplicable', 'questionnaire');
        $complete = true;
        foreach ($this->choices as $cid => $choice) {
            // In case we have named degrees on the Likert scale, count them to substract from nbchoices.
            $nameddegrees = 0;
            $content = $choice->content;
            if (preg_match("/^[0-9]{1,3}=/", $content)) {
                $nameddegrees++;
            } else {
                $str = 'q'."{$this->id}_$cid";
                if (isset($responsedata->$str) && $responsedata->$str == $na) {
                    $responsedata->$str = -1;
                }
                // If choice value == -999 this is a not yet answered choice.
                $num += (isset($responsedata->$str) && ($responsedata->$str != -999));
            }
            $nbchoices -= $nameddegrees;
        }

        if ($num == 0) {
            if (!$this->has_dependencies()) {
                if ($this->required()) {
                    $complete = false;
                }
            }
        }
        return $complete;
    }

    /**
     * Check question's form data for valid response. Override this is type has specific format requirements.
     *
     * @param object $responsedata The data entered into the response.
     * @return boolean
     */
    public function response_valid($responsedata) {
        $num = 0;
        $nbchoices = count($this->choices);
        $na = get_string('notapplicable', 'questionnaire');
        foreach ($this->choices as $cid => $choice) {
            // In case we have named degrees on the Likert scale, count them to substract from nbchoices.
            $nameddegrees = 0;
            $content = $choice->content;
            if (preg_match("/^[0-9]{1,3}=/", $content)) {
                $nameddegrees++;
            } else {
                $str = 'q'."{$this->id}_$cid";
                if (isset($responsedata->$str) && ($responsedata->$str == $na)) {
                    $responsedata->$str = -1;
                }
                // If choice value == -999 this is a not yet answered choice.
                $num += (isset($responsedata->$str) && ($responsedata->$str != -999));
            }
            $nbchoices -= $nameddegrees;
        }
        // If nodupes and nb choice restricted, nbchoices may be > actual choices, so limit it to $question->length.
        $isrestricted = ($this->length < count($this->choices)) && ($this->precise == 2);
        if ($isrestricted) {
            $nbchoices = min ($nbchoices, $this->length);
        }
        if (($num != $nbchoices) && ($num != 0)) {
            return false;
        } else {
            return parent::response_valid($responsedata);
        }
    }

    protected function form_length(\MoodleQuickForm $mform, $helptext = '') {
        return parent::form_length($mform, 'numberscaleitems');
    }

    protected function form_precise(\MoodleQuickForm $mform, $helptext = '') {
        $precoptions = array("0" => get_string('normal', 'questionnaire'),
                             "1" => get_string('notapplicablecolumn', 'questionnaire'),
                             "2" => get_string('noduplicates', 'questionnaire'),
                             "3" => get_string('osgood', 'questionnaire'));
        $mform->addElement('select', 'precise', get_string('kindofratescale', 'questionnaire'), $precoptions);
        $mform->addHelpButton('precise', 'kindofratescale', 'questionnaire');
        $mform->setType('precise', PARAM_INT);

        return $mform;
    }

    /**
     * Preprocess choice data.
     */
    protected function form_preprocess_choicedata($formdata) {
        if (empty($formdata->allchoices)) {
            // Add dummy blank space character for empty value.
            $formdata->allchoices = " ";
        } else {
            $allchoices = $formdata->allchoices;
            $allchoices = explode("\n", $allchoices);
            $ispossibleanswer = false;
            $nbnameddegrees = 0;
            $nbvalues = 0;
            foreach ($allchoices as $choice) {
                if ($choice) {
                    // Check for number from 1 to 3 digits, followed by the equal sign =.
                    if (preg_match("/^[0-9]{1,3}=/", $choice)) {
                        $nbnameddegrees++;
                    } else {
                        $nbvalues++;
                        $ispossibleanswer = true;
                    }
                }
            }
            // Add carriage return and dummy blank space character for empty value.
            if (!$ispossibleanswer) {
                $formdata->allchoices .= "\n ";
            }

            // Sanity checks for correct number of values in $formdata->length.

            // Sanity check for named degrees.
            if ($nbnameddegrees && $nbnameddegrees != $formdata->length) {
                $formdata->length = $nbnameddegrees;
            }
            // Sanity check for "no duplicate choices"".
            if ($formdata->precise == 2 && ($formdata->length > $nbvalues || !$formdata->length)) {
                $formdata->length = $nbvalues;
            }
        }
        return true;
    }
}