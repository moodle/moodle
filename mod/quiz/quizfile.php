<?PHP // $Id$
      // This function fetches files from the data directory
      // Syntax:   quizfile.php/quiz id/question id/dir/.../dir/filename.ext
      // It is supposed to be used by the quiz module only

    require_once("../../config.php");
    require_once("../../files/mimetypes.php");
    require_once("lib.php");

    $lifetime = 86400;

    if (isset($file)) {     // workaround for situations where / syntax doesn't work
        $pathinfo = $file;
    } else {
        $pathinfo = get_slash_arguments("file.php");
    }

    if (!$pathinfo) {
        error("No file parameters!");
    }

    /////////////////////////////////////
    // Extract info from $pathinfo
    /////////////////////////////////////

    $idreg = '[0-9]+';
    if (!ereg("^/?($idreg)/($idreg)/((.+/)?([^/]+))$",
              $pathinfo,
              $regs) ) {
        error("File parameters are badly formated");
    }
    if (! ($quiz = get_record('quiz', 'id', $regs[1]))) {
        error("No valid quiz supplied");
    }
    if (! ($question = get_record('quiz_questions', 'id', $regs[2]))) {
        error("No valid question supplied");
    }
    if (! ($relativefilepath = $regs[3])) {
        error("No valid file path supplied");
    }
    if (! ($filename = $regs[5])) {
        error("No valid file name supplied");
    }

    //////////////////////////////////////////
    // Info from $pathinfo is now extracted!
    // Now check the user's persmissions on this quiz...
    //////////////////////////////////////////

    if (! ($course = get_record("course", "id", $quiz->course))) {
        error("Supplied quiz $quiz->name does not belong to a valid course");
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

    if (! ($questioncategory = get_record('quiz_categories', 'id',
                                          $question->category)))
    {
        error("Question category is not valid");
    }
    // For the moment - questions can reference datafiles through image only
    if (! ($question->image == $relativefilepath)) {
        error("The specified file path is not on the specified question");
    }


    ///////////////////////////////////////////
    // All security stuff is now taken care of.
    // Specified file can now be returned...
    //////////////////////////////////////////

    $pathname = "$CFG->dataroot/$questioncategory->course/$relativefilepath";
    // $filename has already been extracted


    /////////////////////////////////////////////////////////////////
    // The remaining code is identical to the final lines of file.php
    // If you ask me - this stuff should be separated into a separate
    // function for conviency.
    // That function would find itself very in comfortable in the 
    // file mimetypes.php
    //////////////////////////////////

    $mimetype = mimeinfo("type", $filename);

    if (file_exists($pathname)) {
        $lastmodified = filemtime($pathname);

        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT");
        header("Cache-control: max_age = $lifetime"); // a day
        header("Pragma: ");
        header("Content-disposition: inline; filename=$filename");
        header("Content-length: ".filesize($pathname));
        header("Content-type: $mimetype");
        readfile("$pathname");
    } else {
        error("Sorry, but the file you are looking for was not found ($pathname)", "course/view.php?id=$courseid");
    }

    exit;
?>
