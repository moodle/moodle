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
 * Html file replacement support for sub question type qtype_ddmatch
 * @package tool_ally
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally\componentsupport;

defined ('MOODLE_INTERNAL') || die();

use tool_ally\local_file;

require_once($CFG->dirroot.'/question/engine/bank.php');

/**
 * Html file replacement support for sub question type qtype_ddmatch
 * @package tool_ally
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmatch_component extends question_component {

    public function replace_file_links() {
        global $DB;

        $file = $this->file;

        $area = $file->get_filearea();
        $itemid = $file->get_itemid();

        $subquestion = $DB->get_record('qtype_ddmatch_subquestions', ['id' => $itemid]);

        if ($area === 'subquestion') {
            $field = 'questiontext';
        } else if ($area === 'subanswer') {
            $field = 'answertext';
        } else {
            debugging('Area of '.$area.' is not yet supported for qtype_ddmatch_component');
            return;
        }

        $table = 'qtype_ddmatch_subquestions';

        local_file::update_filenames_in_html($field, $table, ' id = ? ',
            ['id' => $itemid], $this->oldfilename, $file->get_filename());

        \question_finder::get_instance()->uncache_question($subquestion->questionid);
    }
}
