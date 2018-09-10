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
 * Class for rank responses.
 *
 * @author Mike Churchward
 * @package responsetypes
 */

class rank extends base {
    static public function response_table() {
        return 'questionnaire_response_rank';
    }

    /**
     * @param int $rid
     * @param mixed $val
     * @return bool|int
     */
    public function insert_response($rid, $val) {
        global $DB;
        if ($this->question->type_id == QUESRATE) {
            $resid = false;
            foreach ($this->question->choices as $cid => $choice) {
                $other = optional_param('q'.$this->question->id.'_'.$cid, null, PARAM_CLEAN);
                // Choice not set or not answered.
                if (!isset($other) || $other == '') {
                    continue;
                }
                if ($other == get_string('notapplicable', 'questionnaire')) {
                    $rank = -1;
                } else {
                    $rank = intval($other);
                }
                $record = new \stdClass();
                $record->response_id = $rid;
                $record->question_id = $this->question->id;
                $record->choice_id = $cid;
                $record->rank = $rank;
                $resid = $DB->insert_record(self::response_table(), $record);
            }
            return $resid;
        } else { // THIS SHOULD NEVER HAPPEN.
            if ($val == get_string('notapplicable', 'questionnaire')) {
                $rank = -1;
            } else {
                $rank = intval($val);
            }
            $record = new \stdClass();
            $record->response_id = $rid;
            $record->question_id = $this->question->id;
            $record->rank = $rank;
            return $DB->insert_record(self::response_table(), $record);
        }
    }

