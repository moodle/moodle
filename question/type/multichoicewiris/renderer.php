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
require_once($CFG->dirroot . '/question/type/multichoice/renderer.php');
require_once($CFG->dirroot . '/question/type/wq/renderer.php');


class qtype_multichoicewiris_single_renderer extends qtype_wq_renderer {
    public function __construct(moodle_page $page, $target) {
        parent::__construct(new qtype_multichoice_single_renderer($page, $target), $page, $target);
    }
}


class qtype_multichoicewiris_multi_renderer extends qtype_wq_renderer {
    public function __construct(moodle_page $page, $target) {
        parent::__construct(new qtype_multichoice_multi_renderer($page, $target), $page, $target);
    }
}
