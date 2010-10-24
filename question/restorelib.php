<?php

    /**
     * Recode content links in question texts.
     * @param object $restore the restore metadata object.
     * @return boolean whether the operation succeeded.
     */
    function question_decode_content_links_caller($restore) {
        global $CFG, $QTYPES, $DB;
        $status = true;
        $i = 1;   //Counter to send some output to the browser to avoid timeouts

        // Get a list of which question types have custom field that will need decoding.
        $qtypeswithextrafields = array();
        $qtypeswithhtmlanswers = array();
        foreach ($QTYPES as $qtype => $qtypeclass) {
            $qtypeswithextrafields[$qtype] = method_exists($qtypeclass, 'decode_content_links_caller');
            $qtypeswithhtmlanswers[$qtype] = $qtypeclass->has_html_answers();
        }
        $extraprocessing = array();

        $coursemodulecontexts = array();
        $context = get_context_instance(CONTEXT_COURSE, $restore->course_id);
        $coursemodulecontexts[] = $context->id;
        $cms = $DB->get_records('course_modules', array('course'=>$restore->course_id), '', 'id');
        if ($cms){
            foreach ($cms as $cm){
                $context =  get_context_instance(CONTEXT_MODULE, $cm->id);
                $coursemodulecontexts[] = $context->id;
            }
        }
        $coursemodulecontextslist = join($coursemodulecontexts, ',');
        // Decode links in questions.
        list($usql, $params) = $DB->get_in_or_equal(explode(',', $coursemodulecontextslist));
        if ($questions = $DB->get_records_sql("SELECT q.id, q.qtype, q.questiontext, q.generalfeedback
                                                 FROM {question} q, {question_categories} qc
                                                WHERE q.category = qc.id
                                                      AND qc.contextid $usql", $params)) {

            foreach ($questions as $question) {
                $questiontext = restore_decode_content_links_worker($question->questiontext, $restore);
                $generalfeedback = restore_decode_content_links_worker($question->generalfeedback, $restore);
                if ($questiontext != $question->questiontext || $generalfeedback != $question->generalfeedback) {
                    $question->questiontext = $questiontext;
                    $question->generalfeedback = $generalfeedback;
                    $DB->update_record('question', $question);
                }

                // Do some output.
                if (++$i % 5 == 0 && !defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if ($i % 100 == 0) {
                        echo "<br />";
                    }
                    backup_flush(300);
                }

                // Decode any questiontype specific fields.
                if ($qtypeswithextrafields[$question->qtype]) {
                    if (!array_key_exists($question->qtype, $extraprocessing)) {
                        $extraprocessing[$question->qtype] = array();
                    }
                    $extraprocessing[$question->qtype][] = $question->id;
                }
            }
        }

        // Decode links in answers.
        if ($answers = $DB->get_records_sql("SELECT qa.id, qa.answer, qa.feedback, q.qtype
                                               FROM {question_answers} qa, {question} q, {question_categories} qc
                                              WHERE qa.question = q.id
                                                    AND q.category = qc.id
                                                    AND qc.contextid $usql", $params)) {

            foreach ($answers as $answer) {
                $feedback = restore_decode_content_links_worker($answer->feedback, $restore);
                if ($qtypeswithhtmlanswers[$answer->qtype]) {
                    $answertext = restore_decode_content_links_worker($answer->answer, $restore);
                } else {
                    $answertext = $answer->answer;
                }
                if ($feedback != $answer->feedback || $answertext != $answer->answer) {
                    unset($answer->qtype);
                    $answer->feedback = $feedback;
                    $answer->answer = $answertext;
                    $DB->update_record('question_answers', $answer);
                }

                // Do some output.
                if (++$i % 5 == 0 && !defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if ($i % 100 == 0) {
                        echo "<br />";
                    }
                    backup_flush(300);
                }
            }
        }

        // Do extra work for certain question types.
        foreach ($extraprocessing as $qtype => $questionids) {
            if (!$QTYPES[$qtype]->decode_content_links_caller($questionids, $restore, $i)) {
                $status = false;
            }
        }

        return $status;
    }

