<?php  // $Id$

/// Library of function for module quiz

/// CONSTANTS ///////////////////////////////////////////////////////////////////

define("QUIZ_MAX_EVENT_LENGTH", "432000");   // 5 days maximum

/// FUNCTIONS ///////////////////////////////////////////////////////////////////

function quiz_add_instance($quiz) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.

    global $SESSION;

    unset($SESSION->modform);

    $quiz->created      = time();
    $quiz->timemodified = time();
    $quiz->timeopen = make_timestamp($quiz->openyear, $quiz->openmonth, $quiz->openday,
                                     $quiz->openhour, $quiz->openminute, 0);
    $quiz->timeclose = make_timestamp($quiz->closeyear, $quiz->closemonth, $quiz->closeday,
                                      $quiz->closehour, $quiz->closeminute, 0);

    if (!$quiz->id = insert_record("quiz", $quiz)) {
        return false;  // some error occurred
    }
    
    if (isset($quiz->optionsettingspref)) {
        set_user_preference('quiz_optionsettingspref', $quiz->optionsettingspref);
    }

    // The grades for every question in this quiz are stored in an array
    // (because this is currently only called from mod.html there are not
    // going to be any grades, but we will leave this code here just in case)
    if (isset($quiz->grades)) {
        foreach ($quiz->grades as $question => $grade) {
            if ($question) {
                unset($questiongrade);
                $questiongrade->quiz = $quiz->id;
                $questiongrade->question = $question;
                $questiongrade->grade = $grade;
                if (!insert_record("quiz_question_grades", $questiongrade)) {
                    return false;
                }
            }
        }
    }

    delete_records('event', 'modulename', 'quiz', 'instance', $quiz->id);  // Just in case

    $event = NULL;
    $event->name        = $quiz->name;
    $event->description = $quiz->intro;
    $event->courseid    = $quiz->course;
    $event->groupid     = 0;
    $event->userid      = 0;
    $event->modulename  = 'quiz';
    $event->instance    = $quiz->id;
    $event->eventtype   = 'open';
    $event->timestart   = $quiz->timeopen;
    $event->visible     = instance_is_visible('quiz', $quiz);
    $event->timeduration = ($quiz->timeclose - $quiz->timeopen);

    if ($event->timeduration > QUIZ_MAX_EVENT_LENGTH) {  /// Long durations create two events
        $event2 = $event;

        $event->name         .= ' ('.get_string('quizopens', 'quiz').')';
        $event->timeduration  = 0;

        $event2->timestart    = $quiz->timeclose;
        $event2->eventtype    = 'close';
        $event2->timeduration = 0;
        $event2->name        .= ' ('.get_string('quizcloses', 'quiz').')';

        add_event($event2);
    }

    add_event($event);

    return $quiz->id;
}


