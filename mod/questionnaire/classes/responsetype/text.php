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

namespace mod_questionnaire\responsetype;

use mod_questionnaire\db\bulk_sql_config;

/**
 * Class for text response types.
 * @author Mike Churchward
 * @copyright 2016 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 */
class text extends responsetype {
    /**
     * Provide the necessary response data table name. Should probably always be used with late static binding 'static::' form
     * rather than 'self::' form to allow for class extending.
     *
     * @return string response table name.
     */
    public static function response_table() {
        return 'questionnaire_response_text';
    }

    /**
     * Provide an array of answer objects from web form data for the question.
     *
     * @param \stdClass $responsedata All of the responsedata as an object.
     * @param \mod_questionnaire\question\question $question
     * @return array \mod_questionnaire\responsetype\answer\answer An array of answer objects.
     */
    public static function answers_from_webform($responsedata, $question) {
        $answers = [];
        if (isset($responsedata->{'q'.$question->id}) && (strlen($responsedata->{'q'.$question->id}) > 0)) {
            $val = $responsedata->{'q' . $question->id};
            $record = new \stdClass();
            $record->responseid = $responsedata->rid;
            $record->questionid = $question->id;
            $record->value = $val;
            $answers[] = answer\answer::create_from_data($record);
        }
        return $answers;
    }

    /**
     * Insert a provided response to the question.
     *
     * @param object $responsedata All of the responsedata as an object.
     * @return int|bool - on error the subtype should call set_error and return false.
     */
    public function insert_response($responsedata) {
        global $DB;

        if (!$responsedata instanceof \mod_questionnaire\responsetype\response\response) {
            $response = \mod_questionnaire\responsetype\response\response::response_from_webform($responsedata, [$this->question]);
        } else {
            $response = $responsedata;
        }

        if (!empty($response) && isset($response->answers[$this->question->id][0])) {
            $record = new \stdClass();
            $record->response_id = $response->id;
            $record->question_id = $this->question->id;
            $record->response = clean_text($response->answers[$this->question->id][0]->value);
            return $DB->insert_record(static::response_table(), $record);
        } else {
            return false;
        }
    }

