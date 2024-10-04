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

namespace qbank_statistics;

use qbank_statistics\columns\facility_index;
use qbank_statistics\columns\discrimination_index;
use qbank_statistics\columns\discriminative_efficiency;
/**
 * Class plugin_features is the entrypoint for the columns.
 *
 * @package    qbank_statistics
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_feature extends \core_question\local\bank\plugin_features_base {

    /**
     * This method will return the array of objects to be rendered as a prt of question bank columns/actions.
     *
     * @param view $qbank
     * @return array
     */
    public function get_question_columns($qbank): array {
        return [
            new discrimination_index($qbank),
            new facility_index($qbank),
            new discriminative_efficiency($qbank)
        ];
    }
}
