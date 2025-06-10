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

namespace mod_questionnaire\question;

/**
 * This file contains the parent class for rate question types.
 *
 * @author Mike Churchward
 * @copyright 2016 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 */
class rate extends question {

    /** @var array $nameddegrees */
    public $nameddegrees = [];

    /** @var int Row start position of the rate table. */
    public const ROW_START = 2;

    /** @var int Column start position of the rate table. */
    public const COL_START = 2;

    /**
     * The class constructor
     * @param int $id
     * @param \stdClass $question
     * @param \context $context
     * @param array $params
     */
    public function __construct($id = 0, $question = null, $context = null, $params = array()) {
        $this->length = 5;
        parent::__construct($id, $question, $context, $params);
        $this->add_nameddegrees_from_extradata();
    }

    /**
     * Each question type must define its response class.
     * @return object The response object based off of questionnaire_response_base.
     */
    protected function responseclass() {
        return '\\mod_questionnaire\\responsetype\\rank';
    }

    /**
     * Short name for this question type - no spaces, etc..
     * @return string
     */
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
     * Return true if rate scale type is set to "Normal".
     * @param int $scaletype
     * @return bool
     */
    public static function type_is_normal_rate_scale($scaletype) {
        return ($scaletype == 0);
    }

    /**
     * Return true if rate scale type is set to "N/A column".
     * @param int $scaletype
     * @return bool
     */
    public static function type_is_na_column($scaletype) {
        return ($scaletype == 1);
    }

    /**
     * Return true if rate scale type is set to "No duplicate choices".
     * @param int $scaletype
     * @return bool
     */
    public static function type_is_no_duplicate_choices($scaletype) {
        return ($scaletype == 2);
    }

    /**
     * Return true if rate scale type is set to "Osgood".
     * @param int $scaletype
     * @return bool
     */
    public static function type_is_osgood_rate_scale($scaletype) {
        return ($scaletype == 3);
    }

    /**
     * Return true if rate scale type is set to "Normal".
     * @return bool
     */
    public function normal_rate_scale() {
        return self::type_is_normal_rate_scale($this->precise);
    }

    /**
     * Return true if rate scale type is set to "N/A column".
     * @return bool
     */
    public function has_na_column() {
        return self::type_is_na_column($this->precise);
    }

    /**
     * Return true if rate scale type is set to "No duplicate choices".
     * @return bool
     */
    public function no_duplicate_choices() {
        return self::type_is_no_duplicate_choices($this->precise);
    }

    /**
     * Return true if rate scale type is set to "Osgood".
     * @return bool
     */
    public function osgood_rate_scale() {
        return self::type_is_osgood_rate_scale($this->precise);
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
        return $this->supports_feedback() && $this->has_choices() && $this->required() && !empty($this->name) &&
            ($this->normal_rate_scale() || $this->osgood_rate_scale()) && !empty($this->nameddegrees);
    }

