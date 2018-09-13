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
 * This file contains the parent class for questionnaire question types.
 *
 * @author Mike Churchward
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questiontypes
 */

namespace mod_questionnaire\response;
defined('MOODLE_INTERNAL') || die();

use mod_questionnaire\db\bulk_sql_config;

/**
 * Class for multiple response types.
 *
 * @author Mike Churchward
 * @package responsetypes
 */

class multiple extends single {
    /**
     * The only differences between multuple and single responses are the
     * response table and the insert logic.
     */
    static public function response_table() {
        return 'questionnaire_resp_multiple';
    }

    public function insert_response($rid, $val) {
        global $DB;
        $resid = '';
        foreach ($this->question->choices as $cid => $choice) {
            if (strpos($choice->content, '!other') === 0) {
                $other = optional_param('q'.$this->question->id.'_'.$cid, '', PARAM_CLEAN);
                if (empty($other)) {
                    continue;
                }
                if (!isset($val) || !is_array($val)) {
                    $val = array($cid);
                } else {
                    array_push($val, $cid);
                }
                if (preg_match("/[^ \t\n]/", $other)) {
                    $record = new \stdClass();
                    $record->response_id = $rid;
                    $record->question_id = $this->question->id;
                    $record->choice_id = $cid;
                    $record->response = $other;
                    $resid = $DB->insert_record('questionnaire_response_other', $record);
                }
            }
        }

        if (!isset($val) || !is_array($val)) {
            return false;
        }

        foreach ($val as $cid) {
            $cid = clean_param($cid, PARAM_CLEAN);
            if ($cid != 0) { // Do not save response if choice is empty.
                if (preg_match("/other_q[0-9]+/", $cid)) {
                    continue;
                }
                $record = new \stdClass();
                $record->response_id = $rid;
                $record->question_id = $this->question->id;
                $record->choice_id = $cid;
                $resid = $DB->insert_record(self::response_table(), $record);
            }
        }
        return $resid;
    }

