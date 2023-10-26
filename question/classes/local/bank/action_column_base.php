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
 * A base class for actions that are an icon that lets you manipulate the question in some way.
 *
 * @package   core_question
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\local\bank;

/**
 * A base class for actions that are an icon that lets you manipulate the question in some way.
 *
 * @copyright 2009 Tim Hunt
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class action_column_base extends column_base {

    public function get_title(): string {
        return '&#160;';
    }

    public function get_extra_classes(): array {
        return ['iconcol'];
    }

    protected function print_icon($icon, $title, $url): void {
        global $OUTPUT;
        echo \html_writer::tag('a', $OUTPUT->pix_icon($icon, $title), ['title' => $title, 'href' => $url]);
    }

    public function get_extra_joins(): array {
        return ['qv' => 'JOIN {question_versions} qv ON qv.questionid = q.id',
                'qbe' => 'JOIN {question_bank_entries} qbe on qbe.id = qv.questionbankentryid',
                'qc' => 'JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid'];
    }

    public function get_required_fields(): array {
        // Createdby is required for permission checks.
        // Qtype so we can easily avoid applying actions to question types that
        // are no longer installed.
        return ['q.id', 'q.qtype', 'q.createdby', 'qc.contextid'];
    }

}
