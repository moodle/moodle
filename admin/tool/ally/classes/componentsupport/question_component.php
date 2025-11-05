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
 * Html file replacement support for core questions
 * @package tool_ally
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\componentsupport;

defined ('MOODLE_INTERNAL') || die();

use tool_ally\local_file;
use tool_ally\models\pluginfileurlprops;

require_once($CFG->dirroot.'/question/engine/bank.php');

/**
 * Html file replacement support for core questions
 * @package tool_ally
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_component extends file_component_base {

    public static function component_type() {
        return self::TYPE_CORE;
    }

    /**
     * Return the properties for a specific pluginfileurl.
     * @param string $pluginfileurl
     * @return bool | pluginfileurlprops
     */
    public static function fileurlproperties($pluginfileurl) {
        $regex = '/(?:.*)pluginfile\.php(?:\?file=|)(?:\/|%2F)(\d*?)(?:\/|%2F)(.*)$/';
        $matches = [];
        $matched = preg_match($regex, $pluginfileurl, $matches);
        if (!$matched) {
            return false;
        }
        $contextid = $matches[1];
        if (strpos($matches[2], '%2F') !== false) {
            $del = '%2F';
        } else {
            $del = '/';
        }
        $arr = explode($del, $matches[2]);
        $component = urldecode(array_shift($arr));
        $filearea = array_shift($arr);

        $qubaidorpreview = array_shift($arr); // Remove qubaid or "preview".
        // Two sub-cases. See question_pluginfile() for more info.
        if ($qubaidorpreview === 'preview') {
            // 1. A question being previewed outside an attempt/usage.
            array_shift($arr); // Remove previewcontextid.
            array_shift($arr); // Remove previewcomponent.
            array_shift($arr); // Remove questionid.
        } else {
            // 2. A question being attempted in the normal way.
            array_shift($arr); // Remove slot.
        }
        $itemid = array_shift($arr);
        $filename = array_shift($arr);

        return new pluginfileurlprops($contextid, $component, $filearea, $itemid, $filename);
    }

    /**
     * Get question record.
     * @param $id
     * @return mixed
     */
    private function get_question($id) {
        global $DB;
        $sql = "SELECT q.*, qbe.idnumber
                  FROM {question} q
                  JOIN {question_versions} qv ON q.id = qv.questionid
                  JOIN {question_bank_entries} qbe ON qbe.id=qv.questionbankentryid
                 WHERE q.id = ?";
        return $DB->get_record_sql($sql, [$id]);
    }

    public function replace_file_links() {
        global $DB;

        $file = $this->file;

        $area = $file->get_filearea();
        $itemid = $file->get_itemid();

        // Correct, incorrect, partially correct feedback areas.
        $inorcorrectfbareas = [
            'correctfeedback',
            'partiallycorrectfeedback',
            'incorrectfeedback'
        ];

        $idfield = null;
        $table = null;
        $field = $area;
        $questionid = null;

        if ($area === 'questiontext' || $area === 'generalfeedback') {
            $table = 'question';
            $idfield = 'id';
            $questionid = $itemid;
        } else if ($area === 'answer' || $area === 'answerfeedback') {
            $table = 'question_answers';
            $idfield = 'id';
            $field = $area === 'answer' ? 'answer' : 'feedback';
            $sqrow = $DB->get_record($table, ['id' => $itemid]);
            $questionid = $sqrow->question;
        } else if (in_array($area, $inorcorrectfbareas)) {
            $question = $this->get_question($itemid);
            $questionid = $question->id;
            $qtype = $question->qtype;
            $idfield = 'questionid';

            switch ($qtype) {
                case 'ddimageortext' :
                    $table = 'qtype_ddimageortext';
                    break;
                case 'ddmarker' :
                    $table = 'qtype_ddmarker';
                    break;
                case 'ddmatch' :
                    if ($area === 'correctfeedback'
                        || $area === 'incorrectfeedback'
                        || $area === 'partiallycorrectfeedback') {
                        $table = 'qtype_ddmatch_options';
                        $idfield = 'questionid';
                    } else {
                        debugging('Area of '.$area.' is not yet supported for qtype_ddmatch_html');
                        return;
                    }
                    break;
                case 'ddwtos' :
                    $table = 'question_ddwtos';
                    break;
                case 'gapfill' :
                    $table = 'question_gapfill';
                    $idfield = 'question';
                    break;
                case 'gapselect' :
                    $table = 'question_gapselect';
                    break;
                case 'match' :
                    $table = 'qtype_match_options';
                    break;
                case 'multichoice' :
                    $table = 'qtype_multichoice_options';
                    break;
                case 'randomsamatch' :
                    $table = 'qtype_randomsamatch_options';
                    break;
                default :
                    debugging('Question area of '.$area.' and question type '.$qtype.' is not yet supported');
                    return;
            }
        }

        if ($idfield === null || $table === null) {
            // We need this because questions are essentially plugins and new ones will be introduced to our code base
            // as and when customer demand necessitates them.
            debugging('Question area of '.$area.' is not yet supported');
            return;
        }

        local_file::update_filenames_in_html($field, $table, ' '.$idfield.' = ? ',
            [$itemid], $this->oldfilename, $file->get_filename());

        \question_finder::get_instance()->uncache_question($questionid);
    }
}