    /**
     * Provide the result information for the specified result records.
     *
     * @param int|array $rids - A single response id, or array.
     * @param boolean $anonymous - Whether or not responses are anonymous.
     * @return array - Array of data records.
     */
    public function get_results($rids=false, $anonymous=false) {
        global $DB;

        $rsql = '';
        if (!empty($rids)) {
            list($rsql, $params) = $DB->get_in_or_equal($rids);
            $rsql = ' AND response_id ' . $rsql;
        }

        if ($anonymous) {
            $sql = 'SELECT t.id, t.response, r.submitted AS submitted, ' .
                    'r.questionnaireid, r.id AS rid ' .
                    'FROM {'.static::response_table().'} t, ' .
                    '{questionnaire_response} r ' .
                    'WHERE question_id=' . $this->question->id . $rsql .
                    ' AND t.response_id = r.id ' .
                    'ORDER BY r.submitted DESC';
        } else {
            $sql = 'SELECT t.id, t.response, r.submitted AS submitted, r.userid, u.username AS username, ' .
                    'u.id as usrid, ' .
                    'r.questionnaireid, r.id AS rid ' .
                    'FROM {'.static::response_table().'} t, ' .
                    '{questionnaire_response} r, ' .
                    '{user} u ' .
                    'WHERE question_id=' . $this->question->id . $rsql .
                    ' AND t.response_id = r.id' .
                    ' AND u.id = r.userid ' .
                    'ORDER BY u.lastname, u.firstname, r.submitted';
        }
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Provide a template for results screen if defined.
     * @param bool $pdf
     * @return mixed The template string or false/
     */
    public function results_template($pdf = false) {
        if ($pdf) {
            return 'mod_questionnaire/resultspdf_text';
        } else {
            return 'mod_questionnaire/results_text';
        }
    }

    /**
     * Provide the result information for the specified result records.
     *
     * @param int|array $rids - A single response id, or array.
     * @param string $sort - Optional display sort.
     * @param boolean $anonymous - Whether or not responses are anonymous.
     * @return string - Display output.
     */
    public function display_results($rids=false, $sort='', $anonymous=false) {
        if (is_array($rids)) {
            $prtotal = 1;
        } else if (is_int($rids)) {
            $prtotal = 0;
        }
        if ($rows = $this->get_results($rids, $anonymous)) {
            $numrespondents = count($rids);
            $numresponses = count($rows);
            $pagetags = $this->get_results_tags($rows, $numrespondents, $numresponses, $prtotal);
        } else {
            $pagetags = new \stdClass();
        }
        return $pagetags;
    }

    /**
     * Gets the results tags for templates for questions with defined choices (single, multiple, boolean).
     *
     * @param array $weights
     * @param int $participants Number of questionnaire participants.
     * @param int $respondents Number of question respondents.
     * @param int $showtotals
     * @param string $sort
     * @return \stdClass
     */
    public function get_results_tags($weights, $participants, $respondents, $showtotals = 1, $sort = '') {
        $pagetags = new \stdClass();
        if ($respondents == 0) {
            return $pagetags;
        }

        // If array element is an object, outputting non-numeric responses.
        if (is_object(reset($weights))) {
            global $CFG, $SESSION, $questionnaire, $DB;
            $viewsingleresponse = $questionnaire->capabilities->viewsingleresponse;
            $nonanonymous = $questionnaire->respondenttype != 'anonymous';
            if ($viewsingleresponse && $nonanonymous) {
                $currentgroupid = '';
                if (isset($SESSION->questionnaire->currentgroupid)) {
                    $currentgroupid = $SESSION->questionnaire->currentgroupid;
                }
                $url = $CFG->wwwroot.'/mod/questionnaire/report.php?action=vresp&amp;sid='.$questionnaire->survey->id.
                    '&currentgroupid='.$currentgroupid;
            }
            $users = [];
            $evencolor = false;
            foreach ($weights as $row) {
                $response = new \stdClass();
                $response->text = format_text($row->response, FORMAT_HTML);
                if ($viewsingleresponse && $nonanonymous) {
                    $rurl = $url.'&amp;rid='.$row->rid.'&amp;individualresponse=1';
                    $title = userdate($row->submitted);
                    if (!isset($users[$row->userid])) {
                        $users[$row->userid] = $DB->get_record('user', ['id' => $row->userid]);
                    }
                    $response->respondent = '<a href="'.$rurl.'" title="'.$title.'">'.fullname($users[$row->userid]).'</a>';
                } else {
                    $response->respondent = '';
                }
                // The 'evencolor' attribute is used by the PDF template.
                $response->evencolor = $evencolor;
                $pagetags->responses[] = (object)['response' => $response];
                $evencolor = !$evencolor;
            }

            if ($showtotals == 1) {
                $pagetags->total = new \stdClass();
                $pagetags->total->total = "$respondents/$participants";
            }
        } else {
            $nbresponses = 0;
            $sum = 0;
            $strtotal = get_string('totalofnumbers', 'questionnaire');
            $straverage = get_string('average', 'questionnaire');

            if (!empty($weights) && is_array($weights)) {
                ksort($weights);
                $evencolor = false;
                foreach ($weights as $text => $num) {
                    $response = new \stdClass();
                    $response->text = $text;
                    $response->respondent = $num;
                    // The 'evencolor' attribute is used by the PDF template.
                    $response->evencolor = $evencolor;
                    $nbresponses += $num;
                    $sum += $text * $num;
                    $evencolor = !$evencolor;
                    $pagetags->responses[] = (object)['response' => $response];
                }

                $response = new \stdClass();
                $response->text = $sum;
                $response->respondent = $strtotal;
                $response->evencolor = $evencolor;
                $pagetags->responses[] = (object)['response' => $response];
                $evencolor = !$evencolor;

                $response = new \stdClass();
                $response->respondent = $straverage;
                $avg = $sum / $nbresponses;
                $response->text = sprintf('%.' . $this->question->precise . 'f', $avg);
                $response->evencolor = $evencolor;
                $pagetags->responses[] = (object)['response' => $response];
                $evencolor = !$evencolor;

                if ($showtotals == 1) {
                    $pagetags->total = new \stdClass();
                    $pagetags->total->total = "$respondents/$participants";
                    $pagetags->total->evencolor = $evencolor;
                }
            }
        }

        return $pagetags;
    }

    /**
     * Return an array of answers by question/choice for the given response. Must be implemented by the subclass.
     *
     * @param int $rid The response id.
     * @return array
     */
    public static function response_select($rid) {
        global $DB;

        $values = [];
        $sql = 'SELECT q.id, q.content, a.response as aresponse '.
            'FROM {'.static::response_table().'} a, {questionnaire_question} q '.
            'WHERE a.response_id=? AND a.question_id=q.id ';
        $records = $DB->get_records_sql($sql, [$rid]);
        foreach ($records as $qid => $row) {
            unset($row->id);
            $row = (array)$row;
            $newrow = [];
            foreach ($row as $key => $val) {
                if (!is_numeric($key)) {
                    $newrow[] = $val;
                }
            }
            $values[$qid] = $newrow;
            $val = array_pop($values[$qid]);
            array_push($values[$qid], $val, $val);
        }

        return $values;
    }

    /**
     * Return an array of answer objects by question for the given response id.
     * THIS SHOULD REPLACE response_select.
     *
     * @param int $rid The response id.
     * @return array array answer
     * @throws \dml_exception
     */
    public static function response_answers_by_question($rid) {
        global $DB;

        $answers = [];
        $sql = 'SELECT id, response_id as responseid, question_id as questionid, 0 as choiceid, response as value ' .
            'FROM {' . static::response_table() .'} ' .
            'WHERE response_id = ? ';
        $records = $DB->get_records_sql($sql, [$rid]);
        foreach ($records as $record) {
            $answers[$record->questionid][] = answer\answer::create_from_data($record);
        }

        return $answers;
    }

    /**
     * Configure bulk sql
     * @return bulk_sql_config
     */
    protected function bulk_sql_config() {
        return new bulk_sql_config(static::response_table(), 'qrt', false, true, false);
    }
}