    /**
     * @param bool $rids
     * @param bool $anonymous
     * @return array
     */
    public function get_results($rids=false, $anonymous=false) {
        global $DB;

        $rsql = '';
        if (!empty($rids)) {
            list($rsql, $params) = $DB->get_in_or_equal($rids);
            $rsql = ' AND response_id ' . $rsql;
        }

        if ($this->question->type_id == QUESRATE) {
            // JR there can't be an !other field in rating questions ???
            $rankvalue = array();
            $select = 'question_id=' . $this->question->id . ' AND content NOT LIKE \'!other%\' ORDER BY id ASC';
            if ($rows = $DB->get_records_select('questionnaire_quest_choice', $select)) {
                foreach ($rows as $row) {
                    $this->counts[$row->content] = new \stdClass();
                    $nbna = $DB->count_records(self::response_table(), array('question_id' => $this->question->id,
                                    'choice_id' => $row->id, 'rank' => '-1'));
                    $this->counts[$row->content]->nbna = $nbna;
                    // The $row->value may be null (i.e. empty) or have a 'NULL' value.
                    if ($row->value !== null && $row->value !== 'NULL') {
                        $rankvalue[] = $row->value;
                    }
                }
            }

            $isrestricted = ($this->question->length < count($this->question->choices)) && $this->question->precise == 2;
            // Usual case.
            if (!$isrestricted) {
                if (!empty ($rankvalue)) {
                    $sql = "SELECT r.id, c.content, r.rank, c.id AS choiceid
                    FROM {questionnaire_quest_choice} c, {".self::response_table()."} r
                    WHERE r.choice_id = c.id
                    AND c.question_id = " . $this->question->id . "
                    AND r.rank >= 0{$rsql}
                    ORDER BY choiceid";
                    $results = $DB->get_records_sql($sql, $params);
                    $value = array();
                    foreach ($results as $result) {
                        if (isset ($value[$result->choiceid])) {
                            $value[$result->choiceid] += $rankvalue[$result->rank];
                        } else {
                            $value[$result->choiceid] = $rankvalue[$result->rank];
                        }
                    }
                }

                $sql = "SELECT c.id, c.content, a.average, a.num
                        FROM {questionnaire_quest_choice} c
                        INNER JOIN
                             (SELECT c2.id, AVG(a2.rank+1) AS average, COUNT(a2.response_id) AS num
                              FROM {questionnaire_quest_choice} c2, {".self::response_table()."} a2
                              WHERE c2.question_id = ? AND a2.question_id = ? AND a2.choice_id = c2.id AND a2.rank >= 0{$rsql}
                              GROUP BY c2.id) a ON a.id = c.id
                              order by c.id";
                $results = $DB->get_records_sql($sql, array_merge(array($this->question->id, $this->question->id), $params));
                if (!empty ($rankvalue)) {
                    foreach ($results as $key => $result) {
                        $result->averagevalue = $value[$key] / $result->num;
                    }
                }
                // Reindex by 'content'. Can't do this from the query as it won't work with MS-SQL.
                foreach ($results as $key => $result) {
                    $results[$result->content] = $result;
                    unset($results[$key]);
                }
                return $results;
                // Case where scaleitems is less than possible choices.
            } else {
                $sql = "SELECT c.id, c.content, a.sum, a.num
                        FROM {questionnaire_quest_choice} c
                        INNER JOIN
                             (SELECT c2.id, SUM(a2.rank+1) AS sum, COUNT(a2.response_id) AS num
                              FROM {questionnaire_quest_choice} c2, {".self::response_table()."} a2
                              WHERE c2.question_id = ? AND a2.question_id = ? AND a2.choice_id = c2.id AND a2.rank >= 0{$rsql}
                              GROUP BY c2.id) a ON a.id = c.id";
                $results = $DB->get_records_sql($sql, array_merge(array($this->question->id, $this->question->id), $params));
                // Formula to calculate the best ranking order.
                $nbresponses = count($rids);
                foreach ($results as $key => $result) {
                    $result->average = ($result->sum + ($nbresponses - $result->num) * ($this->length + 1)) / $nbresponses;
                    $results[$result->content] = $result;
                    unset($results[$key]);
                }
                return $results;
            }
        } else {
            $sql = 'SELECT A.rank, COUNT(A.response_id) AS num ' .
                   'FROM {'.self::response_table().'} A ' .
                   'WHERE A.question_id= ? ' . $rsql . ' ' .
                   'GROUP BY A.rank';
            return $DB->get_records_sql($sql, array_merge(array($this->question->id), $params));
        }
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

        $sql = 'SELECT r.id, r.response_id as rid, r.question_id AS qid, r.choice_id AS cid, r.rank ' .
            'FROM {'.$this->response_table().'} r ' .
            'INNER JOIN {questionnaire_quest_choice} c ON r.choice_id = c.id ' .
            'WHERE r.question_id= ? ' . $rsql . ' ' .
            'ORDER BY rid,cid ASC';
        $responses = $DB->get_recordset_sql($sql, $params);

        $sql = 'SELECT id, value ' .
            'FROM {questionnaire_quest_choice} ' .
            'WHERE question_id = ? AND value IS NOT NULL ' .
            'ORDER BY id ASC ';
        $scorerecs = $DB->get_records_sql($sql, $params);

        // Reindex $scores as a zero starting array.
        $scores = [];
        foreach ($scorerecs as $scorerec) {
            $scores[] = $scorerec->value;
        }

        $rid = 0;
        $feedbackscores = [];
        foreach ($responses as $response) {
            if ($rid != $response->rid) {
                $rid = $response->rid;
                $feedbackscores[$rid] = new \stdClass();
                $feedbackscores[$rid]->rid = $rid;
                $feedbackscores[$rid]->score = 0;
            }
            $feedbackscores[$rid]->score += isset($scores[$response->rank]) ? $scores[$response->rank] : 0;
        }

        return (!empty($feedbackscores) ? $feedbackscores : false);
    }