function quiz_update_instance($quiz) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html or edit.php) this function
/// will update an existing instance with new data.

    global $SESSION;

    unset($SESSION->modform);

    $quiz->timemodified = time();
    if (isset($quiz->openyear)) { // this would not be set if we come from edit.php
        $quiz->timeopen = make_timestamp($quiz->openyear, $quiz->openmonth, $quiz->openday,
                                         $quiz->openhour, $quiz->openminute, 0);
        $quiz->timeclose = make_timestamp($quiz->closeyear, $quiz->closemonth, $quiz->closeday,
                                          $quiz->closehour, $quiz->closeminute, 0);
    }
    $quiz->id = $quiz->instance;

    if (!update_record("quiz", $quiz)) {
        return false;  // some error occurred
    }

    if (isset($quiz->optionsettingspref)) {
        set_user_preference('quiz_optionsettingspref', $quiz->optionsettingspref);
    }

    // The grades for every question in this quiz are stored in an array
    // Insert or update records as appropriate

    $existing = get_records("quiz_question_grades", "quiz", $quiz->id, "", "question,grade,id");

    if (isset($quiz->grades)) { // this will not be set if we come from mod.html
        foreach ($quiz->grades as $question => $grade) {
            if ($question) {
                unset($questiongrade);
                $questiongrade->quiz = $quiz->id;
                $questiongrade->question = $question;
                $questiongrade->grade = $grade;
                if (isset($existing[$question])) {
                    if ($existing[$question]->grade != $grade) {
                        $questiongrade->id = $existing[$question]->id;
                        if (!update_record("quiz_question_grades", $questiongrade)) {
                            return false;
                        }
                    }
                } else {
                    if (!insert_record("quiz_question_grades", $questiongrade)) {
                        return false;
                    }
                }
            }
        }
    }

    // currently this code deletes all existing events and adds new ones
    // this should be improved to update existing events only
    if ($events = get_records_select('event', "modulename = 'quiz' and instance = '$quiz->id'")) {
        foreach($events as $event) {
            delete_event($event->id);
        }
    }
    
    unset($event);
    $event->description = $quiz->intro;
    $event->courseid    = $quiz->course;
    $event->groupid     = 0;
    $event->userid      = 0;
    $event->modulename  = 'quiz';
    $event->instance    = $quiz->id;
    $event->eventtype   = 'open';
    $event->timestart   = $quiz->timeopen;
    $event->visible     = instance_is_visible('quiz', $quiz);
    $event->timeduration = ($quiz->timeclose - $quiz->timeopen);

    if ($event->timeduration > QUIZ_MAX_EVENT_LENGTH) {  /// Long durations create two events

        $event->name          = $quiz->name.' ('.get_string('quizopens', 'quiz').')';
        $event->timeduration  = 0;
        add_event($event);

        $event->timestart    = $quiz->timeclose;
        $event->eventtype    = 'close';
        $event->name         = $quiz->name.' ('.get_string('quizcloses', 'quiz').')';
        unset($event->id);
        add_event($event);
    } else { // single event with duration
        $event->name        = $quiz->name;
        add_event($event);
    }

    return true;
}


function quiz_delete_instance($id) {
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.

    if (! $quiz = get_record("quiz", "id", "$id")) {
        return false;
    }

    $result = true;

    if ($attempts = get_records("quiz_attempts", "quiz", "$quiz->id")) {
        foreach ($attempts as $attempt) {
            if (! delete_records("quiz_responses", "attempt", "$attempt->id")) {
                $result = false;
            }
        }
    }

    if (! delete_records("quiz_attempts", "quiz", "$quiz->id")) {
        $result = false;
    }

    if (! delete_records("quiz_grades", "quiz", "$quiz->id")) {
        $result = false;
    }

    if (! delete_records("quiz_question_grades", "quiz", "$quiz->id")) {
        $result = false;
    }

    if (! delete_records("quiz", "id", "$quiz->id")) {
        $result = false;
    }

    if ($events = get_records_select('event', "modulename = 'quiz' and instance = '$quiz->id'")) {
        foreach($events as $event) {
            delete_event($event->id);
        }
    }

    return $result;
}

function quiz_delete_course($course) {
/// Given a course object, this function will clean up anything that
/// would be leftover after all the instances were deleted
/// In this case, all non-publish quiz categories and questions

    if ($categories = get_records_select("quiz_categories", "course = '$course->id' AND publish = '0'")) {
        foreach ($categories as $category) {
            if ($questions = get_records("quiz_questions", "category", $category->id)) {
                foreach ($questions as $question) {
                    delete_records("quiz_answers", "question", $question->id);
                    delete_records("quiz_match", "question", $question->id);
                    delete_records("quiz_match_sub", "question", $question->id);
                    delete_records("quiz_multianswers", "question", $question->id);
                    delete_records("quiz_multichoice", "question", $question->id);
                    delete_records("quiz_numerical", "question", $question->id);
                    delete_records("quiz_randommatch", "question", $question->id);
                    delete_records("quiz_responses", "question", $question->id);
                    delete_records("quiz_shortanswer", "question", $question->id);
                    delete_records("quiz_truefalse", "question", $question->id);
                }
                delete_records("quiz_questions", "category", $category->id);
            }
        }
        return delete_records("quiz_categories", "course", $course->id);
    }
    return true;
}


