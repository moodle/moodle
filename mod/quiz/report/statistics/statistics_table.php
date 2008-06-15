<?php  // $Id$

class quiz_report_statistics_table extends flexible_table {
    
    var $quiz; 
    
    function quiz_report_statistics_table(){
        parent::flexible_table('mod-quiz-report-statistics-report');
    }

    /**
     * Setup the columns and headers and other properties of the table and then
     * call flexible_table::setup() method.
     */
    function setup($quiz, $cmid, $reporturl){
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
        
        $columns[]= 'intended_weight';
        $headers[]= get_string('intended_weight', 'quiz_statistics');
        
        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->sortable(false);

        // Set up the table
        $this->define_baseurl($reporturl->out());

        $this->collapsible(true);

/*        $this->column_suppress('picture');
        $this->column_suppress('fullname');
        $this->column_suppress('idnumber');

        $this->no_sorting('feedbacktext');

        $this->column_class('picture', 'picture');
        $this->column_class('lastname', 'bold');
        $this->column_class('firstname', 'bold');
        $this->column_class('fullname', 'bold');
        $this->column_class('sumgrades', 'bold');*/
        
        $this->column_class('intended_weight', 'numcol');

        $this->set_attribute('id', 'questionstatistics');
        $this->set_attribute('class', 'generaltable generalbox boxaligncenter');

        parent::setup();
    }


    function col_name($question){
        return $question->name;

    }
    
    function col_icon($question){
        return print_question_icon($question, true);
    }
    
    function col_number($question){
        return $question->number;
    }
    function col_actions($question){
        return quiz_question_action_icons($this->quiz, $this->cmid, $question, $this->baseurl);
    }
    function col_qtype($question){
        return $question->qtype;
    }
    function col_intended_weight($question){
        return quiz_report_scale_sumgrades_as_percentage($question->grade, $this->quiz);
    }
            

}
?>
