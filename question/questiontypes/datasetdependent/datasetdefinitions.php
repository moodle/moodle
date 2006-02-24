<?php
// Now continue by preparing for the second page
// in the question wizard: "questiondatasets.html"

// Determine possible and mandatory datasets...
$possibledatasets = $this->find_dataset_names($form->questiontext);
$mandatorydatasets = array();
foreach ($form->answers as $answer) {
    $mandatorydatasets += $this
            ->find_dataset_names($answer);
}
$datasets = $this->construct_dataset_menus(
        $form, $mandatorydatasets, $possibledatasets);

// Print the page
print_heading_with_help(get_string("choosedatasetproperties", "quiz"), "questiondatasets", "quiz");
require("$CFG->dirroot/mod/quiz/questiontypes/datasetdependent/questiondatasets.html");
?>