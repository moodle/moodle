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
use \html_writer;
use \html_table;

use mod_questionnaire\db\bulk_sql_config;

/**
 * Class for describing a response.
 *
 * @author Mike Churchward
 * @package response
 */

abstract class base {

    public function __construct($question) {
        $this->question = $question;
    }

    /**
     * Provide the necessary response data table name.
     *
     * @return string response table name.
     */
    static public function response_table() {
        return 'Must be implemented!';
    }

    /**
     * Insert a provided response to the question.
     *
     * @param integer $rid - The data id of the response table id.
     * @param mixed $val - The response data provided.
     * @return int|bool - on error the subtype should call set_error and return false.
     */
    abstract public function insert_response($rid, $val);

    /**
     * Provide the result information for the specified result records.
     *
     * @param int|array $rids - A single response id, or array.
     * @param boolean $anonymous - Whether or not responses are anonymous.
     * @return array - Array of data records.
     */
    abstract public function get_results($rids=false, $anonymous=false);

    /**
     * Provide the result information for the specified result records.
     *
     * @param int|array $rids - A single response id, or array.
     * @param string $sort - Optional display sort.
     * @param boolean $anonymous - Whether or not responses are anonymous.
     * @return string - Display output.
     */
    abstract public function display_results($rids=false, $sort='', $anonymous=false);

    /**
     * If the choice id needs to be transformed into a different value, override this in the child class.
     * @param $choiceid
     * @return mixed
     */
    public function transform_choiceid($choiceid) {
        return $choiceid;
    }

    /**
     * Provide the feedback scores for all requested response id's. This should be provided only by questions that provide feedback.
     * @param array $rids
     * @return array | boolean
     */
    public function get_feedback_scores(array $rids) {
        return false;
    }

    /**
     * @param $rows
     * @param $rids
     * @param $sort
     * @return string
     */
    protected function display_response_choice_results($rows, $rids, $sort) {
        $output = '';
        if (is_array($rids)) {
            $prtotal = 1;
        } else if (is_int($rids)) {
            $prtotal = 0;
        }
        if ($rows) {
            foreach ($rows as $idx => $row) {
                if (strpos($idx, 'other') === 0) {
                    $answer = $row->response;
                    $ccontent = $row->content;
                    $content = preg_replace(array('/^!other=/', '/^!other/'),
                            array('', get_string('other', 'questionnaire')), $ccontent);
                    $content .= ' ' . clean_text($answer);
                    $textidx = $content;
                    $this->counts[$textidx] = !empty($this->counts[$textidx]) ? ($this->counts[$textidx] + 1) : 1;
                } else {
                    $contents = questionnaire_choice_values($row->content);
                    $this->choice = $contents->text.$contents->image;
                    $textidx = $this->choice;
                    $this->counts[$textidx] = !empty($this->counts[$textidx]) ? ($this->counts[$textidx] + 1) : 1;
                }
            }
            $output .= \mod_questionnaire\response\display_support::mkrespercent($this->counts, count($rids),
                $this->question->precise, $prtotal, $sort);
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
        return [];
    }

    /**
     * Return all the fields to be used for users in bulk questionnaire sql.
     *
     * @author: Guy Thomas
     * @return string
     */
    protected function user_fields_sql() {
        $userfieldsarr = get_all_user_name_fields();
        $userfieldsarr = array_merge($userfieldsarr, ['username', 'department', 'institution']);
        $userfields = '';
        foreach ($userfieldsarr as $field) {
            $userfields .= $userfields === '' ? '' : ', ';
            $userfields .= 'u.'.$field;
        }
        $userfields .= ', u.id as usrid';
        return $userfields;
    }

    /**
     * Return sql and params for getting responses in bulk.
     * @author Guy Thomas
     * @param int $surveyid
     * @param bool|int $responseid
     * @param bool|int $userid
     * @param bool|int $groupid
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
     * Configure bulk sql
     * @return bulk_sql_config
     */
    protected function bulk_sql_config() {
        return new bulk_sql_config('questionnaire_response_other', 'qro', true, true, false);
    }

    /**
     * Return sql for getting responses in bulk.
     * @author Guy Thomas
     * @return string
     */
    protected function bulk_sql() {
        global $DB;
        $userfields = $this->user_fields_sql();

        $config = $this->bulk_sql_config();
        $alias = $config->tablealias;

        $extraselectfields = $config->get_extra_select();
        $extraselect = '';
        foreach ($extraselectfields as $field => $include) {
            $extraselect .= $extraselect === '' ? '' : ', ';
            if ($include) {
                // The 'response' field can be varchar or text, which doesn't work for all DB's (Oracle).
                // So convert the text if needed.
                if ($field === 'response') {
                    $extraselect .= $DB->sql_order_by_text($alias . '.' . $field, 1000).' AS '.$field;
                } else {
                    $extraselect .= $alias . '.' . $field;
                }
            } else {
                $default = $field === 'response' ? 'null' : 0;
                $extraselect .= $default.' AS ' . $field;
            }
        }

        return "
            SELECT " . $DB->sql_concat_join("'_'", ['qr.id', "'".$this->question->helpname()."'", $alias.'.id']) . " AS id,
                   qr.submitted, qr.complete, qr.grade, qr.userid, $userfields, qr.id AS rid, $alias.question_id,
                   $extraselect
              FROM {questionnaire_response} qr
              JOIN {".$config->table."} $alias
                ON $alias.response_id = qr.id
        ";
    }

}