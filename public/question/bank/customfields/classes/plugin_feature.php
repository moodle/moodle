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

namespace qbank_customfields;

use core_question\local\bank\plugin_features_base;
use core_question\local\bank\view;
use qbank_customfields\customfield\question_handler;

/**
 * Class plugin_feature is the entrypoint for the columns.
 *
 * @package    qbank_customfields
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_feature extends plugin_features_base {

    /**
     * This method will return the array of objects to be rendered as a prt of question bank columns/actions.
     *
     * @param view $qbank
     * @return array
     */
    public function get_question_columns(view $qbank): array {
        // We make a column for each custom field and load the data into it.
        $columns = [];

        // First get all the available question custom fields.
        $customfieldhandler = question_handler::create();
        $fields = $customfieldhandler->get_fields();
        $context = $qbank->get_most_specific_context();

        // Iterate through the fields initialising a column for each.
        // We don't need to know the values that questions have at this stage.
        foreach ($fields as $field) {
            if ($customfieldhandler->can_view_type($field, $context)) {
                $customfieldcolumn = new custom_field_column($qbank, $field);
                $columns[] = $customfieldcolumn;
            }

        }

        return $columns;
    }
}
