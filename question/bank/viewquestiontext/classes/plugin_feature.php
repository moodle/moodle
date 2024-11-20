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

namespace qbank_viewquestiontext;

use core\context;
use core_question\local\bank\plugin_features_base;
use core_question\local\bank\view;
use qbank_viewquestiontext\output\question_text_format;

/**
 * Class columns is the entrypoint for the columns.
 *
 * @package    qbank_viewquestiontext
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_feature extends plugin_features_base {

    /**
     * Return an additional row for displaying the question text, if user has a preference set to display it.
     *
     * @param view $qbank
     * @return array
     */
    public function get_question_columns(view $qbank): array {
        $row = new question_text_row($qbank);
        $preference = (int)question_get_display_preference($row->get_preference_key(), '0', PARAM_INT, new \moodle_url('/'));
        if ($preference != question_text_format::OFF) {
            return [
                $row,
            ];
        }
        return [];

    }

    public function get_question_bank_controls(view $qbank, context $context, int $categoryid): array {
        return [
            400 => new question_text_format($qbank),
        ];
    }
}
