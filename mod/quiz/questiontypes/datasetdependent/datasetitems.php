<?php // $Id$

// Allows a teacher to create, edit and delete datasets

/// Print headings

    $strdatasetnumber = get_string("datasetnumber", "quiz");
    $strnumberinfo = get_string("categoryinfo", "quiz");
    $strquestions = get_string("questions", "quiz");
    $strpublish = get_string("publish", "quiz");
    $strdelete = get_string("remove", "quiz");
    $straction = get_string("action");
    $stradd = get_string("add");
    $strcancel = get_string("cancel");
    $strsavechanges = get_string("savechanges");
    $strbacktoquiz = get_string("backtoquiz", "quiz");

    $streditingquiz = get_string("editingquiz", "quiz");
    $streditdatasets = get_string("editdatasets", "quiz");
    $strreuseifpossible = get_string('reuseifpossible', 'quiz');
    $strforceregeneration = get_string('forceregeneration', 'quiz');

// Get datasetdefinitions:
    $datasetdefs = get_records_sql(
                "SELECT a.* FROM {$CFG->prefix}quiz_dataset_definitions a,
                                 {$CFG->prefix}quiz_question_datasets b
                  WHERE a.id = b.datasetdefinition
                    AND b.question = $question->id");
    if (empty($datasetdefs)) {
        redirect('edit.php');
    }
    foreach($datasetdefs as $datasetdef) {
        if (!isset($maxnumber) || $datasetdef->itemcount < $maxnumber) {
            $maxnumber = $datasetdef->itemcount;
        }        
    }

/// Print heading

    print_heading_with_help($streditdatasets, 'questiondatasets', "quiz");

/// If data submitted, then process and store.
    if ($form = data_submitted()) {
        if (isset($form->addbutton) && $form->addbutton &&
                $maxnumber + 1 == $form->numbertoadd) { // This twisted condition should effectively stop resubmits caused by reloads
            $addeditem->number = $form->numbertoadd;
            foreach ($form->definition as $key => $itemdef) {
                $addeditem->definition = $itemdef;
                $addeditem->value = $form->value[$key];
                if ($form->itemid[$key]) {
                    // Reuse an previously used record
                    $addeditem->id = $form->itemid[$key];
                    if (!update_record('quiz_dataset_items', $addeditem)) {
                        error("Error: Unable to update dataset item");
                    }
                } else {
                    unset($addeditem->id);
                    if (!insert_record('quiz_dataset_items', $addeditem)) {
                        error("Error: Unable to insert dataset item");
                    }
                }
                if ($datasetdefs[$itemdef]->itemcount <= $maxnumber) {
                    $datasetdefs[$itemdef]->itemcount = $maxnumber+1;
                    if (!update_record('quiz_dataset_definitions',
                                       $datasetdefs[$itemdef])) {
                         error("Error: Unable to update itemcount");
                    }
                }
            }
            // else Success:
            $maxnumber = $addeditem->number;

        } else if (isset($form->deletebutton) && $form->deletebutton
                   and $maxnumber == $form->numbertodelete)
        {
            // Simply decrease itemcount where == $maxnumber
            foreach ($datasetdefs as $datasetdef) {
                if ($datasetdef->itemcount == $maxnumber) {
                    $datasetdef->itemcount--;
                    if (!update_record('quiz_dataset_definitions',
                                       $datasetdef)) {
                         error("Error: Unable to update itemcount");
                    }
                }
            }
            --$maxnumber;
        }

        // Handle generator options...
        $olddatasetdefs = $datasetdefs;
        $datasetdefs = $qtypeobj->update_dataset_options($olddatasetdefs, $form);
        foreach ($datasetdefs as $key => $newdef) {
            if ($newdef->options != $olddatasetdefs[$key]->options) {
                // Save the new value for options
                update_record('quiz_dataset_definitions', $newdef);
            }
        }
    }

    make_upload_directory("$course->id");  // Just in case
    $grosscoursefiles = get_directory_list("$CFG->dataroot/$course->id",
                                       "$CFG->moddata");

// Have $coursefiles indexed by file paths:
    $coursefiles = array();
    foreach ($grosscoursefiles as $coursefile) {
        $coursefiles[$coursefile] = $coursefile;
    }


// Get question header if any
    $strquestionheader = $qtypeobj->comment_header($question);

// Get the data set definition and items:
    foreach ($datasetdefs as $key => $datasetdef) {
        $datasetdefs[$key]->items = get_records_sql( // Use number as key!!
                    " SELECT number, definition, id, value
                      FROM {$CFG->prefix}quiz_dataset_items
                      WHERE definition = $datasetdef->id ");
    }

    $table->data = array();
    for ($number = $maxnumber ; $number > 0  ; --$number) {
        $columns = array();
        if ($maxnumber == $number) {
            $columns[] =
                    "<input type=\"hidden\" name=\"numbertodelete\" value=\"$number\"/>
                     <input type=\"submit\" name=\"deletebutton\" value=\"$strdelete\"/>";
        } else {
            $columns[] = '';
        }
        $columns[] = $number;
        foreach ($datasetdefs as $datasetdef) {
            $columns[] =
                    '<input type="hidden" name="itemid[]" value="'. $datasetdef->items[$number]->id .'"/>'
                    . "<input type=\"hidden\" name=\"number[]\" value=\"$number\"/>
                    <input type=\"hidden\" name=\"definition[]\" value=\"$datasetdef->id\"/>"
                    . // Set $data:
                    ($data[$datasetdef->name] = $datasetdef->items[$number]->value) ;

        }
        if ($strquestionheader) {
            $columns[] = $qtypeobj->comment_on_datasetitems($question, $data, $number);
        }
        $table->data[] = $columns;
    }

    $table->head = array($straction, $strdatasetnumber);
    $table->align = array("CENTER", "CENTER");
    $addtable->head = $table->head;
    if ($qtypeobj->supports_dataset_item_generation()) {
        if (isset($form->forceregeneration) && $form->forceregeneration) {
            $force = ' checked="checked" ';
            $reuse = '';
        } else {
            $force = '';
            $reuse = ' checked="checked" ';
        }
        $forceregeneration = '<br/><input type="radio" name="forceregeneration" '
                . $reuse . ' value="0"/>' . $strreuseifpossible
                . '<br/><input type="radio" name="forceregeneration" value="1" '
                . $force . ' />' . $strforceregeneration;
    } else {
        $forceregeneration = '';
    }
    $addline = array('<input type="hidden" name="numbertoadd" value="'
            . ($maxnumber+1)
            . "\"/><input type=\"submit\" name=\"addbutton\" value=\"$stradd\"/>"
            . $forceregeneration
            , $maxnumber+1);
    foreach ($datasetdefs as $datasetdef) {
        if ($datasetdef->name) {
            $table->head[] = $datasetdef->name;
            $addtable->head[] = $datasetdef->name
                    . ($qtypeobj->supports_dataset_item_generation()
                    ?  '<br/>' . $qtypeobj->custom_generator_tools($datasetdef)
                    : '');
            $table->align[] = "CENTER";

            // THE if-statement IS FOR BUT ONE THING
            // - to determine an item value for the input field
            // - this is tried in a number of different way...
            if (isset($form->regenerateddefid) && $form->regenerateddefid) {
                // Regeneration clicked...
                if ($form->regenerateddefid == $datasetdef->id) {
                    //...for this item...
                    $itemvalue = $qtypeobj
                            ->generate_dataset_item($datasetdef->options);
                } else {
                    // ...but not for this, keep unchanged!
                    foreach ($form->definition as $key => $itemdef) {
                        if ($datasetdef->id == $itemdef) {
                            $itemvalue = $form->value[$key];
                            break;
                        }
                    }
                }
            } else if (isset($form->forceregeneration)
                    && $form->forceregeneration) {
                // Can only mean a an "Add operation with forced regeneration:
                $itemvalue = $qtypeobj->generate_dataset_item($datasetdef->options);

            } else if (isset($datasetdef->items[$maxnumber + 1])) {
                // Looks like we do have an old value to use here:
                $itemvalue = $datasetdef->items[$maxnumber + 1]->value;

            } else {
                // We're getting getting desperate -
                // is there any chance to determine a value somehow
                // Let's just try anything now...

                $qtypeobj->supports_dataset_item_generation() and '' !== (
                    // Generation could work if the options are alright:
                    $itemvalue = $qtypeobj->generate_dataset_item($datasetdef->options))

                or ereg('(.*)'.($maxnumber).'(.*)',
                        $datasetdef->items[$maxnumber]->value, $valueregs)
                    // Looks like this trivial generator does it:
                and $itemvalue = $valueregs[1].($maxnumber+1).$valueregs[2]
                
                or // Let's just pick the dataset number, better than nothing:
                    $itemvalue = $maxnumber + 1;
            }

            if (isset($datasetdef->items[$maxnumber + 1]->id)) {
                $itemid = $datasetdef->items[$maxnumber + 1]->id;
            } else {
                $itemid = '';
            }

            $addline[] = 
                    '<input type="hidden" name="itemid[]" value="'.$itemid.'"/>'
                    . "<input type=\"hidden\" name=\"definition[]\" value=\"$datasetdef->id\"/>"
                    . ( 2 != $datasetdef->type
                      ? '<input type="text" size="20" name="value[]" value="'
                            . $itemvalue
                            . '"/>'
                      : choose_from_menu($coursefiles, 'value[]',
                            $itemvalue,
                            '', '', '', true));
            $data[$datasetdef->name] = $itemvalue;
        }
    }
    if ($strquestionheader) {
        $table->head[] = $strquestionheader;
        $addtable->head[] = $strquestionheader;
        $table->align[] = "CENTER";
        $addline[] = $qtypeobj->comment_on_datasetitems($question, $data, $maxnumber + 1);
    }

// Print form for adding one more dataset
    $addtable->align = $table->align;
    $addtable->data = array($addline);
    echo "<form name=\"addform\" method=\"post\" action=\"question.php\">
            <input type=\"hidden\" name=\"regenerateddefid\" value=\"0\"/>
            <input type=\"hidden\" name=\"id\" value=\"$question->id\"/>
            <input type=\"hidden\" name=\"editdatasets\" value=\"1\"/>";
    print_table($addtable);
    echo '</form>';
    
// Print form with current datasets
    if ($table->data) {
        echo "<form method=\"post\" action=\"question.php\">
            <input type=\"hidden\" name=\"id\" value=\"$question->id\"/>
            <input type=\"hidden\" name=\"editdatasets\" value=\"1\"/>";
        print_table($table);
        echo '</form>';
    }

    echo "<center><br><br><form method=\"get\" action=\"edit.php\"><input type=\"hidden\" name=\"question\" value=\"$question->id\"/><input type=\"submit\" name=\"backtoquiz\" value=\"$strbacktoquiz\"></form></center>\n";

    print_footer();

?>