function quiz_user_outline($course, $user, $mod, $quiz) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description
    if ($grade = get_record("quiz_grades", "userid", $user->id, "quiz", $quiz->id)) {

        if ($grade->grade) {
            $result->info = get_string("grade").": $grade->grade";
        }
        $result->time = $grade->timemodified;
        return $result;
    }
    return NULL;

    return $return;
}

function quiz_user_complete($course, $user, $mod, $quiz) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.

    return true;
}

function quiz_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

    global $CFG;

    return true;
}

function quiz_grades($quizid) {
/// Must return an array of grades, indexed by user, and a max grade.

    $quiz = get_record("quiz", "id", $quizid);
    if (empty($quiz) or empty($quiz->grade)) {
        return NULL;
    }

    $return->grades = get_records_menu("quiz_grades", "quiz", $quizid, "", "userid,grade");
    $return->maxgrade = get_field("quiz", "grade", "id", "$quizid");
    return $return;
}

function quiz_get_participants($quizid) {
/// Returns an array of users who have data in a given quiz
/// (users with records in quiz_attempts, students)

    global $CFG;

    return get_records_sql("SELECT DISTINCT u.*
                            FROM {$CFG->prefix}user u,
                                 {$CFG->prefix}quiz_attempts a
                            WHERE a.quiz = '$quizid' and
                                  u.id = a.userid");
}

function quiz_refresh_events($courseid = 0) {
// This standard function will check all instances of this module
// and make sure there are up-to-date events created for each of them.
// If courseid = 0, then every quiz event in the site is checked, else
// only quiz events belonging to the course specified are checked.
// This function is used, in its new format, by restore_refresh_events()

    if ($courseid == 0) {
        if (! $quizzes = get_records("quiz")) {
            return true;
        }
    } else {
        if (! $quizzes = get_records("quiz", "course", $courseid)) {
            return true;
        }
    }
    $moduleid = get_field('modules', 'id', 'name', 'quiz');

    foreach ($quizzes as $quiz) {
        $event = NULL;
        $event2 = NULL;
        $event2old = NULL;

        if ($events = get_records_select('event', "modulename = 'quiz' AND instance = '$quiz->id' ORDER BY timestart")) {
            $event = array_shift($events);
            if (!empty($events)) {
                $event2old = array_shift($events);
                if (!empty($events)) {
                    foreach ($events as $badevent) {
                        delete_records('event', 'id', $badevent->id);
                    }
                }
            }
        }

        $event->name        = addslashes($quiz->name);
        $event->description = addslashes($quiz->intro);
        $event->courseid    = $quiz->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'quiz';
        $event->instance    = $quiz->id;
        $event->visible     = instance_is_visible('quiz', $quiz);
        $event->timestart   = $quiz->timeopen;
        $event->eventtype   = 'open';
        $event->timeduration = ($quiz->timeclose - $quiz->timeopen);

        if ($event->timeduration > QUIZ_MAX_EVENT_LENGTH) {  /// Set up two events

            $event2 = $event;

            $event->name         = addslashes($quiz->name).' ('.get_string('quizopens', 'quiz').')';
            $event->timeduration = 0;

            $event2->name        = addslashes($quiz->name).' ('.get_string('quizcloses', 'quiz').')';
            $event2->timestart   = $quiz->timeclose;
            $event2->eventtype   = 'close';
            $event2->timeduration = 0;

            if (empty($event2old->id)) {
                unset($event2->id);
                add_event($event2);
            } else {
                $event2->id = $event2old->id;
                update_event($event2);
            }
        } else if (!empty($event2->id)) {
            delete_event($event2->id);
        }

        if (empty($event->id)) {
            add_event($event);
        } else {
            update_event($event);
        }

    }
    return true;
}

?>
