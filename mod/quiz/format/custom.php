<?PHP  // $Id$ 

////////////////////////////////////////////////////////////////////////////
/// CUSTOM FORMAT
///
/// This format provides a starting point for creating your own 
/// import format.
/// 
/// If your questions are separated by blank lines, then you will 
/// only need to modify the readquestion() function to parse the 
/// lines into each question.  See default.php for the other 
/// functions that you may need to override if you have different
/// needs.
/// 
/// See missingword.php for an example of how it's done, and see 
/// the top of ../lib.php for some constants you might need.
///
////////////////////////////////////////////////////////////////////////////

require("default.php");

class quiz_file_format extends quiz_default_format {

    function readquestions($lines) {
    /// Parses an array of lines into an array of questions, 
    /// where each item is a question object as defined by 
    /// readquestion().   Questions are defined as anything 
    /// between blank lines.
     
        $questions = array();
        $currentquestion = array();

        foreach ($lines as $line) {
           $line = trim($line);
           if (empty($line)) {
               if (!empty($currentquestion)) {
                   if ($question = $this->readquestion($currentquestion)) {
                       $questions[] = $question;
                   }
                   $currentquestion = array();
               }
           } else {
               $currentquestion[] = $line;
           }
        }

        return $questions;
    }



    function readquestion($lines) {
    /// Given an array of lines known to define a question in 
    /// this format, this function converts it into a question 
    /// object suitable for processing and insertion into Moodle.

        $question = NULL;

        return $question;
    }
}

?>
