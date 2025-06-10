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
 * Class for numerical text response types.
 *
 * @author Mike Churchward
 * @copyright 2016 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 */
class numericaltext extends text {
    /**
     * Provide an array of answer objects from web form data for the question.
     *
     * @param \stdClass $responsedata All of the responsedata as an object.
     * @param \mod_questionnaire\question\question $question
     * @return array \mod_questionnaire\responsetype\answer\answer An array of answer objects.
     */
    public static function answers_from_webform($responsedata, $question) {
        $answers = [];
        if (isset($responsedata->{'q'.$question->id}) && is_numeric($responsedata->{'q'.$question->id})) {
            $val = $responsedata->{'q' . $question->id};
            // Allow commas as well as points in decimal numbers.
            $val = str_replace(",", ".", $responsedata->{'q' . $question->id});
            $val = preg_replace("/[^0-9.\-]*(-?[0-9]*\.?[0-9]*).*/", '\1', $val);
            $record = new \stdClass();
            $record->responseid = $responsedata->rid;
            $record->questionid = $question->id;
            $record->value = $val;
            $answers[] = answer\answer::create_from_data($record);
        }
        return $answers;
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
        if (is_array($rids)) {
            $prtotal = 1;
        } else if (is_int($rids)) {
            $prtotal = 0;
        }
        if ($rows = $this->get_results($rids, $anonymous)) {
            $numrespondents = count($rids);
            $numresponses = count($rows);
            // Count identical answers (numeric questions only).
            $counts = [];
            foreach ($rows as $row) {
                if (!empty($row->response) || is_numeric($row->response)) {
                    $textidx = clean_text($row->response);
                    $counts[$textidx] = !empty($counts[$textidx]) ? ($counts[$textidx] + 1) : 1;
                }
            }
            $pagetags = $this->get_results_tags($counts, $numrespondents, $numresponses, $prtotal);
        } else {
            $pagetags = new \stdClass();
        }
        return $pagetags;
    }
}
