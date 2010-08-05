<?php  // $Id$ 

////////////////////////////////////////////////////////////////////////////
/// MULTIANSWER FORMAT
///
/// Created by Henrik Kaipe
///
////////////////////////////////////////////////////////////////////////////

// Based on format.php, included by ../../import.php
/**
 * @package questionbank
 * @subpackage importexport
 */
class qformat_multianswer extends qformat_default {

    function provide_import() {
      return true;
    }

    function readquestions($lines) {
        // For this class the method has been simplified as
        // there can never be more than one question for a
        // multianswer import
        $questions = array();

        $question = qtype_multianswer_extract_question(
                addslashes(implode('', $lines)));
        $question->qtype = MULTIANSWER;
        $question->course = $this->course;

        if (!empty($question)) {
            $name = html_to_text(implode(' ', $lines));
            $name = preg_replace('/{[^}]*}/', '', $name);
            $name = trim($name);

            if ($name) {
                $question->name = addslashes(shorten_text($name, 45));
            } else {
                // We need some name, so use the current time, since that will be
                // reasonably unique.
                $question->name = userdate(time());
            }

            $questions[] = $question;
        }

        return $questions;
    }
}
