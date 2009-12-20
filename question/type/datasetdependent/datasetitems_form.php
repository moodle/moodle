<?php  // $Id$
class question_dataset_dependent_items_form extends moodleform {
    /**
     * Question object with options and answers already loaded by get_question_options
     * Be careful how you use this it is needed sometimes to set up the structure of the
     * form in definition_inner but data is always loaded into the form with set_defaults.
     *
     * @var object
     */
    var $question;
    /**
     * Reference to question type object
     *
     * @var question_dataset_dependent_questiontype
     */
    var $qtypeobj;

    var $datasetdefs;

    var $maxnumber = -1;

    var $regenerate;

    var $noofitems;
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    function question_dataset_dependent_items_form($submiturl, $question, $regenerate){
        global $QTYPES, $SESSION, $CFG;
        $this->regenerate = $regenerate;
        $this->question = $question;
        $this->qtypeobj =& $QTYPES[$this->question->qtype];
				// Validate the question category.
				if (!$category = get_record('question_categories', 'id', $question->category)) {
				    print_error('categorydoesnotexist', 'question', $returnurl);
				}
        $this->category = $category;
        $this->categorycontext = get_context_instance_by_id($category->contextid);
        //get the dataset defintions for this question
        if (empty($question->id)) {
            $this->datasetdefs = $this->qtypeobj->get_dataset_definitions($question->id, $SESSION->datasetdependent->definitionform->dataset);
        } else {
            if (empty($question->options)) {
                $this->get_question_options($question);
            }
            $this->datasetdefs = $this->qtypeobj->get_dataset_definitions($question->id, array());
        }

        foreach ($this->datasetdefs as $datasetdef) {

            // Get maxnumber
            if ($this->maxnumber == -1 || $datasetdef->itemcount < $this->maxnumber) {
                $this->maxnumber = $datasetdef->itemcount;
            }
        }
        foreach ($this->datasetdefs as $defid => $datasetdef) {
            if (isset($datasetdef->id)) {
                $this->datasetdefs[$defid]->items = get_records_sql( // Use number as key!!
                        " SELECT itemnumber, definition, id, value
                          FROM {$CFG->prefix}question_dataset_items
                          WHERE definition = $datasetdef->id ");
            }
        }
        parent::moodleform($submiturl);
    }
    function definition() {
        $mform =& $this->_form;
        $strquestionlabel = $this->qtypeobj->comment_header($this->question);
        if ($this->maxnumber != -1){
            $this->noofitems = $this->maxnumber;
        } else {
            $this->noofitems = 0;
        }
//------------------------------------------------------------------------------------------------------------------------------
        $mform->addElement('submit', 'updatedatasets', get_string('updatedatasetparam', 'qtype_datasetdependent'));
        $mform->addElement('header', 'additemhdr', get_string('itemtoadd', 'qtype_datasetdependent'));
        $idx = 1;
        $j = (($this->noofitems) * count($this->datasetdefs))+1;
        foreach ($this->datasetdefs as $defkey => $datasetdef){
            $mform->addElement('text', "number[$j]", get_string('param', 'qtype_datasetdependent', $datasetdef->name));
            $mform->setType("number[$j]", PARAM_NUMBER);
            $this->qtypeobj->custom_generator_tools_part($mform, $idx, $j);
            $idx++;
            $mform->addElement('hidden', "definition[$j]");
            $mform->setType("definition[$j]", PARAM_RAW);
            $mform->addElement('hidden', "itemid[$j]");
            $mform->setType("itemid[$j]", PARAM_RAW);
            $mform->addElement('static', "divider[$j]", '', '<hr />');
            $j++;
        }

        if ('' != $strquestionlabel){
            $mform->addElement('static', 'answercomment['.($this->noofitems+1).']', $strquestionlabel);
        }
        $addremoveoptions = Array();
        $addremoveoptions['1']='1';
        for ($i=10; $i<=100 ; $i+=10){
             $addremoveoptions["$i"]="$i";
        }
                    $mform->addElement('header', 'additemhdr', get_string('add', 'moodle'));
        $mform->closeHeaderBefore('additemhdr');

        if ($this->qtypeobj->supports_dataset_item_generation()){
            $radiogrp = array();
            $radiogrp[] =& $mform->createElement('radio', 'nextpageparam[forceregeneration]', null, get_string('reuseifpossible', 'qtype_datasetdependent'), 0);
            $radiogrp[] =& $mform->createElement('radio', 'nextpageparam[forceregeneration]', null, get_string('forceregeneration', 'qtype_datasetdependent'), 1);
            $mform->addGroup($radiogrp, 'forceregenerationgrp', get_string('nextitemtoadd', 'qtype_calculated'), "<br/>", false);
        }

        $mform->addElement('submit', 'getnextbutton', get_string('getnextnow', 'qtype_datasetdependent'));
        $mform->addElement('static', "dividera", '', '<hr />');
        $addgrp = array();
        $addgrp[] =& $mform->createElement('submit', 'addbutton', get_string('add', 'moodle'));
        $addgrp[] =& $mform->createElement('select', "selectadd", get_string('additem', 'qtype_datasetdependent'), $addremoveoptions);
        $addgrp[] = & $mform->createElement('static',"stat","Items",get_string('item(s)', 'qtype_datasetdependent'));
        $mform->addGroup($addgrp, 'addgrp', '', '   ', false);
         $mform->addElement('static', "divideradd", '', '');
    //     $mform->closeHeaderBefore('divideradd');
        if ($this->noofitems > 0) {
            $mform->addElement('header', 'additemhdr', get_string('delete', 'moodle'));
            $deletegrp = array();
            $deletegrp[] =& $mform->createElement('submit', 'deletebutton', get_string('delete', 'moodle'));
            $deletegrp[] =& $mform->createElement('select', "selectdelete", get_string('deleteitem', 'qtype_datasetdependent')."1", $addremoveoptions);
            $deletegrp[] = & $mform->createElement('static',"stat","Items",get_string('lastitem(s)', 'qtype_datasetdependent'));
            $mform->addGroup($deletegrp, 'deletegrp', '', '   ', false);
   //      $mform->addElement('static', "dividerdelete", '', '<hr />');
   //      $mform->closeHeaderBefore('dividerdelete');
        } else {
            $mform->addElement('static','warning','','<span class="error">'.get_string('youmustaddatleastoneitem', 'qtype_datasetdependent').'</span>');
        }

//------------------------------------------------------------------------------------------------------------------------------
        $j = $this->noofitems * count($this->datasetdefs);
        for ($i = $this->noofitems; $i >= 1 ; $i--){
            $mform->addElement('header', '', get_string('itemno', 'qtype_datasetdependent', $i));
            foreach ($this->datasetdefs as $defkey => $datasetdef){
                $mform->addElement('text', "number[$j]", get_string('param', 'qtype_datasetdependent', $datasetdef->name));
                $mform->setType("number[$j]", PARAM_NUMBER);
                $mform->addElement('hidden', "itemid[$j]");
                $mform->setType("itemid[$j]", PARAM_INT);

                $mform->addElement('hidden', "definition[$j]");
                $mform->setType("definition[$j]", PARAM_NOTAGS);

                $j--;
            }
            if ('' != $strquestionlabel){
                $repeated[] =& $mform->addElement('static', "answercomment[$i]", $strquestionlabel);
            }
        }


//------------------------------------------------------------------------------------------------------------------------------
        //non standard name for button element needed so not using add_action_buttons
        $mform->addElement('submit', 'backtoquiz', get_string('savechanges'));
        $mform->closeHeaderBefore('backtoquiz');

        //hidden elements
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', 0);

        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);
        $mform->setDefault('cmid', 0);

        $mform->addElement('hidden', 'category');
        $mform->setType('category', PARAM_RAW);
        $mform->setDefault('category', array('contexts' => array($this->categorycontext)));

        $mform->addElement('hidden', 'wizard', 'datasetitems');
        $mform->setType('wizard', PARAM_ALPHA);
        
        $mform->addElement('hidden', 'returnurl');
        $mform->setType('returnurl', PARAM_LOCALURL);
        $mform->setDefault('returnurl', 0);
    }

