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

/**
 * Class helper for export plugin.
 *
 * @package    qbank_exporttoxml
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Get the URL to export a single question (exportone.php).
     *
     * @param \stdClass|\question_definition $question the question definition as obtained from
     *      question_bank::load_question_data() or question_bank::make_question().
     *      (Only ->id and ->contextid are used.)
     * @return \moodle_url the requested URL.
     */
    public static function question_get_export_single_question_url($question): \moodle_url {
        $params = ['id' => $question->id, 'sesskey' => sesskey()];
        $context = \context::instance_by_id($question->contextid);
        switch ($context->contextlevel) {
            case CONTEXT_MODULE:
                $params['cmid'] = $context->instanceid;
                break;

            case CONTEXT_COURSE:
                $params['courseid'] = $context->instanceid;
                break;

            default:
                $params['courseid'] = SITEID;
        }

        return new \moodle_url('/question/bank/exporttoxml/exportone.php', $params);
    }

}
