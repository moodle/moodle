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
 * qbank_bulkmove lib functions.
 *
 * @package    qbank_bulkmove
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Generates the bulkmove bank and category chooser for display in the modal.
 *
 * @param array $args
 * @return bool|string
 */
function qbank_bulkmove_output_fragment_bulk_move(array $args) {
    global $OUTPUT;

    $currentbankid = clean_param($args['context']->instanceid, PARAM_INT);
    $currentcategoryid = clean_param($args['categoryid'], PARAM_INT);
    $qbankcatchooser = new \qbank_bulkmove\output\bulk_move($currentbankid, $currentcategoryid);

    return $OUTPUT->render($qbankcatchooser);
}
