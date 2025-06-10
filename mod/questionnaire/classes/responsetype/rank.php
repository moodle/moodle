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

use Composer\Package\Package;
use mod_questionnaire\db\bulk_sql_config;

/**
 * Class for rank responses.
 *
 * @author Mike Churchward
 * @copyright 2016 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 */
class rank extends responsetype {
    /**
     * @var \stdClass $counts Range counts.
     */
    public $counts;

    /**
     * Provide the necessary response data table name. Should probably always be used with late static binding 'static::' form
     * rather than 'self::' form to allow for class extending.
     *
     * @return string response table name.
     */
    public static function response_table() {
        return 'questionnaire_response_rank';
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
        foreach ($question->choices as $cid => $choice) {
            $other = isset($responsedata->{'q' . $question->id . '_' . $cid}) ?
                $responsedata->{'q' . $question->id . '_' . $cid} : null;
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
            $record->responseid = $responsedata->rid;
            $record->questionid = $question->id;
            $record->choiceid = $cid;
            $record->value = $rank;
            $answers[$cid] = answer\answer::create_from_data($record);
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
        if (isset($responsedata->{'q'.$question->id}) && !empty($responsedata->{'q'.$question->id})) {
            foreach ($responsedata->{'q' . $question->id} as $choiceid => $choicevalue) {
                if (isset($question->choices[$choiceid])) {
                    $record = new \stdClass();
                    $record->responseid = $responsedata->rid;
                    $record->questionid = $question->id;
                    $record->choiceid = $choiceid;
                    if (!empty($question->nameddegrees)) {
                        // If using named degrees, the app returns the label string. Find the value.
                        $nameddegreevalue = array_search($choicevalue, $question->nameddegrees);
                        if ($nameddegreevalue !== false) {
                            $choicevalue = $nameddegreevalue;
                        }
                    }
                    $record->value = $choicevalue;
                    $answers[] = answer\answer::create_from_data($record);
                }
            }
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

        if (isset($response->answers[$this->question->id])) {
            foreach ($response->answers[$this->question->id] as $answer) {
                // Record the choice selection.
                $record = new \stdClass();
                $record->response_id = $response->id;
                $record->question_id = $this->question->id;
                $record->choice_id = $answer->choiceid;
                $record->rankvalue = $answer->value;
                $resid = $DB->insert_record(static::response_table(), $record);
            }
        }
        return $resid;
    }

    /**
     * @param bool $rids
     * @param bool $anonymous
     * @return array
     *
     * TODO - This works differently than all other get_results methods. This needs to be refactored.
     */
    public function get_results($rids=false, $anonymous=false) {
        global $DB;

        $rsql = '';
        if (!empty($rids)) {
            list($rsql, $params) = $DB->get_in_or_equal($rids);
            $rsql = ' AND response_id ' . $rsql;
        }

        $select = 'question_id=' . $this->question->id . ' AND content NOT LIKE \'!other%\' ORDER BY id ASC';
        if ($rows = $DB->get_records_select('questionnaire_quest_choice', $select)) {
            foreach ($rows as $row) {
                $this->counts[$row->content] = new \stdClass();
                $nbna = $DB->count_records(static::response_table(), array('question_id' => $this->question->id,
                                'choice_id' => $row->id, 'rankvalue' => '-1'));
                $this->counts[$row->content]->nbna = $nbna;
            }
        }

        // For nameddegrees, need an array by degree value of positions (zero indexed).
        $rankvalue = [];
        if (!empty($this->question->nameddegrees)) {
            $rankvalue = array_flip(array_keys($this->question->nameddegrees));
        }

        $isrestricted = ($this->question->length < count($this->question->choices)) && $this->question->no_duplicate_choices();
        // Usual case.
        if (!$isrestricted) {
            if (!empty ($rankvalue)) {
                $sql = "SELECT r.id, c.content, r.rankvalue, c.id AS choiceid
                FROM {questionnaire_quest_choice} c, {".static::response_table()."} r
                WHERE r.choice_id = c.id
                AND c.question_id = " . $this->question->id . "
                AND r.rankvalue >= 0{$rsql}
                ORDER BY choiceid";
                $results = $DB->get_records_sql($sql, $params);
                $value = [];
                foreach ($results as $result) {
                    if (isset($rankvalue[$result->rankvalue])) {
                        if (isset ($value[$result->choiceid])) {
                            $value[$result->choiceid] += $rankvalue[$result->rankvalue] + 1;
                        } else {
                            $value[$result->choiceid] = $rankvalue[$result->rankvalue] + 1;
                        }
                    }
                }
            }

            $sql = "SELECT c.id, c.content, a.average, a.num
                    FROM {questionnaire_quest_choice} c
                    INNER JOIN
                         (SELECT c2.id, AVG(a2.rankvalue) AS average, COUNT(a2.response_id) AS num
                          FROM {questionnaire_quest_choice} c2, {".static::response_table()."} a2
                          WHERE c2.question_id = ? AND a2.question_id = ? AND a2.choice_id = c2.id AND a2.rankvalue >= 0{$rsql}
                          GROUP BY c2.id) a ON a.id = c.id
                          order by c.id";
            $results = $DB->get_records_sql($sql, array_merge(array($this->question->id, $this->question->id), $params));
            if (!empty ($rankvalue)) {
                foreach ($results as $key => $result) {
                    if (isset($value[$key])) {
                        $result->averagevalue = $value[$key] / $result->num;
                    }
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
                         (SELECT c2.id, SUM(a2.rankvalue) AS sum, COUNT(a2.response_id) AS num
                          FROM {questionnaire_quest_choice} c2, {".static::response_table()."} a2
                          WHERE c2.question_id = ? AND a2.question_id = ? AND a2.choice_id = c2.id AND a2.rankvalue >= 0{$rsql}
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

        $sql = 'SELECT r.id, r.response_id as rid, r.question_id AS qid, r.choice_id AS cid, r.rankvalue ' .
            'FROM {'.$this->response_table().'} r ' .
            'INNER JOIN {questionnaire_quest_choice} c ON r.choice_id = c.id ' .
            'WHERE r.question_id= ? ' . $rsql . ' ' .
            'ORDER BY rid,cid ASC';
        $responses = $DB->get_recordset_sql($sql, $params);

        $rid = 0;
        $feedbackscores = [];
        foreach ($responses as $response) {
            if ($rid != $response->rid) {
                $rid = $response->rid;
                $feedbackscores[$rid] = new \stdClass();
                $feedbackscores[$rid]->rid = $rid;
                $feedbackscores[$rid]->score = 0;
            }
            // Only count scores that are currently defined (in case old responses are using older data).
            $feedbackscores[$rid]->score += isset($this->question->nameddegrees[$response->rankvalue]) ? $response->rankvalue : 0;
        }

        return (!empty($feedbackscores) ? $feedbackscores : false);
    }

    /**
     * Provide a template for results screen if defined.
     * @param bool $pdf
     * @return mixed The template string or false/
     */
    public function results_template($pdf = false) {
        if ($pdf) {
            return 'mod_questionnaire/resultspdf_rate';
        } else {
            return 'mod_questionnaire/results_rate';
        }
    }

    /**
     * Provide the result information for the specified result records.
     *
     * @param int|array $rids - A single response id, or array.
     * @param string $sort - Optional display sort.
     * @param boolean $anonymous - Whether or not responses are anonymous.
     * @return string - Display output.
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
                        if ($this->question->osgood_rate_scale()) { // Osgood's semantic differential.
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
            $output1 = $this->mkresavg($sort, $stravgvalue);
            $output2 = $this->mkrescount($rids, $rows, $sort);
            $output = (object)array_merge((array)$output1, (array)$output2);
        } else {
            $output = (object)['noresponses' => true];
        }
        return $output;
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
        $sql = 'SELECT a.id as aid, q.id AS qid, q.precise AS precise, c.id AS cid, q.content, c.content as ccontent,
                                a.rankvalue as arank '.
            'FROM {'.static::response_table().'} a, {questionnaire_question} q, {questionnaire_quest_choice} c '.
            'WHERE a.response_id= ? AND a.question_id=q.id AND a.choice_id=c.id '.
            'ORDER BY aid, a.question_id, c.id';
        $records = $DB->get_records_sql($sql, [$rid]);
        foreach ($records as $row) {
            // Next two are 'qid' and 'cid', each with numeric and hash keys.
            $osgood = false;
            if (\mod_questionnaire\question\rate::type_is_osgood_rate_scale($row->precise)) {
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
        $sql = 'SELECT id, response_id as responseid, question_id as questionid, choice_id as choiceid, rankvalue as value ' .
            'FROM {' . static::response_table() .'} ' .
            'WHERE response_id = ? ';
        $records = $DB->get_records_sql($sql, [$rid]);
        foreach ($records as $record) {
            $answers[$record->questionid][$record->choiceid] = answer\answer::create_from_data($record);
        }

        return $answers;
    }

    /**
     * Configure bulk sql
     * @return bulk_sql_config
     */
    protected function bulk_sql_config() {
        return new bulk_sql_config(static::response_table(), 'qrr', true, false, true);
    }

    /**
     * Return a structure for averages.
     * @param string $sort
     * @param string $stravgvalue
     * @return \stdClass
     */
    private function mkresavg($sort, $stravgvalue='') {
        global $CFG;

        $stravgrank = get_string('averagerank', 'questionnaire');
        $osgood = false;
        if ($this->question->precise == 3) { // Osgood's semantic differential.
            $osgood = true;
            $stravgrank = get_string('averageposition', 'questionnaire');
        }
        $stravg = '<div style="text-align:right">'.$stravgrank.$stravgvalue.'</div>';

        $isna = $this->question->precise == 1;
        $isnahead = '';
        $nbchoices = count($this->counts);
        $isrestricted = ($this->question->length < $nbchoices) && $this->question->precise == 2;

        if ($isna) {
            $isnahead = get_string('notapplicable', 'questionnaire');
        }
        $pagetags = new \stdClass();
        $pagetags->averages = new \stdClass();

        if ($isna) {
            $header1 = new \stdClass();
            $header1->text = '';
            $header1->align = '';
            $header2 = new \stdClass();
            $header2->text = $stravg;
            $header2->align = '';
            $header3 = new \stdClass();
            $header3->text = '&dArr;';
            $header3->align = 'center';
            $header4 = new \stdClass();
            $header4->text = $isnahead;
            $header4->align = 'right';
        } else {
            if ($osgood) {
                $stravg = '<div style="text-align:center">'.$stravgrank.'</div>';
                $header1 = new \stdClass();
                $header1->text = '';
                $header1->align = '';
                $header2 = new \stdClass();
                $header2->text = $stravg;
                $header2->align = '';
                $header3 = new \stdClass();
                $header3->text = '';
                $header3->align = 'center';
            } else {
                $header1 = new \stdClass();
                $header1->text = '';
                $header1->align = '';
                $header2 = new \stdClass();
                $header2->text = $stravg;
                $header2->align = '';
                $header3 = new \stdClass();
                $header3->text = '&dArr;';
                $header3->align = 'center';
            }
        }
        // PDF columns are based on a 11.69in x 8.27in page. Margins are 15mm each side, or 1.1811 in total.
        $pdfwidth = 11.69 - 1.1811;
        if ($isna) {
            $header1->width = '55%';
            $header2->width = '35%';
            $header3->width = '5%';
            $header4->width = '5%';
            $header1->pdfwidth = $pdfwidth * .55;
            $header2->pdfwidth = $pdfwidth * .35;
            $header3->pdfwidth = $pdfwidth * .05;
            $header4->pdfwidth = $pdfwidth * .05;
        } else if ($osgood) {
            $header1->width = '25%';
            $header2->width = '50%';
            $header3->width = '25%';
            $header1->pdfwidth = $pdfwidth * .25;
            $header2->pdfwidth = $pdfwidth * .5;
            $header3->pdfwidth = $pdfwidth * .25;
        } else {
            $header1->width = '60%';
            $header2->width = '35%';
            $header3->width = '5%';
            $header1->pdfwidth = $pdfwidth * .6;
            $header2->pdfwidth = $pdfwidth * .35;
            $header3->pdfwidth = $pdfwidth * .05;
        }
        $pagetags->averages->headers = [$header1, $header2, $header3];
        if (isset($header4)) {
            $pagetags->averages->headers[] = $header4;
        }

        $imageurl = $CFG->wwwroot.'/mod/questionnaire/images/hbar.gif';
        $spacerimage = $CFG->wwwroot . '/mod/questionnaire/images/hbartransp.gif';
        $llength = $this->question->length;
        if (!$llength) {
            $llength = 5;
        }
        // Add an extra column to accomodate lower ranks in this case.
        $llength += $isrestricted;
        $width = 100 / $llength;
        $n = array();
        $nameddegrees = 0;
        foreach ($this->question->nameddegrees as $degree) {
            // To take into account languages filter.
            $content = (format_text($degree, FORMAT_HTML, ['noclean' => true]));
            $n[$nameddegrees] = $degree;
            $nameddegrees++;
        }
        for ($j = 0; $j < $this->question->length; $j++) {
            if (isset($n[$j])) {
                $str = $n[$j];
            } else {
                $str = $j + 1;
            }
        }
        $rankcols = [];
        $pdfwidth = $header2->pdfwidth / (100 / $width);
        for ($i = 0; $i <= $llength - 1; $i++) {
            if ($isrestricted && $i == $llength - 1) {
                $str = "...";
                $rankcols[] = (object)['width' => $width . '%', 'text' => '...', 'pdfwidth' => $pdfwidth];
            } else if (isset($n[$i])) {
                $str = $n[$i];
                $rankcols[] = (object)['width' => $width . '%', 'text' => $n[$i], 'pdfwidth' => $pdfwidth];
            } else {
                $str = $i + 1;
                $rankcols[] = (object)['width' => $width . '%', 'text' => $i + 1, 'pdfwidth' => $pdfwidth];
            }
        }
        $pagetags->averages->choicelabelrow = new \stdClass();
        $pagetags->averages->choicelabelrow->innertablewidth = $header2->pdfwidth;
        $pagetags->averages->choicelabelrow->column1 = (object)['width' => $header1->width, 'align' => $header1->align,
            'text' => '', 'pdfwidth' => $header1->pdfwidth];
        $pagetags->averages->choicelabelrow->column2 = (object)['width' => $header2->width, 'align' => $header2->align,
            'ranks' => $rankcols, 'pdfwidth' => $header2->pdfwidth];
        $pagetags->averages->choicelabelrow->column3 = (object)['width' => $header3->width, 'align' => $header3->align,
            'text' => '', 'pdfwidth' => $header3->pdfwidth];
        if ($isna) {
            $pagetags->averages->choicelabelrow->column4 = (object)['width' => $header4->width, 'align' => $header4->align,
                'text' => '', 'pdfwidth' => $header4->pdfwidth];
        }

        switch ($sort) {
            case 'ascending':
                uasort($this->counts, self::class . '::sortavgasc');
                break;
            case 'descending':
                uasort($this->counts, self::class . '::sortavgdesc');
                break;
        }
        reset ($this->counts);

        if (!empty($this->counts) && is_array($this->counts)) {
            $pagetags->averages->choiceaverages = [];
            foreach ($this->counts as $content => $contentobj) {
                // Eliminate potential named degrees on Likert scale.
                if (!preg_match("/^[0-9]{1,3}=/", $content)) {
                    if (isset($contentobj->avg)) {
                        $avg = $contentobj->avg;
                        // If named degrees were used, swap averages for display.
                        if (isset($contentobj->avgvalue)) {
                            $avg = $contentobj->avgvalue;
                            $avgvalue = $contentobj->avg;
                        } else {
                            $avgvalue = '';
                        }
                    } else {
                        $avg = '';
                    }
                    $nbna = $contentobj->nbna;

                    if ($avg) {
                        if (($j = $avg * $width) > 0) {
                            $marginposition = ($avg - 0.5 ) / ($this->question->length + $isrestricted);
                        }
                        if (!right_to_left()) {
                            $margin = 'margin-left:' . $marginposition * 100 . '%';
                            $marginpdf = $marginposition * $pagetags->averages->choicelabelrow->innertablewidth;
                        } else {
                            $margin = 'margin-right:' . $marginposition * 100 . '%';
                            $marginpdf = $pagetags->averages->choicelabelrow->innertablewidth -
                                ($marginposition * $pagetags->averages->choicelabelrow->innertablewidth);
                        }
                    } else {
                        $margin = '';
                    }

                    if ($osgood) {
                        // Ensure there are two bits of content.
                        list($content, $contentright) = array_merge(preg_split('/[|]/', $content), array(' '));
                    } else {
                        $contents = questionnaire_choice_values($content);
                        if ($contents->modname) {
                            $content = $contents->text;
                        }
                    }
                    if ($osgood) {
                        $choicecol1 = new \stdClass();
                        $choicecol1->width = $header1->width;
                        $choicecol1->pdfwidth = $header1->pdfwidth;
                        $choicecol1->align = $header1->align;
                        $choicecol1->text = '<div class="mdl-right">' .
                            format_text($content, FORMAT_HTML, ['noclean' => true]) . '</div>';
                        $choicecol2 = new \stdClass();
                        $choicecol2->width = $header2->width;
                        $choicecol2->pdfwidth = $header2->pdfwidth;
                        $choicecol2->align = $header2->align;
                        $choicecol2->imageurl = $imageurl;
                        $choicecol2->spacerimage = $spacerimage;
                        $choicecol2->margin = $margin;
                        $choicecol2->marginpdf = $marginpdf;
                        $choicecol3 = new \stdClass();
                        $choicecol3->width = $header3->width;
                        $choicecol3->pdfwidth = $header3->pdfwidth;
                        $choicecol3->align = $header3->align;
                        $choicecol3->text = '<div class="mdl-left">' .
                            format_text($contentright, FORMAT_HTML, ['noclean' => true]) . '</div>';
                        $pagetags->averages->choiceaverages[] = (object)['column1' => $choicecol1, 'column2' => $choicecol2,
                            'column3' => $choicecol3];
                        // JR JUNE 2012 do not display meaningless average rank values for Osgood.
                    } else if ($avg || ($nbna != 0)) {
                        $stravgval = '';
                        if ($avg) {
                            if ($stravgvalue) {
                                $stravgval = '('.sprintf('%.1f', $avgvalue).')';
                            }
                            $stravgval = sprintf('%.1f', $avg).'&nbsp;'.$stravgval;
                            if ($isna) {
                                $choicecol4 = new \stdClass();
                                $choicecol4->width = $header4->width;
                                $choicecol4->pdfwidth = $header4->pdfwidth;
                                $choicecol4->align = $header4->align;
                                $choicecol4->text = $nbna;
                            }
                        }
                        $choicecol1 = new \stdClass();
                        $choicecol1->width = $header1->width;
                        $choicecol1->pdfwidth = $header1->pdfwidth;
                        $choicecol1->align = $header1->align;
                        $choicecol1->text = format_text($content, FORMAT_HTML, ['noclean' => true]);
                        $choicecol2 = new \stdClass();
                        $choicecol2->width = $header2->width;
                        $choicecol2->pdfwidth = $header2->pdfwidth;
                        $choicecol2->align = $header2->align;
                        $choicecol2->imageurl = $imageurl;
                        $choicecol2->spacerimage = $spacerimage;
                        $choicecol2->margin = $margin;
                        $choicecol2->marginpdf = $marginpdf;
                        $choicecol3 = new \stdClass();
                        $choicecol3->width = $header3->width;
                        $choicecol3->pdfwidth = $header3->pdfwidth;
                        $choicecol3->align = $header3->align;
                        $choicecol3->text = $stravgval;
                        if ($avg) {
                            if (isset($choicecol4)) {
                                $pagetags->averages->choiceaverages[] = (object)['column1' => $choicecol1,
                                    'column2' => $choicecol2, 'column3' => $choicecol3, 'column4' => $choicecol4];
                            } else {
                                $pagetags->averages->choiceaverages[] = (object)['column1' => $choicecol1,
                                    'column2' => $choicecol2, 'column3' => $choicecol3];
                            }
                        } else {
                            $choicecol4 = new \stdClass();
                            $choicecol4->width = $header4->width;
                            $choicecol4->pdfwidth = $header4->pdfwidth;
                            $choicecol4->align = $header4->align;
                            $choicecol4->text = $nbna;
                            $pagetags->averages->choiceaverages[] = (object)['column1' => $choicecol1, 'column2' => $choicecol2,
                                'column3' => $choicecol3];
                        }
                    }
                } // End if named degrees.
            } // End foreach.
        } else {
            $nodata1 = new \stdClass();
            $nodata1->width = $header1->width;
            $nodata1->align = $header1->align;
            $nodata1->text = '';
            $nodata2 = new \stdClass();
            $nodata2->width = $header2->width;
            $nodata2->align = $header2->align;
            $nodata2->text = get_string('noresponsedata', 'mod_questionnaire');
            $nodata3 = new \stdClass();
            $nodata3->width = $header3->width;
            $nodata3->align = $header3->align;
            $nodata3->text = '';
            if (isset($header4)) {
                $nodata4 = new \stdClass();
                $nodata4->width = $header4->width;
                $nodata4->align = $header4->align;
                $nodata4->text = '';
                $pagetags->averages->nodata = [$nodata1, $nodata2, $nodata3, $nodata4];
            } else {
                $pagetags->averages->nodata = [$nodata1, $nodata2, $nodata3];
            }
        }
        return $pagetags;
    }

    /**
     * Return a structure for counts.
     * @param array $rids
     * @param array $rows
     * @param string $sort
     * @return \stdClass
     */
    private function mkrescount($rids, $rows, $sort) {
        // Display number of responses to Rate questions - see http://moodle.org/mod/forum/discuss.php?d=185106.
        global $DB;

        $nbresponses = count($rids);
        // Prepare data to be displayed.
        $isrestricted = ($this->question->length < count($this->question->choices)) && $this->question->precise == 2;

        $rsql = '';
        if (!empty($rids)) {
            list($rsql, $params) = $DB->get_in_or_equal($rids);
            $rsql = ' AND response_id ' . $rsql;
        }

        array_unshift($params, $this->question->id); // This is question_id.
        $sql = 'SELECT r.id, c.content, r.rankvalue, c.id AS choiceid ' .
            'FROM {questionnaire_quest_choice} c , ' .
            '{questionnaire_response_rank} r ' .
            'WHERE c.question_id = ?' .
            ' AND r.question_id = c.question_id' .
            ' AND r.choice_id = c.id ' .
            $rsql .
            ' ORDER BY choiceid, rankvalue ASC';
        $choices = $DB->get_records_sql($sql, $params);

        // Sort rows (results) by average value.
        if ($sort != 'default') {
            $sortarray = array();
            foreach ($rows as $row) {
                foreach ($row as $key => $value) {
                    if (!isset($sortarray[$key])) {
                        $sortarray[$key] = array();
                    }
                    $sortarray[$key][] = $value;
                }
            }
            $orderby = "average";
            switch ($sort) {
                case 'ascending':
                    array_multisort($sortarray[$orderby], SORT_ASC, $rows);
                    break;
                case 'descending':
                    array_multisort($sortarray[$orderby], SORT_DESC, $rows);
                    break;
            }
        }
        $nbranks = $this->question->length;
        $ranks = [];
        $rankvalue = [];
        if (!empty($this->question->nameddegrees)) {
            $rankvalue = array_flip(array_keys($this->question->nameddegrees));
        }
        foreach ($rows as $row) {
            $choiceid = $row->id;
            foreach ($choices as $choice) {
                if ($choice->choiceid == $choiceid) {
                    $n = 0;
                    for ($i = 1; $i <= $nbranks; $i++) {
                        if ((isset($rankvalue[$choice->rankvalue]) && ($rankvalue[$choice->rankvalue] == ($i - 1))) ||
                            (empty($rankvalue) && ($choice->rankvalue == $i))) {
                            $n++;
                            if (!isset($ranks[$choice->content][$i])) {
                                $ranks[$choice->content][$i] = 0;
                            }
                            $ranks[$choice->content][$i] += $n;
                        } else if (!isset($ranks[$choice->content][$i])) {
                            $ranks[$choice->content][$i] = 0;
                        }
                    }
                }
            }
        }

        // Psettings for display.
        $strtotal = '<strong>'.get_string('total', 'questionnaire').'</strong>';
        $isna = $this->question->precise == 1;
        $osgood = false;
        if ($this->question->precise == 3) { // Osgood's semantic differential.
            $osgood = true;
        }
        if ($this->question->precise == 1) {
            $na = get_string('notapplicable', 'questionnaire');
        } else {
            $na = '';
        }
        $nameddegrees = 0;
        $n = array();
        foreach ($this->question->nameddegrees as $degree) {
            $content = $degree;
            $n[$nameddegrees] = format_text($content, FORMAT_HTML, ['noclean' => true]);
            $nameddegrees++;
        }
        foreach ($this->question->choices as $choice) {
            $contents = questionnaire_choice_values($choice->content);
            if ($contents->modname) {
                $choice->content = $contents->text;
            }
        }

        $pagetags = new \stdClass();
        $pagetags->totals = new \stdClass();
        $pagetags->totals->headers = [];
        if ($osgood) {
            $align = 'right';
        } else {
            $align = 'left';
        }
        $pagetags->totals->headers[] = (object)['align' => $align,
            'text' => '<span class="smalltext">'.get_string('responses', 'questionnaire').'</span>'];

        // Display the column titles.
        for ($j = 0; $j < $this->question->length; $j++) {
            if (isset($n[$j])) {
                $str = $n[$j];
            } else {
                $str = $j + 1;
            }
            $pagetags->totals->headers[] = (object)['align' => 'center', 'text' => '<span class="smalltext">'.$str.'</span>'];
        }
        if ($osgood) {
            $pagetags->totals->headers[] = (object)['align' => 'left', 'text' => ''];
        }
        $pagetags->totals->headers[] = (object)['align' => 'center', 'text' => $strtotal];
        if ($isrestricted) {
            $pagetags->totals->headers[] = (object)['align' => 'center', 'text' => get_string('notapplicable', 'questionnaire')];
        }
        if ($na) {
            $pagetags->totals->headers[] = (object)['align' => 'center', 'text' => $na];
        }

        // Now display the responses.
        $pagetags->totals->choices = [];
        foreach ($ranks as $content => $rank) {
            $totalcols = [];
            // Eliminate potential named degrees on Likert scale.
            if (!preg_match("/^[0-9]{1,3}=/", $content)) {
                // First display the list of degrees (named or un-named)
                // number of NOT AVAILABLE responses for this possible answer.
                $nbna = $this->counts[$content]->nbna;
                // TOTAL number of responses for this possible answer.
                $total = $this->counts[$content]->num;
                $nbresp = '<strong>'.$total.'</strong>';
                if ($osgood) {
                    // Ensure there are two bits of content.
                    list($content, $contentright) = array_merge(preg_split('/[|]/', $content), array(' '));
                    $header = reset($pagetags->totals->headers);
                    $totalcols[] = (object)['align' => $header->align,
                        'text' => format_text($content, FORMAT_HTML, ['noclean' => true])];
                } else {
                    // Eliminate potentially short-named choices.
                    $contents = questionnaire_choice_values($content);
                    if ($contents->modname) {
                        $content = $contents->text;
                    }
                    $header = reset($pagetags->totals->headers);
                    $totalcols[] = (object)['align' => $header->align,
                        'text' => format_text($content, FORMAT_HTML, ['noclean' => true])];
                }
                // Display ranks/rates numbers.
                $maxrank = max($rank);
                for ($i = 1; $i <= $this->question->length; $i++) {
                    $percent = '';
                    if (isset($rank[$i])) {
                        $str = $rank[$i];
                        if ($total !== 0 && $str !== 0) {
                            $percent = ' (<span class="percent">'.number_format(($str * 100) / $total).'%</span>)';
                        }
                        // Emphasize responses with max rank value.
                        if ($str == $maxrank) {
                            $str = '<strong>'.$str.'</strong>';
                        }
                    } else {
                        $str = 0;
                    }
                    $header = next($pagetags->totals->headers);
                    $totalcols[] = (object)['align' => $header->align, 'text' => $str.$percent];
                }
                if ($osgood) {
                    $header = next($pagetags->totals->headers);
                    $totalcols[] = (object)['align' => $header->align,
                        'text' => format_text($contentright, FORMAT_HTML, ['noclean' => true])];
                }
                $header = next($pagetags->totals->headers);
                $totalcols[] = (object)['align' => $header->align, 'text' => $nbresp];
                if ($isrestricted) {
                    $header = next($pagetags->totals->headers);
                    $totalcols[] = (object)['align' => $header->align, 'text' => $nbresponses - $total];
                }
                if (!$osgood) {
                    if ($na) {
                        $header = next($pagetags->totals->headers);
                        $totalcols[] = (object)['align' => $header->align, 'text' => $nbna];
                    }
                }
            } // End named degrees.
            $pagetags->totals->choices[] = (object)['totalcols' => $totalcols];
        }
        return $pagetags;
    }

    /**
     * Sorting function for ascending.
     * @param \stdClass $a
     * @param \stdClass $b
     * @return int
     */
    private static function sortavgasc($a, $b) {
        if (isset($a->avg) && isset($b->avg)) {
            if ( $a->avg < $b->avg ) {
                return -1;
            } else if ($a->avg > $b->avg ) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    /**
     * Sorting function for descending.
     * @param \stdClass $a
     * @param \stdClass $b
     * @return int
     */
    private static function sortavgdesc($a, $b) {
        if (isset($a->avg) && isset($b->avg)) {
            if ( $a->avg > $b->avg ) {
                return -1;
            } else if ($a->avg < $b->avg) {
                return 1;
            } else {
                return 0;
            }
        }
    }
}
