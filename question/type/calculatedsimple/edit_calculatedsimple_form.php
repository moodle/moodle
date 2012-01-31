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
 * Defines the editing form for the calculated simplequestion type.
 *
 * @package    qtype
 * @subpackage calculatedsimple
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/calculated/edit_calculated_form.php');


/**
 * Editing form for the calculated simplequestion type.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculatedsimple_edit_form extends qtype_calculated_edit_form {
    /**
     * Handle to the question type for this question.
     *
     * @var question_calculatedsimple_qtype
     */
    public $qtypeobj;

    public $wildcarddisplay;

    public $questiondisplay;

    public $datasetdefs;

    public $reload = false;

    public $maxnumber = -1;

    public $regenerate = true;

    public $noofitems;

    public $outsidelimit = false;

    public $commentanswer = array();

    public $answer = array();

    public $nonemptyanswer = array();

    public $numbererrors = array();

    public $formdata = array();

    public function __construct($submiturl, $question, $category, $contexts, $formeditable = true) {
        $this->regenerate = true;
        $this->question = $question;

        $this->qtypeobj = question_bank::get_qtype($this->question->qtype);
        // get the dataset definitions for this question
        // coming here everytime even when using a NoSubmitButton
        // so this will only set the values to the actual question database content
        // which is not what we want so this should be removed from here
        // get priority to paramdatasets

        if ("1" == optional_param('reload', '', PARAM_INT)) {
            $this->reload = true;
        } else {
            $this->reload = false;
        }
        if (!$this->reload) { // use database data as this is first pass
            // question->id == 0 so no stored datasets
            if (!empty($question->id)) {

                $this->datasetdefs = $this->qtypeobj->get_dataset_definitions(
                        $question->id, array());

                if (!empty($this->datasetdefs)) {
                    foreach ($this->datasetdefs as $defid => $datasetdef) {
                        // first get the items in case their number does not correspond to itemcount
                        if (isset($datasetdef->id)) {
                            $this->datasetdefs[$defid]->items =
                                    $this->qtypeobj->get_database_dataset_items($datasetdef->id);
                            if ($this->datasetdefs[$defid]->items != '') {
                                $datasetdef->itemcount = count($this->datasetdefs[$defid]->items);
                            } else {
                                $datasetdef->itemcount = 0;
                            }
                        }
                        // Get maxnumber
                        if ($this->maxnumber == -1 || $datasetdef->itemcount < $this->maxnumber) {
                            $this->maxnumber = $datasetdef->itemcount;
                        }
                    }
                }

                $i = 0;
                foreach ($this->question->options->answers as $answer) {
                     $this->answer[$i] = $answer;
                     $i++;
                }
                $this->nonemptyanswer = $this->answer;
            }
            $datasettoremove = false;
            $newdatasetvalues = false;
            $newdataset = false;
        } else {
            // handle reload to get values from the form-elements
            // answers, datasetdefs and data_items. In any case the validation
            // step will warn the user of any error in settings the values.
            // Verification for the specific dataset values as the other parameters
            // unints, feeedback etc are handled elsewhere
            // handle request buttons :
            //    'analyzequestion' (Identify the wild cards {x..} present in answers)
            //    'addbutton' (create new set of datatitems)
            //    'updatedatasets' is handled automatically on each reload
            // The analyzequestion is done every time on reload
            // to detect any new wild cards so that the current display reflects
            // the mandatory (i.e. in answers) datasets
            //  to implement : don't do any changes if the question is used in a quiz.
            // If new datadef, new properties should erase items.
            // Most of the data
            $datasettoremove = false;
            $newdatasetvalues = false;
            $newdataset = false;
            $dummyform = new stdClass();
            $mandatorydatasets = array();
            // should not test on adding a new answer
            // should test if there are already olddatasets or if the 'analyzequestion'
            // submit button has been clicked
            if ('' != optional_param('datasetdef', '', PARAM_RAW) ||
                    '' != optional_param('analyzequestion', '', PARAM_RAW)) {

                if ($dummyform->answer = optional_param('answer', '', PARAM_NOTAGS)) {
                    // there is always at least one answer...
                    $fraction = optional_param('fraction', '', PARAM_NUMBER);
                    $tolerance = optional_param('tolerance', '', PARAM_NUMBER);
                    $tolerancetype = optional_param('tolerancetype', '', PARAM_NUMBER);
                    $correctanswerlength = optional_param('correctanswerlength', '', PARAM_INT);
                    $correctanswerformat = optional_param('correctanswerformat', '', PARAM_INT);

                    foreach ($dummyform->answer as $key => $answer) {
                        if (trim($answer) != '') {  // just look for non-empty
                            $this->answer[$key] = new stdClass();
                            $this->answer[$key]->answer = $answer;
                            $this->answer[$key]->fraction = $fraction[$key];
                            $this->answer[$key]->tolerance = $tolerance[$key];
                            $this->answer[$key]->tolerancetype = $tolerancetype[$key];
                            $this->answer[$key]->correctanswerlength = $correctanswerlength[$key];
                            $this->answer[$key]->correctanswerformat = $correctanswerformat[$key];
                            $this->nonemptyanswer[]= $this->answer[$key];
                            $mandatorydatasets += $this->qtypeobj->find_dataset_names($answer);
                        }
                    }
                }
                $this->datasetdefs = array();
                // rebuild datasetdefs from old values
                if ($olddef = optional_param('datasetdef', '', PARAM_RAW)) {
                    $calcmin = optional_param('calcmin', '', PARAM_NUMBER);
                    $calclength = optional_param('calclength', '', PARAM_INT);
                    $calcmax = optional_param('calcmax', '', PARAM_NUMBER);
                    $oldoptions  = optional_param('defoptions', '', PARAM_RAW);
                    $newdatasetvalues = false;
                    $sizeofolddef = count($olddef);
                    for ($key = 1; $key <= $sizeofolddef; $key++) {
                        $def = $olddef[$key];
                        $this->datasetdefs[$def]= new stdClass();
                        $this->datasetdefs[$def]->type = 1;
                        $this->datasetdefs[$def]->category = 0;
                        $this->datasetdefs[$def]->options = $oldoptions[$key];
                        $this->datasetdefs[$def]->calcmin = $calcmin[$key];
                        $this->datasetdefs[$def]->calcmax = $calcmax[$key];
                        $this->datasetdefs[$def]->calclength = $calclength[$key];
                        //then compare with new values
                        if (preg_match('~^(uniform|loguniform):([^:]*):([^:]*):([0-9]*)$~',
                                $this->datasetdefs[$def]->options, $regs)) {
                            if ($this->datasetdefs[$def]->calcmin != $regs[2]||
                                    $this->datasetdefs[$def]->calcmax != $regs[3] ||
                                    $this->datasetdefs[$def]->calclength != $regs[4]) {
                                $newdatasetvalues = true;
                            }
                        }
                        $this->datasetdefs[$def]->options = "uniform:" .
                                $this->datasetdefs[$def]->calcmin . ":" .
                                $this->datasetdefs[$def]->calcmax . ":" .
                                $this->datasetdefs[$def]->calclength;
                    }
                }
                // detect new datasets
                $newdataset = false;
                foreach ($mandatorydatasets as $datasetname) {
                    if (!isset($this->datasetdefs["1-0-$datasetname"])) {
                        $key = "1-0-$datasetname";
                        $this->datasetdefs[$key] = new stdClass();
                        $this->datasetdefs[$key]->type = 1;
                        $this->datasetdefs[$key]->category = 0;
                        $this->datasetdefs[$key]->name = $datasetname;
                        $this->datasetdefs[$key]->options = "uniform:1.0:10.0:1";
                        $newdataset = true;
                    } else {
                        $this->datasetdefs["1-0-$datasetname"]->name = $datasetname;
                    }
                }
                // remove obsolete datasets
                $datasettoremove = false;
                foreach ($this->datasetdefs as $defkey => $datasetdef) {
                    if (!isset($datasetdef->name)) {
                        $datasettoremove = true;
                        unset($this->datasetdefs[$defkey]);
                    }
                }
            }
        } // handle reload
        // create items if  $newdataset and noofitems > 0 and !$newdatasetvalues
        // eliminate any items if $newdatasetvalues
        // eliminate any items if $datasettoremove, $newdataset, $newdatasetvalues
        if ($datasettoremove ||$newdataset ||$newdatasetvalues) {
            foreach ($this->datasetdefs as $defkey => $datasetdef) {
                $datasetdef->itemcount = 0;
                unset($datasetdef->items);
            }
        }
        $maxnumber = -1;
        if ("" != optional_param('addbutton', '', PARAM_TEXT)) {
            $maxnumber = optional_param('selectadd', '', PARAM_INT); //FIXME: sloppy coding
            foreach ($this->datasetdefs as $defid => $datasetdef) {
                $datasetdef->itemcount = $maxnumber;
                unset($datasetdef->items);
                for ($numberadded = 1; $numberadded <= $maxnumber; $numberadded++) {
                    $datasetitem = new stdClass();
                    $datasetitem->itemnumber = $numberadded;
                    $datasetitem->id = 0;
                    $datasetitem->value = $this->qtypeobj->generate_dataset_item(
                            $datasetdef->options);
                    $this->datasetdefs[$defid]->items[$numberadded] = $datasetitem;
                }
            }
            $this->maxnumber = $maxnumber;
        } else {
            // Handle reload dataset items
            if ("" != optional_param('definition', '', PARAM_NOTAGS) &&
                    !($datasettoremove ||$newdataset ||$newdatasetvalues)) {
                $i = 1;
                $fromformdefinition = optional_param('definition', '', PARAM_NOTAGS);
                $fromformnumber = optional_param('number', '', PARAM_INT);
                $fromformitemid = optional_param('itemid', '', PARAM_INT);
                ksort($fromformdefinition);

                foreach ($fromformdefinition as $key => $defid) {
                    $addeditem = new stdClass();
                    $addeditem->id = $fromformitemid[$i];
                    $addeditem->value = $fromformnumber[$i];
                    $addeditem->itemnumber = ceil($i / count($this->datasetdefs));
                    $this->datasetdefs[$defid]->items[$addeditem->itemnumber] = $addeditem;
                    $this->datasetdefs[$defid]->itemcount = $i;
                    $i++;
                }
            }
            if (isset($addeditem->itemnumber) && $this->maxnumber < $addeditem->itemnumber) {
                $this->maxnumber = $addeditem->itemnumber;
                if (!empty($this->datasetdefs)) {
                    foreach ($this->datasetdefs as $datasetdef) {
                            $datasetdef->itemcount = $this->maxnumber;
                    }
                }
            }
        }

        parent::__construct($submiturl, $question, $category, $contexts, $formeditable);
    }

    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    protected function definition_inner($mform) {
        $strquestionlabel = $this->qtypeobj->comment_header($this->nonemptyanswer);
        $label = get_string("sharedwildcards", "qtype_calculated");
        $mform->addElement('hidden', 'synchronize', 0);
        $mform->addElement('hidden', 'initialcategory', 1);
        $mform->setType('initialcategory', PARAM_INT);
        $mform->addElement('hidden', 'reload', 1);
        $mform->setType('reload', PARAM_INT);
        $addfieldsname = 'updatequestion value';
        $addstring = get_string("updatecategory", "qtype_calculated");
        $mform->registerNoSubmitButton($addfieldsname);

        $this->add_per_answer_fields($mform, get_string('answerhdr', 'qtype_calculated', '{no}'),
                question_bank::fraction_options(), 1, 1);

        $this->add_unit_options($mform, $this);
        $this->add_unit_fields($mform, $this);
        $this->add_interactive_settings();

        $label = "<div class='mdl-align'></div><div class='mdl-align'>" .
                get_string('wildcardrole', 'qtype_calculatedsimple') . "</div>";
        $mform->addElement('html', "<div class='mdl-align'>&nbsp;</div>");
        // explaining the role of datasets so other strings can be shortened
        $mform->addElement('html', $label);

        $mform->addElement('submit', 'analyzequestion',
                get_string('findwildcards', 'qtype_calculatedsimple'));
        $mform->registerNoSubmitButton('analyzequestion');
        $mform->closeHeaderBefore('analyzequestion');
        $this->wizarddisplay = optional_param('analyzequestion', false, PARAM_BOOL);
        if ($this->maxnumber != -1) {
            $this->noofitems = $this->maxnumber;
        } else {
            $this->noofitems = 0;
        }
        if (!empty($this->datasetdefs)) {//So there are some datadefs
            // we put them on the page
            $key = 0;
            $mform->addElement('header', 'additemhdr',
                    get_string('wildcardparam', 'qtype_calculatedsimple'));
            $idx = 1;
            if (!empty($this->datasetdefs)) {// unnecessary test
                $j = (($this->noofitems) * count($this->datasetdefs))+1;//
                foreach ($this->datasetdefs as $defkey => $datasetdef) {
                    $mform->addElement('static', "na[$j]",
                            get_string('param', 'qtype_calculated', $datasetdef->name));
                    $this->qtypeobj->custom_generator_tools_part($mform, $idx, $j);
                    $mform->addElement('hidden', "datasetdef[$idx]");
                    $mform->setType("datasetdef[$idx]", PARAM_RAW);
                    $mform->addElement('hidden', "defoptions[$idx]");
                    $mform->setType("defoptions[$idx]", PARAM_RAW);
                    $idx++;
                    $mform->addElement('static', "divider[$j]", '', '<hr />');
                    $j++;
                }
            }
            //this should be done before the elements are created and stored as $this->formdata;
            //fill out all data sets and also the fields for the next item to add.
            /*Here we do already the values error analysis so that
             * we could force all wild cards values display if there is an error in values.
             * as using a , in a number */
            $this->numbererrors = array();
            if (!empty($this->datasetdefs)) {
                $j = $this->noofitems * count($this->datasetdefs);
                for ($itemnumber = $this->noofitems; $itemnumber >= 1; $itemnumber--) {
                    $data = array();
                    $numbererrors = array();
                    $comment = new stdClass();
                    $comment->stranswers = array();
                    $comment->outsidelimit = false;
                    $comment->answers = array();

                    foreach ($this->datasetdefs as $defid => $datasetdef) {
                        if (isset($datasetdef->items[$itemnumber])) {
                            $this->formdata["definition[$j]"] = $defid;
                            $this->formdata["itemid[$j]"] =
                                    $datasetdef->items[$itemnumber]->id;
                            $data[$datasetdef->name] = $datasetdef->items[$itemnumber]->value;
                            $this->formdata["number[$j]"] = $number =
                                    $datasetdef->items[$itemnumber]->value;
                            if (! is_numeric($number)) {
                                $a = new stdClass();
                                $a->name = '{'.$datasetdef->name.'}';
                                $a->value = $datasetdef->items[$itemnumber]->value;
                                if (stristr($number, ',')) {
                                    $this->numbererrors["number[$j]"] =
                                            get_string('nocommaallowed', 'qtype_calculated');
                                    $numbererrors .= $this->numbererrors['number['.$j.']']."<br />";

                                } else {
                                    $this->numbererrors["number[$j]"] =
                                            get_string('notvalidnumber', 'qtype_calculated', $a);
                                    $numbererrors .= $this->numbererrors['number['.$j.']']."<br />";
                                }
                            } else if (stristr($number, 'x')) { // hexa will pass the test
                                $a = new stdClass();
                                $a->name = '{'.$datasetdef->name.'}';
                                $a->value = $datasetdef->items[$itemnumber]->value;
                                $this->numbererrors['number['.$j.']'] =
                                        get_string('hexanotallowed', 'qtype_calculated', $a);
                                $numbererrors .= $this->numbererrors['number['.$j.']']."<br />";
                            } else if (is_nan($number)) {
                                $a = new stdClass();
                                $a->name = '{'.$datasetdef->name.'}';
                                $a->value = $datasetdef->items[$itemnumber]->value;
                                $this->numbererrors["number[$j]"] =
                                        get_string('notvalidnumber', 'qtype_calculated', $a);
                                $numbererrors .= $this->numbererrors['number['.$j.']']."<br />";
                            }
                        }
                        $j--;
                    }
                    if ($this->noofitems != 0) {
                        if (empty($numbererrors)) {
                            if (!isset($this->question->id)) {
                                $this->question->id = 0;
                            }
                            $this->question->questiontext = !empty($this->question->questiontext) ?
                                    $this->question->questiontext : '';
                            $comment = $this->qtypeobj->comment_on_datasetitems(
                                    $this->qtypeobj, $this->question->id,
                                    $this->question->questiontext, $this->nonemptyanswer,
                                    $data, $itemnumber);
                            if ($comment->outsidelimit) {
                                $this->outsidelimit = $comment->outsidelimit;
                            }
                            $totalcomment = '';

                            foreach ($this->nonemptyanswer as $key => $answer) {
                                $totalcomment .= $comment->stranswers[$key].'<br/>';
                            }

                            $this->formdata['answercomment['.$itemnumber.']'] = $totalcomment;
                        }
                    }
                }
                $this->formdata['selectdelete'] = '1';
                $this->formdata['selectadd'] = '1';
                $j = $this->noofitems * count($this->datasetdefs)+1;
                $data = array(); // data for comment_on_datasetitems later
                $idx = 1;
                foreach ($this->datasetdefs as $defid => $datasetdef) {
                    $this->formdata["datasetdef[$idx]"] = $defid;
                    $idx++;
                }
                $this->formdata = $this->qtypeobj->custom_generator_set_data(
                        $this->datasetdefs, $this->formdata);
            }

            $addoptions = Array();
            $addoptions['1'] = '1';
            for ($i = 10; $i <= 100; $i += 10) {
                $addoptions["$i"] = "$i";
            }
            $showoptions = Array();
            $showoptions['1'] = '1';
            $showoptions['2'] = '2';
            $showoptions['5'] = '5';
            for ($i = 10; $i <= 100; $i += 10) {
                $showoptions["$i"] = "$i";
            }
            $mform->closeHeaderBefore('additemhdr');
            $addgrp = array();
            $addgrp[] = $mform->createElement('submit', 'addbutton',
                    get_string('generatenewitemsset', 'qtype_calculatedsimple'));
            $addgrp[] = $mform->createElement('select', "selectadd", '', $addoptions);
            $addgrp[] = $mform->createElement('static', "stat", '',
                    get_string('newsetwildcardvalues', 'qtype_calculatedsimple'));
            $mform->addGroup($addgrp, 'addgrp', '', '   ', false);
            $mform->registerNoSubmitButton('addbutton');
            $mform->closeHeaderBefore('addgrp');
            $addgrp1 = array();
            $addgrp1[] = $mform->createElement('submit', 'showbutton',
                    get_string('showitems', 'qtype_calculatedsimple'));
            $addgrp1[] = $mform->createElement('select', "selectshow", '', $showoptions);
            $addgrp1[] = $mform->createElement('static', "stat", '',
                    get_string('setwildcardvalues', 'qtype_calculatedsimple'));
            $mform->addGroup($addgrp1, 'addgrp1', '', '   ', false);
            $mform->registerNoSubmitButton('showbutton');
            $mform->closeHeaderBefore('addgrp1');
            $mform->addElement('static', "divideradd", '', '');
            if ($this->noofitems == 0) {
                $mform->addElement('static', 'warningnoitems', '', '<span class="error">' .
                        get_string('youmustaddatleastonevalue', 'qtype_calculatedsimple') .
                        '</span>');
                $mform->closeHeaderBefore('warningnoitems');
            } else {
                $mform->addElement('header', 'additemhdr1',
                        get_string('wildcardvalues', 'qtype_calculatedsimple'));
                $mform->closeHeaderBefore('additemhdr1');
                if (!empty($this->numbererrors) || $this->outsidelimit) {
                    $mform->addElement('static', "alert", '', '<span class="error">' .
                            get_string('useadvance', 'qtype_calculatedsimple').'</span>');
                }

                $mform->addElement('submit', 'updatedatasets',
                        get_string('updatewildcardvalues', 'qtype_calculatedsimple'));
                $mform->registerNoSubmitButton('updatedatasets');
                $mform->setAdvanced("updatedatasets", true);

                //--------------------------------------------------------------
                $j = $this->noofitems * count($this->datasetdefs);
                $k = 1;
                if ("" != optional_param('selectshow', '', PARAM_INT)) {
                    $k = optional_param('selectshow', '', PARAM_INT);
                }

                for ($i = $this->noofitems; $i >= 1; $i--) {
                    foreach ($this->datasetdefs as $defkey => $datasetdef) {
                        if ($k > 0 ||  $this->outsidelimit || !empty($this->numbererrors)) {
                            $mform->addElement('text', "number[$j]", get_string(
                                    'wildcard', 'qtype_calculatedsimple', $datasetdef->name));
                            $mform->setAdvanced("number[$j]", true);
                            if (!empty($this->numbererrors['number['.$j.']'])) {
                                $mform->addElement('static', "numbercomment[$j]", '',
                                        '<span class="error">' .
                                        $this->numbererrors['number['.$j.']'] . '</span>');
                                $mform->setAdvanced("numbercomment[$j]", true);
                            }
                        } else {
                            $mform->addElement('hidden', "number[$j]", get_string(
                                    'wildcard', 'qtype_calculatedsimple', $datasetdef->name));
                        }
                        $mform->setType("number[$j]", PARAM_NUMBER);

                        $mform->addElement('hidden', "itemid[$j]");
                        $mform->setType("itemid[$j]", PARAM_INT);

                        $mform->addElement('hidden', "definition[$j]");
                        $mform->setType("definition[$j]", PARAM_NOTAGS);

                        $j--;
                    }
                    if (!empty($strquestionlabel) && ($k > 0 ||  $this->outsidelimit ||
                            !empty($this->numbererrors))) {
                        $mform->addElement('static', "answercomment[$i]", "<b>" .
                                get_string('setno', 'qtype_calculatedsimple', $i) .
                                "</b>&nbsp;&nbsp;" . $strquestionlabel);

                    }
                    if ($k > 0 ||  $this->outsidelimit || !empty($this->numbererrors)) {
                        $mform->addElement('static', "divider1[$j]", '', '<hr />');

                    }
                    $k--;
                }
            }
        } else {
            $mform->addElement('static', 'warningnowildcards', '', '<span class="error">' .
                    get_string('atleastonewildcard', 'qtype_calculatedsimple') . '</span>');
            $mform->closeHeaderBefore('warningnowildcards');
        }

        //----------------------------------------------------------------------
        // Non standard name for button element needed so not using add_action_buttons
        // hidden elements

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', 0);

        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);
        $mform->setDefault('cmid', 0);
        if (!empty($this->question->id)) {
            if ($this->question->formoptions->cansaveasnew) {
                $mform->addElement('header', 'additemhdr',
                        get_string('converttocalculated', 'qtype_calculatedsimple'));
                $mform->closeHeaderBefore('additemhdr');

                $mform->addElement('checkbox', 'convert', '',
                        get_string('willconverttocalculated', 'qtype_calculatedsimple'));
                $mform->setDefault('convert', 0);

            }
        }
    }

    public function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question);
        $question = $this->data_preprocessing_hints($question);
        $question = $this->data_preprocessing_units($question);
        $question = $this->data_preprocessing_unit_options($question);

        // This is a bit ugly, but it loads all the dataset values.
        $question = (object)((array)$question + $this->formdata);

        return $question;
    }

    public function qtype() {
        return 'calculatedsimple';
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (empty($data['definition'])) {
            $errors['selectadd'] = get_string('youmustaddatleastonevalue', 'qtype_calculatedsimple');
        }

        return $errors;
    }
}
