<?PHP  // $Id$ 

////////////////////////////////////////////////////////////////////////////
/// WEBCT FORMAT
///
/// This Moodle class provides all functions necessary to import and export 
/// WebCT-formatted question files.
///
////////////////////////////////////////////////////////////////////////////

require("default.php");

class quiz_file_format extends quiz_default_format {

    function readquestions($lines) {
    /// Parses an array of lines into an array of questions, 
    /// where each item is a question object as defined by 
    /// readquestion().
     
        $questions = array();

        /// FIXME

        return $questions;
    }


    function readquestion($lines) {
    /// Given an array of lines known to define a question in 
    /// this format, this function converts it into a question 
    /// object suitable for processing and insertion into Moodle.

        $question = NULL;

        /// FIXME

        return $question;
    }

}

?>
