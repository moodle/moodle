<?php // $Id$

// Get a handle to the question type we are dealing with here
$qtypeobj = $QUIZ_QTYPES[CALCULATED];

if (empty($form)) {
/*********************************************************/
/***** First page in question wizard - calculated.html! **/
/*********************************************************/
// Inside this block everything works as for non-wizardquestions

    // The layout of the editing page will only support
    // one formula alternative for calculated questions.
    // However, the code behind supports up to six formulas
    // and the database store and attempt/review framework
    // does not have any limit.
    $answers= array();
    for ($i=0; $i<6; $i++) {
        // Make answer slots with default values
        $answers[$i]->answer              = "";
        $answers[$i]->feedback            = "";
        $answers[$i]->fraction            = "1.0";
        $answers[$i]->tolerance           = "0.01";
        $answers[$i]->tolerancetype       = "1";
        $answers[$i]->correctanswerlength = "2"; // Defaults to two ...
        $answers[$i]->correctanswerformat = "1"; // ... decimals
    }

    if (!empty($question->id)) {
        $QUIZ_QTYPES[$question->qtype]->get_question_options($question);
        if (!empty($question->options->answers)) {
            // Overwrite the default valued answer slots
            // with correct values from database
            $answersraw = array_values($question->options->answers);
            $n = count($answersraw);
            for ($i = 0; $i < $n; $i++) {
                $answers[$i] = $answersraw[$i];
            }
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
    print_heading_with_help(get_string("editingcalculated", "quiz"), "calculated", "quiz");
    require("$CFG->dirroot/mod/quiz/questiontypes/calculated/calculated.html");
} else { // $form is not empty
    /*********************************************************/
    /*****    Any subsequent page of the question wizard    **/
    /*********************************************************/
    $qtypeobj->print_next_wizard_page($question, $form, $course);
}
?>