    /**
     * Return an array of answers by question/choice for the given response. Must be implemented by the subclass.
     *
     * @param int $rid The response id.
     * @param null $col Other data columns to return.
     * @param bool $csvexport Using for CSV export.
     * @param int $choicecodes CSV choicecodes are required.
     * @param int $choicetext CSV choicetext is required.
     * @return array
     */
    static public function response_select($rid, $col = null, $csvexport = false, $choicecodes = 0, $choicetext = 1) {
        global $DB;

        $stringother = get_string('other', 'questionnaire');
        $values = [];
        $sql = 'SELECT a.id as aid, q.id as qid '.$col.',c.content as ccontent,c.id as cid '.
            'FROM {'.self::response_table().'} a, {questionnaire_question} q, {questionnaire_quest_choice} c '.
            'WHERE a.response_id = ? AND a.question_id=q.id AND a.choice_id=c.id '.
            'ORDER BY a.id,a.question_id,c.id';
        $records = $DB->get_records_sql($sql, [$rid]);
        if ($csvexport) {
            $tmp = null;
            if (!empty($records)) {
                $qids2 = array();
                $oldqid = '';
                foreach ($records as $qid => $row) {
                    if ($row->qid != $oldqid) {
                        $qids2[] = $row->qid;
                        $oldqid = $row->qid;
                    }
                }
                list($qsql, $params) = $DB->get_in_or_equal($qids2);
                $sql = 'SELECT * FROM {questionnaire_quest_choice} WHERE question_id ' . $qsql . ' ORDER BY id';
                $records2 = $DB->get_records_sql($sql, $params);
                foreach ($records2 as $qid => $row2) {
                    $selected = '0';
                    $qid2 = $row2->question_id;
                    $cid2 = $row2->id;
                    $c2 = $row2->content;
                    $otherend = false;
                    if ($c2 == '!other') {
                        $c2 = '!other='.get_string('other', 'questionnaire');
                    }
                    if (preg_match('/^!other/', $c2)) {
                        $otherend = true;
                    } else {
                        $contents = questionnaire_choice_values($c2);
                        if ($contents->modname) {
                            $c2 = $contents->modname;
                        } else if ($contents->title) {
                            $c2 = $contents->title;
                        }
                    }
                    $sql = 'SELECT a.name as name, a.type_id as q_type, a.position as pos ' .
                        'FROM {questionnaire_question} a WHERE id = ?';
                    $currentquestion = $DB->get_records_sql($sql, [$qid2]);
                    foreach ($currentquestion as $question) {
                        $name1 = $question->name;
                        $type1 = $question->q_type;
                    }
                    $newrow = [];
                    foreach ($records as $qid => $row1) {
                        $qid1 = $row1->qid;
                        $cid1 = $row1->cid;
                        // If available choice has been selected by student.
                        if ($qid1 == $qid2 && $cid1 == $cid2) {
                            $selected = '1';
                        }
                    }
                    if ($otherend) {
                        $newrow2 = array();
                        $newrow2[] = $question->pos;
                        $newrow2[] = $type1;
                        $newrow2[] = $name1;
                        $newrow2[] = '['.get_string('other', 'questionnaire').']';
                        $newrow2[] = $selected;
                        $tmp2 = $qid2.'_other';
                        $values["$tmp2"] = $newrow2;
                    }
                    $newrow[] = $question->pos;
                    $newrow[] = $type1;
                    $newrow[] = $name1;
                    $newrow[] = $c2;
                    $newrow[] = $selected;
                    $tmp = $qid2.'_'.$cid2;
                    $values["$tmp"] = $newrow;
                }
            }
            unset($tmp);
            unset($row);

        } else {
            $arr = [];
            $tmp = null;
            foreach ($records as $aid => $row) {
                $qid = $row->qid;
                $cid = $row->cid;
                unset($row->aid);
                unset($row->qid);
                unset($row->cid);
                $arow = get_object_vars($row);
                $newrow = [];
                foreach ($arow as $key => $val) {
                    if (!is_numeric($key)) {
                        $newrow[] = $val;
                    }
                }
                if (preg_match('/^!other/', $row->ccontent)) {
                    $newrow[] = 'other_' . $cid;
                } else {
                    $newrow[] = (int)$cid;
                }
                if ($tmp == $qid) {
                    $arr[] = $newrow;
                    continue;
                }
                if ($tmp != null) {
                    $values["$tmp"] = $arr;
                }
                $tmp = $qid;
                $arr = array($newrow);
            }
            if ($tmp != null) {
                $values["$tmp"] = $arr;
            }
            unset($arr);
            unset($tmp);
            unset($row);
        }

        // Response_other.
        // This will work even for multiple !other fields within one question
        // AND for identical !other responses in different questions JR.
        $sql = 'SELECT c.id as cid, c.content as content, a.response as aresponse, q.id as qid, q.position as position,
                                    q.type_id as type_id, q.name as name '.
            'FROM {questionnaire_response_other} a, {questionnaire_question} q, {questionnaire_quest_choice} c '.
            'WHERE a.response_id= ? AND a.question_id=q.id AND a.choice_id=c.id '.
            'ORDER BY a.question_id,c.id ';
        $records = $DB->get_records_sql($sql, [$rid]);
        foreach ($records as $record) {
            $newrow = [];
            $position = $record->position;
            $typeid = $record->type_id;
            $name = $record->name;
            $cid = $record->cid;
            $qid = $record->qid;
            $content = $record->content;

            // The !other modality with no label.
            if ($content == '!other') {
                $content = '!other='.$stringother;
            }
            $content = substr($content, 7);
            $aresponse = $record->aresponse;
            // The first two empty values are needed for compatibility with "normal" (non !other) responses.
            // They are only needed for the CSV export, in fact.
            $newrow[] = $position;
            $newrow[] = $typeid;
            $newrow[] = $name;
            $content = $stringother;
            $newrow[] = $content;
            $newrow[] = $aresponse;
            $values["${qid}_${cid}"] = $newrow;
        }

        return $values;
    }

    /**
     * Return sql and params for getting responses in bulk.
     * @author Guy Thomas
     * @param int $surveyid
     * @param bool|int $responseid
     * @param bool|int $userid
     * @return array
     */
    public function get_bulk_sql($surveyid, $responseid = false, $userid = false, $groupid = false) {
        global $DB;

        $sql = $this->bulk_sql($surveyid, $responseid, $userid);

        $params = [];
        if (($groupid !== false) && ($groupid > 0)) {
            $groupsql = ' INNER JOIN {groups_members} gm ON gm.groupid = ? AND gm.userid = qr.userid ';
            $gparams = [$groupid];
        } else {
            $groupsql = '';
            $gparams = [];
        }
        $sql .= "
            AND qr.survey_id = ? AND qr.complete = ?
      LEFT JOIN {questionnaire_response_other} qro ON qro.response_id = qr.id AND qro.choice_id = qrm.choice_id
      LEFT JOIN {user} u ON u.id = qr.userid
      $groupsql
        ";
        $params = array_merge([$surveyid, 'y'], $gparams);
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
        $extraselect = '';
        $extraselect .= 'qrm.choice_id, ' . $DB->sql_order_by_text('qro.response', 1000) . ' AS response, 0 AS rank';
        $alias = 'qrm';

        return "
            SELECT " . $DB->sql_concat_join("'_'", ['qr.id', "'".$this->question->helpname()."'", $alias.'.id']) . " AS id,
                   qr.submitted, qr.complete, qr.grade, qr.userid, $userfields, qr.id AS rid, $alias.question_id,
                   $extraselect
              FROM {questionnaire_response} qr
              JOIN {".self::response_table()."} $alias ON $alias.response_id = qr.id
        ";
    }
}
