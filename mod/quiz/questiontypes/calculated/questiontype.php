<?PHP  // $Id$

/////////////////
/// CALCULATED ///
/////////////////

/// QUESTION TYPE CLASS //////////////////

require_once("$CFG->dirroot/mod/quiz/questiontypes/datasetdependent/abstractqtype.php");
class quiz_calculated_qtype extends quiz_dataset_dependent_questiontype {

    // Used by the function custom_generator_tools:
    var $calcgenerateidhasbeenadded = false;
    
    function get_answers($question) {
        global $CFG;
        return get_records_sql(
            "SELECT a.*, c.tolerance, c.tolerancetype, c.correctanswerlength, c.id calcid
               FROM {$CFG->prefix}quiz_answers a,
                    {$CFG->prefix}quiz_calculated c
              WHERE c.question = $question->id AND a.id = c.answer");
    }
    
    function name() {
        return 'calculated';
    }
    
    function create_virtual_qtype() {
        require('modifiednumericalqtype.php');
        return new quiz_calculated_qtype_numerical_helper();
    }

    function supports_dataset_item_generation() {
    // Calcualted support generation of randomly distributed number data
        return true;
    }

    function custom_generator_tools($datasetdef) {
        if (ereg('^(uniform|loguniform):([^:]*):([^:]*):([0-9]*)$',
                $datasetdef->options, $regs)) {
            for ($i = 0 ; $i<10 ; ++$i) {
                $lengthoptions[$i] = get_string(($regs[1] == 'uniform'
                                                ? 'decimals'
                                                : 'significantfigures'), 'quiz', $i);
            }
            return '<INPUT TYPE="submit" onClick="'
                    . "document.addform.regenerateddefid.value='$datasetdef->id'; return true;"
                    .'" VALUE="'. get_string('generatevalue', 'quiz') . '"/><br/>'
                    . '<INPUT TYPE="text" SIZE="3" NAME="calcmin[]" '
                    . " VALUE=\"$regs[2]\"/> &amp; <INPUT NAME=\"calcmax[]\" "
                    . ' TYPE="text" SIZE="3" VALUE="' . $regs[3] .'"/> '
                    . choose_from_menu($lengthoptions, 'calclength[]',
                                       $regs[4], // Selected
                                       '', '', '', true) . '<br/>'
                    . choose_from_menu(array('uniform' => get_string('uniform', 'quiz'),
                                             'loguniform' => get_string('loguniform', 'quiz')),
                                       'calcdistribution[]',
                                       $regs[1], // Selected
                                       '', '', '', true);
        } else {
            return '';
        }
    }

    function update_dataset_options($datasetdefs, $form) {
        // Do we have informatin about new options???
        if (empty($form->definition) || empty($form->calcmin)
                || empty($form->calcmax) || empty($form->calclength)
                || empty($form->calcdistribution)) {
            // I gues not:

        } else {
            // Looks like we just could have some new information here
            foreach ($form->definition as $key => $defid) {
                if (isset($datasetdefs[$defid])
                        && is_numeric($form->calcmin[$key])
                        && is_numeric($form->calcmax[$key])
                        && is_numeric($form->calclength[$key])) {
                    switch     ($form->calcdistribution[$key]) {
                        case 'uniform': case 'loguniform':
                            $datasetdefs[$defid]->options =
                                    $form->calcdistribution[$key] . ':'
                                    . $form->calcmin[$key] . ':'
                                    . $form->calcmax[$key] . ':'
                                    . $form->calclength[$key];
                            break;
                        default:
                            notify("Unexpected distribution $form->calcdistribution[$key]");
                    }
                }
            }
        }

        // Look for empty options, on which we set default values
        foreach ($datasetdefs as $def) {
            if (empty($def->options)) {
                $datasetdefs[$def->id]->options = 'uniform:1.0:10.0:1';
            }
        }
        return $datasetdefs;
    }

    function generate_dataset_item($options) {
        if (!ereg('^(uniform|loguniform):([^:]*):([^:]*):([0-9]*)$',
                $options, $regs)) {
            // Unknown options...
            return false;
        }
        if ($regs[1] == 'uniform') {
            $nbr = $regs[2] + ($regs[3]-$regs[2])*mt_rand()/mt_getrandmax();
            return round($nbr, $regs[4]);

        } else if ($regs[1] == 'loguniform') {
            $log0 = log(abs($regs[2])); // It would have worked the other way to
            $nbr = exp($log0 + (log(abs($regs[3])) - $log0)*mt_rand()/mt_getrandmax());

            // Reformat according to the precision $regs[4]:

            // Determine the format 0.[1-9][0-9]* for the nbr... 
            $p10 = 0;
            while ($nbr < 1) {
                --$p10;
                $nbr *= 10;
            }
            while ($nbr >= 1) {
                ++$p10;
                $nbr /= 10;
            }
            // ... and have the nbr rounded of to the correct length
            $nbr = round($nbr, $regs[4]);

            // Have the nbr written on a suitable format,
            // Either scientific or plain numeric
            if (-2 > $p10 || 4 < $p10) {
                // Use scientific format:
                $eX = 'e'.--$p10;
                $nbr *= 10;
                if (1 == $regs[4]) {
                    $nbr = $nbr.$eX;
                } else {
                    // Attach additional zeros at the end of $nbr,
                    $nbr .= (1==strlen($nbr) ? '.' : '')
                            . '00000000000000000000000000000000000000000x';
                    $nbr = substr($nbr, 0, $regs[4] +1).$eX;
                }
            } else {
                // Stick to plain numeric format
                $nbr *= "1e$p10";
                if (0.1 <= $nbr / "1e$regs[4]") {
                    $nbr = $nbr;
                } else {
                    // Could be an idea to add some zeros here
                    $nbr .= (ereg('^[0-9]*$', $nbr) ? '.' : '')
                            . '00000000000000000000000000000000000000000x';
                    $oklen = $regs[4] + ($p10 < 1 ? 2-$p10 : 1);
                    $nbr = substr($nbr, 0, $oklen);
                }
            }

            // The larger of the values decide the sign in case the 
            // have equal different signs (which they really must not have)
            if ($regs[2] + $regs[3] > 0) {
                return $nbr;
            } else {
                return -$nbr;
            }

        } else {
            error("The distribution $regs[1] caused problems");
        }
        return '';
    }

    function comment_header($question) {
        $answers = $this->get_answers($question);
        $strheader = '';
        $delimiter = '';
        foreach ($answers as $answer) {
            $strheader .= $delimiter.$answer->answer;
            $delimiter = ',';
        }
        return $strheader;
    }

    function comment_on_datasetitems($question, $data, $number) {

        /// Find a default unit:
        if ($unit = get_record('quiz_numerical_units',
                'question', $question->id, 'multiplier', 1.0)) {
            $unit = $unit->unit;
        } else {
            $unit = '';
        }

        // Get answers
        $answers = $this->get_answers($question);
        $stranswers = get_string('answer', 'quiz');
        $strmin = get_string('min', 'quiz');
        $strmax = get_string('max', 'quiz');
        $errors = '';
        $delimiter = ': ';
        foreach ($answers as $answer) {
            $calculated = quiz_qtype_calculated_calculate_answer(
                    $answer->answer, $data, $answer->tolerance,
                    $answer->tolerancetype, $answer->correctanswerlength, $unit);
            if ($calculated->min === '') {
                // This should mean that something is wrong
                $errors .= " -$calculated->answer";
                $stranswers .= $delimiter;
            } else {
                $stranswers .= $delimiter.$calculated->answer;
            }
            $strmin .= $delimiter.$calculated->min;
            $strmax .= $delimiter.$calculated->max;
            $delimiter = ', ';
        }
        return "$stranswers<br/>$strmin<br/>$strmax<br/>$errors";
    }

    function tolerance_types() {
        return array('1'  => get_string('relative', 'quiz'),
                     '2'  => get_string('nominal', 'quiz'),
                     '3'  => get_string('geometric', 'quiz'));
    }

    function save_question_options($question, $options) {
        // Get old answers:
        $oldanswers = $this->get_answers($question)
        or $oldanswers = array(); // if there are none

        // Update with new answers
        $answerrec->question = $calcrec->question = $question->id;
        foreach ($options->answers as $newanswer) {
            $answerrec->answer = $newanswer->answer;
            $answerrec->fraction = $newanswer->fraction;
            $answerrec->feedback = $newanswer->feedback;
            $calcrec->tolerance = $newanswer->tolerance;
            $calcrec->tolerancetype = $newanswer->tolerancetype;
            $calcrec->correctanswerlength = $newanswer->correctanswerlength;
            if ($oldanswer = array_shift($oldanswers)) {
                // Reuse old record:
                $calcrec->answer = $answerrec->id = $oldanswer->id;
                $calcrec->id = $oldanswer->calcid;
                if (!update_record('quiz_answers', $answerrec)) {
                    error("Unable to update answer for calculated question $question->name");
                } else {
                    // notify("Answer updated successfully for calculated question $question->name");
                }
                if (!update_record('quiz_calculated', $calcrec)) {
                    error("Unable to update options calculared question $question->name");
                } else {
                    // notify("Options updated successfully for calculated question $question->name");
                }
            } else {
                unset($answerrec->id);
                unset($calcrec->id);
                if (!($calcrec->answer = insert_record('quiz_answers',
                                                       $answerrec))) {
                    error("Unable to insert answer for calculated question $question->name");
                } else {
                    // notify("Answer inserted successfully for calculated question $question->name");
                }
                if (!insert_record('quiz_calculated', $calcrec)) {
                    error("Unable to insert options calculared question $question->name");
                } else {
                    // notify("Options inserted successfully for calculated question $question->name");
                }
            }
        }

        // Delete excessive records:
        foreach ($oldanswers as $oldanswer) {
            if (!delete_records('quiz_answers', 'id', $oldanswer->id)) {
                error("Unable to delete old answers for calculated question $question->name");
            } else {
                // notify("Old answers deleted successfully for calculated question $question->name");
            }
            if (!delete_records('quiz_calculated', 'id', $oldanswer->calcid)) {
                error("Unable to delete old options for calculated question $question->name");
            } else {
                // notify("Old options deleted successfully for calculated question $question->name");
            }
        }

        // Get old units (just like for numerical questions)
        $oldunits = get_records('quiz_numerical_units',
                                'question', $question->id)
        or $oldunits = array(); // if there are none
        if (1 == count($options->units) && !$options->units[0]->unit) {
            /// Only default unit and it is empty, so drop it:
            $options->units = array();
        }
        foreach ($options->units as $newunit) {
            $newunit->question = $question->id;
            if ($oldunit = array_shift($oldunits)) {
                $newunit->id = $oldunit->id;
                if (!update_record('quiz_numerical_units', $newunit)) {
                    error("Unable to update unit $newunit->unit for $question->name");
                } else {
                    // notify("Unit $newunit->unit was updated successfully for $question->name");
                }
            } else {
                if (!insert_record('quiz_numerical_units', $newunit)) {
                    error("Unable to insert unit $newunit->unit for $question->name");
                } else {
                    // notify("Unit $newunit->unit was inserted successfully for question $question->name");
                }
            }
        }
        
        // Delete excessive unit records
        foreach ($oldunits as $oldunit) {
            if (!delete_records('quiz_numerical_units', 'id', $oldunit->id)) {
                error("Unable to delete old unit $oldunit->unit for question $question->name");
            } else {
                notify("Deleted old unit $oldunit->unit successfully for question $question->name");
            }
        }

        return true;
    }
    
    function dataset_options($question, $name, $renameabledatasets=false) {
    // Takes datasets from the parent implementation but
    // filters options that are currently not accepted by calculated
    // It also determines a default selection...
        list($options, $selected) = parent::dataset_options($question, $name);
        foreach ($options as $key => $whatever) {
            if (!ereg('^'.LITERAL.'-', $key) && $key != '0') {
                unset($options[$key]);
            }
        }
        if (!$selected) {
            $selected = LITERAL . "-0-$name"; // Default
        }
        return array($options, $selected);
    }

    function construct_dataset_menus($question, $mandatorydatasets,
                                     $optionaldatasets) {
        $datasetmenus = array();
        foreach ($mandatorydatasets as $datasetname) {
            if (!isset($datasetmenus[$datasetname])) {
                list($options, $selected) =
                        $this->dataset_options($question, $datasetname);
                unset($options['0']); // Mandatory...
                $datasetmenus[$datasetname] = choose_from_menu ($options,
                        'dataset[]', $selected, '', '', "0", true);
            }
        }
        foreach ($optionaldatasets as $datasetname) {
            if (!isset($datasetmenus[$datasetname])) {
                list($options, $selected) =
                        $this->dataset_options($question, $datasetname);
                $datasetmenus[$datasetname] = choose_from_menu ($options,
                        'dataset[]', $selected, '', '', "0", true);
            }
        }
        return $datasetmenus;
    }

    function grade_response($question, $nameprefix) {
        /// Determines the answers and then lets the
        /// NUMERICAL question type take care of the
        /// grading...
        
        list($datasetnumber, $individualdata) =
        $this->parse_datasetinput($question->response[$nameprefix]);

        // find the raw answer material
        global $CFG;
        if (!($answers = $this->get_answers($question))) {
            notify("Error no answers found for question $question->id");
        }
        
        /// Find a default unit:
        if ($unit = get_record('quiz_numerical_units',
                'question', $question->id, 'multiplier', 1.0)) {
            $unit = $unit->unit;
        } else {
            $unit = '';
        }
        
        // Construct answers for the numerical question type
        foreach ($answers as $aid => $answer) {
            $answernumerical = quiz_qtype_calculated_calculate_answer(
                    $answer->answer, $individualdata,
                    $answer->tolerance, $answer->tolerancetype,
                    $answer->correctanswerlength, $unit);
            $answers[$aid]->answer = $answernumerical->answer;
            $answers[$aid]->min = $answernumerical->min;
            $answers[$aid]->max = $answernumerical->max;
        }

        // Forward the grading to the virtual qtype
        $virtualnameprefix = $this->create_virtual_nameprefix(
                $nameprefix, $question->response[$nameprefix]);
        unset($question->response[$nameprefix]);
        $virtualqtype = $this->get_virtual_qtype();
        $virtualqtype->set_answers($answers);
        return $virtualqtype->grade_response($question,
                                             $virtualnameprefix);
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[CALCULATED]= new quiz_calculated_qtype();

function quiz_qtype_calculated_calculate_answer($formula, $individualdata,
        $tolerance, $tolerancetype, $answerlength, $unit='') {
/// The return value has these properties:
/// ->answer    the correct answer
/// ->min       the lower bound for an acceptable response
/// ->max       the upper bound for an accetpable response
    
    /// Exchange formula variables with the correct values...
    foreach ($individualdata as $name => $value) {
        $formula = str_replace('{'.$name.'}', "($value)", $formula);
    }
    if (ereg('\\{([^}]*)\\}', $formula, $regs)) {
        // This is the normal case for a recently added question.
        // Return a notification about it
        $calculated->answer = $calculated->min = $calculated->max = '';
        return $calculated;
        
    } else if ($error = quiz_qtype_calculated_find_formula_errors($formula)) {
        $calculated->answer = $error;
        $calculated->min = $calculated->max = '';
        return $calculated;
    }

    /// Calculate the correct answer
    eval('$answer = '.$formula.';');

    /// Calculate min and max
    switch ($tolerancetype) {
        case '1': case 'relative':
            /// Recalculate the tolerance and fall through
            /// to the nominal case:
            $tolerance = $answer * $tolerance;
            
            // Falls through to the nominal case -
        case '2': case 'nominal':
            $tolerance = abs($tolerance); // important
            $max = $answer + $tolerance;
            $min = $answer - $tolerance;
            break;

        case '3': case 'geometric':
            $quotient = 1 + abs($tolerance);
            if ($answer >= 0) {
                $max = $answer * $quotient;
                $min = $answer / $quotient;
            } else {
                $min = $answer * $quotient;
                $max = $answer / $quotient;
            }
            break;

        default:
            error("Unknown tolerance type $tolerancetype");
    }
    $calculated->min    = $min;
    $calculated->max    = $max;

    /// Adjust the number of significant digits for the correct answer
    if ($answer) { // Applies only if the result is non-zero

        // Convert to positive answer...
        if ($answer < 0) {
            $answer = -$answer;
            $sign = '-';
        } else {
            $sign = '';
        }

        // Determine the format 0.[1-9][0-9]* for the answer... 
        $p10 = 0;
        while ($answer < 1) {
            --$p10;
            $answer *= 10;
        }
        while ($answer >= 1) {
            ++$p10;
            $answer /= 10;
        }
        // ... and have the answer rounded of to the correct length
        $answer = round($answer, $answerlength);

        // Have the answer written on a suitable format,
        // Either scientific or plain numeric
        if (-2 > $p10 || 4 < $p10) {
            // Use scientific format:
            $eX = 'e'.--$p10;
            $answer *= 10;
            if (1 == $answerlength) {
                $calculated->answer = $sign.$answer.$eX.$unit;
            } else {
                // Attach additional zeros at the end of $answer,
                $answer .= (1==strlen($answer) ? '.' : '')
                        . '00000000000000000000000000000000000000000x';
                $calculated->answer = $sign
                        .substr($answer, 0, $answerlength +1).$eX.$unit;
            }
        } else {
            // Stick to plain numeric format
            $answer *= "1e$p10";
            if (0.1 <= $answer / "1e$answerlength") {
                $calculated->answer = $sign.$answer.$unit;
            } else {
                // Could be an idea to add some zeros here
                $answer .= (ereg('^[0-9]*$', $answer) ? '.' : '')
                        . '00000000000000000000000000000000000000000x';
                $oklen = $answerlength + ($p10 < 1 ? 2-$p10 : 1);
                $calculated->answer = $sign.substr($answer, 0, $oklen).$unit;
            }
        }
    } else {
        $calculated->answer = 0.0;
    }
    
    /// Return the result
    return $calculated;
}


function quiz_qtype_calculated_find_formula_errors($formula) {
/// Validates the formula submitted from the question edit page.
/// Returns false if everything is alright.
/// Otherwise it constructs an error message

    // Strip away dataset names
    while (ereg('\\{[[:alpha:]][^>} <{"\']*\\}', $formula, $regs)) {
        $formula = str_replace($regs[0], '1', $formula);
    }

    // Strip away empty space
    $formula = str_replace(' ', '', $formula);

    $safeoperatorchar = '-+/*%>:^~<?=&|!';
    $operatorornumber = "[$safeoperatorchar.0-9eE]";

    while (ereg("(^|[$safeoperatorchar,(])([a-z0-9_]*)\\(($operatorornumber+(,$operatorornumber+((,$operatorornumber+)+)?)?)?\\)",
            $formula, $regs)) {

        switch ($regs[2]) {
            // Simple parenthesis
            case '':
                if ($regs[4] || empty($regs[3])) {
                    return get_string('illegalformulasyntax', 'quiz', $regs[0]);
                }
                break;
                
            // Zero argument functions
            case 'pi':
                if ($regs[3]) {
                    return get_string('functiontakesnoargs', 'quiz', $regs[2]);
                }
                break;

            // Single argument functions (the most common case)
            case 'abs': case 'acos': case 'acosh': case 'asin': case 'asinh':
            case 'atan': case 'atanh': case 'bindec': case 'ceil': case 'cos':
            case 'cosh': case 'decbin': case 'decoct': case 'deg2rad':
            case 'exp': case 'expm1': case 'floor': case 'is_finite':
            case 'is_infinite': case 'is_nan': case 'log10': case 'log1p':
            case 'octdec': case 'rad2deg': case 'sin': case 'sinh': case 'sqrt':
            case 'tan': case 'tanh':
                if ($regs[4] || empty($regs[3])) {
                    return get_string('functiontakesonearg','quiz',$regs[2]);
                }
                break;

            // Functions that take one or two arguments
            case 'log': case 'round':
                if ($regs[5] || empty($regs[3])) {
                    return get_string('functiontakesoneortwoargs','quiz',$regs[2]);
                }
                break;
                
            // Functions that must have two arguments
            case 'atan2': case 'fmod': case 'pow':
                if ($regs[5] || empty($regs[4])) {
                    return get_string('functiontakestwoargs', 'quiz', $regs[2]);
                }
                break;

            // Functions that take two or more arguments
            case 'min': case 'max':
                if (empty($regs[4])) {
                    return get_string('functiontakesatleasttwo','quiz',$regs[2]);
                }
                break;

            default:
                return get_string('unsupportedformulafunction','quiz',$regs[2]);
        }

        // Exchange the function call with '1' and then chack for
        // another function call...
        if ($regs[1]) {
            // The function call is proceeded by an operator
            $formula = str_replace($regs[0], $regs[1] . '1', $formula);
        } else {
            // The function call starts the formula
            $formula = ereg_replace("^$regs[2]\\([^)]*\\)", '1', $formula);
        }
    }
    
    if (ereg("[^$safeoperatorchar.0-9eE]+", $formula, $regs)) {
        return get_string('illegalformulasyntax', 'quiz', $regs[0]);
    } else {
        // Formula just might be valid
        return false;
    }
}

?>
