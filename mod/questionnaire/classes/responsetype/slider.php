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
 * Class for slider text response types.
 *
 * @author Hieu Vu Van
 * @copyright 2022 The Open University.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 */
class slider extends numericaltext {
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
        $sql = 'SELECT qs.id, qs.response_id as responseid, qs.question_id as questionid,
                       0 as choiceid, qs.response as value,  qq.extradata ' .
                'FROM {' . static::response_table() . '} qs ' .
                'INNER JOIN {questionnaire_question} qq ON qq.id = qs.question_id ' .
                'WHERE response_id = ? ';
        $records = $DB->get_records_sql($sql, [$rid]);
        foreach ($records as $record) {
            $answers[$record->questionid][] = answer\answer::create_from_data($record);
            if (!empty($record->extradata)) {
                $answers[$record->questionid]['extradata'] = json_decode($record->extradata);
            }
        }
        return $answers;
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
        $sql = 'SELECT response_id as rid, response AS score ' .
            'FROM {'.$this->response_table().'} r ' .
            'WHERE r.question_id= ? ' . $rsql . ' ' .
            'ORDER BY response_id ASC';
        return $DB->get_records_sql($sql, $params);
    }

}