    /**
     * Get the maximum score possible for feedback if appropriate. Override if default behaviour is not correct.
     * @return int | boolean
     */
    public function get_feedback_maxscore() {
        if ($this->valid_feedback()) {
            $maxscore = 0;
            $nbchoices = count($this->choices);
            foreach ($this->nameddegrees as $value => $label) {
                if ($value > $maxscore) {
                    $maxscore = $value;
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
     * @param \mod_questionnaire\responsetype\response\response $response
     * @param string $descendantsdata
     * @param boolean $blankquestionnaire
     * @return object The check question context tags.
     *
     * TODO: This function needs to be rewritten. It is a mess!
     *
     */
    protected function question_survey_display($response, $descendantsdata, $blankquestionnaire=false) {
        $choicetags = new \stdClass();
        $choicetags->qelements = [];
        $choicetags->qelements['caption'] = strip_tags($this->content);

        $disabled = '';
        if ($blankquestionnaire) {
            $disabled = ' disabled="disabled"';
        }
        if (!empty($data) && ( !isset($data->{'q'.$this->id}) || !is_array($data->{'q'.$this->id}) ) ) {
            $data->{'q'.$this->id} = [];
        }

        // Check if rate question has one line only to display full width columns of choices.
        $nocontent = false;
        $nameddegrees = count($this->nameddegrees);
        $n = [];
        $v = [];
        $maxndlen = 0;
        foreach ($this->choices as $cid => $choice) {
            $content = $choice->content;
            if (!$nocontent && $content == '') {
                $nocontent = true;
            }
            if ($nameddegrees == 0) {
                // Determine if the choices have named values.
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
        if ($this->osgood_rate_scale()) {
            if ($maxndlen < 4) {
                $width = 45;
            } else if ($maxndlen < 13) {
                $width = 40;
            } else {
                $width = 30;
            }
            $nn = 100 - ($width * 2);
            $colwidth = ($nn / $this->length).'%';
            $textalign = 'right';
            $width = $width . '%';
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

        if ($this->has_na_column()) {
            $na = get_string('notapplicable', 'questionnaire');
        } else {
            $na = '';
        }
        if ($this->no_duplicate_choices()) {
            $order = 'other_rate_uncheck(name, value)';
        } else {
            $order = '';
        }

        if (!$this->no_duplicate_choices()) {
            $nbchoices = count($this->choices);
        } else { // If "No duplicate choices", can restrict nbchoices to number of rate items specified.
            $nbchoices = $this->length;
        }

        // Display empty td for Not yet answered column.
        if (($nbchoices > 1) && !$this->no_duplicate_choices() && !$blankquestionnaire) {
            $choicetags->qelements['headerrow']['colnya'] = true;
        }

        $collabel = [];
        if ($nameddegrees > 0) {
            $currentdegree = reset($this->nameddegrees);
        }
        for ($j = 1; $j <= $this->length; $j++) {
            $col = [];
            if (($nameddegrees > 0) && ($currentdegree !== false)) {
                $str = format_text($currentdegree, FORMAT_HTML, ['noclean' => true]);
                $currentdegree = next($this->nameddegrees);
            } else {
                $str = $j;
            }
            $val = $j;
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
            $num += (isset($response->answers[$this->id][$cid]) && ($response->answers[$this->id][$cid]->value != -999));
        }

        $notcomplete = false;
        if ( ($num != $nbchoices) && ($num != 0) ) {
            $this->add_notification(get_string('checkallradiobuttons', 'questionnaire', $nbchoices));
            $notcomplete = true;
        }

        $rowstart = self::ROW_START;
        $choicetags->qelements['rows'] = [];
        foreach ($this->choices as $cid => $choice) {
            $cols = [];
            if (isset($choice->content)) {
                $str = 'q'."{$this->id}_$cid";
                $content = $choice->content;
                $rendercontent = format_text($choice->content, FORMAT_PLAIN);
                if ($this->osgood_rate_scale()) {
                    list($content, $contentright) = array_merge(preg_split('/[|]/', $content), array(' '));
                }
                $cols[] = ['colstyle' => 'text-align: '.$textalign.';',
                           'coltext' => format_text($content, FORMAT_HTML, ['noclean' => true]).'&nbsp;'];

                $bg = 'c0 raterow';
                $hasnotansweredchoice = false;
                if (($nbchoices > 1) && !$this->no_duplicate_choices()  && !$blankquestionnaire) {
                    $hasnotansweredchoice = true;
                    $checked = ' checked="checked"';
                    $completeclass = 'notanswered';
                    $title = '';
                    if ($notcomplete && isset($response->answers[$this->id][$cid]) &&
                        ($response->answers[$this->id][$cid]->value == -999)) {
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
                    $colinput['label'] = $this->set_label($rowstart, $rendercontent, self::COL_START,
                        get_string('unanswered', 'questionnaire'));
                    $cols[] = ['colstyle' => 'width:1%;', 'colclass' => $completeclass, 'coltitle' => $title,
                        'colinput' => $colinput];
                }
                if ($nameddegrees > 0) {
                    reset($this->nameddegrees);
                }
                $colstart = $hasnotansweredchoice ? self::COL_START + 1 : self::COL_START;
                for ($j = 1; $j <= $this->length + $this->has_na_column(); $j++) {
                    if (!isset($collabel[$j])) {
                        // If not using this value, continue.
                        continue;
                    }
                    $col = [];
                    $checked = '';
                    // If isna column then set na choice to -1 value. This needs work!
                    if (!empty($this->nameddegrees) && (key($this->nameddegrees) !== null)) {
                        $value = key($this->nameddegrees);
                        next($this->nameddegrees);
                    } else {
                        $value = ($j <= $this->length ? $j : -1);
                    }
                    if (isset($response->answers[$this->id][$cid]) && ($value == $response->answers[$this->id][$cid]->value)) {
                        $checked = ' checked="checked"';
                    }
                    $col['colstyle'] = 'text-align:center';
                    $col['colclass'] = $bg;
                    $col['colhiddentext'] = get_string('option', 'questionnaire', $j);
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
                    $col['colinput']['label'] = $this->set_label($rowstart, $rendercontent, $colstart, $collabel[$j]);
                    if ($bg == 'c0 raterow') {
                        $bg = 'c1 raterow';
                    } else {
                        $bg = 'c0 raterow';
                    }
                    $colstart++;
                    $cols[] = $col;
                }
                if ($this->osgood_rate_scale()) {
                    $cols[] = ['coltext' => '&nbsp;'.format_text($contentright, FORMAT_HTML, ['noclean' => true])];
                }
                $choicetags->qelements['rows'][] = ['cols' => $cols];
                $rowstart++;
            }
        }

        return $choicetags;
    }

    /**
     * Return the context tags for the rate response template.
     * @param \mod_questionnaire\responsetype\response\response $response
     * @return \stdClass The rate question response context tags.
     * @throws \coding_exception
     */
    protected function response_survey_display($response) {
        static $uniquetag = 0;  // To make sure all radios have unique names.

        $resptags = new \stdClass();
        $resptags->headers = [];
        $resptags->rows = [];

        if (!isset($response->answers[$this->id])) {
            $response->answers[$this->id][] = new \mod_questionnaire\responsetype\answer\answer();
        }
        // Check if rate question has one line only to display full width columns of choices.
        $nocontent = false;
        foreach ($this->choices as $cid => $choice) {
            if ($choice->content == '') {
                $nocontent = true;
                break;
            }
        }
        $resptags->twidth = $nocontent ? "50%" : "99.9%";

        $bg = 'c0';
        $nameddegrees = 0;
        $cidnamed = array();
        // Max length of potential named degree in column head.
        $maxndlen = 0;
        if ($this->osgood_rate_scale()) {
            $resptags->osgood = 1;
            if ($maxndlen < 4) {
                $sidecolwidth = '45%';
                $sidecolwidthn = 45;
            } else if ($maxndlen < 13) {
                $sidecolwidth = '40%';
                $sidecolwidthn = 40;
            } else {
                $sidecolwidth = '30%';
                $sidecolwidthn = 30;
            }
            $nn = 100 - ($sidecolwidthn * 2);
            $resptags->sidecolwidth = $sidecolwidth;
            $resptags->colwidth = ($nn / $this->length).'%';
            $resptags->textalign = 'right';
        } else {
            $resptags->sidecolwidth = '49%';
            $resptags->colwidth = (50 / $this->length).'%';
            $resptags->textalign = 'left';
        }
        if (!empty($this->nameddegrees)) {
            $this->length = count($this->nameddegrees);
            reset($this->nameddegrees);
        }
        for ($j = 1; $j <= $this->length; $j++) {
            $cellobj = new \stdClass();
            $cellobj->bg = $bg;
            if (!empty($this->nameddegrees)) {
                $cellobj->str = current($this->nameddegrees);
                next($this->nameddegrees);
            } else {
                $cellobj->str = $j;
            }
            if ($bg == 'c0') {
                $bg = 'c1';
            } else {
                $bg = 'c0';
            }
            $resptags->headers[] = $cellobj;
        }
        if ($this->has_na_column()) {
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
                if ($this->osgood_rate_scale()) {
                    list($content, $contentright) = array_merge(preg_split('/[|]/', $content), array(' '));
                }
                $rowobj->content = format_text($content, FORMAT_HTML, ['noclean' => true]).'&nbsp;';
                $bg = 'c0';
                $cols = [];
                if (!empty($this->nameddegrees)) {
                    $this->length = count($this->nameddegrees);
                    reset($this->nameddegrees);
                }
                for ($j = 1; $j <= $this->length; $j++) {
                    $cellobj = new \stdClass();
                    if (isset($response->answers[$this->id][$cid])) {
                        if (!empty($this->nameddegrees)) {
                            if ($response->answers[$this->id][$cid]->value == key($this->nameddegrees)) {
                                $cellobj->checked = 1;
                            }
                            next($this->nameddegrees);
                        } else if ($j == $response->answers[$this->id][$cid]->value) {
                            $cellobj->checked = 1;
                        }
                    }
                    $cellobj->str = $str.$j.$uniquetag++;
                    $cellobj->bg = $bg;
                    // N/A column checked.
                    $checkedna = (isset($response->answers[$this->id][$cid]) && ($response->answers[$this->id][$cid]->value == -1));
                    if ($bg == 'c0') {
                        $bg = 'c1';
                    } else {
                        $bg = 'c0';
                    }
                    $cols[] = $cellobj;
                }
                if ($this->has_na_column()) { // N/A column.
                    $cellobj = new \stdClass();
                    if ($checkedna) {
                        $cellobj->checked = 1;
                    }
                    $cellobj->str = $str.$j.$uniquetag++.'na';
                    $cellobj->bg = $bg;
                    $cols[] = $cellobj;
                }
                $rowobj->cols = $cols;
                if ($this->osgood_rate_scale()) {
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
     * @param \stdClass $responsedata The data entered into the response.
     * @return boolean
     *
     */
    public function response_complete($responsedata) {
        if (!is_a($responsedata, 'mod_questionnaire\responsetype\response\response')) {
            $response = \mod_questionnaire\responsetype\response\response::response_from_webform($responsedata, [$this]);
        } else {
            $response = $responsedata;
        }

        // To make it easier, create an array of answers by choiceid.
        $answers = [];
        if (isset($response->answers[$this->id])) {
            foreach ($response->answers[$this->id] as $answer) {
                $answers[$answer->choiceid] = $answer;
            }
        }

        $answered = true;
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
                if (isset($answers[$cid]) && !empty($answers[$cid]) && ($answers[$cid]->value == $na)) {
                    $answers[$cid]->value = -1;
                }
                // If choice value == -999 this is a not yet answered choice.
                $num += (isset($answers[$cid]) && ($answers[$cid]->value != -999));
            }
            $nbchoices -= $nameddegrees;
        }

        if ($num == 0) {
            if ($this->required()) {
                $answered = false;
            }
        }
        return $answered;
    }

    /**
     * Check question's form data for valid response. Override this is type has specific format requirements.
     *
     * @param \stdClass $responsedata The data entered into the response.
     * @return boolean
     */
    public function response_valid($responsedata) {
        // Work with a response object.
        if (!is_a($responsedata, 'mod_questionnaire\responsetype\response\response')) {
            $response = \mod_questionnaire\responsetype\response\response::response_from_webform($responsedata, [$this]);
        } else {
            $response = $responsedata;
        }
        $num = 0;
        $nbchoices = count($this->choices);
        $na = get_string('notapplicable', 'questionnaire');

        // Create an answers array indexed by choiceid for ease.
        $answers = [];
        $nodups = [];
        if (isset($response->answers[$this->id])) {
            foreach ($response->answers[$this->id] as $answer) {
                $answers[$answer->choiceid] = $answer;
                $nodups[] = $answer->value;
            }
        }

        foreach ($this->choices as $cid => $choice) {
            // In case we have named degrees on the Likert scale, count them to substract from nbchoices.
            $nameddegrees = 0;
            $content = $choice->content;
            if (preg_match("/^[0-9]{1,3}=/", $content)) {
                $nameddegrees++;
            } else {
                if (isset($answers[$cid]) && ($answers[$cid]->value == $na)) {
                    $answers[$cid]->value = -1;
                }
                // If choice value == -999 this is a not yet answered choice.
                $num += (isset($answers[$cid]) && ($answers[$cid]->value != -999));
            }
            $nbchoices -= $nameddegrees;
        }
        // If nodupes and nb choice restricted, nbchoices may be > actual choices, so limit it to $question->length.
        $isrestricted = ($this->length < count($this->choices)) && $this->no_duplicate_choices();
        if ($isrestricted) {
            $nbchoices = min ($nbchoices, $this->length);
        }

        // Test for duplicate answers in a no duplicate question type.
        if ($this->no_duplicate_choices()) {
            foreach ($answers as $answer) {
                if (count(array_keys($nodups, $answer->value)) > 1) {
                    return false;
                }
            }
        }

        if (($num != $nbchoices) && ($num != 0)) {
            return false;
        } else {
            return parent::response_valid($responsedata);
        }
    }

    /**
     * Return the length form element.
     * @param \MoodleQuickForm $mform
     * @param string $helptext
     */
    protected function form_length(\MoodleQuickForm $mform, $helptext = '') {
        return parent::form_length($mform, 'numberscaleitems');
    }

    /**
     * Return the precision form element.
     * @param \MoodleQuickForm $mform
     * @param string $helptext
     */
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
     * Override if the question uses the extradata field.
     * @param \MoodleQuickForm $mform
     * @param string $helpname
     * @return \MoodleQuickForm
     */
    protected function form_extradata(\MoodleQuickForm $mform, $helpname = '') {
        $defaultvalue = '';
        foreach ($this->nameddegrees as $value => $label) {
            $defaultvalue .= $value . '=' . $label . "\n";
        }

        $options = ['wrap' => 'virtual'];
        $mform->addElement('textarea', 'allnameddegrees', get_string('allnameddegrees', 'questionnaire'), $options);
        $mform->setDefault('allnameddegrees', $defaultvalue);
        $mform->setType('allnameddegrees', PARAM_RAW);
        $mform->addHelpButton('allnameddegrees', 'allnameddegrees', 'questionnaire');

        return $mform;
    }

    /**
     * Any preprocessing of general data.
     * @param \stdClass $formdata
     * @return bool
     */
    protected function form_preprocess_data($formdata) {
        $nameddegrees = [];
        // Named degrees are put one per line in the form "[value]=[label]".
        if (!empty($formdata->allnameddegrees)) {
            $nameddegreelines = explode("\n", $formdata->allnameddegrees);
            foreach ($nameddegreelines as $nameddegreeline) {
                $nameddegreeline = trim($nameddegreeline);
                if (($nameddegree = \mod_questionnaire\question\choice::content_is_named_degree_choice($nameddegreeline)) !==
                    false) {
                    $nameddegrees += $nameddegree;
                }
            }
        }

        // Now store the new named degrees in extradata.
        $formdata->extradata = json_encode($nameddegrees);
        return parent::form_preprocess_data($formdata);
    }

    /**
     * Override this function for question specific choice preprocessing.
     * @param \stdClass $formdata
     * @return false
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
            if (self::type_is_no_duplicate_choices($formdata->precise) && ($formdata->length > $nbvalues || !$formdata->length)) {
                $formdata->length = $nbvalues;
            }
        }
        return true;
    }

    /**
     * Update the choice with the choicerecord.
     * @param \stdClass $choicerecord
     * @return bool
     */
    public function update_choice($choicerecord) {
        if ($nameddegree = \mod_questionnaire\question\choice::content_is_named_degree_choice($choicerecord->content)) {
            // Preserve any existing value from the new array.
            $this->nameddegrees = $nameddegree + $this->nameddegrees;
            $this->insert_nameddegrees($this->nameddegrees);
        }
        return parent::update_choice($choicerecord);
    }

    /**
     * Add a new choice to the database.
     * @param \stdClass $choicerecord
     * @return bool
     */
    public function add_choice($choicerecord) {
        if ($nameddegree = \mod_questionnaire\question\choice::content_is_named_degree_choice($choicerecord->content)) {
            // Preserve any existing value from the new array.
            $this->nameddegrees = $nameddegree + $this->nameddegrees;
            $this->insert_nameddegrees($this->nameddegrees);
        }
        return parent::add_choice($choicerecord);
    }

    /**
     * True if question provides mobile support.
     *
     * @return bool
     */
    public function supports_mobile() {
        return true;
    }

    /**
     * Override and return false if not supporting mobile app.
     * @param int $qnum
     * @param bool $autonum
     * @return \stdClass
     */
    public function mobile_question_display($qnum, $autonum = false) {
        $mobiledata = parent::mobile_question_display($qnum, $autonum);
        $mobiledata->rates = $this->mobile_question_rates_display();
        if ($this->has_na_column()) {
            $mobiledata->hasnacolumn = (object)['value' => -1, 'label' => get_string('notapplicable', 'questionnaire')];
        }

        $mobiledata->israte = true;
        return $mobiledata;
    }

    /**
     * Override and return false if not supporting mobile app.
     * @return array
     */
    public function mobile_question_choices_display() {
        $choices = [];
        $excludes = [];
        $vals = $extracontents = [];
        $cnum = 0;
        foreach ($this->choices as $choiceid => $choice) {
            $choice->na = false;
            $choice->choice_id = $choiceid;
            $choice->id = $choiceid;
            $choice->question_id = $this->id;

            // Add a fieldkey for each choice.
            $choice->fieldkey = $this->mobile_fieldkey($choiceid);

            if ($this->osgood_rate_scale()) {
                list($choice->leftlabel, $choice->rightlabel) = array_merge(preg_split('/[|]/', $choice->content), []);
            }

            if ($this->normal_rate_scale() || $this->no_duplicate_choices()) {
                $choices[$cnum] = $choice;
                if ($this->required()) {
                    $choices[$cnum]->min = 0;
                    $choices[$cnum]->minstr = 1;
                } else {
                    $choices[$cnum]->min = 0;
                    $choices[$cnum]->minstr = 1;
                }
                $choices[$cnum]->max = intval($this->length) - 1;
                $choices[$cnum]->maxstr = intval($this->length);

            } else if ($this->has_na_column()) {
                $choices[$cnum] = $choice;
                if ($this->required()) {
                    $choices[$cnum]->min = 0;
                    $choices[$cnum]->minstr = 1;
                } else {
                    $choices[$cnum]->min = 0;
                    $choices[$cnum]->minstr = 1;
                }
                $choices[$cnum]->max = intval($this->length);
                $choices[$cnum]->na = true;

            } else {
                $excludes[$choiceid] = $choiceid;
                if ($choice->value == null) {
                    if ($arr = explode('|', $choice->content)) {
                        if (count($arr) == 2) {
                            $choices[$cnum] = $choice;
                            $choices[$cnum]->content = '';
                            $choices[$cnum]->minstr = $arr[0];
                            $choices[$cnum]->maxstr = $arr[1];
                        }
                    }
                } else {
                    $val = intval($choice->value);
                    $vals[$val] = $val;
                    $extracontents[] = $choice->content;
                }
            }
            if ($vals) {
                if ($q = $choices) {
                    foreach (array_keys($q) as $itemid) {
                        $choices[$itemid]->min = min($vals);
                        $choices[$itemid]->max = max($vals);
                    }
                }
            }
            if ($extracontents) {
                $extracontents = array_unique($extracontents);
                $extrahtml = '<br><ul>';
                foreach ($extracontents as $extracontent) {
                    $extrahtml .= '<li>'.$extracontent.'</li>';
                }
                $extrahtml .= '</ul>';
                $options = ['noclean' => true, 'para' => false, 'filter' => true,
                    'context' => $this->context, 'overflowdiv' => true];
                $choice->content .= format_text($extrahtml, FORMAT_HTML, $options);
            }

            if (!in_array($choiceid, $excludes)) {
                $choice->choice_id = $choiceid;
                if ($choice->value == null) {
                    $choice->value = '';
                }
                $choices[$cnum] = $choice;
            }
            $cnum++;
        }

        return $choices;
    }

    /**
     * Display the rates question for mobile.
     * @return array
     */
    public function mobile_question_rates_display() {
        $rates = [];
        if (!empty($this->nameddegrees)) {
            foreach ($this->nameddegrees as $value => $label) {
                $rates[] = (object)['value' => $value, 'label' => $label];
            }
        } else {
            for ($i = 1; $i <= $this->length; $i++) {
                $rates[] = (object)['value' => $i, 'label' => $i];
            }
        }
        return $rates;
    }

    /**
     * Return the mobile response data.
     * @param response $response
     * @return array
     */
    public function get_mobile_response_data($response) {
        $resultdata = [];
        if (isset($response->answers[$this->id])) {
            foreach ($response->answers[$this->id] as $answer) {
                // Add a fieldkey for each choice.
                if (!empty($this->nameddegrees)) {
                    if (isset($this->nameddegrees[$answer->value])) {
                        $resultdata[$this->mobile_fieldkey($answer->choiceid)] = $this->nameddegrees[$answer->value];
                    } else {
                        $resultdata[$this->mobile_fieldkey($answer->choiceid)] = $answer->value;
                    }
                } else {
                    $resultdata[$this->mobile_fieldkey($answer->choiceid)] = $answer->value;
                }
            }
        }
        return $resultdata;
    }

    /**
     * Add the nameddegrees property.
     */
    private function add_nameddegrees_from_extradata() {
        if (!empty($this->extradata)) {
            $this->nameddegrees = json_decode($this->extradata, true);
        }
    }

    /**
     * Insert nameddegress to the extradata database field.
     * @param array $nameddegrees
     * @return bool
     * @throws \dml_exception
     */
    public function insert_nameddegrees(array $nameddegrees) {
        return $this->insert_extradata(json_encode($nameddegrees));
    }

    /**
     * Helper function used to move existing named degree choices for the specified question from the "quest_choice" table to the
     * "question" table.
     * @param int $qid
     * @param null|\stdClass $questionrec
     */
    public static function move_nameddegree_choices(int $qid = 0, \stdClass $questionrec = null) {
        global $DB;

        if ($qid !== 0) {
            $question = new rate($qid);
        } else {
            $question = new rate(0, $questionrec);
        }
        $nameddegrees = [];
        $oldchoiceids = [];
        // There was an issue where rate values were being stored as 1..n, no matter what the named degree value was. We need to fix
        // the old responses now. This also assumes that the values are now 1 based rather than 0 based.
        $newvalues = [];
        $oldval = 1;
        foreach ($question->choices as $choice) {
            if ($nameddegree = $choice->is_named_degree_choice()) {
                $nameddegrees += $nameddegree;
                $oldchoiceids[] = $choice->id;
                reset($nameddegree);
                $newvalues[$oldval++] = key($nameddegree);
            }
        }

        if (!empty($nameddegrees)) {
            if ($question->insert_nameddegrees($nameddegrees)) {
                // Remove the old named desgree from the choices table.
                foreach ($oldchoiceids as $choiceid) {
                    \mod_questionnaire\question\choice::delete_from_db_by_id($choiceid);
                }

                // First get all existing rank responses for this question.
                $responses = $DB->get_recordset('questionnaire_response_rank', ['question_id' => $question->id]);
                // Iterating over each response record ensures we won't change an existing record more than once.
                foreach ($responses as $response) {
                    // Then, if the old value exists, set it to the new one.
                    if (isset($newvalues[$response->rankvalue])) {
                        $DB->set_field('questionnaire_response_rank', 'rankvalue', $newvalues[$response->rankvalue],
                            ['id' => $response->id]);
                    }
                }
                $responses->close();
            }
        }
    }

    /**
     * Helper function to move named degree choices for all questions, optionally for a specific surveyid.
     * This should only be called for an upgrade from before '2018110103', or from a restore operation for a version of a
     * questionnaire before '2018110103'.
     * @param int|null $surveyid
     */
    public static function move_all_nameddegree_choices(int $surveyid = null) {
        global $DB;

        // This operation might take a while. Cancel PHP timeouts for this.
        \core_php_time_limit::raise();

        // First, let's adjust all rate answers from zero based to one based (see GHI223).
        // If a specific survey is being dealt with, only use the questions from that survey.
        $skip = false;
        if ($surveyid !== null) {
            $qids = $DB->get_records_menu('questionnaire_question', ['surveyid' => $surveyid, 'type_id' => QUESRATE],
                '', 'id,surveyid');
            if (!empty($qids)) {
                list($qsql, $qparams) = $DB->get_in_or_equal(array_keys($qids));
            } else {
                // No relevant questions, so no need to do this step.
                $skip = true;
            }
        }

        // If we're doing this step, let's do it.
        if (!$skip) {
            $select = 'UPDATE {questionnaire_response_rank} ' .
                'SET rankvalue = (rankvalue + 1) ' .
                'WHERE (rankvalue >= 0)';
            if ($surveyid !== null) {
                $select .= ' AND (question_id ' . $qsql . ')';
            } else {
                $qparams = [];
            }
            $DB->execute($select, $qparams);
        }

        $args = ['type_id' => QUESRATE];
        if ($surveyid !== null) {
            $args['surveyid'] = $surveyid;
        }
        $ratequests = $DB->get_recordset('questionnaire_question', $args);
        foreach ($ratequests as $questionrec) {
            self::move_nameddegree_choices(0, $questionrec);
        }
        $ratequests->close();
    }

    /**
     * Set label for per column inside rate table.
     *
     * @param int $rowposition
     * @param string $choicetitle
     * @param int $colposition
     * @param string $choiceanswer
     * @return string
     */
    private function set_label(int $rowposition, string $choicetitle, int $colposition, string $choiceanswer): string {
        $a = (object) [
            'rowposition' => $rowposition,
            'choicetitle' => $choicetitle,
            'colposition' => $colposition,
            'choiceanswer' => $choiceanswer,
        ];
        return get_string('accessibility:rate:choice', 'questionnaire', $a);
    }
}
