<?php

    //This function backups question_numerical_units from different question types
    function question_backup_numerical_units($bf,$preferences,$question,$level=7) {
        global $CFG, $DB;

        $status = true;

        $numerical_units = $DB->get_records("question_numerical_units", array("question"=>$question), "id");
        //If there are numericals_units
        if ($numerical_units) {
            $status = $status && fwrite ($bf,start_tag("NUMERICAL_UNITS",$level,true));
            //Iterate over each numerical_unit
            foreach ($numerical_units as $numerical_unit) {
                $status = $status && fwrite ($bf,start_tag("NUMERICAL_UNIT",$level+1,true));
                //Print numerical_unit contents
                fwrite ($bf,full_tag("MULTIPLIER",$level+2,false,$numerical_unit->multiplier));
                fwrite ($bf,full_tag("UNIT",$level+2,false,$numerical_unit->unit));
                //Now backup numerical_units
                $status = $status && fwrite ($bf,end_tag("NUMERICAL_UNIT",$level+1,true));
            }
            $status = $status && fwrite ($bf,end_tag("NUMERICAL_UNITS",$level,true));
        }

        return $status;

    }

    //This function backups question_numerical_options from different question types
    function question_backup_numerical_options($bf,$preferences,$question,$level=7) {
        global $CFG, $DB;

        $status = true;
        $numerical_options = $DB->get_records("question_numerical_options",array("questionid" => $question),"id");
        if ($numerical_options) {
            //Iterate over each numerical_option
            foreach ($numerical_options as $numerical_option) {
                $status = $status && fwrite ($bf,start_tag("NUMERICAL_OPTIONS",$level,true));
                //Print numerical_option contents
                fwrite ($bf,full_tag("INSTRUCTIONS",$level+1,false,$numerical_option->instructions));
                fwrite ($bf,full_tag("SHOWUNITS",$level+1,false,$numerical_option->showunits));
                fwrite ($bf,full_tag("UNITSLEFT",$level+1,false,$numerical_option->unitsleft));
                fwrite ($bf,full_tag("UNITGRADINGTYPE",$level+1,false,$numerical_option->unitgradingtype));
                fwrite ($bf,full_tag("UNITPENALTY",$level+1,false,$numerical_option->unitpenalty));
                $status = $status && fwrite ($bf,end_tag("NUMERICAL_OPTIONS",$level,true));
            }
        }

        return $status;

    }

    //This function backups dataset_definitions (via question_datasets) from different question types
    function question_backup_datasets($bf,$preferences,$question,$level=7) {
        global $CFG, $DB;

        $status = true;

        //First, we get the used datasets for this question
        $question_datasets = $DB->get_records("question_datasets", array("question"=>$question), "id");
        //If there are question_datasets
        if ($question_datasets) {
            $status = $status &&fwrite ($bf,start_tag("DATASET_DEFINITIONS",$level,true));
            //Iterate over each question_dataset
            foreach ($question_datasets as $question_dataset) {
                $def = NULL;
                //Get dataset_definition
                if ($def = $DB->get_record("question_dataset_definitions", array("id"=>$question_dataset->datasetdefinition))) {;
                    $status = $status &&fwrite ($bf,start_tag("DATASET_DEFINITION",$level+1,true));
                    //Print question_dataset contents
                    fwrite ($bf,full_tag("CATEGORY",$level+2,false,$def->category));
                    fwrite ($bf,full_tag("NAME",$level+2,false,$def->name));
                    fwrite ($bf,full_tag("TYPE",$level+2,false,$def->type));
                    fwrite ($bf,full_tag("OPTIONS",$level+2,false,$def->options));
                    fwrite ($bf,full_tag("ITEMCOUNT",$level+2,false,$def->itemcount));
                    //Now backup dataset_entries
                    $status = $status && question_backup_dataset_items($bf,$preferences,$def->id,$level+2);
                    //End dataset definition
                    $status = $status &&fwrite ($bf,end_tag("DATASET_DEFINITION",$level+1,true));
                }
            }
            $status = $status &&fwrite ($bf,end_tag("DATASET_DEFINITIONS",$level,true));
        }

        return $status;

    }

    //This function backups datases_items from dataset_definitions
    function question_backup_dataset_items($bf,$preferences,$datasetdefinition,$level=9) {
        global $CFG, $DB;

        $status = true;

        //First, we get the datasets_items for this dataset_definition
        $dataset_items = $DB->get_records("question_dataset_items", array("definition"=>$datasetdefinition), "id");
        //If there are dataset_items
        if ($dataset_items) {
            $status = $status &&fwrite ($bf,start_tag("DATASET_ITEMS",$level,true));
            //Iterate over each dataset_item
            foreach ($dataset_items as $dataset_item) {
                $status = $status &&fwrite ($bf,start_tag("DATASET_ITEM",$level+1,true));
                //Print question_dataset contents
                fwrite ($bf,full_tag("NUMBER",$level+2,false,$dataset_item->itemnumber));
                fwrite ($bf,full_tag("VALUE",$level+2,false,$dataset_item->value));
                //End dataset definition
                $status = $status &&fwrite ($bf,end_tag("DATASET_ITEM",$level+1,true));
            }
            $status = $status &&fwrite ($bf,end_tag("DATASET_ITEMS",$level,true));
        }

        return $status;

    }