    function set_data($question){
        $formdata = array();

        //fill out all data sets and also the fields for the next item to add.
        $j = $this->noofitems * count($this->datasetdefs);
        for ($itemnumber = $this->noofitems; $itemnumber >= 1; $itemnumber--){
            $data = array();
            foreach ($this->datasetdefs as $defid => $datasetdef){
                if (isset($datasetdef->items[$itemnumber])){
                    $formdata["number[$j]"] = $datasetdef->items[$itemnumber]->value;
                    $formdata["definition[$j]"] = $defid;
                    $formdata["itemid[$j]"] = $datasetdef->items[$itemnumber]->id;
                    $data[$datasetdef->name] = $datasetdef->items[$itemnumber]->value;
                }
                $j--;
            }
            $formdata['answercomment['.$itemnumber.']'] = $this->qtypeobj->comment_on_datasetitems($this->question, $data, $itemnumber);
        }

        $formdata['nextpageparam[forceregeneration]'] = $this->regenerate;
        $formdata['selectdelete'] = '1';
        $formdata['selectadd'] = '1';
        $j = $this->noofitems * count($this->datasetdefs)+1;
        $data = array(); // data for comment_on_datasetitems later
        //dataset generation dafaults
        if ($this->qtypeobj->supports_dataset_item_generation()) {
            $itemnumber = $this->noofitems+1;
            foreach ($this->datasetdefs as $defid => $datasetdef){
                $formdata["number[$j]"] = $this->qtypeobj->generate_dataset_item($datasetdef->options);
                $formdata["definition[$j]"] = $defid;
                $formdata["itemid[$j]"] =
                        isset($datasetdef->items[$itemnumber])?$datasetdef->items[$itemnumber]->id:0;
                $data[$datasetdef->name] = $formdata["number[$j]"];
                $j++;
            }
        }

        //existing records override generated data depending on radio element
        $j = $this->noofitems * count($this->datasetdefs)+1;
        if (!$this->regenerate){
            $idx = 1;
            $itemnumber = $this->noofitems+1;
            foreach ($this->datasetdefs as $defid => $datasetdef){
                if (isset($datasetdef->items[$itemnumber])){
                    $formdata["number[$j]"] = $datasetdef->items[$itemnumber]->value;
                    $formdata["definition[$j]"] = $defid;
                    $formdata["itemid[$j]"] = $datasetdef->items[$itemnumber]->id;
                    $data[$datasetdef->name] = $datasetdef->items[$itemnumber]->value;
                }
                $j++;
            }

        }
        //default answercomment will get ignored if answer element is not in the form.
        $formdata['answercomment['.($this->noofitems+1).']'] = $this->qtypeobj->comment_on_datasetitems($this->question, $data, ($this->noofitems+1));

        $formdata = $this->qtypeobj->custom_generator_set_data($this->datasetdefs, $formdata);

        parent::set_data((object)($formdata + (array)$question));
    }

    function validation($data, $files) {
        $errors = array();
        if (isset($data['backtoquiz']) && ($this->noofitems==0)){
            $errors['warning'] = get_string('warning', 'mnet');
        }
        return $errors;
    }


}
?>