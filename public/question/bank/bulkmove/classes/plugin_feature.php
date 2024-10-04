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

namespace qbank_bulkmove;

use core_question\local\bank\plugin_features_base;
use core_question\local\bank\view;

/**
 * Class plugin_feature is the entrypoint for the features.
 *
 * @package    qbank_bulkmove
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_feature extends plugin_features_base {

    /**
     * Initialise the bulk action.
     * @param view $qbank
     * @return bulk_move_action[]
     */
    public function get_bulk_actions(view $qbank): array {
        // Don't return bulkmove if we are on the question history page.
        if ($qbank->is_listing_specific_versions()) {
            return [];
        } else {
            return [
                new bulk_move_action($qbank),
            ];
        }
    }
}
