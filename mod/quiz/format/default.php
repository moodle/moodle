<?PHP  // $Id$ 

////////////////////////////////////////////////////////////////////
/// Default class for file imports/exports.                       //
///                                                               //
/// Doesn't do everything on it's own -- it needs to be extended. //
////////////////////////////////////////////////////////////////////


class quiz_default_format {

    var $displayerrors = true;

/// Importing functions

    function readdata($filename) {
    /// Returns complete file with an array, one item per line

        if (is_readable($filename)) {
            return file($filename);
        }
        return false;
    }


    function readquestions($lines) {
    /// Parses an array of lines into an array of questions, 
    /// where each item is a question object as defined by 
    /// readquestion().   Questions are defines as anything 
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

        echo "<p>You need to override the readquestion() function";

        return NULL;
    }


    function swapshuffle($array) {
    /// Given a simple array, shuffles it up just like shuffle()
    /// Unlike PHP's shuffle() ihis function works on any machine.

        srand ((double) microtime() * 10000000);
        $last = count($array) - 1;
        for ($i=0;$i<=$last;$i++) {
            $from = rand(0,$last);
            $curr = $array[$i];
            $array[$i] = $array[$from];
            $array[$from] = $curr;
        }  
        return $array;
    }

}

?>
