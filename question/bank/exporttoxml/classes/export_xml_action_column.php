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

namespace qbank_exporttoxml;

use core_question\local\bank\menu_action_column_base;

/**
 * Question bank column export the question in Moodle XML format.
 *
 * @package   qbank_exporttoxml
 * @copyright 2019 The Open University
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export_xml_action_column extends menu_action_column_base {

    /** @var string avoids repeated calls to get_string('duplicate'). */
    protected $strexportasxml;

    public function init(): void {
        parent::init();
        $this->strexportasxml = get_string('exportasxml', 'question');
    }

    public function get_name(): string {
        return 'exportasxmlaction';
    }

    protected function get_url_icon_and_label(\stdClass $question): array {
        if (!\question_bank::is_qtype_installed($question->qtype)) {
            // It sometimes happens that people end up with junk questions
            // in their question bank of a type that is no longer installed.
            // We cannot do most actions on them, because that leads to errors.
            return [null, null, null];
        }

        if (!question_has_capability_on($question, 'view')) {
            return [null, null, null];
        }

        return [helper::question_get_export_single_question_url($question),
                't/download', $this->strexportasxml];
    }

}
