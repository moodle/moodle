<?php // $Id$

// Get a handle to the question type we are dealing with here
$qtypeobj = $QUIZ_QTYPES[CALCULATED];
if (isset($form->editdatasets) && $form->editdatasets) {
    require("$CFG->dirroot/mod/quiz/questiontypes/datasetdependent/datasetitems.php");
    exit();
}

$calculatedmessages = array();
if ($form) {

    // Verify the quality of the question properties
    if (empty($question->name)) {
        $calculatedmessages[] = get_string('missingname', 'quiz');
    }
    if (empty($question->questiontext)) {
        $calculatedmessages[] = get_string('missingquestiontext', 'quiz');
    }

    // Formula stuff (verify some of them)
    $answers[0]->answer = trim(array_shift($form->answer))
    and false===($formulaerrors =
            quiz_qtype_calculated_find_formula_errors($answers[0]->answer))
    or $answers[0]->answer
    and $calculatedmessages[] = $formulaerrors
    or $calculatedmesages[] = get_string('missingformula', 'quiz');
    
    $answers[0]->tolerance = array_shift($form->tolerance)
    or $answers[0]->tolerance = 0.0;
    is_numeric($answers[0]->tolerance)
    or $calculatedmessages[] = get_string('tolerancemustbenumeric', 'quiz');

    $answers[0]->feedback = array_shift($form->feedback);
    
    // Let's trust the drop down menus.

    $answers[0]->tolerancetype = array_shift($form->tolerancetype);
    $answers[0]->correctanswerlength = array_shift($form->correctanswerlength);
    $answers[0]->fraction = array_shift($form->fraction);

    // Fill with remaining answers, in case calculated.html
    // supports multiple formulas.
    $i = 1;
    foreach ($form->answer as $key => $answer) {
        if (trim($answer)) {
            $answers[$i]->answer = trim($answer);
            $answers[$i]->tolerance = $form->tolerance[$key]
            or $answers[$i]->tolerance = 0.0;
            $answers[$i]->tolerancetype = $form->tolerancetype[$key];
            $answers[$i]->correctanswerlength =
                    $form->correctanswerlength[$key];

            $answers[$i]->fraction = $form->fraction[$key];
            $answers[$i]->feedback = $form->feedback[$key];

            // Check for errors:
            false === ($formulaerrors =
                    quiz_qtype_calculated_find_formula_errors($answer))
            or $calculatedmessages[] = $formulaerrors;
            is_numeric($answers[$i]->tolerance)
            or $calculatedmessages[] = get_string('tolerancemustbenumeric',
                                                  'quiz');
            // Increase answer count
            ++$i;
        }
    }

    // Finally the units:

    // Start with the default units...
    $units[0]->unit = array_shift($form->unit);
    array_shift($form->multiplier); // In case it is not 1.0
    $units[0]->multiplier = 1.0; // Must!
    
    // Accept other units if they have legal multipliers
    $i = 1;
    foreach ($form->multiplier as $key => $multiplier) {
        if ($multiplier && is_numeric($multiplier)) {
            $units[$i]->multiplier = $multiplier;
            $units[$i]->unit = $form->unit[$key];
            ++$i;
        }
    }


    if (empty($calculatedmessages)) {
    // First page calculated.html passed all right!

        if (!empty($form->dataset)) {
            // Dataset definitions have been set
            // Save question!
            $subtypeoptions->answers = $answers;
            $subtypeoptions->units = $units;
            $question = $qtypeobj->save_question
                    ($question, $form, $course, $subtypeoptions);
            require("$CFG->dirroot/mod/quiz/questiontypes/datasetdependent/datasetitems.php");
            exit();
        } else {
            $datasetmessage = '';
        }
        
        // Now continue by preparing for the second page questiondatasets.html
        
        $possibledatasets = $qtypeobj->find_dataset_names(
                $question->questiontext);
    
        $mandatorydatasets = array();
        foreach ($answers as $answer) {
            $mandatorydatasets += $qtypeobj
                    ->find_dataset_names($answer->answer);
        }
        
        $datasets = $qtypeobj->construct_dataset_menus(
                $question, $mandatorydatasets, $possibledatasets);
        print_heading_with_help(get_string("choosedatasetproperties", "quiz"), "questiondatasets", "quiz");
        require("$CFG->dirroot/mod/quiz/questiontypes/datasetdependent/questiondatasets.html");
        exit();
    }
    
} else {
// First page in question wizard - calculated.html!

    // The layout of the editing page will only support
    // one formula alternative for calculated questions.
    // However, the code behind supports up to six formulas
    // and the database store and attempt/review framework 
    // does not have any limit.
    if (!empty($question->id)) {
        $answersraw= $qtypeobj->get_answers($question);
    }
    $answers= array();
    for ($i=0; $i<6; $i++) {
        // Make answer slots with default values
        $answers[$i]->answer              = "";
        $answers[$i]->feedback            = "";
        $answers[$i]->fraction            = "1.0";
        $answers[$i]->tolerance           = "0.01";
        $answers[$i]->tolerancetype       = "1";
        $answers[$i]->correctanswerlength = "2";
    }
    if (!empty($answersraw)) {
        $i=0;
        foreach ($answersraw as $answer) {
            $answers[$i] = $answer;
            $i++;
        }
    }

    // Units are handled the same way
    // as for numerical questions
    $units = array();
    for ($i=0 ; $i<6 ; $i++) {
        // Make unit slots, default as blank...
        $units[$i]->multiplier = '';
        $units[$i]->unit = '';
    }
    if (!empty($question->id) and $unitsraw = get_records(
            'quiz_numerical_units', 'question', $question->id)) {
        /// Find default unit and have it put in the zero slot
        /// This procedure might be overridden later when
        /// the unit is stripped form an answer...
        foreach ($unitsraw as $key => $unit) {
            if (1.0 == $unit->multiplier) {
                /// Default unit found:
                $units[0] = $unit;
                unset($unitsraw[$key]);
                break;
            }
        }
        /// Fill remaining answer slots with whatsever left
        if (!empty($unitsraw)) {
            $i = 1; // The zero slot got the default unit...
            foreach ($unitsraw as $unit) {
                $units[$i] = $unit;
                $i++;
            }
        }
    } else {
        $units[0]->multiplier = 1.0;
    }

    // Strip trailing zeros from multipliers
    foreach ($units as $i => $unit) {
        if (ereg('^(.*\\..(.*[^0])?)0+$', $unit->multiplier, $regs1)) {
            if (ereg('^(.+)\\.0$', $regs1[1], $regs2)) {
                $units[$i]->multiplier = $regs2[1];
            } else {
                $units[$i]->multiplier = $regs1[1];
            }
        }
    }
}

print_heading_with_help(get_string("editingcalculated", "quiz"), "calculated", "quiz");
require("calculated.html");

?>
