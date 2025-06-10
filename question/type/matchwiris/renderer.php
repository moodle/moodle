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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/wq/renderer.php');
require_once($CFG->dirroot . '/question/type/match/renderer.php');

class qtype_matchwiris_helper_renderer extends qtype_match_renderer {

    protected function format_choices($question) {
        $choices = parent::format_choices($question);

        foreach ($choices as $key => $choice) {
            $choices[$key] = $question->expand_variables_text($choice);
        }

        return $choices;
    }
}

class qtype_matchwiris_renderer extends qtype_wq_renderer {
    public function __construct(moodle_page $page, $target) {
        parent::__construct(new qtype_matchwiris_helper_renderer($page, $target), $page, $target);
    }

}
