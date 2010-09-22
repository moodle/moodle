<?php
require_once($CFG->libdir.'/tablelib.php');

class quiz_report_statistics_question_table extends flexible_table {
    /**
     * @var object this question with _stats object.
     */
    var $question;

    function quiz_report_statistics_question_table($qid){
        parent::flexible_table('mod-quiz-report-statistics-question-table'.$qid);
    }

    /**
     * Setup the columns and headers and other properties of the table and then
     * call flexible_table::setup() method.
     */
    function setup($reporturl, $question, $hassubqs){
        $this->question = $question;
        // Define table columns
        $columns = array();
        $headers = array();

        if ($hassubqs){
            $columns[]= 'subq';
            $headers[]= '';
        }

        $columns[]= 'response';
        $headers[]= get_string('response', 'quiz_statistics');


        $columns[]= 'credit';
        $headers[]= get_string('optiongrade', 'quiz_statistics');

        $columns[]= 'rcount';
        $headers[]= get_string('count', 'quiz_statistics');

        $columns[]= 'frequency';
        $headers[]= get_string('frequency', 'quiz_statistics');

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->sortable(false);

        $this->column_class('credit', 'numcol');
        $this->column_class('rcount', 'numcol');
        $this->column_class('frequency', 'numcol');

        // Set up the table
        $this->define_baseurl($reporturl->out());

        $this->collapsible(false);

        $this->set_attribute('class', 'generaltable generalbox boxaligncenter');

        parent::setup();
    }

    function col_response($response){
        global $QTYPES;
        if (!$this->is_downloading() || $this->is_downloading() == 'xhtml'){
            return $response->indent . $QTYPES[$this->question->qtype]->format_response($response->response, $this->question->questiontextformat);
        } else {
            return $response->indent . $response->response;
        }
    }

    function col_subq($response){
        return $response->subq;
    }

    function col_credit($response){
        if (!is_null($response->credit)){
            return ($response->credit*100).'%';
        } else {
            return '';
        }
    }

    function col_frequency($response){
        if ($this->question->_stats->s){
            return format_float((($response->rcount / $this->question->_stats->s)*100),2).'%';
        } else {
            return '';
        }
    }



}

