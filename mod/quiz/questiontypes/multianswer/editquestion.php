<?PHP // $Id$
    if ($question->questiontext and $question->id) {
        $answers = quiz_get_answers($question);

        foreach ($answers as $multianswer) {
            $parsableanswerdef = '{' . $multianswer->norm . ':';
            switch ($multianswer->answertype) {
                case MULTICHOICE:
                    $parsableanswerdef .= 'MULTICHOICE:';
                    break;
                case SHORTANSWER:
                    $parsableanswerdef .= 'SHORTANSWER:';
                    break;
                case NUMERICAL:
                    $parsableanswerdef .= 'NUMERICAL:';
                    break;
                default:
                    error("answertype $multianswer->answertype not recognized");
            }
            $separator= '';
            foreach ($multianswer->subanswers as $subanswer) {
                $parsableanswerdef .= $separator
                        . '%' . round(100*$subanswer->fraction) . '%';
                $parsableanswerdef .= $subanswer->answer;
                if (isset($subanswer->min) && isset($subanswer->max)
                        and $subanswer->min || $subanswer->max) {
                    // Special for numerical answers:
                    $errormargin = $subanswer->answer - $subanswer->min;
                    $parsableanswerdef .= ":$errormargin";
                }
                if ($subanswer->feedback) {
                    $parsableanswerdef .= "#$subanswer->feedback";
                }
                $separator = '~';
            }
            $parsableanswerdef .= '}';
            $question->questiontext = str_replace
                    ("{#$multianswer->positionkey}", $parsableanswerdef,
                     $question->questiontext);
        }
    }
    print_heading_with_help(get_string('editingmultianswer', 'quiz'),
                                       'multianswer', 'quiz');
    require('multianswer.html');

?>
