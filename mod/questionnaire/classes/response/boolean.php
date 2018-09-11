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
 * Class for boolean response types.
 *
 * @author Mike Churchward
 * @package response
 */

class boolean extends base {

    static public function response_table() {
        return 'questionnaire_response_bool';
    }

    public function insert_response($rid, $val) {
        global $DB;
        if (!empty($val)) { // If "no answer" then choice is empty (CONTRIB-846).
            $record = new \stdClass();
            $record->response_id = $rid;
            $record->question_id = $this->question->id;
            $record->choice_id = $val;
            return $DB->insert_record(self::response_table(), $record);
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
        $params[] = '';

        $sql = 'SELECT choice_id, COUNT(response_id) AS num ' .
               'FROM {'.self::response_table().'} ' .
               'WHERE question_id= ? ' . $rsql . ' AND choice_id != ? ' .
               'GROUP BY choice_id';
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * If the choice id needs to be transformed into a different value, override this in the child class.
     * @param $choiceid
     * @return mixed
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

        $sql = 'SELECT response_id as rid, COUNT(response_id) AS score ' .
            'FROM {'.$this->response_table().'} ' .
            'WHERE question_id= ? ' . $rsql . ' AND choice_id = ? ' .
            'GROUP BY response_id ' .
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
        $output = '';

        if (empty($this->stryes)) {
            $this->stryes = get_string('yes');
            $this->strno = get_string('no');
        }

        if (is_array($rids)) {
            $prtotal = 1;
        } else if (is_int($rids)) {
            $prtotal = 0;
        }

         $this->counts = array($this->stryes => 0, $this->strno => 0);
        if ($rows = $this->get_results($rids, $anonymous)) {
            foreach ($rows as $row) {
                $this->choice = $row->choice_id;
                $count = $row->num;
                if ($this->choice == 'y') {
                    $this->choice = $this->stryes;
                } else {
                    $this->choice = $this->strno;
                }
                $this->counts[$this->choice] = intval($count);
            }
            $output .= \mod_questionnaire\response\display_support::mkrespercent($this->counts, count($rids),
                $this->question->precise, $prtotal, $sort = '');
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
        $sql = 'SELECT q.id '.$col.', a.choice_id '.
            'FROM {'.self::response_table().'} a, {questionnaire_question} q '.
            'WHERE a.response_id= ? AND a.question_id=q.id ';
        $records = $DB->get_records_sql($sql, [$rid]);
        foreach ($records as $qid => $row) {
            $choice = $row->choice_id;
            if (isset ($row->name) && $row->name == '') {
                $noname = true;
            }
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
            if (!$csvexport) {
                array_push($values[$qid], $choice); // DEV still needed for responses display.
            }
        }

        return $values;
    }

    /**
     * Configure bulk sql
     * @return bulk_sql_config
     */
    protected function bulk_sql_config() {
        return new bulk_sql_config(self::response_table(), 'qrb', true, false, false);
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
        $extraselect = '0 AS choice_id, ' . $DB->sql_order_by_text('qrb.choice_id', 1000) . ' AS response, 0 AS rank';
        $alias = 'qrb';

        return "
            SELECT " . $DB->sql_concat_join("'_'", ['qr.id', "'".$this->question->helpname()."'", $alias.'.id']) . " AS id,
                   qr.submitted, qr.complete, qr.grade, qr.userid, $userfields, qr.id AS rid, $alias.question_id,
                   $extraselect
              FROM {questionnaire_response} qr
              JOIN {".self::response_table()."} $alias ON $alias.response_id = qr.id
        ";
    }
}

