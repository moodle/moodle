<?php  // $Id$

///////////////////////////////////////////////////////////////
/// ABSTRACT SUPERCLASS FOR QUSTION TYPES THAT USE DATASETS ///
///////////////////////////////////////////////////////////////

require_once($CFG->dirroot . '/files/mimetypes.php');

define("LITERAL", "1");
define("FILE", "2");
define("LINK", "3");

class quiz_dataset_dependent_questiontype extends quiz_default_questiontype {

    var $virtualqtype= false;

    // Contains picked dataset numbers by category. The idea is to
    // reuse dataset number for each category within a quiz.
    var $datasetnumbers = array();

    function name() {
        return 'datasetdependent';
    }

    function uses_quizfile($question, $relativefilepath) {
        // Check whether the specified file is available by any
        // dataset item on this question...
        global $CFG;
        if (get_record_sql(" SELECT *
                 FROM {$CFG->prefix}quiz_dataset_items i,
                      {$CFG->prefix}quiz_dataset_definitions d,
                      {$CFG->prefix}quiz_question_datasets q
                WHERE i.value = '$relativefilepath'
                  AND d.id = i.definition AND d.type = 2
                  AND d.id = q.datasetdefinition
                  AND q.question = $question->id ")) {

            return true;
        } else {
            // Make the check of the parent:
            return parent::uses_quizfile($question, $relativefilepath);
        }
    }
    
    function create_virtual_qtype() {
        error("No vitrual question type for question type ".$this->name());
    }

    function get_virtual_qtype() {
        if (!$this->virtualqtype) {
            $this->virtualqtype = $this->create_virtual_qtype();
        }
        return $this->virtualqtype;
    }

    function comment_header($question) {
    // Used by datasetitems.php
        // Default returns nothing and thus takes away the column
        return '';
    }

    function comment_on_datasetitems($question, $data, $number) {
    // Used by datasetitems.php
        // Default returns nothing
        return '';
    }

    function supports_dataset_item_generation() {
    // Used by datasetitems.php
        // Default does not support any item generation
        return false;
    }

    function custom_generator_tools($datasetdef) {
    // Used by datasetitems.php
        // If there is no generation support,
        // there cannot possibly be any custom tools either
        return '';
    }

    function generate_dataset_item($options) {
    // Used by datasetitems.php
        // By default nothing is generated
        return '';
    }

    function update_dataset_options($datasetdefs, $form) {
    // Used by datasetitems.php
    // Returns the updated datasets
        // By default the dataset options cannot be updated
        return $datasetdefs;
    }

    function dataset_options($question, $name) {
        
        // First options - it is not a dataset...
        $options['0'] = get_string('nodataset', 'quiz');
        
        // Construct question local options
        global $CFG;
        $currentdatasetdef = get_record_sql(
                "SELECT a.*
                   FROM {$CFG->prefix}quiz_dataset_definitions a,
                        {$CFG->prefix}quiz_question_datasets b
                  WHERE a.id = b.datasetdefinition
                    AND b.question = '$question->id'
                    AND a.name = '$name'")
        or $currentdatasetdef->type = '0';        
        foreach (array( LITERAL, FILE, LINK) as $type) {
            $key = "$type-0-$name";
            if ($currentdatasetdef->type == $type
                    and $currentdatasetdef->category == 0) {
                $options[$key] = get_string("keptlocal$type", 'quiz');
            } else {
                $options[$key] = get_string("newlocal$type", 'quiz');
            }
        }
        
        // Construct question category options
        $categorydatasetdefs = get_records_sql(
                "SELECT a.type, a.id
                   FROM {$CFG->prefix}quiz_dataset_definitions a,
                        {$CFG->prefix}quiz_question_datasets b
                  WHERE a.id = b.datasetdefinition
                    AND a.category = '$question->category'
                    AND a.name = '$name'");
        foreach(array( LITERAL, FILE, LINK) as $type) {
            $key = "$type-$question->category-$name";
            if (isset($categorydatasetdefs[$type])
                    and $categorydef = $categorydatasetdefs[$type]) {
                if ($currentdatasetdef->type == $type
                        and $currentdatasetdef->id == $categorydef->id) {
                    $options[$key] = get_string("keptcategory$type", 'quiz');
                } else {
                    $options[$key] = get_string("existingcategory$type", 'quiz');
                }
            } else {
                $options[$key] = get_string("newcategory$type", 'quiz');
            }
        }

        // All done!
        return array($options, $currentdatasetdef->type
                ? "$currentdatasetdef->type-$currentdatasetdef->category-$name"
                : '');
    }

    function save_question_options($question, $options) {
        // Default does nothing...
        return true;
    }
    
    function save_question($question, $form, $course, $subtypeoptions=false) {
    // For dataset dependent questions a wizard is used for editing
    // questions. Therefore calls from question.php are ignored.
    // Instead questions are saved when this method is called by
    // editquestion.php
    
        if ($subtypeoptions) {
            // Let's save the question
            // We need to save the question first (in order to get the id)
            // We then save the dataset definitions and finally we
            // save the subtype options...
            
            // Save question
            if (!empty($question->id)) { // Question already exists
                $question->version++;  // Update version number of question
                if (!update_record("quiz_questions", $question)) {
                    error("Could not update question!");
                }
            } else {   // Question is a new one
                // Set the unique code (not to be changed)
                $question->stamp = make_unique_id_code();
                $question->version = 1;
                if (!$question->id=insert_record("quiz_questions", $question)) {
                    error("Could not insert new question!");
                }
            }
            
            // Save datasets
            global $CFG;
            $datasetdefinitions = get_records_sql( // Indexed by name...
                "SELECT a.name, a.id, a.type, a.category
                  FROM {$CFG->prefix}quiz_dataset_definitions a,
                       {$CFG->prefix}quiz_question_datasets b
                  WHERE a.id = b.datasetdefinition
                    AND b.question = $question->id");

            foreach ($form->dataset as $dataset) {
                if (!$dataset) {
                    continue; // The no dataset case...
                }
                
                list($type, $category, $name) = explode('-', $dataset, 3);

                if (isset($datasetdefinitions[$name])
                        and $datasetdefinitions[$name]->type == $type
                        and $datasetdefinitions[$name]->category == $category) {
                    // Keep this dataset as it already fulfills our dreams
                    // by preventing it from being deleted
                    unset($datasetdefinitions[$name]);
                    continue;
                }

                // We need to create a new datasetdefinition
                unset ($datasetdef);
                $datasetdef->type = $type;
                $datasetdef->name = $name;
                $datasetdef->category = $category;

                if (!$datasetdef->id = insert_record(
                        'quiz_dataset_definitions', $datasetdef)) {
                    error("Unable to create dataset $name");
                }

                if ($category) {
                    // We need to look for already existing 
                    // datasets in the category.
                    // By first creating the datasetdefinition above we
                    // can manage to automatically take care of
                    // some possible realtime concurrence
                    while ($olderdatasetdef = get_record_select(
                            'quiz_dataset_definitions',
                            " type = '$type' AND name = '$name'
                            AND category = '$category'
                            AND id < $datasetdef->id ")) {
                        // Use older dataset instead:
                        delete_records('quiz_dataset_definitions',
                                       'id', $datasetdef->id);
                        $datasetdef = $olderdatasetdef;
                    }
                }
                
                // Create relation to this dataset:
                unset($questiondataset);
                $questiondataset->question = $question->id;
                $questiondataset->datasetdefinition = $datasetdef->id;
                if (!insert_record('quiz_question_datasets',
                                   $questiondataset)) {
                    error("Unable to create relation to dataset $name");
                }
            }

            // Remove local obsolete datasets as well as relations
            // to datasets in other categories:
            if (!empty($datasetdefinitions)) {
                foreach ($datasetdefinitions as $def) {
                    delete_records('quiz_question_datasets',
                                   'question', $question->id,
                                   'datasetdefinition', $def->id);

                    if ($def->category == 0) { // Question local dataset
                        delete_records('quiz_dataset_definitions', 'id', $def->id);
                        delete_records('quiz_dataset_items',
                                       'definition', $def->id);
                    }
                }
            }
            
            // Save subtype options
            $this->save_question_options($question, $subtypeoptions);
            return $question;
            
        } else if (empty($form->editdatasets)) {
            // Parse for common question entries and
            // continue with editquestion.php by returning the question
            $question->name               = $form->name;
            $question->questiontext       = $form->questiontext;
            $question->questiontextformat = $form->questiontextformat;
            if (empty($form->image)) {
                $question->image = "";
            } else {
                $question->image = $form->image;
            }
            if (isset($form->defaultgrade)) {
                $question->defaultgrade = $form->defaultgrade;
            }
            return $question;
        } else {
            return $question;
        }
    }

    function find_dataset_names($text) {
    /// Returns the possible dataset names found in the text as an array
    /// The array has the dataset name for both key and value
        $datasetnames = array();
        while (ereg('\\{([[:alpha:]][^>} <{"\']*)\\}', $text, $regs)) {
            $datasetnames[$regs[1]] = $regs[1];
            $text = str_replace($regs[0], '', $text);
        }
        return $datasetnames;
    }

    function convert_to_response_answer_field($questionresponse) {
        // It does not look like all platforms support the ksort strategi
        // so gotta try something else...
        foreach ($questionresponse as $key => $response) {
            if (!isset($shortestkey)
                    || strlen($shortestkey) > strlen($key)) {
                $shortestkey = $key;
            }
        }
        $dataset = $questionresponse[$shortestkey];
        unset($questionresponse[$shortestkey]);
        $virtualqtype = $this->get_virtual_qtype();
        return "dataset$dataset-" . $virtualqtype
                ->convert_to_response_answer_field($questionresponse);
    }

    function create_response($question, $nameprefix, $questionsinuse) {
    /// This method must pick a dataset and have its number and
    /// data injected in the response keys


        // First we retrieve the dataset definitions for this questions
        // and check how many datasets we have available ($maxnumber)
        global $CFG;
        $datasetdefinitions = get_records_sql(
            "SELECT a.* FROM {$CFG->prefix}quiz_dataset_definitions a,
                             {$CFG->prefix}quiz_question_datasets b
             WHERE a.id = b.datasetdefinition
               AND b.question = $question->id");
        $definitionids = $delimiter = '';
        foreach ($datasetdefinitions as $datasetdef) {
            $definitionids .= $delimiter.$datasetdef->id;
            $delimiter = ',';
            if (!isset($maxnumber) || $datasetdef->itemcount < $maxnumber) {
                $maxnumber = $datasetdef->itemcount;
            }
        }
        
        // We then pick dataset number and retrieve the datasetitems
        if (!isset($maxnumber) || 0 == $maxnumber) {
            notify("Error: Question $question->id does not
                    have items for its datasets");
            $datasetinput = 0;
            $datasetitems = array();
            
        } else {
            isset($this->datasetnumbers[$question->category])
            and   $this->datasetnumbers[$question->category] <= $maxnumber
            or    $this->datasetnumbers[$question->category] =
                    quiz_qtype_dataset_pick_new($question->category,
                                                $maxnumber);
            $datasetinput = $this->datasetnumbers[$question->category];
            $datasetitems = get_records_select('quiz_dataset_items',
                    "definition in ($definitionids)
                    AND number = $datasetinput");
        }
        
        // Build the rest of $datasetinput
        foreach ($datasetitems as $item) {
            $datasetdef = $datasetdefinitions[$item->definition];
            
            // We here need to pay attention to whether the
            // data item is a link or an ordinary literal
            if ($datasetdef->type == LITERAL) {
                // The ordinary simple case
                $value = $item->value;

            } else {
                $icon = "<img src=\"$CFG->wwwroot/pix/f/"
                        . mimeinfo('icon', $item->value)
                        . '" height="16" width="16" border="0" alt="File" />';
                if (substr(strtolower($item->value), 0, 7)=='http://') {
                    $link = $item->value;
                        
                } else {
                    global $quiz; // Try to reach this info globally
                    if ($CFG->slasharguments) {
                        // Use this method if possible for better caching
                        $link = "quizfile.php/$quiz->id/$question->id/$item->value";

                    } else {
                        $link = "quizfile.php?file=/$quiz->id/$question->id/$item->value";
                    }
                }

                if ($datasetdef->type == FILE
                        and ereg('/([^/]+)$', $item->value, $regs)) {
                    $linktext = $regs[1];
                } else {
                    $linktext = $item->value;
                }
                $value = '<a target="_blank" href="' . $link
                        . "\" title=\"$datasetdef->name\">$icon$linktext</a>";
            }

            $datasetinput .= ';' . base64_encode($datasetdef->name)
                          . ':' . base64_encode($value);
        }

        // Use the virtual question type and have it ->create_response:
        $virtualqtype = $this->get_virtual_qtype();
        $response = $virtualqtype->create_response($question,
                $this->create_virtual_nameprefix($nameprefix, $datasetinput),
                $questionsinuse);
        $response[$nameprefix] = $datasetinput;
        return $response;
    }

    function create_virtual_nameprefix($nameprefix, $datasetinput) {
    // This default implementation is sometimes overridden
        if (!ereg('([0-9]+)' . $this->name() . '$', $nameprefix, $regs)) {
            error("Wrongly formatted nameprefix $nameprefix");
        }
        $virtualqtype = $this->get_virtual_qtype();
        return $nameprefix . $regs[1] . $virtualqtype->name();
    }

    function extract_response($rawresponse, $nameprefix) {
        if (!ereg('^dataset([;:0-9A-Za-z+/=]+)-(.*)$',
                $rawresponse->answer, $regs)) {
            error ("Wrongly formatted raw response answer $rawresponse->answer");
        }
        
        // Truncate raw response to fit the virtual qtype
        $rawresponse->answer = $regs[2];
                
        $virtualqtype = $this->get_virtual_qtype();
        $response = $virtualqtype->extract_response($rawresponse,
                $this->create_virtual_nameprefix($nameprefix, $regs[1]));
        $response[$nameprefix] = $regs[1];
        return $response;
    }
    
    function print_question_formulation_and_controls($question,
            $quiz, $readonly, $answers, $correctanswers, $nameprefix) {
        
        // Replace wild-cards with dataset items
        $datasetinput = $question->response[$nameprefix];
        list($datasetnumber, $data) =
                $this->parse_datasetinput($datasetinput);
        foreach ($data as $name => $value) {
            $question->questiontext = str_replace
                    ('{'.$name.'}', $value, $question->questiontext);
        }

        // Print hidden field with dataset info
        echo '<input type="hidden" name="' . $nameprefix
                . '" value="' . $datasetinput . '" />';

        // Forward to the virtual qtype
        unset($question->response[$nameprefix]);
        $virtualqtype = $this->get_virtual_qtype();
        $virtualqtype->print_question_formulation_and_controls(
                $question, $quiz, $readonly, $answers, $correctanswers,
                $this->create_virtual_nameprefix($nameprefix, $datasetinput));
    }

    function parse_datasetinput($datasetinput) {
    /// Returns an array consisting of three pieces of information
    /// In [0] there is the dataset number
    /// In [1] there is an array where the data items are mapped by name
    
    /// The dataset related part of the response key
    /// follows right after the response key and end with :
    /// In order to avoid any conflict that can occur whenever anyone
    /// wish to use : in the data, the data dependent part
    /// has been converted to base64 in two steps

        $rawdata = split('[:;]', $datasetinput);
        $rawlength = count($rawdata);
        $i = 0;
        $data = array();
        while (++$i < $rawlength) {
            $data[base64_decode($rawdata[$i])] =
                    base64_decode($rawdata[++$i]);
        }
        return array($rawdata[0], $data);
    }
}
//// END OF CLASS ////

function quiz_qtype_dataset_pick_new($category, $maxnumber) {
//// Function used by ->create_response
//// It takes care of picking a new datasetnumber for
//// the user in the specified question category
    
    // We need to know whether the attempt builds on the last or
    // not. It can not be determined by the function args to
    // ->create_response.
    // Instead of adding that argument to all implementations of
    // create_response we try to reach $quiz globally. That
    // should work because this function is only used by
    // attempt.php when starting an attempt.
    global $quiz;  //// PATTERN VIOLATION ////
    if ($quiz->attemptonlast) {

        // Dataset numbers for attemptonlast quizes are stored
        // in quiz_attemptonlast_datasets
        global $USER;
        if (!($attemptonlastdataset = get_record(
                'quiz_attemptonlast_datasets',
                'userid', $USER->id, 'category', $category))
                or $attemptonlastdataset->datasetnumber > $maxnumber) {

            // No suitable $attemptonlastdataset

            if ($attemptonlastdataset) {
                // Remove the unsuitable:
                delete_records('quiz_attemptonlast_datasets',
                               'id', $attemptonlastdataset->id);
                unset($attemptonlastdataset->id);
                unset($attemptonlastdataset->datasetnumber);
                
            } else {
                $attemptonlastdataset->userid = $USER->id;
                $attemptonlastdataset->category = $category;
            }

            // Create without setting datasetnumber
            // so that this user gets its id
            $attemptonlastdataset->id = insert_record(
                    'quiz_attemptonlast_datasets', $attemptonlastdataset);
            
            // Pick the datasetnumber in a thread safe way
            // so that this can be done without having
            // concurrent users get the same datasetnumber!
            // The chosen pattern relies on
            // synchronization for autoincrement on the id
            // when the previous insert_record statement
            // was executed.
            if (!($latestdatasetpick = get_record_select(
                    'quiz_attemptonlast_datasets',
                    "category = $category AND datasetnumber > 0
                    AND id < $attemptonlastdataset->id",
                    ' max(id) id '))) {
                // Smells like the current user is first:
                $latestdatasetpick->id = 0;
            }
            $latestattemptonlasts = get_records_select(
                    'quiz_attemptonlast_datasets',
                    "$latestdatasetpick->id <= id
                    AND id < $attemptonlastdataset->id
                    AND category = $category");
            $attemptonlastdataset->datasetnumber =
                    (count($latestattemptonlasts)
                    + (isset($latestattemptonlasts[$latestdatasetpick->id])
                        ? $latestattemptonlasts[$latestdatasetpick->id]
                                ->datasetnumber - 1
                        : 0))
                    % $maxnumber + 1;
            if (!update_record('quiz_attemptonlast_datasets',
                               $attemptonlastdataset)) {
                notify("Error unable to save the picked datasetnumber in
                        quiz_attemptonlast_datasets for user $USER-id");
            }
        }
        return $attemptonlastdataset->datasetnumber;
        
    } else {
        // When it is not an attemptonlast
        // we pick the dataset number randomly
        return  rand ( 1 , $maxnumber );
    }
}

?>
