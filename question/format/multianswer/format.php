<?php  // $Id$ 

////////////////////////////////////////////////////////////////////////////
/// MULTIANSWER FORMAT
///
/// Created by Henrik Kaipe
///
////////////////////////////////////////////////////////////////////////////

// Based on format.php, included by ../../import.php

class quiz_format_multianswer extends quiz_default_format {

    function provide_import() {
      return true;
    }

    function readquestions($lines) {
    /// Parses an array of lines into an array of questions.
    /// For this class the method has been simplified as
    /// there can never be more than one question for a
    /// multianswer import

        $questions= array();
        $thequestion= quiz_qtype_multianswer_extract_question
                            (addslashes(implode('',$lines)));
        $thequestion->qtype = MULTIANSWER;
        $thequestion->course = $this->course;

        if (!empty($thequestion)) {
            $thequestion->name = addslashes($lines[0]);
            
            $questions[] = $thequestion;
        }

        return $questions;
    }
}

?>
