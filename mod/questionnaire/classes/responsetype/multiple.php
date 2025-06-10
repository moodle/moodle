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
 * Class for multiple response types.
 *
 * @author Mike Churchward
 * @copyright 2016 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 */
class multiple extends single {
    /**
     * The only differences between multuple and single responses are the
     * response table and the insert logic.
     */
    public static function response_table() {
        return 'questionnaire_resp_multiple';
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
        if (isset($responsedata->{'q'.$question->id})) {
            foreach ($responsedata->{'q' . $question->id} as $cid => $cvalue) {
                $cid = clean_param($cid, PARAM_CLEAN);
                if (isset($question->choices[$cid])) {
                    $record = new \stdClass();
                    $record->responseid = $responsedata->rid;
                    $record->questionid = $question->id;
                    $record->choiceid = $cid;
                    // If this choice is an "other" choice, look for the added input.
                    if ($question->choices[$cid]->is_other_choice()) {
                        $cname = \mod_questionnaire\question\choice::id_other_choice_name($cid);
                        $record->value = isset($responsedata->{'q' . $question->id}[$cname]) ?
                            $responsedata->{'q' . $question->id}[$cname] : '';
                    }
                    $answers[$cid] = answer\answer::create_from_data($record);
                }
            }
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
        // Need to override "single" class' implementation.
        $answers = [];
        $qname = 'q'.$question->id;
        if (isset($responsedata->{$qname}) && !empty($responsedata->{$qname})) {
            foreach ($responsedata->{$qname} as $choiceid => $choicevalue) {
                if ($choicevalue) {
                    $record = new \stdClass();
                    $record->responseid = $responsedata->rid;
                    $record->questionid = $question->id;
                    $record->choiceid = $choiceid;
                    // If this choice is an "other" choice, look for the added input.
                    if (isset($question->choices[$choiceid]) && $question->choices[$choiceid]->is_other_choice()) {
                        $cname = \mod_questionnaire\question\choice::id_other_choice_name($choiceid);
                        $record->value =
                            isset($responsedata->{$qname}[$cname]) ? $responsedata->{$qname}[$cname] : '';
                    } else {
                        $record->value = $choicevalue;
                    }
                    $answers[] = answer\answer::create_from_data($record);
                }
            }
        }
        return $answers;
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
        if (!empty($records)) {
            $qid = 0;
            $newrow = [];
            foreach ($records as $row) {
                if ($qid == 0) {
                    $qid = $row->qid;
                    $newrow['content'] = $row->content;
                    $newrow['ccontent'] = $row->ccontent;
                    $newrow['responses'] = [];
                } else if ($qid != $row->qid) {
                    $values[$qid] = $newrow;
                    $qid = $row->qid;
                    $newrow = [];
                    $newrow['content'] = $row->content;
                    $newrow['ccontent'] = $row->ccontent;
                    $newrow['responses'] = [];
                }
                $newrow['responses'][$row->cid] = $row->cid;
                if (\mod_questionnaire\question\choice::content_is_other_choice($row->ccontent)) {
                    $newrow['responses'][\mod_questionnaire\question\choice::id_other_choice_name($row->cid)] =
                        $row->response;
                }
            }
            $values[$qid] = $newrow;
        }

        return $values;
    }

    /**
     * Return sql and params for getting responses in bulk.
     * @param int|array $questionnaireids One id, or an array of ids.
     * @param bool|int $responseid
     * @param bool|int $userid
     * @param bool $groupid
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
      LEFT JOIN {questionnaire_response_other} qro ON qro.response_id = qr.id AND qro.choice_id = qrm.choice_id
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
     * @return string
     * author Guy Thomas
     */
    protected function bulk_sql() {
        global $DB;

        $userfields = $this->user_fields_sql();
        $alias = 'qrm';
        $extraselect = '';
        $extraselect .= 'qrm.choice_id, ' . $DB->sql_order_by_text('qro.response', 1000) . ' AS response, 0 AS rankvalue';

        return "
            SELECT " . $DB->sql_concat_join("'_'", ['qr.id', "'".$this->question->helpname()."'", $alias.'.id']) . " AS id,
                   qr.submitted, qr.complete, qr.grade, qr.userid, $userfields, qr.id AS rid, $alias.question_id,
                   $extraselect
              FROM {questionnaire_response} qr
              JOIN {".static::response_table()."} $alias ON $alias.response_id = qr.id
        ";
    }
}
