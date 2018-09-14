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

/**
 * Class for single response types.
 *
 * @author Mike Churchward
 * @package responsetypes
 */

class single extends base {
    static public function response_table() {
        return 'questionnaire_resp_single';
    }

    public function insert_response($rid, $val) {
        global $DB;
        if (!empty($val)) {
            foreach ($this->question->choices as $cid => $choice) {
                if (strpos($choice->content, '!other') === 0) {
                    $other = optional_param('q'.$this->question->id.'_'.$cid, null, PARAM_TEXT);
                    if (!isset($other)) {
                        continue;
                    }
                    if (preg_match("/[^ \t\n]/", $other)) {
                        $record = new \stdClass();
                        $record->response_id = $rid;
                        $record->question_id = $this->question->id;
                        $record->choice_id = $cid;
                        $record->response = $other;
                        $resid = $DB->insert_record('questionnaire_response_other', $record);
                        $val = $cid;
                        break;
                    }
                }
            }
        }
        if (preg_match("/other_q([0-9]+)/", (isset($val) ? $val : ''), $regs)) {
            $cid = $regs[1];
            if (!isset($other)) {
                $other = optional_param('q'.$this->question->id.'_'.$cid, null, PARAM_TEXT);
            }
            if (preg_match("/[^ \t\n]/", $other)) {
                $record = new \stdClass();
                $record->response_id = $rid;
                $record->question_id = $this->question->id;
                $record->choice_id = $cid;
                $record->response = $other;
                $resid = $DB->insert_record('questionnaire_response_other', $record);
                $val = $cid;
            }
        }
        $record = new \stdClass();
        $record->response_id = $rid;
        $record->question_id = $this->question->id;
        $record->choice_id = isset($val) ? $val : 0;
        if ($record->choice_id) {// If "no answer" then choice_id is empty (CONTRIB-846).
            try {
                return $DB->insert_record(static::response_table(), $record);
            } catch (\dml_write_exception $ex) {
                return false;
            }
        } else {
            return false;
        }
    }

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
        return $DB->get_recordset_sql($sql, $params);
    }

    /**
     * @param bool $rids
     * @param string $sort
     * @param bool $anonymous
     * @return string
     */
    public function display_results($rids=false, $sort='', $anonymous=false) {
        return $this->display_response_choice_results($this->get_results($rids, $anonymous), $rids, $sort);
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

        $values = [];
        $sql = 'SELECT q.id '.$col.', q.type_id as q_type, c.content as ccontent,c.id as cid '.
            'FROM {'.static::response_table().'} a, {questionnaire_question} q, {questionnaire_quest_choice} c '.
            'WHERE a.response_id = ? AND a.question_id=q.id AND a.choice_id=c.id ';
        $records = $DB->get_records_sql($sql, [$rid]);
        foreach ($records as $qid => $row) {
            $cid = $row->cid;
            if ($csvexport) {
                static $i = 1;
                $qrecords = $DB->get_records('questionnaire_quest_choice', ['question_id' => $qid]);
                foreach ($qrecords as $value) {
                    if ($value->id == $cid) {
                        $contents = questionnaire_choice_values($value->content);
                        if ($contents->modname) {
                            $row->ccontent = $contents->modname;
                        } else {
                            $content = $contents->text;
                            if (preg_match('/^!other/', $content)) {
                                $row->ccontent = get_string('other', 'questionnaire');
                            } else if (($choicecodes == 1) && ($choicetext == 1)) {
                                $row->ccontent = "$i : $content";
                            } else if ($choicecodes == 1) {
                                $row->ccontent = "$i";
                            } else {
                                $row->ccontent = $content;
                            }
                        }
                        $i = 1;
                        break;
                    }
                    $i++;
                }
            }
            unset($row->id);
            unset($row->cid);
            unset($row->q_type);
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
            $values[$qid] = $newrow;
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
      LEFT JOIN {questionnaire_response_other} qro ON qro.response_id = qr.id AND qro.choice_id = qrs.choice_id
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
        $extraselect = 'qrs.choice_id, ' . $DB->sql_order_by_text('qro.response', 1000) . ' AS response, 0 AS rank';
        $alias = 'qrs';

        return "
            SELECT " . $DB->sql_concat_join("'_'", ['qr.id', "'".$this->question->helpname()."'", $alias.'.id']) . " AS id,
                   qr.submitted, qr.complete, qr.grade, qr.userid, $userfields, qr.id AS rid, $alias.question_id,
                   $extraselect
              FROM {questionnaire_response} qr
              JOIN {".static::response_table()."} $alias ON $alias.response_id = qr.id
        ";
    }
}
