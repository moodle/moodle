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
 * @deprecated Since Moodle 4.3 MDL-75125 - Use question_action_base instead.
 * @todo MDL-78090 This class will be deleted in Moodle 4.7
 */
abstract class action_column_base extends column_base {

    /**
     * @return string
     * @deprecated Since Moodle 4.3
     */
    public function get_title(): string {
        debugging('The action_column_base class is deprecated. Please use question_action_base instead.', DEBUG_DEVELOPER);
        return '&#160;';
    }

    /**
     * @return string[]
     * @deprecated Since Moodle 4.3
     */
    public function get_extra_classes(): array {
        debugging('The action_column_base class is deprecated. Please use question_action_base instead.', DEBUG_DEVELOPER);

        return ['iconcol'];
    }

    /**
     * @param $icon
     * @param $title
     * @param $url
     * @return void
     * @deprecated Since Moodle 4.3
     */
    protected function print_icon($icon, $title, $url): void {
        debugging('The action_column_base class is deprecated. Please use question_action_base instead.', DEBUG_DEVELOPER);
        global $OUTPUT;
        echo \html_writer::tag('a', $OUTPUT->pix_icon($icon, $title), ['title' => $title, 'href' => $url]);
    }

    /**
     * @return string[]
     * @deprecated Since Moodle 4.3
     */
    public function get_extra_joins(): array {
        debugging('The action_column_base class is deprecated. Please use question_action_base instead.', DEBUG_DEVELOPER);
        return ['qv' => 'JOIN {question_versions} qv ON qv.questionid = q.id',
                'qbe' => 'JOIN {question_bank_entries} qbe on qbe.id = qv.questionbankentryid',
                'qc' => 'JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid'];
    }

    /**
     * @return string[]
     * @deprecated Since Moodle 4.3
     */
    public function get_required_fields(): array {
        debugging('The action_column_base class is deprecated. Please use question_action_base instead.', DEBUG_DEVELOPER);
        // Createdby is required for permission checks.
        // Qtype so we can easily avoid applying actions to question types that
        // are no longer installed.
        return ['q.id', 'q.qtype', 'q.createdby', 'qc.contextid'];
    }

}
