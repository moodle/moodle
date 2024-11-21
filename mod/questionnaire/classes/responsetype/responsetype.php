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

use \html_writer;
use \html_table;

use mod_questionnaire\db\bulk_sql_config;

/**
 * This file contains the parent class for questionnaire response types.
 *
 * @author Mike Churchward
 * @copyright 2016 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 */
abstract class responsetype {

    // Class properties.
    /** @var \mod_questionnaire\question\question $question The question for this response. */
    public $question;

    /** @var int $responseid The id of the response this is for. */
    public $responseid;

    /** @var array $choices An array of \mod_questionnaire\responsetype\choice objects. */
    public $choices;

    /**
     * responsetype constructor.
     * @param \mod_questionnaire\question\question $question
     * @param int|null $responseid
     * @param array $choices
     */
    public function __construct(\mod_questionnaire\question\question $question, int $responseid = null, array $choices = []) {
        $this->question = $question;
        $this->responseid = $responseid;
        $this->choices = $choices;
    }

    /**
     * Provide the necessary response data table name. Should probably always be used with late static binding 'static::' form
     * rather than 'self::' form to allow for class extending.
     *
     * @return string response table name.
     */
    public static function response_table() {
        return 'Must be implemented!';
    }

    /**
     * Return the known response tables. Should be replaced by a better management system eventually.
     * @return array
     */
    public static function all_response_tables() {
        return ['questionnaire_response_bool', 'questionnaire_response_date', 'questionnaire_response_other',
            'questionnaire_response_rank', 'questionnaire_response_text', 'questionnaire_resp_multiple',
            'questionnaire_resp_single'];
    }

    /**
     * Provide an array of answer objects from web form data for the question.
     *
     * @param \stdClass $responsedata All of the responsedata as an object.
     * @param \mod_questionnaire\question\question $question
     * @return array \mod_questionnaire\responsetype\answer\answer An array of answer objects.
     */
    abstract public static function answers_from_webform($responsedata, $question);

    /**
     * Insert a provided response to the question.
     *
     * @param object $responsedata All of the responsedata as an object.
     * @return int|bool - on error the subtype should call set_error and return false.
     */
    abstract public function insert_response($responsedata);

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
     * @param mixed $choiceid
     * @return mixed
     */
    public function transform_choiceid($choiceid) {
        return $choiceid;
    }

    /**
     * Provide a template for results screen if defined.
     * @param bool $pdf
     * @return mixed The template string or false.
     */
    public function results_template($pdf = false) {
        return false;
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
        global $CFG;

        $pagetags = new \stdClass();
        $precision = 0;
        $alt = '';
        $imageurl = $CFG->wwwroot.'/mod/questionnaire/images/';

        if (!empty($weights) && is_array($weights)) {
            $pos = 0;
            switch ($sort) {
                case 'ascending':
                    asort($weights);
                    break;
                case 'descending':
                    arsort($weights);
                    break;
            }

            reset ($weights);
            $pagetags->responses = [];
            $evencolor = false;
            foreach ($weights as $content => $num) {
                $response = new \stdClass();
                $response->text = format_text($content, FORMAT_HTML, ['noclean' => true]);
                if ($num > 0) {
                    $percent = round((float)$num / (float)$respondents * 100.0);
                } else {
                    $percent = 0;
                }
                if ($percent > 100) {
                    $percent = 100;
                }
                if ($num) {
                    if (!right_to_left()) {
                        $response->alt1 = $alt;
                        $response->image1 = $imageurl . 'hbar_l.gif';
                        $response->alt3 = $alt;
                        $response->image3 = $imageurl . 'hbar_r.gif';
                    } else {
                        $response->alt1 = $alt;
                        $response->image1 = $imageurl . 'hbar_r.gif';
                        $response->alt3 = $alt;
                        $response->image3 = $imageurl . 'hbar_l.gif';
                    }
                    $response->alt2 = $alt;
                    $response->width2 = $percent * 1.4;
                    $response->image2 = $imageurl . 'hbar.gif';
                    $response->percent = sprintf('&nbsp;%.'.$precision.'f%%', $percent);
                }
                $response->total = $num;
                // The 'evencolor' attribute is used by the PDF template.
                $response->evencolor = $evencolor;
                $pagetags->responses[] = (object)['response' => $response];
                $pos++;
                $evencolor = !$evencolor;
            } // End while.

            if ($showtotals) {
                $pagetags->total = new \stdClass();
                if ($respondents > 0) {
                    $percent = round((float)$respondents / (float)$participants * 100.0);
                } else {
                    $percent = 0;
                }
                if ($percent > 100) {
                    $percent = 100;
                }
                if (!right_to_left()) {
                    $pagetags->total->alt1 = $alt;
                    $pagetags->total->image1 = $imageurl . 'thbar_l.gif';
                    $pagetags->total->alt3 = $alt;
                    $pagetags->total->image3 = $imageurl . 'thbar_r.gif';
                } else {
                    $pagetags->total->alt1 = $alt;
                    $pagetags->total->image1 = $imageurl . 'thbar_r.gif';
                    $pagetags->total->alt3 = $alt;
                    $pagetags->total->image3 = $imageurl . 'thbar_l.gif';
                }
                $pagetags->total->alt2 = $alt;
                $pagetags->total->width2 = $percent * 1.4;
                $pagetags->total->image2 = $imageurl . 'thbar.gif';
                $pagetags->total->percent = sprintf('&nbsp;%.'.$precision.'f%%', $percent);
                $pagetags->total->total = "$respondents/$participants";
                $pagetags->total->evencolor = $evencolor;
            }
        }

        return $pagetags;
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
     * Return an array of answers by question/choice for the given response. Must be implemented by the subclass.
     *
     * @param int $rid The response id.
     * @return array
     */
    public static function response_select($rid) {
        return [];
    }

    /**
     * Return an array of answer objects by question for the given response id.
     * THIS SHOULD REPLACE response_select.
     *
     * @param int $rid The response id.
     * @return array array answer
     */
    public static function response_answers_by_question($rid) {
        return [];
    }

    /**
     * Provide an array of answer objects from mobile data for the question.
     *
     * @param \stdClass $responsedata All of the responsedata as an object.
     * @param \mod_questionnaire\question\question $question
     * @return array \mod_questionnaire\responsetype\answer\answer An array of answer objects.
     */
    public static function answers_from_appdata($responsedata, $question) {
        // In most cases this can be a direct call to answers_from_webform with the one modification below. Override when this will
        // not work.
        if (isset($responsedata->{'q'.$question->id}) && !empty($responsedata->{'q'.$question->id})) {
            $responsedata->{'q'.$question->id} = $responsedata->{'q'.$question->id}[0];
        }
        return static::answers_from_webform($responsedata, $question);
    }

    /**
     * Return all the fields to be used for users in bulk questionnaire sql.
     *
     * @return string
     * author: Guy Thomas
     */
    protected function user_fields_sql() {
        if (class_exists('\core_user\fields')) {
            $userfieldsarr = \core_user\fields::get_name_fields();
        } else {
            $userfieldsarr = get_all_user_name_fields();
        }
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
