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
 * Defines the editing form for the calculated question data set items.
 *
 * @package    qtype
 * @subpackage calculated
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/edit_question_form.php');


/**
 * Calculated question data set items editing form definition.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_dataset_dependent_items_form extends question_wizard_form {
    /**
     * Question object with options and answers already loaded by get_question_options
     * Be careful how you use this it is needed sometimes to set up the structure of the
     * form in definition_inner but data is always loaded into the form with set_defaults.
     *
     * @var object
     */
    public $question;
    /**
     * Reference to question type object
     *
     * @var question_dataset_dependent_questiontype
     */
    public $qtypeobj;

    public $datasetdefs;

    public $maxnumber = -1;

    public $regenerate;

    public $noofitems;

    public $outsidelimit = false;

    public $commentanswers = array();

    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    public function __construct($submiturl, $question, $regenerate) {
        global $SESSION, $CFG, $DB;
        $this->regenerate = $regenerate;
        $this->question = $question;
        $this->qtypeobj = question_bank::get_qtype($this->question->qtype);
        // Validate the question category.
        if (!$category = $DB->get_record('question_categories',
                array('id' => $question->category))) {
            print_error('categorydoesnotexist', 'question', $returnurl);
        }
        $this->category = $category;
        $this->categorycontext = context::instance_by_id($category->contextid);
        // Get the dataset defintions for this question.
        if (empty($question->id)) {
            $this->datasetdefs = $this->qtypeobj->get_dataset_definitions(
                    $question->id, $SESSION->calculated->definitionform->dataset);
        } else {
            if (empty($question->options)) {
                $this->get_question_options($question);
            }
            $this->datasetdefs = $this->qtypeobj->get_dataset_definitions(
                    $question->id, array());
        }

        foreach ($this->datasetdefs as $datasetdef) {
            // Get maxnumber.
            if ($this->maxnumber == -1 || $datasetdef->itemcount < $this->maxnumber) {
                $this->maxnumber = $datasetdef->itemcount;
            }
        }
        foreach ($this->datasetdefs as $defid => $datasetdef) {
            if (isset($datasetdef->id)) {
                $this->datasetdefs[$defid]->items =
                        $this->qtypeobj->get_database_dataset_items($datasetdef->id);
            }
        }
        parent::__construct($submiturl);
    }

    protected function definition() {
        $labelsharedwildcard = get_string("sharedwildcard", "qtype_calculated");
        $mform =& $this->_form;
        $mform->setDisableShortforms();

        $strquestionlabel = $this->qtypeobj->comment_header($this->question);
        if ($this->maxnumber != -1 ) {
            $this->noofitems = $this->maxnumber;
        } else {
            $this->noofitems = 0;
        }
        $label = get_string("sharedwildcards", "qtype_calculated");

        $html2 = $this->qtypeobj->print_dataset_definitions_category_shared(
                $this->question, $this->datasetdefs);
        $mform->addElement('static', 'listcategory', $label, $html2);
        // ...----------------------------------------------------------------------.
        $mform->addElement('submit', 'updatedatasets',
                get_string('updatedatasetparam', 'qtype_calculated'));
        $mform->registerNoSubmitButton('updatedatasets');
        $mform->addElement('header', 'additemhdr',
                get_string('itemtoadd', 'qtype_calculated'));
        $idx = 1;
        $data = array();
        $j = (($this->noofitems) * count($this->datasetdefs))+1;
        foreach ($this->datasetdefs as $defkey => $datasetdef) {
            if ($datasetdef->category |= 0 ) {
                $name = get_string('sharedwildcard', 'qtype_calculated', $datasetdef->name);
            } else {
                $name = get_string('wildcard', 'qtype_calculated', $datasetdef->name);
            }
            $mform->addElement('text', "number[$j]", $name);
            $mform->setType("number[$j]", PARAM_FLOAT);
            $this->qtypeobj->custom_generator_tools_part($mform, $idx, $j);
            $idx++;
            $mform->addElement('hidden', "definition[$j]");
            $mform->setType("definition[$j]", PARAM_RAW);
            $mform->addElement('hidden', "itemid[$j]");
            $mform->setType("itemid[$j]", PARAM_RAW);
            $mform->addElement('static', "divider[$j]", '', '<hr />');
            $mform->setType("divider[$j]", PARAM_RAW);
            $j++;
        }

        $mform->addElement('header', 'updateanswershdr',
                get_string('answerstoleranceparam', 'qtype_calculated'));
        $mform->addElement('submit', 'updateanswers',
                get_string('updatetolerancesparam', 'qtype_calculated'));
        $mform->setAdvanced('updateanswers', true);
        $mform->registerNoSubmitButton('updateanswers');

        $answers = fullclone($this->question->options->answers);
        $key1 =1;
        foreach ($answers as $key => $answer) {
            if ('' === $answer->answer) {
                // Do nothing.
            } else if ('*' === $answer->answer) {
                $mform->addElement('static',
                        'answercomment[' . ($this->noofitems+$key1) . ']', $answer->answer);
                $mform->addElement('hidden', 'tolerance['.$key.']', '');
                $mform->setType('tolerance['.$key.']', PARAM_RAW);
                $mform->setAdvanced('tolerance['.$key.']', true);
                $mform->addElement('hidden', 'tolerancetype['.$key.']', '');
                $mform->setType('tolerancetype['.$key.']', PARAM_RAW);
                $mform->setAdvanced('tolerancetype['.$key.']', true);
                $mform->addElement('hidden', 'correctanswerlength['.$key.']', '');
                $mform->setType('correctanswerlength['.$key.']', PARAM_RAW);
                $mform->setAdvanced('correctanswerlength['.$key.']', true);
                $mform->addElement('hidden', 'correctanswerformat['.$key.']', '');
                $mform->setType('correctanswerformat['.$key.']', PARAM_RAW);
                $mform->setAdvanced('correctanswerformat['.$key.']', true);
            } else {
                $mform->addElement('static', 'answercomment[' . ($this->noofitems+$key1) . ']',
                        $answer->answer);
                $mform->addElement('text', 'tolerance['.$key.']',
                        get_string('tolerance', 'qtype_calculated'));
                $mform->setType('tolerance['.$key.']', PARAM_RAW);
                $mform->setAdvanced('tolerance['.$key.']', true);
                $mform->addElement('select', 'tolerancetype['.$key.']',
                        get_string('tolerancetype', 'qtype_numerical'),
                        $this->qtypeobj->tolerance_types());
                $mform->setAdvanced('tolerancetype['.$key.']', true);

                $mform->addElement('select', 'correctanswerlength['.$key.']',
                        get_string('correctanswershows', 'qtype_calculated'), range(0, 9));
                $mform->setAdvanced('correctanswerlength['.$key.']', true);

                $answerlengthformats = array(
                    '1' => get_string('decimalformat', 'qtype_numerical'),
                    '2' => get_string('significantfiguresformat', 'qtype_calculated')
                );
                $mform->addElement('select', 'correctanswerformat['.$key.']',
                        get_string('correctanswershowsformat', 'qtype_calculated'),
                        $answerlengthformats);
                $mform->setAdvanced('correctanswerformat['.$key.']', true);
                $mform->addElement('static', 'dividertolerance', '', '<hr />');
                $mform->setAdvanced('dividertolerance', true);
            }
            $key1++;
        }

        $addremoveoptions = array();
        $addremoveoptions['1']='1';
        for ($i=10; $i<=100; $i+=10) {
             $addremoveoptions["$i"]="$i";
        }
        $showoptions = Array();
        $showoptions['1']='1';
        $showoptions['2']='2';
        $showoptions['5']='5';
        for ($i=10; $i<=100; $i+=10) {
             $showoptions["$i"]="$i";
        }
        $mform->addElement('header', 'addhdr', get_string('add', 'moodle'));
        $mform->closeHeaderBefore('addhdr');

        if ($this->qtypeobj->supports_dataset_item_generation()) {
            $radiogrp = array();
            $radiogrp[] =& $mform->createElement('radio', 'nextpageparam[forceregeneration]',
                    null, get_string('reuseifpossible', 'qtype_calculated'), 0);
            $radiogrp[] =& $mform->createElement('radio', 'nextpageparam[forceregeneration]',
                    null, get_string('forceregenerationshared', 'qtype_calculated'), 1);
            $radiogrp[] =& $mform->createElement('radio', 'nextpageparam[forceregeneration]',
                    null, get_string('forceregenerationall', 'qtype_calculated'), 2);
            $mform->addGroup($radiogrp, 'forceregenerationgrp',
                    get_string('nextitemtoadd', 'qtype_calculated'), "<br/>", false);
        }

        $mform->addElement('submit', 'getnextbutton', get_string('getnextnow', 'qtype_calculated'));
        $mform->addElement('static', "dividera", '', '<hr />');
        $addgrp = array();
        $addgrp[] =& $mform->createElement('submit', 'addbutton', get_string('add', 'moodle'));
        $addgrp[] =& $mform->createElement('select', "selectadd",
                get_string('additem', 'qtype_calculated'), $addremoveoptions);
        $addgrp[] = & $mform->createElement('static', "stat", "Items",
                get_string('newsetwildcardvalues', 'qtype_calculatedsimple'));
        $mform->addGroup($addgrp, 'addgrp', get_string('additem', 'qtype_calculated'), ' ', false);
        $mform->addElement('static', "divideradd", '', '');
        if ($this->noofitems > 0) {
            $mform->addElement('header', 'deleteitemhdr', get_string('delete', 'moodle'));
            $deletegrp = array();
            $deletegrp[] = $mform->createElement('submit', 'deletebutton',
                    get_string('delete', 'moodle'));
            $deletegrp[] = $mform->createElement('select', 'selectdelete',
                    get_string('deleteitem', 'qtype_calculated')."1", $addremoveoptions);
            $deletegrp[] = $mform->createElement('static', "stat", "Items",
                    get_string('setwildcardvalues', 'qtype_calculatedsimple'));
            $mform->addGroup($deletegrp, 'deletegrp', '', '   ', false);
        } else {
            $mform->addElement('static', 'warning', '', '<span class="error">' .
                    get_string('youmustaddatleastoneitem', 'qtype_calculated').'</span>');
        }

        $addgrp1 = array();
        $addgrp1[] = $mform->createElement('submit', 'showbutton',
                get_string('showitems', 'qtype_calculated'));
        $addgrp1[] = $mform->createElement('select', "selectshow", '' , $showoptions);
        $addgrp1[] = $mform->createElement('static', "stat", '',
                get_string('setwildcardvalues', 'qtype_calculated'));
        $mform->addGroup($addgrp1, 'addgrp1', '', '   ', false);
        $mform->registerNoSubmitButton('showbutton');
        $mform->closeHeaderBefore('addgrp1');
        // ...----------------------------------------------------------------------.
        $j = $this->noofitems * count($this->datasetdefs);
        $k = optional_param('selectshow', 1, PARAM_INT);
        for ($i = $this->noofitems; $i >= 1; $i--) {
            if ($k > 0) {
                $mform->addElement('header', 'setnoheader' . $i, "<b>" .
                        get_string('setno', 'qtype_calculated', $i)."</b>&nbsp;&nbsp;");
            }
            foreach ($this->datasetdefs as $defkey => $datasetdef) {
                if ($k > 0) {
                    if ($datasetdef->category == 0 ) {
                        $mform->addElement('text', "number[$j]",
                                get_string('wildcard', 'qtype_calculated', $datasetdef->name));
                    } else {
                        $mform->addElement('text', "number[$j]", get_string(
                                'sharedwildcard', 'qtype_calculated', $datasetdef->name));
                    }

                } else {
                    $mform->addElement('hidden', "number[$j]" , '');
                }
                $mform->setType("number[$j]", PARAM_FLOAT);
                $mform->addElement('hidden', "itemid[$j]");
                $mform->setType("itemid[$j]", PARAM_INT);

                $mform->addElement('hidden', "definition[$j]");
                $mform->setType("definition[$j]", PARAM_NOTAGS);
                $data[$datasetdef->name] =$datasetdef->items[$i]->value;

                $j--;
            }
            if ('' != $strquestionlabel && ($k > 0 )) {
                // ... $this->outsidelimit || !empty($this->numbererrors ).
                $repeated[] = $mform->addElement('static', "answercomment[$i]", $strquestionlabel);
                // Decode equations in question text.
                $qtext = $this->qtypeobj->substitute_variables(
                        $this->question->questiontext, $data);
                $textequations = $this->qtypeobj->find_math_equations($qtext);
                if ($textequations != '' && count($textequations) > 0 ) {
                    $mform->addElement('static', "divider1[$j]", '',
                            'Formulas {=..} in question text');
                    foreach ($textequations as $key => $equation) {
                        if ($formulaerrors = qtype_calculated_find_formula_errors($equation)) {
                            $str=$formulaerrors;
                        } else {
                            eval('$str = '.$equation.';');
                        }

                        $mform->addElement('static', "textequation", "{=$equation}", "=".$str);
                    }
                }

            }
            $k--;

        }
        $mform->addElement('static', 'outsidelimit', '', '');
        // ...----------------------------------------------------------------------
        // Non standard name for button element needed so not using add_action_buttons.
        if (!($this->noofitems==0) ) {
            $mform->addElement('submit', 'savechanges', get_string('savechanges'));
            $mform->closeHeaderBefore('savechanges');
        }

        $this->add_hidden_fields();

        $mform->addElement('hidden', 'category');
        $mform->setType('category', PARAM_SEQUENCE);

        $mform->addElement('hidden', 'wizard', 'datasetitems');
        $mform->setType('wizard', PARAM_ALPHA);
    }

    public function set_data($question) {
        $formdata = array();
        $fromform = new stdClass();
        if (isset($question->options)) {
            $answers = $question->options->answers;
            if (count($answers)) {
                if (optional_param('updateanswers', false, PARAM_BOOL) ||
                        optional_param('updatedatasets', false, PARAM_BOOL)) {
                    foreach ($answers as $key => $answer) {
                        $fromform->tolerance[$key]= $this->_form->getElementValue(
                                'tolerance['.$key.']');
                        $answer->tolerance = $fromform->tolerance[$key];
                        $fromform->tolerancetype[$key]= $this->_form->getElementValue(
                                'tolerancetype['.$key.']');
                        if (is_array($fromform->tolerancetype[$key])) {
                            $fromform->tolerancetype[$key]= $fromform->tolerancetype[$key][0];
                        }
                        $answer->tolerancetype = $fromform->tolerancetype[$key];
                        $fromform->correctanswerlength[$key]= $this->_form->getElementValue(
                                'correctanswerlength['.$key.']');
                        if (is_array($fromform->correctanswerlength[$key])) {
                            $fromform->correctanswerlength[$key] =
                                    $fromform->correctanswerlength[$key][0];
                        }
                        $answer->correctanswerlength = $fromform->correctanswerlength[$key];
                        $fromform->correctanswerformat[$key] = $this->_form->getElementValue(
                                'correctanswerformat['.$key.']');
                        if (is_array($fromform->correctanswerformat[$key])) {
                            $fromform->correctanswerformat[$key] =
                                    $fromform->correctanswerformat[$key][0];
                        }
                        $answer->correctanswerformat = $fromform->correctanswerformat[$key];
                    }
                    $this->qtypeobj->save_question_calculated($question, $fromform);

                } else {
                    foreach ($answers as $key => $answer) {
                        $formdata['tolerance['.$key.']'] = $answer->tolerance;
                        $formdata['tolerancetype['.$key.']'] = $answer->tolerancetype;
                        $formdata['correctanswerlength['.$key.']'] = $answer->correctanswerlength;
                        $formdata['correctanswerformat['.$key.']'] = $answer->correctanswerformat;
                    }
                }
            }
        }
        // Fill out all data sets and also the fields for the next item to add.
        $j = $this->noofitems * count($this->datasetdefs);
        for ($itemnumber = $this->noofitems; $itemnumber >= 1; $itemnumber--) {
            $data = array();
            foreach ($this->datasetdefs as $defid => $datasetdef) {
                if (isset($datasetdef->items[$itemnumber])) {
                    $formdata["number[$j]"] = $datasetdef->items[$itemnumber]->value;
                    $formdata["definition[$j]"] = $defid;
                    $formdata["itemid[$j]"] = $datasetdef->items[$itemnumber]->id;
                    $data[$datasetdef->name] = $datasetdef->items[$itemnumber]->value;
                }
                $j--;
            }
            $comment = $this->qtypeobj->comment_on_datasetitems($this->qtypeobj, $question->id,
                    $question->questiontext, $answers, $data, $itemnumber);
            if ($comment->outsidelimit) {
                $this->outsidelimit=$comment->outsidelimit;
            }
            $totalcomment='';
            foreach ($question->options->answers as $key => $answer) {
                $totalcomment .= $comment->stranswers[$key].'<br/>';
            }
            $formdata['answercomment['.$itemnumber.']'] = $totalcomment;
        }

        $formdata['nextpageparam[forceregeneration]'] = $this->regenerate;
        $formdata['selectdelete'] = '1';
        $formdata['selectadd'] = '1';
        $j = $this->noofitems * count($this->datasetdefs)+1;
        $data = array(); // Data for comment_on_datasetitems later.
        // Dataset generation defaults.
        if ($this->qtypeobj->supports_dataset_item_generation()) {
            $itemnumber = $this->noofitems+1;
            foreach ($this->datasetdefs as $defid => $datasetdef) {
                if (!optional_param('updatedatasets', false, PARAM_BOOL) &&
                        !optional_param('updateanswers', false, PARAM_BOOL)) {
                    $formdata["number[$j]"] = $this->qtypeobj->generate_dataset_item(
                            $datasetdef->options);
                } else {
                    $formdata["number[$j]"] = $this->_form->getElementValue("number[$j]");
                }
                $formdata["definition[$j]"] = $defid;
                $formdata["itemid[$j]"] = isset($datasetdef->items[$itemnumber]) ?
                        $datasetdef->items[$itemnumber]->id : 0;
                $data[$datasetdef->name] = $formdata["number[$j]"];
                $j++;
            }
        }

        // Existing records override generated data depending on radio element.
        $j = $this->noofitems * count($this->datasetdefs) + 1;
        if (!$this->regenerate && !optional_param('updatedatasets', false, PARAM_BOOL) &&
                !optional_param('updateanswers', false, PARAM_BOOL)) {
            $idx = 1;
            $itemnumber = $this->noofitems + 1;
            foreach ($this->datasetdefs as $defid => $datasetdef) {
                if (isset($datasetdef->items[$itemnumber])) {
                    $formdata["number[$j]"] = $datasetdef->items[$itemnumber]->value;
                    $formdata["definition[$j]"] = $defid;
                    $formdata["itemid[$j]"] = $datasetdef->items[$itemnumber]->id;
                    $data[$datasetdef->name] = $datasetdef->items[$itemnumber]->value;
                }
                $j++;
            }
        }

        $comment = $this->qtypeobj->comment_on_datasetitems($this->qtypeobj, $question->id,
                $question->questiontext, $answers, $data, ($this->noofitems + 1));
        if (isset($comment->outsidelimit) && $comment->outsidelimit) {
            $this->outsidelimit=$comment->outsidelimit;
        }
        $key1 = 1;
        foreach ($question->options->answers as $key => $answer) {
            $formdata['answercomment['.($this->noofitems+$key1).']'] = $comment->stranswers[$key];
            $key1++;
        }

        if ($this->outsidelimit) {
            $formdata['outsidelimit']= '<span class="error">' .
                    get_string('oneanswertrueansweroutsidelimits', 'qtype_calculated') . '</span>';
        }
        $formdata = $this->qtypeobj->custom_generator_set_data($this->datasetdefs, $formdata);

        parent::set_data((object)($formdata + (array)$question));
    }

    public function validation($data, $files) {
        $errors = array();
        if (isset($data['savechanges']) && ($this->noofitems==0) ) {
            $errors['warning'] = get_string('warning', 'mnet');
        }
        if ($this->outsidelimit) {
            $errors['outsidelimits'] =
                    get_string('oneanswertrueansweroutsidelimits', 'qtype_calculated');
        }
        $numbers = $data['number'];
        foreach ($numbers as $key => $number) {
            if (! is_numeric($number)) {
                if (stristr($number, ', ')) {
                    $errors['number['.$key.']'] = get_string(
                        'The , cannot be used, use . as in 0.013 or 1.3e-2', 'qtype_calculated');
                } else {
                    $errors['number['.$key.']'] = get_string(
                            'This is not a valid number', 'qtype_calculated');
                }
            } else if (stristr($number, 'x')) {
                $errors['number['.$key.']'] = get_string(
                        'Hexadecimal format (i.e. 0X12d) is not allowed', 'qtype_calculated');
            } else if (is_nan($number)) {
                $errors['number['.$key.']'] = get_string(
                        'is a NAN number', 'qtype_calculated');
            }
        }
        return $errors;
    }
}
