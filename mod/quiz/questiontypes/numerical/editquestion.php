<?php // $Id$

    // This will only support one answer of the type NUMERICAL
    // However, lib.php has support for multiple answers
    if (!empty($question->id)) {
        $answersraw= quiz_get_answers($question);
    }
    $answers= array();
    for ($i=0; $i<6; $i++) {
        $answers[$i]->answer   = ""; // Make answer slots, default as blank...
        $answers[$i]->min      = "";
        $answers[$i]->max      = "";
        $answers[$i]->feedback = "";
    }
    if (!empty($answersraw)) {
        $i=0;
        foreach ($answersraw as $answer) {
            $answers[$i] = $answer;
            $i++;
        }
    }
    $units = array();
    for ($i=0 ; $i<6 ; $i++) {
        $units[$i]->multiplier = ''; // Make unit slots, default as blank...
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

    // Strip unit from answers, if they have any:
    foreach ($units as $key => $unit) {
        if (1.0 == $unit->multiplier && $unit->unit) {
            $ukey = $key;
            // Possible default unit found:
            foreach ($answers as $i => $answer) {
                if (($answer->min || $answer->max) && ereg(
                        "^(.*)$unit->unit$", $answer->answer, $numreg)) {
                    $answers[$i]->answer = $numreg[1];
                    if (0!=$ukey) {
                        // Make unit default by putting it first:
                        $units[$ukey] = $units[0];
                        $units[0] = $unit;
                        $ukey = 0;
                    }
                }    
            }
        }
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

    print_heading_with_help(get_string("editingnumerical", "quiz"), "numerical", "quiz");
    require("numerical.html");

?>
