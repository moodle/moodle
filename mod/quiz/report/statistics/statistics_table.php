<?php
require_once($CFG->libdir.'/tablelib.php');

class quiz_report_statistics_table extends flexible_table {

    var $quiz;

    function quiz_report_statistics_table(){
        parent::flexible_table('mod-quiz-report-statistics-report');
    }

    /**
     * Setup the columns and headers and other properties of the table and then
     * call flexible_table::setup() method.
     */
    function setup($quiz, $cmid, $reporturl, $s){
        $this->quiz = $quiz;
        $this->cmid = $cmid;
        // Define table columns
        $columns = array();
        $headers = array();

        $columns[]= 'number';
        $headers[]= get_string('questionnumber', 'quiz_statistics');

        if (!$this->is_downloading()){
            $columns[]= 'icon';
            $headers[]= '';
            $columns[]= 'actions';
            $headers[]= '';
        } else {
            $columns[]= 'qtype';
            $headers[]= get_string('questiontype', 'quiz_statistics');
        }
        $columns[]= 'name';
        $headers[]= get_string('questionname', 'quiz');

        $columns[]= 's';
        $headers[]= get_string('attempts', 'quiz_statistics');

        if ($s>1){
            $columns[]= 'facility';
            $headers[]= get_string('facility', 'quiz_statistics');

            $columns[]= 'sd';
            $headers[]= get_string('standarddeviationq', 'quiz_statistics');
        }
        $columns[]= 'random_guess_score';
        $headers[]= get_string('random_guess_score', 'quiz_statistics');

        $columns[]= 'intended_weight';
        $headers[]= get_string('intended_weight', 'quiz_statistics');

        $columns[]= 'effective_weight';
        $headers[]= get_string('effective_weight', 'quiz_statistics');

        $columns[]= 'discrimination_index';
        $headers[]= get_string('discrimination_index', 'quiz_statistics');

        $columns[]= 'discriminative_efficiency';
        $headers[]= get_string('discriminative_efficiency', 'quiz_statistics');

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->sortable(false);

        $this->column_class('s', 'numcol');
        $this->column_class('random_guess_score', 'numcol');
        $this->column_class('intended_weight', 'numcol');
        $this->column_class('effective_weight', 'numcol');
        $this->column_class('sd', 'numcol');
        $this->column_class('facility', 'numcol');
        $this->column_class('discrimination_index', 'numcol');
        $this->column_class('discriminative_efficiency', 'numcol');

        // Set up the table
        $this->define_baseurl($reporturl->out());

        $this->collapsible(true);



        $this->set_attribute('id', 'questionstatistics');
        $this->set_attribute('class', 'generaltable generalbox boxaligncenter');

        parent::setup();
    }


    function col_name($question){
        if (!$this->is_downloading()){
            if ($question->qtype!='random'){
                $tooltip = get_string('detailedanalysis', 'quiz_statistics');
                $url = $this->baseurl .'&amp;qid='.$question->id;
                $html = "<a title=\"$tooltip\" href=\"$url\">".$question->name."</a>";
            } else {
                $html = $question->name;
            }
            if ($this->is_dubious_question($question)){
                return "<div class=\"dubious\">$html</div>";
            } else {
                return $html;
            }
        } else {
            return $question->name;
        }

    }

    /**
     * @param object question the question object with a property _stats which
     * includes all the stats for the question.
     * @return boolean is this question possibly not pulling it's weight?
     */
    function is_dubious_question($question){
        if (!is_numeric($question->_stats->discriminativeefficiency)){
            return false;
        } else {
            return $question->_stats->discriminativeefficiency < 15;
        }
    }

    function col_icon($question){
        return print_question_icon($question, true);
    }

    function col_number($question){
        if (!$question->_stats->subquestion){
            return $question->number;
        } else {
            return '';
        }
    }
    function col_actions($question){
        return quiz_question_action_icons($this->quiz, $this->cmid, $question, $this->baseurl);
    }
    function col_qtype($question){
        return get_string($question->qtype,'quiz');
    }
    function col_intended_weight($question){
        return quiz_report_scale_sumgrades_as_percentage($question->_stats->maxgrade, $this->quiz);
    }
    function col_effective_weight($question){
        global $OUTPUT;
        if (!$question->_stats->subquestion){
            if ($question->_stats->negcovar){
                $negcovar = get_string('negcovar', 'quiz_statistics');
                if (!$this->is_downloading()){
                    $negcovar .= $OUTPUT->old_help_icon('negcovar', $negcovar, 'quiz_statistics');
                    return '<div class="negcovar">'.$negcovar.'</div>';
                } else {
                    return $negcovar;
                }
            } else {
                return number_format($question->_stats->effectiveweight, 2).'%';
            }
        } else {
            return '';
        }
    }
    function col_discrimination_index($question){
        if (is_numeric($question->_stats->discriminationindex)){
            return number_format($question->_stats->discriminationindex, 2).'%';
        } else {
            return $question->_stats->discriminationindex;
        }
    }
    function col_discriminative_efficiency($question){
        if (is_numeric($question->_stats->discriminativeefficiency)){
            return number_format($question->_stats->discriminativeefficiency, 2).'%';
        } else {
            return '';
        }
    }
    function col_random_guess_score($question){
        $randomguessscore = question_get_random_guess_score($question);
        if (is_numeric($randomguessscore)){
            return number_format($randomguessscore * 100, 2).'%';
        } else {
            return $randomguessscore; // empty string returned by random question.
        }
    }

    function col_sd($question){
        if (!is_null($question->_stats->sd) && ($question->_stats->maxgrade!=0)){
            return number_format($question->_stats->sd*100 / $question->_stats->maxgrade, 2).'%';
        } else {
            return '';
        }
    }
    function col_s($question){
        if (isset($question->_stats->s)){
            return $question->_stats->s;
        } else {
            return 0;
        }
    }
    function col_facility($question){
        if (!is_null($question->_stats->facility)){
            return number_format($question->_stats->facility*100, 2).'%';
        } else {
            return '';
        }
    }

}

