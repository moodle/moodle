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

/**
 * Class for single response types.
 *
 * @author Mike Churchward
 * @copyright 2016 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 */
class single extends responsetype {
    /**
     * Provide the necessary response data table name. Should probably always be used with late static binding 'static::' form
     * rather than 'self::' form to allow for class extending.
     *
     * @return string response table name.
     */
    public static function response_table() {
        return 'questionnaire_resp_single';
    }

    /**
     * Provide an array of answer objects from web form data for the question.
     *
     * @param \stdClass $responsedata All of the responsedata as an object.
     * @param \mod_questionnaire\question\question $question
     * @return array \mod_questionnaire\responsetype\answer\answer An array of answer objects.
     * @throws \coding_exception
     */
    public static function answers_from_webform($responsedata, $question) {
        $answers = [];
        if (isset($responsedata->{'q'.$question->id}) && isset($question->choices[$responsedata->{'q'.$question->id}])) {
            $record = new \stdClass();
            $record->responseid = $responsedata->rid;
            $record->questionid = $question->id;
            $record->choiceid = $responsedata->{'q'.$question->id};
            // If this choice is an "other" choice, look for the added input.
            if ($question->choices[$responsedata->{'q'.$question->id}]->is_other_choice()) {
                $cname = 'q' . $question->id .
                    \mod_questionnaire\question\choice::id_other_choice_name($responsedata->{'q'.$question->id});
                $record->value = isset($responsedata->{$cname}) ? $responsedata->{$cname} : '';
            }
            $answers[$responsedata->{'q'.$question->id}] = answer\answer::create_from_data($record);
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
        $answers = [];
        $qname = 'q'.$question->id;
        if (isset($responsedata->{$qname}[0]) && !empty($responsedata->{$qname}[0])) {
            $record = new \stdClass();
            $record->responseid = $responsedata->rid;
            $record->questionid = $question->id;
            $record->choiceid = $responsedata->{$qname}[0];
            // If this choice is an "other" choice, look for the added input.
            if ($question->choices[$record->choiceid]->is_other_choice()) {
                $cname = \mod_questionnaire\question\choice::id_other_choice_name($record->choiceid);
                $record->value =
                    isset($responsedata->{$qname}[$cname]) ? $responsedata->{$qname}[$cname] : '';
            } else {
                $record->value = '';
            }
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

        $resid = false;
        if (!empty($response) && isset($response->answers[$this->question->id])) {
            foreach ($response->answers[$this->question->id] as $answer) {
                if (isset($this->question->choices[$answer->choiceid])) {
                    if ($this->question->choices[$answer->choiceid]->is_other_choice()) {
                        // If no input specified, ignore this choice.
                        if (empty($answer->value) || preg_match("/^[\s]*$/", $answer->value)) {
                            continue;
                        }
                        $record = new \stdClass();
                        $record->response_id = $response->id;
                        $record->question_id = $this->question->id;
                        $record->choice_id = $answer->choiceid;
                        $record->response = clean_text($answer->value);
                        $DB->insert_record('questionnaire_response_other', $record);
                    }
                    // Record the choice selection.
                    $record = new \stdClass();
                    $record->response_id = $response->id;
                    $record->question_id = $this->question->id;
                    $record->choice_id = $answer->choiceid;
                    $resid = $DB->insert_record(static::response_table(), $record);
                }
            }
        }
        return $resid;
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

        // Added qc.id to preserve original choices ordering.
        $sql = 'SELECT rt.id, qc.id as cid, qc.content ' .
               'FROM {questionnaire_quest_choice} qc, ' .
               '{'.static::response_table().'} rt ' .
               'WHERE qc.question_id= ? AND qc.content NOT LIKE \'!other%\' AND ' .
                     'rt.question_id=qc.question_id AND rt.choice_id=qc.id' . $rsql . ' ' .
               'ORDER BY qc.id';

        $rows = $DB->get_records_sql($sql, $params);

        // Handle 'other...'.
        $sql = 'SELECT rt.id, rt.response, qc.content ' .
               'FROM {questionnaire_response_other} rt, ' .
                    '{questionnaire_quest_choice} qc ' .
               'WHERE rt.question_id= ? AND rt.choice_id=qc.id' . $rsql . ' ' .
               'ORDER BY qc.id';

        if ($recs = $DB->get_records_sql($sql, $params)) {
            $i = 1;
            foreach ($recs as $rec) {
                $rows['other'.$i] = new \stdClass();
                $rows['other'.$i]->content = $rec->content;
                $rows['other'.$i]->response = $rec->response;
                $i++;
            }
        }

        return $rows;
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

        $sql = 'SELECT response_id as rid, c.value AS score ' .
            'FROM {'.$this->response_table().'} r ' .
            'INNER JOIN {questionnaire_quest_choice} c ON r.choice_id = c.id ' .
            'WHERE r.question_id= ? ' . $rsql . ' ' .
            'ORDER BY response_id ASC';
        return $DB->get_records_sql($sql, $params);
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
        global $DB;

        $rows = $this->get_results($rids, $anonymous);
        if (is_array($rids)) {
            $prtotal = 1;
        } else if (is_int($rids)) {
            $prtotal = 0;
        }
        $numresps = count($rids);

        $responsecountsql = 'SELECT COUNT(DISTINCT r.response_id) ' .
            'FROM {' . $this->response_table() . '} r ' .
            'WHERE r.question_id = ? ';
        $numrespondents = $DB->count_records_sql($responsecountsql, [$this->question->id]);

        if ($rows) {
            $counts = [];
            foreach ($rows as $idx => $row) {
                if (strpos($idx, 'other') === 0) {
                    $answer = $row->response;
                    $ccontent = $row->content;
                    $content = \mod_questionnaire\question\choice::content_other_choice_display($ccontent);
                    $content .= ' ' . clean_text($answer);
                    $textidx = $content;
                    $counts[$textidx] = !empty($counts[$textidx]) ? ($counts[$textidx] + 1) : 1;
                } else {
                    $contents = questionnaire_choice_values($row->content);
                    $textidx = $contents->text.$contents->image;
                    $counts[$textidx] = !empty($counts[$textidx]) ? ($counts[$textidx] + 1) : 1;
                }
            }
            $pagetags = $this->get_results_tags($counts, $numresps, $numrespondents, $prtotal, $sort);
        } else {
            $pagetags = new \stdClass();
        }
        return $pagetags;
    }

    /**
     * Return an array of answers by question/choice for the given response. Must be implemented by the subclass.
     * Array is indexed by question, and contains an array by choice code of selected choices.
     *
     * @param int $rid The response id.
     * @return array
     */
    public static function response_select($rid) {
        global $DB;

        $values = [];
        $sql = 'SELECT a.id, q.id as qid, q.content, c.content as ccontent, c.id as cid, o.response ' .
            'FROM {'.static::response_table().'} a ' .
            'INNER JOIN {questionnaire_question} q ON a.question_id = q.id ' .
            'INNER JOIN {questionnaire_quest_choice} c ON a.choice_id = c.id ' .
            'LEFT JOIN {questionnaire_response_other} o ON a.response_id = o.response_id AND c.id = o.choice_id ' .
            'WHERE a.response_id = ? ';
        $records = $DB->get_records_sql($sql, [$rid]);
        foreach ($records as $row) {
            $newrow['content'] = $row->content;
            $newrow['ccontent'] = $row->ccontent;
            $newrow['responses'] = [];
            $newrow['responses'][$row->cid] = $row->cid;
            if (\mod_questionnaire\question\choice::content_is_other_choice($row->ccontent)) {
                $newrow['responses'][\mod_questionnaire\question\choice::id_other_choice_name($row->cid)] = $row->response;
            }
            $values[$row->qid] = $newrow;
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
        $sql = 'SELECT r.id as id, r.response_id as responseid, r.question_id as questionid, r.choice_id as choiceid, ' .
            'o.response as value ' .
            'FROM {' . static::response_table() .'} r ' .
            'LEFT JOIN {questionnaire_response_other} o ON r.response_id = o.response_id AND r.question_id = o.question_id AND ' .
            'r.choice_id = o.choice_id ' .
            'WHERE r.response_id = ? ';
        $records = $DB->get_records_sql($sql, [$rid]);
        foreach ($records as $record) {
            $answers[$record->questionid][$record->choiceid] = answer\answer::create_from_data($record);
        }

        return $answers;
    }

    /**
     * Return sql and params for getting responses in bulk.
     * @param int|array $questionnaireids One id, or an array of ids.
     * @param bool|int $responseid
     * @param bool|int $userid
     * @param bool|int $groupid
     * @param int $showincompletes
     * @return array
     * author Guy Thomas
     */
    public function get_bulk_sql($questionnaireids, $responseid = false, $userid = false, $groupid = false, $showincompletes = 0) {
        global $DB;

        $sql = $this->bulk_sql();
        if (($groupid !== false) && ($groupid > 0)) {
            $groupsql = ' INNER JOIN {groups_members} gm ON gm.groupid = ? AND gm.userid = qr.userid ';
            $gparams = [$groupid];
        } else {
            $groupsql = '';
            $gparams = [];
        }

        if (is_array($questionnaireids)) {
            list($qsql, $params) = $DB->get_in_or_equal($questionnaireids);
        } else {
            $qsql = ' = ? ';
            $params = [$questionnaireids];
        }
        if ($showincompletes == 1) {
            $showcompleteonly = '';
        } else {
            $showcompleteonly = 'AND qr.complete = ? ';
            $params[] = 'y';
        }

        $sql .= "
            AND qr.questionnaireid $qsql $showcompleteonly
      LEFT JOIN {questionnaire_response_other} qro ON qro.response_id = qr.id AND qro.choice_id = qrs.choice_id
      LEFT JOIN {user} u ON u.id = qr.userid
      $groupsql
        ";
        $params = array_merge($params, $gparams);

        if ($responseid) {
            $sql .= " WHERE qr.id = ?";
            $params[] = $responseid;
        } else if ($userid) {
            $sql .= " WHERE qr.userid = ?";
            $params[] = $userid;
        }

        return [$sql, $params];
    }

    /**
     * Return sql for getting responses in bulk.
     * @author Guy Thomas
     * @return string
     */
    protected function bulk_sql() {
        global $DB;

        $userfields = $this->user_fields_sql();
        $alias = 'qrs';
        $extraselect = 'qrs.choice_id, ' . $DB->sql_order_by_text('qro.response', 1000) . ' AS response, 0 AS rankvalue';

        return "
            SELECT " . $DB->sql_concat_join("'_'", ['qr.id', "'".$this->question->helpname()."'", $alias.'.id']) . " AS id,
                   qr.submitted, qr.complete, qr.grade, qr.userid, $userfields, qr.id AS rid, $alias.question_id,
                   $extraselect
              FROM {questionnaire_response} qr
              JOIN {".static::response_table()."} $alias ON $alias.response_id = qr.id
        ";
    }
}
