<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Store results of an attempt at a HotPot quiz
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get the standard XML parser supplied with Moodle
require_once($CFG->dirroot.'/lib/xmlize.php');

/**
 * mod_hotpot_storage
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_storage {

    /**
     * the name of the $_POST fields holding the score and details
     * and the xml tag within the details that holds the results
     */
    const scorefield = 'mark';
    const detailsfield = 'detail';
    const xmlresultstag = 'hpjsresult';

    /**
     * the two fields that will be used to determine the duration of a quiz attempt
     *     starttime/endtime are recorded by the client (and may not be trustworthy)
     *     resumestart/resumefinish are recorded by the server (but include transfer time to and from client)
     */
    const durationstartfield = 'timestart';
    const durationfinishfield = 'timefinish';

    // functions to store responses returned from browser

    /**
     * store
     *
     * @param xxx $hotpot
     */
    static public function store($hotpot)  {
        global $CFG, $DB, $USER;

        if (empty($hotpot->attempt)) {
            return; // no attempt record - shouldn't happen !!
        }

        if ($hotpot->attempt->userid != $USER->id) {
            return; // wrong userid - shouldn't happen !!
        }

        // update quiz attempt fields using incoming data
        $hotpot->attempt->score    = max(0, optional_param(self::scorefield, 0, PARAM_INT));
        $hotpot->attempt->status   = max(0, optional_param('status', 0, PARAM_INT));
        $hotpot->attempt->redirect = max(0, optional_param('redirect', 0, PARAM_INT));
        $hotpot->attempt->details  = optional_param(self::detailsfield, '', PARAM_RAW);

        // update timemodified for this attempt
        $hotpot->attempt->timemodified = $hotpot->time;

        // time values, e.g. "2008-09-12 16:18:18 +0900",
        // need to be converted to numeric date stamps
        $timefields = array('starttime', 'endtime');
        foreach ($timefields as $timefield) {

            $hotpot->attempt->$timefield = 0; // default
            if ($time = optional_param($timefield, '', PARAM_RAW)) {

                // make sure the timezone has a "+" sign
                // Note: sometimes it gets stripped (by optional_param?)
                $time = preg_replace('/(?<= )\d{4}$/', '+$0', trim($time));

                // convert $time to numeric date stamp
                // PHP4 gives -1 on error, whereas PHP5 give false
                $time = strtotime($time);

                if ($time && $time>0) {
                    $hotpot->attempt->$timefield = $time;
                }
            }
        }
        unset($timefields, $timefield, $time);

        // set finish times
        $hotpot->attempt->timefinish = $hotpot->time;

        // increment quiz attempt duration
        $startfield = self::durationstartfield; // "starttime" or "timestart"
        $finishfield = self::durationfinishfield; // "endtime" or "timefinish"
        $duration = ($hotpot->attempt->$finishfield - $hotpot->attempt->$startfield);
        if (empty($hotpot->attempt->duration)) {
            $hotpot->attempt->duration = $duration;
        } else if ($duration > 0) {
            $hotpot->attempt->duration += $duration;
        }
        unset($duration, $startfield, $finishfield);

        // set clickreportid, (for click reporting)
        $hotpot->attempt->clickreportid = $hotpot->attempt->id;

        // check if there are any previous results stored for this attempt
        // this could happen if ...
        //     - the quiz has been resumed
        //     - clickreporting is enabled for this quiz
        if ($DB->get_field('hotpot_attempts', 'timefinish', array('id'=>$hotpot->attempt->id))) {
            if ($hotpot->clickreporting) { // self::can_clickreporting()
                // add quiz attempt record for each form submission
                // records are linked via the "clickreportid" field

                // update timemodified and status in previous records in this clickreportid group
                $DB->set_field('hotpot_attempts', 'timemodified', $hotpot->time, array('clickreportid'=>$hotpot->attempt->clickreportid));
                $DB->set_field('hotpot_attempts', 'status', $hotpot->attempt->status, array('clickreportid'=>$hotpot->attempt->clickreportid));

                // add new attempt record
                unset($hotpot->attempt->id);
                if (! $hotpot->attempt->id = $DB->insert_record('hotpot_attempts', $hotpot->attempt)) {
                    print_error('error_insertrecord', 'hotpot', '', 'hotpot_attempts');
                }

            } else {
                // remove previous responses for this attempt, if required
                // (N.B. this does NOT remove the attempt record, just the responses)
                $DB->delete_records('hotpot_responses', array('attemptid'=>$hotpot->attempt->id));
            }
        }

        // add details of this quiz attempt, if required
        // "hotpot_storedetails" is set by administrator
        // Site Admin -> Modules -> Activities -> HotPot
        if ($CFG->hotpot_storedetails) {

            // delete/update/add the details record
            if ($DB->record_exists('hotpot_details', array('attemptid'=>$hotpot->attempt->id))) {
                $DB->set_field('hotpot_details', 'details', $hotpot->attempt->details, array('attemptid'=>$hotpot->attempt->id));
            } else {
                $details = (object)array(
                    'attemptid' => $hotpot->attempt->id,
                    'details' => $hotpot->attempt->details
                );
                if (! $DB->insert_record('hotpot_details', $details, false)) {
                    print_error('error_insertrecord', 'hotpot', '', 'hotpot_details');
                }
                unset($details);
            }
        }

        // add details of this attempt
        self::store_details($hotpot->attempt);

        // update the attempt record
        if (! $DB->update_record('hotpot_attempts', $hotpot->attempt)) {
            print_error('error_updaterecord', 'hotpot', '', 'hotpot_attempts');
        }

        // regrade the quiz to take account of the latest quiz attempt score
        hotpot_update_grades($hotpot->to_stdclass(), $hotpot->attempt->userid);
    }

    /**
     * pre_xmlize
     *
     * @param xxx $old_string (passed by reference)
     * @return xxx
     */
    static public function pre_xmlize(&$old_string)  {
        $new_string = '';
        $str_start = 0;
        while (($cdata_start = strpos($old_string, '<![CDATA[', $str_start)) && ($cdata_end = strpos($old_string, ']]>', $cdata_start))) {
            $cdata_end += 3;
            $new_string .= str_replace('&', '&amp;', substr($old_string, $str_start, $cdata_start-$str_start)).substr($old_string, $cdata_start, $cdata_end-$cdata_start);
            $str_start = $cdata_end;
        }
        $new_string .= str_replace('&', '&amp;', substr($old_string, $str_start));
        return $new_string;
    }

    /**
     * store_details
     *
     * @param xxx $attempt (passed by reference)
     */
    static public function store_details($attempt)  {

        // encode ampersands so that HTML entities are preserved in the XML parser
        // N.B. ampersands inside <![CDATA[ ]]> blocks do NOT need to be encoded
        // disabled 2008.11.20
        // $attempt->details = self::pre_xmlize($attempt->details);

        // parse the attempt details as xml
        $details = xmlize($attempt->details);
        $question_number; // initially unset
        $question = false;
        $response  = false;

        $i = 0;
        while (isset($details[self::xmlresultstag]['#']['fields']['0']['#']['field'][$i]['#'])) {

            // shortcut to field
            $field = &$details[self::xmlresultstag]['#']['fields']['0']['#']['field'][$i]['#'];

            // extract field name and data
            if (isset($field['fieldname'][0]['#']) && is_string($field['fieldname'][0]['#'])) {
                $name = $field['fieldname'][0]['#'];
            } else {
                $name = '';
            }
            if (isset($field['fielddata'][0]['#']) && is_string($field['fielddata'][0]['#'])) {
                $data = $field['fielddata'][0]['#'];
            } else {
                $data = '';
            }

            // parse the field name into $matches
            //  [1] quiz type
            //  [2] attempt detail name
            if (preg_match('/^(\w+?)_(\w+)$/', $name, $matches)) {
                $quiztype = strtolower($matches[1]);
                $name = strtolower($matches[2]);

                // parse the attempt detail $name into $matches
                //  [1] question number
                //  [2] question detail name
                if (preg_match('/^q(\d+)_(\w+)$/', $name, $matches)) {
                    $num = $matches[1];
                    $name = strtolower($matches[2]);
                    // not needed Moodle 2.0 and later
                    // $data = addslashes($data);

                    // adjust JCross question numbers
                    if (preg_match('/^(across|down)(.*)$/', $name, $matches)) {
                        $num .= '_'.$matches[1]; // e.g. 01_across, 02_down
                        $name = $matches[2];
                        if (substr($name, 0, 1)=='_') {
                            $name = substr($name, 1); // remove leading '_'
                        }
                    }

                    if (isset($question_number) && $question_number==$num) {
                        // do nothing - this response is for the same question as the previous response
                    } else {
                        // store previous question / response (if any)
                        self::add_response($attempt, $question, $response);

                        // initialize question object
                        $question = new stdClass();
                        $question->name = '';
                        $question->text = '';
                        $question->hotpotid = $attempt->hotpotid;

                        // initialize response object
                        $response = new stdClass();
                        $response->attemptid = $attempt->id;

                        // update question number
                        $question_number = $num;
                    }

                    // adjust field name and value, and set question type
                    // (may not be necessary one day)
                    // hotpot_adjust_response_field($quiztype, $question, $num, $name, $data);

                    // add $data to the question/response details
                    switch ($name) {
                        case 'name':
                        case 'type':
                            $question->$name = $data;
                            break;
                        case 'text':
                            $question->$name = hotpot::string_id($data);
                            break;

                        case 'correct':
                        case 'ignored':
                        case 'wrong':
                            $response->$name = hotpot::string_ids($data);
                            break;

                        case 'score':
                        case 'weighting':
                        case 'hints':
                        case 'clues':
                        case 'checks':
                            $response->$name = intval($data);
                            break;
                    }

                } else { // attempt details

                    // adjust field name and value
                    //hotpot_adjust_response_field($quiztype, $question, $num='', $name, $data);

                    // add $data to the attempt details
                    if ($name=='penalties') {
                        $attempt->$name = intval($data);
                    }
                }
            }

            $i++;
        } // end while

        // add the final question and response, if any
        self::add_response($attempt, $question, $response);
    }

    /**
     * add_response
     *
     * @param xxx $attempt (passed by reference)
     * @param xxx $question (passed by reference)
     * @param xxx $response (passed by reference)
     */
    static public function add_response(&$attempt, &$question, &$response)  {
        global $DB;

        if (! $question || ! $response || ! isset($question->name)) {
            // nothing to add
            return;
        }

        $loopcount = 1;
        $questionname = $question->name;

        // loop until we are able to add the response record
        $looping = true;
        while ($looping) {

            $question->md5key = md5($question->name);
            if (! $question->id = $DB->get_field('hotpot_questions', 'id', array('hotpotid'=>$attempt->hotpotid, 'md5key'=>$question->md5key))) {
                // add question record
                if (! $question->id = $DB->insert_record('hotpot_questions', $question)) {
                    print_error('error_insertrecord', 'hotpot', '', 'hotpot_questions');
                }
            }

            if ($DB->record_exists('hotpot_responses', array('attemptid'=>$attempt->id, 'questionid'=>$question->id))) {
                // there is already a response to this question for this attempt
                // probably because this quiz has two questions with the same text
                //  e.g. Which one of these answers is correct?

                // To workaround this, we create new question names
                //  e.g. Which one of these answers is correct? (2)
                // until we get a question name for which there is no response yet on this attempt

                $loopcount++;
                $question->name = "$questionname ($loopcount)";

                // This method fails to correctly identify questions in
                // quizzes which allow questions to be shuffled or omitted.
                // As yet, there is no workaround for such cases.

            } else {
                // no response found to this question in this attempt
                // so we can proceed
                $response->questionid = $question->id;

                // add response record
                if(! $response->id = $DB->insert_record('hotpot_responses', $response)) {
                    print_error('error_insertrecord', 'hotpot', '', 'hotpot_responses');
                }
                $looping = false;
            }

        } // end while
    }
}
