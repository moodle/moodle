<?PHP  // $Id$ 

////////////////////////////////////////////////////////////////////////////
/// IMS QTI FORMAT
////////////////////////////////////////////////////////////////////////////

// Based on format.php, included by ../../import.php

class quiz_file_format extends quiz_default_format {

    function importpreprocess($category) {
        global $CFG;

        error("Sorry, this format is not yet implemented!", "$CFG->wwwroot/mod/quiz/import.php?category=$category->id");
    }

}

?>
