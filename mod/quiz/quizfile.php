<?PHP // $Id$
      // This function fetches files from the data directory
      // Syntax:   quizfile.php/quiz id/question id/dir/.../dir/filename.ext
      // It is supposed to be used by the quiz module only
      // I believe this is obsolete, everything should be using moodle/file.php GWD

    require_once('../../config.php');
    require_once($CFG->libdir.'/filelib.php');
    require_once('locallib.php');

    if (empty($CFG->filelifetime)) {
        $lifetime = 86400;     // Seconds for files to remain in caches
    } else {
        $lifetime = $CFG->filelifetime;
    }

    // disable moodle specific debug messages
    disable_debugging();

    $relativepath = get_file_argument('quizfile.php');
    
    if (!$relativepath) {
        error('No valid arguments supplied or incorrect server configuration');
    }
    
    // extract relative path components
    $args = explode('/', trim($relativepath, '/'));
    if (count($args) < 3) { // always at least category, question and path
        error('No valid arguments supplied');
    }
    
    $quizid       = (int)array_shift($args);
    $questionid   = (int)array_shift($args);
    $relativepath = implode ('/', $args);

    if (!($question = get_record('question', 'id', $questionid))) {
        error('No valid arguments supplied');
    }

    if (!($questioncategory = get_record('question_categories', 'id', $question->category))) {
        error('No valid arguments supplied');
    }

    /////////////////////////////////////
    // Check access
    /////////////////////////////////////
    if ($quizid == 0) { // teacher doing preview during quiz creation
        if ($questioncategory->publish) {
            require_login();
            if (!isteacherinanycourse()) {
              error('No valid arguments supplied');
            }
        } else {
            require_login($questioncategory->course);
            $cm = get_coursemodule_from_instance('quiz', $quizid);
            require_capability('mod/quiz:preview', get_context_instance(CONTEXT_MODULE, $cm->id));
        }        
    } else {
        if (!($quiz = get_record('quiz', 'id', $quizid))) {
            error('No valid arguments supplied');
        }
        if (!($course = get_record('course', 'id', $quiz->course))) {
            error('No valid arguments supplied');
        }
        require_login($course->id);
        
        // For now, let's not worry about this.  The following check causes 
        // problems sometimes when reviewing a quiz
        //if (!isteacher($course->id)
        //    and !quiz_get_user_attempt_unfinished($quiz->id, $USER->id)
        //    and ! ($quiz->review  &&  time() > $quiz->timeclose)
        //        || !quiz_get_user_attempts($quiz->id, $USER->id) )
        //{
        //    error("Logged-in user is not allowed to view this quiz");
        //}
    
        ///////////////////////////////////////////////////
        // The logged-in user has the right to view material on this quiz!
        // Now verify the consistency between $quiz, $question, its category and $relativepathname
        ///////////////////////////////////////////////////
    
        // For now, let's not worry about this.  The following check doesn't 
        // work for randomly selected questions and it gets complicated
        //if (!in_array($question->id, explode(',', $quiz->questions), FALSE)) {
        //    error("Specified question is not on the specified quiz");
        //}
    }

    // Have the question check whether it uses this file or not
    if (!$QTYPES[$question->qtype]->uses_quizfile($question,
                                                       $relativepath)) {
        error("The specified file path is not on the specified question");
    }


    ///////////////////////////////////////////
    // All security stuff is now taken care of.
    // Specified file can now be returned...
    //////////////////////////////////////////

    $pathname = "$CFG->dataroot/$questioncategory->course/$relativepath";
    $filename = $args[count($args)-1];


    if (file_exists($pathname)) {
        send_file($pathname, $filename, $lifetime);
    } else {
        header('HTTP/1.0 404 not found');
        print_error('filenotfound', 'error'); //this is not displayed on IIS??
    }
?>
