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

use coding_exception;
use dml_exception;
use mod_questionnaire\db\bulk_sql_config;
use stdClass;

/**
 * Class for boolean response types.
 *
 * @author Mike Churchward
 * @copyright 2016 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 */
class boolean extends responsetype {

    /**
     * Provide the necessary response data table name. Should probably always be used with late static binding 'static::' form
     * rather than 'self::' form to allow for class extending.
     *
     * @return string response table name.
     */
    public static function response_table() {
        return 'questionnaire_response_bool';
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
        if (isset($responsedata->{'q'.$question->id}) && !empty($responsedata->{'q'.$question->id})) {
            $record = new \stdClass();
            $record->responseid = $responsedata->rid;
            $record->questionid = $question->id;
            $record->choiceid = $responsedata->{'q' . $question->id};
            $record->value = $responsedata->{'q' . $question->id};
            $answers[] = answer\answer::create_from_data($record);
        }
        return $answers;
    }

    /**
     * Provide an array of answer objects from mobile data for the question.
     *
     * @param \stdClass $responsedata All of the responsedata as an object.
     * @param \mod_questionnaire\question\question $question
     * @return array \mod_questionnaire\responsetype\answer\answer An array of answer objects.
     */
    public static function answers_from_appdata($responsedata, $question) {
        if (isset($responsedata->{'q'.$question->id}) && !empty($responsedata->{'q'.$question->id})) {
            $responsedata->{'q'.$question->id} = ($responsedata->{'q'.$question->id}[0] == 1) ? 'y' : 'n';
        }
        return static::answers_from_webform($responsedata, $question);
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
            $record->choice_id = $response->answers[$this->question->id][0]->choiceid;
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
        $params = array($this->question->id);
        if (!empty($rids)) {
            list($rsql, $rparams) = $DB->get_in_or_equal($rids);
            $params = array_merge($params, $rparams);
            $rsql = ' AND response_id ' . $rsql;
        }
        $params[] = '';

        $sql = 'SELECT choice_id, COUNT(response_id) AS num ' .
               'FROM {'.static::response_table().'} ' .
               'WHERE question_id= ? ' . $rsql . ' AND choice_id != ? ' .
               'GROUP BY choice_id';
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * If the choice id needs to be transformed into a different value, override this in the child class.
     * @param int $choiceid
     * @return string
     */
    public function transform_choiceid($choiceid) {
        if ($choiceid == 0) {
            $choice = 'y';
        } else {
            $choice = 'n';
        }
        return $choice;
    }

    /**
     * Provide the feedback scores for all requested response id's. This should be provided only by questions that provide feedback.
     * @param array $rids
     * @return array | boolean
     */
    public function get_feedback_scores(array $rids) {
        global $DB;

        $rsql = '';
        $params = [$this->question->id];
        if (!empty($rids)) {
            list($rsql, $rparams) = $DB->get_in_or_equal($rids);
            $params = array_merge($params, $rparams);
            $rsql = ' AND response_id ' . $rsql;
        }
        $params[] = 'y';

        $feedbackscores = false;
        $sql = 'SELECT response_id, choice_id ' .
            'FROM {'.$this->response_table().'} ' .
            'WHERE question_id= ? ' . $rsql . ' ' .
            'ORDER BY response_id ASC';
        if ($responses = $DB->get_recordset_sql($sql, $params)) {
            $feedbackscores = [];
            foreach ($responses as $rid => $response) {
                $feedbackscores[$rid] = new stdClass();
                $feedbackscores[$rid]->rid = $rid;
                $feedbackscores[$rid]->score = ($response->choice_id == 'y') ? 1 : 0;
            }
        }
        return $feedbackscores;
    }

    /**
     * Provide a template for results screen if defined.
     * @param bool $pdf
     * @return mixed The template string or false/
     */
    public function results_template($pdf = false) {
        if ($pdf) {
            return 'mod_questionnaire/resultspdf_choice';
        } else {
            return 'mod_questionnaire/results_choice';
        }
    }

    /**
     * Return the JSON structure required for the template.
     *
     * @param bool $rids
     * @param string $sort
     * @param bool $anonymous
     * @return string
     */
    public function display_results($rids=false, $sort='', $anonymous=false) {
        $stryes = get_string('yes');
        $strno = get_string('no');

        if (is_array($rids)) {
            $prtotal = 1;
        } else if (is_int($rids)) {
            $prtotal = 0;
        }
        $numresps = count($rids);

        $counts = [$stryes => 0, $strno => 0];
        $numrespondents = 0;
        if ($rows = $this->get_results($rids, $anonymous)) {
            foreach ($rows as $row) {
                $choice = $row->choice_id;
                $count = $row->num;
                if ($choice == 'y') {
                    $choice = $stryes;
                } else {
                    $choice = $strno;
                }
                $counts[$choice] = intval($count);
                $numrespondents += $counts[$choice];
            }
            $pagetags = $this->get_results_tags($counts, $numresps, $numrespondents, $prtotal, '');
        } else {
            $pagetags = new stdClass();
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
        $sql = 'SELECT q.id, q.content, a.choice_id '.
            'FROM {'.static::response_table().'} a, {questionnaire_question} q '.
            'WHERE a.response_id= ? AND a.question_id=q.id ';
        $records = $DB->get_records_sql($sql, [$rid]);
        foreach ($records as $qid => $row) {
            $choice = $row->choice_id;
            unset ($row->id);
            unset ($row->choice_id);
            $row = (array)$row;
            $newrow = [];
            foreach ($row as $key => $val) {
                if (!is_numeric($key)) {
                    $newrow[] = $val;
                }
            }
            $values[$qid] = $newrow;
            array_push($values[$qid], ($choice == 'y') ? '1' : '0');
            array_push($values[$qid], $choice); // DEV still needed for responses display.
        }

        return $values;
    }

    /**
     * Return an array of answer objects by question for the given response id.
     * THIS SHOULD REPLACE response_select.
     *
     * @param int $rid The response id.
     * @return array array answer
     * @throws dml_exception
     */
    public static function response_answers_by_question($rid) {
        global $DB;

        $answers = [];
        $sql = 'SELECT id, response_id as responseid, question_id as questionid, choice_id as choiceid, choice_id as value ' .
            'FROM {' . static::response_table() .'} ' .
            'WHERE response_id = ? ';
        $records = $DB->get_records_sql($sql, [$rid]);
        foreach ($records as $record) {
            $record->choiceid = ($record->choiceid == 'y') ? 1 : 0;
            $answers[$record->questionid][] = answer\answer::create_from_data($record);
        }

        return $answers;
    }

    /**
     * Configure bulk sql
     * @return bulk_sql_config
     */
    protected function bulk_sql_config() {
        return new bulk_sql_config(static::response_table(), 'qrb', true, false, false);
    }

    /**
     * Return sql for getting responses in bulk.
     * @author Guy Thomas
     * @author Mike Churchward
     * @return string
     */
    protected function bulk_sql() {
        global $DB;

        $userfields = $this->user_fields_sql();
        // Postgres requires all fields to be the same type. Boolean type returns a character value as "choice_id",
        // while all others are an integer. So put the boolean response in "response" field instead (CONTRIB-6436).
        // NOTE - the actual use of "boolean" should probably change to not use "choice_id" at all, or use it as
        // numeric zero and one instead.
        $alias = 'qrb';
        $extraselect = '0 AS choice_id, ' . $DB->sql_order_by_text('qrb.choice_id', 1000) . ' AS response, 0 AS rankvalue';

        return "
            SELECT " . $DB->sql_concat_join("'_'", ['qr.id', "'".$this->question->helpname()."'", $alias.'.id']) . " AS id,
                   qr.submitted, qr.complete, qr.grade, qr.userid, $userfields, qr.id AS rid, $alias.question_id,
                   $extraselect
              FROM {questionnaire_response} qr
              JOIN {".static::response_table()."} $alias ON $alias.response_id = qr.id
        ";
    }
}