    /**
     * @param bool $rids
     * @param string $sort
     * @param bool $anonymous
     * @return string
     */
    public function display_results($rids=false, $sort='', $anonymous=false) {
        $output = '';

        if (is_array($rids)) {
            $prtotal = 1;
        } else if (is_int($rids)) {
            $prtotal = 0;
        }

        if ($rows = $this->get_results($rids, $sort, $anonymous)) {
            $stravgvalue = ''; // For printing table heading.
            foreach ($this->counts as $key => $value) {
                $ccontent = $key;
                $avgvalue = '';
                if (array_key_exists($ccontent, $rows)) {
                    $avg = $rows[$ccontent]->average;
                    $this->counts[$ccontent]->num = $rows[$ccontent]->num;
                    if (isset($rows[$ccontent]->averagevalue)) {
                        $avgvalue = $rows[$ccontent]->averagevalue;
                        $osgood = false;
                        if ($this->question->precise == 3) { // Osgood's semantic differential.
                            $osgood = true;
                        }
                        if ($stravgvalue == '' && !$osgood) {
                            $stravgvalue = ' ('.get_string('andaveragevalues', 'questionnaire').')';
                        }
                    } else {
                        $avgvalue = null;
                    }
                } else {
                    $avg = 0;
                }
                $this->counts[$ccontent]->avg = $avg;
                $this->counts[$ccontent]->avgvalue = $avgvalue;
            }
            $output .= \mod_questionnaire\response\display_support::mkresavg($this->counts, count($rids), $this->question->choices,
                $this->question->precise, $prtotal, $this->question->length, $sort, $stravgvalue);

            $output .= \mod_questionnaire\response\display_support::mkrescount($this->counts, $rids, $rows, $this->question,
                $this->question->precise, $this->question->length, $sort);
        } else {
            $output .= '<p class="generaltable">&nbsp;'.get_string('noresponsedata', 'questionnaire').'</p>';
        }
        return $output;
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
        $sql = 'SELECT a.id as aid, q.id AS qid, q.precise AS precise, c.id AS cid '.$col.', c.content as ccontent,
                                a.rank as arank '.
            'FROM {'.self::response_table().'} a, {questionnaire_question} q, {questionnaire_quest_choice} c '.
            'WHERE a.response_id= ? AND a.question_id=q.id AND a.choice_id=c.id '.
            'ORDER BY aid, a.question_id, c.id';
        $records = $DB->get_records_sql($sql, [$rid]);
        foreach ($records as $row) {
            // Next two are 'qid' and 'cid', each with numeric and hash keys.
            $osgood = false;
            if ($row->precise == 3) {
                $osgood = true;
            }
            $qid = $row->qid.'_'.$row->cid;
            unset($row->aid); // Get rid of the answer id.
            unset($row->qid);
            unset($row->cid);
            unset($row->precise);
            $row = (array)$row;
            $newrow = [];
            foreach ($row as $key => $val) {
                if ($key != 'content') { // No need to keep question text - ony keep choice text and rank.
                    if ($key == 'ccontent') {
                        if ($osgood) {
                            list($contentleft, $contentright) = array_merge(preg_split('/[|]/', $val), [' ']);
                            $contents = questionnaire_choice_values($contentleft);
                            if ($contents->title) {
                                $contentleft = $contents->title;
                            }
                            $contents = questionnaire_choice_values($contentright);
                            if ($contents->title) {
                                $contentright = $contents->title;
                            }
                            $val = strip_tags($contentleft.'|'.$contentright);
                            $val = preg_replace("/[\r\n\t]/", ' ', $val);
                        } else {
                            $contents = questionnaire_choice_values($val);
                            if ($contents->modname) {
                                $val = $contents->modname;
                            } else if ($contents->title) {
                                $val = $contents->title;
                            } else if ($contents->text) {
                                $val = strip_tags($contents->text);
                                $val = preg_replace("/[\r\n\t]/", ' ', $val);
                            }
                        }
                    }
                    $newrow[] = $val;
                }
            }
            $values[$qid] = $newrow;
        }

        return $values;
    }

    /**
     * Configure bulk sql
     * @return bulk_sql_config
     */
    protected function bulk_sql_config() {
        return new bulk_sql_config(self::response_table(), 'qrr', true, false, true);
    }

}